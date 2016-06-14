<?php
/**
 * Funciones que apoyen agilizar detalles
 */
function pagination($base_url = '', $total_rows = 200, $per_page = 10){
	$this->FW->load->library('pagination');
		
		
	$config = array(
				'base_url' 		=> ($this->is_cms)? base_url('cms/'.strtolower(get_class($this->FW))).'/display_all/':base_url($base_url),
				'total_rows'	=> $total_rows,
				'per_page'		=> $per_page
				);
		
}
function mysql_date_to_dmy($date){
	$dateArray 	= explode('-', $date);
	$hoursArray	= explode(':', $date[2]);
		
	return $dateArray[2][0].$dateArray[2][1].'/'.$dateArray[1].'/'.$dateArray[0];
}
	
 /**
  * Función que despliega los países del mundo en un array
  * @param String	$languaje	sp para españo, en para inglés	b/d sp
  */
 function world_countries($language = 'sp'){
	$paises 		= array("Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");
	$countries 		= array("Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");
	
	$return_array 	= array('sp' => $paises, 'en' => $countries);
	
	return $return_array[$language];
 }
/**
 * Función que obtiene array con meses días y años
 */
 function date_components($languaje = 'sp', $yearset = 10){
 	
	//Array con días
	for($i = 1; $i<30; $i++)
 		$month_days[$i] = '["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"]';
		
 		$month_days[30] = '["1", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"]';
		$month_days[31]	= '["1","3","5","7","8","10","12"]';
	
	//Array con días de semana
	$weekdays['sp']		= array('Lunes', 'Martes', 'Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado','Domingo');
	$weekdays['en']		= array('Monday', 'Tuesday', 'Wednesday','Thursday','Friday','Saturday','Sunday');
	
	//Array con meses
 	$months['sp']		= array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
 	$months['en']		= array('January', 'February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
 	$monthNumber		= array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
 	$monthArray			= array_combine($monthNumber, $months[$languaje]);
 	
 	//Array con años
 	$current_year		= date('Y');
 	$last_year			= ($current_year - $yearset);
 	$top_year			= ($current_year + $yearset);
 	
 	for($a = $last_year; $a <= $current_year; $a++)
 		$previous_years[$a] = $a;
 	for($a = $current_year; $a <= $top_year; $a++)
		$next_years[$a] = $a;
	$timelineOrder	= array_unique(array_merge($previous_years, $next_years));
 	$timeline = array_combine($timelineOrder, $timelineOrder);
 	
	$return_date_array = array(
							'dias' 			=> $month_days,
							'meses'			=> $monthArray,
							'aAnteriores'	=> $previous_years,
							'aSiguientes'	=> $next_years,
							'lineaTiempo'	=> $timeline
							);
	return $return_date_array;
	
 }
/**
 * Función para cargar archivos externos css y/o js
 * @param $file Nombre del archivo a cargar
 * @param $type Especificar si es css o js
 * @param $internal Es un archivo interno al framework o no
 * 
 * @return html para cargar el archivo
 */
 function load_external_file($file = '', $type = 'css', $internal = true){
 	if($type == 'css' && $internal == true)
 	$html_load = '<link rel="stylesheet" type="text/css" href="'.base_url('library/css/'.$file).'">';
	elseif($type == 'js' && $internal == true)
	$html_load = '<script type="text/javascript" src="'.base_url('library/js/'.$file).'" ></script>';
	elseif($type == 'css' && $internal == false)
 	$html_load = '<link rel="stylesheet" type="text/css" href="'.$file.'">';
	elseif($type == 'js' && $internal == false)
	$html_load = '<script type="text/javascript" src="'.$file.'" ></script>';
	
	return $html_load;
 }
 /**
  * Obtener formato para desplegar imágenes
  * @param $src nombre de la imagen u origen completo si no se encuentra en la librería
  * @param $image_properties array asociativo con las propiesdades de la imagen (class, width, height, title, alt, etc.)
  * @param $library booleano true o false si es una imagen de la librería de imágenes o no
  * 
  * @return img html tag
  */
  function display_img($src = NULL, $image_properties = array(), $library = true){
  	$CI =& get_instance();
	$CI->load->helper('html');
	
  	$image_properties['src'] = ($library == true)?base_url('library/images/'.$src):$src;
	return img($image_properties);
  }