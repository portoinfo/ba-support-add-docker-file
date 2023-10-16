/**
 *
 * To set a locale add the param ?locale=xx to the script URL.
 * Accepted values:
 * - br => portuguese
 * - us => english
 *
 *
 * To set a fee  = ?fee=slug
 * To set a plan = ?plan=slug
 */




(() => {
    // dependencies
    const Axios = require('axios');
    const Cookies = require('js-cookie');
    const Uuid = require('uuid');

    // constants
    const env = process.env.MIX_APP_ENV; //
    const cookieName = 'cs_code'; // Cookie Name
    const cookieDays = 3; // Time to live of the cookie

    // script params
    const scriptUrl = new URL(document.currentScript.src); // Dcript url
    const locale = scriptUrl.searchParams.get("locale") || 'us'; // Get the locale
    const fee = scriptUrl.searchParams.get("fee") || ''; // Get the locale
    const plan = scriptUrl.searchParams.get("plan") || ''; // Get the locale

    // campany stuff
    const companyBR = (env == 'production') ? 'SCtiVVNFd2V2SXNMZ2RvQ29lL3UwSitOeFhqL1poSzNHSmczUmI2amJLcz0=' : 'V29XUHhlU0p1c3ZjWVlrMG5qMnFMeGRlc0tjRWs3UGszYklXN1FVbk96TT0=';
    const companyUS = (env == 'production') ? '' : '';
    const companyToUse = (locale == 'br') ? companyBR : companyUS;

    // department_id checkout
    const departmentBR = (env == 'production') ? 'SUVLY3ZjSFZzK1E3STNFU1RhaklmUT09' : 'NndsbjNNZlVqT241ZzhNQUZsWE1iUT09';
    const departmentUS = (env == 'production') ? 'SUVLY3ZjSFZzK1E3STNFU1RhaklmUT09' : 'NndsbjNNZlVqT241ZzhNQUZsWE1iUT09';
    const departmentToUse = (locale == 'br') ? departmentBR : departmentUS;

    // Office Urls
    const officeBaseUrl = (env == 'production') ? 'https://office.builderall.com' : 'https://office.builderall.io';
    const loginStatus = officeBaseUrl + `/${locale}/office/login-status`;

    // Code of the tracking
    const csCode = getCsCode();

    // Final redirect links
    window.redirSupportLogged = `${officeBaseUrl}/${locale}/office/support-system?department-type=checkout&cs-code=${csCode}&did=${departmentToUse}`; // User Logged
    window.redirSupportNotLogged = `${scriptUrl.origin}/client/${companyToUse}/register?department-type=checkout&cs-code=${csCode}&type=chat`; // User not Logged

    if (fee != '') {
        window.redirSupportLogged = `${window.redirSupportLogged}&fee=${fee}`;
        window.redirSupportNotLogged = `${window.redirSupportNotLogged}&fee=${fee}`;
    }

    if (plan != '') {
        window.redirSupportLogged = `${window.redirSupportLogged}&plan=${plan}`;
        window.redirSupportNotLogged = `${window.redirSupportNotLogged}&plan=${plan}`;
    }

    let isLoading = false;

    /**
     * Get and Store the trackin code
     * @returns String
     */
    function getCsCode() {
        let code = Cookies.get(cookieName) || Uuid.v4();
        if (!!code) {
            Cookies.set(cookieName, code, { secure: false, expires: cookieDays, path: '' });
        }

        return code;
    }

    /**
     * Add Loader
     */
    function addLoader() {
        isLoading = true;
        var attendant = document.getElementById('ba-attendant');
        var loader = document.getElementById('ba-loading');
        if (!!loader && !!attendant) {
            attendant.style.display = 'none';
            loader.style.display = '';
        }
    }

    /**
     * Remove Loader
     */
    function removeLoader() {
        isLoading = false;
        var attendant = document.getElementById('ba-attendant');
        var loader = document.getElementById('ba-loading');
        if (!!loader && !!attendant) {
            attendant.style.display = '';
            loader.style.display = 'none';
        }
    }

    /**
     * Makes ajax request
     * @param {Object} obj
     */
    function ajax(obj) {
        var xhttp = new XMLHttpRequest();
        xhttp.open(obj.method || 'GET', obj.url, true);

        // Set Headers
        if (obj.hasOwnProperty('headers') && Object.keys(obj.headers).length) {
            Object.keys(obj.headers).map(function(key) {
                if (key && key.length) {
                    xhttp.setRequestHeader(key, obj.headers[key]);
                }
            });
        }

        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                obj.success(JSON.parse(xhttp.responseText));
            }
        };
        xhttp.onerror = function() {
            console.log('Error on request.', JSON.parse(this.responseText));
            if (obj.hasOwnProperty(error)) { obj.error(JSON.parse(this.responseText)); }
        }

        xhttp.withCredentials = true;
        xhttp.send(obj.data || null);
    }

    /**
     * Click Icon action
     */
    function clickIcon() {
        ajax({
            url: loginStatus,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {

                if (data.isLogged) {
                    window.open(window.redirSupportLogged, "_blank");
                } else {
                    window.open(window.redirSupportNotLogged, "_blank");
                }

                removeLoader();
            },
            error: function(e) {
                console.log("Erros", e);
                removeLoader();
            }
        });
    }

    /**
     * append all the script
     */
    function appendIcon() {
        var supportIconHtml = '<div class="ba-icon-container" id="ba-support">' +
            '<img id="ba-attendant" class="ba-attendant" src="' + scriptUrl.origin + '/images/checkout/attendant.jpg" alt="Builderall Support" title="Precisa de ajuda?"/>' +
            '<img id="ba-loading" class="ba-loader" style="display:none;" src="' + scriptUrl.origin + '/images/checkout/load.svg" alt="Builderall Support" title="Aguarde um momento..."/>' +
            '<div class="ba-online-icon"></div>' +
            '</div>';

        // Creating div to append
        var supportIconDiv = document.createElement("div");
        supportIconDiv.innerHTML = supportIconHtml;

        // Append div to body
        document.body.appendChild(supportIconDiv);

        // Append style
        var linkElement = document.createElement('link');
        linkElement.setAttribute('rel', 'stylesheet');
        linkElement.setAttribute('type', 'text/css');
        linkElement.setAttribute('href', `${scriptUrl.origin}/css/checkout/support.css`);

        document.head.appendChild(linkElement);

        var icon = document.getElementById('ba-support');

        if (!!icon) {

            icon.addEventListener('click', function() {
                if (!isLoading) {
                    addLoader();
                    clickIcon();
                }
            });
        }
    }

    /**
     * Check if department is open and append
     */
    function checkIsAvailable() {
        Axios.get(`${scriptUrl.origin}/api/get-departments-by-timezone`, {
                params: {
                    id: departmentToUse
                }
            })
            .then(function({ data }) {
                if (data.online) {
                    appendIcon();
                }
            });
    }

    /**
     * Check if doc is ready
     * @param {Function} fn
     */
    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // execute the script
    docReady(checkIsAvailable());
})();