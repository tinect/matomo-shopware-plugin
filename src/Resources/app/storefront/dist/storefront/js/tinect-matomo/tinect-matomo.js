(()=>{var e={},r={};function t(o){var n=r[o];if(void 0!==n)return n.exports;var i=r[o]={exports:{}};return e[o](i,i.exports,t),i.exports}t.m=e,(()=>{t.d=(e,r)=>{for(var o in r)t.o(r,o)&&!t.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:r[o]})}})(),(()=>{t.f={},t.e=e=>Promise.all(Object.keys(t.f).reduce((r,o)=>(t.f[o](e,r),r),[]))})(),(()=>{t.u=e=>"./js/tinect-matomo/"+e+".js"})(),(()=>{t.miniCssF=e=>{}})(),(()=>{t.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||Function("return this")()}catch(e){if("object"==typeof window)return window}}()})(),(()=>{t.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r)})(),(()=>{var e={};t.l=(r,o,n,i)=>{if(e[r]){e[r].push(o);return}if(void 0!==n)for(var a,l,s=document.getElementsByTagName("script"),c=0;c<s.length;c++){var u=s[c];if(u.getAttribute("src")==r){a=u;break}}a||(l=!0,(a=document.createElement("script")).charset="utf-8",a.timeout=120,t.nc&&a.setAttribute("nonce",t.nc),a.src=r),e[r]=[o];var p=(t,o)=>{a.onerror=a.onload=null,clearTimeout(d);var n=e[r];if(delete e[r],a.parentNode&&a.parentNode.removeChild(a),n&&n.forEach(e=>e(o)),t)return t(o)},d=setTimeout(p.bind(null,void 0,{type:"timeout",target:a}),12e4);a.onerror=p.bind(null,a.onerror),a.onload=p.bind(null,a.onload),l&&document.head.appendChild(a)}})(),(()=>{t.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}})(),(()=>{t.g.importScripts&&(e=t.g.location+"");var e,r=t.g.document;if(!e&&r&&(r.currentScript&&(e=r.currentScript.src),!e)){var o=r.getElementsByTagName("script");if(o.length)for(var n=o.length-1;n>-1&&!e;)e=o[n--].src}if(!e)throw Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),t.p=e+"../../"})(),(()=>{var e={"tinect-matomo":0};t.f.j=(r,o)=>{var n=t.o(e,r)?e[r]:void 0;if(0!==n){if(n)o.push(n[2]);else{var i=new Promise((t,o)=>n=e[r]=[t,o]);o.push(n[2]=i);var a=t.p+t.u(r),l=Error();t.l(a,o=>{if(t.o(e,r)&&(0!==(n=e[r])&&(e[r]=void 0),n)){var i=o&&("load"===o.type?"missing":o.type),a=o&&o.target&&o.target.src;l.message="Loading chunk "+r+" failed.\n("+i+": "+a+")",l.name="ChunkLoadError",l.type=i,l.request=a,n[1](l)}},"chunk-"+r,r)}}};var r=(r,o)=>{var n,i,[a,l,s]=o,c=0;if(a.some(r=>0!==e[r])){for(n in l)t.o(l,n)&&(t.m[n]=l[n]);s&&s(t)}for(r&&r(o);c<a.length;c++)i=a[c],t.o(e,i)&&e[i]&&e[i][0](),e[i]=0},o=self.webpackChunk=self.webpackChunk||[];o.forEach(r.bind(null,0)),o.push=r.bind(null,o.push.bind(o))})(),window.PluginManager.register("MatomoAnalytics",()=>t.e("extension2285665482_TinectMatomo_src_Resources_app_storefront_src_plugin_MatomoAnalyticsPlugin_js").then(t.bind(t,27)))})();