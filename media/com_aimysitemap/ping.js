/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2014-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
function AimySitemapPing(se,token,outSel) {var $out=jQuery(outSel);var url='index.php?option=com_aimysitemap&task=notify.ping_ajax';var seName=se=='indexnow'?'IndexNow.org':se;var $div=jQuery('<div></div>') .addClass('row-fluid') .html(jQuery('<div></div>') .addClass('span2') .html(jQuery('<strong></strong>').text(seName)));var $progress=jQuery('<div></div>') .addClass('span10 notify-task') .html(jQuery('<img></img>') .attr('src','../media/com_aimysitemap/progress.gif'));$out.append($div.append($progress));var mark_ok=function(d) {$progress.hide() .html(jQuery('<i></i>') .addClass('aimy-icon-ok')) .fadeIn('slow');if('msg'in d) {$progress.append(jQuery('<span></span>').addClass('msg').text(d.msg));}};var mark_fail=function(msg) {$progress.hide() .html(jQuery('<i></i>') .addClass('aimy-icon-cancel'));if(msg) {$progress.append(jQuery('<span></span>') .addClass('error') .text(' '+msg));} $progress.fadeIn('slow');};var respToJson=function(jd) {if(jd&&typeof(jd)=='string') {try {jd=jQuery.parseJSON(jd);} catch(e){}} return jd;};var data={};data[token]=1;data['n']=se;jQuery.post(url,data,function(d) {d=respToJson(d);if(d!==null&&typeof d==='object'&&d.ok==1) {mark_ok(d);} else {var msg='Invalid results from backend';if(d!=null&&typeof d==='object'&&d.err) {msg=d.err;try {if(d.msg&&d.msg.length) {if(!msg.match(/[!.:]$/)) {msg+=':';} msg+=' '+d.msg;}} catch(e){}} mark_fail(msg);} $progress.attr('data-done',1);}) .fail(function() {mark_fail('Could not communicate with backend');$progress.attr('data-done',1);});} 
