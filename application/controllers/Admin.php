<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . "libraries/stripe/init.php";
class Admin extends CI_Controller {

    public $stripe;
	public function __construct()
	{
		parent::__construct();
        $this->stripe = new \Stripe\StripeClient(STRIPE_API_KEY);
	}

    public function StripeRetrieveCustomerByCustomerId($StripeCustomerId){
        return $this->stripe->customers->retrieve(
          $StripeCustomerId,
          []
        );
    }
    public function GetSubscriptionCustomerDataBySubscriptionId($SubscriptionId){
        #Retrieve Subscription Customer
        return $this->stripe->subscriptions->retrieve(
          $SubscriptionId,
          []
        );
    }
    public function StripeGetCustomerInvoicesBySubscriptionId($SubscriptionId){
        return $this->stripe->invoices->all(['subscription' => $SubscriptionId]);
    }

	public function dashboard()
	{
		if($this->session->userdata('role') != 1){
			redirect(base_url() . "account/dashboard", "refresh");
		}

		$call = new Apicall;
        $pieSos         = array();
        $barsDevices    = array();
        $browsers_names = array();
        $browsers_data  = array();

        $data['datefrom'] = date('Y-01-01');
        $data['dateto']   = date('Y-12-31');

        $data['humandatefrom'] = $this->Admin_model->get_human_readable_date($data["datefrom"]);
        $data["humandateto"]   = $this->Admin_model->get_human_readable_date($data["dateto"]);

        if($this->input->post()){
            $data['datefrom'] = $this->input->post('datefrom');
            $data['dateto']   = $this->input->post('dateto');
            $data['humandatefrom'] = $this->Admin_model->get_human_readable_date($this->input->post('datefrom'));
            $data["humandateto"]   = $this->Admin_model->get_human_readable_date($this->input->post('dateto'));
        }

		$data['act'] = $this->Admin_model->GetDashboard($data['datefrom'], $data['dateto']);
        
		$data['all_calendars'] = json_decode($call->mkRequest('get', API_CALENDARIO . 'all_calendars/from/' . $data['datefrom'] . '/to/' . $data["dateto"]))->total;
        
        if(count($data['act']->sos) > 0){
            foreach ($data['act']->sos as $key_sos => $sos){
                if($sos->total > 0){
                    $pieSos[] = array(
                        'name'  => $sos->os,
                        'y'     => $sos->total
                    );
                }
            }
            $data['pie_sos'] = json_encode( $pieSos, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK );
        }

        if(count($data['act']->devices) > 0){
            foreach ($data['act']->devices as $key_device => $device){
                if($device->total > 0){
                    $barsDevices[] = array(
                        'name'    => $device->device,
                        'data'    => [$device->total]
                    );
                }
            }
            $data['bars_devices'] = json_encode( $barsDevices, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK );
        }

        if(count($data["act"]->browsers) > 0){
            foreach ($data["act"]->browsers as $kBrowser => $browser){
                if($browser->total > 0){
                    array_push($browsers_names, '"'.$browser->browser.'"');
                    array_push($browsers_data, $browser->total);
                }
            }
        }



        $data['browsers_names'] = implode(",",$browsers_names);
        $data['browsers_data']  = implode(",",$browsers_data);

        $data["users"] = $this->Admin_model->GetAllUsersPaginated(10, 0);

		$this->load->view('comun/dashheader');
		$this->load->view('admin/index', $data);
		$this->load->view('comun/dashfooter');


	}

    public function users_activity(){
        $data = array();
        $per_page = 10;
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3) - 1) : 0;
        $TotalUsers = $this->Admin_model->GetAllUsers();
        $data["users"] = $this->Admin_model->GetAllUsersPaginated($per_page, $page, $this->input->post());
        $config_pagination = array(
            'base_url'         => base_url() . 'admin/users_activity',
            'uri_segment'      => 3,
            'total_rows'       => $TotalUsers,
            'per_page'         => $per_page,
            'attributes'       => array('class'=>'btn bg-light mx-2 btn-page'),
            'last_link'        => false,
            'num_links'        => ceil($TotalUsers/$per_page),
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config_pagination);
        $data["links"] = $this->pagination->create_links();

        $this->load->view('comun/dashheader');
        $this->load->view('admin/users', $data);
        $this->load->view('comun/dashfooter');
    }

    public function activity(){
        if($this->uri->segment(3)){
            
        }
    }

}

/* End of file Admin.php */
/* Location: ./application/controllers/Admin.php */