<?php
App::uses('Compontent', 'Controller');
App::uses('HttpSocket', 'Network/Http');

class Neo4jComponent extends Component {

	public $api_url = "http://localhost:7474/db/data/";

	public $request_header = array(
								'header' => array(
									'Content-Type' => 'application/json',
									'Accept' => 'application/json; charset=UTF-8'
								)
							);

	private function get_api_url($action)
	{	
		// echo '<pre>';
		// print_r($this->api_url . $action);
		// echo '</pre>';
		return $this->api_url . $action;
	}

	private function build($query){
		return array(
			'statement' => $query,
			'resultDataContents' => array('REST')
			// 'resultDataContents' => array('graph')
		);
	}

	public function get_node()
	{
		
	}

	public function create_contraint($label = "", $property_keys = array())
	{
		echo '<pre>';
		print_r($this->post('schema/constraint/' . $label . '/uniqueness', array('property_keys' => $property_keys)));
		echo '</pre>';

		// echo '<pre>';
		// print_r($this->get('schema/constraint'));
		// echo '</pre>';
	}

	public function create_unique_node_cypher($properties, $label)
	{
		$parameters = "";

		foreach($properties as $key => $value)
		{
			$parameters .= " " . $key . ':' . json_encode($value) . ",";
		}

		$parameters = rtrim($parameters, ",");

		$statements = $this->build("CREATE (n:" . $label . " {" . $parameters . "}) RETURN n LIMIT 1");
		
		$results = $this->query('transaction/commit', $statements);

		return $results;
	}

	public function create_unique_node($data, $label)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		$response = $this->post('index/node/' . $label . '?uniqueness=get_or_create', $data);

		// $response = $this->post('node', $data);

		echo '<pre>';
		print_r($response);
		echo '</pre>';

		// $node_id = $response->metadata->id;

		// $this->post('node/' . $node_id . '/labels', $label);

		return $response;

	}

	public function get_node_by_artefact_id($id)
	{
		$statements = $this->build("MATCH (n:Artefact {lidoRecID : '{$id}'}) RETURN n LIMIT 1");
			
		$results = $this->query('transaction/commit', $statements);

		if($results[0])
		{
			foreach ($results as $key => $value) {
				$results[$key]->id = $results[$key]->data[0]->graph->nodes[0]->id;
			}
			
			return $results[0];
		}
		else
		{
			return false;
		}
	}

	public function create_relationship($from_id, $to_id, $type, $properties)
	{
		return $this->post('node/' . (string) $from_id . '/relationships', array('to' => (string) $to_id, 'type' => $type, 'data' => $properties));
	}


	public function get_relationship_between_nodes($from_id, $to_id, $type)
	{
		return $this->query('transaction/commit', $this->build("START n1 = node(" . $from_id .  "), n2 = node(" . $to_id . ") MATCH n1-[r:" . $type . "]->n2 return r"));
	}

	public function clear_graph()
	{
		$statements = $this->build("MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE n,r");
		$this->query('transaction/commit', $statements);
	}

	public function post($endpoint, $data)
	{
		$HttpSocket = new HttpSocket(); 
		$response = $HttpSocket->post($this->get_api_url($endpoint), json_encode($data), $this->request_header);

		return json_decode($response);
	}

	public function get($endpoint, $data = array())
	{
		$HttpSocket = new HttpSocket(); 
		return $HttpSocket->get($this->get_api_url($endpoint), json_encode($data), $this->request_header);
	}	

	public function query($endpoint, $statements, $show_errors = false)
	{

		$data = array(
			'statements' => array(
				$statements
			)
		);

		$HttpSocket = new HttpSocket(); 
		$response = $HttpSocket->post($this->get_api_url($endpoint), json_encode($data), $this->request_header);

		$response = json_decode($response);
		
		// if($show_errors == false)
		// {
		// 	return $response->results;
		// }

		return $response;
	}
}