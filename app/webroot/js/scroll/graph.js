// ** graph ** //
var zoomListener = d3.behavior.zoom()
  	.scaleExtent([0.4, 1.5])
  	.on("zoom", zoomHandler);

// force directed graph
var graph = new myGraph("#map_explorer");
var vis;

function zoomHandler() {
	vis.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}

function myGraph(el) {

	this.updateGraph = function(){
		update();
	}

    // Add and remove elements on the graph object
    this.addNode = function (id, lidoRecID, text, radius, type) {
        nodes.push({"id":id, "lidoRecID" : lidoRecID,"text" : text, "radius" : radius, "type" : type});
        // update();
    };

    this.removeNode = function (id) {
        var i = 0;
        var n = this.findNode(id);

        while (i < links.length) {
            if ((links[i]['source'] == n)||(links[i]['target'] == n))
            {
                links.splice(i,1);
            }
            else i++;
        }

        nodes.splice(findNodeIndex(id),1);
        // update();
    };

    this.removeLink = function (source,target){
        for(var i=0;i<links.length;i++)
        {
            if(links[i].source.id == source && links[i].target.id == target)
            {
                links.splice(i,1);
                break;
            }
        }
        // update();
    };

    this.removeallLinks = function(){
        links.splice(0,links.length);
        // update();
    };

    this.removeAllNodes = function(){
        nodes.splice(0,nodes.length);
        update();
    };

    this.addLink = function (source, target, value) {
        links.push({"source": this.findNode(source),"target": this.findNode(target),"value":value});
        // update();
    };

    this.findNode = function(id) {
        for (var i in nodes) {
            if (nodes[i]["id"] === id) return nodes[i];};
    };

    var findNodeIndex = function(id) {
        for (var i=0;i<nodes.length;i++) {
            if (nodes[i].id==id){
                return i;
            }
            };
    };

    // set up the D3 visualisation in the specified element
    var w = window.innerWidth,
        h = window.innerHeight;

    vis = d3.select(el)
        .append("svg:svg")
        .attr("class", "map_explorer_svg")
        .attr("width", w)
        .attr("height", h)
        .attr("id","svg")
        .attr("pointer-events", "all")
        .attr("viewBox","0 0 "+w+" "+h)
        .attr("perserveAspectRatio","xMinYMid")
        .append('svg:g');

    d3.select(".map_explorer_svg")
	  .append("defs").append("clipPath")
	    .attr("id", "clipCircle")
      .append("circle")
      	.attr("r", 75);

    zoomListener(d3.select(el));

    var force = d3.layout.force();

    var drag = force.drag()
      .on("dragstart", function(d) {
		d3.event.sourceEvent.stopPropagation();
  	});

    var nodes = force.nodes(),
        links = force.links();

    var update = function () {
          var link = vis.selectAll("line")
            .data(links, function(d) {
                    return d.source.id + "-" + d.target.id; 
                });

        link.enter().append("line")
            .attr("id",function(d){return d.source.id + "-" + d.target.id;})
            .attr("class","link");
        link.append("title")
        .text(function(d){
            return d.value;
        });

        link.exit().remove();

        var node = vis.selectAll("g.node")
            .data(nodes, function(d) { 
                return d.id;});

        var nodeEnter = node.enter().append("g")
            .attr("class", function(d) {
            	return 'node-' + d.type;
            })
            .call(drag)
            .on("mouseover", function(d, i){ 
	            // getArtefactsByNodeId(d, d3.mouse(this));
	            // highlight paths and nodes connected
	            link.classed('link-highlighted', function(l){

		            // if(l.source.id == d.id || l.target.id == d.id)
            		// {
		            // 	return true;
		            // }
		            // else
		            // {
	             //  		return false;
		            // }
	            });
	          })
	          .on("mouseout", function(){ 
	            link.classed('link-highlighted', false);
          	});
          	// .on("dragstart", function() { d3.event.sourceEvent.stopPropagation(); });
	     //    .append("image")
      // 		.attr("xlink:href", function(d){
      // 			if(d.type == "artefact")
      // 			{
      // 				return get_img_url(d.lidoRecID + "/0.jpeg", 1);
      // 				// return "http://localhost/past-paths/img/artefacts/medium/" + d.lidoRecID + "/0.jpeg";
      // 			}
      // 			else
      // 			{
      // 				return null;
      // 			}
      // 		})
      // 		.attr("x", -150)
      // 		.attr("y", -150)
		    // .attr("width", 300)
		    // .attr("height", 300)
		    // .attr("clip-path", "url(#clipCircle)");

        nodeEnter.append("svg:circle")
        .attr("r", function(d) { return d.radius;})
        .attr("id",function(d) { return "Node;"+d.id;})
        .attr("class","node-circle");

        nodeEnter.append("svg:text")
        .attr("class","node-label")
        .text(function(d) { return d.text.charAt(0).toUpperCase() + d.text.slice(1); })
        .attr("text-anchor", "middle");


        d3.selectAll(".node-artefact")
           .append("image")
      		.attr("xlink:href", function(d){
      			if(d.type == "artefact")
      			{
      				return get_img_url(d.lidoRecID + "/0.jpeg", 1);
      				// return "http://localhost/past-paths/img/artefacts/medium/" + d.lidoRecID + "/0.jpeg";
      			}
      			else
      			{
      				return null;
      			}
      		})
      		.attr("x", -150)
      		.attr("y", -150)
		    .attr("width", 300)
		    .attr("height", 300)
		    .attr("clip-path", "url(#clipCircle)");

        node.exit().remove();
        force.on("tick", function() {

            node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y         + ")"; });

            link.attr("x1", function(d) { return d.source.x; })
              .attr("y1", function(d) { return d.source.y; })
              .attr("x2", function(d) { return d.target.x; })
              .attr("y2", function(d) { return d.target.y; });
        });


        // Restart the force layout.
        force
        .gravity(.05)
        .distance((node.length * 800))
        .linkDistance((link.length * 100))
        .linkStrength(0.1)
        .theta(1)
        .charge(Math.abs((node.length * 1000)) * -1)
        .size([w, h])
        .start();
    };

    // Make it all go
    update();
}