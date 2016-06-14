<?php
/**
 * 
 */
class Panel_inicio extends MY_Controller {
	
	var $ot;
	function __construct() {
		parent::__construct();
		
        $this->load->library("nusoap");
		$this->load->library("analyticsapi");
		$this->load->library("api_analytics_service");
		
		$this->ot							= 'OT1234';
		$this->analytics_account_number		= 0;
	}
	public function index(){
		
		//Listado de reclamos;
		$data['reclaims']	= $this->OpenReclaimsList(10);
		
		$this->load->templatecms('cms/panel_inicio', $data);
	}
	/*
	public function _index(){
		session_start();
		
		//Google API
		$GoogleClient	= new Google_Client();
		$GoogleClient->setApplicationName("API Project");
		//In code.google.com/apis/console?api=analytics Is the next information
		$GoogleClient->setClientId('383728281347.apps.googleusercontent.com');
		$GoogleClient->setClientSecret('AqsmEisGeLrTfDKkpRDAgCu5-');
		$GoogleClient->setRedirectUri('http://framework.grupoperinola.net/oauth2callback');
		$GoogleClient->setDeveloperKey('AIzaSyBGIqjzoI1_txlbJMYKKuFJLi_PfPFjU5M');
		$GoogleClient->setScopes('https://www.googleapis.com/auth/analytics.readonly');
		
		$GoogleClient->setUseObjects(true);// Magic. Returns objects from the Analytics Service instead of associative arrays.
		
		//Authorization flow from the server
		if (isset($_GET['code'])) {
			$GoogleClient->authenticate();
			$this->session->set_userdata('token', $GoogleClient->getAccessToken());
		}
		//retrieve and use store credentials
		if ($this->session->userdata('token')) {
			$GoogleClient->setAccessToken($this->session->userdata('token'));
		}
		//Prompt the user to login or run the demo
		$data['google_api_connect'] = '';
		if (!$GoogleClient->getAccessToken()) {
			$authUrl = $GoogleClient->createAuthUrl();
			$data['google_api_connect'] = "<a class='login btn btn-info' href='$authUrl'>Conectarme a Google Analytics</a>";
		}else{
			$analytics = new Google_AnalyticsService($GoogleClient);
			$data['analyticsResponse'] = $this->runMainDemo($analytics);
			
			//Remover la sesión si da algún error
			if($data['analyticsResponse']['ERROR'] != FALSE):
				$this->session->unset_userdata('token');
			endif;
		}
		
		//Webservice to display latest news
		$wsdl					= "http://fwws.grupoperinola.net/index.php?wsdl";
		$client					= new nusoap_client($wsdl, 'wsdl');
		$param					= array(
									'param' 		=> $this->ot,
									'last_session'	=> date('Y-m-d h:i:s')
								);
		
		$dashboard_data 		= $client->call('consultaPersonas', $param);
		$dashboard_array		= json_decode($dashboard_data, true); //Obtener la información en un array
		$nomenclatura			= json_decode($dashboard_array['NOMENCLATURA']); //Obtener los titulos según key
		$analytics				= json_decode($dashboard_array['ANALYTICS']); //Obtener los datos de accesos de analytics
		unset($dashboard_array['NOMENCLATURA']);
		unset($dashboard_array['ANALYTICS']);
		$data['dashboard']		= $dashboard_array;
		$data['nomenclatura']	= $nomenclatura;
		$data['analytics']		= $analytics;
		
		//Enviar email de asistencia
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="help-inline">', '</span>');
		
		//Validar que se haya ingresado desde un formulario
		if($this->input->post()){
				
			//Validar formulario de asistencia
			$this->form_validation->set_rules('inputName', 'Nombre', 'required');
			$this->form_validation->set_rules('inputEmail', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('inputMessage', 'Mensaje', 'required');
			
		
			if ($this->form_validation->run() == FALSE):
				$this->fw_alerts->add_new_alert(3002, 'ERROR');
			else:
				$this->fw_alerts->add_new_alert(3001, 'SUCCESS');
				$this->fw_posts->assistance_request_post();
			endif;
		}
		
		$this->load->templatecms('cms/panel_inicio', $data);
	}
	*/
	private function runMainDemo(&$analytics) {
	  try {
	    // Step 2. Get the users first profile ID.
	    $profileId = $this->getFirstProfileId($analytics);
	    if (isset($profileId)) {
	
	      // Step 3. Query the Core Reporting API.
	      $results = $this->getResults($analytics, $profileId);
	
	      // Step 4. Output the results.
	      $response = $this->printResults($results);
	    }
	
	  } catch (Google_ServiceException $e) {
	    // Error from the API.
	    $response['ERROR'] 		= 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();
		$response['DATA']		= FALSE;
	
	  } catch (Exception $e) {
	    $response['ERROR'] 		= 'There was a general error : ' . $e->getMessage();
		$response['DATA']		= FALSE;
	  }
	  
	  return $response;
	}
	
	private function getFirstprofileId(&$analytics) {
	  $accounts = $analytics->management_accounts->listManagementAccounts();
	
	  if (count($accounts->getItems()) > 0) {
	    $items = $accounts->getItems();
	    $firstAccountId = $items[$this->analytics_account_number]->getId(); //Escoger que cuenta es la que se quiere obtener la información segun el key_number en el arreglo $items
	
	    $webproperties = $analytics->management_webproperties
	        ->listManagementWebproperties($firstAccountId);
		
	    if (count($webproperties->getItems()) > 0) {
	      $items = $webproperties->getItems();
	      $firstWebpropertyId = $items[0]->getId();
	
	      $profiles = $analytics->management_profiles
	          ->listManagementProfiles($firstAccountId, $firstWebpropertyId);
			
	      if (count($profiles->getItems()) > 0) {
	        $items = $profiles->getItems();
	        return $items[0]->getId();
	
	      } else {
	        throw new Exception('No profiles found for this user.');
	      }
	    } else {
	      throw new Exception('No webproperties found for this user.');
	    }
	  } else {
	    throw new Exception('No accounts found for this user.');
	  }
	}
	
	private function getResults(&$analytics, $profileId) {
		$monthbeginning	= date('Y').'-'.date('m').'-01';
		$current_date 	= date('Y').'-'.date('m').'-'.date('d');
		$intervalo		= $this->get_date_interval($monthbeginning, $current_date);
		
		//Agregar cada día en un array
		$month_days = array();
		for($i=0; $i<($intervalo + 1); $i++):
			$month_days[] = date("Y-m-d", strtotime("$monthbeginning +".$i." days"));
		endfor;
		foreach($month_days as $date):
			if($date <= $current_date):
			$visits[] = $analytics->data_ga->get(
	       								'ga:' . $profileId,
	       								$date,
	       								$date,
	       								'ga:visits');
			
			endif;
		endforeach;
		return $visits;
		/*
		echo '<pre>';
		print_r($visits);
		echo '</pre>';*/
	}
	/**
	 * Obtener el intervalo en días entre dos fechas
	 * @param	date	$beginning_date		Fecha en formato Y-m-d
	 * @param	date	$end_date			Fecha en formato Y-m-d
	 * 
	 * @return 	Number
	 */
	private function get_date_interval($beginning_date, $end_date){
		//defino fecha 1 
		$fecha1		= explode('-',$beginning_date);
		$ano1 		= $fecha1[0];
		$mes1 		= $fecha1[1]; 
		$dia1 		= $fecha1[2];
		
		//defino fecha 2
		$fecha2		= explode('-',$end_date);
		$ano2 		= $fecha2[0];
		$mes2 		= $fecha2[1]; 
		$dia2 		= $fecha2[2];
		
		//calculo timestam de las dos fechas 
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); 
		$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2); 
		
		//resto a una fecha la otra 
		$segundos_diferencia = $timestamp1 - $timestamp2; 
		//echo $segundos_diferencia; 
		
		//convierto segundos en días 
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24); 
		
		//obtengo el valor absoulto de los días (quito el posible signo negativo) 
		$dias_diferencia = abs($dias_diferencia); 
		
		//quito los decimales a los días de diferencia 
		$dias_diferencia = floor($dias_diferencia); 
		
		return $dias_diferencia; 
	}
	private function printResults(&$results) {
	  if (count($results) > 0) {
	  	
	  	//Almacenar en un array, cada valor en días de las visitas
	  	foreach($results as $result):
		    $rows = $result->getRows();
		    $visits[] = $rows[0][0];
		endforeach;
		
	    $response['ERROR'] 		= FALSE;
		$response['DATA']		= $visits;
	
	  } else {
	    $response['ERROR'] 		= 'No Results Found.';
		$response['DATA']		= FALSE;
	  }
	  return $response;
	}
	
	public function assistance_request_post(){
		
		redirect('cms/');
	}
	
	private function OpenReclaimsList($total = 6){
		$this->load->model('cms/cms_plugin_reclamos', 'cms_plugin_reclamos');
		$stage_display = array(
			'RECEPCION' => array('label-danger', 'Recepci&oacute;n'),
			'APROBACION' => array('label-warning', 'Aprobaci&oacute;n'),
			'REPARACION' => array('label-warning', 'Reparaci&oacute;n')
		);
		
		$reclaims = $this->cms_plugin_reclamos->total_pending_reclaims($total);
		foreach ($reclaims as $i => $reclaim){
			$process = $reclaim->PROCESS_STAGE;
			$reclaims[$i]->PROCESS_STAGES = $stage_display[$process];
		}
		
		return $reclaims;
	}
}
