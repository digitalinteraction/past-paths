<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
<!-- <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> -->

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<!-- <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> -->

<?php echo $this->Html->script('jquery.jsPlumb-1.6.4-min.js'); ?>
<?php echo $this->Html->script('jqueryui.js'); ?>
<!-- <div class="row search_box">
	<div class="col-md-12">
		<h1>Explore</h1>
		<input type="text" name="q">
	</div>
</div> -->
	<div class="col-md-12 full-screen" id="<?php echo $artefact['lidoRecID']; ?>">
		<div class="title">
			<div class="info">i</div>
			<span class="description"><?php echo $artefact['title']; ?></span>
		</div>
	</div>

	<div class="more-info">
		<div class="row">
			<div class="col-md-12" id="first_artefact">
				<?php echo $this->Element('artefact', $artefact); ?>
			</div>
		</div>
	</div>


	<div id="scroll_output">
		<div class="scroll_output_row">
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
		</div>

		<div class="scroll_output_row">
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
		</div>

		<div class="scroll_output_row">
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
			<div class="scroll_output_tile"></div>
		</div>

	</div>

	<div class="more">
	</div>

<div class="scroll_speed">
</div>

<script id="artefact_template" type="text/x-mustache-template">
	{{#data}}
	<div class="artefact-tile" style="background-image:url('../{{image_root}}{{image}}');" onclick="show_artefact(this);" id="artefact-tile-{{lidoRecID}}">
		<div class="title">
			<div class="info" style="display:none">i</div>
			<span class="description">{{title}}</span>
		</div>
	</div>
	{{/data}}
</script>

<script id="artefact_show_relationships_template" type="text/x-mustache-template">
	{{#data}}
	<div class="artefact-tile explode" style="background-image:url('../{{image_root}}{{image}}');" onclick="show_artefact(this);" id="artefact-tile-{{lidoRecID}}">
		<div class="title">
			<div class="info" style="display:none">i</div>
			<span class="description">{{title}}</span>
		</div>
	</div>
	{{/data}}
</script>
<script>

var artefact = {};
var img_webroot = "<?php echo $this->webroot; ?>app/webroot/img/";
// var img_webroot = "http://localhost/past-paths-images/artefact_images/";
var webroot = <?php echo $this->webroot; ?>;
var artefact_template;
var fetch_count = 10;
var randomness = 0;
var user_path = [];
var scroll_output_array = [];

function HistoryEvent(id) {
	this.created = new Date().getTime();
	this.id = id;
};


function show_artefact(artefact_tile){
	$(artefact_tile).removeClass("artefact-tile");
	$(artefact_tile).removeClass("explode");
	$(artefact_tile).addClass("full-screen");
	// $(artefact_tile).css("border-top", "3px solid white");
	// $(artefact_tile).css("border-bottom", "3px solid white");
	$(artefact_tile).css("marginTop", "30px");
	$(artefact_tile).css("marginBottom", "30px");

	$(artefact_tile).find('.info').show();

	$(artefact_tile).find('.info').on('click', function(){
		// alert();
	});

	$('html, body').animate({
	    scrollTop: $(artefact_tile).offset().top
	}, 500);
	$(artefact_tile).css('height', window.innerHeight);
}


function fetch_more(record_rnd){
	$.ajax('get', {
	  data: { offset: record_rnd, randomness_level:randomness},
      success: function(data) {

      	var artefact_arr = {};
      	artefact_arr.data = $.parseJSON(data);

      	console.log(show_relationships);

      	if(show_relationships == true)
      	{
      		var html = Mustache.render($("#artefact_show_relationships_template").html(), artefact_arr);

      		record_rnd += 10;

	  		var tiles = $('.more').find('.artefact-tile');

	  		if(tiles.length > 10)
	  		{
	  			// var tiles = $('.more').find('.artefact-tile');
				var loop_stop = (Math.floor(Math.random() * 10) + 3);

	  			for(var i=0; i<=loop_stop; i++)
	  			{
	  				var from = (tiles.length - (Math.floor(Math.random() * 10) + 1) - 5);
					var to = tiles.length - (Math.floor(Math.random() * 10) + 1)	
					
					console.log(tiles[from]);

	  				var connections = jsPlumb.connect({
				    	source: tiles[from],
				    	target: tiles[to],
				    	anchor: ["Top", "Bottom"],
						endpoint:[ "Dot", { radius:5, hoverClass:"myEndpointHover" } ],
						connector:[ "Bezier", { curviness:100 } ],
						overlays: [
							[ "Arrow", { foldback:0.2 } ],
							[ "Label", { cssClass:"labelClass" } ]  
						]
					});
				}
	  		}

      	}
      	else
      	{
      		var html = Mustache.render($("#artefact_template").html(), artefact_arr);
      	}

  		$('.more').append(html);
      },
      error: function(e) {
         console.log(e);
      }
   });
}

function show_info(artefact){

}

jsPlumb.bind("ready", function() {
	jsPlumb.setContainer($("body"));
});

var show_relationships = false;

$(document).ready(function(){
	var previous_top_in_px = $(window).scrollTop();
	var previous_event_timestamp = new Date().getTime();
	var record_rnd = 10000;

	artefact = <?php echo json_encode($artefact); ?>;

	var history_event = new HistoryEvent(artefact.lidoRecID);
	user_path.push(history_event);

	console.log(user_path);

	// console.log(artefact);
	// console.log(img_webroot);
	// console.log(artefact.image);

	$('.full-screen').css('background-image', 'url("' + img_webroot + artefact.image + '")');
	$('.full-screen').css('height', window.innerHeight);
	
	// $('body').on("mouseenter", ".full-screen > .title", function(){
	// 	// $('.full-screen > .title').css('max-height', '');
	// 	// $(this).slideToggle();
	// 	$('.full-screen > .title').addClass("show", 300);
	// }).on("mouseleave", ".full-screen > .title" ,function(){
	// 	$('.full-screen > .title').removeClass("show", 300);
	// });

	$('body').on('click', '.full-screen', function(ev) {
	 	if($(this).css("background-size") == "cover")
		{	
			$(this).css("background-size", "auto 100%");
		}
		else
		{
			$(this).css("background-size", "cover");
		}
	});

	// $('body').on('click', '.info', function(event) {
	// 	console.log(event.target);
	// 	// $(event.target).parent().parent().find('.more-info').toggle();

	// 	// console.log($(event.target).closest('div.more-info').toggle());
	// 	// $('.more-info').text("X");
	// 	// $('html, body').animate({
	//  //        scrollTop: ($(".more-info").offset().top - 160)
	//  //    }, 800);
	// });



	$(document).keypress(function(e) {
    	if(e.which == 13) {
    		
    		if(show_relationships == true)
    		{
    			show_relationships = false;
	        	// fetch_more(record_rnd);
	        	$('._jsPlumb_endpoint').fadeOut("slow");
				$('._jsPlumb_connector').fadeOut("slow");
	        	$('.artefact-tile').removeClass("explode", 2000);
	        	// $('.artefact-tile').removeClass("explode", 1000, "easeOutCubic");
	     //    	$('.artefact-tile').animate({
				  //   marginLeft: "15px",
				  //   marginRight: "15px",
				  //   marginBottom: "15px",
				  //   marginTop: "15px"
				  // }, 1000 );
    		}
    		else
    		{
    			
    			show_relationships = true;
	        	// fetch_more(record_rnd);

	        	// $('.artefact-tile').addClass("explode", 1000, "easeOutCubic");
	     //    	$('.artefact-tile').animate({
				  //   marginLeft: "50px",
				  //   marginRight: "50px",
				  //   marginBottom: "150px",
				  // }, 1000 );

				$('.artefact-tile').addClass("explode", 2000);


				setTimeout(function() {
					jsPlumb.repaintEverything();
					$('._jsPlumb_endpoint').fadeIn("slow");
					$('._jsPlumb_connector').fadeIn("slow");
			   		$('._jsPlumb_endpoint').show();
	        		$('._jsPlumb_connector').show();

				}, 2200);

  				// $('.artefact-tile').addClass("explode", 2000, function (){
	     //    		jsPlumb.repaintEverything();
	     //    		$('._jsPlumb_endpoint').show();
	     //    		$('._jsPlumb_connector').show();
	     //    	});
    		}
    	}
	});
		
	// $('.container').hide();

	$(window).scroll(function(e) {
	    // if($(window).scrollTop() > 10){
	    //     $('.container').fadeIn();
	    // }

	    if($(window).scrollTop() >= ($(document).height() - ($(window).height()))){
	    	fetch_more(record_rnd);
	    }

	    // console.log($(this).scrollTop());

	    var delay_in_milliseconds = e.timeStamp - previous_event_timestamp;
	    if(delay_in_milliseconds > 1000)
	    {
	    	var speed_in_px = ($(window).scrollTop() - previous_top_in_px) / delay_in_milliseconds;
	    	// console.log(delay_in_milliseconds);
	    	// console.log(speed_in_px);

	    	if(Math.abs(speed_in_px) > 1.8)
	    	{
	    		randomness = 2;
    			$('.scroll_speed').css("background-color", "#6c6");
	    	} 
	    	else if(Math.abs(speed_in_px) > 0.8)
	    	{
	    		randomness = 1;
	    		$('.scroll_speed').css("background-color", "orange");
	    	}
	    	else
	    	{
	    		randomness = 0;
	    		$('.scroll_speed').css("background-color", "#e44");
	    	}

	    	previous_event_timestamp = new Date().getTime();
	    	previous_top_in_px = $(window).scrollTop();
	    }

	    // var offset = $(window).scrollTop - lastOffset;
	    // var speedInpxPerMs = offset / delayInMs;
	    // console.log(delayInMs);
	    // console.log(offset);
	    // console.log(speedInpxPerMs);

	    // lastDate = e.timeStamp;
	    // lastOffset = e.target.scrollTop;
	});

	hide_details();

	var hide_details_toggle = true;

	$('.full-screen').on('mousemove', function() {
		if(!$(".full-screen > .title").is(":visible"))
		{
			hide_details_toggle = true;
			show_details();
		}
		else
		{
			hide_details_toggle = false;
		}
	});

	function hide_details(){
		setTimeout(function() {
			if(hide_details_toggle == true)
			{
				$(".full-screen > .title").fadeOut(500);
			}
	    }, 3000);
	}

	function show_details(){
		$(".full-screen > .title").fadeIn();
		hide_details();
	}
});

function randomize_scroll(increment, randomness_level)
{
	if(scroll_output_array.length < 15)
	{
		scroll_output_array.push("red");

		console.log($('.scroll_output_tile'));

		var all_tiles = $('.scroll_output_tile');

		// console.log(document.getElementById('scroll_output').innerHTML += '<div class="scroll_output_row"><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div></div>');
		// $('#scroll_output').html($('#scroll_output').html() + '<div class="scroll_output_row"><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div></div>');

		document.getElementById('scroll_output').appendChild('<div class="scroll_output_row"><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div><div class="scroll_output_tile"></div></div>');


		$(all_tiles[(scroll_output_array.length - 1)]).animate({
          backgroundColor: "red",
        }, 300);

		// $(':nth-child(' + scroll_output_array.length + ')').animate({
  //         backgroundColor: "red",
  //       }, 300);
	}

	// console.log()
	// $('#scroll_output div[background-color=fff]').animate({
 //          backgroundColor: "red",
 //        }, 1000);
	// for(var i = 0; i < $('#scroll_output > div > div').length; i++)
	// {
	// 	$('#scroll_output div[background-color=fff]').animate({
 //          backgroundColor: "red",
 //        }, 1000);
	// }
	
}

</script>

			// $.each(data, function(count, artefact) {
			// 	artefacts[artefact.lidoRecID_js] = artefact;
   //  			artefacts_to_insert  += "<div class='artefact-tile' style='background-image:url(" + "<?php echo $this->webroot; ?>img/artefacts/medium/" + artefact.lidoRecID + "/0.jpeg" + ");' id='artefact-tile-" + artefact.lidoRecID_js + "' data-lido-rec-id=" + artefact.lidoRecID_js + "><i class='fa fa-compress fa-lg size-btn'></i><div class='info'>i</div><div class='title'>" + artefact.title + "</div></div>";

   //  			var image = new Image();
		 //        $(image).error(function(e) {
		 //            var value = 'artefact-tile-' + artefact.lidoRecID_js;
		 //            $('#' + value).css('background-image', "");
			// 		$('#' + value).css('background-image' , 'url("<?php echo $this->webroot; ?>img/not-found.png")');
			// 		$('#' + value).css('background-color', '#1a1a1a');
			// 		$('#' + value).css('background-size', '80%');
			// 		$('#' + value).css('background-repeat', 'no-repeat');
		 //        });
		        
		 //        image.src = "<?php echo $this->webroot . 'img/artefacts/medium/';?>" + artefact.lidoRecID + "/0.jpeg";
			// });



