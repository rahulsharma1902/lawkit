<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function update_activity($data, $uid){
		$data = json_decode($data);
		$activity = array(
			'customer_uid'	          => $uid,
			'os'	                  => $data->OS,
			'browser'	              => $data->browser,
			'browser_version'	      => $data->browserversion,
			'cpu'	                  => $data->cpu,
			'device'	              => $data->device,
			'devicetype'	          => $data->devicetype,
			'fingerprint'	          => $data->fingerprint,
			'language'	              => $data->language,
			'screen'	              => $data->screenresolution,
			'timezone'	              => $data->timezone,
			'useragent'	              => $data->useragent,
			'tool'		              => $data->tool
		);
		$this->db->insert('customer_activity', $activity);
		return $this->db->insert_id();
	}

}

/* End of file Client_model.php */
/* Location: ./application/models/Client_model.php */