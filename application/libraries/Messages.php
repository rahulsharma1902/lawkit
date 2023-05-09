<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends CI_Controller {

	public function __construct()
	{
		$this->CI =& get_instance();
	}


	public function toast($svg_icon = '', $title = '', $message = ''){
		$html = '<div style="position: absolute; top: 0; right: 0;margin-right: 1rem;margin-top: 1rem;z-index:1090;" id="toast-parent">
						<div class="toast wow fadeInDown" role="alert" aria-live="assertive" aria-atomic="true"  data-delay="1000" data-autohide="true" data-animation="true">
							<div class="toast-header">
								<img src="'.base_url().'assets/icons/'.$svg_icon.'" class="rounded mr-2" alt="...">
							    <strong class="mr-auto">'.$title.'</strong>
							    <small>Aviso</small>
							    <button type="button" class="ml-2 mb-1 close close-toast" data-dismiss="toast" aria-label="Close">
							      <span aria-hidden="true">&times;</span>
							    </button>
						  	</div>
						  	<div class="toast-body">
						    	'.$message.'
						  	</div>
						</div>
					</div>';
		return $this->CI->session->set_flashdata('toast', $html);
	}

	public function message_error($message = "")
	{
		return $this->CI->session->set_flashdata('error', '<div class="alert animated slideInUp notifications text-white bg-dark rounded-0"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$message.'</div>');
	}

	public function message_success($message = "")
	{
		return $this->CI->session->set_flashdata('success', '<div class="alert animated slideInUp notifications text-white bg-dark rounded-0"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$message.'</div>');
	}

	public function message_custom($class = "alert-default", $message = "")
	{
		return $this->CI->session->set_flashdata('custom', '<div class="alert '.$class.' animated slideInUp notifications text-white bg-dark rounded-0"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$message.'</div>');
	}

}

/* End of file Messages.php */
/* Location: ./application/controllers/Messages.php */