function isInteger(num){
    return(num % 1 == 0);
}

function isMoney(str){
    var moneyRegex = /^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/;    
    return( moneyRegex.test(str) );
}

function isEmail(email){
    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return( emailRegex.test(email) );
}

// Extracts Leading $ from money format and returns in format of ##.##
function parseMoney(money){
    var moneyRegex = /^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/;
    var match = moneyRegex.exec(money);
    alert(match[1]);
    return(match[1]);
}

// Validate a phone number
function isPhone(phone){
    
}

// Get stripped down phone number in format ###########
function parsePhone(phone){
    // changes (###)-###-#### to ##########
}