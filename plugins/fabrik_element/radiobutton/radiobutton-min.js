/*! Fabrik */

define(["jquery","fab/elementlist"],function(t,e){return window.FbRadio=new Class({Extends:e,options:{btnGroup:!0},type:"radio",initialize:function(t,e){this.setPlugin("fabrikradiobutton"),this.parent(t,e),this.btnGroup()},btnGroup:function(){if(this.options.btnGroup){this.btnGroupRelay();var t=this.getContainer();t&&(t.getElements(".radio.btn-group label").addClass("btn"),t.getElements(".btn-group input[checked]").each(function(t){var e,n=t.getParent("label");"null"===typeOf(n)&&(n=t.getNext()),""===(e=t.get("value"))?n.addClass("active btn-primary"):"0"===e?n.addClass("active btn-danger"):n.addClass("active btn-success")}))}},btnGroupRelay:function(){var t=this.getContainer();t&&(t.getElements(".radio.btn-group label").addClass("btn"),t.addEvent("click:relay(.btn-group label)",function(t,e){var n,i=e.get("for");""!==i&&(n=document.id(i)),"null"===typeOf(n)&&(n=e.getElement("input")),this.setButtonGroupCSS(n)}.bind(this)))},setButtonGroupCSS:function(t){var e;""!==t.id&&(e=document.getElement("label[for="+t.id+"]")),"null"===typeOf(e)&&(e=t.getParent("label.btn"));var n=t.get("value"),i=parseInt(t.get("fabchecked"),10);t.get("checked")&&1!==i||(e&&(e.getParent(".btn-group").getElements("label").removeClass("active").removeClass("btn-success").removeClass("btn-danger").removeClass("btn-primary"),""===n?e.addClass("active btn-primary"):0===n.toInt()?e.addClass("active btn-danger"):e.addClass("active btn-success")),t.set("checked",!0),"null"===typeOf(i)&&t.set("fabchecked",1))},watchAddToggle:function(){var t=this.getContainer(),e=t.getElement("div.addoption"),n=t.getElement(".toggle-addoption");if(this.mySlider){var i=e.clone(),a=t.getElement(".fabrikElement");e.getParent().destroy(),a.adopt(i),(e=t.getElement("div.addoption")).setStyle("margin",0)}this.mySlider=new Fx.Slide(e,{duration:500}),this.mySlider.hide(),n.addEvent("click",function(t){t.stop(),this.mySlider.toggle()}.bind(this))},getValue:function(){if(!this.options.editable)return this.options.value;var e="";return this._getSubElements().each(function(t){return t.checked?e=t.get("value"):null}),e},setValue:function(e){this.options.editable&&this._getSubElements().each(function(t){t.value===e?t.set("checked",!0):t.set("checked",!1)})},update:function(e){if("array"===typeOf(e)&&(e=e.shift()),this.setValue(e),!this.options.editable)return""===e?void(this.element.innerHTML=""):void(this.element.innerHTML=$H(this.options.data).get(e));this.options.btnGroup&&this._getSubElements().each(function(t){t.value===e&&this.setButtonGroupCSS(t)}.bind(this))},cloned:function(t){!0===this.options.allowadd&&!1!==this.options.editable&&(this.watchAddToggle(),this.watchAdd()),this._getSubElements().each(function(t,e){t.id=this.options.element+"_input_"+e;var n=t.getParent("label");n&&(n.htmlFor=t.id)}.bind(this)),this.parent(t),this.btnGroup()},getChangeEvent:function(){return this.options.changeEvent},eventDelegate:function(){var t="input[type="+this.type+"][name^="+this.options.fullName+"]";return t+=", [class*=fb_el_"+this.options.fullName+"] .fabrikElement label"}}),window.FbRadio});