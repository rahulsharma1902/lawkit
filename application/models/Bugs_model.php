<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bugs_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");		
	}


	public function add($data){
		#$data["daytype"]
		#2 = habiles
		#1 = naturales
		$days_type = "Hábiles \r\n";
		$description = $data['error_description'] . "\r\n";
		$description .= "Inicio: " . $data["hrd_start"] . "\r\n";
		$description .= "Fin: " . $data["hrd_end"] . "\r\n";
		$description .= "Plazo: " .  $data["add"] . "\r\n";
		if(!empty($data["effect"]) || $data["effect"] != 0)
			$description .= "Surte efectos \r\n";
		if($data["daytype"] != 2)
			$days_type = "Naturales \r\n";
		$description .= $days_type;
		if(!empty($data["calendar_id"]))
			$description .= "Id del calendario: " . $data["calendar_id"] . "\r\n";
		$save_data = [
			"customer_uid"      => $data["customer_uid"],
			"error_description" => $description
		];
		$this->db->insert("bug_report", $save_data);
		return $this->db->insert_id();
	}

	
}

/* End of file Admin_model.php */
/* Location: ./application/models/Admin_model.php */