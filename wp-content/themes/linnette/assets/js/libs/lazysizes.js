!function(e,t){var a=t(e,e.document);e.lazySizes=a,"object"==typeof module&&module.exports?module.exports=a:"function"==typeof define&&define.amd&&define(a)}(window,function(e,t){"use strict";if(t.getElementsByClassName){var a,i=t.documentElement,n=e.addEventListener,r=/^picture$/i,s=["load","error","lazyincluded","_lazyloaded"],o=function(e,t){var a=new RegExp("(\\s|^)"+t+"(\\s|$)");return e.className.match(a)&&a},l=function(e,t){o(e,t)||(e.className+=" "+t)},d=function(e,t){var a;(a=o(e,t))&&(e.className=e.className.replace(a," "))},c=function(e,t,a){var i=a?"addEventListener":"removeEventListener";a&&c(e,t),s.forEach(function(a){e[i](a,t)})},u=function(e,a,i,n,r){var s=t.createEvent("Event");return s.initEvent(a,!n,!r),s.details=i||{},e.dispatchEvent(s),s},f=function(t,i){var n;e.HTMLPictureElement||((n=e.picturefill||e.respimage||a.pf)?n({reevaluate:!0,reparse:!0,elements:[t]}):i&&i.src&&(t.src=i.src))},m=function(e,t){return getComputedStyle(e,null)[t]},g=function(e,t){for(var i=e.offsetWidth;i<a.minSize&&t&&!e._lazysizesWidth;)i=t.offsetWidth,t=t.parentNode;return i},v=function(e){var a,i,n=function(){a&&(a=!1,e())},r=function(){clearInterval(i),t.hidden||(n(),i=setInterval(n,51))};return t.addEventListener("visibilitychange",r),r(),function(e){a=!0,e===!0&&n()}},z=function(){var s,g,z,p,b,h,A,C,E,N,M,L,_,w=/^img$/i,S=/^iframe$/i,x="onscroll"in e&&!/glebot/.test(navigator.userAgent),T=0,B=0,W=0,R=0,D=0,O=function(e){R--,e&&e.target&&c(e.target,O),(!e||R<0||!e.target)&&(R=0)},P=function(e,t){var a,i=e,n="hidden"!=m(e,"visibility");for(C-=t,M+=t,E-=t,N+=t;n&&(i=i.offsetParent);)n=z&&R<2||(m(i,"opacity")||1)>0,n&&"visible"!=m(i,"overflow")&&(a=i.getBoundingClientRect(),n=N>a.left&&E<a.right&&M>a.top-1&&C<a.bottom+1);return n},$=function(){var e,t,i,n,r,o,l,d,c,u=s.length;if(u&&(b=a.loadMode)){for(t=Date.now(),e=D,W++,B<_&&R<1&&W>5&&b>2?(B=_,W=0):B=B!=L&&b>1&&W>4?L:T;e<u;e++,D++)if(s[e]&&!s[e]._lazyRace)if(x){if((d=s[e].getAttribute("data-expand"))&&(o=1*d)||(o=B),!(R>6&&(!d||"src"in s[e])))if(o>T&&(b<2||R>3)&&(o=T),c!==o&&(h=innerWidth+o,A=innerHeight+o,l=o*-1,c=o),i=s[e].getBoundingClientRect(),(M=i.bottom)>=l&&(C=i.top)<=A&&(N=i.right)>=l&&(E=i.left)<=h&&(M||N||E||C)&&(z&&B<_&&R<3&&W<4&&!d&&b>2||P(s[e],o)))D--,t+=2,H(s[e]),r=!0;else{if(Date.now()-t>3)return D++,void I();!r&&z&&!n&&R<3&&W<4&&b>2&&(g[0]||a.preloadAfterLoad)&&(g[0]||!d&&(M||N||E||C||"auto"!=s[e].getAttribute(a.sizesAttr)))&&(n=g[0]||s[e])}}else H(s[e]);D=0,n&&!r&&H(n)}},I=v($),k=function(e){l(e.target,a.loadedClass),d(e.target,a.loadingClass),c(e.target,k)},F=function(e,t){try{e.contentWindow.location.replace(t)}catch(a){e.setAttribute("src",t)}},H=function(e,t){var i,n,s,m,g,v,b,h,A,C,E,N=e.currentSrc||e.src,M=w.test(e.nodeName),L=e.getAttribute(a.sizesAttr)||e.getAttribute("sizes"),_="auto"==L;if(!_&&z||!M||!N||e.complete||o(e,a.errorClass)){if(e._lazyRace=!0,!(A=u(e,"lazybeforeunveil",{force:!!t})).defaultPrevented){if(L&&(_?y.updateElem(e,!0):e.setAttribute("sizes",L)),v=e.getAttribute(a.srcsetAttr),g=e.getAttribute(a.srcAttr),M&&(b=e.parentNode,h=r.test(b.nodeName||"")),C=A.details.firesLoad||"src"in e&&(v||g||h),C&&(R++,c(e,O,!0),clearTimeout(p),p=setTimeout(O,3e3)),h)for(i=b.getElementsByTagName("source"),n=0,s=i.length;n<s;n++)(E=a.customMedia[i[n].getAttribute("data-media")||i[n].getAttribute("media")])&&i[n].setAttribute("media",E),m=i[n].getAttribute(a.srcsetAttr),m&&i[n].setAttribute("srcset",m);v?e.setAttribute("srcset",v):g&&(S.test(e.nodeName)?F(e,g):e.setAttribute("src",g)),l(e,a.loadingClass),c(e,k,!0)}setTimeout(function(){e._lazyRace&&delete e._lazyRace,"auto"==L&&l(e,a.autosizesClass),(v||h)&&f(e,{src:g}),d(e,a.lazyClass),(!C||e.complete&&N==(e.currentSrc||e.src))&&(C&&O(A),k(A)),e=null})}},j=function(){var e,t=function(){a.loadMode=3,I()};z=!0,W+=8,a.loadMode=3,I(!0),n("scroll",function(){3==a.loadMode&&(a.loadMode=2),clearTimeout(e),e=setTimeout(t,66)},!0)};return{_:function(){s=t.getElementsByClassName(a.lazyClass),g=t.getElementsByClassName(a.lazyClass+" "+a.preloadClass),L=a.expand,_=L*a.expFactor,n("scroll",I,!0),n("resize",I,!0),e.MutationObserver?new MutationObserver(I).observe(i,{childList:!0,subtree:!0,attributes:!0}):(i.addEventListener("DOMNodeInserted",I,!0),i.addEventListener("DOMAttrModified",I,!0),setInterval(I,3e3)),n("hashchange",I,!0),["transitionstart","transitionend","load","focus","mouseover","animationend","click"].forEach(function(e){t.addEventListener(e,I,!0)}),(z=/d$|^c/.test(t.readyState))?j():(n("load",j),t.addEventListener("DOMContentLoaded",I)),I(s.length>0)},checkElems:I,unveil:H}}(),y=function(){var e,i=function(e,t){var a,i,n,s,o,l=e.parentNode;if(l&&(a=g(e,l),o=u(e,"lazybeforesizes",{width:a,dataAttr:!!t}),!o.defaultPrevented&&(a=o.details.width,a&&a!==e._lazysizesWidth))){if(e._lazysizesWidth=a,a+="px",e.setAttribute("sizes",a),r.test(l.nodeName||""))for(i=l.getElementsByTagName("source"),n=0,s=i.length;n<s;n++)i[n].setAttribute("sizes",a);o.details.dataAttr||f(e,o.details)}},s=function(){var t,a=e.length;if(a)for(t=0;t<a;t++)i(e[t])},o=v(s);return{_:function(){e=t.getElementsByClassName(a.autosizesClass),n("resize",o)},checkElems:o,updateElem:i}}(),p=function(){p.i||(p.i=!0,y._(),z._())};return function(){var t,i={lazyClass:"lazyload",loadedClass:"lazyloaded",loadingClass:"lazyloading",preloadClass:"lazypreload",errorClass:"lazyerror",autosizesClass:"lazyautosizes",srcAttr:"data-src",srcsetAttr:"data-srcset",sizesAttr:"data-sizes",minSize:50,customMedia:{},init:!0,expFactor:2,expand:300,loadMode:2};a=e.lazySizesConfig||{};for(t in i)t in a||(a[t]=i[t]);e.lazySizesConfig=a,setTimeout(function(){a.init&&p()})}(),{cfg:a,autoSizer:y,loader:z,init:p,uP:f,aC:l,rC:d,hC:o,fire:u,gW:g}}});