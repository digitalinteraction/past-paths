function ScrollIndicator(scroll_canvas) {
	this.bar_height = 0;
	this.bar_margin = 0;
	this.last_y = 0;
	this.scroll_canvas = scroll_canvas;
	var colours = ["#FFF", "#FFF", "#e5e5e5", "#b2b2b2", "#7f7f7f", "#4c4c4c", "#63FF9B", "#63FF6B", "#7BFF63", "#BBFF63", "#DBFF63", "#FBFF63", "#FFD363", "#FFB363", "#FF8363", "#FF7363", "#FF6364"];
	
	var width_scale = d3.scale.linear().domain([0, 1]).range([0, 60]);

	this.add_bar = function(randomness){

		// this.scroll_canvas.append('rect')
		// 		// .attr('width', Math.floor(Math.random() * 150) + (150 * 0.2))
		// 		.attr('width', width_scale(Math.random() * 0.4) + 0.1)
		// 		.attr("fill", function(d) {
		// 			return "white";
		// 		  // return colours[Math.floor(Math.random() * colours.length) + 1];
		// 		  // return this.colours[Math.floor(Math.random() * randomness + 1) + randomness];
		// 		})
		// 		.attr('height', this.bar_height)
		// 		.attr('y', this.last_y)
		// 		.attr('x', function(d, i){
		// 			 // return d3.select('.scroll_indicator').attr('width') - (d3.select(this).attr('width') / 2);
		// 			 // return d3.select('.scroll_indicator').attr('width') - d3.select(this).attr('width');
		// 			 // return d3.select('.scroll_indicator').attr('width');
		// 			 // return width_scale(d3.select('.scroll_indicator').attr('width'));
		// 			 return width_scale(d3.select('.scroll_indicator').attr('width') - (d3.select(this).attr('width') / 2));
		// 		});

		this.scroll_canvas.append('rect')
		// .attr('width', Math.floor(Math.random() * 150) + (150 * 0.2))
		.attr('width', function(d) {
				// return Math.floor(Math.random() * ((randomness * 25)) + (randomness * 25) + 25);
				/*if(randomness == 0)
				{
					return width_scale((Math.random() * 0.2) + 0.1);
				}
				else if(randomness == 1)
				{
					return width_scale((Math.random() * 0.6) + 0.4);
				}
				else
				{
					return width_scale((Math.random() * 0.8) + 0.6);
				}*/

				return width_scale(1);
				// return 50;
		})
		.attr("fill", function(d) {
		  return colours[1];
		})
		.attr('height', function(){
			console.log(bar_height);
			return 1;
		})
		.attr('y', this.last_y)
		.attr('x', function(d, i){
			 // return d3.select('.scroll_indicator').attr('width') - (d3.select(this).attr('width') / 2);
			 // return width_scale( - width_scale(d3.select(this).attr('width')));
			 // return width_scale(d3.select('.scroll_indicator').attr('width') - (d3.select(this).attr('width') / 2));
			 return 0;
		});
	};

	this.remove_bar = function() {
		var bars = this.scroll_canvas.selectAll('rect')
								   	 .attr('y', function(d){
				    			   	 	return d3.select(this).attr('y') - (bar_height + bar_margin);
				    				 });
    	bars[0][0].remove();
	};
}

var scroll_indicator;

$(document).ready(function(){
	scroll_indicator = new ScrollIndicator(d3.select(".scroll_indicator").append("svg").attr('height', window.innerHeight));
	var lastScrollTop = 0;

	scroll_indicator.add_bar(0);

	console.log(scroll_indicator.bar_height);

	$(window).bind("scroll", function(e) {
			// check if the place we are at is greater than where we were before
			// if that's the case we must be going down
			if ($(this).scrollTop() > lastScrollTop){
				// check if we need to stop adding bars and shift stuff up
			    if(scroll_indicator.last_y > window.innerHeight)
			    {
			    	scroll_indicator.remove_bar();
			    }
			    else
			    {
			    	// shift  bars down
			    	scroll_indicator.last_y += scroll_indicator.bar_height + scroll_indicator.bar_margin;
			    }

				scroll_indicator.add_bar(1);
			}
		   
		   lastScrollTop = $(this).scrollTop();
	});

});

