/*! Fabrik */

define(["jquery"],function(a){return new Class({Implements:[Options],options:{j3:!1},initialize:function(a,b,c){this.el=document.id(a),this.fields=b,this.setOptions(c),this.filters=[],this.counter=0},addHeadings:function(){new Element("thead").adopt(new Element("tr",{id:"filterTh",class:"title"}).adopt(new Element("th").set("text",Joomla.JText._("COM_FABRIK_JOIN")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_FIELD")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_CONDITION")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_VALUE")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_TYPE")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_APPLY_FILTER_TO")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_GROUPED")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_DELETE")))).inject(document.id("filterContainer"),"before")},deleteFilterOption:function(a){this.counter--;var b,c;if(a.stop(),this.options.j3){a.target.id.replace("filterContainer-del-","").toInt();c=a.target.getParent("tr"),b=a.target.getParent("table")}else c=a.target.getParent("tr"),b=a.target.getParent("table");0===this.counter&&b.hide(),this.options.j3?(c.getElements("input, select, textarea").dispose(),c.hide()):c.dispose()},_makeSel:function(a,b,c,d,e){var f=[];return e=!0===e,e&&f.push(new Element("option",{value:""}).set("text",Joomla.JText._("COM_FABRIK_PLEASE_SELECT"))),c.each(function(a){a.value===d?f.push(new Element("option",{value:a.value,selected:"selected"}).set("text",a.label)):f.push(new Element("option",{value:a.value}).set("text",a.label))}),new Element("select",{class:a+" input-medium",name:b}).adopt(f)},addFilterOption:function(a,b,c,d,e,f,g){var h,i,j,k,l,m,n;this.counter<=0&&(this.el.getParent("table").getElement("thead")||this.addHeadings()),a=a||"",b=b||"",c=c||"",d=d||"",e=e||"",g=g||"";var o=this.options.filterCondDd,p=new Element("tr");if(this.counter>0){var q={type:"radio",name:"jform[params][filter-grouped]["+this.counter+"]",value:"1"};q.checked="1"===g?"checked":"",l=new Element("label").set("text",Joomla.JText._("JYES")).adopt(new Element("input",q)),q={type:"radio",name:"jform[params][filter-grouped]["+this.counter+"]",value:"0"},q.checked="1"!==g?"checked":"",k=new Element("label").set("text",Joomla.JText._("JNO")).adopt(new Element("input",q))}0===this.counter?j=new Element("span").set("text","WHERE").adopt(new Element("input",{type:"hidden",id:"paramsfilter-join",class:"inputbox",name:"jform[params][filter-join][]",value:a})):("AND"===a?(h=new Element("option",{value:"AND",selected:"selected"}).set("text","AND"),i=new Element("option",{value:"OR"}).set("text","OR")):(h=new Element("option",{value:"AND"}).set("text","AND"),i=new Element("option",{value:"OR",selected:"selected"}).set("text","OR")),j=new Element("select",{id:"paramsfilter-join",class:"inputbox  input-medium",name:"jform[params][filter-join][]"}).adopt([h,i]));var r=new Element("td"),s=new Element("td");this.counter<=0?(r.appendChild(new Element("input",{type:"hidden",name:"jform[params][filter-grouped]["+this.counter+"]",value:"0"})),r.appendChild(new Element("span").set("text","n/a"))):(r.appendChild(k),r.appendChild(l)),s.appendChild(j);var t=new Element("td");t.innerHTML=this.fields;var u=new Element("td");u.innerHTML=o;var v=new Element("td"),w=new Element("td");w.innerHTML=this.options.filterAccess;var x=new Element("td"),y=new Element("textarea",{name:"jform[params][filter-value][]",cols:17,rows:4}).set("text",d);v.appendChild(y),v.appendChild(new Element("br"));var z=[{value:0,label:Joomla.JText._("COM_FABRIK_TEXT")},{value:1,label:Joomla.JText._("COM_FABRIK_EVAL")},{value:2,label:Joomla.JText._("COM_FABRIK_QUERY")},{value:3,label:Joomla.JText._("COM_FABRIK_NO_QUOTES")}],A=new Element("td").adopt(this._makeSel("inputbox elementtype","jform[params][filter-eval][]",z,f,!1)),B=this.el.id+"-del-"+this.counter,C=this.options.j3?"":Joomla.JText._("COM_FABRIK_DELETE"),D=this.options.j3?"btn btn-danger":"removeButton",E='<button id="'+B+'" class="'+D+'"><i class="icon-minus"></i> '+C+"</button>";if(x.set("html",E),p.appendChild(s),p.appendChild(t),p.appendChild(u),p.appendChild(v),p.appendChild(A),p.appendChild(w),p.appendChild(r),p.appendChild(x),this.el.appendChild(p),this.el.getParent("table").show(),document.id(B).addEvent("click",function(a){this.deleteFilterOption(a)}.bind(this)),document.id(this.el.id+"-del-"+this.counter).click=function(a){this.deleteFilterOption(a)}.bind(this),""!==a&&(n=Array.from(s.getElementsByTagName("SELECT")),n.length>=1))for(m=0;m<n[0].length;m++)n[0][m].value===a&&(n[0].options.selectedIndex=m);if(""!==b&&(n=Array.from(t.getElementsByTagName("SELECT")),n.length>=1))for(m=0;m<n[0].length;m++)n[0][m].value===b&&(n[0].options.selectedIndex=m);if(""!==c&&(n=Array.from(u.getElementsByTagName("SELECT")),n.length>=1))for(m=0;m<n[0].length;m++)n[0][m].value===c&&(n[0].options.selectedIndex=m);if(""!==e&&(n=Array.from(w.getElementsByTagName("SELECT")),n.length>=1))for(m=0;m<n[0].length;m++)n[0][m].value===e&&(n[0].options.selectedIndex=m);this.counter++}})});