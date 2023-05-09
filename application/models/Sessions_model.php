<?php
class Sessions_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function clear()
	{
		$this->db->empty_table('customer_session');
		return $this->db->affected_rows();
	}
}
?>