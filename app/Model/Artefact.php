<?php

App::uses('AppModel', 'Model');

spl_autoload_register(function ($class) {
    foreach (App::path('Vendor') as $base) {
        $path = $base . str_replace('\\', DS, $class) . '.php';
        if (file_exists($path)) {
            include $path;
            return;
        }
    }
});

class Artefact extends AppModel {
	public $name = "Artefact";
	
	var $useDbConfig = 'default';

	public $tbl = 'artefacts';

	public $collection;

	public $m;

	public function __construct(){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection = $collection;
	}

	public function get_random_artefact(){
		$collection_count = $this->collection
								 ->count();

		$seed = rand(0, ($collection_count - 1));

		// $seed = 0;

		$results = $this->collection
					   ->find()
					   ->limit(10)
					   ->skip($seed);


		
		$this->m->close();

		$artefact = $results->getNext();

		$client = new Everyman\Neo4j\Client();

		// $artefact['lidoRecID'] = "emu.ecatalogue.decorativeart&design.284446";

		$queryString = "MATCH (n:Artefact {lidoRecID : {lidoRecID}})-[r:HAS]-(k:Keyword) RETURN n, r, k";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact["lidoRecID"]));
		$neo_result = $query->getResultSet();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				if(!array_key_exists('node', $artefact))
				{
					$artefact['node'] = $row['n'];
				}

				$artefact['keyword_nodes'][] = $row['k'];
			}
		}


		$artefact['image_root'] = $this->webroot . "../app/webroot/img/artefacts";
		// $artefact['image_root'] = WEBROOT_DIR;
		// $artefact['image'] = 
		$artefact['images']['large'] = $artefact['image_root'] . '/large/' . $artefact['lidoRecID'] . "/0.jpeg";
		$artefact['images']['medium'] = $artefact['image_root'] . '/medium/' . $artefact['lidoRecID'] . "/0.jpeg";
		
		// $artefact['image_root'] = "http://localhost/past-paths-images/artefact_images/";
		// $artefact['image'] = "medium/" . $artefact['lidoRecID'] . "/0.jpeg";
		$artefact['descriptions'] = array();

		foreach($artefact['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
			$artefact['descriptions'][] = $description['descriptiveNoteValue'];
		}

		if(!is_array($artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
			$artefact['title'] = $artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];	
		}
		else
		{
			$artefact['title'] = $artefact['descriptions'][0];
		}
	
		$artefact['terms'] = $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];

		$artefact['events'] = array();
		$artefact['measurements'] = array();

		// echo '<pre>';
		// print_r((bool)count(array_filter(array_keys($artefact['descriptiveMetadata']['eventWrap']['eventSet']), 'is_string')));
		// echo '</pre>';

		if(!empty($artefact['descriptiveMetadata']['eventWrap']))
		{
			if((bool)count(array_filter(array_keys($artefact['descriptiveMetadata']['eventWrap']['eventSet']), 'is_string')))
			{
				$value = $artefact['descriptiveMetadata']['eventWrap']['eventSet'];
				unset($artefact['descriptiveMetadata']['eventWrap']['eventSet']);
				$artefact['descriptiveMetadata']['eventWrap']['eventSet'][] = $value;	
			}

			foreach($artefact['descriptiveMetadata']['eventWrap']['eventSet'] as $event_record)
			{
				$artefact['events'][] = $event_record;
			}
		}

		if(!empty($artefact['descriptiveMetadata']['objectMeasurementsWrap']))
		{
			if((bool)count(array_filter(array_keys($artefact['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet']), 'is_string')))
			{
				$value = $artefact['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'];
				unset($artefact['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet']);
				$artefact['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'][] = $value;	
			}

			foreach($artefact['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'] as $measurement_record)
			{
				$artefact['measurements'][] = $measurement_record;
			}
		}
		
		$artefact['lidoRecID_js'] = str_replace(array('/','.','&'), "-", $artefact['lidoRecID']);

		$_SESSION['browsing_session']['exclude_from_results'][] = $artefact['lidoRecID'];

		return $artefact;
	}

	public function recommend_artefacts(){
		$already_recommended_lido_rec_ids = $_SESSION['browsing_session']['exclude_from_results'];
		// $_SESSION['browsing_session']['exclude_from_results'][] = $artefact['lidoRecID'];
			
		$artefact_lido_rec_id = "emu.ecatalogue.decorativeart&design.284446";

		// $queryString = "MATCH (n:Artefact {lidoRecID : {lidoRecID}})-[r:HAS]-(k:Keyword) RETURN n, r, k";
		// $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact_lido_rec_id));
		// $neo_result = $query->getResultSet();

		// if($neo_result->count() > 0)
		// {
		// 	foreach($neo_result as $row)
		// 	{
		// 		if(!array_key_exists('node', $artefact))
		// 		{
		// 			$artefact['node'] = $row['n'];
		// 		}

		// 		$artefact['keyword_nodes'][] = $row['k'];
		// 	}
		// }




		$results = $this->collection
						->find(
							array(
								'lidoRecID' => array('$nin' => $already_recommended_lido_rec_ids)
							)
						)
						// ->fields(
						// 	array(
						// 		'lidoRecID' => true
						// 	)
						// )
						->limit(50);

		$this->m->close();

		$results = iterator_to_array($results);

		foreach($results as $key => $result)
		{
			$results[$key]['image_root'] = "img/artefacts";
			// $artefact['image'] = 
			$results[$key]['images']['large'] = $results[$key]['image_root'] . '/large/' . $results[$key]['lidoRecID'] . "/0.jpeg";
			$results[$key]['images']['medium'] = $results[$key]['image_root'] . '/medium/' . $results[$key]['lidoRecID'] . "/0.jpeg";
			
			// $artefact['image_root'] = "http://localhost/past-paths-images/artefact_images/";
			// $artefact['image'] = "medium/" . $artefact['lidoRecID'] . "/0.jpeg";
			$results[$key]['descriptions'] = array();

			foreach($results[$key]['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
				$results[$key]['descriptions'][] = $description['descriptiveNoteValue'];
			}

			if(!is_array($results[$key]['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
				$results[$key]['title'] = $results[$key]['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];	
			}
			else
			{
				$results[$key]['title'] = $results[$key]['descriptions'][0];
			}
		
			$results[$key]['terms'] = $results[$key]['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];

			$results[$key]['lidoRecID_js'] = str_replace('&', '-', str_replace('.', '-', $results[$key]['lidoRecID']));

			$_SESSION['browsing_session']['exclude_from_results'][] = $results[$key]['lidoRecID'];
		}

		return $results;
	}

	public function get_artefact(){	
		$seed = rand(0, 32000);	
		$results = $this->collection
						->find(
							// array(
							// 	'descriptiveMetadata.objectIdentificationWrap.titleWrap.titleSet.appellationValue' => array('$exists' => false)
							// )
						)
						->limit(1)
						->skip($seed);
						// ->fields(
						// 	array(
						// 		'lidoRecID' => true,
						// 		'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType' => true
						// 	)
						// )

		// foreach($results as $result)
		// {
		// 	// echo '<pre>';
		// 	// print_r($result['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term']);
		// 	// echo '</pre>';
		// 	$update_data = array('$set' => array('descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => array($result['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'])));

		// 	$this->collection->update(
		// 		array(
		// 			'lidoRecID' => $result['lidoRecID']
		// 		),
		// 		$update_data
		// 	);
		// }
		// echo '<pre>';
		// print_r($results->explain());
		// echo '</pre>';
		// $results = $m->db->artefacts->aggregate(
		// 	array(
		// 		'$match' => array(
		// 			'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => $term
		// 		)
		// 	),
		// 	array(
		// 		'$project' => array(
		// 			'description' => '$descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue',
		// 			'image' => '$administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource',
		// 			'lidoRecID' => '$lidoRecID'
		// 		)
		// 	),
		// 	array(
		// 		'$limit' => 10
		// 	)
		// );



		$this->m->close();

		$artefact = $results->getNext();

		$artefact['image_root'] = $this->webroot . "app/webroot/img/";
		$artefact['image'] = "artefacts/" . $artefact['lidoRecID'] . "/0.jpeg";
		// $artefact['image_root'] = "http://localhost/past-paths-images/artefact_images/";
		// $artefact['image'] = "medium/" . $artefact['lidoRecID'] . "/0.jpeg";
		$artefact['descriptions'] = array();

		foreach($artefact['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
			$artefact['descriptions'][] = $description['descriptiveNoteValue'];
		}
		
		if(!is_array($artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
			$artefact['title'] = $artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];	
		}
		else
		{
			$artefact['title'] = $artefact['descriptions'][0];
		}


		// break title

		// $title = explode(' ', $artefact['title']);

		
		$artefact['terms'] = $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];

		return $artefact;
	}

	public function get_all_artefacts(){
		$results = $this->collection
						->find();
						// ->fields(array(
						// 	'lidoRecID' => true,
						// ))
						// ->limit(3);
		
		$this->m->close();


		$artefacts = array();
		foreach($results as $artefact)
		{
			$artefact['descriptions'] = array();

			foreach($artefact['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
				$artefact['descriptions'][] = $description['descriptiveNoteValue'];
			}
			
			if(!is_array($artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
				$artefact['title'] = $artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];	
			}
			else
			{
				$artefact['title'] = $artefact['descriptions'][0];
			}
			
			$artefact['terms'] = $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];

			$artefacts[] = $artefact;
		}

		return $artefacts;
	}

	public function get_artefacts($limit, $offset, $randomness){
		$already_recommended_lido_rec_ids = $_SESSION['browsing_session']['exclude_from_results'];

		switch($randomness)
		{
			case 0:
				$offset = $offset + rand(0, 20);
				$seed = rand($offset, ($offset + 40));	
				break;
			case 1:
				$offset = $offset + rand(0, 500);
				$seed = rand($offset, ($offset + 400));	
				break;
			case 2:
				$offset = $offset + rand(0, 2000);
				$seed = rand($offset, ($offset + 4000));	
				break;
		}

		$results = $this->collection
						->find(
							array(
								'lidoRecID' => array('$nin' => $already_recommended_lido_rec_ids)
							)
							// array('lidoRecID' => 'emu.ecatalogue.britisharchaeology.241288')
						)
						->limit(100)
						->skip($seed);
						// ->fields(
						// 	array(
						// 		'lidoRecID' => true,
						// 		'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType' => true
						// 	)
						// )

		// $results = $this->collection
		// 				->find(
		// 					array(
		// 						'lidoRecID' => 'emu.ecatalogue.paintings.300076'
		// 					)
		// 				)
		// 				->limit(10);

		// foreach($results as $result)
		// {
		// 	// echo '<pre>';
		// 	// print_r($result['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term']);
		// 	// echo '</pre>';
		// 	$update_data = array('$set' => array('descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => array($result['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'])));

		// 	$this->collection->update(
		// 		array(
		// 			'lidoRecID' => $result['lidoRecID']
		// 		),
		// 		$update_data
		// 	);
		// }
		// echo '<pre>';
		// print_r($results->explain());
		// echo '</pre>';
		// $results = $m->db->artefacts->aggregate(
		// 	array(
		// 		'$match' => array(
		// 			'descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term' => $term
		// 		)
		// 	),
		// 	array(
		// 		'$project' => array(
		// 			'description' => '$descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue',
		// 			'image' => '$administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource',
		// 			'lidoRecID' => '$lidoRecID'
		// 		)
		// 	),
		// 	array(
		// 		'$limit' => 10
		// 	)
		// );


		$this->m->close();

		$results = iterator_to_array($results);

		foreach($results as $key => $result)
		{
			$results[$key]['image_root'] = $this->webroot;
			// $artefact['image'] = 
			$results[$key]['images']['large'] = $results[$key]['image_root'] . '/large/' . $results[$key]['lidoRecID'] . "/0.jpeg";
			$results[$key]['images']['medium'] = $results[$key]['image_root'] . '/medium/' . $results[$key]['lidoRecID'] . "/0.jpeg";
			
			// $artefact['image_root'] = "http://localhost/past-paths-images/artefact_images/";
			// $artefact['image'] = "medium/" . $artefact['lidoRecID'] . "/0.jpeg";
			$results[$key]['descriptions'] = array();

			foreach($results[$key]['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
				$results[$key]['descriptions'][] = $description['descriptiveNoteValue'];
			}

			if(!is_array($results[$key]['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
				$results[$key]['title'] = $results[$key]['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];	
			}
			else
			{
				$results[$key]['title'] = $results[$key]['descriptions'][0];
			}
		
			$results[$key]['terms'] = $results[$key]['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];

			$results[$key]['events'] = array();
			$results[$key]['measurements'] = array();

			// echo '<pre>';
			// print_r((bool)count(array_filter(array_keys($results[$key]['descriptiveMetadata']['eventWrap']['eventSet']), 'is_string')));
			// echo '</pre>';

			if(!empty($results[$key]['descriptiveMetadata']['eventWrap']))
			{
				if((bool)count(array_filter(array_keys($results[$key]['descriptiveMetadata']['eventWrap']['eventSet']), 'is_string')))
				{
					$value = $results[$key]['descriptiveMetadata']['eventWrap']['eventSet'];
					unset($results[$key]['descriptiveMetadata']['eventWrap']['eventSet']);
					$results[$key]['descriptiveMetadata']['eventWrap']['eventSet'][] = $value;	
				}

				foreach($results[$key]['descriptiveMetadata']['eventWrap']['eventSet'] as $event_record)
				{
					$results[$key]['events'][] = $event_record;
				}
			}

			if(!empty($results[$key]['descriptiveMetadata']['objectMeasurementsWrap']))
			{
				if((bool)count(array_filter(array_keys($results[$key]['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet']), 'is_string')))
				{
					$value = $results[$key]['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'];
					unset($results[$key]['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet']);
					$results[$key]['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'][] = $value;	
				}

				foreach($results[$key]['descriptiveMetadata']['objectMeasurementsWrap']['objectMeasurementsSet'] as $measurement_record)
				{
					$results[$key]['measurements'][] = $measurement_record;
				}
			}
			
			$results[$key]['lidoRecID_js'] = str_replace(array('/','.','&'), "-", $results[$key]['lidoRecID']);

			$_SESSION['browsing_session']['exclude_from_results'][] = $results[$key]['lidoRecID'];
		}

		return $results;
	}

	public function convert_record_descriptions(){
		$results = $this->collection
						->find(
							array(
								'descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.0' => array('$exists' => false)
							)
						);

		foreach($results as $result)
		{
			$update_data = array('$set' => array('descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet' => array($result['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'])));
			$this->collection->update(
				array(
					'lidoRecID' => $result['lidoRecID']
				),
				$update_data
			);
		}
	}

	// Get random nodes
	// MATCH (k:Keyword)
	// WITH k, rand() AS number
	// RETURN k
	// ORDER BY number
	// LIMIT 100

	// Get most connected keywords and associated artefacts
	// MATCH (k:Keyword)-[]-(a:Artefact)
	// WITH k, COUNT(a) AS artefact_count
	// WHERE artefact_count < 10
	// RETURN k, artefact_count
	// ORDER BY artefact_count DESC
	// LIMIT 10

	// (Keywork)-(Artefact)-(Keyword) - Add relationship between related keyword nodes that are related by artefacts
	// MATCH (n:Keyword)-[r*2]-(x:Keyword)
	// WITH n, COUNT(r) AS c, x
	// LIMIT 1
	// MERGE (n)-[s:RELATED_KEY]-(x) SET s.weight = c


	public function get_map_artefacts($limit){
		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (k:Keyword)-[]-(a:Artefact) WITH k, COUNT(a) AS artefact_count RETURN k, artefact_count ORDER BY artefact_count DESC LIMIT {limit}";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('limit' => $limit));
		$neo_result = $query->getResultSet();

		$keywords = array();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				$keyword = array();

				$keyword['node_id'] = $row['k']->getId();
				$keyword['keyword'] = $row['k']->keyword;
				$keyword['artefact_count'] = $row['artefact_count'];
				$keywords[] = $keyword;
			}
		}

		return $keywords;
	}

	public function get_map_node_artefacts($node_id, $limit){
		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (k:Keyword)-[:HAS]-(a:Artefact) WHERE id(k) = {node_id} RETURN k, a LIMIT {limit}";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => (int) $node_id, 'limit' => (int) $limit));
		$neo_result = $query->getResultSet();

		$artefacts = array();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				$artefact = array();

				$artefact['node_id'] = $row['a']->getId();
				$artefact['lidoRecID'] = $row['a']->getProperty('lidoRecID');
				$artefacts[] = $artefact;
			}
		}

		return $artefacts;	
	}
}