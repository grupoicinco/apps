<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class analyticsapi{
	function analyticsapi(){
	    require_once(str_replace("\\","/",APPPATH).'libraries/google-api-src/Google_Client'.EXT); //Por si estamos ejecutando este script en un servidor Windows
	}
}