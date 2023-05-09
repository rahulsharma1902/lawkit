<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Webpush_model extends CI_Model {

	public $appid;
	public $system_salt;
	public $ip_client;

	public function __construct()
	{
		parent::__construct();
		$this->system_salt = 'd41d8cd98f00b204e9800998ecf8427e';
		$this->appid = '2ccd0c6d-0a15-450a-8588-7f904f125617';
		$this->ip_client = $this->input->ip_address();
	}

	public function addDevice($device){

		$this->db->select('id');
		$this->db->where('uid', $this->session->userdata('uid'));
		$user_exists = $this->db->get('web_push_notifications')->row();
		if(count($user_exists)>0){
			$this->db->set('device_id', $device);
			$this->db->set('device_id_mobile', $device);
			$this->db->where('id', $user_exists->id);
			$this->db->update('web_push_notifications');
		}
		else{
			$in = array(
				'uid'=>$this->session->userdata('uid'),
				'device_id'=>$device,
				'device_id_mobile'=>$device
			);
			$this->db->insert('web_push_notifications', $in);
		}

	}

	public function send_notification()
	{
		$devices = array();
		$calendars_to_notify = $this->db->get('notifications_calendar')->result();
		if(count($calendars_to_notify)>0){
			foreach($calendars_to_notify as $key => $value){
				$devices[]=$value->calendar_id;
			}
		}
		$this->db->select('a.name, a.end, DATEDIFF(a.end, DATE(NOW())) AS difference, b.days, c.device_id, c.device_id_mobile');
		$this->db->join('notifications_calendar b', 'a.calendar_id=b.calendar_id', 'left');
		$this->db->join('web_push_notifications c', 'c.uid=a.user_uid', 'left');
		$this->db->where_in('a.calendar_id', $devices);
		$this->db->where('a.deleted',0);
		$not = $this->db->get('users_calendar a')->result();
		if(count($not)>0){
			foreach ($not as $key => $value){
				if($value->difference == $value->days){
					$content = array(
						"en" => 'English Message',
						"es" => 'Tu fecha límite ' . $value->end . ', del calendario ' . $value->name . ' está próximo a vencer.'
						);
					
					$fields = array(
						'app_id' => $this->appid,
						'include_player_ids' => array($value->device_id),
						'contents' => $content
					);
					
					$fields = json_encode($fields);
			    			
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

					$response = curl_exec($ch);
					curl_close($ch);
					file_put_contents(APPPATH . 'logs/notifications.log', $response, FILE_APPEND);
				}
			}
		}
	}

	public function sendPushNotificationsToAdmins($content = ""){
		$content = array(
			"en" => $content
			);
		$arrDevices = array();
		$this->db->select('a.device_id, a.device_id_mobile');
		$this->db->join('users b', 'b.uid=a.uid', 'inner');
		$this->db->where('b.role',2);
		$devices = $this->db->get('web_push_notifications a')->result();
		if(count($devices)>0){
			foreach ($devices as $device){
				if(!is_null($device->device_id)){
					$arrDevices[]=$device->device_id;
				}
			}
		}
		
		$fields = array(
			'app_id' => $this->appid,
			'include_player_ids' => array($arrDevices),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

}

/* End of file Webpush_model.php */
/* Location: ./application/models/Webpush_model.php */