<!-- <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet"> -->
<!-- <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> -->
<!-- Full Screen Tile -->

<?php

echo $this->Html->script('jquery.foggy.min.js');

$user_agent = getenv("HTTP_USER_AGENT");

if(strpos($user_agent, "Win") !== FALSE)
{
	echo $this->Html->script('jquery.nicescroll.min.js');
	$is_mac = false;
}
else
{
	$is_mac = true;
}
?>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.14/jquery.scrollTo.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

<style>
.artefact-tile{
	cursor:pointer;
	box-shadow: 0px 0px 10px rgba(0,0,0,0.4);
}

html,body{
	background-image:url('<?php echo $this->webroot; ?>img/cartographer.png') !important;
	/*background-color:initial !important;*/
	min-height:100%;

}

</style>

<div id="artefact-tile-<?php echo $artefact['lidoRecID_js']; ?>" class="full-screen first-tile" style="z-index:3; background-image:url(<?php echo $this->webroot . 'img/artefacts/large/' . $artefact['images'][0]; ?>);"  data-lido-rec-id="<?php echo $artefact['lidoRecID_js']; ?>">
	<div class="glyphicon glyphicon-zoom-out size-btn artefact-controls" aria-hidden="true"></div><div class='info artefact-controls'><i class="fa fa-info artefact-controls"></i></div><div class='title artefact-controls'><?php echo $artefact['title']; ?></div>
</div>


<div id="sidepanel">
	<button type="button" class="btn btn-default" style="position:absolute;right:-45px;top:5px;background:#222;color:white;" onclick="showpanel();">
    	<i class="fa fa-bars"></i>
  	</button>
  	<div id="historypanel">
  		<div style="overflow-x:hidden;margin:10px;" id="historyinner" class="centered">
  			<h3>Browsing History</h3>
  			<p>Below you can see the records you've explored during your exploration experience.</p>
  		</div>
  		<div class="centered" style="padding:15px;">
  			<button class="btn btn-large btn-primary" id="explore_btn">Explore</button>
  		</div>
  	</div>
</div>


<div id="artefact-container">
	
</div>
<div class="scroll_speed" style="-webkit-transition: all 0.1s ease-out; transition: all 0.1s ease-out;">
	<i class="fa fa-bullseye fa-4x"></i>
</div>


<div id="map_explorer">
	<span class="glyphicon glyphicon-remove close"></span>
<div>


<!-- <div class="scroll_indicator">

</div> -->


<script>
var shown = false;
var is_mac = <?php echo $is_mac; ?>;

function showpanel()
{
	if (shown)
	{
		shown = false;
		$('#sidepanel').animate({left:-300});
	}
	else
	{
		shown = true;
		$('#sidepanel').animate({left:0});
	}

}

var thehistory = [];

$.fn.isAfter = function(sel){
        return this.prevAll().filter(sel).length !== 0;
};

$.fn.isBefore= function(sel){
    return this.nextAll().filter(sel).length !== 0;
};

function zoomto(el)
{
	$('#historypanel').scrollTo($('[data-lido-rec-id-history="'+$(el).data('lido-rec-id-history')+'"]'),500);
	$('body').scrollTo($('[data-lido-rec-id="'+$(el).data('lido-rec-id-history')+'"]'),500);
}

function add_history(a)
{
	
	console.log(a);
	var img = $('<div data-lido-rec-id-history="'+a.lidoRecID_js+'" class="history_artefact" onclick="zoomto(this)"></div>');
	img.css('background-image', "url('<?php echo $this->webroot; ?>img/artefacts/medium/" + a.lidoRecID + "/0.jpeg')");


	// //calculate distance...
	var thisone = $('[data-lido-rec-id="'+a.lidoRecID_js+'"]');

	var gap = $('<div class="gap"></div>');

	var insertafter = false;

	if (thehistory.length > 0)
	{
		var prevone = thisone.prevAll('.full-screen').first();
		if (prevone.length > 0)
		{

			//var prevone = $('[data-lido-rec-id="'+thehistory[thehistory.length-1].lidoRecID_js+'"]');
			var middle = prevone.nextUntil(thisone);
			// var distance = Math.abs(thisone.offset().top - prevone.offset().top);
			// console.log(distance);

			gap.css('height',Math.sqrt(middle.length * 50));

			middle.each(function()
			{
				var img = $(this).data('lido-rec-id');
				var sm = $('<div class="img-sm"></div>');
				sm.css('background-image', 'url(<?php echo $this->webroot; ?>img/artefacts/medium/' + artefacts[img].lidoRecID + '/0.jpeg)');
				gap.append(sm);
			});


			//id of the history item to insert just after:
			var beforeid = prevone.data('lido-rec-id');
			img.insertAfter($('[data-lido-rec-id-history='+beforeid+']'));
			gap.insertBefore(img);
		}
		else
		{
			//insert at front
			$('#historyinner').prepend(img);
		}
		//console.log(middle);
		//$('#historypanel').append(gap);

		// $('.full-screen').css('border','none');
	}
	else
	{
		$('#historyinner').append(img);
	}

	thehistory.push(a);

	// scroll to bottom
	$("#historypanel").animate({ scrollTop: $('#historypanel')[0].scrollHeight });
}

</script>


<script>
var artefacts = {};
artefacts["<?php echo str_replace('&', '-', str_replace('.', '-', $artefact['lidoRecID'])); ?>"] = <?php echo json_encode($artefact); ?>;
var first_artefact = <?php echo json_encode($artefact); ?>;
var img_errors = [];
var fetch_more_tiles = true;
var record_offset = 10000;
var randomness = 0;
var keep_alive_flag = true;
var s_id = "<?php echo $_id; ?>";

// size 0, 1, 2 = small, med, large
function get_img_url(img, size)
{
	var sizes = ['small', 'medium', 'large'];
	return '<?php echo $this->webroot; ?>img/artefacts/' + sizes[size] + '/' + img;
}

function fetch_more(randomness){
	// fetch some records
	console.log(randomness);
	$.ajax('../fetch_more', {
		data: {offset:record_offset, randomness_level:randomness, _id:s_id},
		success: function(data) {
			// attach the response to the artefact container
			var artefacts_to_insert = "";
			data =  $.parseJSON(data);
		
			$.each(data, function(count, artefact_record) {
				// check if images has been set - bug
				if(artefact_record.images)
				{
					artefacts[artefact_record.lidoRecID_js] = artefact_record;
					artefacts_to_insert  += "<div class='artefact-tile' style='background-image:url(" + get_img_url(artefact_record.images[0], 1) + ");' id='artefact-tile-" + artefact_record.lidoRecID_js + "' data-lido-rec-id='" + artefact_record.lidoRecID_js + "'><div class='glyphicon glyphicon-zoom-out size-btn artefact-controls'></div><div class='info artefact-controls'><i class='fa fa-info'></i></div><div class='title artefact-controls'>" + artefact_record.title + "</div>";

					if(artefact_record.images.length > 1)
					{
						artefacts_to_insert += "<div class='artefact-controls image-counter'><span class='glyphicon glyphicon-stop single' aria-hidden='true'></span><span class='glyphicon glyphicon-stop multiple' aria-hidden='true'></span></div>";
					}

					artefacts_to_insert += "</div>";
				}
			});


			$('#artefact-container').append(artefacts_to_insert);
			fetch_more_tiles = true;
			record_offset += Object.keys(data).length;
		},
		error: function(e) {
			console.log(e);
		}
	});
}


$(document).ready(function(){

	var previous_top_in_px = $(window).scrollTop();
	var previous_event_timestamp = new Date().getTime();

	// $('#first-tile').css('background-image', 'url("<?php echo $this->webroot . "img/artefacts/large/" . $artefact["lidoRecID"]; ?>/0.jpeg")');

	// full screen - toggle full view or zoomed in view
	$(document.body).on('click', '.full-screen' ,function(e){
			
		$(this).find('.size-btn').toggleClass('glyphicon-zoom-out');
		$(this).find('.size-btn').toggleClass('glyphicon-zoom-in');

		$(this).toggleClass('show-all');

		$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
	       $('html, body').stop();
	   	});

		$('html, body').animate({
	    	scrollTop: $(this).offset().top
		}, 500, function(){
       		$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
   		});
	});

	// Artefact Click
	$(document.body).on('click', '.artefact-tile' ,function(e){
		// convert to full screen
		$(this).toggleClass('full-screen');
		$(this).toggleClass('artefact-tile');

		var artefact = artefacts[$(this).attr('data-lido-rec-id')];

		// var image = new Image();
		// image.src = artefact.images[0];

		// if(image.height > image.width)
		// {
		// 	$(this).toggleClass('show-all');
		// 	$(this).find('.size-btn').toggleClass('fa-expand');
		// 	$(this).find('.size-btn').toggleClass('fa-compress');
		// }

		// $(this).toggleClass('show-all');
		
		$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
	       $('html, body').stop();
	   	});

		$('html, body').animate({
	    	scrollTop: $(this).offset().top
		}, 500, function(){
			// cancel scroll stop (called above if user scrolls)
       		$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
   		});
		
		// load image(s)
		if(artefact.images.length > 1)
		{
			// remove background on .full-screen div
			$(this).css('background-image', '');

			// contat the images
			var imgs_to_append = "";
			imgs_to_append += "<div id='" + artefact.lidoRecID_js + "-carousel' class='carousel slide' data-ride='carousel'><div class='carousel-inner' role='listbox'>";

			imgs_to_append += "<div class='item active full-screen' style='background-image: url(" + get_img_url(artefact.images[0], 2) + ")'></div>";
			for (var i = 1; i < artefact.images.length; i++) {
				imgs_to_append += '<div class="item full-screen" style="background-image: url(' + get_img_url(artefact.images[i], 2) + ')"></div>';
			};

			imgs_to_append += '</div>';
			imgs_to_append += '<a class="left carousel-control artefact-controls" href="#' + artefact.lidoRecID_js + '-carousel" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span><span class="sr-only">Previous</span></a><a class="right carousel-control artefact-controls" href="#' + artefact.lidoRecID_js + '-carousel" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><span class="sr-only">Next</span></a></div>';

			// append carousel to parent
			$(this).append(imgs_to_append);

			// instantiate carousel
			$('#' + artefact.lidoRecID_js + '-carousel').carousel({
	        	//options here
	        });
		}
		else
		{
			$(this).css('background-image', 'url("' + get_img_url(artefact.images[0], 2) + '")');
		}

		show_hide_controls(artefact);

		$.ajax('../record_click', {
			data: {lidoRecID:artefact.lidoRecID, _id:s_id},
			async: false,
			success: function(data) {
				
			},
			error: function(e) {
				console.log(e);
			}
		});

		add_history(artefact);
	});
	
	

	$(document.body).on('click', '.info' ,function(e){
		var artefact = artefacts[$(this).parent().attr('data-lido-rec-id')];

		$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
	       $('html, body').stop();
	   	});

		if($(this).parent().next().attr('class') != 'description-wrapper'){

				var event_string = "";

				if(artefact.events.length > 0)
				{
					event_string = "<div><span class='artefact-text-title'>Events</span><span class='artefact-text'>";
					$.each(artefact.events, function(count, event_record){
						console.log(event_record.event);
						event_string += "<span class='subtitle'>" + event_record.event.eventType.term + "</span>";

						var event_description = "<span class='text'><ul>";

						if(event_record.event.place)
						{
							event_description += "<li>" + event_record.event.place.namePlaceSet.appellationValue + '</li>';
						}

						if(event_record.event.eventDate)
						{
							event_description += "<li>" + event_record.event.eventDate.displayDate + '</li>';
						}

						if(event_record.event.eventActor)
						{
							event_description += "<li>" + event_record.event.eventActor.actorInRole.actor.nameActorSet.appellationValue + "</li>";
						}

						if(event_record.event.periodName)
						{
							event_description += "<li>" + event_record.event.periodName.term + "</li>";	
						}

						event_description += "</ul></span>";
						event_string += event_description;
					});

					event_string += '</span></div>';
				}

				var measurements_string = "";

				if(artefact.measurements.length > 0)
				{
					measurements_string = "<div><span class='artefact-text-title'>Measurements</span><span class='artefact-text'>";
					$.each(artefact.events, function(count, measurement_record){
						console.log(measurement_record);
						// measurements_string += "<span class='subtitle'>" + measurement_record.event.eventType.term + "</span>";

						// var event_description = "<span class='text'><ul>";

						// if(event_record.event.place)
						// {
						// 	event_description += "<li>" + event_record.event.place.namePlaceSet.appellationValue + '</li>';
						// }

						// if(event_record.event.eventDate)
						// {
						// 	event_description += "<li>" + event_record.event.eventDate.displayDate + '</li>';
						// }

						// if(event_record.event.eventActor)
						// {
						// 	event_description += "<li>" + event_record.event.eventActor.actorInRole.actor.nameActorSet.appellationValue + "</li>";
						// }

						// if(event_record.event.periodName)
						// {
						// 	event_description += "<li>" + event_record.event.periodName.term + "</li>";	
						// }

						// event_description += "</ul></span>";
						// measurements_string += event_description;
					});

					measurements_string += '</span></div>';
				}

				var terms_string = "";

				//console.log(artefact.terms);
				if(artefact.terms.length > 0)
				{
					terms_string = "<div><span class='artefact-text-title'>Terms</span><span class='artefact-text'><ul>";
					$.each(artefact.terms, function(key, term){
						terms_string += '<li>' + term + '</li>';
					});
					terms_string += '</ul></span></div>';
				}

				$(this).parent().after('<div class="description-wrapper">'+
				'<div class="description">' +
						'<div><span class="artefact-text-title">Title(s)</span><span class="artefact-text">' + artefact.title + '</span></div>'+
						'<div><span class="artefact-text-title">Collection</span><span class="artefact-text">' + artefact.descriptiveMetadata.objectClassificationWrap.classificationWrap.classification[2].term + '</span></div>'+
						event_string +
						terms_string +
						'<div><span class="artefact-text-title">Copyright</span><span class="artefact-text">Tyne & Wear Archives & Museums</span></div>'+
				'</div>'+
				'</div>');

				$('html, body').animate({
			   		scrollTop: $(this).parent().next().offset().top - ($(window).height() / 2)
				}, 500, function(){
		       		$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
		   		});
		}
		else
		{

			if($(this).parent().next().is(':hidden'))
			{
				$(this).parent().next().fadeIn();
				$('html, body').animate({
			   		scrollTop: $(this).parent().next().offset().top - ($(window).height() / 2)
				}, 500, function(){
       				$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
   				});
			}
			else
			{
				$(this).parent().next().fadeOut();
				$('html, body').animate({
		    		scrollTop: $(this).parent().offset().top
				}, 500, function(){
       				$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
   				});
			}
		}

		// stop child event bubbling to parent (.full-screen onclick)
		return false;
	});

	// Artefact Hover
	$(document.body).on('mouseenter mouseleave', '.artefact-tile' ,function(e){
		$(this).find('.title').slideToggle(400);
		$(this).find('.image-counter').fadeToggle(400);
	});

	$(document.body).on('error', function (e) {
	    console.log('image error: ' + this.src);
	});

	window.addEventListener("beforeunload", function (e) {
	  // var confirmationMessage = "Do you want to end the session?";

	  // (e || window.event).returnValue = confirmationMessage; //Gecko + IE
	  // return confirmationMessage;                            //Webkit, Safari, Chrome
	});




	$(window).scroll(function(e) {
		randomness = 0;
		var delay_in_milliseconds = e.timeStamp - previous_event_timestamp;
	    if(delay_in_milliseconds > 800)
	    {
	    	var speed_in_px = ($(window).scrollTop() - previous_top_in_px) / delay_in_milliseconds;
	    	// console.log(delay_in_milliseconds);
	    	// console.log(speed_in_px);


	    	$('.scroll_speed').foggy({
				   blurRadius: speed_in_px ^ 1.5,          // In pixels.
				   opacity: 0.8,           // Falls back to a filter for IE.
				   cssFilterSupport: true  // Use "-webkit-filter" where available.
				});

	    	if(Math.abs(speed_in_px) > 3)
	    	{
	    		randomness = 2;
	    		//$('.scroll_speed').css("background-color", "#6c6");
	    		
	    	}
	    	else if(Math.abs(speed_in_px) > 1)
	    	{
	    		randomness = 1;
	    		//$('.scroll_speed').css("background-color", "orange");
	    		// console.log(speed_in_px);
	    	}
	    	else
	    	{
	    		randomness = 0;
	    		//$('.scroll_speed').css("background-color", "#e44");
	    	}

	    	previous_event_timestamp = new Date().getTime();
	    	previous_top_in_px = $(window).scrollTop();

	    	// console.log(lineData[(lineData.length - 1)]);
	    	// lineData.push({ x : Math.floor((Math.random() * 50) + 40), y : (lineData[(lineData.length - 1)].y + 50)});
	    	// console.log(lineData);
	    	// redraw_lines();
	    }

		clearTimeout($.data( this, "scrollCheck" ));
		$.data(this, "scrollCheck", setTimeout(function() {
			// randomness = 0;
			$('.scroll_speed').foggy(false);
			//$('.scroll_speed').css("background-color", "red");
		}, 500));

		if($(window).scrollTop() >= ($(document).height() - ($(window).height() + ($(window).height() * 0.85) ))){
			if(fetch_more_tiles == true){
				fetch_more_tiles = false;
	    		fetch_more(randomness);
			}
	    }

	});

	// polling to keep session alive
	function keep_alive(){
		$.ajax('../keep_alive', {
			data: {_id : s_id},
			success: function(data) {
				setTimeout(keep_alive, 10000);
			},
			error: function(e) {
				console.log(e);
			}
		});
	}

	function show_hide_controls(artefact){
		var i;
		var reading = false;

		$("#artefact-tile-" + artefact.lidoRecID_js).mousemove(function() {
		    clearTimeout(i);

		    $(this).find('.title').mouseleave(function(){
		    	clearTimeout(i);
		    	reading = false;
		    }).mouseenter(function(){
		    	reading = true;
		    	clearTimeout(i);
		    });

		    $(this).find('.artefact-controls').fadeIn();
		    i = setTimeout(function(artefact_tile){ 
		    	if(!reading)
		    	{
		    		$(artefact_tile).find(".artefact-controls").fadeOut();
		    	}
		    }, 2500, $(this));
		}).mouseleave(function() {
		    clearTimeout(i);
		    // $("#menu").hide();  
		});
	}

	keep_alive();
	fetch_more(0);
	show_hide_controls(first_artefact);
});


//The data for our line
// var lineData = [{ x : 50, y : 0}];
 
// var scroll_container = d3.select(".scroll_indicator").append("svg")
//                                     .attr("width", window.innerWidth)
//                                     .attr("height", window.innerHeight);

// var lineFunction;
// var lineGraph;


// lineFunction = d3.svg.line()
//                       .x(function(d) { return d.x; })
//                       .y(function(d) { return d.y; })
//                       .interpolate("cardinal");


// lineGraph = scroll_container.selectAll('path.line')
// 						.data([lineData])
// 					    .enter()
// 					    .append("svg:path")
// 					    .attr("d", lineFunction)
// 					    .attr("stroke", "steelblue")
//  							.attr("stroke-width", "2")
//  							.attr("fill", "none");

// function redraw_lines(){

// 	lineGraph = scroll_container.selectAll('path.line')
// 							.data([lineData])
// 						    .enter()
// 						    .append("svg:path")
// 						    .attr("d", lineFunction)
// 						    .attr("stroke", "steelblue")
//   							.attr("stroke-width", "2")
//   							.attr("fill", "none");

//     var totalLength = lineGraph.node().getTotalLength();

//     lineGraph.attr("stroke-dasharray", totalLength + " " + totalLength)
//       		 .attr("stroke-dashoffset", totalLength)
//       		 .transition()
//         	 .duration(1200)
//         	 .ease("linear")
//         	 .attr("stroke-dashoffset", 0);
// }
</script>



<script>
$(function(){
	if(is_mac == false)
	{
		$("html").niceScroll();
	}
});
</script>



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

var svg = d3.select("#map_explorer").append("svg")
    .attr("width", window.innerWidth)
    .attr("height", window.innerHeight)
    .attr("class", "bubble");

var container = svg.append("g")
                   .attr('class', 'container')
                   .attr('id', 'map_container')
                   .on("dragstart", function() {
                      // alert();
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
    // .gravity(0)
    // .charge(0);
    .gravity(0.5);
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

// explore btn event
$(document.body).on('click', '#explore_btn' ,function(e){

	if($('#map_explorer').is(':visible'))
	{

	}
	else
	{
		$('#map_explorer').show();
		d3.json("../session_data_map?s_id=" + s_id, function(error, response) {

		  // console.log(response);
		  console.log('data fetched');
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
	}


	// for (var i = 0; i < 20; i++) {
	// 	links.push({
	//         'source': Math.log(Math.floor(Math.random() * links.length)), 
	//         'target': Math.log(Math.floor(Math.random() * links.length)),
	//         'value': Math.log(Math.floor(Math.random() * 20))
 //      	});
	// }



});

// close btn event
$(document.body).on('click', '.close' ,function(e){
	$('#map_explorer').hide();

	nodes = [];
	links = [];
	$('#map_container').empty();

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

var padding = 1.5, // separation between same-color circles
    clusterPadding = 6, // separation between different-color circles
    maxRadius = 12;

function tick(e) {

  // Push different nodes in different directions for clustering.
  // var k = 10 * e.alpha;
  // nodes.forEach(function(o, i) {
  //   o.y += i & 1 ? k : -k;
  //   o.x += i & 2 ? k : -k;
  // });


  gnode.attr("transform", function(d, i) { return "translate(" + d.x + "," + d.y + ")"; });

  link.attr("x1", function(d) { return d.source.x; })
      .attr("y1", function(d) { return d.source.y; })
      .attr("x2", function(d) { return d.target.x; })
      .attr("y2", function(d) { return d.target.y; });

  // gnode.each(collide(0.5)).attr("x", function(d) { return d.x; })
      // .attr("y", function(d) { return d.y; });

  gnode.attr("x", function(d) { return d.x; })
      .attr("y", function(d) { return d.y; });
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
	console.log('draw called');
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

  // gnode.exit().remove();

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
  console.log((nodes.length * 10))
  force.charge(Math.abs((nodes.length * 300)) * -1);
  force.start();

    // pan(0);
}

// Resolves collisions between d and all other circles.
function collide(alpha) {
  var quadtree = d3.geom.quadtree(nodes);
  return function(d) {
    var r = d.radius + maxRadius + Math.max(padding, clusterPadding),
        nx1 = d.x - r,
        nx2 = d.x + r,
        ny1 = d.y - r,
        ny2 = d.y + r;
    quadtree.visit(function(quad, x1, y1, x2, y2) {
      if (quad.point && (quad.point !== d)) {
        var x = d.x - quad.point.x,
            y = d.y - quad.point.y,
            l = Math.sqrt(x * x + y * y),
            r = d.radius + quad.point.radius + (d.cluster === quad.point.cluster ? padding : clusterPadding);
        if (l < r) {
          l = (l - r) / l * alpha;
          d.x -= x *= l;
          d.y -= y *= l;
          quad.point.x += x;
          quad.point.y += y;
        }
      }
      return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
    });
  };
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
