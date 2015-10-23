<div class="row">
	<div class="col-md-2">Title</div>
	<div class="col-md-8"><?php echo $artefact['title']; ?></div>
</div>
<div class="row">
	<div class="col-md-2">Description(s)</div>
	<div class="col-md-8">
		<ul>
			<?php foreach($artefact['descriptions'] as $description): ?>
				<li><?php echo $description; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-md-2">Term(s)</div>
	<div class="col-md-8">
		<ul>
			<?php foreach($artefact['terms'] as $term): ?>
				<li><?php echo $term; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-md-2">Rights Holder</div>
	<div class="col-md-8">TWAM</div>
</div>
<div class="row">
</div>
<div class="row">
</div>