<script type="text/javascript">
	(function ($) {

		function newPaginateObj()
		{
			var paginate = {
				init: function (paginateId) {
					paginate.prepare(paginateId);
					paginate.events();
				},

				prepare: function (paginateId) {
					paginate.paginateId     = paginateId;
					paginate.commentContent = $('div#{{ $commentModuleName }}CommentContent');
				},

				events: function () {
					$(document).on('click', paginate.paginateId, function(e) {
						e.preventDefault();
						paginate.link = $(this).attr('href');
						paginate.ajaxAction();
					});
				},

				ajaxAction: function () {
					$.ajax({
						url         : paginate.link,
						type        : 'GET',
						success		: function(data)
						{
							paginate.commentContent.empty();
							paginate.commentContent.append(data);
						}
					});
				}
			}
			return paginate;
		}

		$(document).ready(function (){

			var commentmodulePrevious =  newPaginateObj();
			commentmodulePrevious.init("#{{ $commentModuleName }}commentmodulePrevious");

			var commentmoduleNext     =  newPaginateObj();
			commentmoduleNext.init("#{{ $commentModuleName }}commentmoduleNext");

			var commentmoduleLinks    =  newPaginateObj();
			commentmoduleLinks.init("#{{ $commentModuleName }}commentmoduleLinks");
		});

	}(jQuery));
</script>