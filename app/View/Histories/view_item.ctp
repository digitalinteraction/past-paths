<?php
echo $this->Html->css('artefacts/scroll2');
?>

<style>
html,body{
	background-image:url('<?php echo $this->webroot; ?>img/cartographer.png') !important;
	color: #fff;
}

.header {
	width: 100%;
	padding: 25px;
	text-align: center;
}

.TWAM-logo {
	position: fixed;
	left: 10px;
	top: 10px;
	z-index: 5;
	max-width: 10%;
}

.btn-large {
	padding: 15px;
	min-width: 20%;
	/*position: fixed;*/
	/*bottom: 15px;*/
	z-index: 5;
	/*margin-left: -10%;*/
	/*left: 50%;*/
	-webkit-box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
	-moz-box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
	box-shadow: 0px 0px 15px 2px rgba(51,51,51, 0.8);
}
</style>

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
<p class="centered" style="margin-top:20px; margin-bottom:20px;text-align:center;">
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

<a href="http://www.twmuseums.org.uk"><?php echo $this->Html->image('logos/twam-large.jpg', array('alt' => 'Tyne and Wear Museum Archives', 'class' => array('img-responsive', 'center-block', 'TWAM-logo'))); ?></a>

<div class="container header">
		<div class="row">
			<div class="col-sm-12">
				<h1>I'd like to share my discovery with you</h1>
				<strong>I explored Tyne & Wear Archives & Museums collections and discovered this.</strong>
				<a href="#"><p data-toggle="modal" data-target="#about-modal">Find out more about this project</p></a>
				<a href="../../"><button class="btn btn-large btn-primary"><strong>Start Exploring</strong></button></a>
			</div>
		</div>
	</div>

<div id="artefact-tile-<?php echo $artefact['lidoRecID_js']; ?>" class="full-screen first-tile" style="z-index:3; background-image:url(<?php echo $this->webroot . 'img/artefacts/large/' . $artefact['images'][0]['url']; ?>);"  data-lido-rec-id="<?php echo $artefact['lidoRecID_js']; ?>">
	<div class="glyphicon glyphicon-zoom-in size-btn artefact-controls" aria-hidden="true"></div>
	<div class='info artefact-controls'><i class="fa fa-info artefact-controls"></i></div>
	<div class='title artefact-controls'><?php echo $artefact['title']; ?></div>
    <div class="item-share-links">
        <!-- <input type="text" readonly="readonly" value="" class="share-item-link-url" onClick="this.setSelectionRange(0, this.value.length)"/> -->
        <i class="fa fa-facebook fa-3x share-btn share-item-fb share-fb"></i>
        <i class="fa fa-twitter fa-3x share-btn share-item-twitter share-twitter"></i>
        <i class="fa fa-envelope-o fa-3x share-btn share-item-email share-email"></i>
        <i class="fa fa-link fa-3x share-btn share-item-link share-link"></i>
        <i class="fa fa-link fa-3x share-btn share-item-link share-link"></i>
    </div>
</div>

<script>
var webroot = "<?php echo $this->webroot; ?>";
var artefacts = {};
artefacts["<?php echo $artefact['lidoRecID_js']; ?>"] = <?php echo json_encode($artefact); ?>
</script>

<?php
echo $this->Html->script('share-item.js');
?>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>