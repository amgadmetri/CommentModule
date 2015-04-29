<script type="text/javascript">
	(function ($) {

		function neweditCommentObj(){
			var editComment = {
				init: function (formId) {
					editComment.prepare(formId);
					editComment.events();
				},

				prepare: function (formId) {
					editComment.formId                  = formId;
					editComment.form                    = $(formId);
					editComment.errormessageContainer   = $('div#{{ $commentModuleName }}editErrormessageContainer');
					editComment.messageContainer        = $('div#{{ $commentModuleName }}editMessageContainer');
					editComment.messageContainerUl      = editComment.messageContainer.find("ul");
					editComment.errormessageContainerUl = editComment.errormessageContainer.find("ul");
					editComment.CommentContent          = $('div#{{ $commentModuleName }}singleComment');
					editComment.url                     = editComment.form.attr('action');
				},

				events: function () {
					$(document).on('submit', editComment.formId, function(e) {
						e.preventDefault();
						editComment.prepare(editComment.formId);
						editComment.data = new FormData(editComment.form[0]);  
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
			form_edit_comment.init("#{{ $commentModuleName }}edit_comment_form");

		});

	}(jQuery));
</script>