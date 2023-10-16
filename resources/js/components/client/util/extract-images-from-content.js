export const extractImages = function(content) {
    var quotes = content.split('"');
    var images = [];
    var i = 0;
    quotes.forEach(element => {
        i++
        if (element.substring(0, 10) == 'data:image') {
            images.push(element);
        }
    });

    return images;
}