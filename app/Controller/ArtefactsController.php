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
		// $random = $this->Artefact->find('all', array(
		// 								'conditions' => array(
		// 									'administrativeMetadata' => array(
		// 										'resourceWrap' => array(
		// 											'exists' => 'true'
		// 										)
		// 									)
		// 								),
		// 								'limit' => 10
		// 						   ));

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

	public function constraints()
	{
		$this->Neo4j->create_contraint("Keyword", array('text'));
		$this->Neo4j->create_contraint("Entity", array('text'));
		// $this->Neo4j->create_contraint("Artefact", array('text'));
	}

	public function neo()
	{
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		ini_set('memory_limit','3000M');
		ini_set('max_execution_time', -1);
		error_reporting(-1);
		$this->layout = 'ajax';
		$this->autoRender = false;

		
		$artefacts = $this->Artefact->get_all_artefacts();
		$artefacts = array_reverse($artefacts);

		// $artefacts = $this->Artefact->find('all', array(
		// 								'fields' => array(
		// 									'_id',
		// 									// 'lidoRecID',
		// 									// 'descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue'
		// 								),
		// 								// 'conditions' => array(
		// 								// 	'administrativeMetadata.resourceWrap.resourceSet.0' => array('$exists' =>  'true'),
		// 								// 	'lidoRecID' => ('emu.ecatalogue.decorativeart&design.284061')
		// 								// ),
		// 								'limit' => 1
		// 						   ));


		// echo '<pre>';
		// print_r($artefacts);
		// echo '</pre>';

		// $this->Neo4j->clear_graph();

		$client = new Everyman\Neo4j\Client();
				
		$artefact_node_label = $client->makeLabel('Artefact');
		$keyword_label = $client->makeLabel('Keyword');

		foreach($artefacts as $artefact)
		{
			$queryString = "MATCH (n:Artefact {lidoRecID : {lidoRecID}}) RETURN id(n)";
			$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact["lidoRecID"]));
			$result = $query->getResultSet();

			if($result->count() == 0)
			{
				$queryString = "MERGE (n:Artefact {lidoRecID : {lidoRecID}}) RETURN id(n)";
				$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact["lidoRecID"]));
				$result = $query->getResultSet();
				
				$artefact_node = $result->current();

				$ai = new AlchemyAPI(Configure::read('alchemy_api_key'));

				$description = implode('. ', $artefact['descriptions']);
				$description .= $artefact['title'];
				$description .= implode('. ', $artefact['terms']);

				$keywords = $ai->keywords('text', $description, null);

				foreach($keywords['keywords'] as $keyword)
				{
					$queryString = "MERGE (n:Keyword {keyword : {text}}) RETURN id(n)";
					$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('text' => $keyword["text"]));
					$result = $query->getResultSet();

					$keyword_node = $result->current();
					
					$queryString = "START n=node({node_id}), k=node({keyword_id}) MERGE (n)-[r:HAS {relevance:{relevance}}]->(k) RETURN n,k,r";
					$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $artefact_node['n'], 'keyword_id' => $keyword_node['n'], 'relevance' => floatval($keyword['relevance'])));
					$result = $query->getResultSet();
				}
			}

			// $data = array(
			// 	'key' => 'lidoRecID',
			// 	'value' => $artefact['lidoRecID'],
			// 	'properties' => array(
			// 		'lidoRecID' => $artefact['lidoRecID']
			// 	)
			// );

			// // $node = $this->Neo4j->create_unique_node($data, 'Artefact');

			// $data = array(
			// 	"lidoRecID" => $artefact['lidoRecID'],
			// 	"Boop" => 'KAPOW'
			// );

			// $node = $this->Neo4j->create_unique_node_cypher($data, 'Artefact');

			// echo '<pre>';
			// print_r($node);
			// echo '</pre>';

			// $ai = new AlchemyAPI(Configure::read('alchemy_api_key'));

			// $description = implode('. ', $artefact['descriptions']);
			// $description .= $artefact['title'];
			// $description .= implode('. ', $artefact['terms']);

			// echo '<pre>';
			// print_r($description);
			// echo '</pre>';

			// // 	// $entities = $ai->entities('text', $description, null);
			// $key_words = $ai->keywords('text', $description, null);

			// foreach($key_words['keywords'] as $key_word)
			// {
			// 	$data = array(
			// 		'key' => 'text',
			// 		'value' => $key_word['text'],
			// 		'properties' => array(
			// 			'text' => $key_word['text']
			// 		)
			// 	);

			// 	$key_word_node = $this->Neo4j->create_unique_node($data, 'Keyword');

			// 	echo '<pre>';
			// 	print_r($key_word_node);
			// 	echo '</pre>';
			// }
		}


		// $this->Neo4j->create_contraint("Artefact", array('lidoRecID'));
		// echo '<pre>';
		// print_r($this->Neo4j->get_node_by_artefact_id(25));
		// echo '</pre>';

	}

	public function scroll(){
		// $artefact = $this->Artefact->get_random_artefact();
		$artefact = $this->Artefact->get_artefact();

		// echo '<pre>';
		// print_r($artefact);
		// echo '</pre>';

		$this->set('artefact', $artefact);
		// $this->Artefact->convert_record_descriptions();
	}

	public function scroll2(){
		// start session variable
		session_start();
		session_destroy();

		session_start();
		

		$_SESSION['browsing_session'] = array(
				'start_time' => time(),
				's_id' => session_id(),
				'viewed' => array(),
				'exclude_from_results' => array(),
				'browser' => $_SERVER['HTTP_USER_AGENT']
			);

		$artefact = $this->Artefact->get_random_artefact();
		$this->set('artefact', $artefact);
	}

	public function get2(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		session_start();	

		$randomness = $_GET['randomness_level'];
		$offset = $_GET['offset'];
		return json_encode($this->Artefact->get_artefacts(10, $offset, $randomness));
		// return json_encode($this->Artefact->recommend_artefacts());
	}

	public function get(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		$offset = $_GET['offset'];
		$randomness = $_GET['randomness_level'];

		return json_encode($this->Artefact->get_artefacts(10, $offset, $randomness));
	}

	public function record_click(){
		$this->layout = 'ajax';
		$this->autoRender = false;
		session_start();
		$_SESSION['browsing_session']['viewed'][] = array('lidoRecID' => $_GET['lidoRecID'], 'created' => time());
	}

	public function finish_session(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		session_start();

		$_SESSION['browsing_session']['end_time'] = time();

		$this->BrowsingSession->insert($_SESSION['browsing_session']);

		session_destroy(session_id());
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

	public function random()
	{
		session_start();
		
		// session_destroy();
		// $this->Neo4j->clear_graph();

		$seed = rand(0, 32592);
		// $seed = 1;
		$artefact = $this->Artefact->find('first', array(
										'limit' => 1,
										'offset' => $seed
								   ));
		
		$this->set('artefact', $artefact);

		$data = array(
				'key' => 'lidoRecID',
				'value' => $artefact['Artefact']['lidoRecID'],
				'properties' => array(
					'lidoRecID' => $artefact['Artefact']['lidoRecID']
				)
			);

		// $node = $this->Neo4j->create_unique_node($data, 'Artefact');

		// $queryString = "MATCH (n) RETURN n LIMIT 2";
		// $query = new Everyman\Neo4j\Cypher\Query($this->client, $queryString);
		// $result = $query->getResultSet();
		
		// foreach($result as $row)
		// {
		// 	echo '<pre>';
		// 	print_r($row['n']->getProperty('lidoRecID'));
		// 	echo '</pre>';
		// }

		// $_SESSION['artefacts'][] = $artefact['Artefact']['lidoRecID'];

		// echo '<pre>';
		// print_r($_SESSION);
		// echo '</pre>';


		// echo '<pre>';
		// print_r($this->client->getServerInfo());
		// echo '</pre>';

		$this->add_relationship($_SESSION['artefacts']);

		// $term = 'jug';
		// $results = $this->Artefact->find('all', array(
		// 					'fields' => array(
		// 						'_id',
		// 						'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term',
		// 						'administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource'
		// 					),
		// 					'conditions' => array(
		// 						'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => $term,
		// 						'administrativeMetadata.resourceWrap.resourceSet.1' => array('$exists' =>  'false')
		// 					),
		// 					// 'conditions' => array(
								
		// 					// ),
		// 					'limit' => 5
		// 				));

		// echo '<pre>';
		// print_r($results);
		// echo '</pre>';
		// $db = $this->Artefact->getDataSource();

		// echo '<pre>';
		// print_r(get_class_methods($db));
		// echo '</pre>';
		// $db->rawQuery(
		// 	'db.artefacts.find( {"descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term":"jug","administrativeMetadata.resourceWrap.resourceSet.1":{"$exists":"false"}})'
		// );
	}

	private function add_relationship($artefacts = array())
	{
		if(count($artefacts) > 1)
		{
			$queryString = "MATCH (n:Artefact) RETURN n LIMIT 2";
			$query = new Everyman\Neo4j\Cypher\Query($this->client, $queryString);
			$result = $query->getResultSet();

			// create relationship
			$from_node = $this->Neo4j->get_node_by_artefact_id($artefacts[(count($artefacts) - 2)]);

			echo '<pre>';
			print_r($from_node);
			echo '</pre>';

			$to_node = $this->Neo4j->get_node_by_artefact_id($artefacts[(count($artefacts) - 1)]);

			echo '<pre>';
			print_r($to_node);
			echo '</pre>';


			echo '<pre>';
			print_r($this->Neo4j->get_relationship_between_nodes($from_node->id, $to_node->id, "LEAD_TO"));
			echo '</pre>';
			// echo '<pre>';
			// print_r($this->Neo4j->create_relationship($from_node->data[0]->graph->nodes[0]->id, $to_node->data[0]->graph->nodes[0]->id, "LEAD_TO", array('count' => 1)));
			// echo '</pre>';

		}
	}

	public function get_artefacts($term)
	{
		$this->layout = 'ajax';
		$this->autoRender = false;

		$m = new MongoClient();
		// $results = $m->pastpaths->artefacts->find()->fields('')->limit(1);

		// $query = array('$project' => array('img' => ''))
		// echo '<pre>';
		// $results = $m->pastpaths->artefacts->find()->limit(1);
		// foreach($results as $document) {  
		// 	echo '<pre>';
		// 	print_r($document);
		// 	echo '</pre>';
		// } 

		// echo '</pre>';
		$db = $m->selectDB("pastpaths");

		$results = $m->pastpaths->artefacts->aggregate(
			array(
				'$match' => array(
					'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => $term
				)
			),
			array(
				'$project' => array(
					'description' => '$descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue',
					'image' => '$administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource',
					'lidoRecID' => '$lidoRecID'
				)
			),
			array(
				'$limit' => 35
			)
		);

		$ai = new AlchemyAPI(Configure::read('alchemy_api_key'));

		
		$oc = new OpenCalais(Configure::read('open_calais_api_key'));


		// foreach($results['result'] as $key => $record)
		// {
		// 	if(is_array($results['result'][$key]['description']))
		// 	{
		// 		$description = implode($results['result'][$key]['description']);
		// 	}
		// 	else
		// 	{
		// 		$description = $results['result'][$key]['description'];
		// 	}
			
		// 	// $entities = $oc->getEntities((is_array($results['result'][$key]['description']) ? implode($results['result'][$key]['description']) : $results['result'][$key]['description']));
		// 	$entities = $ai->entities('text', $description, null);
		// 	$key_words = $ai->keywords('text', $description, null);

		// 	$results['result'][$key]['entities'] = $entities['entities'];
		// 	$results['result'][$key]['key_words'] = $key_words['keywords'];
		// }

		echo json_encode($results['result']);

		// $results = $this->Artefact->find('all', array(
		// 					'fields' => array(
		// 						'_id',
		// 						'descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue',
		// 						'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term',
		// 						'administrativeMetadata.resourceWrap.resourceSet' => array('$slice' => 1)

		// 					),
		// 					'conditions' => array(
		// 						// 'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => $term,
		// 						'administrativeMetadata.resourceWrap.resourceSet.0' => array('$exists' =>  'true')
		// 					),
		// 					'limit' => 2
		// 				));

		// echo '<pre>';
		// print_r($results);
		// echo '</pre>';
		// echo json_encode($results);
	}

	public function test() 
	{
	}

	public function import_images()
	{
		$this->layout = 'ajax';
		$this->autoRender = false;
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');

		$m = new MongoClient();
		$db = $m->selectDB("pastpaths");

		$results = $m->pastpaths->artefacts->aggregate(
			array(
				'$project' => array(
					'image' => '$administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource',
					'lidoRecID' => '$lidoRecID'
				)
			)
			// array(
			// 	'$limit' => 5
			// )
		);

		foreach($results['result'] as $artefact)
		{
			$dir = new Folder('../webroot/img/artefacts/' . $artefact['lidoRecID'], true, 0755);

			$img_counter = 0;
			
			if(array_key_exists('image', $artefact))
			{
				if(is_array($artefact['image']))
				{
					foreach($artefact['image'] as $img)
					{
						if(!file_exists('../webroot/img/artefacts/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg'))
						{
							copy($img, '../webroot/img/artefacts/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg');
							$img_counter++;
						}
					}
				}
				else
				{
					if(!file_exists('../webroot/img/artefacts/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg')){
						copy($artefact['image'], '../webroot/img/artefacts/' . $artefact['lidoRecID'] . '/0.jpeg');
					}
				}
			}
		}
		

		// echo '<pre>';
		// print_r(count($results['result']));
		// echo '</pre>';
	}

	public function bubble()
	{

	}

	public function map_data(){
		$this->layout = 'ajax';
		$this->autoRender = false;

		echo json_encode($this->Artefact->get_map_artefacts(100));
	}

	public function map_node_artefacts($node_id, $limit){
		$this->layout = 'ajax';
		$this->autoRender = false;

		echo json_encode($this->Artefact->get_map_node_artefacts($node_id, $limit));	
	}

	public function import()
	{
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');

		$dir = new Folder('../webroot/files/artefact_xml_exports');
		$files = $dir->find('.*\.xml');

		// echo '<pre>';
		// print_r($files);
		// echo '</pre>';

		foreach($files as $file)
		{
			$file = new File($dir->pwd() . DS . $file);

			try
			{
				$xml = simplexml_load_file($file->path);	

				$namespaces = $xml->getNamespaces();
				$results = $xml->xpath('//lido:lido');

				$artefacts = array();

				foreach($results as $result)
				{


					echo '<pre>';
					print_r(json_decode(json_encode($result->children($namespaces['lido'])), true));
					echo '</pre>';

					break;
					// $record = $this->SimpleXML2ArrayWithCDATASupport($result->children($namespaces['lido']));
					// echo '<pre>';
					// print_r($record);
					// echo '</pre>';


					// $check = $this->Artefact->find('first', array(
					// 								'conditions' => array(
					// 									'lidoRecID' => 123
					// 								),
					// 								'limit' => 1
					// 							));

					// if(!$check)
					// {
						// $this->Artefact->create();
						// $this->Artefact->save($record);
					// }

					// $terms = (array)$result->children($namespaces['lido'])->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term;
					
					// $this->break_terms($terms);
					

					// $counter++;
				}
			}
			catch(Exception $e)
			{
				echo '<pre>';
				print_r($e);
				echo '</pre>';
			}

			$file->close();
		}
	}

	function break_terms($terms)
	{
		foreach($terms as $term)
		{
			$term = trim($term);

			// check for ands
			if(substr_count($term, '&') > 1)
			{
				$this->break_terms(explode("&", $term));
			}
			else
			{
				// check for multiple commas
				if(substr_count($term, ','))
				{
					$this->break_terms(explode(",", $term));
				}
				else
				{	
					if(array_key_exists($term, $this->all_terms))
					{
						$this->all_terms[$term]++;
					}
					else
					{
						$this->all_terms[$term] = 1;
					}
				}
			}
		}	
	}
}