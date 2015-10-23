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

class Import extends AppModel {
  public $name = "Import";

  var $useDbConfig = 'default';

  public $tbl = '';

  public $collection;

  public $m;

  public $db;

  public function __construct(){
    $m = new MongoClient();
    $this->m = $m;
    $db = $m->selectDB($this->getDataSource()->config['database']);
    $this->db = $db;
    $collections = $db->listCollections();

    foreach ($collections as $collection) {
        echo "amount of documents in $collection: ";
        echo $collection->count(), "\n";
    }

    // $collection = $db->selectCollection($this->tbl);
    // $this->collection = $collection;
  }

  public function debug_mongodb() {
    $collections = $this->db->listCollections();

    echo '--------------<br />';
    foreach ($collections as $collection) {
        echo "Number of documents in $collection: ";
        echo $collection->count(), "\n<br />";
    }
    echo '--------------<br />';
  }

  public function insert($artefacts) {
    try{
      $collection = $this->db->selectCollection("artefacts");
      $collection->batchInsert($artefacts);
    }
    catch (MongoCursorException $mce) {
      echo '<pre>';
      print_r($mce);
      echo '</pre>';
    }
  }

  public function create_collections() {
    $collection = $this->db->selectCollection("artefacts");
    $collection->drop();
    $this->m->db->createCollection("artefacts");
  }
}
