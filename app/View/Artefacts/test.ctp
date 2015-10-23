<?php echo $this->Html->css('main'); ?>
<!-- <input type="text" id="q"> -->
<div id="sidebar">
	<div id="sidebar-content">
		<input type="text" id="q">
		<h3 class="artefact-title"></h3> 
		<div class="artefact-image-container">
			<img src="" id="artefact-image"/>
		</div>
		<div class="artefact-image">
		</div>
		<div>
			<p class="artefact-description">

			</p>
		</div>
	</div>
</div>
<?php 
// echo $this->Html->script('main');
// echo $this->Html->script('nodes');
echo $this->Html->script('spotlight');
?>