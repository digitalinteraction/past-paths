<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<style>
html{
  height: 100%;
}
body {
  min-height: 100%;
}
body{

font-family: 'Open Sans',sans;
height:100% !important;
margin: 0;
}

a:hover
{
	color:white !important;
}

</style>
<link href='http://fonts.googleapis.com/css?family=Hind' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

<div style="background:url('img/cartographer.png');min-height:100%;position:relative;">

<div style="min-height:400px;background:#111;overflow:hidden;">
<div class="carousel slide" data-ride="carousel" style="height:100%;">

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active" style="height:400px;background-image:url('<?php echo $this->webroot . "img/artefacts/large/" . $artefacts[0]['artefact']['lidoRecID']; ?>/0.jpeg');background-size:cover;background-position:center center;"></div>
    <div class="item" style="height:400px;background-image:url('<?php echo $this->webroot . "img/artefacts/large/" . $artefacts[1]['artefact']['lidoRecID']; ?>/0.jpeg');background-size:cover;background-position:center center;">
    </div>
    <div class="item" style="height:400px;background-image:url('<?php echo $this->webroot . "img/artefacts/large/" . $artefacts[2]['artefact']['lidoRecID']; ?>/0.jpeg');background-size:cover;background-position:center center;">
    </div>
    <div class="item" style="height:400px;background-image:url('<?php echo $this->webroot . "img/artefacts/large/" . $artefacts[3]['artefact']['lidoRecID']; ?>/0.jpeg');background-size:cover;background-position:center center;">
    </div>
  </div>
</div>
</div>

<div class="header-text centered">
	<h1>Past Paths</h1>
	<p>Transforming online digital collections through exploration interfaces</p>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<h1 style="font-size:4em">Welcome</h1>
			<p style="font-size:14pt;font-family:'Open Sans',sans">Past Paths is a research project exploring new ways of interacting with online museum collections. Through building relationships between artefacts, novel interactions for users and dynamically presented results, Past Paths explores the possibilities for serendipitous exploration of collections for all.</p>
		</div>
	</div>
<br><br>
	<div class="row">
		<div class="col-md-12 centered">
			We present two connected experiences of the TWAM online collection:<br>
	<a href="artefacts/scroll2" class="btn btn-lg" style="background:#333;margin:10px;"><img src="img/logos/scroll.png"/><br>
		Infinite Scroll
	</a>

	<!-- <a href="artefacts/bubble" class="btn btn-lg" style="background:#333;margin:10px;"><img src="img/logos/map.png"/><br>
		Spatial Exploration
	</a> -->


		</div>
	</div>
</div>

	<!-- <div style="width:100%;height:inhreit;"> -->
		<div class="container">
			<div class="row">
				<div class="col-md-3 logo">
					<?php echo $this->Html->image('logos/ncl.png', array('alt' => 'Digital Interaction Group, Newcastle University', 'class' => array('img-responsive', 'center-block'))); ?>
				</div>
				<div class="col-md-3 logo">
					<?php echo $this->Html->image('logos/twam.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
				</div>
				<div class="col-md-3 logo">
					<?php echo $this->Html->image('logos/msr.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
				</div>
				<div class="col-md-3 logo">
						<?php echo $this->Html->image('logos/nesta.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
				</div>
			</div>
		</div>
	<!-- </div> -->
</div>
<script>
$(function(){
	$('.carousel').carousel();
});
</script>