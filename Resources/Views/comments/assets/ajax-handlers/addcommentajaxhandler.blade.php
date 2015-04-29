<script type="text/javascript">
	(function ($) {

		function newAddCommentObj(){
			var addComment = {
				init: function (formId) {
					addComment.prepare(formId);
					addComment.events();
				},

				prepare: function (formId) {
					addComment.formId                  = formId;
					addComment.form                    = $(formId);
					addComment.errormessageContainer   = $('div#{{ $commentModuleName }}errormessageContainer');
					addComment.messageContainer        = $('div#{{ $commentModuleName }}messageContainer');
					addComment.messageContainerUl      = addComment.messageContainer.find("ul");
					addComment.errormessageContainerUl = addComment.errormessageContainer.find("ul");
					addComment.CommentContent          = $('div#{{ $commentModuleName }}CommentContent');
					addComment.url                     = addComment.form.attr('action');
				},

				events: function () {
					$(document).on('submit',addComment.formId ,function(e) {
						e.preventDefault();
						addComment.data = new FormData(addComment.form[0]);  
						addComment.prepare(addComment.formId);
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
							addComment.errormessageContainer.removeClass('hidden');
							addComment.errormessageContainerUl.find("li").remove();

							$.each(JSON.parse(data.responseText), function(index, value){
								addComment.errormessageContainer.append('<li>' + value + '</li>')
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
			form_add_comment.init("#{{ $commentModuleName }}add_comment_form");

		});

	}(jQuery));
</script>