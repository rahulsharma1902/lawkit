<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use GuzzleHttp\Psr7\Response;
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . "libraries/stripe/init.php";
require APPPATH . 'libraries/PHPmailer_lib.php';
require FCPATH  . '/vendor/autoload.php';

class Api extends REST_Controller {

	public $stripe;
	public $timestamp;
	public $uid;


	public function __construct()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");
		$this->stripe = new \Stripe\StripeClient(STRIPE_API_KEY);
		$this->timestamp = date("Y-m-d H:i:s");
	}

	

	public function remove_favorite_sentence_delete(){
		try{
			$response = $this->webtoken->verify($this->get('token'));
			if($response && $response->uid){
				$id = $this->get('id');
				$isDeleted = $this->Searches_model->delete_search($id);
				if($isDeleted){
					$result = array(
						'id'        => $isDeleted,
						'message'   => 'Tu búsqueda ha sido eliminada'
					);
				}
				else{
					$result = array(
						'error'    => true,
						'message'  => 'No se pudo eliminar tu búsqueda'
					);
				}
			}
			else{
				$result = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
			}
			$this->response($result);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}
	
	public function save_searches_post()
	{
		$decodeToken = $this->webtoken->verify($this->post("token"));
		if($decodeToken->uid && isset($decodeToken->uid)){
			$busqueda = json_decode(stripslashes($this->post("busqueda")));
			if(count($busqueda) > 0){
				foreach($busqueda as $bu){
					$this->Searches_model->my_searches($bu, $decodeToken->uid);
				}
				$this->response(["message"=>"Busqueda guardada automaticamente"], REST_Controller::HTTP_OK);
			}
		} else {
			$this->response([ "message" => "Token no válido" ], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function verify_get(){
		try{
			$decodeToken = $this->webtoken->verify($this->get('token'));
			$this->check_subscription($decodeToken);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}

	public function delete_cookie($name)
	{
		setcookie($name, "", time() - 3600, '/', COOKIE_HOSTNAME, true);
	}

	public function check_subscription($dataTokenDecode){
		
		try{
			
			if($dataTokenDecode->uid && isset($dataTokenDecode->uid))
			{
				$subscription = $this->Invoices_model->DataSubscriptionByLawkitCustomerId($dataTokenDecode->uid);
				if(!$subscription)
				{
					$this->response(array(
						'error'   => true,
						'message' => "Acceso denegado.",
						'status'  => "denied"
					));
				}
				else
				{
					$isSubscribe = $this->Account_model->check_payload_php_sessid($dataTokenDecode->uid, $dataTokenDecode->php_sessid);
					if(!$isSubscribe){
						if(session_id()){
							session_commit();
							session_id($dataTokenDecode->php_sessid);
							session_start();
							session_destroy();
							session_commit();
							#$this->Account_model->destroy_customer_session($this->session->userdata('uid'));
							$this->delete_cookie("token");
							$this->response(array(
								'error'   => true,
								'message' => "Acceso denegado - session expired or duplicate for user: " . $dataTokenDecode->uid,
								'status'  => "denied"
							));
						}
						else{
							$this->response(array(
								'error'   => true,
								'message' => "Acceso denegado - session expired or duplicate. (no session_id())",
								'status'  => "denied"
							));
						}
					}
					else{
						$data["status"]        = $subscription->status;
						$data["customer_uid"]  = $dataTokenDecode->uid;
						$data["photo"]         = $subscription->photo;
						$data["email"]         = $dataTokenDecode->email;
						$data['name']          = $subscription->username;
						$this->response($data);
					}
				}
			}
			else
			{
				$this->response(array(
					'error'   => true,
					'message' => "Acceso denegado.",
					'status'  => "unknow user id"
				));
			}
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage(),
				'status'  => "unknow token"
			));
		}
	}

	/*========================================
	=            API for Searches            =
	========================================*/

	public function favlist_get()
	{
		$res = [];
		try{
			$fav = $this->Searches_model->get_favlist($this->get('uid'));
			if(count($fav) > 0){
				$res = $fav;
			}
			else{
				$res = [
					'error'   => true,
					'message' => 'No hay favoritos'
				];
			}
		}
		catch(Exception $e){
			$res = [
				'error'   => true,
				'message' => $e->getMessage()
			];
		}
		$this->response($res, REST_Controller::HTTP_OK);
	}

	public function accept_term_and_conditions_post()
	{
		$accept = $this->Account_model->acceptTermAndConditions($this->post('uid'));
		if($accept){
			$this->response(array(
				'error'   => false,
				'message' => 'Aceptado'
			));
		}
		else{
			$this->response(array(
				'error'   => true,
				'message' => 'No se pudo aceptar'
			));
		}
	}

	public function term_and_conditions_get()
	{
		$accept = $this->Account_model->termAndConditionsAcceptedByUser($this->get('uid'));
		if(!$accept){
			$this->response(array(
				'error'   => true,
				'message' => [
					'title'   => 'Términos y condiciones de Lawkit',
					'message' => '<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p><p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laboriosam magni rerum corporis tempore veritatis? Rem, impedit error obcaecati nemo laboriosam voluptatem ullam eum officia velit expedita, voluptate quo amet odio.</p>'
				]
			), REST_Controller::HTTP_OK);
		}
		else{
			$this->response(array(
				'message' => 'Términos y condiciones aceptados'
			), REST_Controller::HTTP_OK);
		}
	}


	public function addtolist_post(){
		$result=[];
		try{
			$response = $this->webtoken->verify($this->post('token'));
			if($response->uid){
				$id = $this->Searches_model->save_search($response->uid, $this->post());
				if($id){
					$result = array(
						'id'        => $id,
						'message'   => 'Tu búsqueda ha sido guardada'
					);
				}
				else{
					$result = array(
						'error'    => true,
						'message'  => 'No se pudo guardar tu búsqueda'
					);
				}
			}
			else{
				$result = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
			}
			$this->response($result);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}

	public function removefromfavlist_get(){
		$result=[];
		try{
			$response = $this->webtoken->verify($this->get('token'));
			if($response->uid){
				$id = $this->Searches_model->remove_search($this->get('id'));
				if($id){
					$result = array(
						'id'        => $id,
						'message'   => 'Tu búsqueda ha sido guardada'
					);
				}
				else{
					$result = array(
						'error'    => true,
						'message'  => 'No se pudo eliminar esta búsqueda de tu lista de favoritos'
					);
				}
			}
			else{
				$result = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
			}
			$this->response($result);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}

	public function save_cs_post(){
		$result=[];
		try{
			$response = $this->webtoken->verify($this->post('token'));
			if($response->uid){
				$id = $this->Searches_model->count_searches_customer($response->uid);
				if($id){
					$result = array(
						'id'        => $id,
						'message'   => 'Búsqueda guardada'
					);
				}
				else{
					$result = array(
						'error'    => true,
						'message'  => 'No se pudo guardar la búsqueda'
					);
				}
			}
			else{
				$result = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
			}
			$this->response($result);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}
	
	
	/*=====  End of API for Searches  ======*/

	/*==============================================
	=            Stripe Endpoint Events            =
	==============================================*/
	public function stripe_events_post(){
		
		$payload = @file_get_contents('php://input');
		$event = null;

		try {
		    $event = \Stripe\Event::constructFrom(
		        json_decode($payload, true)
		    );
		} catch(\UnexpectedValueException $e) {
		    // Invalid payload
		    http_response_code(400);
		    exit();
		}

		try
		{
			$customer_data     = $this->stripe->customers->retrieve(
				$event->data->object->customer
			);			
		}
		catch(Exception $e)
		{
			/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . " customer retrieve err:".$e->getMessage()." \r\n", FILE_APPEND); */
		}


		if( $customer_data )
		{
			$fullname         = empty($customer_data->name) ? "Suscriptor" : $customer_data->name;
			$email            = isset($customer_data->email) ? $customer_data->email : "elnew.luis@gmail.com";
		}
		else
		{
			$fullname = "Suscriptor";
			$email    = "elnew.luis@gmail.com";
		}
		
		
		/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . " Registro de evento recibido: (".$event->type.") \r\n", FILE_APPEND); */
		switch($event->type){
			#case 'customer.subscription.created': break;
			case 'customer.subscription.deleted':
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . " (customer.subscription.deleted) ".$event->data->object->status." | user: \r\n", FILE_APPEND); */
				$data["content"] = "<p>Estimado " . $fullname . " has cancelado tu suscripción a los servicios de Lawkit.</p>";
				$data["content"] .= "<p>Puedes volver a realizar tu suscripción haciendo clic en el siguiente botón:</p>";
				$data["content"] .= "<a href='".base_url('account/upgrade')."' style='background-color:lightgreen; color:#fff; padding:1rem; border-radius:5px; width: 200px; margin-top: 10x;'>ACTUALIZAR MI CUENTA</a>";
				#Actualizar el estado de la suscripción en nuestra BD
				$get_start_time  = new DateTime(date("Y-m-d H:i:s", $event->data->object->current_period_start));
				$start_time      = $get_start_time->format("Y-m-d H:i:s");
				$get_end_time    = new DateTime(date("Y-m-d H:i:s", $event->data->object->current_period_end));
				$end_time        = $get_end_time->format("Y-m-d H:i:s");
				$subscription_id = $event->data->object->id;
				$payment_method  = $event->data->object->default_payment_method;
				$status          = $event->data->object->status;
				$this->Invoices_model->update($subscription_id, $status, $start_time, $end_time);
				$this->sendmail($email, "Haz cancelado tu suscripción en Lawkit.", $data);
			break;
			case 'customer.subscription.trial_will_end':
				//$end_trial_time = new DateTime(date("Y/m/d H:i:s", $event->data->object->current_period_end));
				//$end_time       = $end_trial_time->format("Y/m/d H:i:s");
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("d/m/Y H:i:s") . " Se envió un recordatorio al cliente ".$customer_data->email." por que su suscripción está por expirar el día ".$end_time." \r\n", FILE_APPEND); */
				//$data["content"] = "<p>Estimado " . $fullname . ", tu periodo de prueba de <b>Lawkit</b> está por concluir el día " . $end_time . " para seguir usando los servicios de lawkit, deberás de registrar un método de pago desde tu panel de control.</p>";
				//$data["content"] .= "<p><a href='".base_url('account/dashboard')."'>Panel de control</a></p>";
				//$this->sendmail($email, "Tu suscripción está por expirar.", $data);
			break;
			case 'customer.subscription.updated':			
				if( $event->data->object->collection_method == "charge_automatically" )
				{
					$get_start_time = new DateTime(date("Y-m-d H:i:s", $event->data->object->current_period_start));
					$start_time     = $get_start_time->format("Y-m-d H:i:s");
					
					$get_end_time = new DateTime(date("Y-m-d H:i:s", $event->data->object->current_period_end));
					$end_time     = $get_end_time->format("Y-m-d H:i:s");

					$subscription_id = $event->data->object->id;
					$payment_method  = $event->data->object->default_payment_method;
					$status          = $event->data->object->status;
					$this->Invoices_model->update($subscription_id, $status, $start_time, $end_time);
					/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y-m-d H:i:s") . " (customer.subscription.updated) subscription_id:".$subscription_id." :period_start:" . $start_time . " period_end: " . $end_time . " payment_method: ".$payment_method." status:".$status." \r\n", FILE_APPEND); */
				}
			break;
			case 'payment_intent.payment_failed':
				/* $data["content"] = "<p>Hola ".$event->data->object->customer_name."</p>";
				$data["content"] .= "<p>No pudimos realizar un cargo automático a tu tarjeta de crédito para que puedas seguir usando las herramientas de lawkit.</p>";
				$data["content"] .= "<p>Ingresa a lawkit en tu panel de control y añade una tarjeta de crédito si no cuentas con una, de lo contrario, revisa si dispones de fondos, lawkit intentará nuevamente realizar el cargo en las próximas 2 horas.</p>";
				$data["content"] .= "<p><a style='background-color:darkblue;color:#fff;padding:1rem;border-radius:24px;text-decoration: none;font-weight: bold;' href='".base_url("account/dashboard")."'>MI DASHBOARD</a></p>";
				$this->sendmail($event->data->object->customer_email, "Intento de cargo automático en lawkit", $data); */
				file_put_contents(APPPATH . 'logs/payment_intent.payment_failed.log', date("Y/m/d H:i:s") . " se intentó realizar un cobro al cliente con el correo electrónico ".$event->data->object->customer_email."\r\n", FILE_APPEND);
			break;
			case 'payment_intent.succeeded':
				#recibo de pago
				$receipt_url   = $event->data->object->charges->data[0]->receipt_url;
				$receipt_email = $event->data->object->charges->data[0]->receipt_email;
				$data["content"] = "<p>Gracias por tu pago para el concepto de suscripción mensual a los servicios de lawkit.</p>";
				$data["content"] .= "<p>Puedes ver el recibo de tu pago realizado en el siguiente enlace:</p>";
				$data["content"] .= "<a href='".$receipt_url."'>Recibo de pago - Lawkit</a>";
				if( is_null($receipt_email)  )
					$receipt_email = $email;
				$this->sendmail($receipt_email, "Gracias por tu pago.", $data);
				#capturar método de pago
				/*$this->stripe->subscriptions->update(
					$subscription_id,
				    ['default_payment_method' => $event->data->object->payment_method]
				);*/
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . " " . $event->data->object->billing_reason . " (payment_intent.succeeded) " . $event->data->object->status . " " . $email . " url:".$receipt_url." \r\n", FILE_APPEND); */
			break;
			case 'charge.expired':
				$receipt_url = $event->data->object->receipt_url;
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . ' | (charge.expired) customer: ' . $event->data->object->customer . ', ended at: ' . $receipt_url  . "\r\n", FILE_APPEND);			 */
				$data["content"] = "<p>Estimado " . $fullname . " no hemos podido realizar el cargo a tu tarjeta de crédito para que puedas continuar usando los servicios de lawkit.</p>";
				$data["content"] .= "<p>Puedes ver el estatus de tu suscripción en el siguiente enlace: </p>";
				$data["content"] .= "<p><a href='".$receipt_url."'>Recibo</a></p>";
				$data["content"] .= "<p>Te invitamos a que realices el pago a tráves del sistema lawkit en el siguiente enlace:</p>";
				$data["content"] .= "<a href='".base_url('account/upgrade')."' style='background-color:lightgreen; color:#fff; padding:1rem; border-radius:5px; width: 200px; margin-top: 10x;'>PAGAR</a>";
				$this->sendmail($email, "No se pudo realizar un cargo automático.", $data);
			break;
			case 'charge.failed':
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . ' | falló (charge.failed), customer: ' . $event->data->object->customer . "\r\n", FILE_APPEND); */
			break;
			case 'charge.succeeded':
				$receipt_email = $event->data->object->receipt_email;
				$receipt_url   = $event->data->object->receipt_url;
				$status 	   = $event->data->object->status; //succeeded
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . ' | (charge.success) customer: ' . $event->data->object->customer . "\r\n", FILE_APPEND); */

				$data["content"] = "<p>Estimado " . $fullname . " se ha realizado el pago por concepto de suscripción mensual a los servicios de lawkit</p>";
				$data["content"] .= "<p>Puedes obtener el recibo de este cago en el siguiente enlace:</p>";
				$data["content"] .= "<p><a href='".$receipt_url."'>Recibo</a></p>";
				$this->sendmail($email, "Se ha realizado un cargo automático.", $data);
			break;
			case 'invoice.finalized':
				#Falló el pago, la suscripción se cancela
				$this->stripe->invoices->voidInvoice(
					$event->data->object->id,
					[]
				);
				$subscription_id = $event->data->object->subscription;
				$canceled = $this->stripe->subscriptions->cancel(
					$subscription_id,
					[]
				);
				if($canceled->id){
					$reason["reason"] = "Intento fallido de cobro";
					$reason["other"]  = "by api";
					$this->Account_model->cancel_subscription($reason);
				}
				else{
					file_put_contents(APPPATH . 'logs/invoice.payment_failed.log', date("Y/m/d H:i:s") . " No se pudo realizar el cobro, se cancela la suscripción inmediatamente al cliente con el correo electrónico ".$event->data->object->customer_email."\r\n", FILE_APPEND);
				}

				$data["content"] = "<p>Hola ".$event->data->object->customer_name."</p>";
				$data["content"] .= "<p>No pudimos realizar un cargo automático a tu tarjeta de crédito para que puedas seguir usando las herramientas de lawkit.</p>";
				$data["content"] .= "<p>Tu suscripción a los servicios de Lawkit ha finalizado, para continuar usando lawkit debes volver a suscribirte.</p>";
				$data["content"] .= "<p><a style='-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; height: auto; line-height: 20px; color: #fff; width: 100%; font-size: 14px; font-weight: 400; padding: 8px; text-decoration: none; display: inline-block; cursor: pointer; text-align: center; transition: all 0.3s ease 0s; border: none !important; background-color:#03a9f4;' href='".base_url("account/dashboard")."'>MI DASHBOARD</a></p>";
				$this->sendmail(trim($event->data->object->customer_email), "Intento de cargo automático en lawkit", $data);
			break;
			case 'invoice.payment_failed':
				#Falló el pago, la suscripción se cancela
				$this->stripe->invoices->voidInvoice(
					$event->data->object->id,
					[]
				);
				$subscription_id = $event->data->object->subscription;
				$canceled = $this->stripe->subscriptions->cancel(
					$subscription_id,
					[]
				);
				if($canceled->id){
					$reason["reason"] = "Intento fallido de cobro";
					$reason["other"]  = "by api";
					$this->Account_model->cancel_subscription($reason);
				}
				else{
					file_put_contents(APPPATH . 'logs/invoice.payment_failed.log', date("Y/m/d H:i:s") . " No se pudo realizar el cobro, se cancela la suscripción inmediatamente al cliente con el correo electrónico ".$event->data->object->customer_email."\r\n", FILE_APPEND);
				}

				$data["content"] = "<p>Hola ".$event->data->object->customer_name."</p>";
				$data["content"] .= "<p>No pudimos realizar un cargo automático a tu tarjeta de crédito para que puedas seguir usando las herramientas de lawkit.</p>";
				$data["content"] .= "<p>Tu suscripción a los servicios de Lawkit ha finalizado, para continuar usando lawkit debes volver a suscribirte.</p>";
				$data["content"] .= "<p><a style='-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; height: auto; line-height: 20px; color: #fff; width: 100%; font-size: 14px; font-weight: 400; padding: 8px; text-decoration: none; display: inline-block; cursor: pointer; text-align: center; transition: all 0.3s ease 0s; border: none !important; background-color:#03a9f4;' href='".base_url("account/dashboard")."'>MI DASHBOARD</a></p>";
				$this->sendmail(trim($event->data->object->customer_email), "Intento de cargo automático en lawkit", $data);
			break;
			case 'invoice.payment_succeeded':

				$get_start_time = new DateTime(date("Y/m/d H:i:s", $event->data->object->period_start));
				$start_time     = $get_start_time->format("Y/m/d H:i:s");
				
				$get_end_time = new DateTime(date("Y/m/d H:i:s", $event->data->object->period_end));
				$end_time     = $get_end_time->format("Y/m/d H:i:s");

				$get_next_time = new DateTime(date("Y/m/d H:i:s", $event->data->object->next_payment_attempt));
				$next_time     = $get_next_time->format("Y/m/d H:i:s");

				$status               = $event->data->object->status;
				$paid                 = $event->data->object->paid;
				if( $event->data->object->collection_method == "charge_automatically" )
				{

				}


				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . 'invoice.payment_succeeded status: ' . $status . " paid: ".$paid.", proximo intento: " . $next_time . ", period_end: " . $end_time . ", period_start: " . $start_time. "\r\n", FILE_APPEND); */

			break;
			case 'invoice.updated':
				
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . ' ' . $event->data->object->billing_reason . "-> invoice.updated " . $email . "\r\n", FILE_APPEND); */
			break;
			case 'invoice.paid':
				/* file_put_contents(APPPATH . 'logs/stripe_events.log', date("Y/m/d H:i:s") . ' ' . $event->data->object->billing_reason . "-> invoice.paid " . $email . "\r\n", FILE_APPEND); */
			break;
			case 'invoice.created':
				$discount = $this->Invoices_model->getCustomerDiscountByRef($event->data->object->customer);
				if(count($discount) > 0){
					try {
						$this->stripe->invoices->update(
							$event->data->object->id,
							['discounts' => array
								(
									0 => array('coupon' => $discount->code)
								)
							]
						);
						/* file_put_contents(APPPATH . 'logs/discount_succeed.log', date("Y/m/d H:i:s") . ' CUSTOMER:' . $event->data->object->customer . "-> SUSCRIPCION: ".$event->data->object->subscription."-> CODE: ".$discount->code." invoice.created \r\n", FILE_APPEND); */
					} catch (Exception $e) {
						/* file_put_contents(APPPATH . 'logs/discount_failed.log', date("Y/m/d H:i:s") . ' CUSTOMER:' . $event->data->object->customer . "-> SUSCRIPCION: ".$event->data->object->subscription."-> CODE: ".$discount->code." ERR " . $e->getMessage() . "\r\n", FILE_APPEND); */
					}
				}
			break;
		}
	}
	/*=====  End of Stripe Endpoint Events  ======*/
	
	/*=======================================================
	=            Customer Device Info & Activity            =
	=======================================================*/
	public function customer_activity_get(){
		$this->response($this->Admin_model->GetActivityDataByCustomerId($this->get('uid')));
	}
	public function customer_activity_post(){
		$result=[];
		try{
			$response = $this->webtoken->verify($this->post('token'));
			if($response->uid){
				$id = $this->Client_model->update_activity($this->post('clientjs'), $response->uid);
				if($id){
					$result = array(
						'message'=>'updated'
					);
				}
				else{
					$result = array(
						'error'	    => true,
						'message'   => 'no se registro la actividad'
					);
				}
			}
			else{
				$result = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
			}
			$this->response($result, REST_Controller::HTTP_OK);
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			));
		}
	}
	
	
	/*=====  End of Customer Device Info & Activity  ======*/





	/**
	TODO: Add bug report
	**/
	public function bug_report_post(){
		try{
			$response = $this->webtoken->verify($this->get('token'));
			if($response->uid){
				$result = [];
				if($this->post()){
					$id = $this->Bugs_model->add($this->post());
					if($id){
						$result = array(
							"id"      => $id,
							"message" => "Tu reporte se ha enviado, en breve daremos pronta solución."
						);
					}
					else{
						$result = array(
							"error"   => true,
							"message" => "No se puedo enviar tu reporte de error, intenta más tarde."
						);
					}
					$this->response($result, REST_Controller::HTTP_OK);
				}				
			}
			else{
				$response = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
				$this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
			}
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			), REST_Controller::HTTP_UNAUTHORIZED);
		}
	}


	/**
	TODO: Add comments
	**/
	public function comments_post(){
		try{
			$response = $this->webtoken->verify($this->get('token'));
			if($response->uid){
				if($this->post()){
					$result = [];
					$id = $this->Comments_model->add($this->post());
					if($id){
						$result = array(
							"id"      => $id,
							"message" => "Tu comentario se ha enviado."
						);
					}
					else{
						$result = array(
							"error"   => true,
							"message" => "No se puedo enviar tu comentario, intenta más tarde."
						);
					}
					$this->response($result, REST_Controller::HTTP_OK);
				}	
			}
			else{
				$response = array(
					'error'	    => true,
					'message'   => $response["message"]
				);
				$this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
			}
		}catch(Exception $e){
			$this->response(array(
				'error'   => true,
				'message' => $e->getMessage()
			), REST_Controller::HTTP_UNAUTHORIZED);
		}
	}


	public function sendmail($mailto = "", $subject = "", $data = "", $template_name = "default.php")
    {
		$this->load->library('phpmailer_lib');
		$mail = $this->phpmailer_lib->load();
        $mail->isSMTP();
        $mail->Host     = 'lawkit.mx';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = 'no-reply@lawkit.mx';
        $mail->Password = 'CKijfD$jqv1)';
        $mail->Port     = 465;
        $mail->setFrom('no-reply@lawkit.mx', 'Lawkit');
        $mail->addAddress($mailto);
        $mail->Subject = $subject;
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

		$mensaje = $this->load->view('emailtemplates/' . $template_name, $data, TRUE);
        $mail->Body = $mensaje;
		
        /* if(!$mail->send())
        	file_put_contents(APPPATH . 'logs/email_errors.log', date("Y/m/d H:i:s") . " | " . $mail->ErrorInfo . "\r\n", FILE_APPEND); */
    }






	/** mobile app */
	/**
	 * TODO: Sign in
	 */
	public function signin_post(){
		$user = $this->Account_model->signin($this->post());
		$this->response($user, REST_Controller::HTTP_OK);
	}



	/** facturama */
	public function emitir_post()
	{
		/* file_put_contents(APPPATH . 'logs/facturama_testing.log', date("Y/m/d H:i:s") . "\r\n", FILE_APPEND); */
		$facturama = new \Facturama\Client(FACTURAMA_USER, FACTURAMA_PASSWORD);
		$facturama->setApiUrl(FACTURAMA_SANDBOX);
		$params = [
			'Serie'             => 'A',
			'Folio'             => '100',
			'Date'              => DATE("Y-m-d"),
			'PaymentForm'       => '01',
			'PaymentConditions' => 'CREDITO A SIETE DIAS',
			'Currency'          => 'MXN',
			'CfdiType'          => 'I',
			'PaymentMethod'     => 'PUE',
			'ExpeditionPlace'   => '78140',
			'Receiver' =>
			[
				'Rfc'          => 'URE180429TM6',
				'CfdiUse'      => 'G03',
				'Name'         => 'UNIVERSIDAD ROBOTICA ESPAÑOLA',
				'FiscalRegime' => '603',
				'TaxZipCode'   => '65000',
				'Address' =>
				[
					'Street'         => 'Guadalcazar del receptor',
					'ExteriorNumber' => '300',
					'InteriorNumber' => 'A',
					'Neighborhood'   => 'Las lomas',
					'ZipCode'        => '65000',
					'Municipality'   => 'San Luis Potosi',
					'State'          => 'San Luis Potosi',
					'Country'        => 'México'
				]
			],
			'Items' => [
			   [
					'ProductCode'          => '10101504',
					'IdentificationNumber' => 'EDL',
					'Description'          => 'Estudios de viabilidad',
					'Unit'                 => 'NO APLICA',
					'UnitCode'             => 'MTS',
					'UnitPrice'            => 50.0,
					'Quantity'             => 2.0,
					'Subtotal'             => 100.0,
					"TaxObject"            => "02",
					'Taxes' => [
					   [
						   'Total'       => 16,
						   'Name'        => 'IVA',
						   'Base'        => 100,
						   'Rate'        => "0.16",
						   'IsRetention' => false
					   ],
					],
					'Total' => 116.0,
				],
			],
		];		
		//CFDI 4.0 - Tipo Ingreso
		$result = $facturama->post('/3/cfdis', $params);
		$this->response([
			'data' => $result
		], REST_Controller::HTTP_OK);
		
		
	}


	public function auth_post()
	{
		$signin = $this->Account_model->signin($this->post());
		$this->response(["data" => $signin], REST_Controller::HTTP_OK);
	}




}

/* End of file Api.php */
/* Location: ./application/controllers/Api.php */