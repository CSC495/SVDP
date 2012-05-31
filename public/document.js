function initDocView() {
    // Create a dialog to confirm the case close operation.
    var deleteDoc = $('.deleteDoc');

    $('.deleteDoc').click(function(e){
	e.preventDefault();
	var targetUrl = $(this).attr("href");
	
	var confirmDeleteDoc = $('<div/>')
        .html('<p>Are you sure you wish to delete this document?</p>'
            + '<p>Deleted documents cannot be recovered.</p>')
        .dialog({
            autoOpen: true,
            buttons: {
                'Yes': function () {
			window.location.href = targetUrl;
                },
                'No': function () {
			$(this).dialog("close");
                }
            },
            modal: true,
            resizable: false,
            title: 'Confirm Delete Document'
        });
    });
}

function initDocAdd(){
	
	$(document).ready(function(){
		$("#add").validate({
			rules: {
				name: {
					required: true,
					maxlength: 50
				},
				url: {
					required: true,
					maxlength: 2083
				}
			},
			messages: {
				name: {
					required: "File name must be provided",
					maxlength: "File name cannot exceed 50 characters"
				},
				url: {
					required: "URL must be provided",
					maxlength: "URL cannot exceed 2083 characters"
				}
			},
			submitHandler: function(form) {
				alert('test');
				form.submit();
			}
		});//end validate
	});// end ready
	
	//url,name,add
}