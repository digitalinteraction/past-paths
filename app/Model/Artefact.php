<?php

App::uses('AppModel', 'Model');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

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

	private function fetch_random_artefact(){
		$collection_count = $this->collection
								->find(
									array(
										'administrativeMetadata.resourceWrap.resourceSet' => array('$exists' => true)
										)
									)
								 ->count();

		$seed = rand(0, intval(($collection_count - 1)));

		$results = $this->collection
					   ->find(
					   		array(
					   			'administrativeMetadata.resourceWrap.resourceSet' => array('$exists' => true)
					   		)
					   )
					   ->skip($seed)
					   ->limit(1);


		$artefact = $results->getNext();

		$artefact['images'] = array();

		$dir = new Folder('../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/');
		$files = $dir->find('.*\.jpeg');

		foreach($files as $file)
		{
			$img_sizes = getimagesize(WWW_ROOT . "/img/artefacts/large/" . $artefact['lidoRecID'] . '/' . $file);

			$img["url"] = $artefact['lidoRecID'] .'/' . $file;
			$img["width"] = $img_sizes[0];
			$img["height"] = $img_sizes[1];

			$artefact['images'][] = $img;
		}

		$return = array('artefact' => $artefact, 'seed' => $seed);
		return $return;
	}

	// called on page load of scroll view to fetch a random artefact + associated tiles
	public function get_random_artefact(){

		$artefact_result = $this->fetch_random_artefact();

		$artefact = $artefact_result['artefact'];
		$seed = $artefact_result['seed'];

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


		$artefact['images'] = array();

		$dir = new Folder('../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/');
		$files = $dir->find('.*\.jpeg');

		foreach($files as $file)
		{
			$img_sizes = getimagesize(WWW_ROOT . "/img/artefacts/large/" . $artefact['lidoRecID'] . '/' . $file);

			$img["url"] = $artefact['lidoRecID'] .'/' . $file;
			$img["width"] = $img_sizes[0];
			$img["height"] = $img_sizes[1];

			$artefact['images'][] = $img;
			// $artefact['images'][] = $artefact['lidoRecID'] .'/' . $file;

		}

		$artefact['descriptions'] = array();

		foreach($artefact['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
      foreach($artefact['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
        if(is_array($description))
        {
          $artefact['descriptions'][] = $description['descriptiveNoteValue'];
        }
        else
        {
          $artefact['descriptions'][] = $description;
        }
      }
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

		$artefact['lidoRecID_js'] = str_replace(array('/','.','&', '+'), "-", $artefact['lidoRecID']);

		// $_SESSION['browsing_session']['exclude_from_results'][] = $artefact['lidoRecID'];

		$return["artefact"] = $artefact;
		$return["offset"] = $seed;

		return $return;
	}

	// used to process the data from mongo format to neo graph
	public function get_all_artefacts(){
		$results = $this->collection
						->find();
						// ->fields(array(
						// 	'lidoRecID' => true,
						// ))
						// ->limit(3);

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

	// called once page is scrolled to a certain position
	// needs to incorporate previous clicks into algorithm
	public function get_artefacts($limit, $offset, $randomness, $exclude){
		$collection_count = $this->collection
								->find(
									array(
										'administrativeMetadata.resourceWrap.resourceSet' => array('$exists' => true)
										)
									)
								 ->count();

		// switch($randomness)
		// {
		// 	case 0:
		// 		$offset = $offset + rand(0, 20);
		// 		$seed = rand($offset, ($offset + 40));
		// 		break;
		// 	case 1:
		// 		$offset = $offset + rand(0, 500);
		// 		$seed = rand($offset, ($offset + 400));
		// 		break;
		// 	case 2:
		// 		$offset = $offset + rand(0, 2000);
		// 		$seed = rand($offset, ($offset + 4000));
		// 		break;
		// }

		// switch($randomness)
		// {
		// 	case 0:
		// 		$seed = $offset + rand(50, 100);
		// 		break;
		// 	case 1:
		// 		$seed = rand((int) ($offset + 100), (int) ($offset + 250));
		// 		break;
		// 	case 2:
		// 		$seed = rand((int) ($offset + 300), (int) ($offset + 600));
		// 		break;
		// }
    switch($randomness)
    {
      case 0:
        $seed = $offset + rand(ceil($collection_count * 0.01), ceil($collection_count * 0.05));
        break;
      case 1:
        $seed = ceil(rand($offset + ceil($collection_count * 0.1), $offset + ceil($collection_count * 0.25)));
        break;
      case 2:
        $seed = ceil(rand($offset + ceil($collection_count * 0.25), $offset + ceil($collection_count * 0.45)));
        break;
    }


		// if($seed >= $collection_count)
		// {
		// 	$seed = rand(0, intval((($collection_count - 1)) * 0.15 ));
		// }

    // if($seed >= $collection_count)
    // {

    // }
    //

    $seed %= $collection_count;

		if($exclude)
		{
			$results = $this->collection
						->find(
							array(
								'lidoRecID' => array('$nin' => $exclude),
								'administrativeMetadata.resourceWrap.resourceSet' => array('$exists' => true)
							)
						)
						->limit($limit)
						->skip($seed);
		}
		else
		{
			$results = $this->collection
						->find()
						->limit($limit)
						->skip($seed);
		}


		$results = iterator_to_array($results);

		$results_to_remove = array();

		foreach($results as $key => $result)
		{
			$results[$key]['images'] = array();

			$dir = new Folder('../webroot/img/artefacts/large/' . $result['lidoRecID'] . '/');
			$files = $dir->find('.*\.jpeg');

			foreach($files as $file)
			{
				$img_sizes = getimagesize(WWW_ROOT . "/img/artefacts/large/" . $result['lidoRecID'] . '/' . $file);

				$img["url"] = $result['lidoRecID'] .'/' . $file;
				$img["width"] = $img_sizes[0];
				$img["height"] = $img_sizes[1];

				$results[$key]['images'][] = $img;
			}

			if(count($results[$key]['images']) == 0)
			{
				$results_to_remove[] = $key;
			}

			// $artefact['image_root'] = "http://localhost/past-paths-images/artefact_images/";
			// $artefact['image'] = "medium/" . $artefact['lidoRecID'] . "/0.jpeg";
			$results[$key]['descriptions'] = array();

			foreach($results[$key]['descriptiveMetadata']['objectIdentificationWrap']['objectDescriptionWrap']['objectDescriptionSet'] as $description){
				if(is_array($description))
        {
          $results[$key]['descriptions'][] = $description['descriptiveNoteValue'];
        }
        else
        {
          $results[$key]['descriptions'][] = $description;
        }
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

			$results[$key]['lidoRecID_js'] = str_replace(array('/','.','&', '+'), "-", $results[$key]['lidoRecID']);

			// $results['exclude_from_results'][] = $results[$key]['lidoRecID'];
		}


		foreach($results_to_remove as $remove_result)
		{
			unset($results[$remove_result]);
		}

		$response['record_offset'] = (int) $seed;
		$response['results'] = $results;
		$response['exclude_from_results'] = $results_to_remove;

		return $response;
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

		$links = array();

		foreach($keywords as $keyword)
		{
			$client = new Everyman\Neo4j\Client();
			$queryString = "MATCH (k:Keyword)-[r:RELATED_KEY]-(k2:Keyword) WHERE ID(k)={node_id} AND r.weight > {greater_than_weight} RETURN k2, r ORDER BY r.weight DESC LIMIT {limit}";
			$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $keyword['node_id'], 'greater_than_weight' => 1,'limit' => 10));
			$neo_result = $query->getResultSet();

			foreach($neo_result as $row)
			{
				$link = array();
				$link['source'] = $keyword['node_id'];
				$link['target'] = $row['k2']->getId();
				$link['value'] = $row['r']->weight;
				$links[] = $link;
			}

		}

		$return = array();
		$return['nodes'] = $keywords;
		$return['links'] = $links;

		return $return;
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

	public function get_more_map_nodes($current_nodes_displayed, $limit){
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

	// void
	public function record_click($from_node_id, $to_node_id, $session_id){
		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (from: Artefact {lidoRecID : {from_lido_id}}),(to: Artefact {lidoRecID : {to_lido_id}}) MERGE (from)-[r:VIEWED {session_id : {session_id}}]->(to) ON CREATE SET r+= { created: timestamp(), views: 1 } ON MATCH SET r.views = r.views + 1 RETURN r;";

		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('from_lido_id' => $from_node_id, 'to_lido_id' => $to_node_id, 'session_id' => $session_id));

		$neo_result = $query->getResultSet();
	}

	public function get_session_data_map($s_id){
		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (keyword)-[link:HAS]-(artefact)-[viewed:VIEWED {session_id : {s_id} }]-(child) RETURN keyword, artefact, viewed, child";
		// $queryString = "MATCH (keyword)-[link:HAS]-(artefact)-[viewed:VIEWED {session_id : {s_id} }]-() RETURN keyword, artefact, viewed";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('s_id' => $s_id));
		$neo_result = $query->getResultSet();

		$keywords = array();
		$artefacts = array();
		$links = array();

		$artefact_links = array();

		$artefact_weights = array();

		$artefact_node_ids = array();

		$unique_links = array();

		$children = array();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				$keyword = array();
				$keyword['node_id'] = $row['keyword']->getId();
				$keyword['keyword'] = $row['keyword']->keyword;
				$keyword['artefact_count'] = rand(25, 80);
				$keyword['weight'] = 2;
				$keywords[] = $keyword;

				// if(!array_key_exists($row['artefact']->getId() . "," . $row['keyword']->getId(), $unique_links))
				// {
				// 	$link = array();
				// 	$link['source'] = $row['artefact']->getId();
				// 	$link['target'] = $row['keyword']->getId();
				// 	// $link['value'] = rand(25, 100);
				// 	$link['value'] = 0.05;
				// 	$link['type'] = 'keyword';
				// 	$links[] = $link;
				// 	$unique_links[$row['artefact']->getId() . "," . $row['keyword']->getId()] = null;
				// }

				$source = $row['artefact']->getId();
				$target = $row['keyword']->getId();

				// if(!array_key_exists($source, $unique_links))
				// {
				// 	$unique_links[$source][] = $target;
				// }
				// else
				// {
				// 	if(!in_array($target, $unique_links[$source]))
				// 	{
				// 		$unique_links[$source][] = $target;

				// 		$link = array();
				// 		$link['source'] = $row['artefact']->getId();
				// 		$link['target'] = $row['keyword']->getId();
				// 		$link['value'] = 0.05;
				// 		$link['type'] = 'keyword';

				// 		$links[] = $link;
				// 	}
				// }

				// keyword link
				$link = array();
				$link['source'] = $row['artefact']->getId();
				$link['target'] = $row['keyword']->getId();
				$link['value'] = 0.05;
				$link['type'] = 'keyword';

				$links[] = $link;

				// parent child link
				// $link = array();
				// $link['source'] = $row['artefact']->getId();
				// $link['target'] = $row['child']->getId();
				// $link['value'] = 0.05;
				// $link['type'] = 'artefact';

				// $links[] = $link;



				// if(!in_array($row['artefact']->getId(), $artefact_weights))
				// {
				// 	$artefact_weights[$keyword['node_id']] = 0;
				// }
				// else
				// {
				// 	$artefact_weights[$keyword['node_id']]++;
				// }


				if(!in_array($row['artefact']->getId(), $artefact_node_ids))
				{
					$artefact['node_id'] = $row['artefact']->getId();
					$artefact['lidoRecID'] = $row['artefact']->getProperty('lidoRecID');
					$artefact['weight'] = 2;
					$artefacts[] = $artefact;
					$artefact_node_ids[] = $row['artefact']->getId();
				}


				// $artefact['node_id'] = $row['child']->getId();
				// $artefact['lidoRecID'] = $row['child']->getProperty('lidoRecID');
				// $artefact['weight'] = 2;
				// $artefacts[] = $artefact;
				// $artefact_node_ids[] = $row['child']->getId();

				// $children[] = $artefact;

				// for each artefact, go fetch any other artefacts that have been viewed from this artefact
				// $client = new Everyman\Neo4j\Client();
				// $queryString = "MATCH (parent_artefact:Artefact { lidoRecID : {parent_lidoRecID} })-[r:VIEWED]-(child_artefact:Artefact) RETURN parent_artefact, child_artefact";
				// $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('parent_lidoRecID' => $row['artefact']->getProperty('lidoRecID')));
				// $neo_result = $query->getResultSet();

				// foreach($neo_result as $row)
				// {
				// 	$artefact = array();
				// 	$artefact['node_id'] = $row['artefact']->getId();
				// 	$artefact['lidoRecID'] = $row['artefact']->getProperty('lidoRecID');
				// 	$artefact['value'] = 50;
				// 	$artefact_links[] = $artefact;

				// 	$link = array();
				// 	$link['source'] = $row['parent_artefact']->getId();
				// 	$link['target'] = $row['child_artefact']->getId();
				// 	$link['type'] = "artefact";
				// 	// $link['value'] = rand(25, 100);
				// 	$link['value'] = 50;
				// 	$links[] = $link;
				// }

			}
		}



		// echo '<pre>';
		// print_r($unique_links);
		// echo '</pre>';

		// echo '<pre>';
		// print_r($artefact_weights);
		// echo '</pre>';

		// foreach($links as $link)
		// {
		// 	if(array_key_exists($link['target'], $keyword_weights))
		// 	{
		// 		$keyword_weights[$link['target']]++;
		// 	}
		// }

		// echo '<pre>';
		// print_r($keywords);
		// echo '</pre>';

		// echo '<pre>';
		// print_r($keyword_weights);
		// echo '</pre>';
		// echo '<pre>';
		// print_r($links);
		// echo '</pre>';

		// foreach($keywords as $keyword)
		// {
		// 	$client = new Everyman\Neo4j\Client();
		// 	$queryString = "MATCH (k:Keyword)-[r:RELATED_KEY]-(k2:Keyword) WHERE ID(k)={node_id} AND r.weight > {greater_than_weight} RETURN k2, r ORDER BY r.weight DESC LIMIT {limit}";
		// 	$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $keyword['node_id'], 'greater_than_weight' => 1,'limit' => 10));
		// 	$neo_result = $query->getResultSet();

		// 	foreach($neo_result as $row)
		// 	{
		// 		$link = array();
		// 		$link['source'] = $keyword['node_id'];
		// 		$link['target'] = $row['k2']->getId();
		// 		$link['value'] = $row['r']->weight;
		// 		$links[] = $link;
		// 	}

		// }

		$return = array();
		$return['nodes'] = $keywords;
		$return['artefact_nodes'] = $artefacts;
		$return['links'] = $links;
		$return['artefact_links'] = $artefact_links;

		return $return;

	}

	// public function get_artefact_sample_by_keyword($keyword)
	// {
	// 	$client = new Everyman\Neo4j\Client();
	// 		$queryString = "MATCH (k:Keyword {keyword})-[r:RELATED_KEY]-(k2:Keyword) WHERE ID(k)={node_id} AND r.weight > {greater_than_weight} RETURN k2, r ORDER BY r.weight DESC LIMIT {limit}";
	// 		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $keyword['node_id'], 'greater_than_weight' => 1,'limit' => 10));
	// 		$neo_result = $query->getResultSet();
	// }

	public function get_artefacts_by_keyword($keyword){

		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (artefact:Artefact)-[:HAS]->(keyword:Keyword{keyword:{keyword}}) RETURN artefact, keyword LIMIT 10";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('keyword' => $keyword));
		$neo_result = $query->getResultSet();

		$artefacts = array();
		$keywords = array();
		$artefact_node_ids = array();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				$keyword = array();
				$keyword['node_id'] = $row['keyword']->getId();
				$keyword['keyword'] = $row['keyword']->keyword;
				$keywords[] = $keyword;

				$link = array();
				$link['source'] = $row['artefact']->getId();
				$link['target'] = $row['keyword']->getId();
				$link['value'] = 0.05;
				$link['type'] = 'keyword';

				$links[] = $link;

				if(!in_array($row['artefact']->getId(), $artefact_node_ids))
				{
					$artefact['node_id'] = $row['artefact']->getId();
					$artefact['lidoRecID'] = $row['artefact']->getProperty('lidoRecID');
					$artefact['weight'] = 2;
					$artefacts[] = $artefact;
					$artefact_node_ids[] = $row['artefact']->getId();
				}
			}
		}

		$return = array();
		$return['nodes'] = $keywords;
		$return['artefact_nodes'] = $artefacts;
		$return['links'] = $links;

		return $return;
	}

	public function get_keywords_by_artefact_lido_id($lidoRecID){

		$client = new Everyman\Neo4j\Client();
		$queryString = "MATCH (artefact:Artefact{lidoRecID:{lido_rec_id}})-[:HAS]->(keyword:Keyword) RETURN artefact, keyword LIMIT 10";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lido_rec_id' => $lidoRecID));
		$neo_result = $query->getResultSet();

		$artefacts = array();
		$keywords = array();
		$artefact_node_ids = array();

		if($neo_result->count() > 0)
		{
			foreach($neo_result as $row)
			{
				$keyword = array();
				$keyword['node_id'] = $row['keyword']->getId();
				$keyword['keyword'] = $row['keyword']->keyword;
				$keywords[] = $keyword;

				$link = array();
				$link['source'] = $row['artefact']->getId();
				$link['target'] = $row['keyword']->getId();
				$link['value'] = 0.05;
				$link['type'] = 'keyword';

				$links[] = $link;

				if(!in_array($row['artefact']->getId(), $artefact_node_ids))
				{
					$artefact['node_id'] = $row['artefact']->getId();
					$artefact['lidoRecID'] = $row['artefact']->getProperty('lidoRecID');
					$artefact['weight'] = 2;
					$artefacts[] = $artefact;
					$artefact_node_ids[] = $row['artefact']->getId();
				}
			}
		}

		$return = array();
		$return['nodes'] = $keywords;
		$return['artefact_nodes'] = $artefacts;
		$return['links'] = $links;

		return $return;
	}

	public function get_artefact_by_lidoRecId($lidoRecID){
		$artefact = $this->collection
					   ->findOne(
					   		array(
					   			'lidoRecID' => $lidoRecID
					   		)
					   );

		if(!$artefact)
		{
			return false;
		}

		$artefact['images'] = array();

		$dir = new Folder('../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/');
		$files = $dir->find('.*\.jpeg');

		foreach($files as $file)
		{
			$img_sizes = getimagesize(WWW_ROOT . "/img/artefacts/large/" . $artefact['lidoRecID'] . '/' . $file);

			$img["url"] = $artefact['lidoRecID'] .'/' . $file;
			$img["width"] = $img_sizes[0];
			$img["height"] = $img_sizes[1];

			$artefact['images'][] = $img;
			// $artefact['images'][] = $artefact['lidoRecID'] .'/' . $file;

		}

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

		$artefact['lidoRecID_js'] = str_replace(array('/','.','&', '+'), "-", $artefact['lidoRecID']);

		return $artefact;
	}

	public function get_artefact_by_objectId($objectId){
		$artefact = $this->collection
					   ->findOne(
					   		array(
					   			'_id' => new MongoId($objectId)
					   		)
					   );


		$artefact['images'] = array();

		$dir = new Folder('../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/');
		$files = $dir->find('.*\.jpeg');

		foreach($files as $file)
		{
			$img_sizes = getimagesize(WWW_ROOT . "/img/artefacts/large/" . $artefact['lidoRecID'] . '/' . $file);

			$img["url"] = $artefact['lidoRecID'] .'/' . $file;
			$img["width"] = $img_sizes[0];
			$img["height"] = $img_sizes[1];

			$artefact['images'][] = $img;
			// $artefact['images'][] = $artefact['lidoRecID'] .'/' . $file;

		}

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

		$artefact['lidoRecID_js'] = str_replace(array('/','.','&', '+'), "-", $artefact['lidoRecID']);

		return $artefact;
	}

	public function launch_from_lido_rec_id($lidoRecID){
		$lido_rec_ids = $this->collection
							 ->find()
							 ->fields(array('lidoRecID' => true));


		$results = iterator_to_array($lido_rec_ids);

		$record_index_counter = -1;

		$record_index = -1;
		$record_id = -1;

		foreach($results as $index => $result)
		{
			if($result['lidoRecID'] == $lidoRecID)
			{
				$record_index = $record_index_counter;
				$record_id = $index;
			}

			$record_index_counter++;
		}

		// check we have results

		if($record_index > -1)
		{
			// public function get_artefacts($limit, $offset, $randomness, $exclude){
			$response = $this->get_artefacts(50, $record_index, 0, array());

			$initial_artefact = $this->get_artefact_by_lidoRecId($lidoRecID);

			array_unshift($response['results'], $initial_artefact);

			return $response;
		}

		return false;
	}

  public function batchInsert($artefact) {
    try{
      $this->collection->batchInsert($artefact);
    }
    catch (MongoCursorException $mce) {
      echo '<pre>';
      print_r($mce);
      echo '</pre>';
    }
  }

}
