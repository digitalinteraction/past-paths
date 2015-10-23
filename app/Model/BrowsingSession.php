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

class BrowsingSession extends AppModel {
	public $name = "browsingsession";
	
	var $useDbConfig = 'default';

	public $tbl = 'browsingsession';

	public $collection;

	public $m;

	public function __construct(){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection = $collection;
	}

	public function get($s_id){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		return $collection->findOne(array('_id' => new MongoId($s_id)));
	}

	public function getAll(){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		return iterator_to_array($collection->find(array('start_time' => array('$gt' => 1427885794))));

	}

	public function insert($session){
		$this->collection->insert($session);

		return $session;
	}

	public function update_end_time($_id){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection->update(
		    array('_id' => new MongoId($_id)),
		    array(
		        '$set' => array("end_time" => time()),
		    )
		);
	}

	public function update_exclude_from_results($_id, $exclude_from_results){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection->update(
		    array('_id' => new MongoId($_id)),
		    array(
		        '$set' => array("exclude_from_results" => $exclude_from_results),
		    )
		);
	}

	public function update_viewed($_id, $viewed){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection->update(
		    array('_id' => new MongoId($_id)),
		    array(
		        '$set' => array("viewed" => $viewed),
		    )
		);
	}

	public function update_field($_id, $field, $value){
		$m = new MongoClient();
		$this->m = $m;
		$db = $m->selectDB($this->getDataSource()->config['database']);
		$collection = $db->selectCollection($this->tbl);
		$this->collection->update(
		    array('_id' => new MongoId($_id)),
		    array(
		        '$set' => array($field => $value),
		    )
		);
	}

}
