function validate_new(myform){

}

function adjust_validation(myform){
    $aid  = myform.aid.value;
    $lifetimecases = myform.lifetimecases.value;
    $yearlycases = myform.yearlycases.value;

    // Check the total receivable aid fields
    if($aid == ""){
        alert("Enter a value for Total Receivable Aid");
        return false;
    }

    if( !moneyRegex.test($aid) ){
        alert("Format does not match for Total Receivable Aid.\nExamples of proper format: $1,234.50,$0.70,.7");
        return false;
    }
    if($aid < 0)
    {
        alert("Total Receivable Aid cannot be less than 0.");
        return false;
    }
    
    // Check the lifetimecases fields
    if($lifetimecases == ""){
        alert("Enter a value for Lifetime Cases");
        return false;
    }
    if( !isInteger($lifetimecases) ){
        alert("Lifetime cases must be an integer value.\nExamples of proper format: 1 or 20 or 100");
        return false;
    }
    
    if( $lifetimecases < 0){
        alert("Invalid amount specified for lifetime cases. Cannot be less than 0.");
        return false;
    }
    
    // Check the yearly cases
    if($yearlycases == ""){
        alert("Enter a value for Yearly Cases");
        return false;
    }
    if( !isInteger($yearlycases) ){
        alert("Yearly cases must be an integer value.\nExamples of proper format: 1 or 20 or 100");
        return false;
    }
    
    if($yearlycases < 0){
        alert("Invalid amount specified for yearly cases. Cannot be less than 0.");
        return false;
    }
    
    // Set values to proper format
    myform.aid.value = parseMoney($aid);
    return true;
}
