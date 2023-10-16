export const ct = function(text) {
    if (text && text.substr(0, 3) == 'bs-') {
        return this.$t(text);
    } else {
        return text;
    }
}