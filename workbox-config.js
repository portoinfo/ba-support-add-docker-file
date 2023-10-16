module.exports = {
    globDirectory: 'public/',
    globPatterns: [
        '**/*.{css,ico,ttf,txt,woff,woff2,htm,svg,png,jpg,php,js,js~hotfix_group,json,mp3,scss,config}'
    ],
    ignoreURLParametersMatching: [
        /^utm_/,
        /^fbclid$/
    ],
    swDest: 'public/service-worker.js'
};