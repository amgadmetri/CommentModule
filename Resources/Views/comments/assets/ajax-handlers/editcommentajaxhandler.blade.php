<script type="text/javascript">
	(function ($) {

		function neweditCommentObj(){
			var editComment = {
				init: function (formClass) {
					editComment.prepare(formClass);
					editComment.events();
				},

				prepare: function (formClass) {
					editComment.formClass = formClass;
				},

				events: function () {
					$(document).on('submit',editComment.formClass ,function(e) {
						e.preventDefault();
						editComment.data                    = new FormData(this);
						editComment.url                     = $(this).attr('action');
						editComment.CommentContent          = $(this).parents('div#{{ $commentModuleName }}singleComment');
						editComment.errormessageContainer   = editComment.CommentContent.find('div#{{ $commentModuleName }}editErrormessageContainer');
						editComment.messageContainer        = editComment.CommentContent.find('div#{{ $commentModuleName }}editMessageContainer');
						editComment.messageContainerUl      = editComment.messageContainer.find("ul");
						editComment.errormessageContainerUl = editComment.errormessageContainer.find("ul");
						editComment.ajaxAction();
					});
				},

				ajaxAction: function () 
				{
					$.ajax({
						url         : editComment.url,
						data        : editComment.data,
						type        : 'POST',
						processData : false,
						contentType : false,
						success: function(data)
						{
							editComment.messageContainer.removeClass('hidden');
							editComment.messageContainer.show();
							editComment.messageContainerUl.find("li").remove();

							editComment.messageContainerUl.append('<li>Comment edited successfully.</li>')

							setTimeout(function() {
								editComment.messageContainer.fadeOut();
								editComment.messageContainer.addClass('hidden');
								editComment.messageContainerUl.find("li").remove();
								
								editComment.CommentContent.empty();
								editComment.CommentContent.append(data);
							}, 3000);
						},
						error: function(data, error, errorThrown)
						{
							editComment.errormessageContainer.removeClass('hidden');
							editComment.errormessageContainer.show();
							editComment.errormessageContainerUl.find("li").remove();

							$.each(JSON.parse(data.responseText), function(index, value){
								editComment.errormessageContainer.append('<li>' + value + '</li>')
							});

							setTimeout(function() {
								editComment.errormessageContainer.fadeOut();
								editComment.errormessageContainer.addClass('hidden');
								editComment.errormessageContainerUl.find("li").remove();
							}, 5000);
						}
					});
				}
			}
			return editComment;
		}

		$(document).ready(function (){

			var form_edit_comment =  neweditCommentObj();
			form_edit_comment.init(".{{ $commentModuleName }}edit_comment_form");

		});

	}(jQuery));
</script>