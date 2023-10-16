export const stripHtml = function(html) {
    const strippedString = html ? html.replace(/(<([^>]+)>)/gi, "") : "";
    if (strippedString == "") {
        return strippedString;
    } else {
        return html.replace(/(<([^>]+)>)/gi, " ");
    }
 }