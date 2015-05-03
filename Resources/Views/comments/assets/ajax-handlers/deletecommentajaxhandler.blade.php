<script type="text/javascript">
	(function ($) {

		function newdeleteCommentObj(){
			var deleteComment = {
				init: function (linkClass) {
					deleteComment.prepare(linkClass);
					deleteComment.events();
				},

				prepare: function (linkClass) {
					deleteComment.linkClass = linkClass;
				},

				events: function () {
					$(document).on('click', deleteComment.linkClass, function(e) {
						e.preventDefault();
						deleteComment.url                     = $(this).attr('href');
						deleteComment.CommentContent          = $(this).parents('div#{{ $commentModuleName }}singleComment');
						deleteComment.errormessageContainer   = deleteComment.CommentContent.find('div#{{ $commentModuleName }}deleteErrormessageContainer');
						deleteComment.errormessageContainerUl = deleteComment.errormessageContainer.find("ul");
						deleteComment.ajaxAction();
					});
				},

				ajaxAction: function () 
				{
					$.ajax({
						url         : deleteComment.url,
						type        : 'GET',
						success: function(data)
						{				
							if(data == 'done')
							{
								deleteComment.CommentContent.empty();
							}
						},
						error: function(data, error, errorThrown)
						{
							deleteComment.errormessageContainer.removeClass('hidden');
							deleteComment.errormessageContainer.show();
							deleteComment.errormessageContainerUl.find("li").remove();

							$.each(JSON.parse(data.responseText), function(index, value){
								deleteComment.errormessageContainer.append('<li>' + value + '</li>')
							});

							setTimeout(function() {
								deleteComment.errormessageContainer.fadeOut();
								editComment.errormessageContainer.addClass('hidden');
								deleteComment.errormessageContainerUl.find("li").remove();
							}, 5000);
						}
					});
				}
			}
			return deleteComment;
		}

		$(document).ready(function (){

			var link_delete_comment =  newdeleteCommentObj();
			link_delete_comment.init(".{{ $commentModuleName }}delete_comment_link");

		});

	}(jQuery));
</script>