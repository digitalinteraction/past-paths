<?php foreach($artefacts as $artefact): ?>
	<img src="<?php echo $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet'][0]['resourceRepresentation']['linkResource']; ?>" />
<?php endforeach; ?>