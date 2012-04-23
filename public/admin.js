// Include Utility JS library
var script = document.createElement('script');
script.type = 'text/javascript';
script.src = '/SVDP/public/utility.js';
var content = document.getElementById('head');
document.getElementById('head').appendChild(script);

function adjust_validation(myform){
    $aid  = myform.aid.value;
    $lifetimecases = myform.lifetimecases.value;
    $yearlycases = myform.yearlycases.value;
    
    // Check the total recievable aid fields
    if($aid == ""){
        alert("Enter a value for Total Recievable Aid");
        return false;
    }
    parseMoney($aid);
    if( !moneyRegex.test($aid) ){
        alert("Format does not match for Total Recievable Aid.\nExamples of proper format: $1,234.50,$0.70,.7");
        return false;
    }
    if($aid < 0)
    {
        alert("Total Recievable Aid cannot be less than 0.");
        return false;
    }
    
    // Check the lifetimecases fields
    if($lifetimecases == ""){
        alert("Enter a value for Lifetime Cases");
        return false;
    }
    if( !isInteger($lifetimecases) ){
        alert("Life time cases must be an integral value.\nExamples of proper format: 1 or 20 or 100");
        return false;
    }
    
    if( $lifetimecases < 0){
        alert("Invalid amount specified for life time cases. Cannot be less than 0.");
        return false;
    }
    
    // Check the yearly cases
    if($yearlycases == ""){
        alert("Enter a value for Yearly Cases");
        return false;
    }
    if( !isInteger($yearlycases) ){
        alert("Yearly cases must be an integral value.\nExamples of proper format: 1 or 20 or 100");
        return false;
    }
    
    if($yearlycases < 0){
        alert("Invalid amount specified for yearly cases. Cannot be less than 0.");
        return false;
    }
    
    return true;
}