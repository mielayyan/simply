!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):"object"==typeof exports?t(require("jquery")):t(jQuery)}(function(zt){"use strict";zt.Zebra_DatePicker=function(t,M){var P,F,Z,S,x,Y,I,z,j,m,N,H,O,L,T,R,W,B,E,Q,J,G,U,V,$,q,d,X,K,tt,et,st,it,nt,at,i,rt,ot,dt,ct,lt,gt,c,_t,ht,pt={always_visible:!(this.version="1.9.12"),container:zt("body"),current_date:!1,custom_classes:!1,days:["domingo","Segunda-feira","terça","Quarta-feira","Quinta-feira","Sexta-feira","sábado"],days_abbr:!1,default_position:"above",direction:0,disable_time_picker:!1,disabled_dates:!1,enabled_dates:!1,enabled_hours:!1,enabled_minutes:!1,enabled_seconds:!1,fast_navigation:!0,first_day_of_week:1,format:"Y-m-d",header_captions:{days:"F, Y",months:"Y",years:"Y1 - Y2"},icon_margin:!1,icon_position:"right",inside:!0,lang_clear_date:"Limpar data",months:["janeiro","fevereiro","Março","abril","Maio","Junho","Julho","agosto","setembro","Outubro","novembro","dezembro"],months_abbr:!1,navigation:["&#9664;","&#9654;","&#9650;","&#9660;"],offset:[5,-5],open_icon_only:!1,open_on_focus:!1,pair:!1,readonly_element:!0,rtl:!1,select_other_months:!1,show_clear_date:0,show_icon:!0,show_other_months:!0,show_select_today:"Hoje",show_week_number:!1,start_date:!1,strict:!1,view:"days",weekend_days:[0,6],zero_pad:!1,onChange:null,onClear:null,onOpen:null,onClose:null,onSelect:null},ut={},mt={},ft=!1,bt="",yt=!1,vt=!!navigator.platform&&/iPad|iPhone|iPod/.test(navigator.platform),wt=this,kt=zt(t),e=function(t){var e,s,i,a,n={days:["d","j","D"],months:["F","m","M","n","t"],years:["o","Y","y"],hours:["G","g","H","h"],minutes:["i"],seconds:["s"],ampm:["A","a"]},r=null,o=!1;for(i=0;i<3;i++)bt+=Math.floor(65536*(1+Math.random())).toString(16);if(L=[],T=[],!t)for(e in wt.settings=zt.extend({},pt,zt.fn.Zebra_DatePicker.defaults,M),mt.readonly=kt.attr("readonly"),mt.style=kt.attr("style"),mt.padding_left=parseInt(kt.css("paddingLeft"),10)||0,mt.padding_right=parseInt(kt.css("paddingRight"),10)||0,kt.data())0===e.indexOf("zdp_")&&(e=e.replace(/^zdp\_/,""),void 0!==pt[e]&&(wt.settings[e]="pair"===e?zt(kt.data("zdp_"+e)):kt.data("zdp_"+e)));for(wt.settings.readonly_element?kt.attr("readonly","readonly"):kt.removeAttr("readonly"),lt=!1,ht=[];!o;){for(r in n)zt.each(n[r],function(t,e){var s,i;if(-1<wt.settings.format.indexOf(e))if("days"===r)ht.push("days");else if("months"===r)ht.push("months");else if("years"===r)ht.push("years");else if(("hours"===r||"minutes"===r||"seconds"===r||"ampm"===r)&&!wt.settings.disable_time_picker)if(lt||(lt={is12hour:!1},ht.push("time")),"hours"===r)for("g"===(lt.hour_format=e)||"h"===e?(i=12,lt.is12hour=!0):i=24,lt.hours=[],s=12===i?1:0;s<(12===i?13:i);s++)(!zt.isArray(wt.settings.enabled_hours)||-1<zt.inArray(s,wt.settings.enabled_hours))&&lt.hours.push(s);else if("minutes"===r)for(lt.minutes=[],s=0;s<60;s++)(!zt.isArray(wt.settings.enabled_minutes)||-1<zt.inArray(s,wt.settings.enabled_minutes))&&lt.minutes.push(s);else if("seconds"===r)for(lt.seconds=[],s=0;s<60;s++)(!zt.isArray(wt.settings.enabled_seconds)||-1<zt.inArray(s,wt.settings.enabled_seconds))&&lt.seconds.push(s);else lt.ampm=["am","pm"]});lt.hour_format&&lt.ampm&&!1===lt.is12hour?wt.settings.format=wt.settings.format.replace(lt.hour_format,lt.hour_format.toLowerCase()):o=!0}for(i in 0===ht.length&&(ht=["years","months","days"]),-1===zt.inArray(wt.settings.view,ht)&&(wt.settings.view=ht[ht.length-1]),I=[],wt.settings.custom_classes)wt.settings.custom_classes.hasOwnProperty(i)&&-1===I.indexOf(i)&&I.push(i);for(a=0;a<2+I.length;a++)s=0===a?wt.settings.disabled_dates:1===a?wt.settings.enabled_dates:wt.settings.custom_classes[I[a-2]],zt.isArray(s)&&0<s.length&&zt.each(s,function(){var t,e,s,i,n=this.split(" ");for(t=0;t<4;t++){for(n[t]||(n[t]="*"),n[t]=-1<n[t].indexOf(",")?n[t].split(","):new Array(n[t]),e=0;e<n[t].length;e++)if(-1<n[t][e].indexOf("-")&&null!==(i=n[t][e].match(/^([0-9]+)\-([0-9]+)/))){for(s=xt(i[1]);s<=xt(i[2]);s++)-1===zt.inArray(s,n[t])&&n[t].push(s+"");n[t].splice(e,1)}for(e=0;e<n[t].length;e++)n[t][e]=isNaN(xt(n[t][e]))?n[t][e]:xt(n[t][e])}0===a?L.push(n):1===a?T.push(n):(void 0===ut[I[a-2]]&&(ut[I[a-2]]=[]),ut[I[a-2]].push(n))});var d,c,l=!1!==wt.settings.current_date?new Date(wt.settings.current_date):new Date,g=wt.settings.reference_date?wt.settings.reference_date:kt.data("zdp_reference_date")&&void 0!==kt.data("zdp_reference_date")?kt.data("zdp_reference_date"):l;if(R=ot=void 0,B=g.getMonth(),x=l.getMonth(),E=g.getFullYear(),Y=l.getFullYear(),W=g.getDate(),S=l.getDate(),!0===wt.settings.direction)ot=g;else if(!1===wt.settings.direction)V=(R=g).getMonth(),$=R.getFullYear(),U=R.getDate();else if(!zt.isArray(wt.settings.direction)&&Pt(wt.settings.direction)&&0<xt(wt.settings.direction)||zt.isArray(wt.settings.direction)&&((d=Dt(wt.settings.direction[0]))||!0===wt.settings.direction[0]||Pt(wt.settings.direction[0])&&0<wt.settings.direction[0])&&((c=Dt(wt.settings.direction[1]))||!1===wt.settings.direction[1]||Pt(wt.settings.direction[1])&&0<=wt.settings.direction[1]))ot=d||new Date(E,B,W+(zt.isArray(wt.settings.direction)?xt(!0===wt.settings.direction[0]?0:wt.settings.direction[0]):xt(wt.settings.direction))),B=ot.getMonth(),E=ot.getFullYear(),W=ot.getDate(),c&&+ot<=+c?R=c:!c&&!1!==wt.settings.direction[1]&&zt.isArray(wt.settings.direction)&&(R=new Date(E,B,W+xt(wt.settings.direction[1]))),R&&(V=R.getMonth(),$=R.getFullYear(),U=R.getDate());else if(!zt.isArray(wt.settings.direction)&&Pt(wt.settings.direction)&&xt(wt.settings.direction)<0||zt.isArray(wt.settings.direction)&&(!1===wt.settings.direction[0]||Pt(wt.settings.direction[0])&&wt.settings.direction[0]<0)&&((d=Dt(wt.settings.direction[1]))||Pt(wt.settings.direction[1])&&0<=wt.settings.direction[1]))R=new Date(E,B,W+(zt.isArray(wt.settings.direction)?xt(!1===wt.settings.direction[0]?0:wt.settings.direction[0]):xt(wt.settings.direction))),V=R.getMonth(),$=R.getFullYear(),U=R.getDate(),d&&+d<+R?ot=d:!d&&zt.isArray(wt.settings.direction)&&(ot=new Date($,V,U-xt(wt.settings.direction[1]))),ot&&(B=ot.getMonth(),E=ot.getFullYear(),W=ot.getDate());else if(zt.isArray(wt.settings.disabled_dates)&&0<wt.settings.disabled_dates.length)for(var _ in L)if(-1<zt.inArray("*",L[_][0])&&-1<zt.inArray("*",L[_][1])&&-1<zt.inArray("*",L[_][2])&&-1<zt.inArray("*",L[_][3])){var h=[];if(zt.each(T,function(){var t=this;"*"!==t[2][0]&&h.push(parseInt(t[2][0]+("*"===t[1][0]?"12":St(t[1][0],2))+("*"===t[0][0]?"*"===t[1][0]?"31":new Date(t[2][0],t[1][0],0).getDate():St(t[0][0],2)),10))}),h.sort(),0<h.length){var p=(h[0]+"").match(/([0-9]{4})([0-9]{2})([0-9]{2})/);E=parseInt(p[1],10),B=parseInt(p[2],10)-1,W=parseInt(p[3],10)}break}if(Mt(E,B,W)){for(;Mt(E);)ot?(E++,B=0):(E--,B=11);for(;Mt(E,B);)ot?(B++,W=1):(B--,W=new Date(E,B+1,0).getDate()),11<B?(E++,B=0,W=1):B<0&&(E--,B=11,W=new Date(E,B+1,0).getDate());for(;Mt(E,B,W);)ot?W++:W--,l=new Date(E,B,W),E=l.getFullYear(),B=l.getMonth(),W=l.getDate();l=new Date(E,B,W),E=l.getFullYear(),B=l.getMonth(),W=l.getDate()}wt.settings.start_date&&"object"==typeof wt.settings.start_date&&wt.settings.start_date instanceof Date&&(wt.settings.start_date=Ct(wt.settings.start_date));var u=Dt(kt.val()||(wt.settings.start_date?wt.settings.start_date:""));if(u&&wt.settings.strict&&Mt(u.getFullYear(),u.getMonth(),u.getDate())&&kt.val(""),t||void 0===ot&&void 0===u||Yt(void 0!==u?u:ot),!(wt.settings.always_visible instanceof jQuery)){if(!t){if(wt.settings.show_icon){"firefox"===It.name&&kt.is('input[type="text"]')&&"inline"===kt.css("display")&&kt.css("display","inline-block");var m=parseInt(kt.css("marginTop"),10)||0,f=parseInt(kt.css("marginRight"),10)||0,b=parseInt(kt.css("marginBottom"),10)||0,y=parseInt(kt.css("marginLeft"),10)||0,v=zt('<span class="Zebra_DatePicker_Icon_Wrapper"></span>').css({display:kt.css("display"),position:"static"===kt.css("position")?"relative":kt.css("position"),"float":kt.css("float"),top:kt.css("top"),right:kt.css("right"),bottom:kt.css("bottom"),left:kt.css("left"),marginTop:m<0?m:0,marginRight:f<0?f:0,marginBottom:b<0?b:0,marginLeft:y<0?y:0,paddingTop:m,paddingRight:f,paddingBottom:b,paddingLeft:y});"block"===kt.css("display")&&v.css("width",kt.outerWidth(!0)),kt.wrap(v).css({position:"relative","float":"none",top:"auto",right:"auto",bottom:"auto",left:"auto",marginTop:0,marginRight:0,marginBottom:0,marginLeft:0}),G=zt('<button type="button" class="Zebra_DatePicker_Icon'+("disabled"===kt.attr("disabled")?" Zebra_DatePicker_Icon_Disabled":"")+'">Pick a date</button>'),wt.icon=G,F=wt.settings.open_icon_only?G:G.add(kt)}else F=kt;F.on("click.Zebra_DatePicker_"+bt+(wt.settings.open_on_focus?" focus.Zebra_DatePicker_"+bt:""),function(){z.hasClass("dp_hidden")&&!kt.attr("disabled")&&(!yt||wt.settings.readonly_element?wt.show():(clearTimeout(dt),dt=setTimeout(function(){wt.show()},600)))}),F.on("keydown.Zebra_DatePicker_"+bt,function(t){9!==t.keyCode||z.hasClass("dp_hidden")||wt.hide()}),!wt.settings.readonly_element&&wt.settings.pair&&kt.on("blur.Zebra_DatePicker_"+bt,function(){var t;(t=Dt(zt(this).val()))&&!Mt(t.getFullYear(),t.getMonth(),t.getDate())&&Yt(t)}),void 0!==G&&G.insertAfter(kt)}if(void 0!==G){G.attr("style","");var w=kt.outerWidth(),k=kt.outerHeight(),D=G.outerWidth(),A=G.outerHeight();G.css("top",(k-A)/2),wt.settings.inside?"right"===wt.settings.icon_position?(G.css("right",!1!==wt.settings.icon_margin?wt.settings.icon_margin:mt.padding_right),kt.css("paddingRight",2*(!1!==wt.settings.icon_margin?wt.settings.icon_margin:mt.padding_right)+D)):(G.css("left",!1!==wt.settings.icon_margin?wt.settings.icon_margin:mt.padding_left),kt.css("paddingLeft",2*(!1!==wt.settings.icon_margin?wt.settings.icon_margin:mt.padding_left)+D)):G.css("left",w+(!1!==wt.settings.icon_margin?wt.settings.icon_margin:mt.padding_left)),G.removeClass("Zebra_DatePicker_Icon_Disabled"),"disabled"===kt.attr("disabled")&&G.addClass("Zebra_DatePicker_Icon_Disabled")}}if(rt=!1!==wt.settings.show_select_today&&-1<zt.inArray("days",ht)&&!Mt(Y,x,S)&&wt.settings.show_select_today,t)return zt(".dp_previous",z).html(wt.settings.navigation[0]),zt(".dp_next",z).html(wt.settings.navigation[1]),zt(".dp_time_controls_increase .dp_time_control",z).html(wt.settings.navigation[2]),zt(".dp_time_controls_decrease .dp_time_control",z).html(wt.settings.navigation[3]),zt(".dp_clear",z).html(wt.settings.lang_clear_date),void zt(".dp_today",z).html(wt.settings.show_select_today);zt(window).on("resize.Zebra_DatePicker_"+bt+", orientationchange.Zebra_DatePicker_"+bt,function(){wt.hide()});var C='<div class="Zebra_DatePicker"><table class="dp_header dp_actions"><tr><td class="dp_previous">'+wt.settings.navigation[0]+(vt?"&#xFE0E;":"")+'</td><td class="dp_caption"></td><td class="dp_next">'+wt.settings.navigation[1]+(vt?"&#xFE0E;":"")+'</td></tr></table><table class="dp_daypicker'+(wt.settings.show_week_number?" dp_week_numbers":"")+' dp_body"></table><table class="dp_monthpicker dp_body"></table><table class="dp_yearpicker dp_body"></table><table class="dp_timepicker dp_body"></table><table class="dp_footer dp_actions"><tr><td class="dp_today">'+rt+'</td><td class="dp_clear">'+wt.settings.lang_clear_date+'</td><td class="dp_view_toggler dp_icon">&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="dp_confirm dp_icon"></td></tr></table></div>';z=zt(C),J=zt("table.dp_header",z),j=zt("table.dp_daypicker",z),q=zt("table.dp_monthpicker",z),gt=zt("table.dp_yearpicker",z),ct=zt("table.dp_timepicker",z),Q=zt("table.dp_footer",z),at=zt("td.dp_today",Q),P=zt("td.dp_clear",Q),st=zt("td.dp_view_toggler",Q),Z=zt("td.dp_confirm",Q),wt.settings.always_visible instanceof jQuery?kt.attr("disabled")||(wt.settings.always_visible.append(z),wt.show()):wt.settings.container.append(z),z.on("mouseover","td:not(.dp_disabled)",function(){zt(this).addClass("dp_hover")}).on("mouseout","td:not(.dp_disabled)",function(){zt(this).removeClass("dp_hover")}),At(z),zt(wt.settings.rtl?".dp_next":".dp_previous",J).on("click",function(){"months"===_t?nt--:"years"===_t?nt-=12:--it<0&&(it=11,nt--),Ft()}),wt.settings.fast_navigation&&zt(".dp_caption",J).on("click",function(){_t="days"===_t?-1<zt.inArray("months",ht)?"months":-1<zt.inArray("years",ht)?"years":"days":"months"===_t?-1<zt.inArray("years",ht)?"years":-1<zt.inArray("days",ht)?"days":"months":-1<zt.inArray("days",ht)?"days":-1<zt.inArray("months",ht)?"months":"years",Ft()}),zt(wt.settings.rtl?".dp_previous":".dp_next",J).on("click",function(){"months"===_t?nt++:"years"===_t?nt+=12:12==++it&&(it=0,nt++),Ft()}),j.on("click","td:not(.dp_disabled)",function(){var t;wt.settings.select_other_months&&zt(this).attr("class")&&null!==(t=zt(this).attr("class").match(/date\_([0-9]{4})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])/))?Zt(t[1],t[2]-1,t[3],"days",zt(this)):Zt(nt,it,xt(zt(this).html()),"days",zt(this))}),q.on("click","td:not(.dp_disabled)",function(){var t=zt(this).attr("class").match(/dp\_month\_([0-9]+)/);it=xt(t[1]),-1===zt.inArray("days",ht)?Zt(nt,it,1,"months",zt(this)):(_t="days",wt.settings.always_visible&&kt.val(""),Ft())}),gt.on("click","td:not(.dp_disabled)",function(){nt=xt(zt(this).html()),-1===zt.inArray("months",ht)?Zt(nt,1,1,"years",zt(this)):(_t="months",wt.settings.always_visible&&kt.val(""),Ft())}),at.on("click",function(t){var e=!1!==wt.settings.current_date?new Date(wt.settings.current_date):new Date;t.preventDefault(),Zt(e.getFullYear(),e.getMonth(),e.getDate(),"days",zt(".dp_current",j))}),P.on("click",function(t){t.preventDefault(),kt.val(""),O=H=N=null,wt.settings.always_visible?zt("td.dp_selected",z).removeClass("dp_selected"):nt=it=null,kt.focus(),wt.hide(),wt.settings.onClear&&"function"==typeof wt.settings.onClear&&wt.settings.onClear.call(kt)}),st.on("click",function(){"time"!==_t?(_t="time",Ft()):zt(".dp_caption",J).trigger("click")}),Z.on("click",function(){if(zt(".dp_time_controls_increase td",ct).trigger("click"),zt(".dp_time_controls_decrease td",ct).trigger("click"),wt.settings.onSelect&&"function"==typeof wt.settings.onSelect){var t=new Date(nt,it,N,lt&&lt.hours?X+(lt.ampm&&("pm"===et&&X<12||"am"===et&&12===X)?12:0):0,lt&&lt.minutes?K:0,lt&&lt.seconds?tt:0);wt.settings.onSelect.call(kt,Ct(t),nt+"-"+St(it+1,2)+"-"+St(N,2)+(lt?" "+St(t.getHours(),2)+":"+St(t.getMinutes(),2)+":"+St(t.getSeconds(),2):""),t)}wt.hide()}),z.on("click",".dp_time_controls_increase td, .dp_time_controls_decrease td",function(){var t,e=0<zt(this).parent(".dp_time_controls_increase").length,s=zt(this).attr("class").match(/dp\_time\_([^\s]+)/i),i=zt(".dp_time_segments .dp_time_"+s[1]+("ampm"!==s[1]?"s":""),ct),n=i.text().toLowerCase(),a=lt[s[1]+("ampm"!==s[1]?"s":"")],r=zt.inArray("ampm"!==s[1]?parseInt(n,10):n,a),o=-1===r?0:e?r+1>=a.length?0:r+1:r-1<0?a.length-1:r-1;"hour"===s[1]?X=a[o]:"minute"===s[1]?K=a[o]:"second"===s[1]?tt=a[o]:et=a[o],!N&&wt.settings.start_date&&(t=Dt(wt.settings.start_date))&&(N=t.getDate()),N||(N=W),i.text(St(a[o],2).toUpperCase()),Zt(nt,it,N)}),wt.settings.always_visible instanceof jQuery||(zt(document).on("touchmove.Zebra_DatePicker_"+bt,function(){ft=!0}),zt(document).on("mousedown.Zebra_DatePicker_"+bt+" touchend.Zebra_DatePicker_"+bt,function(t){if("touchend"===t.type&&ft)return ft=!(yt=!0);ft=!1,z.hasClass("dp_hidden")||(!wt.settings.open_icon_only||!wt.icon||zt(t.target).get(0)===wt.icon.get(0))&&(wt.settings.open_icon_only||zt(t.target).get(0)===kt.get(0)||wt.icon&&zt(t.target).get(0)===wt.icon.get(0))||0!==zt(t.target).parents().filter(".Zebra_DatePicker").length||wt.hide(!0)}),zt(document).on("keyup.Zebra_DatePicker_"+bt,function(t){z.hasClass("dp_hidden")||27!==t.which||wt.hide()})),Ft()},Dt=function(t){if(t+="",""!==zt.trim(t)){var e,s,i=w(wt.settings.format),n=["d","D","j","l","N","S","w","F","m","M","n","Y","y","G","g","H","h","i","s","a","A"],a=[],r=[],o=null,d=null;for(s=0;s<n.length;s++)-1<(o=i.indexOf(n[s]))&&a.push({character:n[s],position:o});if(a.sort(function(t,e){return t.position-e.position}),zt.each(a,function(t,e){switch(e.character){case"d":r.push("0[1-9]|[12][0-9]|3[01]");break;case"D":r.push("[a-z]{3}");break;case"j":r.push("[1-9]|[12][0-9]|3[01]");break;case"l":r.push("[a-zÀ-ɏ]+");break;case"N":r.push("[1-7]");break;case"S":r.push("st|nd|rd|th");break;case"w":r.push("[0-6]");break;case"F":r.push("[a-z]+");break;case"m":r.push("0[1-9]|1[012]");break;case"M":r.push("[a-z]{3}");break;case"n":r.push("[1-9]|1[012]");break;case"Y":r.push("[0-9]{4}");break;case"y":r.push("[0-9]{2}");break;case"G":r.push("[1-9]|1[0-9]|2[0123]");break;case"g":r.push("[0-9]|1[012]");break;case"H":r.push("0[0-9]|1[0-9]|2[0123]");break;case"h":r.push("0[0-9]|1[012]");break;case"i":case"s":r.push("0[0-9]|[12345][0-9]");break;case"a":r.push("am|pm");break;case"A":r.push("AM|PM")}}),r.length&&(a.reverse(),zt.each(a,function(t,e){i=i.replace(e.character,"("+r[r.length-t-1]+")")}),r=new RegExp("^"+i+"$","ig"),d=r.exec(t))){var c,l,g=new Date,_=1,h=g.getMonth()+1,p=g.getFullYear(),u=g.getHours(),m=g.getMinutes(),f=g.getSeconds(),b=["domingo","Segunda-feira","terça","Quarta-feira","Quinta-feira","Sexta-feira","sábado"],y=["janeiro","fevereiro","Março","abril","Maio","Junho","Julho","agosto","setembro","Outubro","novembro","dezembro"],v=!0;if(a.reverse(),zt.each(a,function(s,i){if(!v)return!0;switch(i.character){case"m":case"n":h=xt(d[s+1]);break;case"d":case"j":_=xt(d[s+1]);break;case"D":case"l":case"F":case"M":l="D"===i.character||"l"===i.character?wt.settings.days:wt.settings.months,v=!1,zt.each(l,function(t,e){if(v)return!0;if(d[s+1].toLowerCase()===e.substring(0,"D"===i.character||"M"===i.character?3:e.length).toLowerCase()){switch(i.character){case"D":d[s+1]=b[t].substring(0,3);break;case"l":d[s+1]=b[t];break;case"F":d[s+1]=y[t],h=t+1;break;case"M":d[s+1]=y[t].substring(0,3),h=t+1}v=!0}});break;case"Y":p=xt(d[s+1]);break;case"y":p="19"+xt(d[s+1]);break;case"G":case"H":case"g":case"h":u=xt(d[s+1]);break;case"i":m=xt(d[s+1]);break;case"s":f=xt(d[s+1]);break;case"a":case"A":c=d[s+1].toLowerCase()}}),v&&(e=new Date(p,(h||1)-1,_||1,u+("pm"===c&&12!==u?12:"am"===c&&12===u?-12:0),m,f)).getFullYear()===p&&e.getDate()===(_||1)&&e.getMonth()===(h||1)-1)return e}return!1}},At=function(t){"firefox"===It.name?t.css("MozUserSelect","none"):"explorer"===It.name?zt(document).on("selectstart",t,function(){return!1}):t.mousedown(function(){return!1})},w=function(t){return t.replace(/([-.,*+?^${}()|[\]\/\\])/g,"\\$1")},Ct=function(t){var e,s,i="",n=t.getDate(),a=t.getDay(),r=wt.settings.days[a],o=t.getMonth()+1,d=wt.settings.months[o-1],c=t.getFullYear()+"",l=t.getHours(),g=l%12==0?12:l%12,_=t.getMinutes(),h=t.getSeconds(),p=12<=l?"pm":"am";for(e=0;e<wt.settings.format.length;e++)switch(s=wt.settings.format.charAt(e)){case"y":c=c.substr(2);case"Y":i+=c;break;case"m":o=St(o,2);case"n":i+=o;break;case"M":d=zt.isArray(wt.settings.months_abbr)&&void 0!==wt.settings.months_abbr[o-1]?wt.settings.months_abbr[o-1]:wt.settings.months[o-1].substr(0,3);case"F":i+=d;break;case"d":n=St(n,2);case"j":i+=n;break;case"D":r=zt.isArray(wt.settings.days_abbr)&&void 0!==wt.settings.days_abbr[a]?wt.settings.days_abbr[a]:wt.settings.days[a].substr(0,3);case"l":i+=r;break;case"N":a++;case"w":i+=a;break;case"S":i+=n%10==1&&11!==n?"st":n%10==2&&12!==n?"nd":n%10==3&&13!==n?"rd":"th";break;case"g":i+=g;break;case"h":i+=St(g,2);break;case"G":i+=l;break;case"H":i+=St(l,2);break;case"i":i+=St(_,2);break;case"s":i+=St(h,2);break;case"a":i+=p;break;case"A":i+=p.toUpperCase();break;default:i+=s}return i},n=function(){var t,e,s,i,n,a,r,o,d,c,l,g,_=new Date(nt,it+1,0).getDate(),h=new Date(nt,it,1).getDay(),p=new Date(nt,it,0).getDate(),u=h-wt.settings.first_day_of_week;for(u=u<0?7+u:u,y(wt.settings.header_captions.days),e="<tr>",wt.settings.show_week_number&&(e+="<th>"+wt.settings.show_week_number+"</th>"),t=0;t<7;t++)s=(wt.settings.first_day_of_week+(wt.settings.rtl?6-t:t))%7,e+="<th>"+(zt.isArray(wt.settings.days_abbr)&&void 0!==wt.settings.days_abbr[s]?wt.settings.days_abbr[s]:wt.settings.days[s].substr(0,2))+"</th>";for(e+="</tr><tr>",t=0;t<42;t++)g=wt.settings.rtl?6-t%7*2:0,0<t&&t%7==0&&(e+="</tr><tr>"),t%7==0&&wt.settings.show_week_number&&(e+="<th>"+b(new Date(nt,it,t-u+1))+"</th>"),s=g+(t-u+1),wt.settings.select_other_months&&(t<u||_<s)&&(n=(i=new Date(nt,it,s)).getFullYear(),a=i.getMonth(),r=i.getDate(),i=n+St(a+1,2)+St(r,2)),o=(wt.settings.first_day_of_week+t)%7,l=-1<zt.inArray(o,wt.settings.weekend_days),wt.settings.rtl&&s<1||!wt.settings.rtl&&t<u?e+='<td class="dp_not_in_month '+(l?"dp_weekend ":"")+(wt.settings.select_other_months&&!Mt(n,a,r)?"date_"+i:"dp_disabled")+'">'+(wt.settings.select_other_months||wt.settings.show_other_months?St(g+p-u+t+1,wt.settings.zero_pad?2:0):"&nbsp;")+"</td>":_<s?e+='<td class="dp_not_in_month '+(l?"dp_weekend ":"")+(wt.settings.select_other_months&&!Mt(n,a,r)?"date_"+i:"dp_disabled")+'">'+(wt.settings.select_other_months||wt.settings.show_other_months?St(s-_,wt.settings.zero_pad?2:0):"&nbsp;")+"</td>":(d="",c=f(nt,it,s),l&&(d=" dp_weekend"),it===x&&nt===Y&&S===s&&(d+=" dp_current"),""!==c&&(d+=" "+c),it===H&&nt===O&&N===s&&(d+=" dp_selected"),Mt(nt,it,s)&&(d+=" dp_disabled"),e+="<td"+(""!==d?' class="'+zt.trim(d)+'"':"")+">"+((wt.settings.zero_pad?St(s,2):s)||"&nbsp;")+"</td>");e+="</tr>",j.html(zt(e)),wt.settings.always_visible&&(m=zt("td:not(.dp_disabled)",j)),j.show()},f=function(s,i,n){var a,t,r;for(t in void 0!==i&&(i+=1),I)if(a=I[t],r=!1,zt.isArray(ut[a])&&zt.each(ut[a],function(){if(!r){var t,e=this;if((-1<zt.inArray(s,e[2])||-1<zt.inArray("*",e[2]))&&(void 0!==i&&-1<zt.inArray(i,e[1])||-1<zt.inArray("*",e[1]))&&(void 0!==n&&-1<zt.inArray(n,e[0])||-1<zt.inArray("*",e[0]))){if(-1<zt.inArray("*",e[3]))return r=a;if(t=new Date(s,i-1,n).getDay(),-1<zt.inArray(t,e[3]))return r=a}}}),r)return r;return r||""},b=function(t){var e,s,i,n,a,r,o,d=t.getFullYear(),c=t.getMonth()+1,l=t.getDate();return c<3?(i=(s=((e=d-1)/4|0)-(e/100|0)+(e/400|0))-(((e-1)/4|0)-((e-1)/100|0)+((e-1)/400|0)),n=0,a=l-1+31*(c-1)):(n=(i=(s=((e=d)/4|0)-(e/100|0)+(e/400|0))-(((e-1)/4|0)-((e-1)/100|0)+((e-1)/400|0)))+1,a=l+((153*(c-3)+2)/5|0)+58+i),(o=a+3-(l=(a+(r=(e+s)%7)-n)%7))<0?53-((r-i)/5|0):364+i<o?1:1+(o/7|0)},l=function(t){var e,s;if("explorer"===It.name&&6===It.version)switch(i||(e=xt(z.css("zIndex"))-1,i=zt("<iframe>",{src:'javascript:document.write("")',scrolling:"no",frameborder:0,css:{zIndex:e,position:"absolute",top:-1e3,left:-1e3,width:z.outerWidth(),height:z.outerHeight(),filter:"progid:DXImageTransform.Microsoft.Alpha(opacity=0)",display:"none"}}),zt("body").append(i)),t){case"hide":i.hide();break;default:s=z.offset(),i.css({top:s.top,left:s.left,display:"block"})}},Mt=function(s,i,n){var t,e,a,r;if(!(void 0!==s&&!isNaN(s)||void 0!==i&&!isNaN(i)||void 0!==n&&!isNaN(n)))return!1;if(s<1e3)return!0;if(zt.isArray(wt.settings.direction)||0!==xt(wt.settings.direction)){if(8===(e=((t=xt(o(s,void 0!==i?St(i,2):"",void 0!==n?St(n,2):"")))+"").length)&&(void 0!==ot&&t<xt(o(E,St(B,2),St(W,2)))||void 0!==R&&t>xt(o($,St(V,2),St(U,2)))))return!0;if(6===e&&(void 0!==ot&&t<xt(o(E,St(B,2)))||void 0!==R&&t>xt(o($,St(V,2)))))return!0;if(4===e&&(void 0!==ot&&t<E||void 0!==R&&$<t))return!0}return void 0!==i&&(i+=1),r=a=!1,zt.isArray(L)&&L.length&&zt.each(L,function(){if(!a){var t,e=this;if((-1<zt.inArray(s,e[2])||-1<zt.inArray("*",e[2]))&&(void 0!==i&&-1<zt.inArray(i,e[1])||-1<zt.inArray("*",e[1]))&&(void 0!==n&&-1<zt.inArray(n,e[0])||-1<zt.inArray("*",e[0]))){if(-1<zt.inArray("*",e[3]))return a=!0;if(t=new Date(s,i-1,n).getDay(),-1<zt.inArray(t,e[3]))return a=!0}}}),T&&zt.each(T,function(){if(!r){var t,e=this;if((-1<zt.inArray(s,e[2])||-1<zt.inArray("*",e[2]))&&(r=!0,void 0!==i))if(r=!0,-1<zt.inArray(i,e[1])||-1<zt.inArray("*",e[1])){if(void 0!==n)if(r=!0,-1<zt.inArray(n,e[0])||-1<zt.inArray("*",e[0])){if(-1<zt.inArray("*",e[3]))return r=!0;if(t=new Date(s,i-1,n).getDay(),-1<zt.inArray(t,e[3]))return r=!0;r=!1}else r=!1}else r=!1}}),(!T||!r)&&!(!L||!a)},Pt=function(t){return(t+"").match(/^\-?[0-9]+$/)},y=function(t){!isNaN(parseFloat(it))&&isFinite(it)&&(t=t.replace(/\bm\b|\bn\b|\bF\b|\bM\b/,function(t){switch(t){case"m":return St(it+1,2);case"n":return it+1;case"F":return wt.settings.months[it];case"M":return zt.isArray(wt.settings.months_abbr)&&void 0!==wt.settings.months_abbr[it]?wt.settings.months_abbr[it]:wt.settings.months[it].substr(0,3);default:return t}})),!isNaN(parseFloat(nt))&&isFinite(nt)&&(t=t.replace(/\bY\b/,nt).replace(/\by\b/,(nt+"").substr(2)).replace(/\bY1\b/i,nt-7).replace(/\bY2\b/i,nt+4)),zt(".dp_caption",J).html(t)},Ft=function(){var t,e,s,i;""===j.text()||"days"===_t?(""===j.text()?(wt.settings.always_visible instanceof jQuery||z.css("left",-1e3),z.removeClass("hidden"),n(),t=void 0!==j[0].getBoundingClientRect&&void 0!==j[0].getBoundingClientRect().height?j[0].getBoundingClientRect().height:j.outerHeight(!0),q.css("height",t),gt.css("height",t),ct.css("height",t+J.outerHeight(!0)),z.css("width",z.outerWidth()),z.addClass("dp_hidden")):n(),J.show(),q.hide(),gt.hide(),ct.hide(),st.hide(),Z.hide(),lt&&st.show().removeClass("dp_calendar")):"months"===_t?(!function(){y(wt.settings.header_captions.months);var t,e,s,i="<tr>";for(t=0;t<12;t++)0<t&&t%3==0&&(i+="</tr><tr>"),e="dp_month_"+(s=wt.settings.rtl?2+t-t%3*2:t),Mt(nt,s)?e+=" dp_disabled":!1!==H&&H===s&&nt===O?e+=" dp_selected":x===s&&Y===nt&&(e+=" dp_current"),i+='<td class="'+zt.trim(e)+'">'+(zt.isArray(wt.settings.months_abbr)&&void 0!==wt.settings.months_abbr[s]?wt.settings.months_abbr[s]:wt.settings.months[s].substr(0,3))+"</td>";i+="</tr>",q.html(zt(i)),wt.settings.always_visible&&(d=zt("td:not(.dp_disabled)",q)),q.show()}(),j.hide(),gt.hide(),ct.hide(),st.hide(),Z.hide()):"years"===_t?(!function(){y(wt.settings.header_captions.years);var t,e,s,i="<tr>";for(t=0;t<12;t++)0<t&&t%3==0&&(i+="</tr><tr>"),s=wt.settings.rtl?2+t-t%3*2:t,e="",Mt(nt-7+s)?e+=" dp_disabled":O&&O===nt-7+s?e+=" dp_selected":Y===nt-7+s&&(e+=" dp_current"),i+="<td"+(""!==zt.trim(e)?' class="'+zt.trim(e)+'"':"")+">"+(nt-7+s)+"</td>";i+="</tr>",gt.html(zt(i)),wt.settings.always_visible&&(c=zt("td:not(.dp_disabled)",gt)),gt.show()}(),j.hide(),q.hide(),ct.hide(),st.hide(),Z.hide()):"time"===_t&&(i=lt.hours&&lt.minutes&&lt.seconds&&lt.ampm,s='<tr class="dp_time_controls_increase'+(i?" dp_time_controls_condensed":"")+'">'+(wt.settings.rtl&&lt.ampm?'<td class="dp_time_ampm dp_time_control">'+wt.settings.navigation[2]+"</td>":"")+(lt.hours?'<td class="dp_time_hour dp_time_control">'+wt.settings.navigation[2]+"</td>":"")+(lt.minutes?'<td class="dp_time_minute dp_time_control">'+wt.settings.navigation[2]+"</td>":"")+(lt.seconds?'<td class="dp_time_second dp_time_control">'+wt.settings.navigation[2]+"</td>":"")+(!wt.settings.rtl&&lt.ampm?'<td class="dp_time_ampm dp_time_control">'+wt.settings.navigation[2]+"</td>":"")+"</tr>",s+='<tr class="dp_time_segments'+(i?" dp_time_controls_condensed":"")+'">',wt.settings.rtl&&lt.ampm&&(s+='<td class="dp_time_ampm dp_disabled'+(lt.hours||lt.minutes||lt.seconds?" dp_time_separator":"")+'"><div>'+et.toUpperCase()+"</div></td>"),lt.hours&&(s+='<td class="dp_time_hours dp_disabled'+(lt.minutes||lt.seconds||!wt.settings.rtl&&lt.ampm?" dp_time_separator":"")+'"><div>'+("h"===lt.hour_format||"H"===lt.hour_format?St(X,2):X)+"</div></td>"),lt.minutes&&(s+='<td class="dp_time_minutes dp_disabled'+(lt.seconds||!wt.settings.rtl&&lt.ampm?" dp_time_separator":"")+'"><div>'+St(K,2)+"</div></td>"),lt.seconds&&(s+='<td class="dp_time_seconds dp_disabled'+(!wt.settings.rtl&&lt.ampm?" dp_time_separator":"")+'"><div>'+St(tt,2)+"</div></td>"),!wt.settings.rtl&&lt.ampm&&(s+='<td class="dp_time_ampm dp_disabled">'+et.toUpperCase()+"</td>"),s+="</tr>",s+='<tr class="dp_time_controls_decrease'+(i?" dp_time_controls_condensed":"")+'">'+(wt.settings.rtl&&lt.ampm?'<td class="dp_time_ampm dp_time_control">'+wt.settings.navigation[3]+"</td>":"")+(lt.hours?'<td class="dp_time_hour dp_time_control">'+wt.settings.navigation[3]+"</td>":"")+(lt.minutes?'<td class="dp_time_minute dp_time_control">'+wt.settings.navigation[3]+"</td>":"")+(lt.seconds?'<td class="dp_time_second dp_time_control">'+wt.settings.navigation[3]+"</td>":"")+(!wt.settings.rtl&&lt.ampm?'<td class="dp_time_ampm dp_time_control">'+wt.settings.navigation[3]+"</td>":"")+"</tr>",ct.html(zt(s)),ct.show(),1===ht.length?(st.hide(),Z.show()):(st.show().addClass("dp_calendar"),""===kt.val()?Z.hide():Z.show()),J.hide(),j.hide(),q.hide(),gt.hide()),"time"!==_t&&wt.settings.onChange&&"function"==typeof wt.settings.onChange&&void 0!==_t&&((e="days"===_t?j.find("td:not(.dp_disabled)"):"months"===_t?q.find("td:not(.dp_disabled)"):gt.find("td:not(.dp_disabled)")).each(function(){var t;"days"===_t?zt(this).hasClass("dp_not_in_month")&&!zt(this).hasClass("dp_disabled")?(t=zt(this).attr("class").match(/date\_([0-9]{4})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])/),zt(this).data("date",t[1]+"-"+t[2]+"-"+t[3])):zt(this).data("date",nt+"-"+St(it+1,2)+"-"+St(xt(zt(this).text()),2)):"months"===_t?(t=zt(this).attr("class").match(/dp\_month\_([0-9]+)/),zt(this).data("date",nt+"-"+St(xt(t[1])+1,2))):zt(this).data("date",xt(zt(this).text()))}),wt.settings.onChange.call(kt,_t,e)),Q.show(),"time"===_t&&1<ht.length?(at.hide(),P.hide(),st.css("width",""===kt.val()?"100%":"50%")):(at.show(),P.show(),!0===wt.settings.show_clear_date||0===wt.settings.show_clear_date&&""!==kt.val()||wt.settings.always_visible&&!1!==wt.settings.show_clear_date?rt?(at.css("width","50%"),P.css("width","50%")):(at.hide(),P.css("width",-1<zt.inArray(ht,"time")?"50%":"100%")):(P.hide(),rt?at.css("width","100%"):(at.hide(),(!lt||"time"!==_t&&"days"!==_t)&&Q.hide())))},Zt=function(t,e,s,i,n){var a=new Date(t,e,s,lt&&lt.hours?X+(lt.ampm?"pm"===et&&12!==X?12:"am"===et&&12===X?-12:0:0):12,lt&&lt.minutes?K:0,lt&&lt.seconds?tt:0),r="days"===i?m:"months"===i?d:c,o=Ct(a);kt.val(o),(wt.settings.always_visible||lt)&&(H=a.getMonth(),it=a.getMonth(),O=a.getFullYear(),nt=a.getFullYear(),N=a.getDate(),n&&r&&(r.removeClass("dp_selected"),n.addClass("dp_selected"),"days"===i&&n.hasClass("dp_not_in_month")&&!n.hasClass("dp_disabled")&&wt.show())),lt?(_t="time",Ft()):(kt.focus(),wt.hide()),Yt(a),!lt&&wt.settings.onSelect&&"function"==typeof wt.settings.onSelect&&wt.settings.onSelect.call(kt,o,t+"-"+St(e+1,2)+"-"+St(s,2),a)},o=function(){var t,e="";for(t=0;t<arguments.length;t++)e+=arguments[t]+"";return e},St=function(t,e){for(t+="";t.length<e;)t="0"+t;return t},xt=function(t){return parseInt(t,10)},Yt=function(s){wt.settings.pair&&zt.each(wt.settings.pair,function(){var t,e=zt(this);e.data&&e.data("Zebra_DatePicker")?((t=e.data("Zebra_DatePicker")).update({reference_date:s,direction:0===t.settings.direction?1:t.settings.direction}),t.settings.always_visible&&t.show()):e.data("zdp_reference_date",s)})},It={init:function(){this.name=this.searchString(this.dataBrowser)||"",this.version=this.searchVersion(navigator.userAgent)||this.searchVersion(navigator.appVersion)||""},searchString:function(t){var e,s,i;for(e=0;e<t.length;e++)if(s=t[e].string,i=t[e].prop,this.versionSearchString=t[e].versionSearch||t[e].identity,s){if(-1!==s.indexOf(t[e].subString))return t[e].identity}else if(i)return t[e].identity},searchVersion:function(t){var e=t.indexOf(this.versionSearchString);if(-1!==e)return parseFloat(t.substring(e+this.versionSearchString.length+1))},dataBrowser:[{string:navigator.userAgent,subString:"Firefox",identity:"firefox"},{string:navigator.userAgent,subString:"MSIE",identity:"explorer",versionSearch:"MSIE"}]};wt.settings={},wt.clear_date=function(){zt(P).trigger("click")},wt.destroy=function(){void 0!==wt.icon&&(wt.icon.off("click.Zebra_DatePicker_"+bt),wt.icon.off("focus.Zebra_DatePicker_"+bt),wt.icon.off("keydown.Zebra_DatePicker_"+bt),wt.icon.remove()),z.off(),z.remove(),!wt.settings.show_icon||wt.settings.always_visible instanceof jQuery||kt.unwrap(),kt.off("blur.Zebra_DatePicker_"+bt),kt.off("click.Zebra_DatePicker_"+bt),kt.off("focus.Zebra_DatePicker_"+bt),kt.off("keydown.Zebra_DatePicker_"+bt),kt.off("mousedown.Zebra_DatePicker_"+bt),zt(document).off("keyup.Zebra_DatePicker_"+bt),zt(document).off("mousedown.Zebra_DatePicker_"+bt),zt(document).off("touchend.Zebra_DatePicker_"+bt),zt(window).off("resize.Zebra_DatePicker_"+bt),zt(window).off("orientationchange.Zebra_DatePicker_"+bt),kt.removeData("Zebra_DatePicker"),kt.attr("readonly",mt.readonly),kt.attr("style",mt.style?mt.style:""),kt.css("paddingLeft",mt.padding_left),kt.css("paddingRight",mt.padding_right)},wt.hide=function(t){z.hasClass("dp_hidden")||wt.settings.always_visible&&!t||(l("hide"),z.addClass("dp_hidden"),wt.settings.onClose&&"function"==typeof wt.settings.onClose&&wt.settings.onClose.call(kt))},wt.set_date=function(t){var e;"object"==typeof t&&t instanceof Date&&(t=Ct(t)),(e=Dt(t))&&!Mt(e.getFullYear(),e.getMonth(),e.getDate())&&(kt.val(t),Yt(e))},wt.show=function(){_t=wt.settings.view;var t,e=Dt(kt.val()||(wt.settings.start_date?wt.settings.start_date:""));if(e?(H=e.getMonth(),it=e.getMonth(),O=e.getFullYear(),nt=e.getFullYear(),N=e.getDate(),Mt(O,H,N)&&(wt.settings.strict&&kt.val(""),it=B,nt=E)):(it=B,nt=E),lt&&(t=e||new Date,X=t.getHours(),K=t.getMinutes(),tt=t.getSeconds(),et=12<=X?"pm":"am",lt.is12hour&&(X=X%12==0?12:X%12),zt.isArray(wt.settings.enabled_hours)&&-1===zt.inArray(X,wt.settings.enabled_hours)&&(X=wt.settings.enabled_hours[0]),zt.isArray(wt.settings.enabled_minutes)&&-1===zt.inArray(K,wt.settings.enabled_minutes)&&(K=wt.settings.enabled_minutes[0]),zt.isArray(wt.settings.enabled_seconds)&&-1===zt.inArray(tt,wt.settings.enabled_seconds)&&(tt=wt.settings.enabled_seconds[0])),Ft(),wt.settings.always_visible instanceof jQuery)z.removeClass("dp_hidden");else{if(wt.settings.container.is("body")){var s=z.outerWidth(),i=z.outerHeight(),n=(void 0!==G?G.offset().left+G.outerWidth(!0):kt.offset().left+kt.outerWidth(!0))+wt.settings.offset[0],a=(void 0!==G?G.offset().top:kt.offset().top)-i+wt.settings.offset[1],r=zt(window).width(),o=zt(window).height(),d=zt(window).scrollTop(),c=zt(window).scrollLeft();"below"===wt.settings.default_position&&(a=(void 0!==G?G.offset().top:kt.offset().top)+wt.settings.offset[1]),c+r<n+s&&(n=c+r-s),n<c&&(n=c),d+o<a+i&&(a=d+o-i),a<d&&(a=d),z.css({left:n,top:a})}else z.css({left:0,top:0});z.removeClass("dp_hidden"),l()}wt.settings.onOpen&&"function"==typeof wt.settings.onOpen&&wt.settings.onOpen.call(kt)},wt.update=function(t){wt.original_direction&&(wt.original_direction=wt.direction),wt.settings=zt.extend(wt.settings,t),e(!0)},It.init(),e()},zt.fn.Zebra_DatePicker=function(e){return this.each(function(){void 0!==zt(this).data("Zebra_DatePicker")&&zt(this).data("Zebra_DatePicker").destroy();var t=new zt.Zebra_DatePicker(this,e);zt(this).data("Zebra_DatePicker",t)})},zt.fn.Zebra_DatePicker.defaults={}});