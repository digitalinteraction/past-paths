<!-- Full Screen Tile -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.14/jquery.scrollTo.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>


<?php
echo $this->Html->css('app');
echo $this->Html->css('artefacts/scroll2');
?>

<style>
.artefact-tile{
	cursor:pointer;
	box-shadow: 0px 0px 10px rgba(0,0,0,0.4);
}

html,body{
	background-image:url('<?php echo $this->webroot; ?>img/cartographer.png') !important;
	min-height:100%;
}

</style>

<!-- Fixed Nav Bar -->
<div id="fixed-nav-bar" class="navbar-fixed-top">
	<div class="fixed-nav-bar-inner">
		<ul>
			<li data-toggle="modal" data-target="#about-modal">About</li>
		</ul>
	</div>
</div>

<!-- Used to show nav bar if user moves cursor to top -->
<div id="nav-hit-area">
</div>

<!-- END landing info -->

<!-- Experience -->
<div id="artefact-tile-<?php echo $artefact['lidoRecID_js']; ?>" class="full-screen first-tile" style="z-index:3; background-image:url(<?php echo $this->webroot . 'img/artefacts/large/' . $artefact['images'][0]['url']; ?>);"  data-lido-rec-id="<?php echo $artefact['lidoRecID_js']; ?>">
	<div class="artefact-controls share-item" aria-hidden="true"><i class="fa fa-share-alt"></i></div>
  <div class="glyphicon glyphicon-zoom-in size-btn artefact-controls" aria-hidden="true"></div>
  <div class='info artefact-controls'><i class="fa fa-info artefact-controls"></i></div>
  <div class='title artefact-controls'><?php echo $artefact['title']; ?></div>
  <div class="item-share-links">
    <div class="item-share-links-wrapper">
      <input type="text" readonly="readonly" value="" class="share-link-item-url" onClick="this.setSelectionRange(0, this.value.length)"/>
      <i class="fa fa-facebook fa-3x share-btn share-item-fb share-fb"></i>
      <i class="fa fa-twitter fa-3x share-btn share-item-twitter share-twitter"></i>
      <i class="fa fa-envelope-o fa-3x share-btn share-item-email share-email"></i>
      <i class="fa fa-link fa-3x share-btn share-item-link share-link"></i>
    </div>
  </div>
</div>


<div id="sidepanel">
	<button type="button" class="btn btn-default side-panel-btn" data-placement="right" data-toggle="tooltip" title="Click here to show or hide the history panel" id="show-hide-history-btn">
    	<i class="fa fa-bars"></i>
  	</button>
  	<div id="historypanel">
  		<div class="centered">
  			<h2>Everything You've Explored</h2>
  		</div>
  		<div style="overflow-x:hidden;margin:10px;" id="historyinner" class="centered">
  			
  		</div>
      <div class="centered explore_btn">
          <button class="btn btn-large btn-primary" id="explore_btn">Explore Connections</button>
      </div>
      <hr />
      <div class="centered share-btns">
        <h4>Share Your Discoveries</h4>
        <input type="text" readonly="readonly" value="" class="share-link-url" onClick="this.setSelectionRange(0, this.value.length)"/>
        <i class="fa fa-facebook fa-3x share-btn share-history-fb share-fb"></i>
        <i class="fa fa-twitter fa-3x share-btn share-history-twitter share-twitter"></i>
        <i class="fa fa-envelope-o fa-3x share-btn share-history-email share-email"></i>
        <i class="fa fa-link fa-3x share-btn share-history-link share-link"></i>
      </div>
  	</div>
</div>

<div id="survey">
  <i class="fa fa-bullhorn fa-4x"></i>
  <p>Click here to tell us about your experience so far<br />(3 questions)</p><span class="survey-nope">No thanks</span>
</div>



<div class="modal fade" id="about-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" style="color:#333" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">About the Project</h4>
      </div>
      <div class="modal-body">
        <p>This site gives you access to a huge number of objects from Tyne & Wear Archives & Museums’ collections.</p>
        <p>It has been designed to help you easily discover intriguing material. Objects are presented based on how you use the site. The more you explore certain artefacts, the more related the content will be. The faster you scroll, the more random the results.</p> 
<p>Is using this site enjoyable or frustrating? <a href="https://docs.google.com/forms/d/185JlbCT3IyLyHksUeAtYFwpL0xESBm6OYcmkJ8he3Nc/viewform" target="_blank">Click here</a> to let us know what you think.</p>
<p>If you’d prefer to search for specific objects, <a href="http://www.twmuseums.org.uk/collections.html" target="_blank">use this online search tool</a> instead.</p>
<p>This website has been designed in partnership between Tyne & Wear Museums & Archives, Newcastle University and Microsoft Research. The project has been supported by the Digital R&D Fund for the Arts (Nesta), the Arts & Humanities Research Council and the National Lottery through Arts Council England.</p>
<p>Please contact <a href="mailto:john.coburn@twmuseums.org.uk">john.coburn@twmuseums.org.uk</a> with any queries you might have.<p>
<p class="centered" style="margin-top:20px; margin-bottom:20px;">
<a href="https://openlab.ncl.ac.uk/things/past-paths/" target="_blank"><button class="btn btn-large btn-primary">Find out more about this research</button></a>
</p>
<hr />
<div class="row">
  <div class="col-sm-4 logo">
    <a href="https://openlab.ncl.ac.uk" target="_blank"><?php echo $this->Html->image('logos/ncl-light.jpeg', array('alt' => 'Open Lab, Newcastle University', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
  <div class="col-sm-4 logo">
    <a href="http://www.twmuseums.org.uk" target="_blank"><?php echo $this->Html->image('logos/twam-light.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
  <div class="col-sm-4 logo">
    <a href="http://research.microsoft.com/en-us/labs/cambridge/" target="_blank"><?php echo $this->Html->image('logos/msr-light.png', array('alt' => 'Microsoft Research', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
</div>
<div class="row">
  <div class="col-sm-4 logo">
      <a href="http://www.collectionstrust.org.uk/" target="_blank"><?php echo $this->Html->image('logos/collections-trust.jpeg', array('alt' => 'Collections Trust', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
  <div class="col-sm-4 logo">
      <a href="http://thecreativeexchange.org/" target="_blank"><?php echo $this->Html->image('logos/cx_logo.gif', array('alt' => 'The Creative Exchange', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
  <div class="col-sm-4 logo">
      <a href="http://www.nesta.org.uk/" target="_blank"><?php echo $this->Html->image('logos/lotto-large.jpg', array('alt' => 'Arts and Humanities Research Council', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
</div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="terms_cloud" class="">
  <div class="terms-cloud-overview-wrapper">
     <span class="terms-cloud-overview"></span>
	   <i class="fa fa-angle-down fa-2x terms-cloud-close"></i>
  </div>
	<div class="terms-cloud-inner">
	</div>
</div>

<div id="artefact-container">
	
</div>

<div id="map_explorer" class="bubble">
	<span class="glyphicon glyphicon-remove close"></span>
  <div class="map_artefact_preview_wrapper">
      <div class="map_artefact_preview">
      </div>
      <button class="btn btn-large btn-primary launch-btn">Explore from here</button>
  </div>
</div>


<div class="prompt-scroll prompt-scroll-event">
  <div>
    <div class="scroll-event">
      <i class="fa fa-cog fa-3x"></i>
    </div>
    <div class="scroll-text">
      <p><strong>Scrolling fast</strong><br />will randomise the collections</p>
    </div>
  </div>
</div>


<div class="prompt-scroll prompt-scroll-up">
	<div>
		<div class="scroll-direction">
			<i class="fa fa-arrow-up fa-3x"></i>
		</div>
		<div class="scroll-text">
			<p><strong>Scroll up</strong><br />to see previous artefacts</p>
		</div>
	</div>
</div>

<div class="prompt-scroll prompt-scroll-down">
	<div>
		<div class="scroll-text">
			<p><strong>Scroll down</strong><br />to discover new artefacts</p>
		</div>
		<div class="scroll-direction">
			<i class="fa fa-arrow-down fa-3x"></i>
		</div>
	</div>
</div>

<div id="ieoverlay">
<h1>Welcome to Tyne & Wear Archives & Museums Discovery Interface</h1>
<h3>Your browser is currently not supported (Internet Explorer version 8 and below).</h3>
<p>Don't worry, there is an easy fix. All you have to do is visit us using a different web browser, see below for more details</p>

<div style="text-aign:center;margin:auto; width:100%">
<div class="browser-tile">
  <div>
    <a href="http://google.com/chrome" target="_blank"><?php echo $this->Html->image('browsers/chrome.png', array('alt' => 'Google Chrome (31+)', 'class' => array('img-responsive', 'center-block'))); ?></a>
  </div>
  </span><a href="http://google.com/chrome" target="_blank">Google Chrome</a></span>
</div>
<div class="browser-tile">
  <div><a href="https://www.mozilla.org" target="_blank"><?php echo $this->Html->image('browsers/firefox.png', array('alt' => 'Mozilla Firefox (31+)', 'class' => array('img-responsive', 'center-block'))); ?></a></div>
  <div><a href="https://www.mozilla.org" target="_blank">Mozilla Firefox</a></div>
</div>
<div class="browser-tile">
  <div><a href="https://www.apple.com/uk/safari/" target="_blank"><?php echo $this->Html->image('browsers/safari.png', array('alt' => 'Safari (7+)', 'class' => array('img-responsive', 'center-block'))); ?></a></div>
  <div><a href="https://www.apple.com/uk/safari/" target="_blank">Safari</a></div>
</div>
<div class="browser-tile">
  <div><a href="http://www.opera.com" target="_blank"><?php echo $this->Html->image('browsers/opera.png', array('alt' => 'Opera (30+)', 'class' => array('img-responsive', 'center-block'))); ?></a></div>
  <div><a href="http://www.opera.com" target="_blank">Opera</a></div>
</div>
<div class="browser-tile">
  <div><a href="http://windows.microsoft.com/en-GB/internet-explorer/download-ie" target="_blank"><?php echo $this->Html->image('browsers/ie.png', array('alt' => 'Internet Explorer (9+)', 'class' => array('img-responsive', 'center-block'))); ?></a></div>
  <div><a href="http://windows.microsoft.com/en-GB/internet-explorer/download-ie" target="_blank">Internet Explorer 9+</a></div>
</div>
<div>
</div>
<script>
(function() {
    "use strict";

    // Detecting IE
    var oldIE;
    var class_name = document.documentElement.className;
    if(class_name == 'ie6' || class_name == 'ie7' || class_name == 'ie8')
    {
      var overlay = document.createElement("div");
      document.getElementById('ieoverlay').style.display = 'block';
      document.body.style.overflow = "hidden"; 
    }
    
})();
</script>

<?php
echo $this->Html->script('d3.v3.min');
?>
<script>
var first_artefact = <?php echo json_encode($artefact); ?>;
var record_offset = <?php echo $offset; ?>;
var s_id = "<?php echo $_id; ?>";
var webroot = "<?php echo $this->webroot; ?>";
$(function(){
	if((navigator.appVersion.indexOf("Mac")==-1))
	{
		$("body").niceScroll({
      hwacceleration: true,
      bouncescroll: true,
      smoothscroll: true,
      mousescrollstep: 80,
      enablemousewheel: true,
    });
	}
});
</script>

<script>

function convert_to_js_lido_id(lidoRecID){
  return lidoRecID.replace(/\.|\+|\&|\//g, '-');
}

// ** graph ** //
var zoomListener = d3.behavior.zoom()
  	.scaleExtent([0.4, 1.5])
  	.on("zoom", zoomHandler);

// force directed graph
var graph;
var vis;

function collide(node) {
  var r = node.radius + 16,
      nx1 = node.x - r,
      nx2 = node.x + r,
      ny1 = node.y - r,
      ny2 = node.y + r;
  return function(quad, x1, y1, x2, y2) {
    if (quad.point && (quad.point !== node)) {
      var x = node.x - quad.point.x,
          y = node.y - quad.point.y,
          l = Math.sqrt(x * x + y * y),
          r = node.radius + quad.point.radius;
      if (l < r) {
        l = (l - r) / l * .5;
        node.x -= x *= l;
        node.y -= y *= l;
        quad.point.x += x;
        quad.point.y += y;
      }
    }
    return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
  };
}


function zoomHandler() {
	vis.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
}

function myGraph(el) {

	this.updateGraph = function(){
		update();
	}

    this.nodes = function(){
      $.each(nodes, function(key, value){
        console.log(key);
        console.log(value);
      });
    };

    // Add and remove elements on the graph object
    this.addNode = function (id, lidoRecID, text, radius, type, weight) {
        nodes.push({"id":id, "lidoRecID" : lidoRecID,"text" : text, "radius" : radius, "type" : type, "weight" : weight});
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

    this.removeAllLinks = function(){
        links.splice(0,links.length);
        vis.selectAll(".link").remove();
        // update();
    };

    this.removeAllNodes = function(){
        nodes.splice(0,nodes.length);
        vis.selectAll(".node-artefact").remove();
        vis.selectAll(".node-keyword").remove();
        // update();
    };

    this.addLink = function (source, target, value, type) {
        links.push({"source": this.findNode(source),"target": this.findNode(target),"value": value, "type" : type});
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
      	.attr("r", 45);

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

        link.exit().remove();

        var node = vis.selectAll("g.node")
            .data(nodes, function(d) { 
                return d.id;})

        
        node.enter().append("g")
            .attr("class", function(d) {
            	return 'node-' + d.type;
            })
            .call(drag)
            .on("mouseover", function(d, i){ 
	            link.classed('link-highlighted', function(l){
		            if(l.source.id == d.id || l.target.id == d.id)
            		{
		              return true;
		            }
		            else
		            {
            	  	return false;
		            }
	            });

              $('[data-lido-rec-id-history="' + convert_to_js_lido_id(d.lidoRecID) + '"]').addClass('history_artefact_highlighted');

	          })
              .on("mousedown", function(d) { d.fixed = true; })
	          .on("mouseout", function(d, i){ 
	            link.classed('link-highlighted', false);
              $('[data-lido-rec-id-history="' + convert_to_js_lido_id(d.lidoRecID) + '"]').removeClass('history_artefact_highlighted');
          	})
            .on('click', function(d){
              if(d.type == 'keyword')
              {
                  actionData = {};
                  actionData.keyword = d.text;
                  recordAction('map_fetch_artefacts', actionData);

                  $.ajax('map/get_artefacts_by_keyword', {
                    data: {keyword:d.text},
                    async: true,
                    success: function(response) {

                      response = jQuery.parseJSON(response);

                      for (var i = 0; i < response.artefact_nodes.length; i++) {
                        graph.addNode(response.artefact_nodes[i].node_id, response.artefact_nodes[i].lidoRecID, "" , response.artefact_nodes[i].weight, 'artefact');
                      }

                      for (var i = 0; i < response.nodes.length; i++) {
                        graph.addNode(response.nodes[i].node_id, "", response.nodes[i].keyword, response.nodes[i].weight, 'keyword');
                      }


                      for (var i = 0; i < response.links.length; i++) {
                        if(graph.findNode(response.links[i].source) !== undefined && graph.findNode(response.links[i].target) !== undefined)
                        {
                          graph.addLink(response.links[i].source, response.links[i].target, Math.log(Math.floor(response.links[i].value)), response.links[i].type); 
                        }
                      }

                      $('.node-keyword').remove();
                      $('.node-artefact').remove();

                      graph.updateGraph();

                    },
                    error: function(e) {
                      console.log(e);
                    }
                  });
              }

              return false;

            })
        .append("text")
        .attr("class","node-label")
        .text(function(d) { return d.text.charAt(0).toUpperCase() + d.text.slice(1);})
        .attr("text-anchor", "middle");


        node.append("image")
          .attr("class", "artefact-node-img")
      		.attr("xlink:href", function(d){
      			if(d.type == "artefact")
      			{
      				return get_img_url(d.lidoRecID + "/0.jpeg", 1);
      			}
      			else
      			{
      				return null;
      			}
      		})
      		.attr("x", -150)
      		.attr("y", -150)
        .on('mouseover', function(d){
            if(d.type == "artefact")
            {
                var img = get_img_url(d.lidoRecID + '/0.jpeg', 1);
                $('.map_artefact_preview').css('background-image', "url(" + img + ")");
                $('.map_artefact_preview').attr('lidoRecID', d.lidoRecID);
                $('.map_artefact_preview_wrapper').hide().fadeIn();
            }
        })
        .on('mouseout', function(d){
        })
        .on('click', function(d){
            if(d.type == 'artefact')
            {
                var img = get_img_url(d.lidoRecID + '/0.jpeg', 1);
                $('.map_artefact_preview').css('background-image', "url(" + img + ")");
                $('.map_artefact_preview_wrapper').hide().fadeIn();

                actionData = {};
                actionData.lidoRecID = d.lidoRecID;
                recordAction('map_fetch_keywords', actionData);

                $.ajax('map/get_keywords_by_lido_id', {
                  data: {lidoRecId:d.lidoRecID},
                  async: true,
                  success: function(response) {

                    response = jQuery.parseJSON(response);

                    for (var i = 0; i < response.artefact_nodes.length; i++) {
                      graph.addNode(response.artefact_nodes[i].node_id, response.artefact_nodes[i].lidoRecID, "" , response.artefact_nodes[i].weight, 'artefact');
                    }

                    for (var i = 0; i < response.nodes.length; i++) {
                      graph.addNode(response.nodes[i].node_id, "", response.nodes[i].keyword, response.nodes[i].weight, 'keyword');
                    }


                    for (var i = 0; i < response.links.length; i++) {
                      if(graph.findNode(response.links[i].source) !== undefined && graph.findNode(response.links[i].target) !== undefined)
                      {
                        graph.addLink(response.links[i].source, response.links[i].target, Math.log(Math.floor(response.links[i].value)), response.links[i].type); 
                      }
                    }

                    $('.node-keyword').remove();
                    $('.node-artefact').remove();

                    graph.updateGraph();

                  },
                  error: function(e) {
                    console.log(e);
                  }
                });
            }
        })
		    .attr("width", 300)
		    .attr("height", 300)
		    .attr("clip-path", "url(#clipCircle)");

        node.exit().remove();

        force.on("tick", function(e) {

          link.attr("x1", function(d) { return d.source.x; })
          .attr("y1", function(d) { return d.source.y; })
          .attr("x2", function(d) { return d.target.x; })
          .attr("y2", function(d) { return d.target.y; });


          var q = d3.geom.quadtree(nodes),
          i = 0,
          n = nodes.length;

          while (++i < n) q.visit(collide(nodes[i]));

          node.attr("transform", function(d) { 
            return "translate(" + d.x + "," + d.y         + ")"; 
          });

        });



        // Restart the force layout.
        force
        .charge(-100)
    	.gravity(0.02)
    	// .chargeDistance(0.5)
    	// .linkStrength(0.2)
        // .gravity(0.05)
        // .gravity(.05)
        .linkDistance(function(d, i){

        	if(d.type == "keyword")
        	{
        		return link.length * Math.floor(Math.random() * 200) + 80;
        	}
        	else
        	{
        		return link.length * 200;
        	}

        })
        // .linkStrength(0.1)
        // .alpha(0.5)
        // .charge(Math.abs((node.length * 10)) * -1)
        // .charge(-1 * node.length)
        .size([w, h])
        .start();
    };

    // Make it all go
    update();
}

function resize() {
    $('.map_explorer_svg').attr("width", window.innerWidth).attr("height", window.innerHeight);
}

window.addEventListener('resize', resize); 
</script>

<?php

echo $this->Html->script('jquery.visible.min.js');
echo $this->Html->script('scrollstop.js');
echo $this->Html->script('jquery.withinviewport.js');
echo $this->Html->script('google-analytics.js');
echo $this->Html->script('jquery.bootstrap-autohidingnavbar.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('lodash.min.js');
echo $this->Html->script('app.js');
echo $this->Html->script('js.cookie.js');
echo $this->Html->script('jquery.nicescroll.min.js');
?>

