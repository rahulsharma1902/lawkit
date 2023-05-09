<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Facturama extends CI_Controller
{
	protected $timestamp;
	public function __construct()
	{
		parent::__construct();
		$this->timestamp = date("Y-m-d H:i:s");
	}

	public function index()
	{
		file_put_contents(APPPATH . 'logs/facturama_cron.log', " script executed at: " . $this->timestamp ."\r\n");
	}	
}
?>