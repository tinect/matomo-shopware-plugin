"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["MatomoAnalyticsPlugin"],{639:(e,t,o)=>{o.r(t),o.d(t,{default:()=>i});class i extends window.PluginBaseClass{init(){this.cookieEnabledName="matomo-analytics-enabled",window.matomoCookieActive=!!this.getCookieItem(this.cookieEnabledName),document.$emitter.subscribe("CookieConfiguration_Update",this.handleCookies.bind(this)),window.mTrackCall()}handleCookies(e){let t=e.detail;Object.prototype.hasOwnProperty.call(t,this.cookieEnabledName)&&!t[this.cookieEnabledName]&&this.removeCookies()}removeCookies(){let e=document.cookie.split(";"),t=/^(_pk)/;e.forEach(e=>{let o=e.split("=")[0].trim();o.match(t)&&(document.cookie="".concat(o,"= ; expires = Thu, 01 Jan 1970 00:00:00 GMT;path=/"))})}getCookieItem(e){if(!e)return!1;let t=e+"=",o=document.cookie.split(";");for(let e=0;e<o.length;e++){let i=o[e];for(;" "===i.charAt(0);)i=i.substring(1);if(0===i.indexOf(t))return i.substring(t.length,i.length)}return!1}}}}]);