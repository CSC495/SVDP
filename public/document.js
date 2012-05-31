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