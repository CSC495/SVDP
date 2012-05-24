function initDocView() {
    // Create a dialog to confirm the case close operation.
    var deleteDoc = $('.deleteDoc');

    var confirmDeleteDoc = $('<div/>')
        .html('<p>Are you sure you wish to delete this document?</p>'
            + '<p>Deleted documents cannot be recovered.</p>')
        .dialog({
            autoOpen: false,
            buttons: {
                'Yes': function () {
                    deleteDoc.trigger('click');
                },
                'No': function () {
                    confirmDeleteDoc.dialog('close');
                }
            },
            modal: true,
            resizable: false,
            title: 'Confirm Delete Document'
        });

    // Attach event handler to close button.
    deleteDoc.click(function () {
        // If the confirmation dialog is already open, we should hide the dialog and let the form
        // submit.
        if (confirmDeleteDoc.dialog('isOpen')) {
            confirmDeleteDoc.dialog('close');
	    window.location.href = deleteDoc.attr("href");
            return true;
        }

        // Otherwise, we should show the confirmation dialog.
        confirmDeleteDoc.dialog('open');
        return false;
    });
}