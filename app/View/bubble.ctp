<!DOCTYPE html>
<meta charset="utf-8">
<head>
<style>

text {
  font: 10px sans-serif;
}

.artefact_tooltip{
  position: absolute;
  z-index: 100;
  visibility: hidden;
  font-size: 50pt;
}

.node{
    z-index: 2;
    /*fill  : #00cc00;*/
    opacity: 0.5;
}

.node-label{
  font-size: 15pt;
  /*stroke:white;*/
  fill:white;
}

.link {
  stroke: #999;
  stroke-opacity: .6;
  z-index: 1;
}

/*.fade-in {
  opacity: 1;
  transition:opacity 2s linear;
}*/

body{
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
<head>
<body>
<script src="http://d3js.org/d3.v3.min.js"></script>
</body>
</html>
<script>


var width = window.innerWidth,
    height = window.innerHeight;

var fill = d3.scale.category20();

var nodes = [];

var links = [];

var node;

var force = d3.layout.force();

var panning = false;

var panning_enabled = false;

var freeze_panning = false;

var dX=0, dY=0, oX = 0, oY = 0;
var largest_x = 0, smallest_x = window.width;
var largest_y = 0, smallest_y = window.height;
var pan_speed = 0.005;

var tooltip = d3.select("body")
  .append("div")
  .attr("class","artefact_tooltip")
  .text("");

var svg = d3.select("body").append("svg")
    .attr("width", window.innerWidth)
    .attr("height", window.innerHeight)
    .attr("class", "bubble");

var container = svg.append("g").attr('class', 'container').attr('id', 'map_container');

var gnode = container.selectAll('g.gnode');

var link = container.selectAll(".link");

var scale;

var node_mouse_position;

var labels;

var force = d3.layout.force()
    .nodes(nodes)
    .links(links)
    .size([width, height])
    .linkStrength(0.1)
    .gravity(0.01)
    .charge(-800);

// var loading_circle = svg.append('circle')
//                         .attr('fill', 'white')
//                         .attr('r', function(d, i) { console.log(d3.select(this).attr('r')); if(d3.select(this).attr('r') < 150){ return d3.select(this).attr('r') + 150 }else{ return d3.select(this).attr('r') - 150;} })
//                         .attr('cx', function() { return width / 2; })
//                         .attr('cy', function() { return height / 2; })
//                         .each('end', loading_repeat);

// function loading_repeat() {
//   loading_circle.transition()
//                 .attr('r', 0)
//                 .duration(500)
// }
//                         loading_circle = loading_circle.transition()
//                                                          .duration(1500)
//                                                          .ease('cubic-in-out')
//                                                          // .attr('r', function(d, i) { console.log(d3.select(this).attr('r')); if(d3.select(this).attr('r') < 150){ return d3.select(this).attr('r') + 150 }else{ return d3.select(this).attr('r') - 150;} })
//                                                          .attr('r', 50);
                                                         // .each("end", loading_repeat);


var response = [];

for(var i = 0; i < 50; i++)
{
	var random_node = {
		node_id : i,
		radius: Math.floor(Math.random() * (50)),
		label : i
	}
	response.push(random_node);
}

for (var i = 0; i < response.length; i++) {
	nodes.push({node_id : response[i].node_id, radius : , x : Math.random() * svg.attr("width"), y : Math.random() * svg.attr("height"), label : response[i].keyword, artefacts : []});
	links.push({
	  'source': i, 
	  'target': Math.floor(Math.random() * (response.length)),
	  // 'value': Math.log(Math.floor(Math.random() * (response.length)))
	  'value': 1
	});
}

// svg.style("opacity", 1e-6)
  // .transition()
    // .duration(2000)
    // .style("opacity", 0.5);

d3.select("body").on("mousedown", mousedown);




function tick(e) {

  // Push different nodes in different directions for clustering.
  // var k = 10 * e.alpha;
  // nodes.forEach(function(o, i) {
  //   o.y += i & 1 ? k : -k;
  //   o.x += i & 2 ? k : -k;
  // });

  gnode.attr("x", function(d) { return d.x; })
      .attr("y", function(d) { return d.y; });

  gnode.attr("transform", function(d, i) { return "translate(" + d.x + "," + d.y + ")"; });

  link.attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });
}

function mousedown() {
  // nodes.forEach(function(o, i) {
  //   o.x += (Math.random() - .5) * 40;
  //   o.y += (Math.random() - .5) * 40;
  // });
  // force.resume();

  freeze_panning = !freeze_panning;
}

function getMoreData(){
  d3.json("map_data", function(error, response) {
    for (var i = 0; i < response.length; i++) {
      nodes.push({node_id : response[i].node_id, radius : response[i].artefact_count+1, x : Math.random() * svg.attr("width"), y : Math.random() * svg.attr("height"), label : response[i].keyword, artefacts : []});
      links.push({
        'source': i, 
        'target': Math.floor(Math.random() * (response.length)),
        'value': Math.log(Math.floor(Math.random() * (response.length)))
      });
    }
    draw();
  });
}

function draw(){
  scale = d3.scale.log()
                    .domain([1, d3.max(nodes, function(d) { return d.radius; })])
                    .range([0, 100]);

  link = link.data(links)
             .enter().append("line")
             .attr("class", "link")
             .style("stroke-width", function(d) { Math.sqrt(d.value); });

  gnode = gnode.data(nodes)
                    .enter()
                    .append('svg:g')
                      .call(force.drag)
                      // .on("mousedown", function(){return tooltip.style("visibility", "visible");})
                      .on("mouseover", function(d, i){ 
                        // stop panning
                        freeze_panning = true;
                        getArtefactsByNodeId(d, d3.mouse(this));
                        return tooltip.style("visibility", "visible");
                      })
                      .on("mousemove", function(d){
                        getArtefactsByNodeId(d, d3.mouse(this));
                        return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");
                      })
                      .on("mouseout", function(){ freeze_panning = false; return tooltip.style("visibility", "hidden");})
                      .on("click", function(d, i){ 
                        d3.select(this).transition()
                          .ease("linear")
                          .duration(100)
                          // .attr('fill', function(d, i){ console.log(d3.select(this).style('fill')); });
                          .attr('fill', "white");
                      })
                    .classed('gnode', true);

  node = gnode.append("circle")
      .transition()
        .duration(function(d, i) { return Math.floor(Math.random() * 2000); })
        .attr("r", function(d) { return 0; })
          .transition(100)
          .ease("elastic")
            .attr("r", function(d) { return scale(d.radius); })
            .each("end", function(d, i) { /* console.log(d3.select('text').style('opacity',1)); */ })
      .attr("class", "node");


  labels = gnode.append("text")
                    .style("opacity", 0)
                    .transition()
                      .duration(5000)
                      .style("opacity", 1)
                    .attr('class', 'node-label')
                    .text(function(d) { return d.label.charAt(0).toUpperCase() + d.label.slice(1); })
                    .attr("cx", function(d) { return d.x; })
                    .attr("cy", function(d) { return d.y; })
                    .attr("text-anchor", "middle");

    force.on("tick", function() {
          gnode.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
        });
    
    force.start();

    pan(0);
}

function pan(){

  setTimeout(function() {

    if(!freeze_panning){

      oX = oX - dX * pan_speed;
      oY = oY - dY * pan_speed;

      // console.log(((prev_oX - oX) / 100));

      // console.log("dx :" + dX + " dy :" + dY + " oX :" + oX + " oY :" + oY);
      container.attr('transform', 'translate('+oX+','+oY+')');

    }

    pan();
  }, 10);
}

function resize() {
    width = window.innerWidth, height = window.innerHeight;
    svg.attr("width", width).attr("height", height);
    container.attr("width", width).attr("height", height);
    force.size([width, height]).resume();
}

function getArtefactsByNodeId(node, mousePos){

  console.log(node);

  var limit = 5;
  var max_radius = scale(node.radius * 2);
  var distance_from_center = Math.sqrt((Math.pow(mousePos[0], 2) + Math.pow(mousePos[1], 2)));

  var step_size = max_radius / limit;
  var artefact_index = Math.floor(distance_from_center / step_size);

  if(nodes[node.index].artefacts.length == 0)
  {
    d3.json("map_node_artefacts/" + node.node_id + "/" + limit , function(error, artefacts) {
      tooltip.html("<img src='../img/artefacts/medium/" + artefacts[artefact_index].lidoRecID + "/0.jpeg' />");
      nodes[node.index].artefacts.length = 0;
      nodes[node.index].artefacts = nodes[node.index].artefacts.concat(artefacts);
      // document.body.style.backgroundImage="url('../img/artefacts/large/" + artefacts[artefact_index].lidoRecID + "/0.jpeg')";
    });
  }
  else
  {
    tooltip.html("<img src='../img/artefacts/medium/" + nodes[node.index].artefacts[artefact_index].lidoRecID + "/0.jpeg' />");
    // document.body.style.backgroundImage="url('../img/artefacts/large/" + artefacts[artefact_index].lidoRecID + "/0.jpeg')";
  }
}

window.addEventListener('mousemove', function(e){ 
  nodes.forEach(function(o, i) {
    if(largest_x < o.x)
    {
      largest_x = o.x - window.innerWidth/2;
    }

    if(o.x < smallest_x)
    {
      smallest_x = o.x - window.innerWidth/2;
    }

    if(largest_y < o.y)
    {
      largest_y = o.y  - window.innerHeight/2;
    }

    if(o.y < smallest_y)
    {
      smallest_y = o.y - window.innerHeight/2; 
    }
  });

  dX = e.x - window.innerWidth/2; 
  dY = e.y - window.innerHeight/2; 

  // console.log("ex" + e.x);
  // console.log("ey" + e.y);
  // console.log(largest_x);
  // console.log(smallest_x);
  // console.log(dX);

  // if(dX > largest_x || dX < smallest_x)
  // {
  //   pan_speed = 0.05;
  // }

});

window.addEventListener('resize', resize); 

</script>