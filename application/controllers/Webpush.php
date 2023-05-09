<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Webpush extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function add_device()
	{
		$this->Webpush_model->addDevice($this->input->post('device_id'));
	}

	public function send(){
		$this->Webpush_model->send_notification();
	}

}

/* End of file Webpush.php */
/* Location: ./application/controllers/Webpush.php */