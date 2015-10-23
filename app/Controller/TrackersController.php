<?php
spl_autoload_register(function ($class) {
    foreach (App::path('Vendor') as $base) {
        $path = $base . str_replace('\\', DS, $class) . '.php';
        if (file_exists($path)) {
            include $path;
            return;
        }
    }
});

class TrackersController extends AppController {
		
	public $name = 'Trackers';

	public $uses = array('Artefact', 'BrowsingSession');

	public $client;

	public $components = array('Neo4j');

	public function record_action(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$this->Session->check('browsing_session');
		$_id = $_GET['_id'];
		$action_type = $_GET['action'];

		$action_types = array(
			'artefact_click',
			'remove_artefact',
			'remove_artefact_from_historybar',
			'open_history_panel',
			'close_history_panel',
			'information_button_click',
			'zoom_button_click',
			'open_map_view',
			'close_map_view',
			'carousel',
			'fetch_more_event',	
			'map_fetch_artefacts',
			'map_fetch_keywords',
			'share_history_facebook',
			'share_history_twitter',
			'share_history_email',
			'share_history_link',
			'share_item_facebook',
			'share_item_twitter',
			'share_item_email',
			'share_item_link',
			'dive_from_map_view',
			'artefact_tile_hover'
		);

		// Start exploring from shares

		if(in_array($action_type, $action_types))
		{
			$browsing_session = $this->BrowsingSession->get($_GET['_id']);

			$action = array();
			$action['action'] = $action_type;
			$action['created'] = date("Y-m-d H:i:s");

			if(isset($_GET['data']))
			{
				$action['data'] = $_GET['data'];
			}
			else
			{
				$action['data'] = null;
			}

			$browsing_session['actions'][] = $action;

			$this->BrowsingSession->update_field($browsing_session['_id'], 'actions', $browsing_session['actions']);
		}

	}

	public function record_visit(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$browsing_session = $this->BrowsingSession->get($_GET['_id']);

		$this->BrowsingSession->update_field($browsing_session['_id'], 'u_id', $_GET['u_id']);

	}

	public function export(){
		// echo '<pre>';
		// print_r($this->BrowsingSession->getAll());
		// echo '</pre>';

	}
}