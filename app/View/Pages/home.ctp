<link href='http://fonts.googleapis.com/css?family=Hind' rel='stylesheet' type='text/css'>

<div class="header centered" style="background-image:url('<?php echo $this->webroot . "img/artefacts/large/" . $artefact['lidoRecID']; ?>/0.jpeg')">
</div>

<div class="header-text centered">
	<h1>Past Paths</h1>
	<p>Transforming online digital collections through exploration interfaces</p>
</div>

<div class="container content">
	<div class="row">
		<div class="col-md-12">
			<h1>Welcome</h1>
			<strong>See below for how to launch</strong>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<p>This is the website for the Past Paths Project.</p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12 centered">
			<?php echo $this->Html->link('Scroll Interface', array("controller" => "artefacts", "action" => "scroll2"), array('class' => array('btn', 'btn-primary', 'btn-lg'))); ?>
			<?php echo $this->Html->link('Map Interface', array("controller" => "artefacts", "action" => "bubble"), array('class' => array('btn', 'btn-primary', 'btn-lg'))); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4 logo">
			<?php echo $this->Html->image('logos/ncl.png', array('alt' => 'Digital Interaction Group, Newcastle University', 'class' => array('img-responsive', 'center-block'))); ?>
		</div>
		<div class="col-md-4 logo">
			<?php echo $this->Html->image('logos/twam.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
		</div>
		<div class="col-md-4 logo">
			<?php echo $this->Html->image('logos/msr.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
		</div>
	</div>	

	<div class="row">
		<div class="col-md-4 col-md-offset-4 logo centered">
				<?php echo $this->Html->image('logos/nesta.png', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block'))); ?>
		</div>
	</div>
</div>