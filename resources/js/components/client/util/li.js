export const li = function(text) {
    const n  = text.split(" ");
    const fw = n[0];
    if (n.length === 1) {
        const li = fw.substr(0, 2);
        return li.toUpperCase();
    } else {
        const lw = n[n.length - 1];
        const li = fw.substr(0, 1)+lw.substr(0, 1);
        return li.toUpperCase();
    }
}