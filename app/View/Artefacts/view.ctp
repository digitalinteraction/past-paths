<?php
echo '<pre>';
print_r($artefact);
echo '</pre>';
?>

<h1><?php echo $artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue']; ?></h1>
<h4><?php echo $artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'][0]['descriptiveNoteValue']; ?></h4>
<img src="<?php echo $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet']['resourceRepresentation']['linkResource'];?>" />