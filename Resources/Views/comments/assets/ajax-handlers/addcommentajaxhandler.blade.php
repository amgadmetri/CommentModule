<script type="text/javascript">
	(function ($) {
		
		function newAddCommentObj(){
			var addComment = {
				init: function (formClass) {
					addComment.prepare(formClass);
					addComment.events();
				},

				prepare: function (formClass) {
					addComment.formClass               = formClass;
					addComment.errormessageContainer   = $('#{{ $commentTemplateName }}addErrormessageContainer');
					addComment.messageContainer        = $('#{{ $commentTemplateName }}addMessageContainer');
					addComment.messageContainerUl      = addComment.messageContainer.find("ul");
					addComment.errormessageContainerUl = addComment.errormessageContainer.find("ul");
					addComment.CommentContent          = $('#{{ $commentTemplateName }}CommentContent');
				},

				events: function () {
					$(document).on('submit',addComment.formClass ,function(e) {
						e.preventDefault();
						addComment.data = new FormData(this);
						addComment.url  = $(this).attr('action');
						addComment.ajaxAction();
					});
				},

				ajaxAction: function () 
				{
					$.ajax({
						url         : addComment.url,
						data        : addComment.data,
						type        : 'POST',
						processData : false,
						contentType : false,
						success: function(data)
						{
							addComment.messageContainer.removeClass('hidden');
							addComment.messageContainer.show();
							addComment.messageContainerUl.find("li").remove();

							addComment.messageContainerUl.append('<li>Comment created successfully.</li>')
							addComment.CommentContent.empty();
							addComment.CommentContent.append(data);

							setTimeout(function() {
								addComment.messageContainer.fadeOut();
								addComment.messageContainer.addClass('hidden');
								addComment.messageContainerUl.find("li").remove();
							}, 5000);
						},
						error: function(data, error, errorThrown)
						{
							console.log(data.responseText);
							addComment.errormessageContainer.removeClass('hidden');
							addComment.errormessageContainer.show();
							addComment.errormessageContainerUl.find("li").remove();

							$.each(JSON.parse(data.responseText), function(index, value){
								addComment.errormessageContainerUl.append('<li>' + value + '</li>')
							});

							setTimeout(function() {
								addComment.errormessageContainer.fadeOut();
								addComment.errormessageContainer.addClass('hidden');
								addComment.errormessageContainerUl.find("li").remove();
							}, 5000);
						}
					});
				}
			}
			return addComment;
		}
		$(document).ready(function (){

			var form_add_comment =  newAddCommentObj();
			form_add_comment.init(".add_comment_form");

		});
	}(jQuery));
</script>