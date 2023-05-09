<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Landpage extends CI_Controller {



	public function index()

	{
		$acl = new Acl;

		$this->cookie_exists();

		$this->load->view('index');

	}



	public function cookie_exists(){

		if(get_cookie("token")){

			redirect(base_url("account/dashboard"));

		}

		else {

			#Checar por $_SESSION

			if($this->session->userdata('uid')) {

				redirect(base_url("account/dashboard"));

			}

		}

	}





	public function delete_token(){

		setcookie("token", "", time() - 3600, 'SameSite=Lax', COOKIE_HOSTNAME, true); 

		return;

	}



}