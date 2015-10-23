function zoom_artefact(){
	var artefact_tile = $(this).closest('.full-screen');
	
	// see if we're inside a carousel and change what we're modifying

	if($(artefact_tile).find('.carousel').length)
	{
		artefact_tile = $(artefact_tile).find('.active');
	}

	var current_zoom_level;

	if($(artefact_tile).attr('zoom-level') === undefined)
	{	
		if($(artefact_tile).css('background-size') == 'auto 100%')
		{
			$(artefact_tile).attr('zoom-level', 1);
			current_zoom_level = 1;
		}
		else
		{
			$(artefact_tile).attr('zoom-level', 2);
			current_zoom_level = 2;	
		}
	}
	else
	{
		current_zoom_level = $(artefact_tile).attr('zoom-level');
	}

	var updated_zoom_level = parseInt(parseInt($(artefact_tile).attr('zoom-level')) + 1);

	switch(parseInt(current_zoom_level)) {
		case 1:
			$(artefact_tile).css('background-size', 'cover');
			$(artefact_tile).attr('zoom-level', updated_zoom_level);
			break;
		case 2:
			$(artefact_tile).css('background-size', '145%');
			$(artefact_tile).attr('zoom-level', updated_zoom_level);
			break;
		case 3:
			$(artefact_tile).css('background-size', '165%');
			$(artefact_tile).attr('zoom-level', updated_zoom_level);
			break;	
		default:
			$(artefact_tile).css('background-size', 'auto 100%');
			$(artefact_tile).attr('zoom-level', 1);
			break;
	}

	return false;
}

function toggle_info(){
	var artefact = artefacts[$(this).parent().attr('data-lido-rec-id')];

	$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
       $('html, body').stop();
   	});

	// if the info window isn't open, open it
	if($(this).parent().next().attr('class') != 'description-wrapper'){
						
			var event_string = "";

			if(artefact.events.length > 0)
			{
				event_string = "<div><span class='artefact-text-title'>Details</span><span class='artefact-text'>";
				$.each(artefact.events, function(count, event_record){
					event_string += "<span class='subtitle'>" + event_record.event.eventType.term + "</span>";

					var event_description = "<span class='text'><ul>";

					if(event_record.event.periodName)
					{
						event_description += "<li>" + event_record.event.periodName.term + "</li>";	
					}

					if(event_record.event.eventDate)
					{
						if($.isArray(event_record.event.eventDate))
						{
							$.each(event_record.event.eventDate, function(count, event_record){
								event_description += "<li>" + event_record.displayDate + "</li>";
							});
						}
						else
						{
							if(event_record.event.eventDate.displayDate.length > 0)
							{
								event_description += "<li>" + event_record.event.eventDate.displayDate + '</li>';
							}
						}
					}

					if(event_record.event.eventPlace)
					{

						if(event_record.event.eventPlace.appellationValue)
						{
							event_description += "<li>" + event_record.event.eventPlace.appellationValue + '</li>';
						}

						if(event_record.event.eventPlace.namePlaceSet)
						{
							event_description += "<li>" + event_record.event.eventPlace.namePlaceSet.appellationValue + '</li>';
						}

						if(event_record.event.eventPlace.place)
						{
							if(event_record.event.eventPlace.place.namePlaceSet)
							{
								event_description += "<li>" + event_record.event.eventPlace.place.namePlaceSet.appellationValue + "</li>";
							}

							if(event_record.event.eventPlace.place.appellationValue)
							{
								event_description += "<li>" + event_record.event.eventPlace.place.appellationValue + "</li>";
							}
						}
						else
						{
							$.each(event_record.event.eventPlace, function(count, event_place){
								if(event_place.appellationValue)
								{
									event_description += "<li>" + event_place.appellationValue + '</li>';
								}

								if(event_place.namePlaceSet)
								{
									event_description += "<li>" + event_place.namePlaceSet.appellationValue + '</li>';
								}
							});
						}
					}


					if(event_record.event.eventActor)
					{
						$.each(event_record.event.eventActor, function(count, event_actor){

							event_description += "<li>";

							if(event_actor.actor)
							{
								event_description += event_actor.actor.nameActorSet.appellationValue;
							}

							if(event_actor.roleActor)
							{
								event_description += " (" + event_actor.roleActor.term + ")";
							}

							if(event_actor.actorInRole)
							{

								if(event_actor.actorInRole.nameActorSet)
								{
									event_description += event_actor.actorInRole.nameActorSet.appellationValue;
								}

								if(event_actor.actorInRole.actor)
								{
									event_description += event_actor.actorInRole.actor.nameActorSet.appellationValue;
								}

								if(event_actor.actorInRole.roleActor)
								{
									event_description += " (" + event_actor.actorInRole.roleActor.term + ")";
								}
							}

							event_description += "</li>";


						});
						// event_description += "<li>" + event_record.event.eventActor.actorInRole.actor.nameActorSet.appellationValue + "</li>";

					}

					if(event_record.event.place)
					{
						if(event_record.event.place.namePlaceSet)
						{
							event_description += "<li>" + event_record.event.place.namePlaceSet.appellationValue + "</li>";
						}
					}

					event_description += "</ul></span>";
					event_string += event_description;
				});

				event_string += '</span></div>';
			}

			var json_events = "<div><code>";

			$.each(artefact.events, function(count, event_record){
				json_events += JSON.stringify(event_record);
			});

			json_events += "</code></div>";

			var terms_string = "";

			if(artefact.terms.length > 0)
			{
				terms_string = "<div><span class='artefact-text-title'>Keywords</span><span class='artefact-text'><ul>";
				$.each(artefact.terms, function(key, term){
					terms_string += '<li>' + term + '</li>';
				});
				terms_string += '</ul></span></div>';
			}

			var descriptions = "";

			if(artefact.descriptions.length > 1)
			{
				descriptions = "<div><span class='artefact-text-title'>Description(s)</span><span class='artefact-text-description'><ul>";
				$.each(artefact.descriptions, function(key, description){
					if(description != artefact.title)
					{
						descriptions += "<li>" + description + "</li>";
					}
				});
				descriptions += '</ul></span></div>';
			}

			var work_id_string = "<div><span class='artefact-text-title'>Unique Object Number</span><span class='artefact-text'><a href='http://collectionssearchtwmuseums.org.uk/resultslist.html?collection=&searchTerm=" + encodeURIComponent(artefact.descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID) + "&imageSearch=false&search=true' target='_blank'>" + artefact.descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID + "</a></span></div>";

			$(this).parent().after('<div class="description-wrapper">'+
			'<div class="description">' +
					'<div><span class="artefact-text-title">Title(s)</span><span class="artefact-text">' + artefact.title + '</span></div>'+
					'<div><span class="artefact-text-title">Collection</span><span class="artefact-text">' + artefact.descriptiveMetadata.objectClassificationWrap.classificationWrap.classification[2].term + '</span></div>'+
					descriptions + 
					event_string +
					terms_string +
					work_id_string +
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
}

$(document).ready(function(){
	$(document.body).on('click', '.info', toggle_info);
	$(document.body).on('click', '.size-btn', zoom_artefact);
});