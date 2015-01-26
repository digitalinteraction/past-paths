<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<?php echo $this->Html->script('jquery.jsPlumb-1.6.4-min.js'); ?>
<div class="row">
	<div class="col-md-12 full-screen">
		<div class="title">
			<span class="description"><?php echo $artefact['title']; ?></span>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->Element('artefact', $artefact); ?>
		</div>
	</div>
</div>

	<div class="more">
	</div>

<div class="scroll_speed">
</div>

<script id="artefact_template" type="text/x-mustache-template">
	{{#data}}
	<div class="artefact-tile" style="background-image:url('../app/webroot/img/{{image}}');" onclick="show_artefact(this);">
		<div class="title">
			<span class="description">{{title}}</span>
		</div>
	</div>
	{{/data}}
</script>

<script>

var artefact = {};
var img_webroot = "<?php echo $this->webroot; ?>app/webroot/img/";
var webroot = <?php echo $this->webroot; ?>;
var artefact_template;

// function render_artefact(artefact){
// 	$.get(webroot + 'app/webroot/views/artefact.mst', function(template) {
// 	    this.artefact_template = Mustache.render(template, artefact);
//   	});
// }

// function show_artefact(artefact_tile){
// 	console.log($(artefact_tile));

// 	$(artefact_tile).css("position", "fixed");
// 	$(artefact_tile).css("top", "0px");
// 	$(artefact_tile).css("left", "0px");
// 	$(artefact_tile).css("right", "0px");
// 	$(artefact_tile).css("bottom", "0px");
// 	$(artefact_tile).css("max-width", "100%");
// 	$(artefact_tile).css("max-height", "100%");
// 	$(artefact_tile).css("z-index", "2");
// }

function show_artefact(artefact_tile){
	$(artefact_tile).removeClass("artefact-tile");
	$(artefact_tile).addClass("full-screen");
	$('html, body').animate({
	    scrollTop: $(artefact_tile).offset().top
	}, 500);
	$(artefact_tile).css('height', window.innerHeight);
	// $(artefact_tile).attr('onclick','').unbind('click');
	$(artefact_tile).attr('onclick', function(){
		if($(artefact_tile).css("background-size") == "cover")
		{	
			$(artefact_tile).css("background-size", "auto 100%");
		}
		else
		{
			$(artefact_tile).css("background-size", "cover");
		}

	}).bind("click");
}


function fetch_more(record_rnd){
	$.ajax('get', {
	  data: { offset: record_rnd },
      success: function(data) {

      	var artefact_arr = {};
      	artefact_arr.data = $.parseJSON(data);

      	// console.log(artefact_arr);

      	var html = Mustache.render($("#artefact_template").html(), artefact_arr);
  		$('.more').append(html);
  		record_rnd += 10;

     //  	for(var i=0; i<=data.length; i++)
     //  	{	
     //  		// var template = $('#artefact_template').val();



     //  		var html = Mustache.render(template, data[i]);

     //  		console.log(html);

     //  		$('.more').append(html);

     // //  		$.get(webroot + 'app/webroot/views/artefact.mst', function(template) {
		   // //  	$('.more').append(Mustache.render(template, data[i]));
  			// // });
     //  	}
      },
      error: function(e) {
         console.log(e);
      }
   });
}

$(document).ready(function(){
	// load_templates();

	var previous_top_in_px = $(window).scrollTop();
	var previous_event_timestamp = new Date().getTime();
	var record_rnd = 0;

	artefact = <?php echo json_encode($artefact); ?>;
	$('.full-screen').css('background-image', 'url("' + img_webroot + artefact.image + '")');
	$('.full-screen').css('height', window.innerHeight);
	
	$('.full-screen').attr("onclick", function(){
		if($(this).css("background-size") == "cover")
		{	
			$(this).css("background-size", "auto 100%");
		}
		else
		{
			$(this).css("background-size", "cover");
		}
	}).bind('click');
		
	// $('.container').hide();

	$(window).scroll(function(e) {
	    // if($(window).scrollTop() > 10){
	    //     $('.container').fadeIn();
	    // }

	    if($(window).scrollTop() >= ($(document).height() - ($(window).height()) - 300)){
	    	fetch_more(record_rnd);
	    }

	    // console.log($(this).scrollTop());

	    var delay_in_milliseconds = e.timeStamp - previous_event_timestamp;
	    if(delay_in_milliseconds > 500)
	    {
	    	var speed_in_px = ($(window).scrollTop() - previous_top_in_px) / delay_in_milliseconds;
	    	// console.log(delay_in_milliseconds);
	    	// console.log(speed_in_px);

	    	if(Math.abs(speed_in_px) > 1)
	    	{
	    		$('.scroll_speed').css("background-color", "green");
	    	}
	    	else
	    	{
	    		$('.scroll_speed').css("background-color", "red");	
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
});


jsPlumb.bind("ready", function() {
	jsPlumb.setContainer($("body"));

	jsPlumb.connect({
    	source:"container",
    	target:"artefact_template",
    	endpoint:"Rectangle"
	});
});

</script>



