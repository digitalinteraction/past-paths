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

	public function insert($session){
		$this->collection->insert($session);
		echo '<pre>';
		print_r($session);
		echo '</pre>';

		// $this->collection->insert($session);

		// $session = $this->collection
		// 			   	->findOne(array('session_id' => $session['s_id']));
		// echo '<pre>';
		// print_r($session);
		// echo '</pre>';
	}
}