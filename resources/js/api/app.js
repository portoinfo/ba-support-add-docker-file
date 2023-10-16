require("./bootstrap");

window.Vue = require("vue");
// window.$ = window.jQuery = require('jquery');
import Vue from "vue";
import store from "./../store";

Vue.component(
    "api-popup",
    require("./../components/api/chat-popup/Popup.vue").default
);

createApp();

function createApp() {
    // Creating div to append
    var d = document.createElement("DIV");
    // set id to vueJS
    d.setAttribute("id", "ba-helpdesk");
    // Append div to body
    document.body.appendChild(d);
    // Append main component
    var main = document.createElement("api-popup");
    document.getElementById("ba-helpdesk").appendChild(main);
    const ba_helpdesk = new Vue({
        el: "#ba-helpdesk",
        store,
        created() {
            var script = document.getElementById("ba-helpdesk__script");

            var icon_hidden = script.getAttribute("icon-hidden");
            var icon_size = script.getAttribute("icon-size");
            var icon_margin_bottom = script.getAttribute("icon-margin-bottom");
            var icon_margin_right = script.getAttribute("icon-margin-right");
            var popup_margin_right = script.getAttribute("popup-margin-right");
            var popup_margin_bottom = script.getAttribute("popup-margin-bottom");
            var popup_height = script.getAttribute("popup-height");
            var popup_width = script.getAttribute("popup-width");

            if (icon_hidden == 'true') {
                this.$store.state.icon_hidden = true;
            }

            if (!icon_size || icon_size === 'default') {
                this.$store.state.icon_size = 48;
            } else {
                if (icon_size) {
                    if (Number.isInteger(parseInt(icon_size))) {
                        this.$store.state.icon_size = icon_size;
                    } else {
                        this.$store.state.icon_size = 48;
                    }
                }
            }

            if (!icon_margin_bottom || icon_margin_bottom === 'default') {
                this.$store.state.icon_margin_bottom = 55;
            } else {
                if (icon_margin_bottom) {
                    if (Number.isInteger(parseInt(icon_margin_bottom))) {
                        this.$store.state.icon_margin_bottom = icon_margin_bottom;
                    } else {
                        this.$store.state.icon_margin_bottom = 55;
                    }
                }
            }

            if (!icon_margin_right || icon_margin_right === 'default') {
                this.$store.state.icon_margin_right = 50;
            } else {
                if (icon_margin_right) {
                    if (Number.isInteger(parseInt(icon_margin_right))) {
                        this.$store.state.icon_margin_right = icon_margin_right;
                    } else {
                        this.$store.state.icon_margin_right = 50;
                    }
                }
            }

            if (!popup_margin_right || popup_margin_right === 'default') {
                this.$store.state.popup_margin_right = 25;
            } else {
                if (popup_margin_right) {
                    if (Number.isInteger(parseInt(popup_margin_right))) {
                        this.$store.state.popup_margin_right = popup_margin_right;
                    } else {
                        this.$store.state.popup_margin_right = 25;
                    }
                }
            }

            if (!popup_margin_bottom || popup_margin_bottom === 'default') {
                this.$store.state.popup_margin_bottom = 20;
            } else {
                if (popup_margin_bottom) {
                    if (Number.isInteger(parseInt(popup_margin_bottom))) {
                        this.$store.state.popup_margin_bottom = popup_margin_bottom;
                    } else {
                        this.$store.state.popup_margin_bottom = 20;
                    }
                }
            }

            if (!popup_height || popup_height === 'default') {
                this.$store.state.popup_height = 540;
            } else {
                if (popup_height) {
                    if (Number.isInteger(parseInt(popup_height))) {
                        if (parseInt(popup_height) < 375) {
                            this.$store.state.popup_height = 375;
                        } else {
                            this.$store.state.popup_height = popup_height;
                        }
                    } else {
                        this.$store.state.popup_height = 540;
                    }
                }
            }

            if (!popup_width || popup_width === 'default') {
                this.$store.state.popup_width = 327;
            } else {
                if (popup_width) {
                    if (Number.isInteger(parseInt(popup_width))) {
                        this.$store.state.popup_width = popup_width;
                    } else {
                        this.$store.state.popup_width = 327;
                    }
                }
            }

            // get the company hash from the script
            var hash_company = script.getAttribute("hc");
            this.$store.state.hash_company = hash_company;

            // get the module from the script
            var module = script.getAttribute("module");
            this.$store.state.module = module;

            // get the chat-type from the script
            var chat_type = script.getAttribute("chat-type");
            this.$store.state.chat_type = chat_type;

            // get the base url from the script
            var src = script.getAttribute("src");
            var url = new URL(src);
            var hostname = url.hostname;
            if (hostname == "localhost") {
                hostname = url.protocol + "//" + url.hostname + ":" + url.port;
                this.$store.state.baseURL = hostname;
            } else {
                this.$store.state.baseURL = url.protocol + "//" + url.hostname;
            }

            var icon_url = script.getAttribute("icon-image-url");
            if (!icon_url || icon_url == 'default') {
                this.$store.state.icon_url = `${this.$store.state.baseURL}/images/help_desk_popup.svg`;
            } else if (icon_url) {
                this.$store.state.icon_url = icon_url;
            }

            // get client hash_code (Builderall Office)
            var client_hash = script.getAttribute("client-hash");

            if (client_hash) {
                this.$store.state.builderallOffice = true;
                this.$store.state.client_hash = client_hash;
            }
        },
    });
}
