<!-- <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet"> -->
<!-- <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> -->
<!-- Full Screen Tile -->

<div id="first-tile" class="full-screen"  data-lido-rec-id="<?php echo $artefact['lidoRecID_js']; ?>">
	<i class='fa fa-compress fa-lg size-btn'></i><div class='info'>i</div><div class='title'><?php echo $artefact['title']; ?></div>
</div>
<div id="artefact-container">
	
</div>
<div class="scroll_speed">
</div>

<script>
var artefacts = {};
artefacts["<?php echo str_replace('&', '-', str_replace('.', '-', $artefact['lidoRecID'])); ?>"] = <?php echo json_encode($artefact); ?>;
var img_errors = [];
var fetch_more_tiles = true;
var record_offset = 10000;
var randomness = 0;

function fetch_more(randomness){
	// fetch some records
	// console.log(randomness);
	$.ajax('get2', {
		data: {offset:record_offset, randomness_level:randomness},
		success: function(data) {
			// attach the response to the artefact container
			var artefacts_to_insert = "";
			data =  $.parseJSON(data);
		
			$.each(data, function(count, artefact) {
				artefacts[artefact.lidoRecID_js] = artefact;
    			artefacts_to_insert  += "<div class='artefact-tile' style='background-image:url(" + "<?php echo $this->webroot; ?>img/artefacts/medium/" + artefact.lidoRecID + "/0.jpeg" + ");' id='artefact-tile-" + artefact.lidoRecID_js + "' data-lido-rec-id=" + artefact.lidoRecID_js + "><i class='fa fa-compress fa-lg size-btn'></i><div class='info'>i</div><div class='title'>" + artefact.title + "</div></div>";

    			var image = new Image();
		        $(image).error(function(e) {
		            var value = 'artefact-tile-' + artefact.lidoRecID_js;
		            $('#' + value).css('background-image', "");
					$('#' + value).css('background-image' , 'url("<?php echo $this->webroot; ?>img/not-found.png")');
					$('#' + value).css('background-color', '#1a1a1a');
					$('#' + value).css('background-size', '80%');
					$('#' + value).css('background-repeat', 'no-repeat');
		        });
		        
		        image.src = "<?php echo $this->webroot . 'img/artefacts/medium/';?>" + artefact.lidoRecID + "/0.jpeg";
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

	$('#first-tile').css('background-image', 'url("<?php echo $this->webroot . "img/artefacts/large/" . $artefact["lidoRecID"]; ?>/0.jpeg")');

	// full screen - toggle full view or zoomed in view
	$(document.body).on('click', '.full-screen' ,function(e){

		if($(event.target).attr('class') !== 'info'){
			$(this).toggleClass('show-all');
			$(this).find('.size-btn').toggleClass('fa-expand');
			$(this).find('.size-btn').toggleClass('fa-compress');

			$('html, body').animate({
		    	scrollTop: $(this).offset().top
			}, 500);
		}
	});

	// Artefact Click
	$(document.body).on('click', '.artefact-tile' ,function(e){
		// convert to full screen
		$(this).toggleClass('full-screen');
		$(this).toggleClass('artefact-tile');

		var artefact = artefacts[$(this).attr('data-lido-rec-id')];

		var image = new Image();
		image.src = artefact.images.medium;

		if(image.height > image.width)
		{
			$(this).toggleClass('show-all');
			$(this).find('.size-btn').toggleClass('fa-expand');
			$(this).find('.size-btn').toggleClass('fa-compress');
		}

		// $(this).toggleClass('show-all');
		
		$('html, body').animate({
	    	scrollTop: $(this).offset().top
		}, 500);


		if($(this).css('background-image').indexOf('not-found.png') == -1)
		{
			// load image
			$(this).css('background-image', 'url("' + artefacts[$(this).attr('data-lido-rec-id')].images.large + '")');
		}
		else
		{
			$(this).css('background-size','auto 100%');
		}

		$.ajax('record_click', {
			data: {lidoRecID:artefact.lidoRecID},
			async: false,
			success: function(data) {
				console.log('logged click');
			},
			error: function(e) {
				console.log(e);
			}
		});
	});

	$(document.body).on('click', '.info' ,function(e){
		var artefact = artefacts[$(this).parent().attr('data-lido-rec-id')];

		if($(this).parent().next().attr('class') != 'description-wrapper'){

			// var description_view = '<div class="description-wrapper">';

			// console.log(artefact);

			// console.log(artefact.descriptiveMetadata.eventWrap.eventSet.event.eventType);

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

			console.log(artefact.terms);
			if(artefact.terms.length > 0)
			{
				terms_string = "<div><span class='artefact-text-title'>Terms</span><span class='artefact-text'><ul>";
				$.each(artefact.terms, function(key, term){
					terms_string += '<li>' + term + '</li>';
				});
				terms_string += '</ul></span></div>';
			}

			$(this).parent().after();

			$(this).parent().after('<div class="description-wrapper">'+
			'<div class="description">' +
					'<div><span class="artefact-text-title">Title(s)</span><span class="artefact-text">' + artefact.title + '</span></div>'+
					'<div><span class="artefact-text-title">Collection</span><span class="artefact-text">' + artefact.descriptiveMetadata.objectClassificationWrap.classificationWrap.classification[2].term + '</span></div>'+
					event_string +
					terms_string +

					'<div><span class="artefact-text-title">Copyright</span><span class="artefact-text">Tyne and Wear Museum Archives</span></div>'+
			'</div>'+
			'</div>');
		}

		$('html, body').animate({
	    	scrollTop: $(this).parent().next().offset().top - ($(window).height() / 2)
		}, 500);
	});

	// Artefact Hover
	$(document.body).on('mouseenter mouseleave', '.artefact-tile' ,function(e){
		$(this).find('.title').slideToggle(400);
	});

	$(document.body).on('error', function (e) {
	    console.log('image error: ' + this.src);
	});

	// window.addEventListener("beforeunload", function (e) {
	//   var confirmationMessage = "Do you want to end the session?";

	//   (e || window.event).returnValue = confirmationMessage; //Gecko + IE
	//   return confirmationMessage;                            //Webkit, Safari, Chrome
	// });

	$(window).on('unload', function(){
         $.ajax('finish_session', {
			data: {},
			async: false,
			success: function(data) {
				console.log('finished session');
			},
			error: function(e) {
				console.log(e);
			}
		});
	});

	$(window).scroll(function(e) {
		randomness = 0;
		var delay_in_milliseconds = e.timeStamp - previous_event_timestamp;
	    if(delay_in_milliseconds > 800)
	    {
	    	var speed_in_px = ($(window).scrollTop() - previous_top_in_px) / delay_in_milliseconds;
	    	// console.log(delay_in_milliseconds);
	    	// console.log(speed_in_px);

	    	if(Math.abs(speed_in_px) > 3)
	    	{
	    		randomness = 2;
	    		$('.scroll_speed').css("background-color", "#6c6");
	    		// console.log(speed_in_px);
	    	}
	    	else if(Math.abs(speed_in_px) > 1)
	    	{
	    		randomness = 1;
	    		$('.scroll_speed').css("background-color", "orange");	
	    		// console.log(speed_in_px);
	    	}
	    	else
	    	{
	    		randomness = 0;
	    		$('.scroll_speed').css("background-color", "#e44");
	    	}

	    	previous_event_timestamp = new Date().getTime();
	    	previous_top_in_px = $(window).scrollTop();
	    }

		clearTimeout($.data( this, "scrollCheck" ));
		$.data(this, "scrollCheck", setTimeout(function() {
			randomness = 0;
			$('.scroll_speed').css("background-color", "red");
		}, 500));

		if($(window).scrollTop() >= ($(document).height() - ($(window).height() + ($(window).height() * 0.85) ))){
			if(fetch_more_tiles == true){
				fetch_more_tiles = false;
	    		fetch_more(randomness);
			}
	    }

	});

	fetch_more(0);
});
</script>