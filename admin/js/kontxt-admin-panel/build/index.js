/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);





var MyDisabled = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["withState"])({
  isDisabled: false
})(function (_ref) {
  var isDisabled = _ref.isDisabled,
      setState = _ref.setState;

  var runKontxtAnalyze = function runKontxtAnalyze() {
    setState(function (state) {
      return {
        isDisabled: !state.isDisabled
      };
    });
    var select = wp.data.select;
    var title = select('core/editor').getEditedPostAttribute('title').trim();
    var content = select('core/editor').getEditedPostAttribute('content').trim();
    var security = kontxtAjaxObject.security;
    var txtToAnalyze = jQuery('<div>' + title + ' ' + content + '</div>').text();
    txtToAnalyze = txtToAnalyze.replace(/[^0-9a-z\s]/gi, '');
    txtToAnalyze = txtToAnalyze.replace(/\u00A0/g, ' ');
    var data = jQuery.param({
      'action': 'kontxt_analyze',
      'apikey': kontxtAjaxObject.apikey,
      'kontxt_text_to_analyze': txtToAnalyze,
      'service': 'keywords'
    });
    jQuery.ajax({
      type: 'post',
      url: kontxtAjaxObject.ajaxurl,
      security: security,
      data: data,
      cache: false,
      success: function success(response) {
        if (response.status === 'error') {
          jQuery('#kontxt-analyze-results-status').html(response.message).show();
          return false;
        }

        var jsonResponse = JSON.parse(response);
        var jsonResponseSize = jQuery(jsonResponse).length;
        var keywordFieldArray = [];
        var keywordFieldText = null;

        if (jsonResponseSize >= 1) {
          jsonResponse.forEach(function (element) {
            keywordFieldArray.push(element.text.trim());
          });
          keywordFieldText = keywordFieldArray.join(', ');
        } else {
          keywordFieldText = 'No keywords found.';
        }

        jQuery('#kontxt_keywords').val(keywordFieldText);
        setState(function (state) {
          return {
            isDisabled: !state.isDisabled
          };
        });
      }
    });
  };

  var kontxtButton = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Button"], {
    onChange: function onChange() {},
    isPrimary: true,
    onClick: runKontxtAnalyze
  }, "Analyze");

  if (isDisabled) {
    kontxtButton = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Disabled"], null, kontxtButton, " ", Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Spinner"], null));
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, kontxtButton), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextareaControl"], {
    name: "kontxt_keywords",
    type: "text",
    id: "kontxt_keywords",
    placeholder: "Keywords"
  }));
});

var KontxtSettingPanel = function KontxtSettingPanel() {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__["PluginDocumentSettingPanel"], {
    name: "kontxt-admin-panel",
    title: "KONTXT SEO Adviser",
    className: "kontxt-panel"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, "KONTXT will analyze your content and provide you with a set of recommended keywords. Copy and paste these to your Tags to improve SEO."), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(MyDisabled, null));
};

Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__["registerPlugin"])('plugin-document-setting-panel-kontxt', {
  render: KontxtSettingPanel,
  icon: 'performance'
});

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/edit-post":
/*!*******************************************!*\
  !*** external {"this":["wp","editPost"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editPost"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/plugins":
/*!******************************************!*\
  !*** external {"this":["wp","plugins"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["plugins"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map