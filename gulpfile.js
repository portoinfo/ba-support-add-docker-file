const gulp = require('gulp');
const request = require('request');
const fs = require('fs');
const { exit } = require('process');
String.prototype.escapeHTML = function () {
    return this.replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
function objToString(obj) {
    var string = '';
    Object.keys(obj).forEach(key => {
        string += "\"" + key + "\" => \"" + obj[key].escapeHTML() + "\",";
    });
    string = '[' + string.slice(0, -1) + ']';
    return string;
}
let firstString = '';
let secondString = '';
let thirdString = '';

gulp.task('download', function (done) {
    console.info('Iniciado download das traduções');
    request
    .get('https://cheetah-api.builderall.com/api/translate/all/languages', (err, res, allLangs) => {
        if (err) {
            console.log('Error:', err);
        } else if (res.statusCode !== 200) {
            console.log('Status:', res.statusCode);
        } else {
            var allLangs = JSON.parse(allLangs);
            var promisses = [];
            var translatedLangs = {};
            allLangs.forEach((dataLang) => {
                var lang = dataLang['descricao'];
                firstString += "import " + lang + " from './" + lang + ".json'\n";
                secondString += lang + ": " + lang + ',\n';
                console.info('Baixado traduçõe de ' + lang);
                promisses.push(
                    new Promise(function (resolve, reject) {
                        request.get('https://cheetah-api.builderall.com/api/translate/language/' + lang + '/96', (err, res, data) => {
                            if (err) {
                                console.log('Error:', err);
                            } else if (res.statusCode !== 200) {
                                console.log('Status:', res.statusCode);
                            } else {
                                var data = JSON.parse(data);
                                var translations = data.data;
                                n_traducoes = Object.keys(translations).length
                                if(n_traducoes) {
                                    translatedLangs[lang] = dataLang['label_desc'];
                                }
                                fs.writeFileSync('static/translation/' + lang + '.json', JSON.stringify(translations));
// For laravel
if (!fs.existsSync('resources/lang/' + lang)) {
    fs.mkdirSync('resources/lang/' + lang);
}
fs.writeFileSync('resources/lang/' + lang + '/app.php', "<?php return " + objToString(translations) + ";");
console.log('Criado aquido de tradução ' + lang + '.json com ' + n_traducoes + ' traduções');
resolve();
}
})
                        .on('error', function (error) {
                            console.log("Error: ", error);
                            reject(error);
                        });
                    }));
            });
            Promise.all(promisses).then(() => {
                fs.writeFileSync('static/translation/index.js', firstString + '\nexport const languages = {\n' + secondString + '}');
                console.log('Gravado index.js');
// Cria lista de idiomas ordenada
allLangs.forEach((lang) => {
    if (typeof translatedLangs[lang.descricao] !== 'undefined') {
        thirdString += '{key: "' + lang.descricao + '", desc: "' + translatedLangs[lang.descricao] + '"},';
    }
});
fs.writeFileSync('static/translation/select.js', 'export const languages = [\n' + thirdString + ']');
console.log('Gravado select.js');
done();
})

        }
    })
    .on('error', function (error) {
        console.log("Error: ", error);
    });
});