<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");		
	}


	public function post_contact($data){
		$email = $data["email"];
		$emailto = "hola@lawkit.com";
		$subject = $data["subject"];
		$name = $data["name"];
		$message = $data["message"];
		
		
		// Para enviar un correo HTML, debe establecerse la cabecera Content-type
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Cabeceras adicionales
        $cabeceras .= 'To: hola@lawkit.com' . "\r\n";
        $cabeceras .= "From: $email" . "\r\n";
        
        
        if(mail($emailto, $subject, $message)){
		
		    return true;
        }else{
            $errorMessage = error_get_last()['msg'];
	        return $errorMessage;
        }
	}
	
	public function ValidarTexto($data){
	    $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
	}

	
}