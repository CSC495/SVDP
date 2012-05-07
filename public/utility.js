function isInteger(num){
    return typeof n === 'number' && parseFloat(n) == parseInt(n) && !isNaN(n);
}

function isMoney(str){
    var moneyRegex = /^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/;    
    return( moneyRegex.test(str) );
}

// Validate a phone number
function isPhone(phone){
    
}
