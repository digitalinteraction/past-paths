<?php
echo $this->Html->css('histories/view.css');
?>
<?php //echo $tiles[rand(0, (count($tiles) - 1))]; ?>

<style>

html,body{
	background-image:url('<?php echo $this->webroot; ?>img/cartographer.png') !important;
	min-height:100%;
	color: #fff;
}

.header {
	width: 100%;
	padding: 25px;
	text-align: center;
}

.logo {
	position: fixed;
	left: 10px;
	top: 10px;
	z-index: 3;
	max-width: 10%;
}

.btn-large {
	padding: 15px;
	min-width: 20%;
	position: fixed;
	bottom: 15px;
	z-index: 5;
	margin-left: -10%;
	left: 50%;
	-webkit-box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
	-moz-box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
	box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
}

.cell {
	padding: 10px;
	background-size: cover;
	background-repeat: no-repeat;
	background-position: center center;
}

.cell:hover {
	/*border:5px solid #fff;*/
	cursor: pointer;
}

/*.free-wall{
	width: 100%;
	height: 100%;
}*/

.free-wall-wrapper{
	bottom: 0px;
	left: 0px;
	right: 0px;
	top: 0px;
}

.glyphicon-remove{
	position: absolute;
	right: 20px;
	top:20px;
	border-radius: 20px;
	border:5px solid #fff;
	background-color: #fff;
	opacity: 0.85;
	color: #333;
	display: none;
}

.glyphicon-remove:hover {
	opacity: 1;
}

.overlay {
	position: fixed;
	height: 100%;
	width: 100%;
	opacity: 0.7;
	background-color: #000;
	z-index: 1;	
	display: none;	
}

.info{
	position: fixed;
	height:50px;
	width: 50px;
	border-radius: 50px;
	background-color: #fff;
	line-height: 45px;
	font-size: 24pt;
	bottom: 20px;
	right: 20px;
	color: #333;
	-webkit-box-shadow: 0px 0px 10px 2px rgba(51,51,51,0.1);
	-moz-box-shadow: 0px 0px 10px 2px rgba(51,51,51,0.1);
	box-shadow: 0px 0px 10px 2px rgba(51,51,51,0.1);
	z-index: 5;
	text-align: center;
	display: none;
}

.info:hover{
	background-color: #333;
	color: #fff;
}

.close{
	display: none;
	z-index: 3;
	position: fixed;
	top:20px;
	right:20px;
}

#description {
	position: fixed;
	/*background-color: rgba(38,38,38,0.5);*/
	left:0;
	right:0;
	bottom: 0px;
	z-index: 3;
	padding: 20px;
	padding-bottom:80px;
	display: none;
}

.description-wrapper{
	display: inline-block;
	position: relative;
	text-align: left;
	width: 100%;
	/*z-index: -2;*/
}

.description-wrapper h3{
	margin: 0px;
}

.description-wrapper span {
	padding: 15px;
}

.description{
	background:rgba(0,0,0,0.6);
	box-shadow: 5px 5px 5px rgba(0,0,0,0.4);
	margin-top:-10px;
	padding:15px;
	width:75%;
	max-width: 960px;
	margin: 25px auto;
}


.description span
{
	padding:5px;
}

.artefact-text-title{
	width:25%;
	display:inline-block;
	vertical-align: top;
	height: 100%;
	padding: 5px;
	min-width: 150px;
	font-weight: bold;
}

.artefact-text{
	width:75%;
	display:inline-block;
	padding: 5px;
	vertical-align: top;
}

.artefact-text li{
	text-transform: capitalize;
}


.artefact-text-description {
	width:75%;
	display:inline-block;
	padding: 5px;
	vertical-align: top;
}

.artefact-text-description ul {
	list-style-type: none;
	padding: 0px;
}

.artefact-text > .subtitle {
	display: inline-block;
	width: 10%;
	min-width: 80px;
	padding: 5px;
	height: 100%;
	vertical-align: top;
	font-weight: bold;
}

.artefact-text > .text {
	display: inline-block;
	width: 90%;
	padding: 5px;	
}

</style>

<script>
var webroot = "<?php echo $this->webroot; ?>";
</script>

<?php
function get_random($min,$max,$step)
{
  $numSteps = ($max-$min)/$step;
  $multiplier = rand(0,$numSteps);
  $num = $min + ($multiplier * $step);
  return $num;
}
?>
<div class="overlay">
</div>

<a href="../"><button class="btn btn-large btn-primary"><strong>Start Exploring</strong></button></a>

<a href="http://www.twmuseums.org.uk"><?php echo $this->Html->image('logos/twam-large.jpg', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block', 'logo'))); ?></a>

<div class="container header">
	<div class="row">
		<div class="col-sm-12">
			<h1>I'd like to share my discoveries with you</h1>
			<strong>I explored Tyne & Wear Archives & Museums collections and discovered some interesting artefacts</strong>
		</div>
	</div>
</div>

<span class="glyphicon glyphicon-remove close" style="cursor:pointer"></span>
<div class='info artefact-controls' style="cursor:pointer"><i class="fa fa-info artefact-controls"></i></div>

<div id="description">
</div>

<div class="free-wall-wrapper">
<div id="freewall" class="free-wall">
<?php foreach($history['viewed'] as $artefact): ?>
	<?php if(array_key_exists('images', $artefact)): ?>
   		<div class="cell" style='background-image:url("<?php echo $artefact['images'][0]['url']; ?>");width:<?php echo get_random(400, 600, 25); ?>px;height:<?php echo get_random(400, 600, 25); ?>px' data-lidoRecID="<?php echo $artefact['lidoRecID']; ?>">
   		</div>
	<?php endif; ?>
<?php endforeach; ?>
</div>
</div>

<script type="text/javascript">
var artefacts;
var current_item;

function show_item(){
	$('#' + current_item).data('p_width', $('#' + current_item).css('width'));
	$('#' + current_item).data('p_height', $('#' + current_item).css('height'));
	$('#' + current_item).data('p_left', $('#' + current_item).css('left'));
	$('#' + current_item).data('p_top', $('#' + current_item).css('top'));

	$('#' + current_item).css('width', '100%');
	$('#' + current_item).css('height', '100%');
	$('#' + current_item).css('left', '0px');
	$('#' + current_item).css('right', '0px');
	$('#' + current_item).css('top', '0px');
	$('#' + current_item).css('bottom', '0px');
	$('#' + current_item).css('z-index', '2');
	$('#' + current_item).css('position', 'fixed');
	$('#' + current_item).css('background-size', 'auto 100%');

	$(document.body).css('overflow', 'hidden');
	$('.overlay').show();
	$('.info').delay(500).fadeIn(500);
	$('.glyphicon-remove').delay(500).fadeIn(500);
}

function close_item(){
	$(document.body).css('overflow', 'auto');
	$('#' + current_item).addClass('full-screen');
	$('#' + current_item).css('z-index', '0');
	$('#' + current_item).css('width', $('#' + current_item).data('p_width'));
	$('#' + current_item).css('height', $('#' + current_item).data('p_height'));
	$('#' + current_item).css('left', $('#' + current_item).data('p_left'));
	$('#' + current_item).css('top', $('#' + current_item).data('p_top'));
	$('#' + current_item).css('background-size', 'cover');
	$('#' + current_item).css('background-color', 'none');
	$('#' + current_item).removeData('full-screen');
	$('#' + current_item).css('position', 'absolute');
	$('.glyphicon-remove').hide();
	$('.info').hide();
	$('.overlay').hide();
	hide_description();
	console.log('close item called');
}

function hide_description(){
	// console.log($('#description'));
	// $('#description').animate({height: "0px"}, 1000);
	if($('#description').is(":visible"))
	{
		$('#description').slideToggle("slow");
	}
}

$(document).ready(function(){
	artefacts = <?php echo json_encode($artefacts); ?>;

	var wall = new freewall("#freewall");
	wall.reset({
		selector: '.cell',
		animate: true,
		cellW: 20,
		cellH: 200,
		onResize: function() {
			wall.fitWidth();
		}
	});

	wall.fitWidth();
	// for scroll bar appear;
	$(window).trigger("resize");

	$(document).on('click', '.cell', function(){
		
		if(current_item == undefined)
		{
			current_item = $(this).attr('id');
			show_item();
		}
		else
		{
			// close current item
			close_item();
			current_item = undefined;
		}
	});

	$(document).on('click', '.close', function(){
		close_item();
	});

	$(document).on('click', '.info', function(){
		var artefact = artefacts[$('#' + current_item).data('lidorecid')];
		console.log(artefact);

		var info_html = "<div>";
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

			var work_id_string = "<div><span class='artefact-text-title'>Unique Object Number</span><span class='artefact-text'><a href='http://collectionssearchtwmuseums.org.uk/resultslist.html?collection=&searchTerm=" + encodeURIComponent(artefact.descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID) + "&imageSearch=false&search=true' target='_blank'>" + artefact.descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID + "</a></span></div>";

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

			$('#description').html('<div class="description-wrapper">'+
			'<span class="glyphicon glyphicon-remove close-description" style="cursor:pointer"></span>' +
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

		$('#description').slideToggle("slow");

		// if($('#description').is(":visible"))
		// {
		// 	hide_description();
		// }
		// else
		// {
		// 	$('#description').slideToggle("slow");
		// }
	});
});
</script>


<?php
echo $this->Html->script('history.js');
echo $this->Html->script('freewall.js');
?>

