<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_model extends CI_Model {

	public $system_salt;
	public $ip_client;
	public $timestamp;

	public function __construct()
	{
		parent::__construct();
		$this->system_salt = 'd41d8cd98f00b204e9800998ecf8427e';
		$this->ip_client = $this->input->ip_address();
		date_default_timezone_set("America/Mexico_City");
		$this->timestamp = date("Y-m-d H:i:s");		
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
	// public function getCode()
	// {
	// 	$hasCoupon = $this->db->get_where('coupons', [
	// 		'uid' => $this->session->userdata('uid')
	// 	])->row();
	// 	if($hasCoupon && count($hasCoupon) > 0)
	// 		return $hasCoupon->code;
	// }
	public function getCode()
	{
		$hasCoupon = $this->db->get_where('coupons', [
			'uid' => $this->session->userdata('uid')
		])->row();

		if ($hasCoupon && is_countable($hasCoupon) && count($hasCoupon) > 0) {
			return $hasCoupon->code;
		}
	return null; // or return an appropriate value if no coupon is found
	}

	public function acceptTermAndConditions($uid)
	{
		$this->db->set('tc_accepted', 1)->where('uid', $uid)->update('customers');
		if($this->db->affected_rows() > 0)
			return true;
	}

	public function termAndConditionsAcceptedByUser($uid)
	{
		return $this->db->where('tc_accepted', 1)->where('uid', $uid)->get('customers')->row();
	}

	public function confirm_customer($id){
		$this->db->set('confirmed',1);
		$this->db->where('uid',$id);
		$this->db->update('customers');
		if($this->db->affected_rows() >=0)
			return true;
	}

	
	public function set_new_password_by_email($email = "", $pass =''){
		if(!empty($email)){
			$this->db->where('email', $email);
			$customer = $this->db->get('customers')->row();
		}
		if(count($customer) > 0){
			//$tdes = new Cipher($this->system_salt . $customer->uid);
			//$pas = $tdes->Encrypt($pass);
			$this->db->set('pwd', password_hash($pass, PASSWORD_DEFAULT));
			$this->db->where('uid', $customer->uid);
			$this->db->update('customers');
			if($this->db->affected_rows() >=0){
				$customer->pas = $pass;
				return $customer;
			}
		}
	}

	public function codeAlreadyExistByUser($code)
	{
		$this->db->select('code');
		$this->db->where('code', $code);
		$this->db->where('uid', $this->session->userdata('uid'));
		return $this->db->get('coupons')->row();
	}

	public function saveCouponCustomer($uid, $coupon_id, $type = "once")
	{
		$this->db->insert('coupons', [
			'uid'  => $uid,
			'code' => $coupon_id,
			'type' => $type
		]);
		return $this->db->insert_id();
	}

	public function saveCouponReferred($coupon)
	{
		$QUERY = 'UPDATE coupons SET applied = (applied+1) WHERE code = "'.$coupon.'" and type = "forever" limit 1';
		if($this->db->query($QUERY)){
			$this->db->select('a.customer_uid, a.stripe_subscription_id');
			$this->db->join('coupons b', 'a.customer_uid = b.uid', 'inner');
			$this->db->where('b.code', $coupon);
			$this->db->where('b.type', "forever");
			$this->db->limit(1);
			return $this->db->get('customer_subscriptions a')->row();
		}
	}

	public function getDiscountsCustomer($uid, $currentDiscount = null)
	{
		$this->db->select('id, code');
		$this->db->where('uid', $uid);
		$this->db->where('type', 'once');
		$this->db->where('applied', 0);
		$discounts = $this->db->get('coupons')->result();
		if(count($discounts) > 0){
			foreach($discounts as $discount){
				if(!is_null($currentDiscount) && $discount->code == $currentDiscount->discount->coupon->id){
					$discount->actual = true;
					$discount->aplica = $this->get_human_readable_date(date('Y-m-d', $currentDiscount->current_period_end));
				}
				$discount->off    = $currentDiscount->discount->coupon->percent_off;
				$discount->valid  = $currentDiscount->discount->coupon->valid;
			}
			return $discounts;
		} else{
			return [];
		}
	}

	public function get_human_readable_date($date){
		$day = substr($date, 8, 2);
		$month = substr($date, 5, 2);
		$year = substr($date, 0, 4);
		$convert = DateTime::createFromFormat("Y-m-d", $date);
		$dayname = strftime("%A", $convert->getTimestamp());
		return $day . ' de '.$this->monthnamespanish[$month].' de ' . $year;
		
	}

	public function saveCouponGiftByReferred($giftCode, $couponReferred)
	{
		$QUERY = 'INSERT INTO coupons (uid, code, type)
			SELECT uid, "'.$giftCode.'", "once"
			FROM coupons
			WHERE code = "'.$couponReferred.'"
			AND type = "forever"
			LIMIT 1';
			$this->db->query($QUERY);
	}
// This Creat function add data to coustomers table of database table
	public function create($data){
		$email = $this->db->get_where('customers', array('email'=>$data["email"]))->row();
		if($email){
			return (object)array(
				'customer' => NULL,
				'error'    => true,
				'message'  => 'Este correo ya se encuentra registrado'
			);
		}
		if($data["password"] != $data["password_confirm"]){
			return (object)array(
				'customer' => NULL,
				'error'    => true,
				'message'  => 'Las contraseñas no coinciden'
			);
		}
		$uid = $this->uuid->v5($data['email'], $this->system_salt);
		//$tdes = new Cipher($this->system_salt . $uid);
		//$pas = $tdes->Encrypt($data["password"]);
		
		$in = array(
			'uid'       => $uid,
			'email'     => $data['email'],
			'fname'     => $data['fusername'],
			'lname'     => $data['lusername'],
			'pwd'       => password_hash($data["password"], PASSWORD_DEFAULT),
			'confirmed' => 0
		);
		$this->db->insert('customers', $in);
		$in['newpas']=$data["password"];
		return (object)array(
			'customer'=>$in
		);
	}

	public function check_subscription_customer($data){
		$customer = $this->db->get_where('customers', array('email' => $data['email'], 'deleted' => 0))->row();
		if(!$customer){
			return (object)array(
				"error"   => true,
				"message" => "Usuario o contraseña incorrectos"
			);
		}
		//legacy
		//$tdes = new Cipher($this->system_salt . $customer->uid);
		//$pass_decrypted = $tdes->Decrypt($customer->pwd);
		if(!password_verify($data["password"], $customer->pwd)){
			return (object)array(
				"error"   => true,
				"message" => "Usuario o contraseña incorrectos."
			);
		}
		
		return $customer;
	}

	public function destroy_customer_session($customer_uid)
	{
		$this->db->where('customer_uid', $customer_uid);
		$this->db->delete('customer_session');
		return $this->db->affected_rows();
	}

	public function reset($email)
	{
		$uid = $this->uuid->v5($email, $this->system_salt);
		return $this->destroy_customer_session($uid);
	}

	public function signin($data, $cookie = false){
		$customer = $this->db->get_where('customers', array('email' => $data['email']))->row();
		if(!$customer){
			return (object)array(
				"error"    => true,
				"message"  => "Usuario o contraseña incorrectos",
				"customer" => NULL
			);
		}

		if($cookie)
		{
			if(!password_verify($data["password"], $customer->pwd)){
				return (object)array(
					"error"    => true,
					"message"  => "Usuario o contraseña incorrectos",
					"customer" => NULL
				);
			}			
		}
		else
		{
			//$tdes = new Cipher($this->system_salt . $customer->uid);
			//$pass_decrypted = $tdes->Decrypt($customer->pwd);
			if(!password_verify($data["password"], $customer->pwd)){
				return (object)array(
					"error"    => true,
					"message"  => "Usuario o contraseña incorrectos",
					"customer" => NULL
				);				
			}		
		}

		if($customer->deleted == 1){
			return (object)array(
				"error"    => true,
				"message"  => "Tu cuenta ha sido suspendida.",
				"customer" => NULL
			);
		}
		if($customer->confirmed == 0){
			return (object)array(
				"error"    => true,
				"message"  => "Confirma tu cuenta haciendo clic en el enlace enviado a tu dirección de correo electrónico.",
				"customer" => NULL
			);
		}
		
		$this->db->from('customer_subscriptions');
		$this->db->where('customer_uid', $customer->uid);
		$this->db->limit(1);
		$this->db->order_by('created', 'desc');
		$CustomerSubscriptionData = $this->db->get()->row();
		if($CustomerSubscriptionData)
		{
			$php_sessid = session_id();
			$token = $this->webtoken->GenerateToken($customer, $data['password'], $php_sessid);
			$this->create_cookie("token", $token["token"]);
			$data = [
				"customer_uid" => $customer->uid,
				"customer_ip"  => $this->input->ip_address(),
				"token"        => $token["token"],
				'php_sessid'   => $php_sessid,
			];
			$this->db->replace("customer_session", $data);
			if($this->db->affected_rows() > 0){
				//file_put_contents(APPPATH . 'logs/tokens.log', date("Y/m/d H:i:s") . " usuario: ".$customer->uid." \r\n", FILE_APPEND);
			}

			$customer->token      = $token["token"];
			$customer->stripedata = $CustomerSubscriptionData;
		}
		return $customer;
	}

	public function check_customer_session($uid = null)
	{
		if(is_null($uid))
			$uid = $this->session->userdata('uid');

		$getValue = $this->db->get_where('customer_session', array('customer_uid' => $uid))->row();
		if($getValue && $getValue->php_sessid != ''){
			/* file_put_contents(APPPATH . 'logs/php_sessids.log', date("Y/m/d H:i:s") . " | php_sessid: " . $getValue->php_sessid . " | session_id(): " . session_id() . " | customer_uid: " . $uid . "\r\n", FILE_APPEND); */
			if($getValue->php_sessid == session_id()){
				return true;
			} else return false;
		} else return false;
	}

	public function check_payload_php_sessid($uid, $php_sessid_from_token)
	{
		if(is_null($uid))
			$uid = $this->session->userdata('uid');

		$getValue = $this->db->get_where('customer_session', array('customer_uid' => $uid))->row();
		if($getValue && $getValue->php_sessid != ''){
			/* file_put_contents(APPPATH . 'logs/php_sessids.log', date("Y/m/d H:i:s") . " Method: check_payload_php_sessid() | php_sessid: " . $getValue->php_sessid . " | session_id(): " . $php_sessid_from_token . " | customer_uid: " . $uid . "\r\n", FILE_APPEND); */
			if($getValue->php_sessid == $php_sessid_from_token){
				return true;
			} else return false;
		} else return false;
	}

	public function create_cookie($name, $value)
	{
		setcookie($name, $value, strtotime( ' +1 year' ), '/', COOKIE_HOSTNAME, true); 
	}

	public function delete_cookie($name, $value)
	{
		setcookie($name, $value, time() - 3600, '/', COOKIE_HOSTNAME, true); 
	}


	public function get_customer_by_uid($uid){
		$this->db->select('email, fname, lname, photo');
		return $this->db->get_where('customers', array('uid' => $uid))->row();
	}

	public function get_customer_by_email($email){
		$this->db->select('uid');
		$customer = $this->db->get_where('customers', array('email' => $email))->row();
		if($customer && $customer->uid)
			return $customer->uid;
	}

	public function getUserByEmail($email)
	{
		$this->db->select("a.uid, a.email, a.fname, a.lname, b.plan_period_start, b.plan_period_end, c.code");
		$this->db->join("customer_subscriptions b", "a.uid=b.customer_uid", "inner");
		$this->db->join("coupons c", "c.uid=a.uid", "inner");
		$this->db->where("a.role", 3);
		$this->db->where("a.confirmed", 0);
		$this->db->where("c.type", "forever");
		$this->db->where("a.email", $email);
		return $this->db->get('customers a')->row();
	}

	public function update_photo($photo){
		$this->db->set('photo', $photo);
		$this->db->where('uid', $this->session->userdata('uid'));
		$this->db->update('customers');
	}

	public function update_customer_profile($data){
		$this->db->set('fname', $data['fname']);
		$this->db->set('lname', $data['lname']);
		$this->db->set('customer_updated_at', $this->timestamp);		
		$this->db->set('photo', $data['photo']);
		$this->db->where('uid', $this->session->userdata('uid'));
		$this->db->update('customers');

		$this->session->set_userdata('fname', $data["fname"]);
		$this->session->set_userdata('lname', $data["lname"]);

		return $this->db->affected_rows();
	}

	public function cancel_subscription($data){
		$InsertCancelData = array(
			'customer_uid' 			=> $this->session->userdata('uid'),
			'reason'                => empty($data['reason']) ? "No seleccionó una razón": "",
			'other'                 => $data['other']
		);
		$this->db->insert('cancellations', $InsertCancelData);
		return $this->db->insert_id();
	}

	public function get_subs_status(){
		$this->db->select('status');
		$this->db->where('customer_uid', $this->session->userdata('uid'));
		return $this->db->get('customer_subscriptions')->row();
	}

	public function get_period_subs_by_customer(){
		$this->db->select('plan_period_end, status');
		$this->db->where('customer_uid', $this->session->userdata('uid'));
		return $this->db->get('customer_subscriptions')->row();
	}

	public function get_current_pwd($uid){
		$curr = $this->db->get_where('customers', array('uid'=>$uid))->row();
		//$tdes = new Cipher($this->system_salt . $uid);
		//$current_pwd = $tdes->Decrypt($curr->pwd);
		return $curr->pwd;
	}

	public function customer_is_subscribe($uid){
		$sub = $this->db->get_where('customer_subscriptions', array('customer_uid' => $uid))->row();
		if($sub->plan_period_end >= $this->timestamp && $sub->status != 'canceled'){
			return $this->db->get_where('customers', array('uid'=>$uid))->row();
		}
	}

	public function get_customer_id_by_stripe_customer_id($stripe_customer_id)
	{
		$this->db->from('customer_subscriptions');
		$this->db->where('stripe_customer_id', $stripe_customer_id);
		$this->db->limit(1);
		$this->db->order_by('created', 'desc');
		return $this->db->get()->row();		
	}

	public function getSubscriptionData(){
		return $this->db->get_where('customer_subscriptions', [
			'customer_uid' => $this->session->userdata('uid')
		])->row();
	}

	public function get_users()
	{
		return $this->db->get('customers')->result();
	}

	public function update_password($uid, $new_password)
	{
		$this->db->set('pwd', $new_password);
		$this->db->where('uid', $uid);
		$this->db->update('customers');
		if($this->db->affected_rows() > 0)
			return true;
	}

	// ~~~~ CRUD para datos de facturacion ~~~ //
	public function saveDFacturacion($data){
		$t = $this->db->get_where('facturama', ['customer_uid' => $this->session->userdata("uid")])->row();
		if($t <= 0)
			$actual = 1;
		else $actual = 0;
		$this->db->insert('facturama', [
			'customer_uid' => $this->session->userdata('uid'),
			'nombre'       => $data['nombreCompleto'],
			'rfc'          => $data['rfc'],
			'direccion'    => $data['direccion'],
			'colonia'      => $data['colonia'],
			'municipio'    => $data['municipioOCiudad'],
			'cp'           => $data['codigoPostal'],
			'actual'       => $actual
		]);
		return $this->db->insert_id();
	}

	public function listarDFacturacion($uid)
	{
		$this->db->where('customer_uid', $uid);
		$this->db->order_by('actual', 'desc');
		return $this->db->get('facturama')->result();
	}

	public function usarDireccion($id)
	{
		$this->db->set('actual', 0);
		$this->db->where('customer_uid', $this->session->userdata('uid'));
		$this->db->update('facturama');
		
			$this->db->set('actual', 1);
			$this->db->where('id', $id);
			$this->db->update('facturama');
			return $this->db->affected_rows();
		
	}

}

/* End of file Account_model.php */
/* Location: ./application/models/Account_model.php */