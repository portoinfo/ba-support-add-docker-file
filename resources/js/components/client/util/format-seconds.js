export const formatSeconds = function(secs) {
    return moment.utc(secs*1000).format('HH:mm:ss');
}