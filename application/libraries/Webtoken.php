<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/php-jwt/BeforeValidException.php';
require APPPATH . 'libraries/php-jwt/ExpiredException.php';
require APPPATH . 'libraries/php-jwt/SignatureInvalidException.php';
require APPPATH . 'libraries/php-jwt/JWT.php';
class Webtoken extends CI_Controller {

	public $uid;
	private static $server_key = 'd41d8cd98f00b204e9800998ecf8427e';
	private static $encrypt = ['HS256'];
	private static $aud = null;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function verify($token){
		try{
			return self::Check($token);
		}catch(Exception $e){
			$data = array(
				'error'   => true,
				'message' => $e->getMessage()
			);
			return (object)$data;
		}
	}

	private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public static function Check($token)
    {
        if(empty($token))
        {
            return (object)array('error'=>true, 'message'=>"Invalid token supplied");
        }

        $decode = \Firebase\JWT\JWT::decode(
            $token,
            self::$server_key,
            self::$encrypt
        );

        if($decode->aud !== self::Aud())
        {
            return (object)array('error'=>true, 'message'=>"Invalid user logged in.");
        }

        return self::GetData($token);
    }

    public static function GetData($token)
    {
        return \Firebase\JWT\JWT::decode(
            $token,
            self::$server_key,
            self::$encrypt
        );
    }

    
    public function GenerateToken($UserData, $pwdN, $phpSessId){
    	$nbf_time = date('Y-m-d H:i:s');
		$exp_time = new DateTime(date('Y-m-d H:i:s', strtotime($nbf_time . ' + 168 hour'))); //1 week
		$nbf = strtotime($nbf_time);
		$exp = strtotime($exp_time->format("Y-m-d H:i:s"));
		$payloadArray = array();
		$payloadArray['uid']        = $UserData->uid;
		$payloadArray['photo']      = isset($UserData->photo) ? $UserData->photo : "";
		$payloadArray['pass']       = $pwdN;
		$payloadArray['email']      = $UserData->email;
        $payloadArray['php_sessid'] = $phpSessId;
		if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
		if (isset($exp)) {$payloadArray['exp'] = $exp;}
		$payloadArray['aud'] = self::Aud();
		$token = \Firebase\JWT\JWT::encode($payloadArray, self::$server_key);
		return array('token' => $token);
    }
}