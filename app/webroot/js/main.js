var artefacts = {};


var width = window.innerWidth,
    height = window.innerHeight;

var d3lines = d3.svg.line()
		  .x(function(d){return d.x;})
		  .y(function(d){return d.y;})
		  .interpolate("linear");

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height)
    // .attr('preserveAspectRatio','xMinYMin')
    // .attr('viewBox','0 0 '+Math.min(width,height)+' '+Math.min(width,height))
  	.append("g")
    .call(d3.behavior.zoom().scaleExtent([1, 8]).on("zoom", zoom))
  	.append("g");

var drag = d3.behavior.drag()
    .origin(function(d) { return d; })
    .on("dragstart", dragstarted)
    .on("drag", dragged)
    .on("dragend", dragended);


// get the data
$(document).ready(function(){
	$('#q').keypress(function(e){
		if(e.which == 13){
			$.ajax({
				type: "GET",
				url: "get_artefacts/" + $('#q').val(),
				data: { }
			}).done(function(results) {
				// draw the results
				results = $.parseJSON(results);
				artefacts = results;
				draw_graph();
		  	});
		}
	});
});


function draw_graph(){
	var randomX = d3.random.normal(width / 2, 150),
    randomY = d3.random.normal(height / 2, 150);

	var data = d3.range(artefacts.length).map(function(nodeIndex) {
	  return {
	    x : randomX(),
	    y : randomY()
	  };
	});

	svg.append("rect")
	    .attr("class", "overlay")
	    .attr("width", width)
	    .attr("height", height);


	svg.append('svg:path')
	   .attr('d', d3lines(data))
	   .style("stroke-width", 3)
	   .style("stroke", "black")
	   .style("fill", "none");

    var circles = svg.selectAll(".circle")
	    .data(data)
	  	.enter().append("g").attr('class','circle')
	  	.attr('transform', 'translate(0,0)');

	//svg.selectAll("circle")
	 //   .data(data)
	 // 	.enter()
	 circles.append("circle")
	    .attr("r", 15)
	    .attr("id", function(d, i) { return i; })
	    .attr("cx", function(d) { return d.x;})
	    .attr("cy", function(d) { return d.y;})
	    // .attr("transform", function(d) { return "translate(" + d.cx + "," + d.cy +")"; })
	    .on('click', function(node, i){console.log(artefacts[i])})
	    .attr("fill", function(d, i) { return "url(#bg-" + i + ")" })
	    //.attr("fill", function(d, i) { return "#f90" })
	    // .style("opacity", .5)
	    .call(drag);

	circles.append("svg:defs")
	    .append('svg:pattern')
		.attr('id', function(d, i){ return "bg-" + i;})
		.attr('patternUnits', 'userSpaceOnUse')
		.attr('width', '30')
		.attr('height', '30')
		.append('svg:image')
		.attr('x', '0')
		.attr('y', '0')
		.attr('width', 30)
		.attr('height', 30)
	    .attr('xlink:href', function(d, i){ console.log(artefacts[i]); return artefacts[i].image[0].replace(/width=[0-9]+/,'width=30'); });
		//.attr('xlink:href', 'http://www3.canisius.edu/~grandem/animalshabitats/JungleAnimalsBorder.jpg');


}

d3.select(window).on('resize', resize);

function zoom() {
  svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}

function resize() {
	console.log('changed');

	d3.select('svg').attr('width', window.innerWidth).attr('height', window.innerHeight);
}

function dragstarted(d) {
  
  d3.event.sourceEvent.stopPropagation();
  d3.select(this).classed("dragging", true);
}

function dragged(d) {
	console.log(d3.event);
  d3.select(this).attr("cx", d.x = d3.event.x).attr("cy", d.y = d3.event.y);
}

function dragended(d) {
  d3.select(this).classed("dragging", false);
}





// function resize() {
// 	console.log('chabnged');
// }

// d3.select(window).on('resize', resize);

// var svgContainer = d3.select('#svg');

// console.log(svgContainer);

// var circle = svgContainer.append('circle')
// 						 .attr('cx', 300)
// 						 .attr('cy', 300)
// 						 .attr('r', 300)
// 						 .append('pattern')
// 						 .attr('patternUnits', 'userSpaceOnUse')
// 						 .attr('fill', 'https://avatars3.githubusercontent.com/u/9898?s=140')
// 						 // .attr('x', 0)
// 						 // .attr('y', 0)
// 						 // .attr('width', 300)
// 						 // .attr('height', 300);



// circle.style('fill', 'steelblue');
// // circle.attr('r', function() { return Math.random() * 100; });


// var circleEnter = circle.enter().append('circle');
// circleEnter.attr('cy', 60);
// circleEnter.attr('cx', function(d, i) { return i * 100 + 30; });
// circleEnter.attr('r', function(d) { return Math.sqrt(d); });
