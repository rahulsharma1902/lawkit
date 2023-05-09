<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl extends CI_Controller {

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	//Manda a iniciar sesión si no existe
	public function if_session_not_exists(){
		if(!$this->CI->session->userdata('uid'))
		{
			if(!get_cookie('token')){
				redirect(base_url() . "account/signin");
			}
			else{
				
				$jsonEncodedReturnArray = $this->CI->webtoken->verify(get_cookie("token"));
				if(isset($jsonEncodedReturnArray->error) && $jsonEncodedReturnArray->error == 1){
					setcookie("token", "", time() - 3600, '/', COOKIE_HOSTNAME, true); 
					redirect(base_url() . "account/signin");
				}
				else
				{
					$success = $this->CI->Account_model->destroy_customer_session($jsonEncodedReturnArray->uid);
					if($success){
						$postdata = [
							"email"    => $jsonEncodedReturnArray->email,
							"password" => $jsonEncodedReturnArray->pass
						];
						$signin = $this->CI->Account_model->signin($postdata, true);
						if($signin && !isset($signin->error))
						{
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
								'role'               => $signin->role
							);
							$this->CI->session->set_userdata($session);
							redirect(base_url("account/dashboard"));					
						}
						else{
							setcookie("token", "", time() - 3600, '/', COOKIE_HOSTNAME, true); 
							redirect(base_url() . "account/signin");
						}
					}
				}
			}
		}
	}

	public function if_session_exists(){
		if($this->CI->session->userdata('uid'))
			redirect(base_url() . "account/dashboard");
	}

	public function delete_token($uid){
		$this->CI->Account_model->destroy_customer_session($uid);
		setcookie("token", "", time() - 3600, '/', COOKIE_HOSTNAME, true); 
	}
}

/* End of file Acl.php */
/* Location: ./application/controllers/Acl.php */