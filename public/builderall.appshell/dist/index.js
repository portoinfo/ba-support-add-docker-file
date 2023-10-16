var $parcel$global =
typeof globalThis !== 'undefined'
  ? globalThis
  : typeof self !== 'undefined'
  ? self
  : typeof window !== 'undefined'
  ? window
  : typeof global !== 'undefined'
  ? global
  : {};
var $parcel$modules = {};
var $parcel$inits = {};

var parcelRequire = $parcel$global["parcelRequire0588"];
if (parcelRequire == null) {
  parcelRequire = function(id) {
    if (id in $parcel$modules) {
      return $parcel$modules[id].exports;
    }
    if (id in $parcel$inits) {
      var init = $parcel$inits[id];
      delete $parcel$inits[id];
      var module = {id: id, exports: {}};
      $parcel$modules[id] = module;
      init.call(module.exports, module, module.exports);
      return module.exports;
    }
    var err = new Error("Cannot find module '" + id + "'");
    err.code = 'MODULE_NOT_FOUND';
    throw err;
  };

  parcelRequire.register = function register(id, init) {
    $parcel$inits[id] = init;
  };

  $parcel$global["parcelRequire0588"] = parcelRequire;
}
parcelRequire.register("7S9ok", function(module, exports) {
module.exports = "<style>* {\n  color: #4d5d71;\n  margin: 0;\n  padding: 0;\n  font-family: Mulish-Regular;\n}\n\nul {\n  list-style: none;\n}\n\n.wrapper {\n  min-height: 100vh;\n  min-width: 100vw;\n  flex-direction: column;\n  display: flex;\n  position: absolute;\n  overflow-x: hidden;\n}\n\n.wrapper .wrapper-header {\n  height: 80px;\n  background-color: #fff;\n  grid-template-columns: 1fr 1fr 1fr;\n  align-items: center;\n  padding: 0 1rem;\n  display: grid;\n}\n\n.wrapper .wrapper-header .header-left {\n  height: 100%;\n  align-items: center;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-left a {\n  align-items: center;\n  gap: .8rem;\n  text-decoration: none;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-left a .header-left-title {\n  opacity: 0;\n  transition: opacity .2s;\n}\n\n.wrapper .wrapper-header .header-left.show {\n  padding-left: 1rem;\n  animation-name: showBuilderallLogo;\n  animation-duration: .4s;\n}\n\n.wrapper .wrapper-header .header-left.show a .header-left-title {\n  opacity: 1;\n  animation-name: opacityAnimation;\n  animation-duration: 1s;\n}\n\n.wrapper .wrapper-header .header-center {\n  height: 100%;\n  justify-content: center;\n  align-items: center;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul {\n  height: 100%;\n  align-items: center;\n  gap: 4rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .header-center-btn {\n  height: 100%;\n  cursor: pointer;\n  background-color: #0000;\n  border: none;\n  align-items: center;\n  gap: 1rem;\n  font-size: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .header-center-btn.active :is(.btn-title, .header-center-icon) {\n  color: #0072e1;\n  transition: all .4s;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .header-center-btn.active svg {\n  fill: #0072e1;\n  transform: rotate(180deg);\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .header-center-btn .btn-title {\n  font-family: Mulish-Light;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown {\n  z-index: -1;\n  opacity: 1;\n  background-color: #fff;\n  border-radius: 0 0 8px 8px;\n  gap: 1rem;\n  animation-name: showDropdownHeaderCenter;\n  animation-duration: .65s;\n  display: none;\n  position: absolute;\n  top: 82px;\n  left: 50%;\n  transform: translateX(-50%);\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown .dropdown-hr {\n  border: 1px solid #d5dee9;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown :is(.dropdown-left-content, .dropdown-sections-content) {\n  flex-direction: column;\n  gap: 1rem;\n  padding: 2rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown :is(.dropdown-left-content-title, .dropdown-sections-title) {\n  color: #0072e1;\n  -webkit-line-clamp: 2;\n  text-overflow: ellipsis;\n  -webkit-box-orient: vertical;\n  font-family: Mulish-Bold;\n  font-size: 2rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n  display: -webkit-box;\n  overflow: hidden;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown :is(.dropdown-left-content-description, .dropdown-sections-description) {\n  font-family: Mulish-Light;\n  font-size: .8rem;\n  animation: .5s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal {\n  display: none;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-left-content {\n  width: 200px;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content {\n  width: 750px;\n  justify-content: center;\n  gap: 1rem;\n  padding: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div {\n  min-width: max-content;\n  border-radius: 8px;\n  flex-direction: column;\n  gap: 1rem;\n  padding: 1rem 2rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div .dropdown-main-content-title-column-div {\n  align-items: center;\n  gap: .4rem;\n  margin-left: -6px;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div .dropdown-main-content-title-column-div span {\n  width: 20px;\n  height: 20px;\n  background-color: #fff;\n  border-radius: 50%;\n  justify-content: center;\n  align-items: center;\n  padding: .4rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div .dropdown-main-content-title-column-div span svg {\n  fill: #0072e1;\n  width: 100%;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div .dropdown-main-content-title-column-div p {\n  color: #0072e1;\n  font-size: 1.2rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul {\n  flex-direction: column;\n  gap: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li {\n  align-items: center;\n  gap: .4rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li:hover :is(.link-tool, .link-blank-tool) {\n  color: #0072e1 !important;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li:hover .link-tool {\n  text-decoration: underline;\n  transition: all .4s;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li:hover .link-blank-tool {\n  visibility: visible;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li .link-tool {\n  font-size: .9rem;\n  text-decoration: none;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li .link-blank-tool {\n  visibility: hidden;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div ul li.li-dont-show {\n  display: none;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div button {\n  color: #0072e1;\n  text-align: left;\n  cursor: pointer;\n  background-color: #0000;\n  border: none;\n  font-size: .9rem;\n  text-decoration: underline;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.normal .dropdown-main-content .dropdown-main-content-main-div:hover {\n  background: #f9fafc;\n  transition: all .4s;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections {\n  display: none;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content {\n  min-width: 290px;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-description b {\n  color: #0072e1;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements {\n  flex-wrap: wrap;\n  justify-content: flex-start;\n  align-items: center;\n  gap: 1rem;\n  margin-top: .4rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements.connect-community a:last-child svg {\n  margin-top: 7px;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements.connect-real-time-support {\n  justify-content: space-evenly;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements.connect-real-time-support a span {\n  margin-top: -10px;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements.connect-help {\n  white-space: nowrap;\n  grid-template-columns: 1fr 1fr;\n  gap: 1.2rem;\n  margin-top: 1rem;\n  display: grid;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements a {\n  align-items: center;\n  gap: .2rem;\n  text-decoration: none;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements a span {\n  font-size: .9rem;\n}\n\n.wrapper .wrapper-header .header-center #header-center-ul li .dropdown.sections .dropdown-sections-content .dropdown-sections-content-elements a:hover span {\n  color: #0072e1;\n  transition: all .4s;\n}\n\n.wrapper .wrapper-header .header-right {\n  height: 100%;\n  justify-content: flex-end;\n  align-items: center;\n  gap: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul {\n  align-items: center;\n  gap: 1.4rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li {\n  position: relative;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li :is(button, a) {\n  cursor: pointer;\n  background-color: #0000;\n  border: none;\n  outline: none;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li :is(button, a) svg {\n  fill: #c4d1e0;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li :is(button, a) svg:hover {\n  fill: #0072e1;\n  transition: all .2s;\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li .tooltip {\n  width: max-content;\n  z-index: 3;\n  background-color: #3c5572;\n  border-radius: 4px;\n  padding: .6rem;\n  display: none;\n  position: absolute;\n  top: 30px;\n  left: 50%;\n  transform: translateX(-50%);\n}\n\n.wrapper .wrapper-header .header-right #header-right-ul li .tooltip p {\n  color: #fff;\n  font-size: 1rem;\n}\n\n.wrapper .wrapper-header .header-right .vertical-line {\n  height: 35px;\n  border: 1px solid #d5dee9;\n}\n\n.wrapper .wrapper-header .header-right .avatar {\n  color: #fff;\n  width: 40px;\n  height: 40px;\n  cursor: pointer;\n  background-color: #0072e1;\n  border: none;\n  border-radius: 50%;\n  justify-content: center;\n  align-items: center;\n  font-size: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar {\n  z-index: 2;\n  opacity: 1;\n  width: 100px;\n  background-color: #fff;\n  border-radius: 8px;\n  flex-direction: column;\n  padding: 1.8rem 0;\n  animation-name: opacityAnimation;\n  animation-duration: .6s;\n  display: none;\n  position: absolute;\n  top: 82px;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content:not(:first-child) {\n  padding-top: 1.8rem;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content {\n  background-color: #fff;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link {\n  width: 100%;\n  cursor: pointer;\n  background-color: #0000;\n  border: none;\n  flex-direction: column;\n  align-items: center;\n  text-decoration: none;\n  display: flex;\n  position: relative;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link.active .dropdown-element-icon svg, .wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link:hover .dropdown-element-icon svg {\n  fill: #0072e1;\n  transition: all .6s;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link.active .dropdown-element-icon svg .has-stroke, .wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link:hover .dropdown-element-icon svg .has-stroke {\n  stroke: #0072e1;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link.active .dropdown-element-title, .wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link:hover .dropdown-element-title {\n  color: #0072e1;\n  transition: all .6s;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link .dropdown-element-icon.logout {\n  background-color: #fedbdd;\n  border-radius: 8px;\n  justify-content: center;\n  align-items: center;\n  padding: .6rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link .dropdown-element-icon.logout svg {\n  fill: #fa4b57;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link .dropdown-element-icon svg {\n  fill: #3c5572;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-element-link .dropdown-element-title {\n  color: #3c5572;\n  font-size: .8rem;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left {\n  width: 340px;\n  opacity: 1;\n  z-index: -1;\n  background-color: #fff;\n  border-radius: 8px;\n  flex-direction: column;\n  gap: 1.4rem;\n  padding: 2rem;\n  animation-name: showDropdownAvatarLeft;\n  animation-duration: .65s;\n  display: none;\n  position: absolute;\n  top: 0;\n  right: 101px;\n  box-shadow: 0 1px #00000040;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left hr {\n  border: 1px solid #d5dee9;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-header {\n  flex-direction: column;\n  gap: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-header .dropdown-left-header-title {\n  color: #0072e1;\n  font-family: Mulish-Bold;\n  font-size: 1.4rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-header .dropdown-left-header-description {\n  font-family: Mulish-Light;\n  font-size: .9rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-main {\n  height: 200px;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-footer {\n  justify-content: flex-end;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-footer .dropdown-left-footer-link {\n  justify-content: flex-end;\n  align-items: flex-end;\n  gap: 1rem;\n  text-decoration: none;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-footer .dropdown-left-footer-link .dropdown-left-footer-link-text {\n  font-size: .9rem;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-footer .dropdown-left-footer-link .dropdown-left-footer-link-icon {\n  justify-content: center;\n  align-items: center;\n  display: flex;\n}\n\n.wrapper .wrapper-header .header-right #dropdown-avatar .dropdown-content .dropdown-left .dropdown-left-footer .dropdown-left-footer-link .dropdown-left-footer-link-icon svg {\n  fill: #0072e1;\n  width: 15px;\n  height: 15px;\n}\n\n.wrapper .wrapper-main {\n  grid-template-columns: 4.8rem auto;\n  display: grid;\n}\n\n.wrapper .wrapper-main .main-sidebar {\n  min-height: calc(100vh - 80px - 2rem);\n  z-index: auto;\n  width: 100%;\n  background-color: #fff;\n  flex-direction: column;\n  align-items: center;\n  gap: 4rem;\n  padding-top: 2rem;\n  transition: width .2s;\n  display: flex;\n  position: relative;\n}\n\n.wrapper .wrapper-main .main-sidebar.opened {\n  width: 200px;\n  transition: all .4s;\n  box-shadow: 0 4px 4px #00000040;\n}\n\n.wrapper .wrapper-main .main-sidebar.opened #main-sidebar-ul li {\n  justify-content: flex-start;\n}\n\n.wrapper .wrapper-main .main-sidebar.opened #main-sidebar-ul li .sidebar-element-link {\n  width: 100%;\n  align-items: center;\n  gap: 1rem;\n  padding-left: 1.4rem;\n  transition: padding-left .2s;\n  display: flex;\n}\n\n.wrapper .wrapper-main .main-sidebar.opened #main-sidebar-ul li .sidebar-element-link .sidebar-element-title {\n  opacity: 1;\n  white-space: nowrap;\n  animation-name: opacityAnimation;\n  animation-duration: 1s;\n  display: block;\n}\n\n.wrapper .wrapper-main .main-sidebar.opened #main-sidebar-ul li hr {\n  margin: 0 2.6rem;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul {\n  width: 100%;\n  flex-direction: column;\n  gap: 2rem;\n  display: flex;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li {\n  width: 100%;\n  justify-content: center;\n  align-items: center;\n  display: flex;\n  position: relative;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link {\n  cursor: pointer;\n  background-color: #0000;\n  border: none;\n  text-decoration: none;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link.active .sidebar-element-icon svg, .wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link:hover .sidebar-element-icon svg {\n  fill: #0072e1;\n  transition: all .6s;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link.active .sidebar-element-title, .wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link:hover .sidebar-element-title {\n  color: #0072e1;\n  transition: all .6s;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link .sidebar-element-icon svg {\n  fill: #c4d1e0;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .sidebar-element-link .sidebar-element-title {\n  opacity: 0;\n  font-family: Mulish-SemiBold;\n  font-size: .93rem;\n  display: none;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar {\n  width: 500px;\n  opacity: 1;\n  z-index: -1;\n  background-color: #fff;\n  border-radius: 8px 8px 0 0;\n  flex-direction: column;\n  gap: 1.4rem;\n  animation-name: showDropdownSidebar;\n  animation-duration: .65s;\n  display: none;\n  position: absolute;\n  top: -170px;\n  left: 202px;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-title {\n  padding: 2rem 2rem 0;\n  font-family: Mulish-Bold;\n  font-size: 1.1rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-description {\n  padding: 0 2rem;\n  font-family: Mulish-Light;\n  font-size: .9rem;\n  animation: .4s cubic-bezier(.1, 0, .175, 1) reveal;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar hr {\n  width: 100%;\n  border: 1px solid #d5dee9;\n  margin: 0;\n  animation-name: opacityAnimation;\n  animation-duration: 1s;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content {\n  grid-template-columns: auto auto auto;\n  gap: 2rem;\n  padding: 0 2rem 2rem;\n  display: grid;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div {\n  height: 100px;\n  background-color: #f9fafc;\n  border-radius: 8px;\n  flex-direction: column;\n  justify-content: space-between;\n  gap: 1rem;\n  padding: 1rem;\n  display: flex;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div:hover {\n  background-color: #0072e1;\n  transition: all .4s;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div:hover span {\n  background-color: #fff;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div:hover p {\n  color: #fff;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div:hover a svg {\n  fill: #fff;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div span {\n  min-width: 18px;\n  min-height: 18px;\n  max-width: 18px;\n  max-height: 18px;\n  background-color: #0072e1;\n  border-radius: 50%;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li .dropdown-sidebar .dropdown-content div a svg {\n  fill: #0072e1;\n}\n\n.wrapper .wrapper-main .main-sidebar #main-sidebar-ul li hr {\n  width: 60%;\n  border: 1px solid #d5dee9;\n}\n\n@keyframes showBuilderallLogo {\n  from {\n    padding-left: 0;\n  }\n\n  to {\n    padding-left: 1rem;\n  }\n}\n\n@keyframes showDropdownHeaderCenter {\n  0% {\n    opacity: 0;\n    top: -15px;\n  }\n\n  100% {\n    opacity: 1;\n    top: 82px;\n  }\n}\n\n@keyframes showDropdownAvatarLeft {\n  0% {\n    opacity: 0;\n    right: -15px;\n  }\n\n  100% {\n    opacity: 1;\n    right: 101px;\n  }\n}\n\n@keyframes showDropdownSidebar {\n  0% {\n    opacity: 0;\n    left: 100px;\n  }\n\n  100% {\n    opacity: 1;\n    left: 202px;\n  }\n}\n\n@keyframes opacityAnimation {\n  from {\n    opacity: 0;\n  }\n\n  to {\n    opacity: 1;\n  }\n}\n\n@keyframes reveal {\n  0% {\n    transform: translate(0, 100%);\n  }\n\n  100% {\n    transform: translate(0);\n  }\n}\n\n\n\n</style>\n<div class=\"wrapper\">\n    <header class=\"wrapper-header\">\n        <div class=\"header-left\">\n            <a href=\"https://office.builderall.com/us/office\" title=\"Builderall\">\n                <img src=\"https://cheetah.builderall.com/franquias/2/7291108/editor-html/11332996.svg\" alt=\"Builderall Logo\">\n                <span class=\"header-left-title\">Builderall</span>\n            </a>\n        </div>\n        <nav class=\"header-center\">\n            <ul id=\"header-center-ul\"></ul>\n        </nav>\n        <div class=\"header-right\">\n            <ul id=\"header-right-ul\"></ul>\n            <hr class=\"vertical-line\">\n            <button class=\"avatar\">\n                BA\n            </button>\n            <div id=\"dropdown-avatar\"></div>\n        </div>\n    </header>\n\n    <main class=\"wrapper-main\">\n        <aside class=\"main-sidebar\">\n            <ul id=\"main-sidebar-ul\"></ul>\n        </aside>\n    </main>\n</div>";

});

parcelRequire.register("aCgwa", function(module, exports) {
module.exports = "<button class=\"header-center-btn\">\n    <span class=\"btn-title\"></span>\n    <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"6\" fill=\"currentColor\" class=\"header-center-icon\"><path d=\"M.208.98a.506.506 0 0 1 .37-.157c.142 0 .265.052.37.156L4 4.031 7.062.97a.495.495 0 0 1 .365-.146c.146 0 .27.052.375.156a.506.506 0 0 1 .156.37.505.505 0 0 1-.156.37l-3.51 3.5a.368.368 0 0 1-.136.089.465.465 0 0 1-.313 0 .368.368 0 0 1-.135-.09l-3.51-3.51a.488.488 0 0 1-.146-.359c0-.142.052-.266.156-.37z\"></path><script xmlns=\"\"></script></svg>\n</button>\n<div class=\"dropdown normal\">\n    <div class=\"dropdown-left-content\">\n        <h2 class=\"dropdown-left-content-title\"></h2>\n        <p class=\"dropdown-left-content-description\"></p>\n    </div>\n    <hr class=\"dropdown-hr\">\n    <div class=\"dropdown-main-content\"></div>\n</div>\n<div class=\"dropdown sections\"></div>\n<input type=\"hidden\" class=\"header-center-hidden-input\">";

});

parcelRequire.register("gxvcP", function(module, exports) {
module.exports = "<div class=\"tooltip\">\n    <p class=\"tooltip-message\"></p>\n</div>";

});

parcelRequire.register("uBj66", function(module, exports) {
module.exports = "<a class=\"sidebar-element-link\">\n    <span class=\"sidebar-element-icon\"></span>\n    <span class=\"sidebar-element-title\"></span>\n</a>\n<div class=\"dropdown-sidebar\">\n    <h2 class=\"dropdown-title\"></h2>\n    <p class=\"dropdown-description\"></p>\n    <hr>\n    <div class=\"dropdown-content\"></div>\n</div>";

});

parcelRequire.register("Ni4Er", function(module, exports) {
module.exports = "<a class=\"dropdown-element-link\">\n    <span class=\"dropdown-element-icon\"></span>\n    <span class=\"dropdown-element-title\"></span>\n</a>\n<div class=\"dropdown-left\">\n    <div class=\"dropdown-left-header\">\n        <h2 class=\"dropdown-left-header-title\"></h2>\n        <p class=\"dropdown-left-header-description\"></p>\n    </div>\n    <hr>\n    <div class=\"dropdown-left-main\"></div>\n    <hr>\n    <div class=\"dropdown-left-footer\">\n        <a href=\"#\" class=\"dropdown-left-footer-link\">\n            <span class=\"dropdown-left-footer-link-text\"></span>\n            <span class=\"dropdown-left-footer-link-icon\"></span>\n        </a>\n    </div>\n</div>";

});






customElements.define("builderall-appshell", class extends HTMLElement {
    constructor(){
        super();
        this.attachShadow({
            mode: "open"
        });
        this.shadowRoot.innerHTML = (parcelRequire("7S9ok"));
        this.mountHeaderDropdown();
        this.mountHeaderTooltip();
        this.mountSidebarElements();
        this.mountAvatarElements();
        this.defaultActions();
    }
    defaultActions() {
        const mainSidebarElement = this.shadowRoot.querySelector(".main-sidebar");
        const wrapperHeaderElement = this.shadowRoot.querySelector(".wrapper-header");
        const baAppContentElement = ()=>document.querySelector(".ba-app-content");
        wrapperHeaderElement?.addEventListener("mouseenter", ()=>{
            baAppContentElement().style.zIndex = "-5";
        });
        wrapperHeaderElement?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                baAppContentElement().style.zIndex = "5";
            }, 200);
            wrapperHeaderElement.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
        });
        mainSidebarElement?.addEventListener("mouseenter", ()=>{
            baAppContentElement().style.zIndex = "-5";
        });
        mainSidebarElement?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                baAppContentElement().style.zIndex = "5";
            }, 200);
            mainSidebarElement.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
        });
    }
    mountHeaderDropdown() {
        const dropdownTemplate = (parcelRequire("aCgwa"));
        const headerDropdownItems = this.getHeaderDropdownItems();
        headerDropdownItems.forEach((it)=>{
            const li = document.createElement("li");
            li.innerHTML = dropdownTemplate;
            const dropdownSectionsElement = li.querySelector(".dropdown.sections");
            // Set title to menu
            li.querySelector(".btn-title").textContent = it.element;
            // Set new value to hidden input with title and description about main element
            li.querySelector(".header-center-hidden-input").value = JSON.stringify([
                it.element,
                it.description
            ]);
            /* Dropdown */ if (it.type == "normal") {
                li.querySelector(".dropdown.normal").classList.add("show");
                // Set left content to dropdown
                this.resetDropdownData(li);
                var mainContent = li.querySelector(".dropdown-main-content");
                it.data.forEach((d)=>{
                    let newMainDiv = document.createElement("div");
                    newMainDiv.className = "dropdown-main-content-main-div";
                    // Main content in dropdown with icon
                    let newDiv = document.createElement("div");
                    let newSpan = document.createElement("span");
                    let newP = document.createElement("p");
                    newDiv.className = "dropdown-main-content-title-column-div";
                    newSpan.innerHTML = d.icon;
                    newP.textContent = d.title;
                    newDiv.appendChild(newSpan);
                    newDiv.appendChild(newP);
                    newMainDiv.appendChild(newDiv);
                    // Elements in dropdown
                    var newUl = document.createElement("ul");
                    var hasShowFalse = false;
                    d.data.forEach((e)=>{
                        let newLi = document.createElement("li");
                        let newA1 = document.createElement("a");
                        let newA2 = document.createElement("a");
                        let newHiddenInput = document.createElement("input");
                        newA1.href = e.href;
                        newA1.textContent = e.title;
                        newA1.className = "link-tool";
                        newA2.href = e.href;
                        newA2.innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' width='10' height='11' fill='none'><path d='M8.889 9.389H1.11V1.61H5V.5H1.111C.494.5 0 1 0 1.611V9.39A1.11 1.11 0 0 0 1.111 10.5H8.89C9.5 10.5 10 10 10 9.389V5.5H8.889v3.889zM6.11.5v1.111h1.995L2.644 7.072l.784.784 5.46-5.462V4.39H10V.5H6.111z' fill='#0080FC'/><script xmlns=''/></svg>";
                        newA2.target = "_blank";
                        newA2.className = "link-blank-tool";
                        newHiddenInput.type = "hidden";
                        newHiddenInput.value = JSON.stringify([
                            e.title,
                            e.description
                        ]);
                        if (!e.show) {
                            newLi.className = "li-dont-show";
                            hasShowFalse = true;
                        }
                        newLi.appendChild(newHiddenInput);
                        newLi.appendChild(newA1);
                        newLi.appendChild(newA2);
                        newUl.appendChild(newLi);
                        newMainDiv.appendChild(newUl);
                    });
                    // Show All Tools Button or Retract
                    let newButton = document.createElement("button");
                    newButton.textContent = hasShowFalse ? "All tools" : Object.entries(d).length > 5 ? "Retract" : "";
                    newButton.className = "dropdown-main-content-btn all-tools";
                    newMainDiv.appendChild(newButton);
                    mainContent?.appendChild(newMainDiv);
                });
            } else {
                dropdownSectionsElement?.classList.add("show");
                for(let i = 0; i < it.data.length; i++){
                    let newMainDiv = document.createElement("div");
                    let newH2 = document.createElement("h2");
                    let newSpan = document.createElement("span");
                    let newHr = document.createElement("hr");
                    newMainDiv.className = "dropdown-sections-content";
                    newH2.textContent = it.data[i].title;
                    newH2.className = "dropdown-sections-title";
                    newSpan.innerHTML = it.data[i].description;
                    newSpan.className = "dropdown-sections-description";
                    newHr.className = "dropdown-hr";
                    newMainDiv.appendChild(newH2);
                    newMainDiv.appendChild(newSpan);
                    let newDiv = document.createElement("div");
                    newDiv.className = "dropdown-sections-content-elements";
                    if (it.data[i].slug == "connect_community") newDiv.classList.add("connect-community");
                    else if (it.data[i].slug == "connect_real_time_support") newDiv.classList.add("connect-real-time-support");
                    else if (it.data[i].slug == "connect_help") newDiv.classList.add("connect-help");
                    it.data[i].data.forEach((e)=>{
                        let newLink = document.createElement("a");
                        newLink.href = e.href;
                        if (e.icon) {
                            let newIconSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                            newIconSvg.setAttribute("width", "40px");
                            newIconSvg.setAttribute("height", "40px");
                            newIconSvg.innerHTML = e.icon;
                            newLink.appendChild(newIconSvg);
                        }
                        if (e.title) {
                            let newTitleSpan = document.createElement("span");
                            newTitleSpan.textContent = e.title;
                            newLink.appendChild(newTitleSpan);
                        }
                        newDiv.append(newLink);
                        newMainDiv.appendChild(newDiv);
                    });
                    if (i != 0) dropdownSectionsElement?.appendChild(newHr);
                    dropdownSectionsElement?.appendChild(newMainDiv);
                }
            }
            // Actions
            this.defineAddEventListenerToHeaderDropdown(li);
            this.shadowRoot?.getElementById("header-center-ul").appendChild(li);
        });
    }
    defineAddEventListenerToHeaderDropdown(element) {
        const headerCenterElement = this.shadowRoot?.querySelector(".header-center");
        const headerCenterBtnElement = element.querySelector(".header-center-btn");
        const dropdownMainContentBtns = element.querySelectorAll(".dropdown-main-content-btn");
        const linkToolDropdownElement = element.querySelectorAll(".link-tool");
        const dropdownElement = element.querySelector(".dropdown.show");
        // Show Dropdown
        headerCenterBtnElement?.addEventListener("mouseenter", ()=>{
            this.shadowRoot?.querySelectorAll(".dropdown").forEach((el)=>el.style.display = "none");
            this.resetDropdownData(element);
            dropdownElement.style.display = "flex";
            // Add active class
            this.shadowRoot?.querySelectorAll(".header-center-btn").forEach((el)=>{
                if (el.classList.contains("active")) el.classList.remove("active");
            });
            headerCenterBtnElement?.classList.add("active");
            // Change z-index
            this.shadowRoot?.querySelectorAll(".dropdown").forEach((el)=>el.style.zIndex = "-1");
        });
        // Hide Dropdown
        element.querySelector(".dropdown")?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                dropdownElement.style.display = "none";
                this.resetDropdownData(element);
            }, 200);
            headerCenterElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
        });
        headerCenterElement?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                dropdownElement.style.display = "none";
                this.resetDropdownData(element);
            }, 200);
            headerCenterElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
            // Change z-index
            this.shadowRoot?.querySelectorAll(".dropdown").forEach((el)=>el.style.zIndex = 3);
        });
        // Show All Tools or Hide Tools
        dropdownMainContentBtns.forEach((el)=>{
            el.addEventListener("click", ()=>{
                if (el.classList.contains("all-tools")) {
                    el.classList.remove("all-tools");
                    el.textContent = "Retract";
                } else {
                    el.classList.add("all-tools");
                    el.textContent = "All tools";
                }
                // Set tools with display block or none
                let ul = el.previousElementSibling;
                ul.querySelectorAll(".li-dont-show").forEach((el)=>{
                    el.style.display = el.style.display == "flex" ? "none" : "flex";
                });
            });
        });
        // Set new title and description in left content
        linkToolDropdownElement.forEach((el)=>{
            el.addEventListener("mouseenter", ()=>{
                let data = JSON.parse(el.previousElementSibling.value);
                const titleEl = element.querySelector(".dropdown-left-content-title");
                titleEl.textContent = data[0];
                const descEl = element.querySelector(".dropdown-left-content-description");
                descEl.textContent = data[1];
            });
        });
    }
    resetDropdownData(element) {
        element.querySelector(".header-center-btn")?.classList.remove("active");
        let data = JSON.parse(element.querySelector(".header-center-hidden-input")?.value);
        element.querySelector(".dropdown-left-content-title").textContent = data[0];
        element.querySelector(".dropdown-left-content-description").textContent = data[1];
    }
    getHeaderDropdownItems() {
        return [
            {
                id: 1,
                element: "Tools",
                slug: "tools",
                type: "normal",
                left_title: "Builderall Tools",
                description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                data: [
                    {
                        title: "Builder",
                        slug: "tools_builder",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='16' fill='none'><path d='M15.272 2.989c0-.48-.209-.69-.688-.69H2.504c-.445 0-.655.211-.658.66-.002.444 0 .887.001 1.33 0 .052.01.104.014.158h13.412V2.99zM17.02 4.454c.56.005 1.028-.45 1.032-1.005a1.026 1.026 0 0 0-1.013-1.042 1.028 1.028 0 0 0-1.034 1.005 1.025 1.025 0 0 0 1.015 1.042z' fill='#0072E1'/><path d='M18.263.467H1.74C.682.467.003 1.152.002 2.227c-.002 3.939 0 7.878 0 11.817 0 .144.004.292.033.432.164.799.808 1.325 1.625 1.326 2.77.002 5.54 0 8.312 0l.905.001c2.496 0 4.991.002 7.487-.002.733-.001 1.35-.456 1.549-1.154a2.65 2.65 0 0 0 .083-.715c.005-2.447.003-4.893.003-7.34 0-1.45.005-2.902-.002-4.353-.006-1.084-.684-1.772-1.732-1.772zm.79 6.313c0 2.15.001 4.3-.004 6.45 0 .21-.016.429-.075.629-.18.614-.738 1.013-1.402 1.014-2.26.004-4.52.002-6.779.002H2.447c-.74-.001-1.322-.464-1.47-1.166a1.868 1.868 0 0 1-.03-.38C.945 9.867.944 6.404.947 2.942c0-.945.615-1.547 1.574-1.547h14.96c.95 0 1.564.605 1.569 1.558.007 1.275.002 2.55.002 3.826z' fill='#0072E1'/><path d='M9.256 5.7H2.698c-.37 0-.67.3-.67.67v6.152c0 .143 0 .582.367.916.31.282.675.288.86.288h6.001c.37 0 .669-.3.669-.669V6.37c0-.37-.3-.67-.67-.67zm-.67 6.688h-5.22l.001-3.756V7.038h5.22v5.35z' fill='#0072E1'/><path d='M4.856 11.543a.67.67 0 0 0 .985-.11l1.92-2.656a.669.669 0 1 0-1.084-.784l-1.488 2.058-.311-.275a.669.669 0 1 0-.887 1.002l.865.765zM11.395 13.971h6.052c.506 0 .704-.198.705-.707V5.552h-6.757v8.42z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "Builderall Builder",
                                href: "#",
                                description: "Builderall Builder is the fastest website builder on Earth. Eliminate the worry of losing site visitors because it takes too long to load!",
                                show: true
                            },
                            {
                                title: "Booking Builder",
                                href: "#",
                                description: "Booking Builder is the ...",
                                show: true
                            },
                            {
                                title: "eLearning Builder",
                                href: "#",
                                description: "eLearning Builder is the ...",
                                show: true
                            },
                            {
                                title: "Webinar Builder",
                                href: "#",
                                description: "Webinar Builder is the ...",
                                show: true
                            },
                            {
                                title: "Quiz Builder",
                                href: "#",
                                description: "Quiz Builder is the ...",
                                show: true
                            }
                        ]
                    },
                    {
                        title: "Email & Engage",
                        slug: "tools_email_engage",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='19' height='19' fill='none'><path d='M9.5 4.75A4.764 4.764 0 0 0 4.75 9.5a4.764 4.764 0 0 0 4.75 4.75h4.75A4.764 4.764 0 0 0 19 9.5C19 4.251 14.749 0 9.5 0A9.497 9.497 0 0 0 0 9.5C0 14.749 4.251 19 9.5 19H19v-2.375H9.5A7.115 7.115 0 0 1 2.375 9.5 7.115 7.115 0 0 1 9.5 2.375 7.115 7.115 0 0 1 16.625 9.5a2.382 2.382 0 0 1-2.375 2.375h-.665c.404-.713.665-1.496.665-2.375A4.764 4.764 0 0 0 9.5 4.75zm0 7.125A2.382 2.382 0 0 1 7.125 9.5 2.382 2.382 0 0 1 9.5 7.125 2.382 2.382 0 0 1 11.875 9.5 2.382 2.382 0 0 1 9.5 11.875z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "MailingBoss",
                                href: "#",
                                description: "MailingBoss is the ...",
                                show: true
                            },
                            {
                                title: "WhatsApp Central",
                                href: "#",
                                description: "WhatsApp Central is the ...",
                                show: true
                            },
                            {
                                title: "Telegram",
                                href: "#",
                                description: "Telegram is the ...",
                                show: true
                            },
                            {
                                title: "SMS Messaging",
                                href: "#",
                                description: "SMS Messaging is the ...",
                                show: true
                            },
                            {
                                title: "Messanger / Instagram Chatbot",
                                href: "#",
                                description: "Messanger/Instagram Chatbot is the ...",
                                show: true
                            },
                            {
                                title: "Sitebot",
                                href: "#",
                                description: "Sitebot is the ...",
                                show: false
                            },
                            {
                                title: "Social Autopost App",
                                href: "#",
                                description: "Social Autopost App is the ...",
                                show: false
                            },
                            {
                                title: "Social Proof Pop-Up",
                                href: "#",
                                description: "Telegram Chatbot is the ...",
                                show: false
                            },
                            {
                                title: "Browser Notifications",
                                href: "#",
                                description: "Browser Notifications is the ...",
                                show: false
                            },
                            {
                                title: "ClickMap",
                                href: "#",
                                description: "ClickMap is the ...",
                                show: false
                            }
                        ]
                    },
                    {
                        title: "Design/Video",
                        slug: "tools_design_video",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='17' fill='none'><path d='M13.817 15.19H8.183a.388.388 0 0 0-.39.39v.91c0 .217.173.39.39.39h5.634c.216 0 .39-.173.39-.39v-.91a.388.388 0 0 0-.39-.39zM11.888 4.377a1.025 1.025 0 0 0-.476-.433v4.659c.476.173.801.628.801 1.148 0 .672-.541 1.214-1.213 1.214A1.211 1.211 0 0 1 9.786 9.75c0-.52.347-.975.802-1.148V3.966c-.195.086-.347.238-.477.411l-2.903 5.07c-.195.326-.26.716-.174 1.084l.694 3.446c.043.173.195.325.39.325h5.785c.195 0 .347-.13.39-.325l.694-3.446a1.462 1.462 0 0 0-.173-1.083l-2.926-5.07z' fill='#0072E1'/><path d='M20.23 2.124c.586 0 1.063-.477 1.063-1.062C21.293.477 20.816 0 20.23 0c-.455 0-.845.303-.997.715H12.04V.022H9.938v.693H2.766A1.077 1.077 0 0 0 1.769 0C1.184 0 .707.477.707 1.062c0 .585.477 1.062 1.062 1.062.455 0 .845-.304.997-.715H7.25a9.701 9.701 0 0 0-5.85 7.865H.707v2.102H2.81V9.274h-.585A8.836 8.836 0 0 1 9.938 1.56v.585h2.102V1.56a8.836 8.836 0 0 1 7.714 7.714h-.585v2.102h2.102V9.274h-.694a9.701 9.701 0 0 0-5.85-7.865h4.507c.152.433.542.715.997.715z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "Magazine Builder",
                                href: "#",
                                description: "Magazine Builder is the ...",
                                show: true
                            },
                            {
                                title: "Mockup Studio",
                                href: "#",
                                description: "Mockup Studio is the ...",
                                show: true
                            },
                            {
                                title: "Video Hosting",
                                href: "#",
                                description: "Video Hosting is the ...",
                                show: true
                            },
                            {
                                title: "3D Photos Studio",
                                href: "#",
                                description: "3D Photos Studio is the ...",
                                show: true
                            }
                        ]
                    }
                ]
            },
            {
                id: 2,
                element: "University",
                slug: "university",
                type: "normal",
                left_title: "Builderall University",
                description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                data: [
                    {
                        title: "Training",
                        slug: "university_training",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'><path d='M21.628.5H4.373a4.328 4.328 0 0 0-3.05 1.255 4.272 4.272 0 0 0-1.264 3.03v8.572c.004.755.21 1.494.598 2.143a4.222 4.222 0 0 0-.598 2.143c0 .758.303 1.485.842 2.02.54.536 1.27.837 2.034.837H13c.763 0 1.494-.3 2.034-.837.539-.535.842-1.262.842-2.02h5.751a4.327 4.327 0 0 0 3.05-1.255 4.272 4.272 0 0 0 1.264-3.03V4.785a4.272 4.272 0 0 0-1.263-3.03A4.328 4.328 0 0 0 21.628.5zM7.968 4.786c.762 0 1.493.3 2.033.837a2.848 2.848 0 0 1 0 4.04 2.886 2.886 0 0 1-4.067 0 2.847 2.847 0 0 1-.843-2.02c.001-.758.305-1.484.844-2.02a2.887 2.887 0 0 1 2.032-.837zM13 19.07H2.935c-.382 0-.747-.15-1.017-.419a1.426 1.426 0 0 1-.421-1.01 2.852 2.852 0 0 1 .843-2.019 2.89 2.89 0 0 1 2.033-.837h7.19a2.889 2.889 0 0 1 2.032.837 2.85 2.85 0 0 1 .843 2.02c0 .378-.152.742-.421 1.01-.27.267-.635.418-1.017.418z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "Quick Start Training",
                                href: "#",
                                description: "Quick Start Training is the fastest website builder on Earth. Eliminate the worry of losing site visitors because it takes too long to load!",
                                show: true
                            },
                            {
                                title: "Builderall University Live",
                                href: "#",
                                description: "Builderall University Live is the ...",
                                show: true
                            },
                            {
                                title: "Email Marketing + Mailing Boss",
                                href: "#",
                                description: "Email Marketing + Mailing Boss is the ...",
                                show: true
                            },
                            {
                                title: "Directory Builder",
                                href: "#",
                                description: "Directory Builder is the ...",
                                show: true
                            },
                            {
                                title: "Local Business Website Agency Course",
                                href: "#",
                                description: "Local Business Website Agency Course is the ...",
                                show: true
                            },
                            {
                                title: "Facebook Ads Course - Module 1",
                                href: "#",
                                description: "Facebook Ads Course - Module 1 ...",
                                show: true
                            }
                        ]
                    },
                    {
                        title: "Add-ons",
                        slug: "university_add_ons",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='20' fill='none'><path d='M15.771 0a.639.639 0 0 0-.317.106.686.686 0 0 0-.292.416l-.498 1.942a.716.716 0 0 0 .065.524.663.663 0 0 0 .402.32c.169.048.349.022.5-.07a.685.685 0 0 0 .305-.422l.5-1.937a.713.713 0 0 0-.116-.618.647.647 0 0 0-.549-.26zm-4.309 2.652a.638.638 0 0 0-.394.165L9.47 4.26l4.821 8.729 1.995-.724a.672.672 0 0 0 .4-.41.715.715 0 0 0-.047-.584l-4.572-8.278a.672.672 0 0 0-.257-.26.633.633 0 0 0-.349-.08zm8.304.534a.637.637 0 0 0-.34.09l-1.665 1.007a.683.683 0 0 0-.313.417.716.716 0 0 0 .064.527c.088.16.234.275.404.322.17.046.351.02.503-.075l1.665-1.005a.687.687 0 0 0 .32-.472.71.71 0 0 0-.14-.56.648.648 0 0 0-.497-.252zm-5.48 1.071 2.417 4.376c.527-.872.601-2.009.076-2.96a2.717 2.717 0 0 0-2.493-1.416zm-5.83.922-6.504 5.877 2.95 5.342 8.123-2.95-4.568-8.27zm10.07 2.652a.638.638 0 0 0-.372.13.691.691 0 0 0-.263.428.714.714 0 0 0 .09.5.66.66 0 0 0 .396.298l1.857.52a.64.64 0 0 0 .628-.185.71.71 0 0 0 .17-.658.676.676 0 0 0-.456-.489L18.72 7.86a.63.63 0 0 0-.194-.028zM1.273 12.58l-.942.568a.684.684 0 0 0-.308.417.715.715 0 0 0 .064.522l1.371 2.484a.662.662 0 0 0 .401.32.633.633 0 0 0 .5-.072l.942-.568-2.028-3.67zm8.025 3.678L5.791 17.53l2.11 2.262a.633.633 0 0 0 .798.115l1.866-1.126a.706.706 0 0 0 .131-1.081l-1.398-1.444z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "Inbox",
                                href: "#",
                                description: "Inbox is the ...",
                                show: true
                            },
                            {
                                title: "Solo Ads",
                                href: "#",
                                description: "Solo Ads is the ...",
                                show: true
                            },
                            {
                                title: "TyBL Solo Ads",
                                href: "#",
                                description: "TyBL Solo Ads is the ...",
                                show: true
                            }
                        ]
                    },
                    {
                        title: "Event",
                        slug: "university_event",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='21' height='20' fill='none'><path d='M20.144 18.34v.256A1.407 1.407 0 0 1 18.74 20H1.404A1.406 1.406 0 0 1 0 18.596v-.256c.322.457.846.729 1.404.728H18.74c.559 0 1.083-.271 1.404-.728zM4.34.703v2.025a.703.703 0 0 1-1.406 0V.703a.703.703 0 1 1 1.406 0zM10.774.703v2.025a.702.702 0 1 1-1.404 0V.703a.702.702 0 1 1 1.404 0zM7.557.703v2.025a.702.702 0 1 1-1.404 0V.703a.702.702 0 1 1 1.404 0zM13.992.703v2.025a.702.702 0 1 1-1.404 0V.703a.702.702 0 1 1 1.404 0z' fill='#0072E1'/><path d='M20.146 2.889v2.489H0v-2.49a1.406 1.406 0 0 1 1.404-1.403h1.215v1.169a1.018 1.018 0 0 0 2.035 0V1.485h1.184v1.169a1.016 1.016 0 1 0 2.034 0V1.485h1.183v1.169a1.017 1.017 0 0 0 2.034 0V1.485h1.183v1.169a1.017 1.017 0 0 0 2.034 0V1.485h1.183v1.169a1.018 1.018 0 0 0 2.036 0V1.485h1.217a1.394 1.394 0 0 1 1.293.86c.074.172.112.357.111.544z' fill='#0072E1'/><path d='M17.21.703v2.025a.703.703 0 1 1-1.406 0V.703a.703.703 0 0 1 1.406 0zM0 5.692v11.66a1.405 1.405 0 0 0 1.404 1.401h17.337a1.406 1.406 0 0 0 1.404-1.401V5.692H0zm1.6 1.202h4.39a.157.157 0 0 1 0 .314H1.6a.157.157 0 1 1 0-.314zm2.81 1.39H1.6a.157.157 0 0 1 0-.315h2.81a.157.157 0 1 1 0 .315zm10.417 2.77-2.263 2.205.535 3.115a.158.158 0 0 1-.228.165l-2.798-1.469-2.795 1.47a.149.149 0 0 1-.073.016.157.157 0 0 1-.155-.183l.532-3.114-2.262-2.205a.151.151 0 0 1-.04-.16.154.154 0 0 1 .126-.107l3.127-.457 1.4-2.833a.163.163 0 0 1 .281 0l1.4 2.833 3.127.457a.156.156 0 0 1 .086.267z' fill='#0072E1'/><script xmlns=''/></svg>",
                        data: [
                            {
                                title: "Workshops",
                                href: "#",
                                description: "Workshops is the ...",
                                show: true
                            },
                            {
                                title: "Event Calendar",
                                href: "#",
                                description: "Event Calendar is the ...",
                                show: true
                            }
                        ]
                    }
                ]
            },
            {
                id: 3,
                element: "Connect",
                slug: "connect",
                type: "sections",
                data: [
                    {
                        title: "Community",
                        slug: "connect_community",
                        description: "Join <b>Builderall</b> on social media",
                        data: [
                            {
                                title: "",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'><path fill='#0072E1' d='M18.518.001H1.476A1.46 1.46 0 0 0 0 1.443v17.114A1.46 1.46 0 0 0 1.476 20h17.042c.808.01 1.47-.636 1.482-1.443V1.442A1.463 1.463 0 0 0 18.518 0'/><path d='M14.078 17.041h2.963l.001-5.235c0-2.57-.554-4.546-3.557-4.546a3.118 3.118 0 0 0-2.807 1.542h-.04V7.497H7.793v9.544h2.964V12.32c0-1.245.236-2.45 1.78-2.45 1.52 0 1.54 1.424 1.54 2.53v4.641zM2.73 4.473a1.72 1.72 0 1 0 3.44 0 1.72 1.72 0 0 0-3.44 0zM2.965 17.041h2.966V7.497H2.965v9.544z' fill='#fff'/><script xmlns=''/></svg>",
                                href: "#"
                            },
                            {
                                title: "",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'><path d='M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z' fill='#0072E1'/><path d='m13.893 12.89.443-2.89h-2.774V8.124c0-.79.388-1.562 1.63-1.562h1.261v-2.46s-1.144-.196-2.238-.196c-2.285 0-3.777 1.385-3.777 3.89V10h-2.54v2.89h2.54v6.988a10.073 10.073 0 0 0 3.124 0v-6.987h2.33z' fill='#fff'/><script xmlns=''/></svg>",
                                href: "#"
                            },
                            {
                                title: "",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none'><path fill-rule='evenodd' clip-rule='evenodd' d='M4.865 10a5.135 5.135 0 1 1 10.27 0 5.135 5.135 0 0 1-10.27 0zM10 13.333a3.333 3.333 0 1 1 0-6.666 3.333 3.333 0 0 1 0 6.666z' fill='#0072E1'/><path fill-rule='evenodd' clip-rule='evenodd' d='M4.865 10a5.135 5.135 0 1 1 10.27 0 5.135 5.135 0 0 1-10.27 0zM10 13.333a3.333 3.333 0 1 1 0-6.666 3.333 3.333 0 0 1 0 6.666z' fill='#0072E1'/><path d='M15.338 5.862a1.2 1.2 0 1 0 0-2.4 1.2 1.2 0 0 0 0 2.4z' fill='#0072E1'/><path d='M15.338 5.862a1.2 1.2 0 1 0 0-2.4 1.2 1.2 0 0 0 0 2.4z' fill='#0072E1'/><path fill-rule='evenodd' clip-rule='evenodd' d='M10 0C7.284 0 6.944.012 5.877.06 4.813.11 4.086.278 3.45.525a4.902 4.902 0 0 0-1.772 1.153A4.902 4.902 0 0 0 .525 3.45C.278 4.086.109 4.813.06 5.877.012 6.944 0 7.284 0 10s.012 3.056.06 4.123c.049 1.064.218 1.791.465 2.427a4.902 4.902 0 0 0 1.153 1.772 4.902 4.902 0 0 0 1.772 1.153c.636.247 1.363.416 2.427.465 1.067.048 1.407.06 4.123.06s3.056-.012 4.123-.06c1.064-.049 1.791-.218 2.427-.465a4.902 4.902 0 0 0 1.772-1.153 4.902 4.902 0 0 0 1.153-1.772c.247-.636.416-1.363.465-2.427.048-1.067.06-1.407.06-4.123s-.012-3.056-.06-4.123c-.049-1.064-.218-1.791-.465-2.427a4.902 4.902 0 0 0-1.153-1.772A4.902 4.902 0 0 0 16.55.525C15.914.278 15.187.109 14.123.06 13.056.012 12.716 0 10 0zm0 1.802c2.67 0 2.986.01 4.04.058.976.045 1.505.207 1.858.344.466.182.8.399 1.15.748.35.35.566.684.748 1.15.137.353.3.882.344 1.857.048 1.055.058 1.37.058 4.041 0 2.67-.01 2.986-.058 4.04-.045.976-.207 1.505-.344 1.858-.182.466-.398.8-.748 1.15-.35.35-.683.566-1.15.748-.353.137-.882.3-1.857.344-1.054.048-1.37.058-4.041.058-2.67 0-2.987-.01-4.04-.058-.976-.045-1.505-.207-1.858-.344a3.097 3.097 0 0 1-1.15-.748 3.098 3.098 0 0 1-.748-1.15c-.137-.353-.3-.882-.344-1.857-.048-1.055-.058-1.37-.058-4.041 0-2.67.01-2.986.058-4.04.045-.976.207-1.505.344-1.858.182-.466.399-.8.748-1.15.35-.35.684-.566 1.15-.748.353-.137.882-.3 1.857-.344 1.055-.048 1.37-.058 4.041-.058z' fill='#0072E1'/><path fill-rule='evenodd' clip-rule='evenodd' d='M10 0C7.284 0 6.944.012 5.877.06 4.813.11 4.086.278 3.45.525a4.902 4.902 0 0 0-1.772 1.153A4.902 4.902 0 0 0 .525 3.45C.278 4.086.109 4.813.06 5.877.012 6.944 0 7.284 0 10s.012 3.056.06 4.123c.049 1.064.218 1.791.465 2.427a4.902 4.902 0 0 0 1.153 1.772 4.902 4.902 0 0 0 1.772 1.153c.636.247 1.363.416 2.427.465 1.067.048 1.407.06 4.123.06s3.056-.012 4.123-.06c1.064-.049 1.791-.218 2.427-.465a4.902 4.902 0 0 0 1.772-1.153 4.902 4.902 0 0 0 1.153-1.772c.247-.636.416-1.363.465-2.427.048-1.067.06-1.407.06-4.123s-.012-3.056-.06-4.123c-.049-1.064-.218-1.791-.465-2.427a4.902 4.902 0 0 0-1.153-1.772A4.902 4.902 0 0 0 16.55.525C15.914.278 15.187.109 14.123.06 13.056.012 12.716 0 10 0zm0 1.802c2.67 0 2.986.01 4.04.058.976.045 1.505.207 1.858.344.466.182.8.399 1.15.748.35.35.566.684.748 1.15.137.353.3.882.344 1.857.048 1.055.058 1.37.058 4.041 0 2.67-.01 2.986-.058 4.04-.045.976-.207 1.505-.344 1.858-.182.466-.398.8-.748 1.15-.35.35-.683.566-1.15.748-.353.137-.882.3-1.857.344-1.054.048-1.37.058-4.041.058-2.67 0-2.987-.01-4.04-.058-.976-.045-1.505-.207-1.858-.344a3.097 3.097 0 0 1-1.15-.748 3.098 3.098 0 0 1-.748-1.15c-.137-.353-.3-.882-.344-1.857-.048-1.055-.058-1.37-.058-4.041 0-2.67.01-2.986.058-4.04.045-.976.207-1.505.344-1.858.182-.466.399-.8.748-1.15.35-.35.684-.566 1.15-.748.353-.137.882-.3 1.857-.344 1.055-.048 1.37-.058 4.041-.058z' fill='#0072E1'/><script xmlns=''/></svg>",
                                href: "#"
                            },
                            {
                                title: "",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='15' fill='none'><path fill-rule='evenodd' clip-rule='evenodd' d='M17.814.426c.86.23 1.538.907 1.768 1.768C20 3.754 20 7.008 20 7.008s0 3.254-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.418-7.814.418-7.814.418s-6.254 0-7.814-.418a2.504 2.504 0 0 1-1.768-1.768C0 10.262 0 7.008 0 7.008s0-3.254.418-4.814c.23-.86.908-1.538 1.768-1.768C3.746.008 10 .008 10 .008s6.254 0 7.814.418zm-4.618 6.582-5.196 3v-6l5.196 3z' fill='#0072E1'/><script xmlns=''/></svg>",
                                href: "#"
                            }
                        ]
                    },
                    {
                        title: "Real-time Support",
                        slug: "connect_real_time_support",
                        description: "Come be part of <b>Builderall</b> on social networks",
                        data: [
                            {
                                title: "Ticket",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='29' height='29' fill='none'><path d='M14.5 0A14.5 14.5 0 1 0 29 14.5 14.516 14.516 0 0 0 14.5 0zm12.642 13.598-2.498 1.696a.559.559 0 0 1-.239.08.566.566 0 0 1-.238-.08s-9.092-6.115-9.291-6.232c.019-.226.21-.399.437-.397h.556l8.537 5.718 2.628-1.773c.046.324.084.654.108.988zM14.5 27.188A12.688 12.688 0 1 1 25.224 7.75H14.4a.44.44 0 0 0-.437.437v12.626a.44.44 0 0 0 .437.436h10.824A12.677 12.677 0 0 1 14.5 27.188z' fill='#0072E1'/><path d='M7.51 9.278a.84.84 0 0 0-.964-.206.954.954 0 0 0-.546.879v9.098c0 .384.215.731.546.879a.82.82 0 0 0 .338.072c.235 0 .46-.1.626-.278l4.231-4.55a1.002 1.002 0 0 0 0-1.345L7.51 9.279z' fill='#0072E1'/><script xmlns=''/></svg>",
                                href: "#"
                            },
                            {
                                title: "Chat",
                                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='none'><path d='M12.94 13.315H4.2c-.406 0-.677-.265-.677-.662 0-.398.271-.663.677-.663h11.383a15.632 15.632 0 0 1 5.352-.927c1.355 0 2.642.199 3.862.464.135-.53.203-1.126.203-1.722C25 4.372 19.377 0 12.466 0 5.556 0 0 4.372 0 9.805c0 2.053.813 3.908 2.168 5.498C2.1 17.158 1.423 19.079.068 21c3.048-.265 5.352-1.06 6.775-2.451.542.199 1.016.397 1.558.53.678-2.319 2.304-4.306 4.54-5.764zM4.2 6.293h16.532c.406 0 .677.331.677.663 0 .397-.27.662-.677.662H4.2c-.406 0-.677-.265-.677-.662 0-.332.271-.663.677-.663zm0 2.849h16.532c.406 0 .677.265.677.662a.671.671 0 0 1-.677.663H4.2a.671.671 0 0 1-.677-.663c0-.397.271-.662.677-.662z' fill='#0072E1'/><path d='M31.936 21.884c0-4.929-4.875-8.884-10.968-8.884S10 16.956 10 21.884c.064 4.863 4.939 8.82 11.032 8.82 1.796 0 3.464-.325 5.003-.974C27.254 30.963 29.306 31.74 32 32c-1.219-1.75-1.796-3.502-1.86-5.123 1.09-1.426 1.796-3.177 1.796-4.993zm-4.17 2.918H14.234a.578.578 0 0 1-.577-.584c0-.324.256-.583.577-.583h13.534c.32 0 .577.26.577.583a.578.578 0 0 1-.577.584zm0-2.4H14.234a.578.578 0 0 1-.577-.583c0-.324.256-.584.577-.584h13.534c.32 0 .577.26.577.584a.578.578 0 0 1-.577.584zm0-2.399H14.234a.578.578 0 0 1-.577-.583c0-.325.256-.584.577-.584h13.534c.32 0 .577.26.577.584a.578.578 0 0 1-.577.583z' fill='#0072E1'/><script xmlns=''/></svg>",
                                href: "#"
                            }
                        ]
                    },
                    {
                        title: "Help",
                        slug: "connect_help",
                        description: "Come be part of <b>Builderall</b> on social networks",
                        data: [
                            {
                                title: "Support",
                                icon: "",
                                href: "#"
                            },
                            {
                                title: "Certified Partners",
                                icon: "",
                                href: "#"
                            },
                            {
                                title: "Glossary",
                                icon: "",
                                href: "#"
                            },
                            {
                                title: "Knowledgebase",
                                icon: "",
                                href: "#"
                            },
                            {
                                title: "Onboarding",
                                icon: "",
                                href: "#"
                            }
                        ]
                    }
                ]
            },
            {
                id: 4,
                element: "Earn",
                slug: "earn",
                type: "normal",
                left_title: "Builderall Earn",
                description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                data: [
                    {
                        title: "Builder",
                        slug: "earn_builder",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-window' viewBox='0 0 16 16'><path d='M2.5 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm1 .5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z'/><path d='M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zM2 14a1 1 0 0 1-1-1V6h14v7a1 1 0 0 1-1 1H2z'/></svg>",
                        data: [
                            {
                                title: "Builderall Builder",
                                href: "#",
                                description: "Builderall Builder is the fastest website builder on Earth. Eliminate the worry of losing site visitors because it takes too long to load!",
                                show: true
                            },
                            {
                                title: "eLearning Builder",
                                href: "#",
                                description: "eLearning Builder is the ...",
                                show: true
                            },
                            {
                                title: "Funnel Builder",
                                href: "#",
                                description: "Funnel Builder is the ...",
                                show: true
                            },
                            {
                                title: "Directory Builder",
                                href: "#",
                                description: "Directory Builder is the ...",
                                show: true
                            },
                            {
                                title: "Webinar Builder",
                                href: "#",
                                description: "Webinar Builder is the ...",
                                show: true
                            }
                        ]
                    },
                    {
                        title: "Email & Engage",
                        slug: "earn_email_engage",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-window' viewBox='0 0 16 16'><path d='M2.5 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm1 .5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z'/><path d='M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zM2 14a1 1 0 0 1-1-1V6h14v7a1 1 0 0 1-1 1H2z'/></svg>",
                        data: [
                            {
                                title: "MailingBoss",
                                href: "#",
                                description: "MailingBoss is the ...",
                                show: true
                            },
                            {
                                title: "WhatsApp Launch",
                                href: "#",
                                description: "WhatsApp Launch is the ...",
                                show: true
                            },
                            {
                                title: "Telegram Chatbot",
                                href: "#",
                                description: "Telegram Chatbot is the ...",
                                show: true
                            },
                            {
                                title: "Quiz Builder",
                                href: "#",
                                description: "Quiz Builder is the ...",
                                show: true
                            },
                            {
                                title: "Custom Email",
                                href: "#",
                                description: "Custom Email is the ...",
                                show: true
                            }
                        ]
                    },
                    {
                        title: "Design/Video",
                        slug: "earn_design_video",
                        icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-window' viewBox='0 0 16 16'><path d='M2.5 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm1 .5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z'/><path d='M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zM2 14a1 1 0 0 1-1-1V6h14v7a1 1 0 0 1-1 1H2z'/></svg>",
                        data: [
                            {
                                title: "Magazine Builder",
                                href: "#",
                                description: "Magazine Builder is the ...",
                                show: true
                            },
                            {
                                title: "Mockup Studio",
                                href: "#",
                                description: "Mockup Studio is the ...",
                                show: true
                            },
                            {
                                title: "3D Photos Studio",
                                href: "#",
                                description: "3D Photos Studio is the ...",
                                show: true
                            },
                            {
                                title: "Video Funnel",
                                href: "#",
                                description: "Video Funnel is the ...",
                                show: true
                            },
                            {
                                title: "Video Hosting",
                                href: "#",
                                description: "Video Hosting is the ...",
                                show: true
                            }
                        ]
                    }
                ]
            }
        ];
    }
    mountHeaderTooltip() {
        const tooltipTemplate = (parcelRequire("gxvcP"));
        const headerTooltipItems = this.getHeaderTooltipItems();
        headerTooltipItems.forEach((it)=>{
            const li = document.createElement("li");
            li.innerHTML = tooltipTemplate;
            let newElement;
            if (it.type == "link") {
                newElement = document.createElement("a");
                newElement.href = it.href;
            } else newElement = document.createElement("button");
            li.appendChild(newElement);
            newElement.innerHTML = it.icon;
            newElement.className = "header-right-btn";
            li.querySelector(".tooltip-message").textContent = it.tooltip_message;
            // Actions
            this.defineAddEventListenerToHeaderTooltips(li);
            this.shadowRoot?.getElementById("header-right-ul").appendChild(li);
        });
    }
    defineAddEventListenerToHeaderTooltips(element) {
        // Show Tooltip
        element.querySelector(".header-right-btn")?.addEventListener("mouseenter", ()=>{
            element.querySelector(".tooltip").style.display = "block";
        });
        // Hide Tooltip
        element.querySelector(".header-right-btn")?.addEventListener("mouseleave", ()=>{
            element.querySelector(".tooltip").style.display = "none";
        });
    }
    getHeaderTooltipItems() {
        return [
            {
                id: 1,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='18' fill='none'><path d='M13.188 12.525h5.825a.676.676 0 0 0 .663-.663V.662A.676.676 0 0 0 19.014 0h-5.058a1.456 1.456 0 0 0-1.431 1.343v10.519a.687.687 0 0 0 .663.663zm-8.931 0h5.809a.676.676 0 0 0 .662-.663V1.343A1.48 1.48 0 0 0 9.299 0H4.24a.676.676 0 0 0-.663.663v11.184a.699.699 0 0 0 .68.678z'/><path d='M22.018 2.058a.437.437 0 0 0-.54.454v11.13a.676.676 0 0 1-.663.663H2.459a.676.676 0 0 1-.662-.663V2.547a.454.454 0 0 0-.21-.369.448.448 0 0 0-.422-.032A2.056 2.056 0 0 0 0 4.03V14.32a1.806 1.806 0 0 0 1.797 1.796h7.379a.676.676 0 0 1 .662.663.676.676 0 0 0 .663.663h2.234a.675.675 0 0 0 .663-.667.676.676 0 0 1 .662-.663h7.376a1.806 1.806 0 0 0 1.796-1.797V4.03c.021-.943-.292-1.744-1.214-1.972z'/><script xmlns=''/></svg>",
                type: "link",
                href: "",
                tooltip_message: "Knowledgebase"
            },
            {
                id: 2,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='none'><path d='M23.543 6.115a.913.913 0 0 0-.906.011L18 8.866v7.268l4.637 2.74a.903.903 0 0 0 .906.01.884.884 0 0 0 .457-.77V6.886a.884.884 0 0 0-.457-.771zM17 17.317C17 19.35 15.337 21 13.286 21H4.714C2.663 21 1 19.351 1 17.317V7.683C1 5.65 2.663 4 4.714 4h8.572C15.337 4 17 5.649 17 7.683v9.634z'/><script xmlns=''/></svg>",
                type: "link",
                href: "",
                tooltip_message: "Metting Room"
            },
            {
                id: 3,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='23' height='24' fill='none'><path d='M11.738.985c-4.413 0-8.019 3.419-8.155 7.676h-.501c-1.234 0-2.238.97-2.238 2.176v2.465c0 1.205 1.004 2.175 2.238 2.175h.494v.152c0 4.151 3.48 7.524 7.75 7.524.43 0 .783-.338.783-.763a.772.772 0 0 0-.783-.763c-3.411 0-6.183-2.694-6.183-5.998v-6.72c0-3.524 2.956-6.398 6.595-6.398 3.64 0 6.596 2.874 6.596 6.399v5.81a.772.772 0 0 0 .829.76l.013-.001.016-.002h.583c1.234 0 2.238-.97 2.238-2.175v-2.465c0-1.164-.94-2.11-2.118-2.172-.134-4.26-3.742-7.68-8.157-7.68z' stroke='#C4D1E0' stroke-width='.2'/><path d='M14.74 12.842c.44 0 .797-.347.797-.774a.786.786 0 0 0-.798-.774.786.786 0 0 0-.796.774c0 .427.356.774.796.774zM11.738 12.842c.44 0 .797-.347.797-.774a.786.786 0 0 0-.797-.774.786.786 0 0 0-.797.774c0 .427.357.774.797.774zM8.739 12.842c.44 0 .797-.347.797-.774a.786.786 0 0 0-.797-.774.786.786 0 0 0-.797.774c0 .427.357.774.797.774z' stroke='#C4D1E0' stroke-width='.6'/><script xmlns=''/></svg>",
                type: "link",
                href: "",
                tooltip_message: "Support"
            },
            {
                id: 4,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='21' fill='none'><path d='M7.816 21a2.023 2.023 0 0 0 1.455-.633 2.21 2.21 0 0 0 .605-1.52h-4.12c0 .57.217 1.118.603 1.522.386.404.91.63 1.457.63zM14.595 17.77H1.03a.98.98 0 0 1-.57-.178 1.12 1.12 0 0 1-.437-1.11c.04-.211.142-.405.289-.556l1.325-1.386V9.158a7.91 7.91 0 0 1 1.21-4.378 5.69 5.69 0 0 1 3.428-2.436v-.73c0-.428.163-.838.453-1.141.29-.303.682-.474 1.092-.474.41 0 .802.17 1.092.474.29.303.453.713.453 1.141v.73a5.637 5.637 0 0 1 1.581.653 5.16 5.16 0 0 0-2.475 2.42 5.51 5.51 0 0 0-.461 3.511 5.337 5.337 0 0 0 1.76 3.027 4.932 4.932 0 0 0 4.233 1.084v1.503l1.325 1.386a1.124 1.124 0 0 1 .221 1.177c-.077.196-.21.366-.379.485-.17.119-.37.182-.574.182v-.002z'/><path d='M12.96 11.03c1.68 0 3.04-1.421 3.04-3.175 0-1.754-1.36-3.177-3.04-3.177-1.678 0-3.038 1.423-3.038 3.177s1.36 3.176 3.039 3.176z' fill='#FF3636'/><script xmlns=''/></svg>",
                type: "button",
                action: "",
                tooltip_message: "Notifications"
            }
        ];
    }
    mountSidebarElements() {
        const sidebarTemplate = (parcelRequire("uBj66"));
        const sidebarItems = this.getSidebarItems();
        sidebarItems.forEach((it)=>{
            const li = document.createElement("li");
            li.innerHTML = sidebarTemplate;
            const sidebarElementLinkElement = li.querySelector(".sidebar-element-link");
            if (it == "line") {
                let newHr = document.createElement("hr");
                li.appendChild(newHr);
                sidebarElementLinkElement.style.display = "none";
            } else {
                sidebarElementLinkElement.href = it.href;
                sidebarElementLinkElement.target = it.target;
                li.querySelector(".sidebar-element-icon").innerHTML = it.icon;
                li.querySelector(".sidebar-element-title").textContent = it.title;
                // Data to right dropdown
                if (it.data) {
                    li.querySelector(".dropdown-sidebar")?.classList.add("show");
                    li.querySelector(".dropdown-title").textContent = it.data.title;
                    li.querySelector(".dropdown-description").textContent = it.data.description;
                    let content = li.querySelector(".dropdown-content");
                    it.data.data.forEach((e)=>{
                        let newDiv = document.createElement("div");
                        let newSpan = document.createElement("span");
                        let newP = document.createElement("p");
                        let newA = document.createElement("a");
                        newSpan.innerHTML = e.icon;
                        newP.textContent = e.title;
                        newA.href = e.href;
                        newA.target = e.target;
                        newA.innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='8' fill='currentColor'><path d='M12.01 3H0v2h12.01v3L16 4l-3.99-4v3z'/><script xmlns=''/></svg>";
                        newDiv.appendChild(newSpan);
                        newDiv.appendChild(newP);
                        newDiv.appendChild(newA);
                        content?.appendChild(newDiv);
                    });
                }
            }
            // Actions
            this.defineAddEventListenerToSidebarElements(li);
            this.shadowRoot?.getElementById("main-sidebar-ul").appendChild(li);
        });
    }
    defineAddEventListenerToSidebarElements(element) {
        const mainSidebarElement = this.shadowRoot?.querySelector(".main-sidebar");
        const dropdownSidebarElement = element.querySelector(".dropdown-sidebar");
        const sidebarElementBtnElement = element.querySelector(".sidebar-element-link");
        // Open Sidebar
        mainSidebarElement?.addEventListener("mouseenter", ()=>{
            mainSidebarElement?.classList.add("opened");
            this.shadowRoot?.querySelector(".header-left")?.classList.add("show");
        });
        // Close Sidebar and close dropdwons
        mainSidebarElement?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                this.shadowRoot.querySelector(".wrapper-main").style.height = "auto";
                this.shadowRoot?.querySelector(".header-left")?.classList.remove("show");
                mainSidebarElement?.classList.remove("opened");
                this.shadowRoot?.querySelectorAll(".sidebar-element-link").forEach((el)=>el.classList.remove("active"));
                element.querySelectorAll(".dropdown-sidebar").forEach((el)=>el.style.display = "none");
            }, 200);
            dropdownSidebarElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
            mainSidebarElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
            // Set z-index
            this.shadowRoot?.querySelectorAll(".dropdown-sidebar").forEach((el)=>el.style.zIndex = 1);
        });
        // Hover in sidebar elements
        if (dropdownSidebarElement?.classList.contains("show")) // Show Right Dropdown
        sidebarElementBtnElement?.addEventListener("mouseenter", ()=>{
            // Show Dropdown
            this.shadowRoot?.querySelectorAll(".dropdown-sidebar").forEach((el)=>el.style.display = "none");
            element.querySelector(".dropdown-sidebar").style.display = "flex";
            // Set active class
            this.shadowRoot?.querySelectorAll(".sidebar-element-link").forEach((el)=>el.classList.remove("active"));
            sidebarElementBtnElement?.classList.add("active");
            // Set z-index
            this.shadowRoot?.querySelectorAll(".dropdown-sidebar").forEach((el)=>el.style.zIndex = -1);
            // Verify if dropdown is bellow header
            let needMarginTop = !this.isDivBelow(this.shadowRoot?.querySelector(".wrapper-header"), dropdownSidebarElement);
            if (needMarginTop) dropdownSidebarElement.style.marginTop = "24px";
        });
        else // Remove active class and close all dropdowns
        sidebarElementBtnElement?.addEventListener("mouseenter", ()=>{
            this.shadowRoot?.querySelectorAll(".sidebar-element-link").forEach((el)=>el.classList.remove("active"));
            this.shadowRoot?.querySelectorAll(".dropdown-sidebar").forEach((el)=>el.style.display = "none");
        });
    }
    getSidebarItems() {
        return [
            {
                id: 1,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor'><path d='M7.255 22.744c.128.568-.28 1.12-.913 1.235-.632.115-1.248-.252-1.376-.82L.023 1.257C-.104.689.304.136.937.02c.631-.115 1.248.252 1.376.819l4.942 21.904zM18.761 1.827c-4.353.384-5.13-1.55-9.284-1.494-2.374.032-4.733.596-5.909 1.014l2.95 13.072c.717-.23 1.771-.426 3.193-.331 1.886.125 2.592 1.35 6.81 1.065 3.034-.205 4.709-1.84 4.709-1.84L22.588.459s-.846 1.105-3.827 1.368z'/><script xmlns=''/></svg>",
                title: "BA Nation",
                target: "_blank",
                href: "#"
            },
            {
                id: 2,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor'><path d='M24 17.874v3.6A2.535 2.535 0 0 1 21.474 24h-7.39a2.535 2.535 0 0 1-2.526-2.526v-3.6a2.534 2.534 0 0 1 2.526-2.527h7.39A2.534 2.534 0 0 1 24 17.874zM7.39 0H2.525A2.535 2.535 0 0 0 0 2.526v3.6a2.535 2.535 0 0 0 2.526 2.527H7.39a2.534 2.534 0 0 0 2.527-2.527v-3.6A2.534 2.534 0 0 0 7.389 0zM21.474 0h-7.39a2.535 2.535 0 0 0-2.526 2.526v8.653a2.533 2.533 0 0 0 2.526 2.526h7.39A2.534 2.534 0 0 0 24 11.18V2.526A2.535 2.535 0 0 0 21.474 0zM7.389 10.295H2.526A2.534 2.534 0 0 0 0 12.821v8.653A2.535 2.535 0 0 0 2.526 24H7.39a2.534 2.534 0 0 0 2.527-2.526V12.82a2.533 2.533 0 0 0-2.527-2.526z'/><script xmlns=''/></svg>",
                title: "Dashboard",
                target: "_blank",
                href: "#"
            },
            {
                id: 3,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor' class='bi bi-tools' viewBox='0 0 16 16'><path d='M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0Zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708ZM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11Z'/></svg>",
                title: "Business Center",
                target: "_blank",
                href: "#",
                data: {
                    title: "Build Leverage your digital marketing strategies",
                    description: "such as sharing, liking, commenting, creating content and interacting with other users.",
                    data: [
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        }
                    ]
                }
            },
            "line",
            {
                id: 4,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor'><path d='M19.67 15.44v-2.094c.345-1.017.532-2.107.532-3.24C20.202 4.524 15.68 0 10.102 0 4.521 0 0 4.525 0 10.106c0 5.582 4.523 10.106 10.1 10.106 1.146 0 2.245-.192 3.271-.543h2.072V22h4.227v-2.331H22v-4.23h-2.33zm-7.726.657H8.138V14.07h3.806v2.026zm1.866-7.01c-.111.504-1.866 2.434-1.866 2.434l.003 1.18H8.145l-.004-1.16S6.506 9.601 6.393 9.085c-.078-.356-.16-.719-.16-1.102a3.869 3.869 0 1 1 7.736 0c0 .384-.081.747-.16 1.102zm6.724 9.115h-2.33v2.33H16.91v-2.33h-2.33v-1.295h2.33v-2.331h1.294v2.33h2.33v1.296z'/><script xmlns=''/></svg>",
                title: "Build",
                target: "_blank",
                href: "#",
                data: {
                    title: "Build Leverage your digital marketing strategies",
                    description: "such as sharing, liking, commenting, creating content and interacting with other users.",
                    data: [
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        }
                    ]
                }
            },
            {
                id: 5,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='18' height='24' fill='currentColor'><path d='M5.316 1.9c0 1.05-.931 1.9-2.08 1.9s-2.08-.85-2.08-1.9c0-1.05.931-1.9 2.08-1.9s2.08.85 2.08 1.9zM6.474 7.6v-.386c0-1.63-1.452-2.957-3.237-2.957C1.452 4.257 0 5.583 0 7.214v.387c0 .188.167.34.373.34H6.1c.206 0 .373-.152.373-.34zM16.843 1.9c0 1.05-.931 1.9-2.08 1.9-1.15 0-2.08-.85-2.08-1.9 0-1.05.93-1.9 2.08-1.9 1.149 0 2.08.85 2.08 1.9zM14.763 4.257c-1.785 0-3.237 1.327-3.237 2.958V7.6c0 .188.167.34.373.34h5.728c.206 0 .373-.152.373-.34v-.386c0-1.631-1.452-2.958-3.237-2.958z'/><path d='M4.715 9.066V7.704c0-.187-.167-.34-.373-.34-.205 0-.372.153-.372.34v1.362c0 .188.167.34.372.34.206 0 .373-.152.373-.34zM14.03 9.066V7.704c0-.187-.168-.34-.373-.34-.206 0-.373.153-.373.34v1.362c0 .188.167.34.373.34.205 0 .372-.152.372-.34z'/><path d='M17.247 8.587h-.886V7.566c0-.188-.167-.34-.373-.34-.205 0-.372.152-.372.34v1.021H12.28c-.206 0-.373.153-.373.34v2.99h5.712v-2.99c0-.187-.167-.34-.373-.34zM11.907 16.125c0 1.465-1.305 2.656-2.908 2.656s-2.907-1.191-2.907-2.656v-3.528H.38v3.528C.38 20.467 4.247 24 9 24c4.752 0 8.62-3.533 8.62-7.875v-3.528h-5.712l-.001 3.528zM2.01 7.226c-.205 0-.372.152-.372.34v1.021H.752c-.205 0-.372.153-.372.34v2.99H6.09v-2.99c0-.187-.167-.34-.372-.34H2.383V7.566c0-.188-.167-.34-.372-.34z'/><script xmlns=''/></svg>",
                title: "Engage",
                target: "_blank",
                href: "#",
                data: {
                    title: "Engage Leverage your digital marketing strategies",
                    description: "such as sharing, liking, commenting, creating content and interacting with other users.",
                    data: [
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        }
                    ]
                }
            },
            {
                id: 6,
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='23' height='21' fill='currentColor'><path d='M20.682 14.496a.583.583 0 0 0-.414-.544l-7.432-2.26a.584.584 0 0 0-.737.694l1.684 7.053a.583.583 0 0 0 1.08.144l.877-1.607 2.787 2.849a.584.584 0 0 0 .791.04l1.889-1.575a.584.584 0 0 0 .043-.856l-2.7-2.758 1.742-.616a.584.584 0 0 0 .39-.564z'/><path d='M2.334 14h8.952l-.32-1.344a1.75 1.75 0 0 1 2.21-2.08l7.432 2.26c.402.123.747.389.969.746a2.333 2.333 0 0 0 1.007-1.915V2.333A2.34 2.34 0 0 0 20.25 0H2.333A2.34 2.34 0 0 0 0 2.333v9.334A2.34 2.34 0 0 0 2.334 14z'/><script xmlns=''/></svg>",
                title: "Sell",
                target: "_blank",
                href: "#",
                data: {
                    title: "Sell Leverage your digital marketing strategies",
                    description: "such as sharing, liking, commenting, creating content and interacting with other users.",
                    data: [
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        },
                        {
                            icon: "",
                            title: "Label Text Here",
                            href: "#",
                            target: "_blank"
                        }
                    ]
                }
            },
            "line"
        ];
    }
    mountAvatarElements() {
        const avatarTemplate = (parcelRequire("Ni4Er"));
        const avatarItems = this.getAvatarItems();
        avatarItems.forEach((it)=>{
            const div = document.createElement("div");
            div.className = "dropdown-content";
            div.innerHTML = avatarTemplate;
            const dropdownElementLinkElement = div.querySelector(".dropdown-element-link");
            const dropdownElementIconElement = div.querySelector(".dropdown-element-icon");
            const dropdownLeftFooterLinkTextElement = div.querySelector(".dropdown-left-footer-link-text");
            dropdownElementLinkElement.href = it.href;
            dropdownElementLinkElement.target = it.target;
            dropdownElementIconElement.innerHTML = it.icon;
            if (it.slug == "logout") dropdownElementIconElement?.classList.add("logout");
            if (it.data) {
                dropdownElementLinkElement?.classList.add("show");
                div.querySelector(".dropdown-element-title").textContent = it.title;
                div.querySelector(".dropdown-left-header-title").textContent = it.title;
                div.querySelector(".dropdown-left-header-description").textContent = it.data.description;
                dropdownLeftFooterLinkTextElement.href = it.data.href;
                dropdownLeftFooterLinkTextElement.target = it.data.target;
                dropdownLeftFooterLinkTextElement.textContent = it.data.link_title;
                div.querySelector(".dropdown-left-footer-link-icon").innerHTML = "<svg xmlns='http://www.w3.org/2000/svg' width='8' height='12' fill='none'><path d='M.59 10.59 5.17 6 .59 1.41 2 0l6 6-6 6-1.41-1.41z' fill='#0072E1'/><script xmlns=''/></svg>";
            }
            // Actions
            this.defineAddEventListenerToAvatarElements(div);
            this.shadowRoot?.getElementById("dropdown-avatar").appendChild(div);
        });
    }
    defineAddEventListenerToAvatarElements(element) {
        const avatarElement = this.shadowRoot?.querySelector(".avatar");
        const dropdownElementLinkElement = element.querySelector(".dropdown-element-link");
        const dropdownAvatarElement = this.shadowRoot.getElementById("dropdown-avatar");
        // Open Main Dropdown
        avatarElement?.addEventListener("mouseenter", ()=>{
            dropdownAvatarElement.style.display = "flex";
        });
        // Open Left Dropdown
        dropdownElementLinkElement?.addEventListener("mouseenter", ()=>{
            this.resetAvatarDropdownData();
            if (dropdownElementLinkElement.classList.contains("show")) {
                element.querySelector(".dropdown-left").style.display = "flex";
                element.querySelector(".dropdown-element-link").classList.add("active");
            }
        });
        // Hide Dropdown
        dropdownAvatarElement?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                dropdownAvatarElement.style.display = "none";
                this.resetAvatarDropdownData();
            }, 200);
            dropdownElementLinkElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
            dropdownAvatarElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
        });
        this.shadowRoot?.querySelector(".header-right")?.addEventListener("mouseleave", ()=>{
            let timer = setTimeout(()=>{
                dropdownAvatarElement.style.display = "none";
                this.resetAvatarDropdownData();
            }, 200);
            dropdownAvatarElement?.addEventListener("mouseenter", ()=>{
                clearTimeout(timer);
            });
        });
    }
    resetAvatarDropdownData() {
        this.shadowRoot?.querySelectorAll(".dropdown-left").forEach((el)=>el.style.display = "none");
        this.shadowRoot?.querySelectorAll(".dropdown-element-link").forEach((el)=>el.classList.remove("active"));
    }
    getAvatarItems() {
        return [
            {
                id: 1,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='23' height='24' fill='none'><path d='m11.5 3.004 2.433 3.065 3.863-.425-.42 3.91 3.027 2.463-3.028 2.463.42 3.91-3.862-.425L11.5 21.03l-2.434-3.065-3.862.425.42-3.91-3.027-2.463 3.027-2.464-.42-3.91 3.862.426L11.5 3.004z' fill='#3C5572' stroke='#3C5572' stroke-width='1.185' stroke-linecap='round' stroke-linejoin='round' class='has-stroke' /><path class='has-stroke' d='m7.04 7.503 2.736.301 1.723-2.171 1.724 2.171 2.736-.301-.298 2.77 2.145 1.744-2.145 1.745.298 2.77-2.736-.302-1.724 2.171-1.723-2.17-2.736.3.298-2.769-2.145-1.745 2.145-1.745-.298-2.77z' fill='#fff' stroke='#3C5572' stroke-width='1.185' stroke-linecap='round' stroke-linejoin='round' /></svg>",
                title: "Bagdes",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 2,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='16' fill='currentColor'><path d='M2.261 5.6h9.55a2.4 2.4 0 0 0 0-4.8H2.26a2.4 2.4 0 0 0 0 4.792V5.6zm.248-3.445a1.05 1.05 0 1 1-.742.303 1.05 1.05 0 0 1 .742-.306v.003zM11.81 6.54H2.262a2.4 2.4 0 0 0 0 4.793h4.294v2.907h-3.15a.48.48 0 1 0 0 .96h7.26a.48.48 0 1 0 0-.96h-3.15v-2.907h4.294a2.4 2.4 0 0 0 0-4.792zm-9.3 3.447a1.05 1.05 0 1 1 0-2.1 1.05 1.05 0 0 1 0 2.1z'/><script xmlns=''/></svg>",
                title: "DNS",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 3,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='13' height='16' fill='currentColor'><path d='M6.686 6.457H5.657v-.514a.514.514 0 0 1 1.029 0v.514z'/><path fill-rule='evenodd' clip-rule='evenodd' d='M6.036.818a.513.513 0 0 1 .27 0l5.658 1.543a.514.514 0 0 1 .379.496v6.3a4.629 4.629 0 0 1-2.412 4.064l-3.513 1.916a.514.514 0 0 1-.493 0l-3.513-1.916A4.629 4.629 0 0 1 0 9.157v-6.3c0-.232.155-.435.379-.496L6.036.818zm1.678 5.125v.602c.6.212 1.029.783 1.029 1.455v1.028c0 .852-.69 1.543-1.543 1.543H5.143c-.852 0-1.543-.69-1.543-1.543V8c0-.672.43-1.244 1.029-1.455v-.602a1.543 1.543 0 1 1 3.085 0z'/><script xmlns=''/></svg>",
                title: "Privacy",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 4,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none'><path d='M14.434.8H1.482A1.483 1.483 0 0 0 0 2.28v11.437A1.483 1.483 0 0 0 1.482 15.2h12.952a1.483 1.483 0 0 0 1.481-1.482V2.281A1.483 1.483 0 0 0 14.434.8zm-1.93 9a1.802 1.802 0 0 1-1.757-1.421H9.716a1.798 1.798 0 0 1-3.517 0H5.17a1.8 1.8 0 1 1 0-.758h1.03a1.798 1.798 0 0 1 3.517 0h1.03A1.8 1.8 0 1 1 12.506 9.8z'/><path d='M13.547 8a1.042 1.042 0 1 1-2.084 0 1.042 1.042 0 0 1 2.084 0zM9 8a1.042 1.042 0 1 1-2.084 0A1.042 1.042 0 0 1 9 8zM4.452 8a1.042 1.042 0 1 1-2.084 0 1.042 1.042 0 0 1 2.084 0z'/><script xmlns=''/></svg>",
                title: "First step",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 5,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='14' height='16' fill='none'><path d='M5.83.8a.68.68 0 0 0-.681.685v.943a5.804 5.804 0 0 0-2.304 1.356l-.825-.461a.676.676 0 0 0-.926.262L.086 5.38a.692.692 0 0 0 .268.938l.82.46a5.855 5.855 0 0 0 0 2.443l-.82.461a.692.692 0 0 0-.268.938l1.008 1.794a.676.676 0 0 0 .926.263l.825-.461A5.8 5.8 0 0 0 5.15 13.57v.943c0 .38.301.686.68.686h2.058a.68.68 0 0 0 .68-.686v-.943a5.804 5.804 0 0 0 2.304-1.355l.825.46c.33.186.741.07.926-.262l1.008-1.794a.692.692 0 0 0-.268-.938l-.82-.46a5.858 5.858 0 0 0 0-2.444l.82-.46a.692.692 0 0 0 .268-.938l-1.008-1.795a.676.676 0 0 0-.926-.262l-.825.46a5.8 5.8 0 0 0-2.304-1.355v-.943A.68.68 0 0 0 7.887.8H5.829zm1.028 4.114a3.086 3.086 0 1 1 0 6.172 3.086 3.086 0 0 1 0-6.172z'/><script xmlns=''/></svg>",
                title: "Settings",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 6,
                slug: "",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='16' fill='none'><path d='M7.2.8a7.2 7.2 0 1 0 0 14.4A7.2 7.2 0 0 0 7.2.8zm-.232 10.8a.95.95 0 0 1-.943-.945.901.901 0 0 1 .935-.936.94.94 0 0 1 .008 1.88zm1.617-4.3a7.427 7.427 0 0 1-.307.336c-.404.388-.63.925-.624 1.486v.076H6.3c-.036-.328.001-.66.108-.972.134-.388.343-.746.615-1.054.163-.176.298-.376.4-.593.196-.445-.053-.828-.54-.874a1.88 1.88 0 0 0-1.123.234c-.135-.351-.27-.694-.396-1.04a.134.134 0 0 1 .058-.117c.263-.116.535-.213.813-.288a3.177 3.177 0 0 1 1.627.025c.72.218 1.11.738 1.16 1.49.035.472-.123.938-.437 1.291z'/><script xmlns=''/></svg>",
                title: "Help",
                href: "#",
                target: "_blank",
                data: {
                    description: "Digital marketing is the process of promoting products or services through digital channels such as search engines, social media, email, and websites.",
                    link_title: "View All",
                    href: "#",
                    target: "_blank",
                    data: []
                }
            },
            {
                id: 7,
                slug: "logout",
                icon: "<svg xmlns='http://www.w3.org/2000/svg' width='21' height='18' fill='none'><path d='m15.5 4-1.41 1.41L16.67 8H6.5v2h10.17l-2.58 2.58L15.5 14l5-5-5-5zm-13-2h8V0h-8C1.4 0 .5.9.5 2v14c0 1.1.9 2 2 2h8v-2h-8V2z'/><script xmlns=''/></svg>",
                title: "",
                href: "#",
                target: "_blank"
            }
        ];
    }
    isDivBelow(div1, div2) {
        const rect1 = div1.getBoundingClientRect();
        const rect2 = div2.getBoundingClientRect();
        return rect1.top >= rect2.bottom || rect1.right <= rect2.left || rect1.bottom <= rect2.top || rect1.left >= rect2.right;
    }
});


//# sourceMappingURL=index.js.map
