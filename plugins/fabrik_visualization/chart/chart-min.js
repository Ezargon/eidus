/*! Fabrik */

var fabrikGraph=new Class({Implements:[Options],options:{legend:!1,label:"",aChartKeys:{},axis_label:"",json:{},chartType:"barChart",xticks:[]},initialize:function(a,b,c){this.setOptions(c),this.el=a,this.json=b,this.render()},render:function(){switch(this.options.chartType){case"BarChart":this.graph=new Plotr.BarChart(this.el,this.options);break;case"PieChart":this.graph=new Plotr.PieChart(this.el,this.options);break;case"LineChart":this.graph=new Plotr.LineChart(this.el,this.options)}this.graph.addDataset(this.json),this.graph.render(),"1"===this.options.legend&&this.graph.addLegend(this.el)}});