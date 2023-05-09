<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . "libraries/stripe/init.php";
require APPPATH . 'libraries/PHPmailer_lib.php';
class Account extends CI_Controller {

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

	public function confirm_again()
	{
		$this->cookie_exists();
		$data['redirect'] = "";
		
		if($this->input->post()){
			$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
			if($this->form_validation->run() == FALSE){
				$this->msg->toast('check-square.svg', 'Aviso', 'Ingresa un correo electrónico válido');
				redirect(base_url('account/signin'));
			}
			else{
				$signin = $this->Account_model->signin($this->input->post());
				if(isset($signin->error) && $signin->error){
					if($signin->customer != null){
						$_SESSION['email_intent'] = $this->input->post('email');
						$message = $signin->message;
						$message .= '<button class="btn btn-secondary btn-block btn-sm btnDeviceSession">Cerrar sesión</button>';
						$this->msg->toast('check-square.svg', 'Aviso', $message);
					}
					else
						$this->msg->toast('check-square.svg', 'Aviso', $signin->message);
					
					redirect(base_url('account/signin'));
				}
				else{
					$session = array(
						'uid'                => $signin->uid,
						'pwd'                => $signin->pwd,
						'email'              => $signin->email,
						'fname'              => $signin->fname,
						'lname'              => $signin->lname,
						'address'            => $signin->address,
						'phone'              => $signin->phone,
						'photo'              => $signin->photo,
						'subscription_id'    => $signin->stripedata->stripe_subscription_id,
						'customer_stripe_id' => $signin->stripedata->stripe_customer_id,
						'period_start'       => $signin->stripedata->plan_period_start,
						'period_end'         => $signin->stripedata->plan_period_end,
						'period_status'      => $signin->stripedata->status,
						'role'               => $signin->role,
						'rfc'                => $signin->rfc
					);
					$this->session->set_userdata($session);
					if($this->input->post("app"))
						header("Location: https://" . $this->input->post("app"));
					else
						redirect(base_url("account/dashboard"));
				}			
			}
		}
		
		$this->load->view('account/signin', $data);
	}


	



	public function get_human_readable_date($date){
		$day = substr($date, 8, 2);
		$month = substr($date, 5, 2);
		$year = substr($date, 0, 4);
		$convert = DateTime::createFromFormat("Y-m-d", $date);
		$dayname = strftime("%A", $convert->getTimestamp());
		return $this->weekdayname[$dayname] . ' ' . $day . ' de '.$this->monthnamespanish[$month].' de ' . $year;
		
	}

	public function signup()
	{		
		$this->acl->if_session_exists();
		$this->cookie_exists();

		

		if($this->input->post()){
			print_r($this->input->post());
			die();
			#validar si existe ya en bd // Check user email exists or not
			$exists = $this->Account_model->get_customer_by_email($this->input->post('email'));
			if($exists){
				$this->msg->toast('alert-circle.svg', 'Error', "Este correo electrónico ya se encuentra registrado.");
				redirect(base_url() . "account/signup", 'refresh');
			}


			if($this->input->post('password') != $this->input->post('password_confirm')){
				$this->msg->toast('alert-circle.svg', 'Error', "Confirma correctamente tu contraseña de acceso.");
				redirect(base_url() . "account/signup", 'refresh');
			}


			$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
			if($this->form_validation->run() == FALSE){
				$this->msg->toast('alert-circle.svg', 'Error', "Ingresa un correo electrónico válido");
				redirect(base_url() . "account/signup", 'refresh');
			}
			else{
				#start creating new customer using Stripe API 
				try{
					//Crea el cliente
					$customer = $this->stripe->customers->create([
					  'name'           => $this->input->post('fusername') . ' ' . $this->input->post('lusername'),
					  'email'          => $this->input->post("email"),
					  'description'    => 'New customer',
					  'payment_method' => $this->input->post('payment_method_id'),
					]);
    			}catch(Exception $e){
    				$api_error = $e->getMessage();
				  	$this->msg->toast('alert-circle.svg', 'Error', $api_error);
				  	redirect(base_url() . "account/signup", 'refresh');
    			}

    			if(empty($api_error)){
					
    				#TODO: Set how many days is available subscription
					$endtrial = new DateTime(date('Y-m-d H:i:s', strtotime($this->timestamp . ' +1 days')));
//					$endtrial = new DateTime(date('Y-m-d H:i:s', strtotime($this->timestamp . ' +15 minute')));
					$end = $this->get_human_readable_date(date('Y-m-d',strtotime($endtrial->format("Y-m-d"))));

					//Create a trial period (subscription)
					try{
						$subscription = $this->stripe->subscriptions->create([
							'customer' => $customer->id,
							'items'     => array(
								array(
									'price' => LAWKIT_10,  // Go to application/config/constants.php for change this lawkit price
								),
							),
							'trial_end' => strtotime($endtrial->format("Y-m-d H:i:s"))
						]);
					}
					catch(Exception $e){
						$api_error_trial = $e->getMessage();
						$this->msg->toast('alert-circle.svg', 'Error', $api_error_trial);
						redirect(base_url() . "account/signup", 'refresh');
					}

					if(empty($api_error_trial)){

						$subscription_data = $subscription->jsonSerialize();
						if($subscription_data['status'] != 'trialing'){
							$this->msg->toast('alert-circle.svg', 'Error', 'No se pudo crear tu cuenta en Lawkit, intenta más tarde.');
							redirect(base_url() . "account/signup", 'refresh');
							return;
						}
						/**  
						 Send data to Account_model and save to database
						 */
						$created = $this->Account_model->create($this->input->post());
						if(isset($created->error) && $created->error){
							$this->msg->toast('alert-circle.svg', 'Error', $created->message);
							redirect(base_url() . "account/signup", 'refresh');
						}
						else{

							//Attach payment method
							$this->stripe->paymentMethods->attach(
								$this->input->post('payment_method_id'),
								[
									'customer' => $customer->id
								]
							);

							$this->stripe->customers->createSource(
								$customer->id,
								['source' => $this->input->post('card_token')]
							);

							//Create a unique and forever lifetime discount coupon for this customer to share it
							$coupon = $this->createCoupon('forever', 15);
							if($coupon)
								$this->Account_model->saveCouponCustomer($created->customer['uid'], $coupon->id, 'forever');
							
							//Envia un cupon al crear cuenta
							if($this->input->post('coupon') && !empty($this->input->post('coupon'))){
								//Verificar si existe este cupon y  si pertenece a alguien y si es forever
								$isValid = $this->Account_model->saveCouponReferred($this->input->post('coupon'));
								if($isValid){
									$gift = $this->createCoupon('once', 15);
									$this->Account_model->saveCouponGiftByReferred($gift->id, $this->input->post('coupon'));
								}
							}

							$added = $this->Invoices_model->add_new_subscription($subscription_data, $created->customer['uid']);
							if(!$added){
								$this->msg->toast('alert-circle.svg', 'Error', 'No se pudo añadir tu periodo de prueba para Lawkit');
							}
							$trial_begin = $this->get_human_readable_date(date('Y-m-d',strtotime($this->timestamp)));
							$data['content'] = '<p><strong>Bienvenido ' . $this->input->post('fusername') . ' ' . $this->input->post('lusername') .'</strong></p>';
							$data['content'] .= '<p>Tu periodo de prueba comienza a partir de ahora ' . $trial_begin . ' y termina el día '.$end.'</p>';
							$data['content'] .= "<p>Tu contraseña de acceso es: <strong>" . $this->input->post('password').'</strong></p>';
							$data['content'] .= "<p>Comparte el siguiente código con tus colegas, amigos y familiares para que obtengan un descuento del 15% sobre una suscripción de Lawkit</p>";
							$data['content'] .= '<h1 style="text-align:center">' . $coupon->id . '</h1>';
							$data['content'] .= "<p>Confirma tu nueva cuenta haciendo clic en el siguiente enlace: <a href='".base_url()."account/confirm/".$created->customer['uid']."'>Confirmar cuenta en lawkit</a></p>";
							$this->sendmail($this->input->post('email'), 'Nuevo registro en lawkit', $data, 'signup');
							$this->msg->toast('check-square.svg', 'Aviso', 'Como medida de seguridad te hemos enviado un enlace para confirmar tu cuenta a tu correo electrónico proporcionado.');
							redirect(base_url('account/signin'));
						}		
					}
					else{
						$this->msg->toast('alert-circle.svg', 'Error', $api_error_trial);
						redirect(base_url() . "account/signup", 'refresh');
					}
    			}
    			else{
    				$this->msg->toast('alert-circle.svg', 'Error', $api_error);
					redirect(base_url() . "account/signup", 'refresh');
    			}
			}
		}
		$this->load->view('account/signup');
	}

	public function resend_email_confirmation()
	{
		$customer    = $this->Account_model->getUserByEmail($this->input->post("email"));
		if($customer) {
			$trial_begin = $this->get_human_readable_date(date('Y-m-d',strtotime($customer->plan_period_start)));
			$end         = $this->get_human_readable_date(date('Y-m-d',strtotime($customer->plan_period_end)));
			$data['content'] = '<p><strong>Bienvenido ' . $customer->fname . ' ' . $customer->lname .'</strong></p>';
			$data['content'] .= '<p>Tu periodo de prueba comienza a partir de ' . $trial_begin . ' y termina el día '.$end.'</p>';
			/* $data['content'] .= "<p>Tu contraseña de acceso es: <strong>" . $this->input->post('password').'</strong></p>'; */
			$data['content'] .= "<p>Comparte el siguiente código con tus colegas, amigos y familiares para que obtengan un descuento del 15% sobre una suscripción de Lawkit</p>";
			$data['content'] .= '<h1 style="text-align:center">' . $customer->code . '</h1>';
			$data['content'] .= "<p>Confirma tu nueva cuenta haciendo clic en el siguiente enlace: <a href='".base_url()."account/confirm/".$customer->uid."'>Confirmar cuenta en lawkit</a></p>";
			$this->sendmail($customer->email, 'Nuevo registro en lawkit', $data, 'signup');
			echo json_encode(["message" => "Se ha enviado nuevamente el correo de confirmación de cuenta"]);
		} else {
			echo json_encode(["error"=>true, "message" => "Tu cuenta ya se encuentra confirmada, si continuas teniendo problemas para ingresar comunicate con el equipo de soporte de lawkit al siguiente correo: soporte@lawkit.com"]);
		}
	}

	public function download()
	{
		$this->load->helper("download");
		$data = file_get_contents(APPPATH . "LawkitT&C.pdf");
		force_download("Términos y Condiciones de Lawkit.pdf", $data);
	}

	public function confirm(){
		$confirmed = $this->Account_model->confirm_customer($this->uri->segment(3));
		if($confirmed)
			$this->msg->toast('check-square.svg', 'Aviso', 'Tu cuenta ha sido confirmada, ahora puedes iniciar sesión.');
		else $this->msg->toast('alert-circle.svg', 'Aviso', 'Tu cuenta no pudo ser confirmada.');
		redirect(base_url() . "account/signin");
	}


	public function createCoupon($couponLifeTime = 'once', $discount = LAWKIT_10_DISCOUNT)
	{
		try{
			$coupon = $this->stripe->coupons->create([
				'percent_off' => $discount,
				'currency'    => 'MXN',
				'duration'    => $couponLifeTime
			]);
			return $coupon;
		} catch(Exception $e){
			return false;
		}
	}


	public function cookie_exists()
	{
		if(get_cookie("token")){
			redirect(base_url("account/dashboard"));
		}
	}

	public function hash()
	{
		$usuarios = $this->Account_model->get_users();
		if(count($usuarios) > 0){
			foreach($usuarios as $usuario){
				$password = random_string('alnum', 8);
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);
				if($this->Account_model->update_password($usuario->uid, $hashed_password)){
					$data['content'] = '<p>Estimado ' . $usuario->fname .' ' . $usuario->lname . ', hemos generado una nueva contraseña de acceso a Lawkit</p>';
					$data['content'] .= '<p>Nueva contraseña: <b>' . $password . '</b></p>';
					$this->sendmail($usuario->email, 'Nueva contraseña en lawkit', $data);
				}
			}
		}
	}


	public function signin()
	{
		$this->cookie_exists();
		$data['redirect'] = "";

		if($this->input->get('_ga'))
			$data['redirect'] = $this->input->get('_ga');
		
		if($this->input->post()){
			$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
			if($this->form_validation->run() == FALSE){
				$this->msg->toast('check-square.svg', 'Aviso', 'Ingresa un correo electrónico válido');
				redirect(base_url('account/signin'));
			}
			else{
				$signin = $this->Account_model->signin($this->input->post());
				if(isset($signin->error) && $signin->error){
					if($signin->customer != null){
						$_SESSION['email_intent'] = $this->input->post('email');
						$message = $signin->message;
						$message .= '<button class="btn btn-secondary btn-block btn-sm btnDeviceSession">Cerrar sesión</button>';
						$this->msg->toast('check-square.svg', 'Aviso', $message);
					}
					else
						$this->msg->toast('check-square.svg', 'Aviso', $signin->message);
					
					redirect(base_url('account/signin'));
				}
				else{
					$session = array(
						'uid'                => $signin->uid,
						'pwd'                => $signin->pwd,
						'email'              => $signin->email,
						'fname'              => $signin->fname,
						'lname'              => $signin->lname,
						'address'            => $signin->address,
						'phone'              => $signin->phone,
						'photo'              => $signin->photo,
						'subscription_id'    => $signin->stripedata->stripe_subscription_id,
						'customer_stripe_id' => $signin->stripedata->stripe_customer_id,
						'period_start'       => $signin->stripedata->plan_period_start,
						'period_end'         => $signin->stripedata->plan_period_end,
						'period_status'      => $signin->stripedata->status,
						'role'               => $signin->role,
						'rfc'                => $signin->rfc
					);
					$this->session->set_userdata($session);
					if($this->input->post("app"))
						header("Location: https://" . $this->input->post("app"));
					else
						redirect(base_url("account/dashboard"));
				}			
			}
		}
		
		$this->load->view('account/signin', $data);
	}

	public function checkcustomersession()
	{
		$isSubscribe = $this->Account_model->check_customer_session();
		if(!$isSubscribe){
			if(session_id()){
				session_commit();
				session_id(get_cookie('ci_session'));
				session_start();
				session_destroy();
				session_commit();
				#$this->Account_model->destroy_customer_session($this->session->userdata('uid'));
				$this->delete_cookie("token");
				echo json_encode(array(
					'error'   => true,
					'message' => "Acceso denegado.",
					'status'  => "session expired or duplicate"
				));
			}			
		}
	}

	public function devices()
	{
		$email = base64_encode($_SESSION["email_intent"]);
		$data["content"] = '<p>Hemos enviado este correo electrónico ya que solicitaste cerrar tu sesión de Lawkit</p>';
		$data["content"] .= '<p>Haciendo clic en el siguiente botón deberás de volver a iniciar sesión en el sistema Lawkit desde el dispositivo que hizo la solicitud.</p>';
		$data["content"] .= '<hr/>';
		$data["content"] .= '<a href="'.base_url('account/reset?tr=' . $email).'" style="background-color: red;border: none;padding: 10px;width: 420px;color: white;font-size: 21px;">Cerrar todas las sesiones</a>';
		$data["content"] .= '<hr/>';
		$data["content"] .= '<p>Sí tu no realizaste esta petición, alguien más está intentando usar tu cuenta, por lo que te sugerimos cambiar la contraseña.</p>';
		$this->sendmail($email, 'Solicitud de cierre de cuenta', $data);
		unset($_SESSION['email_intent']);
		$this->msg->toast('check-square.svg', 'Aviso', "Se ha enviado un correo electrónico a tu cuenta para cerrar tu sesión");
		return;
	}

	public function reset()
	{
		if(!isset($_GET["tr"])){
			$this->msg->toast('check-square.svg', 'Aviso', "El correo electrónico no es válido");
			redirect(base_url('account/signin'));
		}
		$email = base64_decode($_GET["tr"]);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->msg->toast('check-square.svg', 'Aviso', "El correo electrónico no es válido");
			redirect(base_url('account/signin'));
		}
		$wasChanged = $this->Account_model->reset($email);
		if(get_cookie("token"))
			setcookie("token", "", time() - 3600, '/', COOKIE_HOSTNAME, true); 			
		if($wasChanged){
			$this->msg->toast('check-square.svg', 'Aviso', "La sesión ha sido cerrada, ahora puedes iniciar sesión desde cualquier dispositivo");
			redirect(base_url('account/signin'));
		}
	}

	public function create_cookie($name, $value)
	{
		setcookie($name, $value, strtotime( ' +7 days' ), '/', COOKIE_HOSTNAME, true); 
	}

	public function delete_cookie($name)
	{
		setcookie($name, "", time() - 3600, '/', COOKIE_HOSTNAME, true);
	}


	public function calendar_data()
	{
		$data = [];
		$calendarios = json_decode($this->call->mkRequest('get', API_CALENDARIO . 'current_calendar/customer/' . $this->session->userdata('uid')));
		if(count($calendarios->calendario->calendars) > 0){
			$data['calendars'] = $calendarios->calendario->calendars;
			$data['total_calendars'] = count($data['calendars']);
			$data['clip']            = isset($calendarios->calendario->toclipboard) ? $calendarios->calendario->toclipboard : NULL;
			$data['final']           = isset($calendarios->calendario->final) ? $calendarios->calendario->final : NULL;
			$data['name']            = isset($calendarios->name) ? $calendarios->name : NULL;
			$data['description']     = isset($calendarios->description) ? $calendarios->description : NULL;
			$data['termino']         = isset($calendarios->termino) ? $calendarios->termino : NULL;			
		}	
		echo json_encode($data);
	}



	public function dashboard(){
		$this->acl->if_session_not_exists();
		$CustomerSubscriptionData  = $this->GetSubscriptionCustomerDataBySubscriptionId(
			$this->session->userdata('subscription_id'));
		$data['alreadyCancelled']  = $CustomerSubscriptionData->cancel_at_period_end;
		$data["html_calendar"]     = NULL;
		$data["total_calendars"]   = 0;
		$data['contracts_total']   = 0;
		$data['busquedas_total']   = 0;
		$data['cards']             = [];
		//tarjetas		
		$data['cards']  = $this->getCustomerStripeSources($this->session->userdata('customer_stripe_id'))['data'];
		$couponCode     = $this->Account_model->getCode();
		if($couponCode)
			$data['coupon'] = $this->stripe->coupons->retrieve($couponCode, []);		
		else $data['coupon'] = '';

		//Busquedas
		$data['count_searches'] = 0;
		$data['wishlist']       = array();
		$buscador               = $this->Searches_model->get_data_searches();
		$data['count_searches'] = count($buscador);
		if(count($buscador) > 0)
			$data['wishlist'] = $buscador;
		
		//Códigos de descuento
		$data["yourCoupons"] = $this->Account_model->getDiscountsCustomer($this->session->userdata('uid'), $CustomerSubscriptionData);

		//Profile
    	if($this->input->post()){
    		$updated = $this->Account_model->update_customer_profile($this->input->post());
    		if($updated){
    			$this->msg->toast('check-square.svg', 'Aviso', 'Tus datos fueron actualizados.');
    		}
    		else{
    			$this->msg->toast('alert-circle.svg', 'Error', "Hubo un problema al actualizar datos.");
    		}
    	}
    	$data['profile'] = (object)$this->session->userdata();
    	    	    	
    	//Pagos
    	$data['invoices'] = $this->StripeGetCustomerInvoicesBySubscriptionId($this->session->userdata('subscription_id'));
    	$CustomerSubscriptionData = $this->GetSubscriptionCustomerDataBySubscriptionId($this->session->userdata('subscription_id'));
		$data['customer_suscription'] = array(
			'status'    => $CustomerSubscriptionData->status,
		);
		if($data['customer_suscription']['status'] == 'trialing'){
			$StartTrial = date('Y-m-d', $CustomerSubscriptionData->trial_start);
			$FormatStartTrial = $this->get_human_readable_date($StartTrial);
			$EndTrial = date('Y-m-d', $CustomerSubscriptionData->trial_end);
			$FormatEndTrial = $this->get_human_readable_date($EndTrial);

			$data['customer_suscription']['trial_start'] = $FormatStartTrial;
			$data['customer_suscription']['trial_end']   = $FormatEndTrial;
		}
		
		$this->load->view('comun/dashheader');
		$this->load->view('account/dashboard', $data);
		$this->load->view('comun/dashfooter');
	}

	public function logout(){

		$destroyed = $this->Account_model->destroy_customer_session($this->session->userdata('uid'));
		$destroyed = true;
		if($destroyed)
		{
			$this->session->sess_destroy();
			$this->delete_cookie("token");
			redirect(base_url('account/signin'),'refresh');			
		}
		else
		{
			$this->msg->toast('alert-circle.svg', 'Error', 'Imposible cerrar sesión en este momento, intenta más tarde.');
			redirect(base_url('account/dashboard'));
		}
	}

	public function sendmail($mailto = "", $subject = "", $data = "", $template_name = "default.php")
    {
		$this->load->library('phpmailer_lib');
		$mail = $this->phpmailer_lib->load();
        $mail->isSMTP();
		/** 
		# Add mailtrap code here for my local project :
		 */
		$mail->Host     = 'smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'tls';
		$mail->Username = '4f657c4c247aca';
		$mail->Password = 'f4eb9e951b6937';
		$mail->Port     = 2525;

        // $mail->Host     = 'lawkit.mx';
        // $mail->SMTPAuth = true;
        // $mail->SMTPSecure = 'ssl';
        // $mail->Username = 'no-reply@lawkit.mx';
        // $mail->Password = 'CKijfD$jqv1)';
        // $mail->Port     = 465;
        $mail->setFrom('no-reply@lawkit.mx', 'Lawkit');
        $mail->addAddress($mailto);
        $mail->addCC('elnew.luis@gmail.com');
        $mail->Subject = $subject;
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

		$mensaje = $this->load->view('emailtemplates/' . $template_name, $data, TRUE);
        $mail->Body = $mensaje;
		
        if(!$mail->send())
        	file_put_contents(APPPATH . 'logs/email_errors.log', date("Y/m/d H:i:s") . " | " . $mail->ErrorInfo . "\r\n", FILE_APPEND);
    }

	

    public function email_template(){
    	$data["content"] = "lorem";
    	$this->load->view('emailtemplates/default', $data);
    }



    

    public function upgrade(){
    	$currentSubscriptionData = $this->Account_model->getSubscriptionData();
		$api_error = "";
    	if($this->input->post('myCard')){
			$card = $this->stripe->customers->retrieveSource(
			  $this->session->userdata('customer_stripe_id'),
			  $this->input->post('myCard'),
			  []
			);
			if($card && $card->id){
				try{
					$subscription = $this->stripe->subscriptions->create([
					    'customer'  => $this->session->userdata('customer_stripe_id'),
					    'items'     => array(
							array(
								'price' => LAWKIT_10,
							),
						),
						'default_payment_method' => $card->id
					]);
				}
				catch(Exception $e){
					$api_error = $e->getMessage();
					$this->msg->toast('alert-circle.svg', 'Error', $api_error);
				}			
			}
			else{
				$this->msg->toast('alert-circle.svg', 'Error', 'Tu tarjeta no pudo ser procesada para realizar el pago.');
			}			

			
			if(empty($api_error)){
				$subscription_data         = $subscription->jsonSerialize();
				$updated                   = $this->Invoices_model->update_customer_subscription($subscription_data, $currentSubscriptionData->subs_id);
				if($updated && $updated['error'])
					$api_error = $updated['message'];
			}

			if(!empty($api_error)){
				$this->msg->toast('alert-circle.svg', 'Error', $api_error);
			}

			$this->msg->toast('check-square.svg', 'Aviso', $updated['message']);
			redirect('account/dashboard', 'refresh');

		}

    	$data['msg_user'] = '';
    	$this->acl->if_session_not_exists();
    	$data['cards'] = [];
    	$CustomerSubscriptionData = $this->GetSubscriptionCustomerDataBySubscriptionId($this->session->userdata('subscription_id'));
    	$data['cards'] = $this->getCustomerStripeSources($this->session->userdata('customer_stripe_id'))['data'];
    	    	    	
    	if($CustomerSubscriptionData->status == 'active'){ # && invoice->paid
			$EndTrial       = date('Y/m/d', strtotime($CustomerSubscriptionData->trial_end));
			$FormatEndTrial = $this->get_human_readable_date($EndTrial);
    		$message        = 'Tu cuenta de servicios de Lawkit finaliza el día ' . $FormatEndTrial;
    		$this->msg->toast('alert-circle.svg', 'Error', $message);
			redirect(base_url() . "account/dashboard", 'refresh');
		}
		else{
			$subscriptionData = $this->Account_model->getSubscriptionData();
			if($subscriptionData->plan_period_end > $this->timestamp){
				$newDate          = date('Y/m/d', strtotime($subscriptionData->plan_period_end));
				$data['msg_user'] = 'Si vuelves a suscribirte, la suscripción nueva comenzará el ' . $newDate;
			}
		}
		
		$data['customer_suscription'] = array(
			'id'        => $CustomerSubscriptionData->id,
			'customer'  => $CustomerSubscriptionData->customer,
			'status'    => $CustomerSubscriptionData->status,
		);

    	$this->load->view('comun/dashheader');
    	$this->load->view('account/upgrade', $data);
    	$this->load->view('comun/dashfooter');


    }

    public function invoices(){
    	
		$this->acl->if_session_not_exists();
    	if(!empty($this->session->userdata('subscription_id'))){
    		try{
				$data['invoices'] = $this->stripe->invoices->all(['subscription' => $this->session->userdata('subscription_id')]);
    		}
	    	catch(Exception $e){
	    		$this->msg->toast('alert-circle.svg', 'Error', 'No pudimos obtener tus facturas.');
	    	}    		
    	}
		else $data['invoices'] = array();
		$data['customer_suscripcion'] = $this->Account_model->get_subs_status();
    	$this->load->view('comun/dashheader');
    	$this->load->view('account/invoices', $data);
    	$this->load->view('comun/dashfooter');
    }

    public function profile(){
    	
    	if($this->input->post()){
    		$CustomerSubscriptionData = $this->GetSubscriptionCustomerDataBySubscriptionId($this->session->userdata('subscription_id'));
    		$updated = $this->Account_model->update_customer_profile($this->input->post());
    		if($updated){
    			$this->stripe->customers->update(
				  $CustomerSubscriptionData["customer"],
				  ['name' => $this->input->post("fname") . " " . $this->input->post("lname")]
				);
    			$this->msg->toast('check-square.svg', 'Aviso', 'Tus datos fueron actualizados.');
    		}
    		else{
    			$this->msg->toast('alert-circle.svg', 'Error', "Hubo un problema al actualizar datos.");
    		}
    	}
    	redirect(base_url() . "account/dashboard", "refresh");
    }

    public function upload_photo(){
    	if(!empty($_FILES['image']['name']))
			$img = $this->doupload->image($_FILES['image'], 'assets/images/photo_customers', 'No se pudo guardar la imagen de perfil.');
		echo json_encode($img);
		if($img["filename"] != "")
			$this->Account_model->update_photo($img["filename"]);
		$this->session->set_userdata('photo', $img["filename"]);
    }

    public function cancel_subscription(){
    	
    	if($this->input->post()){
			$canceled = $this->stripe->subscriptions->update(
			  $this->session->userdata('subscription_id'),
			  [
			  	'cancel_at_period_end' => true
			  ]
			);
			if($canceled->id){
				$this->Account_model->cancel_subscription($this->input->post());
				$this->msg->toast('check-square.svg', 'Aviso', 'Tu suscripción ha sido cancelada.');
				redirect(base_url() . "account/dashboard", "refresh");
			}
			else{
				$this->msg->toast('alert-circle.svg', 'Error', "No se pudo cancelar tu suscripción");
				redirect(base_url() . "account/dashboard", "refresh");
			}
    	}
    	$this->load->view('comun/dashheader');
    	$this->load->view('account/cancel_subscription');
    	$this->load->view('comun/dashfooter');
    }

    public function recovery()
	{
		$this->acl->if_session_exists();
		$this->cookie_exists();
		$this->load->view('account/recovery');
	}

	public function recovery_password(){
		$pass = random_string('alnum',10);
		
		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
		if($this->form_validation->run() == FALSE){
			$this->msg->message_error('Ingresa un correo electrónico válido.');
			redirect(base_url() . 'accounts/recovery', 'refresh');
		}
		else{
			$cus = $this->Account_model->set_new_password_by_email($this->input->post('email'), $pass);
			if($cus){
				$data['content'] = '<p>Estimado ' . $cus->fname .' ' . $cus->lname . ', hemos generado una nueva contraseña de acceso a Lawkit</p>';
				$data['content'] .= '<p>Nueva contraseña: <b>' . $pass . '</b></p>';
				$this->sendmail($this->input->post('email'), 'Nueva contraseña de acceso a Lawkit', $data);
				$this->msg->toast('check-square.svg', 'Aviso', 'Hemos enviado una nueva contraseña de acceso a tu correo electrónico proporcionado.');
				redirect(base_url() . "account/signin", 'refresh');
			}
			else{
				$this->msg->toast('alert-circle.svg', 'Error', 'Este correo electrónico no se encuentra registrado.');
				redirect(base_url() . 'account/recovery', 'refresh');
			}
		}
	}

	public function forgotten_pwd(){

		
		if($this->input->post()){
			if($this->input->post('new_pwd') != $this->input->post('conf_pwd')){
				$this->msg->toast('alert-circle.svg', 'Error', 'Confirma correctamente tu nueva contraseña');
				redirect(base_url() . "account/dashboard", "refresh");
			}
			else{
				$get_current_pwd = $this->Account_model->get_current_pwd($this->session->userdata('uid'));
				if($this->input->post('current_pwd') != $get_current_pwd){
					$this->msg->toast('alert-circle.svg', 'Error', 'Tu contraseña actual no coincide con nuestros registros, intenta nuevamente o reestablece tu contraseña');
					redirect(base_url() . "account/dashboard", "refresh");
				}
			}

			$pass = $this->input->post('conf_pwd');
			$cus = $this->Account_model->set_new_password_by_email($this->session->userdata('email'), $pass);
			if($cus){
				$data['content'] = '<p>Estimado ' . $cus->fname .' ' . $cus->lname . ', hemos generado una nueva contraseña de acceso a Lawkit</p>';
				$data['content'] .= '<p>Nueva contraseña: <b>' . $pass . '</b></p>';
				$this->sendmail($this->input->post('email'), 'Nueva contraseña de acceso a Lawkit', $data);
				$this->msg->toast('check-square.svg', 'Aviso', 'Hemos enviado una nueva contraseña de acceso a tu correo electrónico proporcionado.');
				redirect(base_url() . "account/dashboard", 'refresh');
			}
			else{
				$this->msg->toast('alert-circle.svg', 'Error', 'No pudimos enviar por correo tu nueva contraseña de acceso. intenta nuevamente o reestablece tu contraseña');
				redirect(base_url() . 'account/dashboard', 'refresh');
			}
		}

		redirect(base_url() . "account/dashboard", "refresh");
	}



	public function pay(){
		$api_error = "";
		$currentSubscriptionData = $this->Account_model->getSubscriptionData();
		$card                    = $this->saveCardOnPay($this->input->post('cardToken'));
		if($card && $card->id){
			try{
				$subscription = $this->stripe->subscriptions->create([
				    'customer' => $currentSubscriptionData->stripe_customer_id,
				    'items'     => array(
						array(
							'price' => LAWKIT_10,
						),
					),
					'default_payment_method' => $card->id
				]);
			}
			catch(Exception $e){
				$api_error = $e->getMessage();
				echo json_encode(['error' => true, 'message' => $api_error]);
			}			
		}
		else{
			echo json_encode(['error'   => true, 'message' => 'No se guardó el método de pago']);
		}

		if(empty($api_error)){
			$subscription_data         = $subscription->jsonSerialize();			
			$updateCustomerSuscription = $this->Invoices_model->update_customer_subscription($subscription_data, $currentSubscriptionData->subs_id);
			echo json_encode($updateCustomerSuscription);
		}
	}


	public function savecard(){
		$token             = $this->input->post('cardToken');
		$response          = "";
		$stripe_api_errors = "";
		try{
			$response = $this->stripe->customers->createSource(
			  $this->session->userdata('customer_stripe_id'),
			  ['source' => $token]
			);
			echo json_encode($response);
		}
		catch(Exception $e){
			$stripe_api_errors = $e->getMessage();
			echo json_encode(
				array(
					"error"   => true,
					"message" => $stripe_api_errors
				)
			);
		}
	}

	public function carddelete(){
		$cardId = $this->input->get('id');
		try{
			$this->stripe->customers->deleteSource(
			  $this->session->userdata('customer_stripe_id'),
			  $cardId,
			  []
			);
			$this->msg->toast('check-square.svg', 'Aviso', 'Tu tarjeta ha sido eliminada');
		}
		catch(Exception $e){
			$this->msg->toast('alert-circle.svg', 'Error', $e->getMessage());
		}
		redirect(base_url() . "account/dashboard", 'refresh');
	}

	public function cardretrieve(){
		$cardId = $this->input->get('id');
		try{
			$response = $this->stripe->customers->retrieveSource(
			  $this->session->userdata('customer_stripe_id'),
			  $cardId,
			  []
			);
			echo json_encode($response);
		}
		catch(Exception $e){
			echo json_encode([
				'error' => true,
				'message' => $e->getMessage()
			]);
		}
	}




	/**
	 * stripe api
	 * */
	public function GetCustomerDataStripe($customer_id){
		try{
	    	$customer_data     = $this->stripe->customers->retrieve(
				$customer_id
			);
			return $customer_data;			
		}
    	catch(Exception $e){
    		$this->msg->toast('alert-circle.svg', 'Error', 'Tu suscripción ha sido eliminada.');
    	}
    }

    public function getCustomerStripeSources($customer_stripe_id){
    	try{
	    	return $this->stripe->customers->allSources(
			  $customer_stripe_id,
			  ['object' => 'card', 'limit' => 10]
			);    		
    	}
    	catch(Exception $e){
			$this->msg->toast('alert-circle.svg', 'Error', 'Tu suscripción ha sido eliminada.');
		}
    }

    public function StripeRetrieveCustomerByCustomerId($StripeCustomerId){
    	try{
			return $this->stripe->customers->retrieve(
			  $StripeCustomerId,
			  []
			);
    	}
    	catch(Exception $e){
    		$this->msg->toast('alert-circle.svg', 'Error', 'Tu suscripción ha sido eliminada.');
    	}
	}
	public function GetSubscriptionCustomerDataBySubscriptionId($SubscriptionId){
		try{
			return $this->stripe->subscriptions->retrieve(
			  $SubscriptionId,
			  []
			);			
		}
    	catch(Exception $e){
    		$this->msg->toast('alert-circle.svg', 'Error', 'Tu suscripción ha sido eliminada.');
    	}
	}
	public function StripeGetCustomerInvoicesBySubscriptionId($SubscriptionId){
		try{
			return $this->stripe->invoices->all(['subscription' => $SubscriptionId]);
		}
    	catch(Exception $e){
    		$this->msg->toast('alert-circle.svg', 'Error', 'Tu suscripción ha sido eliminada.');
    	}
	}

	public function saveCardOnPay($token){
		try{
			$response = $this->stripe->customers->createSource(
			  $this->session->userdata('customer_stripe_id'),
			  ['source' => $token]
			);
			return $response;
		}
		catch(Exception $e){
			return array("error" => true, "message" => $e->getMessage());
		}
	}


	public function facturacion()
	{
		if($this->input->post()){
			if(strlen($this->input->post('nombreCompleto')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo nombre completo es obligatorio'
				]);
				return;
			}
			if(strlen($this->input->post('rfc')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo RFC es obligatorio'
				]);
				return;
			}
			if(strlen($this->input->post('direccion')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo dirección es obligatorio'
				]);
				return;
			}
			if(strlen($this->input->post('colonia')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo colonia es obligatorio'
				]);
				return;
			}
			if(strlen($this->input->post('municipioOCiudad')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo municipio o ciudad es obligatorio'
				]);
				return;
			}
			if(strlen($this->input->post('codigoPostal')) <= 0){
				echo json_encode([
					'error'   => true,
					'message' => 'El campo código postal es obligatorio'
				]);
				return;
			}
			$affected = $this->Account_model->saveDFacturacion($this->input->post());
			if(!$affected){
				echo json_encode([
					'error'   => true,
					'message' => 'Los datos de facturación no pudieron ser guardados'
				]);
				return;
			}
			echo json_encode([
				'message' => 'Los datos de facturación han sido guardados'
			]);
			return;
		}
		$data = [];
		$data['datosf'] = $this->Account_model->listarDFacturacion($this->session->userdata('uid'));
		$this->load->view('comun/dashheader');
		$this->load->view('account/facturacion', $data);
		$this->load->view('comun/dashfooter');
		//redirect(base_url() . "account/dashboard", "refresh");
	}

	public function datos()
	{
		$id = $_GET['id'];
		$affected = $this->Account_model->usarDireccion($id);
		if($affected){
			echo json_encode([
				'message' => 'La dirección ha sido establecida para usar en las facturas'
			]);
			return;
		} else {
			echo json_encode([
				'error'   => true,
				'message' => 'No se pudo establecer esta dirección como predeterminada para la facturación'
			]);
		}
	}

	public function save_user_coupon()
	{
		$api_error = "";
		$ownCode = $this->Account_model->codeAlreadyExistByUser($this->input->post('MdCupon'));
		if(count($ownCode) > 0){
			$this->msg->toast('alert-circle.svg', 'Error', "El cupón no es válido. E993");
			redirect(base_url() . "account/dashboard", 'refresh');
			return;
		}
		
		try {
			$coupon = $this->createCoupon('once', LAWKIT_10_DISCOUNT);
		} catch (Exception $e) {
			$api_error = $e->getMessage();
		}

		if($api_error == "")
		{
			//guarda cupon compartido para 1 solo uso
			$id = $this->Account_model->saveCouponCustomer($this->session->userdata('uid'), $coupon->id, "once");
			if($id > 0)
			{
				$this->msg->toast('check-square.svg', 'Aviso', "Has guardado un cupón para tú próxima factura");
				redirect(base_url() . "account/dashboard", 'refresh');
			}
			else
			{
				$this->msg->toast('alert-circle.svg', 'Error', "El cupón no es válido. E1018");
				redirect(base_url() . "account/dashboard", 'refresh');
			}
		}
		else
		{
			$this->msg->toast('alert-circle.svg', 'Error', $api_error);
			redirect(base_url() . "account/dashboard", 'refresh');
		}
	}
    
    
    
	public function post_contact()
	{
		
		if($this->input->post()){
			$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
			$this->form_validation->set_rules('name', 'name', 'required');
			$this->form_validation->set_rules('subject', 'subject', 'required');
			$this->form_validation->set_rules('message', 'message', 'required');
			if($this->form_validation->run() == FALSE){
				$errors[] = 'Favor de revisar que los datos sean válidos. ';
			}else{}
			
		}else{
		    $errors[] = "sin datos";
		}
		
		$captcha_response = true;
        $recaptcha = $this->input->post('g-recaptcha-response');
     
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $dat = array(
            'secret' => '6LfQAsQkAAAAABK_Al3dOMclI5iLEV3z6HVJkxzA',
            'response' => $recaptcha
        );
        $options = array(
            'http' => array (
                'method' => 'POST',
                'content' => http_build_query($dat)
            )
        );
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success = json_decode($verify);
        $captcha_response = $captcha_success->success;
     
        if ($captcha_response) {} else {
            $errors[] = 'Debes indicar que no eres un robot.';
        }
		
		if (isset($errors)){}else{
		    $data['content'] = "<p>La persona <b>". $this->input->post('name')."</b>  Dejó su correo (<a href='mailto:".$this->input->post('email')."'>".$this->input->post('email')."</a>) con el siguiente mensaje:</p>";
			$data['content'] .= "<p>". $this->input->post('message')."</p>";
		    
		    $envio = $this->sendmail('hola@lawkit.mx', 'Web Contactanos - '.$this->input->post('subject'), $data);
    		
    		if(!$envio){
    		    $messages[] = "Mensaje enviado correctamente, favor de esperar a que lo contactemos!";
    		}else{
    		    $errors[] = "El envio de datos tuvo un error, favor de contactarte directamente a: <b><ahref='mailto:hola@lawkit.mx'>hola@lawkit.mx</a><b/>".$envio;
    		}
    		//$messages[] = "Mensaje enviado correctamente, favor de esperar a que lo contactemos!";
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

/* End of file Account.php */
/* Location: ./application/controllers/Account.php */