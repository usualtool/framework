/**
       * --------------------------------------------------------       
       *  |    ░░░░░░░░░     █   █░▀▀█▀▀░    ░░░░░░░░░      |           
       *  |  ░░░░░░░         █▄▄▄█   █                      |            
       *  |                                                 |            
       *  | Author:HuangDou   Email:292951110@qq.com        |            
       *  | QQ-Group:583610949                              |           
       *  | WebSite:http://www.UsualTool.com                |            
       *  | UT Framework is suitable for Apache2 protocol.  |            
       * --------------------------------------------------------                
*/
function changelang(){
	var value = $("#language").children('option:selected').val();
	if(value=="big5"){
		setcookie(cookiename,"zh");
		setcookie("chinaspeak","big5");
	}else{
		setcookie(cookiename,value);
		setcookie("chinaspeak","");
	}
	location.reload();
}
function clicklang(lang){
	var lang = lang;
	if(lang=="big5"){
		setcookie(cookiename,"zh");
		setcookie("chinaspeak","big5");
	}else{
		setcookie(cookiename,lang);
		setcookie("chinaspeak","");
	}
	location.reload();
}
function setcookie(cookiename,value){ 
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = cookiename + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function transbig(){
	document.body.innerHTML = document.body.innerHTML.transbig();
}
function jsonstr(word){
	var word;
	$.ajax({
		url:jsonweb+"lang/lg-"+getcookie(""+cookiename+"")+".json",
		success:function(jsondata){
			var data = eval(jsondata);
			alert(data["l"][word]);
		}
	});
}
function getcookie(cookiename){
var arr = document.cookie.match(new RegExp("(^| )"+cookiename+"=([^;]*)(;|$)"));
if(arr != null) return unescape(arr[2]); return null
}
(function($,undefined){
	$(document).ready(function(){
		var cookiename = "Language";
		if(window.ROOTPATH=='' || window.ROOTPATH==undefined || window.ROOTPATH==null){
			var jsonweb="http://"+document.domain+":"+location.port+"/";
		}else{
			var jsonweb=""+window.ROOTPATH+"/";
		}
		String.prototype.transbig=function(){
			htmlobj=$.ajax({url:jsonweb+"lang/lg-"+getcookie("chinaspeak")+".json",async:false});
			var zhjsondata=(htmlobj.responseText);
			var obj = eval("("+zhjsondata+")");
			var s=obj["l"]["simplified"];
			var t=obj["l"]["traditional"];
			var k='';
			for(var i=0;i<this.length;i++) k+=(s.indexOf(this.charAt(i))==-1)?this.charAt(i):t.charAt(s.indexOf(this.charAt(i)))
			return k;
		}
		var mylanguage = (navigator.language || navigator.browserLanguage).toLowerCase();
		if (getcookie(cookiename) != ""){
			if (getcookie(cookiename) == "zh" && getcookie("chinaspeak") == "big5"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "zh"});transbig();}
			else if(getcookie(cookiename) == "ja"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ja"});}
			else if(getcookie(cookiename) == "en"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "en"});}
			else if(getcookie(cookiename) == "ko"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ko"});}
			else if(getcookie(cookiename) == "ru"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ru"});}
			else if(getcookie(cookiename) == "fr"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "fr"});}
			else if(getcookie(cookiename) == "bo"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "bo"});}
			else if(getcookie(cookiename) == "ug"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ug"});}
			else if(getcookie(cookiename) == "de"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "de"});}
			else if(getcookie(cookiename) == "ar"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ar"});}
			else if(getcookie(cookiename) == "pt"){$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "pt"});}
			else{$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "zh"});}
		}else{
			if (mylanguage.indexOf("zh") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "zh"});}
			else if(mylanguage.indexOf("tw") > -1 || mylanguage.indexOf("hk") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "zh"});transbig();}
			else if(mylanguage.indexOf("en") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "en"});}
			else if(mylanguage.indexOf("ja") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ja"});}
			else if(mylanguage.indexOf("ko") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ko"});}
			else if(mylanguage.indexOf("ru") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ru"});}
			else if(mylanguage.indexOf("fr") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "fr"});}
			else if(mylanguage.indexOf("ug") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ug"});}
			else if(mylanguage.indexOf("de") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "de"});}
			else if(mylanguage.indexOf("ar") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "ar"});}
			else if(mylanguage.indexOf("pt") > -1) {$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "pt"});}
			else{$("[data-localize]").localize("lg", {pathPrefix: "lang", language: "zh"});}
		}
	}); 
})(jQuery);