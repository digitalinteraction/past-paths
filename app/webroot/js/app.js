var history_bar_visible = false;
var artefacts = {};
var artefacts_lookup = [];
var img_errors = [];
var fetch_more_tiles = true;
var randomness = 0;
var keep_alive_flag = true;
var lastScrollTop = 0;
var fetch_request_count = 0;
var thehistory = [];
var map_displayed = false;

var transition;
var delay = 50; // ticker delay for scroll indicator
var timeout = null;
var prompt_scroll_shown = false;

artefacts[first_artefact.lidoRecID_js] = first_artefact;

var terms_scale = d3.scale.linear()
						  .domain([1, 30])
						  .range([10, 26]);

var lastScrollTop = 0;

var term_cloud_counter = 0;
var term_cloud_tick = true;

// terms cloud
var terms_list = {};
var terms_array = [];

var nav_bar_height = 0;

var has_completed_tutorial = undefined;

// survey vars
var has_survey_cookie = undefined;
var survey_shown = false;
var default_survey_timeout_in_millisecs = 90000;
var show_survey_artefact_clicked_threshold = 5;


// tracking
var u_id;
var is_scrolling = false;

var is_dragging = false;
var mouse_down = false;

// scroll prompt
var prompt_scroll_event_shown = false;

// var is_mac = <?php // echo $is_mac; ?>;


// function showHideTiles()
// {
// 	var remove_background_images = true;

// 	$('.artefact-tile').each(function(){
// 		// if an element is not in the view port, then remove the background style
// 		if($(this).visible(true) == false)
// 		{
// 			if(remove_background_images)
// 			{
// 				$(this).css('background-image', 'none');
// 			}
// 		}
// 		else
// 		{
// 			// if it is visible but needs to redraw the background because it's been removed previously, redraw it
// 			remove_background_images = false;
// 			if($(this).css('background-image') == 'none')
// 			{
// 				var artefact = artefacts[$(this).data('lido-rec-id')];
// 				console.log(get_img_url(artefact.images[0], 1));
// 				$(this).css('background-image', 'url(' + get_img_url(artefact.images[0], 1) + ')');
// 			}
// 		}
// 	});
// }

function toggle_share_links(){

	if($(this).parent().find('.item-share-links').is(":visible"))
	{
		$(this).parent().find('.item-share-links').hide();	
	}
	else
	{
		$(this).parent().find('.item-share-links').show();
		var artefact = artefacts[$(this).closest('.full-screen').data('lido-rec-id')];
    	$(this).parent().find('.share-link-item-url').val('http://collectionsdivetwmuseums.org.uk/share/a/' + artefact._id.$id);
	}

	return false;
}

function recordAction(type, data){
	$.ajax('t/action', {
		data: {u_id:u_id, _id:s_id, action:type, data:data},
		async: false,
		success: function(data) {
			
		},
		error: function(e) {
			console.log(e);
		}
	});
}

// records unique visits
function recordVisit(){

	// if the current user doesn't have unique tracking cookie
	// add one and update the browsing session
	u_id = Cookies.get('pastpaths_u_id');

	if(u_id === undefined)
	{
		u_id = Cookies.set('pastpaths_u_id', s_id);
	}
	
	$.ajax('t/visit', {
		data: {_id:s_id, u_id:u_id},
		success: function(data) {
		},
		error: function(e) {
			console.log(e);
		}
	});
}

// this sets the timeout function to display the survey
function startSurveyCountdown(){
	setTimeout(function(){
		showSurvey();
	}, default_survey_timeout_in_millisecs);
}

function showSurvey(){

	if(survey_shown == false && has_survey_cookie === undefined)
	{
		$('#survey').fadeIn().effect("bounce", { times: 2 }, "slow");

		survey_shown = true;
		Cookies.set('pastpaths_surveyed', '1');
		has_survey_cookie = Cookies.get('pastpaths_surveyed');

		setTimeout(function(){
			$('#survey').fadeOut(1000);
		}, 8000);
	}
}

function detectIE() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
       // IE 12 => return version number
       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
}

function toggleCloud(){
	// $('#terms_cloud').toggleClass('close-terms-cloud', 'slow');
	// $('#terms_cloud').slideToggle();

	if($('#terms_cloud').hasClass('close-terms-cloud'))
	{
		$('#terms_cloud').removeClass('close-terms-cloud', 'slow');
		// $('#terms_cloud').addClass('open-terms-cloud', 'slow');
		$('#terms_cloud').animate({
			height: '100%'
		}, 500);

		$('.terms-cloud-overview').fadeOut();
	}
	else
	{
		$('#terms_cloud').addClass('close-terms-cloud', 'slow');
		// $('#terms_cloud').removeClass('open-terms-cloud', 'slow', function(){
		$('#terms_cloud').animate({
			height: '35px'
		}, 500);
		// });
		$('.terms-cloud-overview').fadeIn();
	}
	
	if($('#terms_cloud').find($(".fa")).first().hasClass('fa-angle-down'))
	{
		$('#terms_cloud').find($(".fa")).removeClass('fa-angle-down').addClass('fa-angle-up');	
	}
	else
	{
		$('#terms_cloud').find($(".fa")).removeClass('fa-angle-up').addClass('fa-angle-down');		
	}
}

function updateCloud(scrolling_up){

	if(term_cloud_counter > 15 && term_cloud_tick == true)
	{
		term_cloud_counter = 0;
		terms_list = {};
		$('.terms-cloud-inner').html('');
		$('.terms-cloud-overview').html('');
		terms_array = [];

		// console.log($('div.within-viewport:last').data('lido-rec-id'));

		var last_visible_artefact_lido_id = $('div.within-viewport:last').data('lido-rec-id');

		if(last_visible_artefact_lido_id === undefined)
		{
			return false;
		}

		var index_of_last_visible_artefact = artefacts_lookup.indexOf(last_visible_artefact_lido_id);

		for (var i = (index_of_last_visible_artefact - 16); i <= index_of_last_visible_artefact; i++) {
			// loop through the lookup array to find the nth position in the object array
			var artefact = artefacts[artefacts_lookup[i]];
			
			if(artefact)
			{
				$.each(artefact.terms, function(index, term){
					if(term.length < 35 && term.length > 1)
					{
						if(terms_list[term] === undefined)
						{
							var artefact_id_list = [];
							artefact_id_list.push(artefact.lidoRecID_js);

							var term_object = {
								"term" : term,
								"count" : 1,
								"artefacts" : artefact_id_list
							};
					
							terms_list[term] = term_object;
							terms_array.push(term);
						}
						else
						{
							terms_list[term].count += 1;
							terms_list[term].artefacts.push(artefact.lidoRecID_js);
						}
					}
				});

				$.each(artefact.descriptions, function(index, description){
					if(description.length < 35 && description.length > 1)
					{
						if(terms_list[description] === undefined)
						{
							var artefact_id_list = [];
							artefact_id_list.push(artefact.lidoRecID_js);

							var term_object = {
								"term" : description,
								"count" : 1,
								"artefacts" : artefact_id_list
							};
					
							terms_list[description] = term_object;
							terms_array.push(description);
						}
						else
						{
							terms_list[description].count += 1;
							terms_list[description].artefacts.push(artefact.lidoRecID_js);
						}
					}
				});

				if(artefact.title.length < 35 && artefact.title > 1)
				{
					if(terms_list[artefact.title] === undefined)
					{
						var artefact_id_list = [];
						artefact_id_list.push(artefact.lidoRecID_js);

						var term_object = {
							"term" : artefact.title,
							"count" : 1,
							"artefacts" : artefact_id_list
						};
				
						terms_list[artefact.title] = term_object;
						terms_array.push(artefact.title);
					}
					else
					{
						terms_list[artefact.title].count += 1;
						terms_list[artefact.title].artefacts.push(artefact.lidoRecID_js);
					}
				}
			}	
		}



		var output = "";
		var most_frequent_term_count = 0;
		var most_frequent_term = "";
		$.each(terms_array, function(index, term){
			output += "<div><p class='terms-cloud-tag' data-tagsize='" + terms_scale(terms_list[term].count) +  "' style='font-size:" + terms_scale(terms_list[term].count) + "pt'>" + term + "</p></div>";

			if(terms_list[term].count > most_frequent_term_count)
			{
				most_frequent_term_count = terms_list[term].count;
				most_frequent_term = term;
			}

		});

		if($('#terms_cloud').hasClass('close-terms-cloud') == true)
		{
			$('.terms-cloud-overview').html(most_frequent_term);	
		}
		
		
		$('.terms-cloud-inner').html(output);
	}

	term_cloud_counter++;
}

function showpanel(){
	if (history_bar_visible)
	{
		recordAction('close_history_panel');

		history_bar_visible = false;
		$('#sidepanel').animate({left:-300}, function(){
			$('#sidepanel').toggleClass('open');
		});
	}
	else
	{
		recordAction('open_history_panel');
		history_bar_visible = true;
		$('#sidepanel').toggleClass('open');
		$('#sidepanel').animate({left:0});
	}
}

function slideFade(elem) {
   var fade = { opacity: 0, transition: 'opacity 0.2s' };
   elem.css(fade).slideUp();
}

function zoomto()
{
	$('#historypanel').scrollTo($('[data-lido-rec-id-history="'+$(this).data('lido-rec-id-history')+'"]'),500);
	$('body').scrollTo($('[data-lido-rec-id="'+$(this).data('lido-rec-id-history')+'"]'),500);
}

function add_history(a){
	// populate history tile element

	var history_item = '<div data-lido-rec-id-history="' + a.lidoRecID_js + '" class="history_artefact">';

	if(thehistory.length > 0)
	{
		history_item += '<span class="glyphicon glyphicon-remove remove-history-item"></span>';
	}

	history_item += '</div>';

	var img = $(history_item);
	img.css('background-image', "url('" + webroot + "img/artefacts/medium/" + a.lidoRecID + "/0.jpeg')");

	// //calculate distance...
	var thisone = $('[data-lido-rec-id="'+a.lidoRecID_js+'"]');

	var gap = $('<div class="gap"></div>');

	var insertafter = false;

	if (thehistory.length > 1)
	{
		var prevone = thisone.prevAll('.full-screen').first();
		if (prevone.length > 0)
		{
			var middle = prevone.nextUntil(thisone);
			var distance = Math.abs(thisone.offset().top - prevone.offset().top);

			gap.css('height',Math.sqrt(middle.length * 50));

			middle.each(function()
			{
				if($(this).hasClass('artefact-tile'))
				{
					var lido_id = $(this).data('lido-rec-id');
					var sm = $('<div class="img-sm"></div>');
					sm.css('background-image', 'url("' + webroot + 'img/artefacts/medium/' + artefacts[lido_id].lidoRecID + '/0.jpeg")');
					gap.append(sm);
				}
			});

			//id of the history item to insert just after:
			var beforeid = prevone.data('lido-rec-id');
			if($('[data-lido-rec-id-history='+beforeid+']').length)
			{
				img.insertAfter($('[data-lido-rec-id-history='+beforeid+']')).hide().fadeIn(800);
				gap.insertBefore(img);
			}
			else
			{
				$('#historyinner').append(img);
				gap.insertBefore(img);
			}
		}
		else
		{
			// do clever stuff to work out where to put it
			$('#historyinner div:first-child').after(img);
			img.hide().fadeIn(800);
			// .after(img);
		}
	}
	else
	{
		// img.css('margin-bottom', '15px')
		img.hide().fadeIn(800);
		$('#historyinner').append(img);
	}

	thehistory.push(a);

	// scroll to bottom
	$("#historypanel").animate({ scrollTop: $(img).offset().top});
}

// size 0 = small, 1 = med, 2 = large
function get_img_url(img, size)
{
	var sizes = ['small', 'medium', 'large'];
	return root_url + webroot + 'img/artefacts/' + sizes[size] + '/' + img;
}

function fetch_more(randomness) {
	fetch_request_count ++;
	
	actionData = {};
	actionData.offset = record_offset;
	actionData.randomness = randomness;
	recordAction('fetch_more_event', actionData);

	// fetch some records
	$.ajax('artefacts/fetch_more', {
		data: {offset:record_offset, randomness_level:randomness, _id:s_id},
		success: function(data) {
			// attach the response to the artefact container
			var artefacts_to_insert = "";
			data =  $.parseJSON(data);
		
			$.each(data.results, function(count, artefact_record) {
				// check if images has been set - bug
				if(artefact_record.images)
				{
					artefacts[artefact_record.lidoRecID_js] = artefact_record;
					artefacts_lookup.push(artefact_record.lidoRecID_js);

					artefacts_to_insert  += "<div class='artefact-tile' style='background-image:url(" + get_img_url(artefact_record.images[0]["url"], 1) + ");' id='artefact-tile-" + artefact_record.lidoRecID_js + "' data-lido-rec-id='" + artefact_record.lidoRecID_js + "'><div class='artefact-controls share-item' aria-hidden='true'><i class='fa fa-share-alt'></i></div><div class='glyphicon glyphicon-zoom-in size-btn artefact-controls'></div><div class='info artefact-controls'><i class='fa fa-info'></i></div><div class='title artefact-controls'>" + artefact_record.title + "</div><div class='item-share-links'><div class='item-share-links-wrapper'><input type='text' readonly='readonly' value='' class='share-link-item-url' onClick='this.setSelectionRange(0, this.value.length)'/><i class='fa fa-facebook fa-3x share-btn share-item-fb share-fb'></i><i class='fa fa-twitter fa-3x share-btn share-item-twitter share-twitter'></i><i class='fa fa-envelope-o fa-3x share-btn share-item-email share-email'></i><i class='fa fa-link fa-3x share-btn share-item-link share-link'></i></div></div>";

					if(artefact_record.images.length > 1)
					{
						artefacts_to_insert += "<div class='image-counter'><span>" + artefact_record.images.length + "</span><i class='fa fa-camera-retro'></i></span></div>";
					}

					artefacts_to_insert += '<span class="glyphicon glyphicon-remove remove-tile artefact-controls"></span>';

					artefacts_to_insert += "</div>";
				}
			});
	
			$('#artefact-container').append(artefacts_to_insert);
			
			fetch_more_tiles = true;

			record_offset = data.record_offset;
		},
		error: function(e) {
			console.log(e);
		}
	});
}


function show_artefact_tile(){

	if(thehistory.length == 1 && has_completed_tutorial === undefined)
	{
		$('.prompt-scroll-event').fadeOut();
		$('.prompt-scroll-up').delay(1000).fadeIn().effect("bounce", { times: 2 }, "slow").promise().done(
			function(){
				prompt_scroll_shown = true;
				history_bar_visible = false;
				showpanel();
				$('#show-hide-history-btn').tooltip('show');
				$('#show-hide-history-btn').tooltip('disable');
		});
	}


	if(thehistory.length == show_survey_artefact_clicked_threshold){
		showSurvey();
	}

	// convert to full screen
	$(this).toggleClass('artefact-tile');
	$(this).toggleClass('full-screen');

	$(this).removeClass('highlight-artefact');
	
	var artefact = artefacts[$(this).attr('data-lido-rec-id')];
	
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

		imgs_to_append += "<div class='item active full-screen-carousel";

		// check if img is portrait, show all if true
		if(artefact.images[0].height > artefact.images[0].width)
		{
			imgs_to_append += " show-all";
		}

		imgs_to_append += "' style='background-image: url(" + get_img_url(artefact.images[0]["url"], 2) + ")'></div>";
		for (var i = 1; i < artefact.images.length; i++) {
			imgs_to_append += '<div class="item full-screen-carousel';

			if(artefact.images[i].height > artefact.images[i].width)
			{
				imgs_to_append += ' show-all';
			}

			imgs_to_append += '" style="background-image: url(' + get_img_url(artefact.images[i]["url"], 2) + ')"></div>';
		}

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

		// if image is portrait then show full image
		if(artefact.images[0].height > artefact.images[0].width)
		{
			$(this).toggleClass('show-all');
		}

		$(this).css('background-image', 'url("' + get_img_url(artefact.images[0]["url"], 2) + '")');
	}

	show_hide_controls(artefact);

	actionData = {};
	actionData.lidoRecID = artefact.lidoRecID;
	recordAction('artefact_click', actionData);

	$.ajax('session/record_click', {
		data: {lidoRecID:artefact.lidoRecID, _id:s_id},
		async: false,
		success: function(data) {
			
		},
		error: function(e) {
			console.log(e);
		}
	});

	add_history(artefact);
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
	    		$(artefact_tile).find('.item-share-links').fadeOut();
	    	}
	    }, 2500, $(this));
	}).mouseleave(function() {
	    clearTimeout(i);
	    // $("#menu").hide();  
	});
}

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

function toggle_full_screen(){
	$(this).toggleClass('show-all');

	actionData = {};
	actionData.lidoRecID = artefacts[$(this).data('lidoRecId')].lidoRecID;

	recordAction('zoom_button_click', actionData);

	// $(this).find('.size-btn').toggleClass('glyphicon-zoom-out');
	// $(this).find('.size-btn').toggleClass('glyphicon-zoom-in');

	$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
       $('html, body').stop();
   	});

	$('html, body').animate({
    	scrollTop: $(this).offset().top
	}, 500, function(){
   		$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
	});
}

function toggle_full_screen_carousel(){
	$(this).toggleClass('show-all');
	actionData = {};
	actionData.lidoRecID = artefacts[$(this).parent().parent().parent().data('lidoRecId')].lidoRecID;
	recordAction('zoom_button_click', actionData);

	// $(this).find('.size-btn').toggleClass('glyphicon-zoom-out');
	// $(this).find('.size-btn').toggleClass('glyphicon-zoom-in');

	$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
       $('html, body').stop();
   	});

	$('html, body').animate({
    	scrollTop: $(this).offset().top
	}, 500, function(){
   		$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
	});
}

function toggle_map(){
	$('.map_explorer_svg').remove();
	$('.map_explorer_svg').empty();

	if($('#map_explorer').is(':visible'))
	{
		recordAction('close_map_view');
		$('#map_explorer').hide();
	}
	else
	{
		recordAction('open_map_view');
		if(graph !== undefined)
		{
			graph.removeAllNodes();
			graph.removeAllLinks();
		}

		$('#map_explorer').show();
		show_map();
	}
}

function remove_history(artefact){
	var item = $('#historyinner').find('[data-lido-rec-id-history="' + artefact.lidoRecID_js + '"]');

	if(thehistory.length > 0){
		if(item.prev().hasClass('gap'))
		{
			// item.prev().fadeOut(1000, function(){ $(this).remove(); });
			slideFade(item.prev());
		}
	}

	slideFade(item);
	// item.fadeOut(1000, function(){ $(this).remove(); });
	
	var item_to_remove = -1;

	for (var i = 0; i < thehistory.length; i++) {
		if(thehistory[i]['lidoRecID_js'] == artefact.lidoRecID_js){
			item_to_remove = i;
		}
	}

	if(item_to_remove > -1)
	{
		thehistory.splice(item_to_remove, 1);
	}

	$.ajax('session/remove_artefact_from_session', {
		data: {lidoRecID:artefact.lidoRecID, _id:s_id},
		async: false,
		success: function(data) {
			
		},
		error: function(e) {
			console.log(e);
		}
	});

	// handle the gap
	return false;
}

function remove_history_item() {
	var artefact = artefacts[$(this).parent().attr('data-lido-rec-id-history')];
	recordAction('remove_artefact_from_historybar', artefact.lidoRecID);

	remove_history(artefact);
	remove_tile(artefact, true, true);
	return false;
}

function remove_tile(artefact, animate_close, was_closed_from_historybar){

	if(was_closed_from_historybar === undefined)
	{
		actionData = {};
		actionData.lidoRecID = artefact.lidoRecID;
		recordAction('remove_artefact', actionData);
	}

	var artefact_tile = $('#artefact-tile-' + artefact.lidoRecID_js);

	artefact_tile.find(".artefact-controls").fadeOut();
	artefact_tile.find(".item-share-links").fadeOut();
	artefact_tile.unbind('mousemove');
	artefact_tile.unbind('mouseleave');

	artefact_tile.removeClass('full-screen');
	artefact_tile.removeClass('show-all');
	artefact_tile.addClass('artefact-tile');

	// hide info window
	if(artefact_tile.next().attr('class') == 'description-wrapper'){
		artefact_tile.next().fadeOut();
	}

	// remove full screen on carousel items
	if(artefact.images.length > 1)
	{
		$("#" + artefact.lidoRecID_js + "-carousel").remove();
		// reset background image
		artefact_tile.css('background-image', 'url("' + get_img_url(artefact.images[0]["url"], 1) + '")');
	}

	artefact_tile.addClass('highlight-artefact');

	setTimeout(function(){
		artefact_tile.addClass("removed");
    	artefact_tile.removeClass('highlight-artefact');
	}, 3000);

	if(animate_close)
	{
		$('html, body').animate({
			scrollTop: artefact_tile.next().offset().top - ($(window).height() / 2)
		}, 500, function(){
			$("html, body").off("scroll mousedown DOMMouseScroll mousewheel keyup");
		});
	}

	// remove from history side panel
	remove_history(artefact);
	// send get request to endpoint to remove item from history

	

	return false;
}

function show_map(){
	recordAction('open_map_view');
	
	graph = new myGraph("#map_explorer");

	function pulse() {
	    var circle = vis.select("circle");
	    (function repeat() {
	        circle = circle.transition()
	            .duration(2000)
	            .attr("stroke-width", 8)
	            .attr("r", 25)
	                .transition()
	                .duration(2000)
	                .attr('stroke-width', 0)
	                .attr("r", 200)
	                .ease('sine')
	                .each("end", repeat);
	    })();
	}

	var loading_circle = vis.append('circle')
				.attr('class', 'loading-circle')
                .attr("stroke-width", 10)
                .attr('r', function(d, i) { return 10; })
                .attr('cx', function() { return window.innerWidth / 2; })
                .attr('cy', function() { return window.innerHeight / 2; })
                .each(pulse);

	// fetch some data
	d3.json("session/session_data_map?s_id=" + s_id, function(error, response) {

  		loading_circle.transition().duration(50).style('opacity', 0).remove();

  		for (var i = 0; i < response.artefact_nodes.length; i++) {
			graph.addNode(response.artefact_nodes[i].node_id, response.artefact_nodes[i].lidoRecID, "" , response.artefact_nodes[i].weight, 'artefact');
		}

		for (var i = 0; i < response.nodes.length; i++) {
			// graph.addNode(response.nodes[i].node_id, "", response.nodes[i].keyword, response.nodes[i].artefact_count + 1, 'keyword');
			graph.addNode(response.nodes[i].node_id, "", response.nodes[i].keyword, response.nodes[i].weight, 'keyword');
		}


		for (var i = 0; i < response.links.length; i++) {
		    if(graph.findNode(response.links[i].source) !== undefined && graph.findNode(response.links[i].target) !== undefined)
		    {
		        graph.addLink(response.links[i].source, response.links[i].target, Math.log(Math.floor(response.links[i].value)), response.links[i].type); 
		    }
		}

		graph.updateGraph();

    });

	map_displayed = true;
}

function toggle_info(){
	var artefact = artefacts[$(this).parent().attr('data-lido-rec-id')];

	$("html, body").on("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
       $('html, body').stop();
   	});

	// if the info window isn't open, open it
	if($(this).parent().next().attr('class') != 'description-wrapper'){
			actionData = {};
			actionData.lidoRecID = artefact.lidoRecID;
			recordAction('information_button_click', actionData);
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

			if(artefact.descriptions.length > 0)
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

function share(windowUri){
	var centerWidth = (window.screen.width - 600) / 2;
    var centerHeight = (window.screen.height - 440) / 2;

    newWindow = window.open(windowUri, 'Share', 'resizable=1,width=' + 600 + ',height=' + 440 + ',left=' + centerWidth + ',top=' + centerHeight);
    newWindow.focus();
    return newWindow.name;
}

// polling to keep session alive
function keep_alive(){
	$.ajax('session/keep_alive', {
		data: {_id : s_id},
		success: function(data) {
			setTimeout(keep_alive, 10000);
		},
		error: function(e) {
			console.log(e);
		}
	});
}


$(document).ready(function(){
	var previous_top_in_px = $(window).scrollTop();
	var previous_event_timestamp = new Date().getTime();
	nav_bar_height = $('#fixed-nav-bar').height();

	add_history(first_artefact);

	// window.history.pushState('share', 'Share', document.location.href + s_id);

	$('[data-toggle="tooltip"]').tooltip();

	$("div.navbar-fixed-top").autoHidingNavbar();

	$('#nav-hit-area').on('mouseenter', function(){
		$("div.navbar-fixed-top").autoHidingNavbar('show');
	});

	// check if we have a cookie
	has_completed_tutorial = Cookies.get('pastpaths_viewed_tutorial');

	has_survey_cookie = Cookies.get('pastpaths_surveyed');

	if(has_survey_cookie === undefined)
	{
		startSurveyCountdown();
	}

	if(has_completed_tutorial === undefined)
	{
		Cookies.set('pastpaths_viewed_tutorial', '1');
		setTimeout(function(){
			if($('.first-tile').is(':within-viewport'))
			{
				$('.prompt-scroll-down').fadeIn().effect("bounce", { times: 2 }, "slow");
				prompt_scroll_shown = true;	
			}
		}, 3000);
	}

	recordVisit();

	$(document.body).on('click', '.left', function(){
		actionData = {};
		actionData.lidoRecID = artefacts[$(this).parent().parent().data('lidoRecId')].lidoRecID;
		recordAction('carousel', actionData);
		$(this).parent().carousel('prev');

		return false;
	});

    $(document.body).on('click', '.right', function(){
		actionData = {};
		actionData.lidoRecID = artefacts[$(this).parent().parent().data('lidoRecId')].lidoRecID;
		recordAction('carousel', actionData);

		$(this).parent().carousel('next');
		return false;
	});

	$(document.body).on('click', '.terms-cloud-close', function(){
		toggleCloud();
	});

	$(document.body).on('click', '.terms-cloud-tag', function(){
		var term = terms_list[$(this).text()];

		if(term !== undefined)
		{	
			term_cloud_tick = false;
			$(window).scrollTo($('[data-lido-rec-id="' + term.artefacts[0] + '"]').offset().top - (nav_bar_height + 35), 500, function(){
				term_cloud_tick = true;
			});
		}
	});


	$(document.body).on('mouseenter', '.terms-cloud-tag', function(){

		$(this).css('font-size', (parseInt($(this).css('fontSize')) * 1.25) + 'px');
		$('div.highlight-artefact').removeClass('highlight-artefact');

		var term = terms_list[$(this).text()];

		if(term !== undefined)
		{
			$.each(term.artefacts, function(index, lidoRecID_js){
				$('[data-lido-rec-id="' + lidoRecID_js + '"]').toggleClass('highlight-artefact');
			});
		}
	});

	$(document.body).on('mouseleave', '.terms-cloud-tag', function(){
		$(this).css('font-size', (parseInt($(this).css('fontSize')) / 1.25) + 'px');
		$('div.highlight-artefact').removeClass('highlight-artefact');
	});

	$(document.body).on('click', '.survey-nope', function(){
		$('#survey').fadeOut();
		return false;
	});

	$(document.body).on('click', '#survey', function(){
		$('#survey').html('<i class="fa fa-heart-o fa-4x"></i><h3>Thanks!</h3>');
		setTimeout(function(){
			$('#survey').fadeOut(1000);
		}, 1200);
		share('https://docs.google.com/forms/d/1-rCfAX0pnIpqAqvHkUKbz8oINPEsmZ0Q7OrbtM8kfW0/viewform');
	});


	// $(document.body).on('mousedown', '.full-screen', function(){
	// 	is_dragging = false;
	// 	mouse_down = true;
	// });

	// $(document.body).on('mousemove', '.full-screen', function(e){
	// 	if(mouse_down = true)
	// 	{
	// 		// get current xy of background

	// 		// move to new
	// 		// e.pageX, y: e.pageY
	// 		is_dragging = true;
	// 		console.log(is_dragging);
	// 	}
	// });

	// $(document.body).on('mouseup', '.full-screen', function(){
	// 	is_dragging = false;
	// 	mouse_down = false;
	// });

	// full screen - toggle full view or zoomed in view
	$(document.body).on('click', '.full-screen', toggle_full_screen);

	// seperate logic for carousel full screenage
	$(document.body).on('click', '.full-screen-carousel', toggle_full_screen_carousel);

	$(document.body).on('click', '.size-btn', zoom_artefact);

	// Artefact Click
	$(document.body).on('click', '.artefact-tile', show_artefact_tile);

	$(document.body).on('click', '.info', toggle_info);

	$(document.body).on('click', '.history_artefact', zoomto);

	// close the current artefact tile and remove it from history
	$(document.body).on('click', '.remove-tile', function(e) {
		var artefact = artefacts[$(this).parent().attr('data-lido-rec-id')];
		remove_tile(artefact, true);
		return false;
	});

	// Artefact Hover
	$(document.body).on('mouseenter', '.artefact-tile' ,function(e){
		// stops animation firing
		// also allows us to track when the user hovers over an artefact
		if(is_scrolling == false)
		{	
			var artefact = artefacts[$(this).closest('.artefact-tile').data('lido-rec-id')];
			var actionData = {};
			actionData.lidoRecID = artefact.lidoRecID;
			recordAction('artefact_tile_hover', actionData);
			$(this).find('.title').slideDown(400);
		}
	});

	$(document.body).on('mouseleave', '.artefact-tile' ,function(e){
		// stops animation firing
		// also allows us to track when the user hovers over an artefact
		if(is_scrolling == false)
		{	
			$(this).find('.title').slideUp(400);
		}
	});

	$(document.body).on('error', function (e) {
	    console.log('image error: ' + this.src);
	});

	$(document.body).on('click', '.remove-history-item', remove_history_item);

	// esc pressed hide map
	$(document).keyup(function(e) {
		// esc key pressed, hide map view
		if (e.keyCode == 27 && map_displayed) { toggle_map(); recordAction('close_map_view'); map_displayed = false;}
	});

	$(window).bind("scroll", function(e) {
		var st = $(this).scrollTop();

		// randomness = 0;
		var delay_in_milliseconds = e.timeStamp - previous_event_timestamp;
	    if(delay_in_milliseconds > 800)
	    {
	    	var speed_in_px = ($(window).scrollTop() - previous_top_in_px) / delay_in_milliseconds;

	    	if(Math.abs(speed_in_px) > 3)
	    	{
	    		random = d3.random.normal(0, 1);
	    		randomness = 2;
	    	}
	    	else if(Math.abs(speed_in_px) > 1)
	    	{
	    		random = d3.random.normal(0, 0.8);
	    		randomness = 1;
	    	}
	    	else
	    	{
	    		random = d3.random.normal(0, 0.05);
	    		randomness = 0;
	    	}

	    	previous_event_timestamp = new Date().getTime();
	    	previous_top_in_px = $(window).scrollTop();
	    }

		clearTimeout($.data( this, "scrollCheck" ));

		$.data(this, "scrollCheck", setTimeout(function() {
			randomness = 0;
		}, 800));

		if($(window).scrollTop() >= ($(document).height() - ($(window).height() + ($(window).height() * 0.65) ))){
			if(fetch_more_tiles == true){
				fetch_more_tiles = false;
	    		fetch_more(randomness);
			}
	    }

	    if(prompt_scroll_shown){
	    	$('.prompt-scroll').fadeOut();

	    	prompt_scroll_shown = false;


	    	if(prompt_scroll_event_shown == false)
	    	{
	    		$('.prompt-scroll-event').fadeIn();

	    		setTimeout(function(){
					$('.prompt-scroll-event').fadeOut();
					prompt_scroll_event_shown = true;
	    		}, 5000);
	    	}
	    }


	 //    $.data(this, "scrollTermsCheck", setTimeout(function() {
		// 	if (st > lastScrollTop){
		// 		updateCloud(false);
		// 	} else {
		// 		updateCloud(true);
		// 	}
		// }, 25));
	
		if(artefacts_lookup.length > 16)
		{
			updateCloud();
		}

		lastScrollTop = st;

		$('div.artefact-tile').removeClass('within-viewport').filter(':within-viewport').addClass('within-viewport');

	});


	$(window).on("scrollstart", function() {	
		is_scrolling = true;
  	})
		
	$(window).on("scrollstop", function() {
		is_scrolling = false;
	});

	
	// explore btn event
	$(document.body).on('click', '#explore_btn', toggle_map);

	// close btn event
	$(document.body).on('click', '.close' ,function(e){
		$('#map_explorer').hide();
		map_displayed = false;
		recordAction('close_map_view');
	});

	$(document.body).on('click', '#show-hide-history-btn', showpanel);

	$(document.body).on('click', '.share-item', toggle_share_links);

	$('.share-history-fb').click(function(){

		recordAction('share_history_facebook');
		// $('meta[property="og:image"]').remove();
    	// $('head').append('<meta property="og:image" content="' + root_url + get_img_url(thehistory[thehistory.length - 1]["images"][0]["url"], 2) + '">' );
		
		FB.ui({
		  method: 'feed',
		  link: document.location.href + 'share/' + s_id,
		  picture: get_img_url(thehistory[thehistory.length - 1]["images"][0]["url"], 2)
		}, function(response){
			console.log(response);
		});

		return false;
	});

	$('.share-history-twitter').click(function(){
		recordAction('share_history_twitter');
		var status = "I've just discovered something amazing @TWAMmuseums";
		var url = 'http://twitter.com/intent/tweet?hashtags=artsdigital&text=' + status + ' http://collectionsdivetwmuseums.org.uk/share/' + s_id;
		share(url);

		return false;

	});

	$('.share-history-email').on('click',function(){
		recordAction('share_history_email');
       	window.location.href = "mailto:?subject=" + encodeURIComponent("I’d like to share my discoveries with you") + "&body=" + encodeURIComponent("Hey,\n\nI was exploring Tyne & Wear Archives & Museum's collections and discovered these artefacts.\n\nhttp://collectionsdivetwmuseums.org.uk/share/" + s_id);
    });

    $('.share-history-link').on('click', function(){
    	recordAction('share_history_link');
    	$('.share-link-url').val('http://collectionsdivetwmuseums.org.uk/share/' + s_id);
    	$('.share-link-url').show();
    	$('.share-link-url').select();
    	$("#historypanel").animate({ scrollTop: $('#historypanel')[0].scrollHeight });
    });


    // ITEM SHARE LINKS
    $(document.body).on('click','.share-item-fb', function(){
		// $('meta[property="og:image"]').remove();
    	// $('head').append('<meta property="og:image" content="' + root_url + get_img_url(thehistory[thehistory.length - 1]["images"][0]["url"], 2) + '">' );
		
		var artefact = artefacts[$(this).parent().parent().parent().data('lido-rec-id')];

		var actionData = {};
		actionData.lidoRecID = artefact.lidoRecID;
		recordAction('share_item_facebook', actionData);

		FB.ui({
		  method: 'feed',
		  link: document.location.href + 'share/a/' + artefact._id.$id,
		  picture: get_img_url(artefact["images"][0]["url"], 2)
		}, function(response){
			console.log(response);
		});
		return false;
	});

	$(document.body).on('click', '.share-item-twitter', function(){
		var artefact = artefacts[$(this).parent().parent().parent().data('lido-rec-id')];
		var actionData = {};
		actionData.lidoRecID = artefact.lidoRecID;
		recordAction('share_item_twitter', actionData);

		var status = "I've just discovered something amazing @TWAMmuseums";
		var url = 'http://twitter.com/intent/tweet?hashtags=artsdigital&text=' + status + ' http://collectionsdivetwmuseums.org.uk/share/a/' + artefact._id.$id;
		share(url);
		return false;
	});

	$(document.body).on('click', '.share-item-email', function(){
		var artefact = artefacts[$(this).parent().parent().parent().data('lido-rec-id')];

		var actionData = {};
		actionData.lidoRecID = artefact.lidoRecID;
		recordAction('share_item_email', actionData);
       	window.location.href = "mailto:?subject=" + encodeURIComponent("I’d like to share my discoveries with you") + "&body=" + encodeURIComponent("Hey,\n\nI was exploring Tyne & Wear Archives & Museum's collections and discovered these artefacts.\n\nhttp://collectionsdivetwmuseums.org.uk/share/a/" + artefact._id.$id);
       	return false;
    });

    $(document.body).on('click', '.share-item-link', function(){
    	var artefact = artefacts[$(this).parent().parent().parent().data('lido-rec-id')];

    	var actionData = {};
		actionData.lidoRecID = artefact.lidoRecID;
    	recordAction('share_item_link', actionData);

    	$(this).parent().find('.share-link-item-url').show();
    	$(this).parent().find('.share-link-item-url').select();
    	return false;
    });

    $(document.body).on('click', '.launch-btn', function(){
    	toggle_map();

    	var lidoRecID = $('.map_artefact_preview').attr('lidorecid');

    	$.ajax('artefacts/fetch_more_from_lido_rec_id', {
			data: {lidoRecID:lidoRecID},
			success: function(data) {
				var artefacts_to_insert = "";
				data =  $.parseJSON(data);
					
				var scrollToArtefactId = null;
				var artefact_lido_rec_id = null;

				$.each(data.results, function(count, artefact_record) {
					if(scrollToArtefactId == null)
					{
						scrollToArtefactId = artefact_record.lidoRecID_js;
						artefact_lido_rec_id = artefact_record.lidoRecID
					}

					// check if images has been set - bug
					if(artefact_record.images)
					{
						artefacts[artefact_record.lidoRecID_js] = artefact_record;
						artefacts_lookup.push(artefact_record.lidoRecID_js);

						artefacts_to_insert  += "<div class='artefact-tile' style='background-image:url(" + get_img_url(artefact_record.images[0]["url"], 1) + ");' id='artefact-tile-" + artefact_record.lidoRecID_js + "' data-lido-rec-id='" + artefact_record.lidoRecID_js + "'><div class='artefact-controls share-item' aria-hidden='true'><i class='fa fa-share-alt'></i></div><div class='glyphicon glyphicon-zoom-in size-btn artefact-controls'></div><div class='info artefact-controls'><i class='fa fa-info'></i></div><div class='title artefact-controls'>" + artefact_record.title + "</div><div class='item-share-links'><div class='item-share-links-wrapper'><input type='text' readonly='readonly' value='' class='share-link-item-url' onClick='this.setSelectionRange(0, this.value.length)'/><i class='fa fa-facebook fa-3x share-btn share-item-fb share-fb'></i><i class='fa fa-twitter fa-3x share-btn share-item-twitter share-twitter'></i><i class='fa fa-envelope-o fa-3x share-btn share-item-email share-email'></i><i class='fa fa-link fa-3x share-btn share-item-link share-link'></i></div></div>";

						if(artefact_record.images.length > 1)
						{
							artefacts_to_insert += "<div class='image-counter'><span>" + artefact_record.images.length + "</span><i class='fa fa-camera-retro'></i></span></div>";
						}

						artefacts_to_insert += '<span class="glyphicon glyphicon-remove remove-tile artefact-controls"></span>';

						artefacts_to_insert += "</div>";
					}
				});
				
				$('#artefact-container').append(artefacts_to_insert);
				$('#artefact-tile-' + scrollToArtefactId).trigger("click");
				$('.close').trigger("click");

				fetch_more_tiles = true;

				// dive_from_map_view
				actionData = {};
				actionData.lidoRecID = artefact_lido_rec_id;
				recordAction('dive_from_map_view', actionData);

				record_offset = data.record_offset;
			},
			error: function(e) {
				console.log(e);
			}
		});
    });

	keep_alive();
	fetch_more(0);
	show_hide_controls(first_artefact);
});