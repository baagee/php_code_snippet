<!DOCTYPE html>
<html>
<head>
    <title><?php echo $host . '-' . $title_type; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE"/>
    <style type="text/css">
        body{background-color:white;color:black;font:9pt/11pt verdana,arial,sans-serif}#container{margin:10px}.error_line{padding-left:.5em;color:red;font-weight:bold;background-color:white;overflow: hidden;text-overflow:ellipsis;}a:link{font:9pt/11pt verdana,arial,sans-serif;color:red}a:visited{font:9pt/11pt verdana,arial,sans-serif;color:#4e4e4e}h1{color:#f00;font:18pt "Verdana";margin-bottom:.5em}.bg1{background-color:#fcf8e3;padding-left:5px}.bg2{background-color:#eee;font-weight:600}.table{font:11pt Menlo,Consolas,"Lucida Console"}.info{background:none repeat scroll 0 0 #f2dedf;border:0 solid #aaa;border-radius:10px 10px 10px 10px;color:#c04644;font-size:11pt;line-height:160%;margin-bottom:1em;padding:1em}.hljs{display:block;overflow-x:auto;padding:.5em;background:#fdf6e3;color:#657b83}.hljs-comment,.hljs-quote{color:#93a1a1}.hljs-keyword,.hljs-selector-tag,.hljs-addition{color:#859900}.hljs-number,.hljs-string,.hljs-meta .hljs-meta-string,.hljs-literal,.hljs-doctag,.hljs-regexp{color:#2aa198}.hljs-title,.hljs-section,.hljs-name,.hljs-selector-id,.hljs-selector-class{color:#268bd2}.hljs-attribute,.hljs-attr,.hljs-variable,.hljs-template-variable,.hljs-class .hljs-title,.hljs-type{color:#b58900}.hljs-symbol,.hljs-bullet,.hljs-subst,.hljs-meta,.hljs-meta .hljs-keyword,.hljs-selector-attr,.hljs-selector-pseudo,.hljs-link{color:#cb4b16}.hljs-built_in,.hljs-deletion{color:#dc322f}.hljs-formula{background:#eee8d5}.hljs-emphasis{font-style:italic}.hljs-strong{font-weight:bold}
    </style>
    <script>
        !function(e){var n="object"==typeof window&&window||"object"==typeof self&&self;"undefined"!=typeof exports?e(exports):n&&(n.hljs=e({}),"function"==typeof define&&define.amd&&define([],function(){return n.hljs}))}(function(e){function n(e){return e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function t(e){return e.nodeName.toLowerCase()}function r(e,n){var t=e&&e.exec(n);return t&&0===t.index}function a(e){return k.test(e)}function i(e){var n,t,r,i,o=e.className+" ";if(o+=e.parentNode?e.parentNode.className:"",t=B.exec(o)){return w(t[1])?t[1]:"no-highlight"}for(o=o.split(/\s+/),n=0,r=o.length;r>n;n++){if(i=o[n],a(i)||w(i)){return i}}}function o(e){var n,t={},r=Array.prototype.slice.call(arguments,1);for(n in e){t[n]=e[n]}return r.forEach(function(e){for(n in e){t[n]=e[n]}}),t}function u(e){var n=[];return function r(e,a){for(var i=e.firstChild;i;i=i.nextSibling){3===i.nodeType?a+=i.nodeValue.length:1===i.nodeType&&(n.push({event:"start",offset:a,node:i}),a=r(i,a),t(i).match(/br|hr|img|input/)||n.push({event:"stop",offset:a,node:i}))}return a}(e,0),n}function c(e,r,a){function i(){return e.length&&r.length?e[0].offset!==r[0].offset?e[0].offset<r[0].offset?e:r:"start"===r[0].event?e:r:e.length?e:r}function o(e){function r(e){return" "+e.nodeName+'="'+n(e.value).replace('"',"&quot;")+'"'}s+="<"+t(e)+E.map.call(e.attributes,r).join("")+">"}function u(e){s+="</"+t(e)+">"}function c(e){("start"===e.event?o:u)(e.node)}for(var l=0,s="",f=[];e.length||r.length;){var g=i();if(s+=n(a.substring(l,g[0].offset)),l=g[0].offset,g===e){f.reverse().forEach(u);do{c(g.splice(0,1)[0]),g=i()}while(g===e&&g.length&&g[0].offset===l);f.reverse().forEach(o)}else{"start"===g[0].event?f.push(g[0].node):f.pop(),c(g.splice(0,1)[0])}}return s+n(a.substr(l))}function l(e){return e.v&&!e.cached_variants&&(e.cached_variants=e.v.map(function(n){return o(e,{v:null},n)})),e.cached_variants||e.eW&&[o(e)]||[e]}function s(e){function n(e){return e&&e.source||e}function t(t,r){return new RegExp(n(t),"m"+(e.cI?"i":"")+(r?"g":""))}function r(a,i){if(!a.compiled){if(a.compiled=!0,a.k=a.k||a.bK,a.k){var o={},u=function(n,t){e.cI&&(t=t.toLowerCase()),t.split(" ").forEach(function(e){var t=e.split("|");o[t[0]]=[n,t[1]?Number(t[1]):1]})};"string"==typeof a.k?u("keyword",a.k):x(a.k).forEach(function(e){u(e,a.k[e])}),a.k=o}a.lR=t(a.l||/\w+/,!0),i&&(a.bK&&(a.b="\\b("+a.bK.split(" ").join("|")+")\\b"),a.b||(a.b=/\B|\b/),a.bR=t(a.b),a.e||a.eW||(a.e=/\B|\b/),a.e&&(a.eR=t(a.e)),a.tE=n(a.e)||"",a.eW&&i.tE&&(a.tE+=(a.e?"|":"")+i.tE)),a.i&&(a.iR=t(a.i)),null==a.r&&(a.r=1),a.c||(a.c=[]),a.c=Array.prototype.concat.apply([],a.c.map(function(e){return l("self"===e?a:e)})),a.c.forEach(function(e){r(e,a)}),a.starts&&r(a.starts,i);var c=a.c.map(function(e){return e.bK?"\\.?("+e.b+")\\.?":e.b}).concat([a.tE,a.i]).map(n).filter(Boolean);a.t=c.length?t(c.join("|"),!0):{exec:function(){return null}}}}r(e)}function f(e,t,a,i){function o(e,n){var t,a;for(t=0,a=n.c.length;a>t;t++){if(r(n.c[t].bR,e)){return n.c[t]}}}function u(e,n){if(r(e.eR,n)){for(;e.endsParent&&e.parent;){e=e.parent}return e}return e.eW?u(e.parent,n):void 0}function c(e,n){return !a&&r(n.iR,e)}function l(e,n){var t=N.cI?n[0].toLowerCase():n[0];return e.k.hasOwnProperty(t)&&e.k[t]}function p(e,n,t,r){var a=r?"":I.classPrefix,i='<span class="'+a,o=t?"":C;return i+=e+'">',i+n+o}function h(){var e,t,r,a;if(!E.k){return n(k)}for(a="",t=0,E.lR.lastIndex=0,r=E.lR.exec(k);r;){a+=n(k.substring(t,r.index)),e=l(E,r),e?(B+=e[1],a+=p(e[0],n(r[0]))):a+=n(r[0]),t=E.lR.lastIndex,r=E.lR.exec(k)}return a+n(k.substr(t))}function d(){var e="string"==typeof E.sL;if(e&&!y[E.sL]){return n(k)}var t=e?f(E.sL,k,!0,x[E.sL]):g(k,E.sL.length?E.sL:void 0);return E.r>0&&(B+=t.r),e&&(x[E.sL]=t.top),p(t.language,t.value,!1,!0)}function b(){L+=null!=E.sL?d():h(),k=""}function v(e){L+=e.cN?p(e.cN,"",!0):"",E=Object.create(e,{parent:{value:E}})}function m(e,n){if(k+=e,null==n){return b(),0}var t=o(n,E);if(t){return t.skip?k+=n:(t.eB&&(k+=n),b(),t.rB||t.eB||(k=n)),v(t,n),t.rB?0:n.length}var r=u(E,n);if(r){var a=E;a.skip?k+=n:(a.rE||a.eE||(k+=n),b(),a.eE&&(k=n));do{E.cN&&(L+=C),E.skip||(B+=E.r),E=E.parent}while(E!==r.parent);return r.starts&&v(r.starts,""),a.rE?0:n.length}if(c(n,E)){throw new Error('Illegal lexeme "'+n+'" for mode "'+(E.cN||"<unnamed>")+'"')}return k+=n,n.length||1}var N=w(e);if(!N){throw new Error('Unknown language: "'+e+'"')}s(N);var R,E=i||N,x={},L="";for(R=E;R!==N;R=R.parent){R.cN&&(L=p(R.cN,"",!0)+L)}var k="",B=0;try{for(var M,j,O=0;;){if(E.t.lastIndex=O,M=E.t.exec(t),!M){break}j=m(t.substring(O,M.index),M[0]),O=M.index+j}for(m(t.substr(O)),R=E;R.parent;R=R.parent){R.cN&&(L+=C)}return{r:B,value:L,language:e,top:E}}catch(T){if(T.message&&-1!==T.message.indexOf("Illegal")){return{r:0,value:n(t)}}throw T}}function g(e,t){t=t||I.languages||x(y);var r={r:0,value:n(e)},a=r;return t.filter(w).forEach(function(n){var t=f(n,e,!1);t.language=n,t.r>a.r&&(a=t),t.r>r.r&&(a=r,r=t)}),a.language&&(r.second_best=a),r}function p(e){return I.tabReplace||I.useBR?e.replace(M,function(e,n){return I.useBR&&"\n"===e?"<br>":I.tabReplace?n.replace(/\t/g,I.tabReplace):""
        }):e}function h(e,n,t){var r=n?L[n]:t,a=[e.trim()];return e.match(/\bhljs\b/)||a.push("hljs"),-1===e.indexOf(r)&&a.push(r),a.join(" ").trim()}function d(e){var n,t,r,o,l,s=i(e);a(s)||(I.useBR?(n=document.createElementNS("http://www.w3.org/1999/xhtml","div"),n.innerHTML=e.innerHTML.replace(/\n/g,"").replace(/<br[ \/]*>/g,"\n")):n=e,l=n.textContent,r=s?f(s,l,!0):g(l),t=u(n),t.length&&(o=document.createElementNS("http://www.w3.org/1999/xhtml","div"),o.innerHTML=r.value,r.value=c(t,u(o),l)),r.value=p(r.value),e.innerHTML=r.value,e.className=h(e.className,s,r.language),e.result={language:r.language,re:r.r},r.second_best&&(e.second_best={language:r.second_best.language,re:r.second_best.r}))}function b(e){I=o(I,e)}function v(){if(!v.called){v.called=!0;var e=document.querySelectorAll("pre code");E.forEach.call(e,d)}}function m(){addEventListener("DOMContentLoaded",v,!1),addEventListener("load",v,!1)}function N(n,t){var r=y[n]=t(e);r.aliases&&r.aliases.forEach(function(e){L[e]=n})}function R(){return x(y)}function w(e){return e=(e||"").toLowerCase(),y[e]||y[L[e]]}var E=[],x=Object.keys,y={},L={},k=/^(no-?highlight|plain|text)$/i,B=/\blang(?:uage)?-([\w-]+)\b/i,M=/((^(<[^>]+>|\t|)+|(?:\n)))/gm,C="</span>",I={classPrefix:"hljs-",tabReplace:null,useBR:!1,languages:void 0};return e.highlight=f,e.highlightAuto=g,e.fixMarkup=p,e.highlightBlock=d,e.configure=b,e.initHighlighting=v,e.initHighlightingOnLoad=m,e.registerLanguage=N,e.listLanguages=R,e.getLanguage=w,e.inherit=o,e.IR="[a-zA-Z]\\w*",e.UIR="[a-zA-Z_]\\w*",e.NR="\\b\\d+(\\.\\d+)?",e.CNR="(-?)(\\b0[xX][a-fA-F0-9]+|(\\b\\d+(\\.\\d*)?|\\.\\d+)([eE][-+]?\\d+)?)",e.BNR="\\b(0b[01]+)",e.RSR="!|!=|!==|%|%=|&|&&|&=|\\*|\\*=|\\+|\\+=|,|-|-=|/=|/|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\\?|\\[|\\{|\\(|\\^|\\^=|\\||\\|=|\\|\\||~",e.BE={b:"\\\\[\\s\\S]",r:0},e.ASM={cN:"string",b:"'",e:"'",i:"\\n",c:[e.BE]},e.QSM={cN:"string",b:'"',e:'"',i:"\\n",c:[e.BE]},e.PWM={b:/\b(a|an|the|are|I'm|isn't|don't|doesn't|won't|but|just|should|pretty|simply|enough|gonna|going|wtf|so|such|will|you|your|they|like|more)\b/},e.C=function(n,t,r){var a=e.inherit({cN:"comment",b:n,e:t,c:[]},r||{});return a.c.push(e.PWM),a.c.push({cN:"doctag",b:"(?:TODO|FIXME|NOTE|BUG|XXX):",r:0}),a},e.CLCM=e.C("//","$"),e.CBCM=e.C("/\\*","\\*/"),e.HCM=e.C("#","$"),e.NM={cN:"number",b:e.NR,r:0},e.CNM={cN:"number",b:e.CNR,r:0},e.BNM={cN:"number",b:e.BNR,r:0},e.CSSNM={cN:"number",b:e.NR+"(%|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc|px|deg|grad|rad|turn|s|ms|Hz|kHz|dpi|dpcm|dppx)?",r:0},e.RM={cN:"regexp",b:/\//,e:/\/[gimuy]*/,i:/\n/,c:[e.BE,{b:/\[/,e:/\]/,r:0,c:[e.BE]}]},e.TM={cN:"title",b:e.IR,r:0},e.UTM={cN:"title",b:e.UIR,r:0},e.METHOD_GUARD={b:"\\.\\s*"+e.UIR,r:0},e});hljs.registerLanguage("php",function(e){var c={b:"\\$+[a-zA-Z_-ÿ][a-zA-Z0-9_-ÿ]*"},i={cN:"meta",b:/<\?(php)?|\?>/},t={cN:"string",c:[e.BE,i],v:[{b:'b"',e:'"'},{b:"b'",e:"'"},e.inherit(e.ASM,{i:null}),e.inherit(e.QSM,{i:null})]},a={v:[e.BNM,e.CNM]};return{aliases:["php3","php4","php5","php6"],cI:!0,k:"and include_once list abstract global private echo interface as static endswitch array null if endwhile or const for endforeach self var while isset public protected exit foreach throw elseif include __FILE__ empty require_once do xor return parent clone use __CLASS__ __LINE__ else break print eval new catch __METHOD__ case exception default die require __FUNCTION__ enddeclare final try switch continue endfor endif declare unset true false trait goto instanceof insteadof __DIR__ __NAMESPACE__ yield finally",c:[e.HCM,e.C("//","$",{c:[i]}),e.C("/\\*","\\*/",{c:[{cN:"doctag",b:"@[A-Za-z]+"}]}),e.C("__halt_compiler.+?;",!1,{eW:!0,k:"__halt_compiler",l:e.UIR}),{cN:"string",b:/<<<['"]?\w+['"]?$/,e:/^\w+;?$/,c:[e.BE,{cN:"subst",v:[{b:/\$\w+/},{b:/\{\$/,e:/\}/}]}]},i,{cN:"keyword",b:/\$this\b/},c,{b:/(::|->)+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/},{cN:"function",bK:"function",e:/[;{]/,eE:!0,i:"\\$|\\[|%",c:[e.UTM,{cN:"params",b:"\\(",e:"\\)",c:["self",c,e.CBCM,t,a]}]},{cN:"class",bK:"class interface",e:"{",eE:!0,i:/[:\(\$"]/,c:[{bK:"extends implements"},e.UTM]},{bK:"namespace",e:";",i:/[\.']/,c:[e.UTM]},{bK:"use",e:";",c:[e.UTM]},{b:"=>"},t,a]}});
    </script>
    <script>hljs.initHighlightingOnLoad();</script>
</head>
<body>
<div id="container">
    <h1><strong><?php echo $title_type; ?></strong></h1>
    <div class='info'>
        <p><strong><?php echo $title_type . ' message: ' . $errorMsg; ?></strong></p>

<?php
if ($is_debug) {
    if (!empty($backtrace) && is_array($backtrace)) {
        $end = $backtrace[0];
        echo '<p class="bg1" style="line-height: 30px;">In: ' . $end['file'] . ': ' . $end['line'] . '</p>';

        if (!empty($err_php_code_arr)) {
            echo '<pre style="overflow: hidden;"><code class="php" style="padding-bottom: 0;overflow: hidden;text-overflow:ellipsis;">';
            foreach ($err_php_code_arr as $line => $code) {
                if (!empty($code)) {
                    if ($err_line == $line) {
                        echo '</code>';
                        echo '<div class="error_line">' . $line . '. ' . $code . '</div>';
                        echo '<code class="php" style="padding-top: 0;overflow: hidden;text-overflow:ellipsis;">';
                    } else {
                        echo $line . '. ' . $code;
                    }
                }
            }
            echo '</code></pre>';
        }
        echo '</div>';

        echo '<div class="info">';
        echo '<p><strong>PHP Backtrace</strong></p>';
        echo '<table cellpadding="5" cellspacing="1" width="100%" class="table"><tbody>';
        echo '<tr class="bg2"><td>No.</td><td>File</td><td>Line</td><td>Code</td></tr>';
        foreach ($backtrace as $k => $msg) {
            $k++;
            echo '<tr class="bg1">';
            echo '<td>' . $k . '</td>';
            echo '<td>' . $msg['file'] . '</td>';
            echo '<td>' . $msg['line'] . '</td>';
            echo '<td>' . $msg['function'] . '</td>';
            echo '</tr>';
        }
        echo "</tbody></table></div>";

    } else {
        echo '</div>';
    }
} else {
    // debug=false
    echo "</div>";
}
echo '</div></body></html>';
