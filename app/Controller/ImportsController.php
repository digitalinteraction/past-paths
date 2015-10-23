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

class ImportsController extends AppController {
    public $name = 'Imports';

    public $uses = array('Artefact', 'BrowsingSession', 'Import');

    public $all_terms = array();

    public $components = array('Neo4j');

    public $client;

    public function beforeFilter(){
       $this->client = new Everyman\Neo4j\Client('localhost', 7474);
   }

   public function constraints()
   {
       $this->Neo4j->create_contraint("Keyword", array('text'));
	// $this->Neo4j->create_contraint("Entity", array('text'));
       $this->Neo4j->create_contraint("Artefact", array('text'));
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

        // using the alchemy API terms
        // update the mongo record
            $new_keywords = [];

            foreach($keywords['keywords'] as $keyword)
            {
                $new_keywords[] = $keyword["text"];
                $queryString = "MERGE (n:Keyword {keyword : {text}}) RETURN id(n)";
                $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('text' => $keyword["text"]));
                $result = $query->getResultSet();

                $keyword_node = $result->current();

                $queryString = "START n=node({node_id}), k=node({keyword_id}) MERGE (n)-[r:HAS {relevance:{relevance}}]->(k) RETURN n,k,r";
                $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $artefact_node['n'], 'keyword_id' => $keyword_node['n'], 'relevance' => floatval($keyword['relevance'])));
                $result = $query->getResultSet();
            }
        }

    }


	// $this->Neo4j->create_contraint("Artefact", array('lidoRecID'));
	// echo '<pre>';
	// print_r($this->Neo4j->get_node_by_artefact_id(25));
	// echo '</pre>';

    }

    public function import()
    {
      ini_set('max_execution_time', 0);
      ini_set('memory_limit', '-1');

      $dir = new Folder('../webroot/files/artefact_xml_exports');
      $files = $dir->find('.*\.xml');

      $client = new Everyman\Neo4j\Client();

      echo "<br />";
      echo "Clearing mongodb collections";
      echo '<pre>';
      print_r($this->Import->create_collections());
      echo '</pre>';

      $total_records_imported = array(
                                      'mongo_artefacts' => 0,
                                      'neo4j_artefacts' => 0,
                                      'neo4j_keywords' => 0
                                      );

      // Clear graph
      $this->Neo4j->clear_graph();

      // Make labels for nodes
      $artefact_node_label = $client->makeLabel('Artefact');
      $keyword_label = $client->makeLabel('Keyword');

      // Create constraints
      $this->Neo4j->create_contraint("Keyword", array('text'));
      $this->Neo4j->create_contraint("Artefact", array('text'));

      // Loop through XML files and parse data
      foreach($files as $file)
      {
         $file = new File($dir->pwd() . DS . $file);

         echo "<br />";
         echo "Importing " . $file->name;

         try
         {
            $xml = simplexml_load_file($file->path);

            $namespaces = $xml->getNamespaces();
            $results = $xml->xpath('//lido:lido');

            $artefacts = array();

            echo "<br/>";
            echo "Importing " . count($results) . " artefacts<br />";

            $artefacts_array = [];
            $counter = 0;
            foreach($results as $result)
            {

               $record = (array)$result->children($namespaces['lido']);

               $record_json = json_encode($record);
               $artefact = json_decode($record_json,TRUE);

               $artefact['descriptions'] = array();

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

            if(!is_array($artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'])){
              $artefact['title'] = $artefact['descriptiveMetadata']['objectIdentificationWrap']['titleWrap']['titleSet']['appellationValue'];
          }
          else
          {
              $artefact['title'] = $artefact['descriptions'][0];
          }

              // if there are multiple items then break them into
          if(is_array($artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term']))
          {
              $artefact['terms'] = $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];
          }
          else
          {
              $artefact['terms'][] = $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'];
          }

          $ai = new AlchemyAPI(Configure::read('alchemy_api_key'));

          $description = implode('. ', $artefact['descriptions']);

          foreach($artefact['terms'] as $term)
          {
              $description .= ' ' . ucfirst($term) . '.';
          }

              //
          $description = str_replace(array('  '), ' ' , $description);

          $keywords = $ai->keywords('text', $description, null);

              // merge new keywords with artefact terms
          // echo '----';
          // echo '<pre>';
          // print_r($description);
          // echo '</pre>';

          // echo '<pre>';
          // print_r($artefact['terms']);
          // echo '</pre>';

              // Add Alchemy API keyterms to array
          $new_keywords = $artefact['terms'];
          if(array_key_exists('keywords', $keywords))
          {
              foreach($keywords['keywords'] as $keyword){
                $new_keywords[] = $keyword['text'];
            }
        }

              // Add existing terms with 100% relevance score to the keyword array
        foreach($artefact['terms'] as $term)
        {
          $keywords['keywords'][] = array('text' => $term, 'relevance' => 1.0);
      }

              // update existing json terms with new terms from Alchemy API
      $artefact['terms'] = $new_keywords;
      $artefact['descriptiveMetadata']['objectClassificationWrap']['objectWorkTypeWrap']['objectWorkType']['term'] = $new_keywords;

          // echo '<pre>';
          // print_r($new_keywords);
          // echo '</pre>';

          // echo '<pre>';
          // print_r($keywords);
          // echo '</pre>';

              // Insert artefact node
      $queryString = "MATCH (n:Artefact {lidoRecID : {lidoRecID}}) RETURN id(n)";
      $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact["lidoRecID"]));
      $result = $query->getResultSet();

      if($result->count() == 0)
      {
          $queryString = "MERGE (n:Artefact {lidoRecID : {lidoRecID}}) RETURN id(n)";
          $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('lidoRecID' => $artefact["lidoRecID"]));
          $result = $query->getResultSet();

          $artefact_node = $result->current();

                // using the alchemy API terms
                // update the mongo record
          $new_keywords = [];

          foreach($keywords['keywords'] as $keyword)
          {
            $new_keywords[] = $keyword["text"];
            $queryString = "MERGE (n:Keyword {keyword : {text}}) RETURN id(n)";
            $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('text' => $keyword["text"]));
            $result = $query->getResultSet();

            $keyword_node = $result->current();

            $queryString = "START n=node({node_id}), k=node({keyword_id}) MERGE (n)-[r:HAS {relevance:{relevance}}]->(k) RETURN n,k,r";
            $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('node_id' => $artefact_node['n'], 'keyword_id' => $keyword_node['n'], 'relevance' => floatval($keyword['relevance'])));
            $result = $query->getResultSet();
        }


        $counter++;
        $artefacts_array[] = $artefact;
        if($counter > 100){
            break;
        }
    }
    }

    $total_records_imported['mongo_artefacts'] += count($artefacts_array);
        // batch insert into db
    $this->Import->insert($artefacts_array);
    }
    catch(Exception $e)
    {
      echo '<pre>';
      print_r($e);
      echo '</pre>';
    }

    $file->close();
    }

    $this->Import->debug_mongodb();

    echo 'IMPORTED<br />';
    echo 'Number of Artefacts imported into mongo: ' . $total_records_imported['mongo_artefacts'];

    $queryString = "MATCH n RETURN DISTINCT LABELS(n), COUNT(n)";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array());
    $results = $query->getResultSet();

    foreach($results as $result)
    {
        $label = $result['LABELS(n)'][0];
        $count = $result['COUNT(n)'];
        echo "<br />Number of $label nodes created: $count";
    }

    echo '<br/>-------------------<br />';
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
                                                       );

      // create directories
        new Folder('../webroot/img/artefacts/medium', true, 0755);
        new Folder('../webroot/img/artefacts/large', true, 0755);

        $image = new SimpleImage();

        foreach($results['result'] as $artefact)
        {
            $dir = new Folder('../webroot/img/artefacts/medium/' . $artefact['lidoRecID'], true, 0755);
            $dir = new Folder('../webroot/img/artefacts/large/' . $artefact['lidoRecID'], true, 0755);

            $img_counter = 0;
            if(array_key_exists('image', $artefact))
            {
                if(is_array($artefact['image']))
                {
                    foreach($artefact['image'] as $img)
                    {
                        if(!file_exists('../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg'))
                        {
                            copy($img, '../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg');
                            $file = '../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg';
                            $resizedFile = '../webroot/img/artefacts/medium/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg';
                            $dimentions = getimagesize($file);
                        //   //call the function (when passing pic as string)
                            $image->load($file);
                            $image->resize(ceil($dimentions[0] / 2), ceil($dimentions[1] / 2));
                            $image->save($resizedFile);

                            $img_counter++;
                        }
                    }
                }
                else
                {
                    if(!file_exists('../webroot/img/artefacts/' . $artefact['lidoRecID'] . '/' . $img_counter . '.jpeg')){
                        copy($artefact['image'], '../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/0.jpeg');
                        $file = '../webroot/img/artefacts/large/' . $artefact['lidoRecID'] . '/0.jpeg';
                        $resizedFile = '../webroot/img/artefacts/medium/' . $artefact['lidoRecID'] . '/0.jpeg';
                        $dimentions = getimagesize($file);
                    //   //call the function (when passing pic as string)
                        $image->load($file);
                        $image->resize(ceil($dimentions[0] / 2), ceil($dimentions[1] / 2));
                        $image->save($resizedFile);
                    }
                }
            }
        }
    }
}
