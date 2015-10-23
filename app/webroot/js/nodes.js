var width = window.innerWidth,
    height = window.innerHeight;

d3.select(window).on('resize', resize);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

// svg.append("svg:defs").selectAll("marker")
//   	.enter().append("svg:marker")
//     .attr("id", String)
//     .attr("viewBox", "0 -5 10 10")
//     .attr("refX", 15)
//     .attr("refY", -1.5)
//     .attr("markerWidth", 6)
//     .attr("markerHeight", 6)
//     .attr("orient", "auto")
//     .attr("d", "M0,-5L10,0L0,5");

var edges;
var node;

function draw_graph(data)
{
    svg.selectAll("*").remove();
    var nodes = [];
    var links = [];
    for(var i=0; i < data.length; i++)
    {
        // console.log(i);
        nodes.push({index: i, name: "a name", artefact: data[i]});
        links.push({source: i, target: Math.floor(Math.random() * data.length), value:1});
    }

    // console.log(nodes);

    // var nodes = [
    //     {index: 0,name:"andy"},
    //     {index: 1, name:"steve"},
    //     {index: 2, name:"bob"}
    // ];


    // var nodes_array = d3.range(100).map(function(i) {
    //   return {index: i};
    // });

    // var nodes_array = [{index: 1},{index:2}, {index:3}];



    var force = d3.layout.force()
        .nodes(d3.values(nodes))
        .links(links)
        .size([width, height])
        .linkDistance(100)
        .charge(-220)
        .on("tick", tick)
        .start();



    // add the links and the arrows
    // var path = svg.append("svg:g").selectAll("path")
    //     .data(force.links())
    //      .enter().append("svg:path")

    // var path = svg.append("svg:g").selectAll("path")
    //     .data(force.links())
    //     .enter().append("svg:path")

        // .attr("class", "link")
        // .attr("marker-end", "url(#end)");

    edges = svg.selectAll(".link")
                   .data(links)
                   .enter()
                   .append("g")
                   .attr("class", "link")
                   .append("line")
                   .style('stroke', 'black')
                   .style("stroke-width", "3px");


    // define the nodes
    node = svg.selectAll(".node")
        .data(force.nodes())
        .enter().append("g")
        .attr("class", "node")
        .append("circle")
        .attr("r", 5)
        .on('click', function(d){ console.log(d); })
        .call(force.drag);


    // node.append("svg:image")
    //     .attr("xlink:href", "http://localhost/past-paths/app/webroot/img/test-fail-icon.png")
    //     .attr('x', '-5px')
    //     .attr('y', '-5px')
    //     .attr('width', '16px')
    //     .attr('height', '16px');
    // add the nodes
    // node

    // add the text 
    node.append("text")
        .attr("x", 12)
        .attr("dy", ".35em")
        .text(function(d) { return d.name; })
        .on('click', function(d){ console.log('kapow'); });
}

// add the curvy lines
function tick() {
    // path.attr("d", function(d) {
    //     var dx = d.target.x - d.source.x,
    //         dy = d.target.y - d.source.y,
    //         dr = Math.sqrt(dx * dx + dy * dy);
    //     return "M" + d.source.x + "," + d.source.y + 
    //            "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
    // });
    
    edges.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node
        .attr("transform", function(d) { 
            return "translate(" + d.x + "," + d.y + ")"; });
}

function zoom() {
  svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}

function resize() {
    console.log('changed');

    d3.select('svg').attr('width', window.innerWidth).attr('height', window.innerHeight);
}

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
                console.log(results);
                draw_graph(results);
            });
        }
    });

        // $.ajax({
        //         type: "GET",
        //         url: "get_artefacts/jug",
        //         data: { }
        //     }).done(function(results) {
        //         // draw the results
        //         results = $.parseJSON(results);
        //         artefacts = results;
        //         console.log(results);
        //         draw_graph(results);
        //     });
});