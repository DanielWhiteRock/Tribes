(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";var _core=require("../utils/core");var _plugins=require("../utils/plugins");(0,_core.querySelector)('#login form p, #login form div',_plugins.createFormInput);

},{"../utils/core":2,"../utils/plugins":3}],2:[function(require,module,exports){
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.filterString=exports.limitLineBreaks=exports.dateMinutesDiff=exports.stripHTML=exports.hexaToRGB=exports.ucfirst=exports.replaceEnterWithBr=exports.insertWordInText=exports.updateShadowInput=exports.getPositionInInput=exports.getWordInPosition=exports.wrapLinks=exports.extractAttachedMedia=exports.getMediaLink=exports.joinText=exports.truncateText=exports.querySelector=exports.deepMerge=exports.deepExtend=void 0;function _toConsumableArray(arr){return _arrayWithoutHoles(arr)||_iterableToArray(arr)||_unsupportedIterableToArray(arr)||_nonIterableSpread();}function _nonIterableSpread(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");}function _iterableToArray(iter){if(typeof Symbol!=="undefined"&&Symbol.iterator in Object(iter))return Array.from(iter);}function _arrayWithoutHoles(arr){if(Array.isArray(arr))return _arrayLikeToArray(arr);}function _createForOfIteratorHelper(o,allowArrayLike){var it;if(typeof Symbol==="undefined"||o[Symbol.iterator]==null){if(Array.isArray(o)||(it=_unsupportedIterableToArray(o))||allowArrayLike&&o&&typeof o.length==="number"){if(it)o=it;var i=0;var F=function F(){};return{s:F,n:function n(){if(i>=o.length)return{done:true};return{done:false,value:o[i++]};},e:function e(_e){throw _e;},f:F};}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");}var normalCompletion=true,didErr=false,err;return{s:function s(){it=o[Symbol.iterator]();},n:function n(){var step=it.next();normalCompletion=step.done;return step;},e:function e(_e2){didErr=true;err=_e2;},f:function f(){try{if(!normalCompletion&&it.return!=null)it.return();}finally{if(didErr)throw err;}}};}function _unsupportedIterableToArray(o,minLen){if(!o)return;if(typeof o==="string")return _arrayLikeToArray(o,minLen);var n=Object.prototype.toString.call(o).slice(8,-1);if(n==="Object"&&o.constructor)n=o.constructor.name;if(n==="Map"||n==="Set")return Array.from(o);if(n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return _arrayLikeToArray(o,minLen);}function _arrayLikeToArray(arr,len){if(len==null||len>arr.length)len=arr.length;for(var i=0,arr2=new Array(len);i<len;i++){arr2[i]=arr[i];}return arr2;}function _typeof(obj){"@babel/helpers - typeof";if(typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"){_typeof=function _typeof(obj){return typeof obj;};}else{_typeof=function _typeof(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};}return _typeof(obj);}var deepExtend=function deepExtend(a,b){for(var prop in b){if(_typeof(b[prop])==='object'){a[prop]=b[prop]instanceof Array?[]:{};deepExtend(a[prop],b[prop]);}else{a[prop]=b[prop];}}};exports.deepExtend=deepExtend;var deepMerge=function deepMerge(){var c={};for(var _len=arguments.length,objects=new Array(_len),_key=0;_key<_len;_key++){objects[_key]=arguments[_key];}objects.forEach(function(obj){deepExtend(c,obj);});return c;};exports.deepMerge=deepMerge;var query=function query(options){var config={method:'GET',async:true,header:{type:'Content-type',value:'application/json'},data:''};deepExtend(config,options);return new Promise(function(resolve,reject){var xhttp=new XMLHttpRequest();xhttp.onreadystatechange=function(){if(xhttp.readyState!==4)return;if(xhttp.status===200){resolve(xhttp.responseText);}else{reject({status:xhttp.status,statusText:xhttp.statusText});}};xhttp.open(config.method,config.url,config.async);xhttp.setRequestHeader(config.header.type,config.header.value);if(config.method==='GET'){xhttp.send();}else if(config.method==='POST'){xhttp.send(config.data);}});};var querySelector=function querySelector(selector,callback){var el=document.querySelectorAll(selector);if(el.length){callback(el);}};exports.querySelector=querySelector;var dateDiff=function dateDiff(date1){var date2=arguments.length>1&&arguments[1]!==undefined?arguments[1]:new Date();var timeDiff=Math.abs(date1.getTime()-date2.getTime()),secondsDiff=Math.ceil(timeDiff/1000),minutesDiff=Math.floor(timeDiff/(1000*60)),hoursDiff=Math.floor(timeDiff/(1000*60*60)),daysDiff=Math.floor(timeDiff/(1000*60*60*24)),weeksDiff=Math.floor(timeDiff/(1000*60*60*24*7)),monthsDiff=Math.floor(timeDiff/(1000*60*60*24*7*4)),yearsDiff=Math.floor(timeDiff/(1000*60*60*24*7*4*12));var unit;if(secondsDiff<60){unit=secondsDiff===1?'second':'seconds';return{unit:unit,value:secondsDiff};}else if(minutesDiff<60){unit=minutesDiff===1?'minute':'minutes';return{unit:unit,value:minutesDiff};}else if(hoursDiff<24){unit=hoursDiff===1?'hour':'hours';return{unit:unit,value:hoursDiff};}else if(daysDiff<7){unit=daysDiff===1?'day':'days';return{unit:unit,value:daysDiff};}else if(weeksDiff<4){unit=weeksDiff===1?'week':'weeks';return{unit:unit,value:weeksDiff};}else if(monthsDiff<12){unit=monthsDiff===1?'month':'months';return{unit:unit,value:monthsDiff};}else{unit=yearsDiff===1?'year':'years';return{unit:unit,value:yearsDiff};}};var dateMinutesDiff=function dateMinutesDiff(date1){var date2=arguments.length>1&&arguments[1]!==undefined?arguments[1]:new Date();var timeDiff=Math.abs(date1.getTime()-date2.getTime()),minutesDiff=Math.floor(timeDiff/(1000*60));return minutesDiff;};exports.dateMinutesDiff=dateMinutesDiff;var truncateText=function truncateText(text,limit){var moreText=arguments.length>2&&arguments[2]!==undefined?arguments[2]:'...';if(text.length<=limit){return text;}var truncatedText=text.substring(0,limit);return truncatedText.trim()+moreText;};exports.truncateText=truncateText;var joinText=function joinText(text){var separator=arguments.length>1&&arguments[1]!==undefined?arguments[1]:'-';var joinedText=text.replace(/\s+/igm,separator);return joinedText;};exports.joinText=joinText;var getMediaLink=function getMediaLink(type,id){if(type==='youtube'){return"https://www.youtube.com/embed/".concat(id);}if(type==='twitch'){return"//player.twitch.tv/?autoplay=false&video=v".concat(id,"&parent=").concat(vikinger_constants.settings.stream_twitch_embeds_parent);}if(type==='twitch_channel'){return"//player.twitch.tv/?channel=".concat(id,"&parent=").concat(vikinger_constants.settings.stream_twitch_embeds_parent);}};exports.getMediaLink=getMediaLink;var extractAttachedMedia=function extractAttachedMedia(string){var attached_media=false,minMatchIndex=Number.MAX_SAFE_INTEGER;// test for youtube link match
var youtubeLinkRegex=/youtube\.com\/watch\?v=(.*?)(&|\s|$)/igm,youtubeLinkShortRegex=/youtu\.be\/(.*?)(&|\s|$)/igm,youtubeLinkMatches=youtubeLinkRegex.exec(string)||youtubeLinkShortRegex.exec(string);if(youtubeLinkMatches&&youtubeLinkMatches.index<minMatchIndex){var type='youtube',id=youtubeLinkMatches[1];attached_media={type:type,id:id,link:getMediaLink(type,id)};minMatchIndex=youtubeLinkMatches.index;}// test for twitch link match
var twitchVideoLinkRegex=/twitch\.tv\/videos\/(.*?)(\?|&|\s|$)/igm,twitchChannelLinkRegex=/twitch\.tv\/(.*?)(&|\s|$)/igm,twitchVideoLinkMatches=twitchVideoLinkRegex.exec(string),twitchChannelLinkMatches=twitchChannelLinkRegex.exec(string);if(twitchVideoLinkMatches&&twitchVideoLinkMatches.index<minMatchIndex){var _type='twitch',_id=twitchVideoLinkMatches[1];attached_media={type:_type,id:_id,link:getMediaLink(_type,_id)};minMatchIndex=twitchVideoLinkMatches.index;}if(twitchChannelLinkMatches&&twitchChannelLinkMatches.index<minMatchIndex){var _type2='twitch_channel',_id2=twitchChannelLinkMatches[1];attached_media={type:_type2,id:_id2,link:getMediaLink(_type2,_id2)};minMatchIndex=twitchChannelLinkMatches.index;}return attached_media;};exports.extractAttachedMedia=extractAttachedMedia;var wrapLinks=function wrapLinks(string){var linkRegex=/((^|[^"'])https?:\/\/[^\s]*)/igm;return string.replace(linkRegex,'<a href="$1" target="_blank">$1</a>');};exports.wrapLinks=wrapLinks;var getWordInPosition=function getWordInPosition(text,position){var wordStartIndex=0,wordEndIndex=text.length;var isSeparator=function isSeparator(character){return character===' '||character==='\n';};// search word ending after cursor position
for(var i=position-1;i<text.length;i++){// if word ends
if(isSeparator(text[i])){wordEndIndex=i;break;}}// search word ending before cursor position
for(var _i=position-1;_i>=0;_i--){// if word ends
if(isSeparator(text[_i])){wordStartIndex=_i+1;break;}}// console.log('GET WORD INDEX - START: ', wordStartIndex);
// console.log('GET WORD INDEX - END: ', wordEndIndex);
return{word:text.substring(wordStartIndex,wordEndIndex),startIndex:wordStartIndex,endIndex:wordEndIndex};};exports.getWordInPosition=getWordInPosition;var getPositionInInput=function getPositionInInput(input,shadowInput,index){if(shadowInput.children&&index>0){var inputDimensions=input.getBoundingClientRect(),shadowInputDimensions=shadowInput.children[index-1].getBoundingClientRect();// console.log('INPUT CLIENT RECT: ', inputDimensions);
// console.log('SHADOW INPUT CLIENT RECT: ', shadowInput.getBoundingClientRect());
// console.log('SHADOW INPUT CURRENT SPAN: ', shadowInput.children[index - 1]);
// console.log('SHADOW INPUT CURRENT SPAN CLIENT RECT: ', shadowInputDimensions);
return{width:shadowInputDimensions.width,height:shadowInputDimensions.height,top:shadowInputDimensions.top,left:shadowInputDimensions.left,relTop:shadowInputDimensions.top-inputDimensions.top,relLeft:shadowInputDimensions.left-inputDimensions.left};}return{width:0,height:0,top:0,left:0,relTop:0,relLeft:0};};exports.getPositionInInput=getPositionInInput;var updateShadowInput=function updateShadowInput(shadowInput,text){shadowInput.innerHTML='';var textCharacters=text.split('');// console.log('TEXT CHARACTERS: ', textCharacters);
var _iterator=_createForOfIteratorHelper(textCharacters),_step;try{for(_iterator.s();!(_step=_iterator.n()).done;){var character=_step.value;var span=document.createElement('span');if(character===' '){span.innerHTML='&nbsp;';}else if(character==='\n'){span.innerHTML='<br>';}else{span.innerHTML=character;}shadowInput.appendChild(span);}// console.log('UPDATE SHADOW INPUT: ', shadowInput);
}catch(err){_iterator.e(err);}finally{_iterator.f();}};exports.updateShadowInput=updateShadowInput;var insertWordInText=function insertWordInText(text,word,index){var offset=arguments.length>3&&arguments[3]!==undefined?arguments[3]:0;var textBeforeWord=text.substring(0,index),textAfterWord=text.substring(index+offset);// console.log('TEXT BEFORE WORD: ', textBeforeWord);
// console.log('TEXT AFTER WORD: ', textAfterWord);
return textBeforeWord+word+textAfterWord;};exports.insertWordInText=insertWordInText;var replaceEnterWithBr=function replaceEnterWithBr(text){var enterRegex=/\r\n/gim,newText=text.replace(enterRegex,'<br>');return newText;};exports.replaceEnterWithBr=replaceEnterWithBr;var ucfirst=function ucfirst(string){return string[0].toUpperCase()+string.substring(1);};exports.ucfirst=ucfirst;var hexaToRGB=function hexaToRGB(hexa){var opacity=arguments.length>1&&arguments[1]!==undefined?arguments[1]:1;var hex=hexa.substring(1);return"rgba(".concat(Number.parseInt(hex.substring(0,2),16),", ").concat(Number.parseInt(hex.substring(2,4),16),", ").concat(Number.parseInt(hex.substring(4,6),16),", ").concat(opacity,")");};exports.hexaToRGB=hexaToRGB;var stripHTML=function stripHTML(string){var DP=new DOMParser();var parsedString=DP.parseFromString(string,'text/html');return parsedString.body.textContent;};exports.stripHTML=stripHTML;var limitLineBreaks=function limitLineBreaks(string){var maxLineBreakCount=arguments.length>1&&arguments[1]!==undefined?arguments[1]:2;var limitLineBreakRegex=new RegExp("(s*\ns*){".concat(maxLineBreakCount+1,",}"),'igm');return string.replace(limitLineBreakRegex,'\n'.repeat(maxLineBreakCount));};exports.limitLineBreaks=limitLineBreaks;var filterString=function filterString(string,filters){return filters.reduce(function(filteredString,filter){if(typeof filter==='function'){return filter(filteredString);}else if(_typeof(filter)==='object'){if(typeof filter.filterFN==='function'){if(filter.filterArgs instanceof Array&&filter.filterArgs.length>0){return filter.filterFN.apply(filter,[filteredString].concat(_toConsumableArray(filter.filterArgs)));}return filter.filterFN(filteredString);}}return filteredString;},string);};exports.filterString=filterString;

},{}],3:[function(require,module,exports){
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.createFormInput=exports.createAccordion=exports.createPopup=exports.createSlider=exports.createTooltip=exports.createDropdown=exports.createProgressBar=exports.createHexagon=exports.createTab=void 0;function _createForOfIteratorHelper(o,allowArrayLike){var it;if(typeof Symbol==="undefined"||o[Symbol.iterator]==null){if(Array.isArray(o)||(it=_unsupportedIterableToArray(o))||allowArrayLike&&o&&typeof o.length==="number"){if(it)o=it;var i=0;var F=function F(){};return{s:F,n:function n(){if(i>=o.length)return{done:true};return{done:false,value:o[i++]};},e:function e(_e){throw _e;},f:F};}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");}var normalCompletion=true,didErr=false,err;return{s:function s(){it=o[Symbol.iterator]();},n:function n(){var step=it.next();normalCompletion=step.done;return step;},e:function e(_e2){didErr=true;err=_e2;},f:function f(){try{if(!normalCompletion&&it.return!=null)it.return();}finally{if(didErr)throw err;}}};}function _unsupportedIterableToArray(o,minLen){if(!o)return;if(typeof o==="string")return _arrayLikeToArray(o,minLen);var n=Object.prototype.toString.call(o).slice(8,-1);if(n==="Object"&&o.constructor)n=o.constructor.name;if(n==="Map"||n==="Set")return Array.from(o);if(n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return _arrayLikeToArray(o,minLen);}function _arrayLikeToArray(arr,len){if(len==null||len>arr.length)len=arr.length;for(var i=0,arr2=new Array(len);i<len;i++){arr2[i]=arr[i];}return arr2;}var existsInDOM=function existsInDOM(selector){return document.querySelectorAll(selector).length;};var createTab=function createTab(options){if(existsInDOM(options.triggers)&&existsInDOM(options.elements)){return new XM_Tab(options);}};exports.createTab=createTab;var createHexagon=function createHexagon(options){if(existsInDOM(options.container)||typeof options.containerElement!=='undefined'){return new XM_Hexagon(options);}};exports.createHexagon=createHexagon;var createProgressBar=function createProgressBar(options){if(existsInDOM(options.container)){return new XM_ProgressBar(options);}};exports.createProgressBar=createProgressBar;var createDropdown=function createDropdown(options){if((existsInDOM(options.container)||typeof options.containerElement!=='undefined')&&options.controlToggle||(existsInDOM(options.trigger)||typeof options.triggerElement!=='undefined')&&(existsInDOM(options.container)||typeof options.containerElement!=='undefined')){return new XM_Dropdown(options);}};exports.createDropdown=createDropdown;var createTooltip=function createTooltip(options){if(existsInDOM(options.container)||typeof options.containerElement!=='undefined'){return new XM_Tooltip(options);}};exports.createTooltip=createTooltip;var createSlider=function createSlider(container,options){if(container instanceof HTMLElement||existsInDOM(container)){return new Swiper(container,options);}};exports.createSlider=createSlider;var createPopup=function createPopup(options){if(existsInDOM(options.trigger)||typeof options.triggerElement!=='undefined'||typeof options.premadeContentElement!=='undefined'){return new XM_Popup(options);}};exports.createPopup=createPopup;var createAccordion=function createAccordion(options){if(existsInDOM(options.triggerSelector)&&existsInDOM(options.contentSelector)){return new XM_Accordion(options);}};exports.createAccordion=createAccordion;var createFormInput=function createFormInput(elements){var _iterator=_createForOfIteratorHelper(elements),_step;try{var _loop=function _loop(){var el=_step.value;if(el.classList.contains('always-active'))return"continue";var input=el.querySelector('input'),textarea=el.querySelector('textarea'),activeClass='active';var inputItem=undefined;if(input)inputItem=input;if(textarea)inputItem=textarea;if(inputItem){// if input item has value or is already focused, activate it
if(inputItem.value!==''||inputItem.getAttribute('data-pwd')!==null&&inputItem.getAttribute('data-pwd')!==''||inputItem===document.activeElement){el.classList.add(activeClass);}inputItem.addEventListener('focus',function(){el.classList.add(activeClass);});inputItem.addEventListener('blur',function(){if(inputItem.value===''){el.classList.remove(activeClass);}});}};for(_iterator.s();!(_step=_iterator.n()).done;){var _ret=_loop();if(_ret==="continue")continue;}}catch(err){_iterator.e(err);}finally{_iterator.f();}};exports.createFormInput=createFormInput;

},{}],4:[function(require,module,exports){
"use strict";/*------------
    FORM 
------------*/require('./form/vklogin-form-input');

},{"./form/vklogin-form-input":1}]},{},[4]);
