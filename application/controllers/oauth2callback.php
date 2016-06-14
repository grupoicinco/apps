<?php
/**
 * Google register permission
 */
 class Oauth2callback extends MY_Controller {
     
     function __construct() {
     	parent::__construct();
		$this->load->library("analyticsapi");
     }
	 
	 public function index(){
		session_start();
		
		//Google API
		$GoogleClient	= new Google_Client();
		$GoogleClient->setApplicationName("API Project");
		//In code.google.com/apis/console?api=analytics Is the next information
		$GoogleClient->setClientId('383728281347.apps.googleusercontent.com');
		$GoogleClient->setClientSecret('AqsmEisGeLrTfDKkpRDAgCu5');
		$GoogleClient->setRedirectUri('http://framework.grupoperinola.net/oauth2callback');
		$GoogleClient->setDeveloperKey('AIzaSyBGIqjzoI1_txlbJMYKKuFJLi_PfPFjU5M');
		$GoogleClient->setScopes('https://www.googleapis.com/auth/analytics.readonly');
		
		$GoogleClient->setUseObjects(true);// Magic. Returns objects from the Analytics Service instead of associative arrays.
		
		//Authorization flow from the server
		if (isset($_GET['code'])) {
			$GoogleClient->authenticate();
			$this->session->set_userdata('token', $GoogleClient->getAccessToken());
			redirect('cms/panel_inicio');
		}else{
			redirect('cms');
		}
	 }
 }
 
