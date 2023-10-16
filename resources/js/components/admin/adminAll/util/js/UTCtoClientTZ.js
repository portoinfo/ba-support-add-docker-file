export const UTCtoClientTZ = function(h, tz) {
    let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
        "YYYY-MM-DD HH:mm:ss"
    );
    let datetime = h_format.split(" ");
    let date = datetime[0];
    let time = datetime[1];
    let date_split = date.split("-");
    let time_split = time.split(":");
    let year = date_split[0];
    let month = date_split[1];
    let day = date_split[2];
    let hour = time_split[0];
    let minute = time_split[1];
    let second = time_split[2];
    var month_integer = parseInt(month, 10);
    if (month_integer >= 1) {
        month_integer--;
    }
    let dateUTC = new Date(
        Date.UTC(year, month_integer, day, hour, minute, second)
    );
    let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz
    });
    return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(
        "YYYY-MM-DD HH:mm:ss"
    );
}