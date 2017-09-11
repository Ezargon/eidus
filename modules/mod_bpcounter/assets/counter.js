/* 
	Name:			Counter
	Version:		1.0.0
	Description:	Script that animates number using jQuery
	Created on:		2016-02-29
	Author:			Artur Stępień  (artur.stepien@bestproject.pl)
	Author URI:		http://www.bestproject.pl
	Copyrights:		BestProject
	License:		GNU GPL 3.0 <http://www.gnu.org/licenses/>
	Requires:		jQuery
	How to use:		Provive an element with a number inside (eg. $('.number').animateCounter() )
*/

(function ($) {

	$.fn.animateCounter = function(){
		return this.each(function(){
			var $this = $(this);
			var text = $this.text().replace(',','.');
			var time = parseInt($this.data('time'));

			var i = text.length-1;
			for(i; i>=0; i--){
				if( !isNaN(parseFloat(text[i])) ) break;
			}
			var number = parseFloat(text.substring(0, i+1));
			var suffix = text.substring(i+1);
			
			$this.html('<span class="nr">'+number+'</span><span class="suffix">'+suffix+'</span>');
			
			$(this).find('.nr').each(function () {
				$(this).prop('Counter',0).animate({
					Counter: $(this).text()
				}, {
					duration: time,
					easing: 'swing',
					step: function (now) {
						$(this).text(Math.ceil(now));
					}
				});
			});
			
		});
	};

}(jQuery));