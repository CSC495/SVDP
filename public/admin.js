function initAdminLimits(){
	$(document).ready(function(){
		$("#adjust").validate({
			rules: {
				aid: {
					required: true,
					min: 0,
                    number: true
				},
                casefund: {
                    required: true,
                    min: 0,
                    number: true
                },
                lifetimecases: {
                    required: true,
                    min: 0,
                    digits: true
                },
                yearlycases: {
                    required: true,
                    min: 0,
                    digits: true
                }
			},
			messages: {
				aid: {
					required: "Value must be provided",
					min: "Value must be 0 or greater",
                    number: "Value is not valid dollar amount"
				},
                casefund: {
                    required: "Value must be provided",
					min: "Value must be 0 or greater",
                    number: "Value is not valid dollar amount"
                },
                lifetimecases: {
                    required: "Value must be provided",
                    min: "Value must be 0 or greater",
                    digits: "Value must be an integral value. (No decimals)"
                },
                yearlycases: {
                    required: "Value must be provided",
                    min: "Value must be 0 or greater",
                    digits: "Value must be an integral value. (No decimals)"
                }
			},
			highlight: function(element, errorClass, validClass){
                if( element.id === 'aid' || element.id === 'casefund'){
                    $(element).parent("div").parent("div").parent("div").addClass(errorClass).removeClass(validClass);
                }
                else{
                    $(element).parent("div").parent("div").addClass(errorClass).removeClass(validClass);
                }
			},
			unhighlight: function(element, errorClass, validClass){
                if( element.id === 'aid' || element.id === 'casefund'){
                    $(element).parent("div").parent("div").parent("div").addClass(validClass).removeClass(errorClass);
                }
                else{
                    $(element).parent("div").parent("div").addClass(validClass).removeClass(errorClass);
                }
			},
			submitHandler: function(form) {
				form.submit();
			},
			errorClass: "error",
			validClass: "success",
			errorElement: "span",
			errorPlacement: function(error, element){
				error.insertAfter(element);
				error.addClass('help-inline');
			}
		});//end validate
	});// end ready
}