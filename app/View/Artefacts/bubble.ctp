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
    z-index: 20;
    fill  : white;
    opacity: 0.5;
    cursor: pointer;
}

.highlighted {
  fill: red;
}

.node-label{
  font-size: 15pt;
  /*stroke:white;*/
  fill:white;
  z-index: 21;
}

.link {
  stroke: #fff;
  stroke-opacity: .6;
  z-index: 1;
}

.link-highlighted{
  stroke:red;
  stroke-width:10;
}

.bubble {
   cursor:move;
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

var container = svg.append("g")
                   .attr('class', 'container')
                   .attr('id', 'map_container')
                   .on("dragstart", function() {
                      alert();
                   });

var gnode = container.selectAll('g.gnode');

var link = container.selectAll(".link");

var scale;

var node_mouse_position;

var labels;

var root;

var force = d3.layout.force()
    .nodes(nodes)
    .links(links)
    .size([width, height])
    .gravity(0.5)
    .charge(-100000);
    // .charge(-)
    // .linkDistance(function(d) { 
    //     console.log(d);
    //     return d.source.radius + d.target.radius + d.value;
    //     // console.log(links[lookup[d.node_id]]);
    // });
    // .linkStrength(0)
    // .gravity(0.01)
    // .charge(-100);

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

var zoomListener = d3.behavior.zoom()
  .scaleExtent([0.4, 1.5])
  .on("zoom", zoomHandler);

function zoomHandler() {
 container.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}


zoomListener(svg);

var lookup = {};

var node_id_lookup = {};

d3.json("map_data", function(error, response) {

  console.log(response);

  for (var i = 0; i < response.nodes.length; i++) {
    nodes.push({node_id : response.nodes[i].node_id, radius : response.nodes[i].artefact_count+1, x : Math.random() * svg.attr("width"), y : Math.random() * svg.attr("height"), label : response.nodes[i].keyword, artefacts : []});
    lookup[response.nodes[i].node_id] = i;
  }


  for (var i = 0; i < response.links.length; i++) {
    if(lookup[response.links[i].source] && lookup[response.links[i].target])
    {
      links.push({
        'source': lookup[response.links[i].source], 
        'target': lookup[response.links[i].target],
        'value': Math.log(Math.floor(response.links[i].value))
      });
    }
  }

  // loading_circle = loading_circle.transition()
  //                             .duration(1500)
  //                             .ease('cubic-in-out')
  //                             .attr('r', 50)

                               // .each('end', repeat);

   draw();
});

// var random_nodes = 20;

// for (var i = 0; i < random_nodes; i++) {
//   nodes.push({node_id : i, radius : (Math.floor(Math.random() * (random_nodes * 6)) + 1), x : Math.random() * svg.attr("width"), y : Math.random() * svg.attr("height"), label : i + "", artefacts : []});
//   lookup[i] = i;
// }


// for (var i = 0; i < (random_nodes * 2); i++) {
//   var node_id = Math.floor(Math.random() * (random_nodes));
//   if(lookup[i] && lookup[node_id])
//   {
//     links.push({
//       'source': lookup[i], 
//       'target': lookup[node_id],
//       'value': Math.floor(Math.random() * (random_nodes))
//     });

//     // console.log(links[i]);

//     // if(!node_id_lookup[node_id])
//     // {
//     //   var link_array = [];
//     //   link_array.push(links[i]);
//     //   node_id_lookup[node_id] = link_array;
//     // }
//     // else
//     // {
//     //   // console.log(links[i]);
//     //   node_id_lookup[node_id].push(links[i]); 
//     // }
//   }
// }

// draw();

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
  freeze_panning = !freeze_panning;
}

// function getMoreData(){
//   d3.json("map_data", function(error, response) {
//     for (var i = 0; i < response.length; i++) {
//       nodes.push({node_id : response[i].node_id, radius : response[i].artefact_count+1, x : Math.random() * svg.attr("width"), y : Math.random() * svg.attr("height"), label : response[i].keyword, artefacts : []});
//       links.push({
//         'source': i, 
//         'target': Math.floor(Math.random() * (response.length)),
//         'value': Math.log(Math.floor(Math.random() * (response.length)))
//       });
//     }
//     draw();
//   });
// }

function draw(){
  scale = d3.scale.log()
                    .domain([1, d3.max(nodes, function(d) { return d.radius; })])
                    .range([0, 100]);

  link = link.data(links)
             .enter().append("line")
             .attr("class", "link")
             .style("stroke-width", function(d) { d.value; });
             // .style("stroke", '#fff');

  gnode = gnode.data(nodes)
                    .enter()
                    .append('svg:g')
                      .call(force.drag)
                      // .on("mousedown", function(){return tooltip.style("visibility", "visible");})
                      .on("mouseover", function(d, i){ 
                        // stop panning
                        freeze_panning = true;
                        // getArtefactsByNodeId(d, d3.mouse(this));

                        // highlight paths and nodes connected
                        link.classed('link-highlighted', function(l){
                          console.log(l);
                          if(l.source.node_id == d.node_id || l.target.node_id == d.node_id)
                          {
                            return true;
                          }
                          else
                          {
                            return false;
                          }
                        });

                        return tooltip.style("visibility", "visible");
                      })
                      .on("mousemove", function(d){
                        return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");
                      })
                      .on("mouseout", function(){ 
                        freeze_panning = false; 
                        link.classed('link-highlighted', false);
                        return tooltip.style("visibility", "hidden");
                      })
                      .on("click", function(d, i){ 
                        // d3.select(this).transition()
                          // .ease("linear")
                          // .duration(200)
                          // .attr('fill', function(d, i){ console.log(d3.select(this).style('fill')); });
                          // d.attr("class", "highlighted");
                          // d3.select(this).select("circle").classed('highlighted');
                          // d3.select(this).select("circle").classed('highlighted', true);
                          if(d3.select(this).select("circle").classed('highlighted'))
                          {
                            d3.select(this).select("circle").classed('highlighted', false);
                          }
                          else
                          {
                            d3.select(this).select("circle").classed('highlighted', true);
                          }
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

    // force.on("tick", function() {
    //       gnode.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
    //     });

  force.on("tick", tick);
    
    force.start();

    // pan(0);
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

  // console.log(node);

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