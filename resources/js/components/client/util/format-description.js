import { stripHtml } from './strip-html';
export const formatDescription = function(description) {
    if (description) {
        var hasImg = description.search("<img");
        var isAtachment = description.search(',"original_name":');
        var hasSnippet = description.search('<pre');
        if(hasImg !== -1 && !stripHtml(description)) {
            return 'bs-image'; 
        } else if (isAtachment !== -1 && !stripHtml(description)) {
            return 'bs-attachment';
        } else if (hasSnippet !== -1) {
            return 'Snippet';
        } else {
            return stripHtml(description);
        } 
    }
};