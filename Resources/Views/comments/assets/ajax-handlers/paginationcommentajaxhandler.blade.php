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
						paginate.ajaxAction();
					});
				},

				ajaxAction: function () {
					$.ajax({
						url         : $(paginate.paginateId).attr('href'),
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

			var commentmoduleNext =  newPaginateObj();
			commentmoduleNext.init("#{{ $commentModuleName }}commentmoduleNext");
		});

	}(jQuery));
</script>