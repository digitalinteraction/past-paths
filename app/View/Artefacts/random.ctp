<?php
$artefact['Artefact']['img_url'] = "";
$artefact['Artefact']['title'] = "";
$artefact['Artefact']['description'] = "";

// get img
if(count($artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet']) > 1)
{
	if(array_key_exists(0, $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet']))
	{
		$artefact['Artefact']['img_url'] = $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet'][0]['resourceRepresentation']['linkResource'];
	}
	else
	{
		$artefact['Artefact']['img_url'] = $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet']['resourceRepresentation']['linkResource'];
	}
}
else
{
	$artefact['Artefact']['img_url'] = $artefact['Artefact']['administrativeMetadata']['resourceWrap']['resourceSet']['resourceRepresentation']['linkResource'];
}

// get title
if(!is_array($artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue']))
{
	$artefact['Artefact']['title'] = $artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue']; 
}



// get description
if(count($artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet']) == 1)
{
	$artefact['Artefact']['description'] = $artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet']['descriptiveNoteValue'];
}
else
{
	$artefact['Artefact']['description'] = $artefact['Artefact']['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'][0]['descriptiveNoteValue'];
}
?>

<h1><?php echo $artefact['Artefact']['title']; ?></h1>
<h4><?php echo $artefact['Artefact']['description']; ?></h4>

<img src="<?php echo $artefact['Artefact']['img_url']; ?>"/>

<?php
// echo '<pre>';
// print_r($artefact);
// echo '</pre>';
?>