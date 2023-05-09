<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . "libraries/stripe/init.php";
require APPPATH . 'libraries/PHPmailer_lib.php';
class Contact extends CI_Controller {

	public $stripe;
	public $timestamp;
	public $acl, $msg, $call;

	public function __construct()
	{
		parent::__construct();
		$this->stripe = new \Stripe\StripeClient(STRIPE_API_KEY);
		$this->timestamp = date("Y-m-d H:i:s");
		date_default_timezone_set("America/Mexico_City");
		$this->acl    = new Acl();
		$this->msg    = new Messages();
		$this->call   = new Apicall();
		$this->monthnamespanish = array(
			"01"=>'Enero',
			"02"=>'Febrero',
			"03"=>'Marzo',
			"04"=>'Abril',
			"05"=>'Mayo',
			"06"=>'Junio',
			"07"=>'Julio',
			"08"=>'Agosto',
			"09"=>'Septiembre',
			"10"=>'Octubre',
			"11"=>'Noviembre',
			"12"=>'Diciembre'
		);
		$this->monthnamespanishAbbr = array(
			"01"=>'Ene',
			"02"=>'Feb',
			"03"=>'Mar',
			"04"=>'Abr',
			"05"=>'May',
			"06"=>'Jun',
			"07"=>'Jul',
			"08"=>'Ago',
			"09"=>'Sep',
			"10"=>'Oct',
			"11"=>'Nov',
			"12"=>'Dic'
		);
		$this->weekdayname = array(
			"Monday"   => "Lunes",
			"Tuesday" => "Martes",
			"Wednesday"=> "Miércoles",
			"Thursday" => "Jueves",
			"Friday"   => "Viernes",
			"Saturday" => "Sábado",
			"Sunday"   => "Domingo"
		);
	}

	public function post()
	{
		
		if($this->input->post()){
			$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
			if($this->form_validation->run() == FALSE){
				$errors[] = 'Ingresa un correo electrónico válido. ';
			}else{}
			
			
		}else{
		    $errors[] = "sin datos";
		}
		
		if (isset($errors)){}else{
		    $envio = $this->Contact_model->post_contact($this->input->post());
    		if($envio === true){
    		    $messages[] = "Mensaje enviado correctamente, favor de esperar a que lo contactemos!";
    		}else{
    		    $errors[] = "El envio de datos tuvo un error, favor de contactarte directamente a: <b>correo@correo.com<b/>".$envio;
    		}
		}
		
		if (isset($errors)){
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <center>
                    <strong>Error!</strong> 
                    <?php
                        foreach ($errors as $error) {
                            echo $error;
                        }
                    ?>
                </center>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }
        if (isset($messages)){
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <center>
                    <strong>¡Bien hecho!</strong>
                    <?php
                        foreach ($messages as $message) {
                            echo $message;
                        }
                    ?>
                </center>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }
		
	}
}
?>