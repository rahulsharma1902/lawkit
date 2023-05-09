<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_model extends CI_Model {

	public $timestamp;

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");
		$this->timestamp = date("Y-m-d H:i:s");
	}

	public function get_subscription_id_db($id){
		$sub = $this->db->get_where('customer_subscriptions', array("stripe_subscription_id" => $id))->row();
		if($sub)
			return $sub->subs_id;
	}

	public function add_new_subscription($data, $uid){
		$new_subscription_data = array(
			'customer_uid'              => $uid,
			'payment_method' 			=> 'stripe',
			'stripe_subscription_id' 	=> $data['id'],
			'stripe_customer_id' 		=> $data['customer'],
			'stripe_plan_id' 			=> $data['plan']['id'],
			'plan_amount' 				=> ($data['plan']['amount']/100),
			'plan_amount_currency' 		=> $data['plan']['currency'],
			'plan_interval' 			=> $data['plan']['interval'],
			'plan_interval_count' 		=> $data['plan']['interval_count'],
			'created' 					=> date('Y-m-d H:i:s', $data['created']),
			'plan_period_start' 		=> date('Y-m-d H:i:s', $data['current_period_start']),
			'plan_period_end' 			=> date('Y-m-d H:i:s', $data['current_period_end']),
			'status' 					=> $data['status']
		);
		$this->db->insert('customer_subscriptions', $new_subscription_data);
		$id = $this->db->insert_id();
		if($id){			
			$this->session->set_userdata('subscription_id', $data["id"]);
			return $id;
		}
	}

	//actualiza por renovacion
	public function update_customer_subscription($data, $subs_id){
		$this->db->set('stripe_subscription_id', $data['id']);
		$this->db->set('plan_period_start', date('Y-m-d H:i:s', $data['current_period_start']));
		$this->db->set('plan_period_end', date('Y-m-d H:i:s', $data['current_period_end']));
		$this->db->set('status', $data['status']);
		$this->db->where("subs_id", $subs_id);
		$this->db->update("customer_subscriptions");
		if( $this->db->affected_rows() > 0 )
		{
			$this->session->set_userdata('subscription_id', $data["id"]);
			$this->session->set_userdata('period_start', date('Y-m-d H:i:s', $data['current_period_start']));
			$this->session->set_userdata('period_end', date('Y-m-d H:i:s', $data['current_period_end']));
			$this->session->set_userdata('period_status', $data['status']);
			return array(
				"message" => "Tu suscripción a los servicios de Lawkit ha sido renovada."
			);
		}
		else{
			return array(
				'error'   => true,
				'message' => 'No se actualizó la suscripción en Lawkit'
			);
		}
	}

	public function update($subscription_id, $status, $new_start, $new_end){
		$this->db->set("status", $status);
		$this->db->set("plan_period_start", $new_start);
		$this->db->set("plan_period_end", $new_end);
		$this->db->where("stripe_subscription_id", $subscription_id);
		$this->db->update("customer_subscriptions");
		if( $this->db->affected_rows() > 0 )
		{
			return array(
				"message" => "status updated"
			);
		}
	}

	public function getCustomerDiscountByRef($customer_id)
	{
		$SQL = 'SELECT id, code, type, applied
		FROM coupons
		WHERE uid = (
			SELECT customer_uid 
			FROM customer_subscriptions 
			WHERE stripe_customer_id = "'.$customer_id.'"
			LIMIT 1)
		and type = "once"
		and applied = 0
		order by id ASC
		LIMIT 1';
		$d = $this->db->query($SQL)->row();
		if(count($d) > 0){
			$this->db->set('applied', 1);
			$this->db->where('id', $d->id);
			$this->db->update('coupons');
			if($this->db->affected_rows() > 0)
				return $d;
		}
	}

	public function updated_subscription($subscription_id, $status, $start, $end){
		$this->db->set("plan_period_start", $start);
		$this->db->set("plan_period_end", $end);
		$this->db->set("status", $status);
		$this->db->where("stripe_subscription_id", $subscription_id);
		$this->db->update("customer_subscriptions");
		if( $this->db->affected_rows() > 0 )
		{
			return array(
				"message" => "status updated"
			);
		}
	}

	public function DataSubscriptionByLawkitCustomerId($LawkitCustomerId)
	{
		$this->db->select("CONCAT(a.fname, ' ', a.lname) as username, a.photo, b.*");
		$this->db->from('customer_subscriptions b');
		$this->db->join('customers a', 'a.uid=b.customer_uid','inner');
		$this->db->where('b.customer_uid', $LawkitCustomerId);
		$this->db->limit(1);
		$this->db->order_by('b.created', 'desc');
		return $this->db->get()->row();
	}

	public function updateSubscriptionCustomerByCharge($current, $new, $uid){
		$this->db->set('stripe_subscription_id', $new['id']);
		$this->db->set('plan_period_start', $new['current_period_start']);
		$this->db->set('plan_period_end', $new['current_period_end']);
		$this->db->set('status', $new['status']);
		$this->db->where('subs_id', $current->subs_id);
		$this->db->update("customer_subscriptions");
		if( $this->db->affected_rows() > 0 )
		{
			return array(
				"message" => "status updated"
			);
		}
	}
	
}

/* End of file Admin_model.php */
/* Location: ./application/models/Admin_model.php */