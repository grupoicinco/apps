<?php
class Api_analytics_service{
	function __construct(){
	    require_once(str_replace("\\","/",APPPATH).'libraries/google-api-src/contrib/Google_AnalyticsService'.EXT); //Por si estamos ejecutando este script en un servidor Windows
	}
}
