<?php
/**
 * Converts entities to ascii descriptive
 */
function ascii_to_desc_entities($str){
	$entitiesChars 	= 'Ã||Ã‰||Ã||Ã“||Ãš||Ã¡||Ã©||Ã­||Ã³||Ãº||Ã‘||Ã±';
	$iso88591Chars	= 'Á||É||Í||Ó||Ú||á||é||í||ó||ú||Ñ||ñ';
	$friendlyascii	= "&Aacute;||&Eacute;||&Iacute;||&Oacute;||&Uacute;||&aacute;||&eacute;||&iacute;||&oacute;||&uacute;||&Ntilde;||&ntilde;";
	
	$entitiesArray	= explode("||", $entitiesChars);
	$iso88591Array	= explode("||", $iso88591Chars);
	$asciiArray		= explode("||", $friendlyascii);
	
	//Convert string
	$return_string 	= str_replace($entitiesArray, $asciiArray, $str);
	$return_string 	= str_replace($iso88591Array, $asciiArray, $return_string);
	
	return $return_string;
}
function entities_to_iso88591($str){
	$entitiesChars 	= 'Ã||Ã‰||Ã||Ã“||Ãš||Ã¡||Ã©||Ã­||Ã³||Ãº||Ã‘||Ã±';
	$iso88591Chars	= 'Á||É||Í||Ó||Ú||á||é||í||ó||ú||Ñ||ñ';
	
	$entitiesArray	= explode("||", $entitiesChars);
	$iso88591Array	= explode("||", $iso88591Chars);
	
	//Convert string
	$return_string 	= str_replace($entitiesArray, $iso88591Array, $str);
	
	return $return_string;
}
function amp_to_ascii($str){
	return str_replace('&amp;', '&', $str);
}
