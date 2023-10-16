export const calculateWaitingTime = function(d, c) {
    var moment = require("moment-timezone");
    var startDateTime = moment
        .tz(d, Intl.DateTimeFormat().resolvedOptions().timeZone)
        .toDate();
    var startStamp = startDateTime.valueOf();

    var newDate = new Date();
    var newStamp = newDate.getTime();

    var timer;

    var diff_0 = false;

    function updateClock() {
        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp - startStamp) / 1000);

        // var d = Math.floor(diff / (24 * 60 * 60));
        // diff = diff - d * 24 * 60 * 60;
        var h = Math.floor(diff / (60 * 60));
        diff = diff - h * 60 * 60;
        var m = Math.floor(diff / 60);
        diff = diff - m * 60;
        var s = diff;

        if (h < 10) {
            h = "0" + h;
        }

        if (m < 10) {
            m = "0" + m;
        }

        if (s < 10) {
            s = "0" + s;
        }

        if (document.getElementById(c) !== null) {
            document.getElementById(c).innerHTML =
                h + ":" + m + ":" + s;
        }
    }

    setInterval(updateClock, 1000);
}