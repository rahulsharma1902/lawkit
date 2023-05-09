<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Handlesessions extends CI_Controller
{
	protected $timestamp;
	public function __construct()
	{
		parent::__construct();
		$this->timestamp = date("Y-m-d H:i:s");
	}

	public function index()
	{
		if($this->Sessions_model->clear() > 0){

			file_put_contents(APPPATH . 'logs/sessions_deleted.log', "customer_session table was cleared at " . $this->timestamp ."\r\n");
		}
	}	
}
?>