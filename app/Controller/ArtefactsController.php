<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('OpenCalais', 'Lib');
App::uses('AlchemyAPI', 'Lib');
App::import('Vendor', 'SimpleImage');

spl_autoload_register(function ($class) {
    foreach (App::path('Vendor') as $base) {
        $path = $base . str_replace('\\', DS, $class) . '.php';
        if (file_exists($path)) {
            include $path;
            return;
        }
    }
});

App::import('Vendor', 'Everyman');

class ArtefactsController extends AppController {

	public $name = 'Artefacts';

	public $uses = array('Artefact', 'BrowsingSession');

	public $all_terms = array();

	public $components = array('Neo4j');

	public $client;

	public function beforeFilter(){
		$this->client = new Everyman\Neo4j\Client('localhost', 7474);
	}

	public function index() {
		
		$artefacts = $this->Artefact->find('all', array(
										'fields' => array(
											'_id',
											'administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource'
										),
										'conditions' => array(
											'administrativeMetadata.resourceWrap.resourceSet.0' => array('$exists' =>  'true')
										),
										'limit' => 5
								   ));

		$this->set('artefacts', $artefacts);
		
	}

	// public function register_session(){
	// 	$this->layout = 'ajax';
	// 	$this->autoRender = false;

	// 	// prevent browser from caching the redirect
	// 	$this->response->disableCache();

	// 	$browsing_session = array(
	// 		'start_time' => time(),
	// 		's_id' => $this->Session->read('Config.userAgent'),
	// 		'viewed' => array(),
	// 		'exclude_from_results' => array(),
	// 		'browser' => $_SERVER['HTTP_USER_AGENT'],
	// 		'end_time' => null
	// 	);
		
	// 	$browsing_session = $this->BrowsingSession->insert($browsing_session);

	// 	$this->redirect(array(
	// 	    'controller' => 'artefacts', 'action' => 'scroll2', '_id' => $browsing_session['_id']
	// 	));
	// }

	public function scroll2(){

		$browsing_session = array(
			'start_time' => time(),
			's_id' => $this->Session->read('Config.userAgent'),
			'u_id' => null, 
			'viewed' => array(),
			'exclude_from_results' => array(),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'end_time' => null,
			'actions' => array()
		);
		
		$browsing_session = $this->BrowsingSession->insert($browsing_session);

		$browsing_session = $this->BrowsingSession->get($browsing_session['_id']);

		$result = $this->Artefact->get_random_artefact();

		$click_event['lidoRecID'] = $result["artefact"]['lidoRecID'];
		$click_event['removed'] = false;
		$click_event['created'] = date("Y-m-d H:i:s");

		$browsing_session['viewed'][] = $click_event;

		$this->BrowsingSession->update_viewed($browsing_session['_id'], $browsing_session['viewed']);

		$this->set('artefact', $result["artefact"]);
		$this->set('offset', $result["offset"]);
		$this->set('_id', $browsing_session['_id']);
	}

	public function kapow() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		echo '<pre>';
		print_r(json_encode($this->BrowsingSession->get($_GET['_id'])));
		echo '</pre>';
	}


	public function fetch_more(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$randomness = $_GET['randomness_level'];
		$offset = $_GET['offset'];

		// $this->Session->check('browsing_session');
		// $browsing_session = $this->Session->read('browsing_session');

		$browsing_session = $this->BrowsingSession->get($_GET['_id']);

		if($browsing_session)
		{
			$limit = 50;

			switch ($randomness) {
				case 0:
					$limit = 48;
					break;
				case 1:
					$limit = 24;
					break;
				case 2:
					$limit = 16;
					break;
				default:
					$limit = 48;
					break;
			}

			$results = $this->Artefact->get_artefacts($limit, $offset, $randomness, $browsing_session['exclude_from_results']);
			
			if(is_array($browsing_session['exclude_from_results']) && is_array($results['exclude_from_results']))
			{
				$browsing_session['exclude_from_results'] = array_merge($results['exclude_from_results'], $browsing_session['exclude_from_results']);
				// update_session		
			}

			$this->BrowsingSession->update_exclude_from_results($browsing_session['_id'], $browsing_session['exclude_from_results']);

			$results['randomness'] = $randomness;

			unset($results['exclude_from_results']);
			return json_encode($results);
		}
		else
		{
			$this->redirect(array(
			    'controller' => 'artefacts', 'action' => 'scroll2'
			));
		}
	}

	public function remove_artefact_from_session() {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$this->Session->check('browsing_session');
		$artefact_clicked = $_GET['lidoRecID'];
		$_id = $_GET['_id'];

		$browsing_session = $this->BrowsingSession->get($_GET['_id']);

		// loop through the browsed items and find the one we want to update
		for($i = 0; $i < count($browsing_session['viewed']); $i++)
		{
			if($browsing_session['viewed'][$i]['lidoRecID'] == $artefact_clicked)
			{
				$browsing_session['viewed'][$i]['removed'] = true;
			}
		}

		$this->BrowsingSession->update_viewed($browsing_session['_id'], $browsing_session['viewed']);

		// remove from neo

	}

	public function record_click(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$this->Session->check('browsing_session');
		$artefact_clicked = $_GET['lidoRecID'];
		$_id = $_GET['_id'];

		$browsing_session = $this->BrowsingSession->get($_GET['_id']);

		$click_event['lidoRecID'] = $artefact_clicked;
		$click_event['removed'] = false;
		$click_event['created'] = date("Y-m-d H:i:s");

		$browsing_session['viewed'][] = $click_event;
		
		$this->BrowsingSession->update_viewed($browsing_session['_id'], $browsing_session['viewed']);

		if($browsing_session['viewed'] > 1){
			// update neo with click reference
			$from = $browsing_session['viewed'][ (count($browsing_session['viewed'] ) - 1) ]['lidoRecID'];
			$to = $browsing_session['viewed'][ (count($browsing_session['viewed'] ) - 2) ]['lidoRecID'];

			$this->Artefact->record_click($from, $to, (string) $browsing_session['_id']);
		}

	}

	public function keep_alive() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		
		$browsing_session = $this->BrowsingSession->get($_GET['_id']);
		$this->BrowsingSession->update_end_time($browsing_session['_id']);
	
	}

	public function view($id)
	{
		$artefact = $this->Artefact->find('first', array(
										'conditions' => array(
											// 'administrativeMetadata.resourceWrap.resourceSet.0' => array('$exists' =>  'true'),
											'_id' => $id
										)
								   ));
		$this->set('artefact', $artefact);
	}

	public function map_data(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		echo json_encode($this->Artefact->get_map_artefacts(10));
	}

	public function map_node_artefacts($node_id, $limit){
		$this->layout = 'ajax';
		$this->autoRender = false;

		echo json_encode($this->Artefact->get_map_node_artefacts($node_id, $limit));	
	}

	public function more_map_data(){
		$this->layout = 'ajax';
		$this->autoRender = false;
	}

	public function explore_keyword(){
		$this->layout = 'ajax';
		$this->autoRender = false;	

		$response = array();

		$keyword = $_GET['keyword'];

		$response = $this->Artefact->get_artefacts_by_keyword($keyword);

		echo json_encode($response);
	}

	public function explore_artefact(){
		$this->layout = 'ajax';
		$this->autoRender = false;	

		$response = array();

		$lidoRecId = $_GET['lidoRecId'];

		$response = $this->Artefact->get_keywords_by_artefact_lido_id($lidoRecId);

		echo json_encode($response);
	}

	public function session_data_map(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$_id = $_GET['s_id'];

		echo json_encode($this->Artefact->get_session_data_map($_id));
	}

	public function fetch_more_from_lido_rec_id(){
		$this->layout = 'ajax';
		$this->autoRender = false;


		$lidoRecID = $_GET['lidoRecID'];
		
		echo json_encode($this->Artefact->launch_from_lido_rec_id($lidoRecID));
	}
}