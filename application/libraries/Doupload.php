<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doupload extends CI_Controller {

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function image($inputFileName, $relative_path, $message_error = "")
    {
    	$result_upload = array();
    	$pathUpload = FCPATH . $relative_path;
    	$type = array('jpg', 'png', 'jpeg', 'webp');
    	if (isset($inputFileName)) {
    		if($inputFileName["error"] == 0){
    	        $ext = strtolower(pathinfo($inputFileName["name"], PATHINFO_EXTENSION));
    	        $name = md5(strtolower(basename($inputFileName["name"])) . time());
    	        $nameDB = $name . '.' . $ext;
    	        if(in_array($ext, $type)){
    	            if(move_uploaded_file($inputFileName["tmp_name"], $pathUpload . '/' . $nameDB)){
    	                $result_upload = array(
    	                	'image' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $relative_path . '/' . $nameDB,
    	                	'filename'=>$nameDB
    	                );
    	            }
    	            else{
	                    $result_upload = array(
	                    	'error' => $message_error
	                    );
    	            }
    	        }
    	        else{
    	        	$result_upload = array(
		            	'error' => 'Solo se permiten imagenes de tipo JPG, PNG o JPEG'
		            );
    	        }
    		}
    		else{
    			$result_upload = array(
            		'error' => $inputFileName['error']
            	);
    		}
    	}
    	else{
    		$result_upload = array(
            	'error' => 'No se detectó ninguna imagen'
            );
    	}
    	return $result_upload;
    }

    public function images($inputFileName, $relative_path)
    {
    	
    }

    public function file($inputFileName, $relative_path)
    {
    	
    }

    public function files($inputFileName, $relative_path)
    {
    	
    }

}

/* End of file Upload.php */
/* Location: ./application/libraries/Upload.php */