var margin = {top: -5, right: -5, bottom: -5, left: -5},
    width = (window.innerWidth - 350),
    height = window.innerHeight;

d3.select(window).on('resize', resize);

var artefacts;

var mouse_position = [(width / 2), (height / 2)];

var zoom = d3.behavior.zoom()
    .scaleExtent([1, 10])
    .on("zoom", zoomed);

var svg = d3.select("#content").append("svg")
    .attr("id", "svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    // .append("g")
    // .attr("transform", "translate(" + margin.left + "," + margin.right + ")")
    // .call(zoom);

var container = svg.append("g");

var drag = d3.behavior.drag()
    .origin(function(d) { return d; })
    .on("dragstart", dragstarted)
    .on("drag", dragged)
    .on("dragend", dragended);

// var board = svg.append("rect")
// 	.attr("width", width)
//     .attr("height", height)
//     .attr("fill", "#000")
//     .attr("fill-opacity", .5)
//     .attr("mask", "url(#spotlight)");

var edges;
var node;

function draw_graph(data)
{
    svg.selectAll("*").remove();

	var defs = svg.append("defs");

	var filter = defs.append("filter")
	.attr("id", "light-filter");

	filter.append("feGaussianBlur")
	.attr("stdDeviation", 10);

	defs.append("mask")
	.attr("id", "circle-mask")
	.append("circle")
	.attr("r", 25)
	.attr("cx", 0)
	.attr("cy", 0)
	// .attr("width", 100)
	// .attr("height", 100)
	.attr("fill", "white");

	var mask = defs.append("mask")
		.attr("id", "spotlight")

	mask.append("rect")
		.attr("width", width)
	    .attr("height", height)
	    .attr("fill", "#fff")
	    .attr("fill-opacity", 1)
	    .attr("mask", "url(#spotlight)");

	var spotlight = mask
		.append("circle")
	    .attr("cx", mouse_position[0])
		.attr("cy", mouse_position[1])
		.attr("r", 125)
		.attr("fill", "grey")
		.attr("filter", "url(#light-filter)");

	 svg.on("mousemove", function() {
	    var m = d3.mouse(this);
	    // console.log("moved");
	    spotlight
	      .attr("cx", m[0])
	      .attr("cy", m[1]);
	  });

	  svg.on('mouseenter', function(){
	    svg.selectAll('circle').attr('opacity', 1.0);
	  });

	  svg.on('mouseleave', function(){
	    // svg.selectAll('circle').attr('opacity', 0.5);
	  });

    var nodes = [];
    var links = [];

    var sub_node_loop_counter = 0;
    var target = -1;

    for(var i=0; i < data.length; i++)
    {
        // console.log(i);
        nodes.push({index: i, name: "a name", artefact: data[i]});

        if(sub_node_loop_counter < 20)
        {
            if(target == -1)
            {
                target = Math.floor(Math.random() * data.length);
            }

            sub_node_loop_counter++;
        }
        else
        {
            sub_node_loop_counter = 0;
            target = -1;
            target = Math.floor(Math.random() * data.length);
        }

        links.push({source: i, target: target, value:1, type: Math.floor(Math.random() * 4)});
    }

    var force = d3.layout.force()
        .nodes(d3.values(nodes))
        .links(links)
        .size([width, height])
        .linkDistance(function(){ return Math.floor((Math.random() * 100) + 125); })
        .charge(-1000)
        // .gravity()
        // .theta(1)
        .on("tick", tick)
        .start();

    edges = svg.selectAll(".link")
                   .data(links)
                   .enter()
                   .append("g")
                   .attr("class", "link")
                   .append("line")
                   .style('stroke', function(d, i){ 
                        switch(d.type)
                        {
                            case 0:
                                return "#9fc7d1";
                                break;
                            case 1: 
                                return "#3c4a53";
                                break;
                            case 2:
                                return "#e87b5a";
                                break;
                            default:
                                return "#eec97b";
                                break;
                        }
                    })
                   .style("stroke-width", "3px")
                   // .style("stroke-dasharray", ("10, 8"));

    node = svg.selectAll(".node")
        .data(force.nodes())
        .enter().append("g")
        .on('click', function(d, i){
        	// console.log(d3.select(this));
        	// d3.select(this)
        	//   .transition()
        	//   .duration(500)
        	//   .attr("cx", 500);
        	$('#sidebar-content').fadeIn("fast");
        	$('#sidebar').animate({width:"350px"}, "fast");
        	$('.artefact-title').html(d.artefact.description);
        	// $('#artefact-image').attr("src","../app/webroot/img/artefacts/" + d.artefact.lidoRecID + "/0.jpeg");
        	$('.artefact-image').css("background-image",'url("../app/webroot/img/artefacts/' + d.artefact.lidoRecID + '/0.jpeg")');
        	$('.artefact-image').css("background-size", "cover");
        })
        .on('dblclick', function(){
        	$('#sidebar-content').fadeOut("fast");
        	$('#sidebar').animate({width:"0px"}, "fast");	
        })
        .attr("class", "node")
        .attr("data-id", function(d, i) { return i; })
        // .append("circle")
        // .attr("width", 200)
        // .attr("height", 200)
        // .attr("r", 60)
        .append("svg:image")
        .attr("xlink:href", function(d, i){ return "../app/webroot/img/artefacts/" + artefacts[i].lidoRecID + "/0.jpeg"; })
        .attr("width", 200)
        .attr("height", 200)
        .attr("x", -100)
        .attr("y", -100)
        .attr("mask", "url(#circle-mask)")
        .call(force.drag);

    // board.append()

    // add the text 
    // node.append("text")
    //     .attr("x", 12)
    //     .attr("dy", ".35em")
    //     .text(function(d) { return d.name; })
    //     .on('click', function(d){ console.log('kapow'); });


// var board = svg.append("rect")
// 	.attr("width", width)
//     .attr("height", height)
//     .attr("fill", "#000")
//     .attr("fill-opacity", .9)
//     .attr("mask", "url(#spotlight)")
//     .attr("class", "hideme");

}

function tick() {
    edges.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });
    node
        .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
}

function zoomed() {
  container.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}

function dragstarted(d) {
  d3.event.sourceEvent.stopPropagation();
  d3.select(this).classed("dragging", true);
}

function dragged(d) {
  d3.select(this).attr("cx", d.x = d3.event.x).attr("cy", d.y = d3.event.y);
}

function dragended(d) {
  d3.select(this).classed("dragging", false);
}

function resize() {
    console.log('changed');

    d3.select('svg').attr('width', window.innerWidth).attr('height', window.innerHeight);
}



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
            console.log(results);
            draw_graph(results);
        });
    }
});

// $.ajax({
//             type: "GET",
//             url: "get_artefacts/jug",
//             data: { }
//         }).done(function(results) {
//             // draw the results
//             results = $.parseJSON(results);
//             artefacts = results;
//             console.log(results);
//             draw_graph(results);
//         });
});

