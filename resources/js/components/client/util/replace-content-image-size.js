export const replaceImageSize = function (content) {
    return content.replaceAll('><img', '><img  style="height: 150px;"');
}