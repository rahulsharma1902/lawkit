<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Searches_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function remove_search($id){
		$this->db->where('id', $id);
		$this->db->delete('searches');
		return $this->db->affected_rows();
	}

	public function save_search($uid, $data){
		$in = array(
			'customer_uid' => $uid,
			'circuito'     => $data['circuito'],
		    'organo1'      => $data['organo1'],
			'words'        => $data['words']
		);
		$this->db->insert('searches',$in);
		$IdSearches = $this->db->insert_id();
		$words = json_decode($data['arr']);
		if(count($words) > 0){
			foreach($words as $key => $value){
				$i = array(
					'id_searches' => $IdSearches,
					'word'        => $value->word,
					'count'       => $value->count
				);
				$this->db->insert('word_searches', $i);
			}
		}
		return $IdSearches;
	}

	public function count_searches_customer($uid){
		$in = array(
			'customer_uid'  => $uid,
			'qty'           => 1
		);
		$this->db->insert('count_searches', $in);
		return $this->db->affected_rows();
	}

	public function get_data_searches(){
		$this->db->select('circuito, organo1, materia, words, search_date');
		$this->db->where('customer_uid', $this->session->userdata('uid'));
		$this->db->order_by('id', 'desc');
		$this->db->limit(20);
		return $this->db->get('searches')->result();
	}

	public function delete_search($id){
		//delete search
		$this->db->where('id', $id);
		$this->db->delete('searches');
		//delete words
		$this->db->where('id_searches', $id);
		$this->db->delete('word_searches');
		return $this->db->affected_rows();
	}

	public function get_favlist($uid){
		$this->db->select('words');
		$this->db->where('customer_uid', $uid);
		$this->db->group_by('words');
		return $this->db->get('searches')->result();
	}


	public function my_searches($data, $uid)
	{
		$in = array(
			'customer_uid' => $uid,
			'circuito'     => $data->circuito,
		    'organo1'      => $data->organos,
			'materia'      => implode(",", $data->materias),
			"words"        => $data->palabras
		);
		$this->db->insert('searches',$in);
	}


}

/* End of file Searches_model.php */
/* Location: ./application/models/Searches_model.php */