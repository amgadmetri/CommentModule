<script type="text/javascript">
	(function ($) {

		function newdeleteCommentObj(){
			var deleteComment = {
				init: function (linkId) {
					deleteComment.prepare(linkId);
					deleteComment.events();
				},

				prepare: function (linkId) {
					deleteComment.linkId                  = linkId;
					deleteComment.link                    = $(linkId);
					deleteComment.CommentContent          = $('div#{{ $commentModuleName }}singleComment');
					deleteComment.errormessageContainer   = $('div#{{ $commentModuleName }}deleteErrormessageContainer');
					deleteComment.errormessageContainerUl = deleteComment.errormessageContainer.find("ul");
					deleteComment.url                     = deleteComment.link.attr('href');
				},

				events: function () {
					$(document).on('click', deleteComment.linkId, function(e) {
						e.preventDefault();
						deleteComment.prepare(deleteComment.linkId);
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
			link_delete_comment.init("#{{ $commentModuleName }}delete_comment_link");

		});

	}(jQuery));
</script>