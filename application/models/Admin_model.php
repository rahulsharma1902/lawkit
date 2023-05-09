<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");
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

	public function get_human_readable_date($date){
		$day = substr($date, 8, 2);
		$month = substr($date, 5, 2);
		$year = substr($date, 0, 4);
		$convert = DateTime::createFromFormat("Y-m-d", $date);
		$dayname = strftime("%A", $convert->getTimestamp());
		return $this->weekdayname[$dayname] . ' ' . $day . ' de '.$this->monthnamespanish[$month].' de ' . $year;
		
	}

	public function GetActivityDataByCustomerId($CustomerId = ""){
		$request_data = (object)array();
		
		//Conteo de exploradores
		$sql = 'SELECT browser, COUNT(browser) as total
				FROM customer_activity
				WHERE customer_uid = "'.$CustomerId.'"
				GROUP BY browser';
		$request_data->browsers = $this->db->query($sql)->result();
		if(count($request_data->browsers) > 0){
			$UseSum = array_reduce($request_data->browsers, function(&$res, $item) {
			    return $res + $item->total;
			}, 0);

			foreach ($request_data->browsers as $key => $value){
				if($value->total > 0 && $UseSum > 0)
					$request_data->browsers[$key]->percent = number_format((($value->total*100)/$UseSum),2);
			}
		}

		//Conteo por S.O
		$sql = 'SELECT os, COUNT(os) as total
				FROM customer_activity
				WHERE customer_uid = "'.$CustomerId.'"
				GROUP BY os';
		$request_data->sos = $this->db->query($sql)->result();
		if(count($request_data->sos) > 0){
			$UseSum = array_reduce($request_data->sos, function(&$res, $item) {
			    return $res + $item->total;
			}, 0);

			foreach ($request_data->sos as $key => $value){
				if($value->total > 0 && $UseSum > 0)
					$request_data->sos[$key]->percent = number_format((($value->total*100)/$UseSum),2);
			}
		}

		//Dispositivos usados
		$sql = 'SELECT device, COUNT(device) as total 
				FROM customer_activity
				WHERE customer_uid = "'.$CustomerId.'"
				GROUP BY device';
		$request_data->devices = $this->db->query($sql)->result();
		if(count($request_data->devices) > 0){
			$UseSum = array_reduce($request_data->devices, function(&$res, $item) {
			    return $res + $item->total;
			}, 0);

			foreach ($request_data->devices as $key => $value){
				if($value->total > 0 && $UseSum > 0)
					$request_data->devices[$key]->percent = number_format((($value->total*100)/$UseSum),2);
			}
		}

		
		$CustSearches = (object)array();
		$sqlSea = $this->db->get_where('searches', array('customer_uid' => $CustomerId))->result();
		$qtySea = $this->db->get_where('count_searches', array('customer_uid' => $CustomerId))->num_rows();
		if($sqlSea){
			$CustSearches->RowsFavs = $sqlSea;

			$cwords = array();
			foreach ($sqlSea as $kk => $w){
				$arr = explode(",", $w->words);
				if(is_array($arr) && count($arr) > 0){
					foreach($arr as $kr => $vr){
						$cwords[] = $vr;
					}
				}
			}
			if(count($cwords)>0)
				$request_data->cwords = array_count_values($cwords);


			/*foreach ($CustSearches->RowsFavs as $key=>$searches){
				$getWords = $this->db->get_where('word_searches', array('id_searches' => $searches->id))->result();
				if(count($getWords)>0){
					$CustSearches->RowsFavs[$key]->words = $getWords;
						
						$UseSum = array_reduce($CustSearches->RowsFavs[$key]->words, function(&$res, $item) {
						    return $res + $item->count;
						}, 0);

						foreach ($CustSearches->RowsFavs[$key]->words as $keyWord => $valueWord){
							if($valueWord->count > 0 && $UseSum > 0)
								$CustSearches->RowsFavs[$key]->words[$keyWord]->percent = number_format((($valueWord->count*100)/$UseSum),1);
						}
				}
				else{
					$CustSearches->RowsFavs[$key]->words = array();
				}
			}*/
		}
		else $CustSearches->RowsFavs = null;
		$CustSearches->count_searches = $qtySea;
		$request_data->searches = $CustSearches;

		$call = new Apicall;
		$TotalCal = json_decode($call->mkRequest('get', API_CALENDARIO . 'totalcalendarscustomer/uid/' . $CustomerId));
		$request_data->calendars = $TotalCal->total_calendars > 0 ? $TotalCal->total_calendars : 0;

		return $request_data;
	}



	public function GetDashboard($from="",$to=""){
		$request_data = (object)array();
				
		//usuarios con actividad en el mes actual
		$sql = 'SELECT * FROM customer_activity 
		WHERE DATE(stamp_activity) >= "'.$from.'" 
		AND DATE(stamp_activity) <= "'.$to.'" 
		GROUP BY customer_uid';
		$qty_customers = $this->db->query($sql)->result();

		//total de usuarios registrados en el mes
		$sql = 'SELECT * FROM customers
		WHERE deleted = 0
		AND DATE(customer_create_at) >= "'.$from.'" 
		AND DATE(customer_create_at) <= "'.$to.'"';
		$customers_registered = $this->db->query($sql)->result();

		//Conteo de exploradores
		$sql = 'SELECT browser, COUNT(browser) as total
				FROM customer_activity
				WHERE DATE(stamp_activity) >= "'.$from.'" 
				AND DATE(stamp_activity) <= "'.$to.'"
				GROUP BY browser';
		$browsers = $this->db->query($sql)->result();

		//Conteo por S.O
		$sql = 'SELECT os, COUNT(os) as total
				FROM customer_activity
				WHERE DATE(stamp_activity) >= "'.$from.'" 
				AND DATE(stamp_activity) <= "'.$to.'"
				GROUP BY os';
		$sos = $this->db->query($sql)->result();

		//Dispositivos usados
		$sql = 'SELECT device, COUNT(device) as total 
				FROM customer_activity
				WHERE DATE(stamp_activity) >= "'.$from.'" 
				AND DATE(stamp_activity) <= "'.$to.'"
				GROUP BY device';
		$devices = $this->db->query($sql)->result();

		//Suma de todas las busquedas
		$sql = 'SELECT SUM(qty) as total_searches
		FROM count_searches
		WHERE DATE(timestamp) >= "'.$from.'" 
		AND DATE(timestamp) <= "'.$to.'"';
		$all_searches = $this->db->query($sql)->row();
		
		//Suma de todas las busquedas por usuario
		$sql = 'SELECT a.email, fname, lname, SUM(qty) as total_searches
		FROM customers a
		JOIN count_searches b on a.uid = b.customer_uid
		WHERE DATE(timestamp) >= "'.$from.'" 
		AND DATE(timestamp) <= "'.$to.'"
		GROUP BY a.uid
		ORDER BY SUM(qty) DESC
		LIMIT 10';
		$all_searches_by_customer = $this->db->query($sql)->result();

		//Actividad en los servicios
		$str = "";
		$tools = array('buscador', 'calculadora', 'contratos');
		$str = "Date," . implode(",", $tools) . "\r\n";
		$meses = array();
		$offset = 0;
		$current_tool = $tools[0];
		$activity = array();
		$dataCSV = array();



		/*for($i = 1; $i <= 12; $i++){
			$month = str_pad($i,2,"0",STR_PAD_LEFT);
		    if($i>10){
		        $month = $i;
		    }
			$inicio = date("Y-" . $month . "-01");
			$fin = date("Y-m-t", strtotime($inicio));
			$meses[$i] = array($fin);
		}

		




		$current_tool = $tools[0];//buscador
		for($t = 0; $t < count($tools); $t++){
			foreach ($meses as $key => $value){
				if($current_tool != $tools[$t]){
					echo 'current_tool cambio a: ' . $tools[$t] . "\r\n";
					$current_tool = $tools[$t];
				}
				else{
					echo 'current_tool no ha cambiado de: ' . $tools[$t] . "\r\n";
				}
			}
		}

		return;
		/*



*/
			for($t = 0; $t < count($tools); $t++){
				for($i=1; $i<=12; $i++){
				    $month = str_pad($i,2,"0",STR_PAD_LEFT);
				    if($i>10){
				        $month = $i;
				    }
					$inicio = date("Y-" . $month . "-01");
					$fin = date("Y-m-t", strtotime($inicio));
								
					$sql = 'SELECT tool, COUNT(tool) as total, "'.$fin.'" as month
							FROM customer_activity
							WHERE DATE(stamp_activity) >= DATE("'.$inicio.'")
							AND DATE(stamp_activity) <= DATE("'.$fin.'")
							AND tool = "'.$tools[$t].'"
							GROUP BY tool';
							
					$result = $this->db->query($sql)->row();
					
					if(count($result) > 0){
						$activity[$tools[$t]][] = array(
							'date'  => $fin,
							'total' => $result->total
						);
					}
					else{
						$activity[$tools[$t]][] = array(
							'date'  => $fin,
							'total' => 0
						);
					}
				}
			}

			if(count($activity)>0){
				for($m = 0; $m <= 12; $m++){
					
				}
			}
		//}

		//return $rows;
		//return $str;
		

		$sql = 'SELECT word, count FROM word_searches';
		$words = $this->db->query($sql)->result();
		$strWords = array();
		$i=0;
		if(count($words) > 0){
			foreach ($words as $word){
				do{
					array_push($strWords, $word->word);
					$i++;
				}while($i < $word->count);
			}
			$i=0;
		}


		$request_data->qty_customers = count($qty_customers) > 0 ? $qty_customers : array();
		$request_data->customers_registered = count($customers_registered) > 0 ? $customers_registered : array();
		$request_data->sos = count($sos) > 0 ? $sos : array();
		$request_data->devices = count($devices) > 0 ? $devices : array();
		$request_data->all_searches = $all_searches->total_searches > 0 ? $all_searches->total_searches : 0;
		$request_data->all_searches_by_customer = count($all_searches_by_customer) > 0 ? $all_searches_by_customer : array();
		$request_data->browsers = count($browsers) > 0 ? $browsers : array();
		//$request_data->activity = count($activity) > 0 ? implode(",",$activity) : array();
		$request_data->words = count($strWords) > 0 ? "'" . implode(" ", $strWords) . "'" : array();

		return $request_data;
	}


	public function GetAllUsers(){
		return $this->db->get_where('customers', array('role'=>0, 'deleted'=>0))->num_rows();
	}

	public function GetAllUsersPaginated($per_page, $page, $data = NULL){
		$this->db->query("SET lc_time_names = 'es_UY';");
		$this->db->select('a.uid, a.fname, a.lname, a.email, CONCAT_WS(" ", CONCAT(""), DATE_FORMAT(a.customer_create_at, "%d de"), CONCAT(UCASE(SUBSTRING(MONTHNAME(a.customer_create_at), 1, 1)), LCASE(SUBSTRING(MONTHNAME(a.customer_create_at), 2))), concat("de ", YEAR(a.customer_create_at)), concat("a las ", TIME_FORMAT(a.customer_create_at, "%H:%i"), " Hrs")
		        ) AS created_at, b.stripe_subscription_id, b.stripe_customer_id');
		$this->db->join('customer_subscriptions b', 'a.uid=b.customer_uid','left');
		if(!is_null($data) && isset($data["search_field"]) && isset($data["buscar"]))
			$this->db->like($data["search_field"], $data["buscar"]);
		$this->db->order_by('customer_create_at desc');
		$this->db->limit($per_page, ($page*$per_page));
		return $this->db->get_where('customers a', array('a.deleted'=>0))->result();
	}

}

/* End of file Admin_model.php */
/* Location: ./application/models/Admin_model.php */