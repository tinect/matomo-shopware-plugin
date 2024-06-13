"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["extension3924379877_TinectMatomo_src_Resources_app_storefront_src_plugin_override_header_Sear-3ec6ef"],{857:t=>{var e=function(t){var e;return!!t&&"object"==typeof t&&"[object RegExp]"!==(e=Object.prototype.toString.call(t))&&"[object Date]"!==e&&t.$$typeof!==r},r="function"==typeof Symbol&&Symbol.for?Symbol.for("react.element"):60103;function i(t,e){return!1!==e.clone&&e.isMergeableObject(t)?a(Array.isArray(t)?[]:{},t,e):t}function s(t,e,r){return t.concat(e).map(function(t){return i(t,r)})}function n(t){return Object.keys(t).concat(Object.getOwnPropertySymbols?Object.getOwnPropertySymbols(t).filter(function(e){return Object.propertyIsEnumerable.call(t,e)}):[])}function o(t,e){try{return e in t}catch(t){return!1}}function a(t,r,c){(c=c||{}).arrayMerge=c.arrayMerge||s,c.isMergeableObject=c.isMergeableObject||e,c.cloneUnlessOtherwiseSpecified=i;var l,u,h=Array.isArray(r);return h!==Array.isArray(t)?i(r,c):h?c.arrayMerge(t,r,c):(u={},(l=c).isMergeableObject(t)&&n(t).forEach(function(e){u[e]=i(t[e],l)}),n(r).forEach(function(e){(!o(t,e)||Object.hasOwnProperty.call(t,e)&&Object.propertyIsEnumerable.call(t,e))&&(o(t,e)&&l.isMergeableObject(r[e])?u[e]=(function(t,e){if(!e.customMerge)return a;var r=e.customMerge(t);return"function"==typeof r?r:a})(e,l)(t[e],r[e],l):u[e]=i(r[e],l))}),u)}a.all=function(t,e){if(!Array.isArray(t))throw Error("first argument should be an array");return t.reduce(function(t,r){return a(t,r,e)},{})},t.exports=a},294:(t,e,r)=>{r.r(e),r.d(e,{default:()=>E});var i=r(857),s=r.n(i);class n{static ucFirst(t){return t.charAt(0).toUpperCase()+t.slice(1)}static lcFirst(t){return t.charAt(0).toLowerCase()+t.slice(1)}static toDashCase(t){return t.replace(/([A-Z])/g,"-$1").replace(/^-/,"").toLowerCase()}static toLowerCamelCase(t,e){let r=n.toUpperCamelCase(t,e);return n.lcFirst(r)}static toUpperCamelCase(t,e){return e?t.split(e).map(t=>n.ucFirst(t.toLowerCase())).join(""):n.ucFirst(t.toLowerCase())}static parsePrimitive(t){try{return/^\d+(.|,)\d+$/.test(t)&&(t=t.replace(",",".")),JSON.parse(t)}catch(e){return t.toString()}}}class o{static isNode(t){return"object"==typeof t&&null!==t&&(t===document||t===window||t instanceof Node)}static hasAttribute(t,e){if(!o.isNode(t))throw Error("The element must be a valid HTML Node!");return"function"==typeof t.hasAttribute&&t.hasAttribute(e)}static getAttribute(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!1===o.hasAttribute(t,e))throw Error('The required property "'.concat(e,'" does not exist!'));if("function"!=typeof t.getAttribute){if(r)throw Error("This node doesn't support the getAttribute function!");return}return t.getAttribute(e)}static getDataAttribute(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2],i=e.replace(/^data(|-)/,""),s=n.toLowerCamelCase(i,"-");if(!o.isNode(t)){if(r)throw Error("The passed node is not a valid HTML Node!");return}if(void 0===t.dataset){if(r)throw Error("This node doesn't support the dataset attribute!");return}let a=t.dataset[s];if(void 0===a){if(r)throw Error('The required data attribute "'.concat(e,'" does not exist on ').concat(t,"!"));return a}return n.parsePrimitive(a)}static querySelector(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!o.isNode(t))throw Error("The parent node is not a valid HTML Node!");let i=t.querySelector(e)||!1;if(r&&!1===i)throw Error('The required element "'.concat(e,'" does not exist in parent node!'));return i}static querySelectorAll(t,e){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!o.isNode(t))throw Error("The parent node is not a valid HTML Node!");let i=t.querySelectorAll(e);if(0===i.length&&(i=!1),r&&!1===i)throw Error('At least one item of "'.concat(e,'" must exist in parent node!'));return i}}class a{publish(t){let e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},r=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=new CustomEvent(t,{detail:e,cancelable:r});return this.el.dispatchEvent(i),i}subscribe(t,e){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},i=this,s=t.split("."),n=r.scope?e.bind(r.scope):e;if(r.once&&!0===r.once){let e=n;n=function(r){i.unsubscribe(t),e(r)}}return this.el.addEventListener(s[0],n),this.listeners.push({splitEventName:s,opts:r,cb:n}),!0}unsubscribe(t){let e=t.split(".");return this.listeners=this.listeners.reduce((t,r)=>([...r.splitEventName].sort().toString()===e.sort().toString()?this.el.removeEventListener(r.splitEventName[0],r.cb):t.push(r),t),[]),!0}reset(){return this.listeners.forEach(t=>{this.el.removeEventListener(t.splitEventName[0],t.cb)}),this.listeners=[],!0}get el(){return this._el}set el(t){this._el=t}get listeners(){return this._listeners}set listeners(t){this._listeners=t}constructor(t=document){this._el=t,t.$emitter=this,this._listeners=[]}}class c{init(){throw Error('The "init" method for the plugin "'.concat(this._pluginName,'" is not defined.'))}update(){}_init(){this._initialized||(this.init(),this._initialized=!0)}_update(){this._initialized&&this.update()}_mergeOptions(t){let e=n.toDashCase(this._pluginName),r=o.getDataAttribute(this.el,"data-".concat(e,"-config"),!1),i=o.getAttribute(this.el,"data-".concat(e,"-options"),!1),a=[this.constructor.options,this.options,t];r&&a.push(window.PluginConfigManager.get(this._pluginName,r));try{i&&a.push(JSON.parse(i))}catch(t){throw console.error(this.el),Error('The data attribute "data-'.concat(e,'-options" could not be parsed to json: ').concat(t.message))}return s().all(a.filter(t=>t instanceof Object&&!(t instanceof Array)).map(t=>t||{}))}_registerInstance(){window.PluginManager.getPluginInstancesFromElement(this.el).set(this._pluginName,this),window.PluginManager.getPlugin(this._pluginName,!1).get("instances").push(this)}_getPluginName(t){return t||(t=this.constructor.name),t}constructor(t,e={},r=!1){if(!o.isNode(t))throw Error("There is no valid element given.");this.el=t,this.$emitter=new a(this.el),this._pluginName=this._getPluginName(r),this.options=this._mergeOptions(e),this._initialized=!1,this._registerInstance(),this._init()}}class l{static debounce(t,e){let r,i=arguments.length>2&&void 0!==arguments[2]&&arguments[2];return function(){for(var s=arguments.length,n=Array(s),o=0;o<s;o++)n[o]=arguments[o];i&&!r&&setTimeout(t.bind(t,...n),0),clearTimeout(r),r=setTimeout(t.bind(t,...n),e)}}}class u{get(t,e){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"application/json",i=this._createPreparedRequest("GET",t,r);return this._sendRequest(i,null,e)}post(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("POST",t,i);return this._sendRequest(s,e,r)}delete(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("DELETE",t,i);return this._sendRequest(s,e,r)}patch(t,e,r){let i=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/json";i=this._getContentType(e,i);let s=this._createPreparedRequest("PATCH",t,i);return this._sendRequest(s,e,r)}abort(){if(this._request)return this._request.abort()}_registerOnLoaded(t,e){e&&t.addEventListener("loadend",()=>{e(t.responseText,t)})}_sendRequest(t,e,r){return this._registerOnLoaded(t,r),t.send(e),t}_getContentType(t,e){return t instanceof FormData&&(e=!1),e}_createPreparedRequest(t,e,r){return this._request=new XMLHttpRequest,this._request.open(t,e),this._request.setRequestHeader("X-Requested-With","XMLHttpRequest"),r&&this._request.setRequestHeader("Content-type",r),this._request}constructor(){this._request=null}}class h{static iterate(t,e){if(t instanceof Map||Array.isArray(t))return t.forEach(e);if(t instanceof FormData){for(var r of t.entries())e(r[1],r[0]);return}if(t instanceof NodeList)return t.forEach(e);if(t instanceof HTMLCollection)return Array.from(t).forEach(e);if(t instanceof Object)return Object.keys(t).forEach(r=>{e(t[r],r)});throw Error("The element type ".concat(typeof t," is not iterable!"))}}let d="loader",p={BEFORE:"before",INNER:"inner"};class g{create(){if(!this.exists()){if(this.position===p.INNER){this.parent.innerHTML=g.getTemplate();return}this.parent.insertAdjacentHTML(this._getPosition(),g.getTemplate())}}remove(){let t=this.parent.querySelectorAll(".".concat(d));h.iterate(t,t=>t.remove())}exists(){return this.parent.querySelectorAll(".".concat(d)).length>0}_getPosition(){return this.position===p.BEFORE?"afterbegin":"beforeend"}static getTemplate(){return'<div class="'.concat(d,'" role="status">\n                    <span class="').concat("visually-hidden",'">Loading...</span>\n                </div>')}static SELECTOR_CLASS(){return d}constructor(t,e=p.BEFORE){this.parent=t instanceof Element?t:document.body.querySelector(t),this.position=e}}class _ extends g{create(){super.create(),this._isButtonElement()?this.parent.disabled=!0:this._isAnchorElement()&&this.parent.classList.add("disabled")}remove(){super.remove(),this._isButtonElement()?this.parent.disabled=!1:this._isAnchorElement()&&this.parent.classList.remove("disabled")}_isButtonElement(){return"button"===this.parent.tagName.toLowerCase()}_isAnchorElement(){return"a"===this.parent.tagName.toLowerCase()}constructor(t,e="before"){if(super(t,e),!this._isButtonElement()&&!this._isAnchorElement())throw Error("Parent element is not of type <button> or <a>")}}class m{static isTouchDevice(){return"ontouchstart"in document.documentElement}static isIOSDevice(){return m.isIPhoneDevice()||m.isIPadDevice()}static isNativeWindowsBrowser(){return m.isIEBrowser()||m.isEdgeBrowser()}static isIPhoneDevice(){return!!navigator.userAgent.match(/iPhone/i)}static isIPadDevice(){return!!navigator.userAgent.match(/iPad/i)}static isIEBrowser(){return -1!==navigator.userAgent.toLowerCase().indexOf("msie")||!!navigator.userAgent.match(/Trident.*rv:\d+\./)}static isEdgeBrowser(){return!!navigator.userAgent.match(/Edge\/\d+/i)}static getList(){return{"is-touch":m.isTouchDevice(),"is-ios":m.isIOSDevice(),"is-native-windows":m.isNativeWindowsBrowser(),"is-iphone":m.isIPhoneDevice(),"is-ipad":m.isIPadDevice(),"is-ie":m.isIEBrowser(),"is-edge":m.isEdgeBrowser()}}}let f="is-active";class b{resetIterator(){this._iterator=-1}_registerEvents(){this._element.addEventListener("keydown",this._onKeyDown.bind(this))}_onKeyDown(t){let e=o.querySelector(document,this._parentSelector,!1);if(e&&(this._items=e.querySelectorAll(this._itemSelector),0!==this._items.length)){switch(t.key){case"Enter":this._onPressEnter(t);return;case"ArrowDown":t.preventDefault(),this._iterator++;break;case"ArrowUp":t.preventDefault(),this._iterator--;break;default:return}this._clampIterator(),h.iterate(this._items,t=>t.classList.remove(f)),this._getCurrentSelection().classList.add(f)}}_onPressEnter(t){if(!(this._iterator<=-1))try{let e=o.querySelector(this._getCurrentSelection(),"a");t.preventDefault(),e.click()}catch(t){}}_getCurrentSelection(){return this._items[this._iterator]}_clampIterator(){let t=this._getMaxItemCount();this._iterator<0&&(this._iterator=this._infinite?t:0),this._iterator>t&&(this._iterator=this._infinite?0:t)}_getMaxItemCount(){return this._items.length-1}constructor(t,e,r,i=!0){this._element=t,this._parentSelector=e,this._infinite=i,this._itemSelector=r,this.resetIterator(),this._registerEvents()}}class v extends c{init(){try{this._inputField=o.querySelector(this.el,this.options.searchWidgetInputFieldSelector),this._submitButton=o.querySelector(this.el,this.options.searchWidgetButtonFieldSelector),this._url=o.getAttribute(this.el,this.options.searchWidgetUrlDataAttribute)}catch(t){return}this._client=new u,this._navigationHelper=new b(this._inputField,this.options.searchWidgetResultSelector,this.options.searchWidgetResultItemSelector,!0),this._registerEvents()}_registerEvents(){this._inputField.addEventListener("input",l.debounce(this._handleInputEvent.bind(this),this.options.searchWidgetDelay),{capture:!0,passive:!0}),this.el.addEventListener("submit",this._handleSearchEvent.bind(this));let t=m.isTouchDevice()?"touchstart":"click";document.body.addEventListener(t,this._onBodyClick.bind(this)),this._registerInputFocus()}_handleSearchEvent(t){this._inputField.value.trim().length<this.options.searchWidgetMinChars&&(t.preventDefault(),t.stopPropagation())}_handleInputEvent(){let t=this._inputField.value.trim();if(t.length<this.options.searchWidgetMinChars){this._clearSuggestResults();return}this._suggest(t),this.$emitter.publish("handleInputEvent",{value:t})}_suggest(t){let e=this._url+encodeURIComponent(t);this._client.abort();let r=new _(this._submitButton);r.create(),this.$emitter.publish("beforeSearch"),this._client.get(e,t=>{this._clearSuggestResults(),r.remove(),this.el.insertAdjacentHTML("beforeend",t),this.$emitter.publish("afterSuggest")})}_clearSuggestResults(){this._navigationHelper.resetIterator();let t=document.querySelectorAll(this.options.searchWidgetResultSelector);h.iterate(t,t=>t.remove()),this.$emitter.publish("clearSuggestResults")}_onBodyClick(t){t.target.closest(this.options.searchWidgetSelector)||t.target.closest(this.options.searchWidgetResultSelector)||(this._clearSuggestResults(),this.$emitter.publish("onBodyClick"))}_registerInputFocus(){if(this._toggleButton=o.querySelector(document,this.options.searchWidgetCollapseButtonSelector,!1),!this._toggleButton){console.warn("Called selector '".concat(this.options.searchWidgetCollapseButtonSelector,"' for the search toggle button not found. Autofocus has been disabled on mobile."));return}let t=m.isTouchDevice()?"touchstart":"click";this._toggleButton.addEventListener(t,()=>{setTimeout(()=>this._focusInput(),0)})}_focusInput(){this._toggleButton&&!this._toggleButton.classList.contains(this.options.searchWidgetCollapseClass)&&(this._toggleButton.blur(),this._inputField.setAttribute("tabindex","-1"),this._inputField.focus()),this.$emitter.publish("focusInput")}}v.options={searchWidgetSelector:".js-search-form",searchWidgetResultSelector:".js-search-result",searchWidgetResultItemSelector:".js-result",searchWidgetInputFieldSelector:"input[type=search]",searchWidgetButtonFieldSelector:"button[type=submit]",searchWidgetUrlDataAttribute:"data-url",searchWidgetCollapseButtonSelector:".js-search-toggle-btn",searchWidgetCollapseClass:"collapsed",searchWidgetDelay:250,searchWidgetMinChars:3};class E extends v{init(){super.init(),this.$emitter.subscribe("afterSuggest",t=>{let e=null,r=!1,i=document.getElementsByClassName("matomo-search-suggest-helper")[0];if(i){let t=JSON.parse(i.dataset.value);e=t.term,r=t.count}window._paq=window._paq||[],window._paq.push(["trackSiteSearch",e,"suggestSearch",r])})}}}}]);