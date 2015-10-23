<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class HistoriesController extends AppController {
	
	public $uses = array('Artefact', 'BrowsingSession');

	public function view($_sid){
		$browsing_session = $this->BrowsingSession->get($_sid);

		$artefacts = array();

		foreach($browsing_session['viewed'] as $index => $artefact)
		{
			$artefacts[$artefact['lidoRecID']] = $this->Artefact->get_artefact_by_lidoRecId($artefact['lidoRecID']);

			$dir = new Folder('../webroot/img/artefacts/large/' . $browsing_session['viewed'][$index]['lidoRecID'] . '/');
			$files = $dir->find('.*\.jpeg');

			foreach($files as $file)
			{
				$img["url"] = $this->webroot . "img/artefacts/large/" . $browsing_session['viewed'][$index]['lidoRecID'] .'/' . $file;
				$browsing_session['viewed'][$index]['images'][] = $img;
			}
		}

		// echo '<pre>';
		// print_r($browsing_session);
		// echo '</pre>';

		$this->set('history', $browsing_session);
		$this->set('artefacts', $artefacts);
	}

	public function view_item($lidoRecID) {
		if(!$lidoRecID){
			return false;
		}


		if(substr($lidoRecID, 0, 3) == "emu")
		{
			$artefact = $this->Artefact->get_artefact_by_lidoRecId($lidoRecID);
		}
		else
		{
			$artefact = $this->Artefact->get_artefact_by_objectId($lidoRecID);
		}

		if($artefact)
		{
			$this->set('artefact', $artefact);
		}
		else
		{
			throw new NotFoundException("Oops looks like we couldn't find that");
		}
	}
}