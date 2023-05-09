<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");		
	}


	public function add($data){
		$save_data = [
			"customer_uid" => $data["comm_cus_id"],
			"comments"     => $data["ds_description"]
		];
		$this->db->insert("comments", $save_data);
		return $this->db->insert_id();
	}

	
}

/* End of file Admin_model.php */
/* Location: ./application/models/Admin_model.php */