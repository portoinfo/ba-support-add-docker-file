export const cleanEmail = function(email) { 
    return  email.replace(/^comp_\d+_/i, '');
}