<?php
/**
 * Conjunto de mensajes para desplegar en las alertas
 */
 if ( ! function_exists('display_message'))
{
 	function display_message($key){
 	
		//Login messages
		$return_message[1001]				= "<h4>Error al iniciar sesi&oacute;n</h4>Los datos de su sesi&oacute;n no coinciden, por favor intente nuevamente.";
		$return_message[1002]				= "<h4>Sesi&oacute;n expirada</h4> Ha salido de su sesi&oacute;n, por favor vuelva a ingresar.";
		$return_message[1003]				= "<h4>Sesi&oacute;n ingresada</h4> Ha ingresado a su sesi&oacute;n satisfactoriamente.";
		//Mi perfil
		$return_message[2001]				= "<h4>Informaci&oacute;n actualizada</h4> Se han actualizado sus datos de usuario satisfactoriamente, por favor vuelva a auntenticarse.";
		//Permisos
		$return_message[9990]				= "<h4>Ingreso incorrecto</h4> No puede ingresar a esta &aacute;rea directamente.";
		$return_message[9991]				= "<h4>Ingreso incorrecto</h4> Por favor revise que haya accedido correctamente, o comun&iacute;quese a <a href='mailto:tumicayala@grupoi5.com' target='_blank'>tumicayala@grupoi5.com</a> para solicitar un nuevo ingreso.";
		//Email notifications
		$return_message[3001]				= "<h4>Mensaje enviado</h4> Su mensaje ha sido enviado exitosamente.";
		$return_message[3002]				= "<h4>Error enviando mensaje</h4> Hubo alg&uacute;n error al enviar su mensaje, por favor intente nuevamente.";
		$return_message[3003]				= "<h4>Error enviando mensaje</h4> Los campos del formulario vienen vac&iacute;os, por favor completar los campos requeridos.";
		$return_message[3004]				= "<h4>Error enviando mensaje</h4> Revisar que todos los campos requeridos est&eacute;n debidamente llenos.";
		//Upload notifications
		$return_message[4001]				= "<h4>Archivo cargado</h4> Su archivo ha sido cargado exitosamente.";
		$return_message[4002]				= "<h4>Error al cargar archivo</h4> Su archivo no ha sido cargado, esto puede deberse a que no cumple con los par&aacute;metros establecidos. Por favor, revise que su archivo es v&aacute;lido e intente cargarlo nuevamente.";
		//CRUD notifications
		$return_message[4010]				= "<h4>Datos cambiados</h4> Su informaci&oacute;n ha sido actualizada exitosamente.";
		$return_message[4011]				= "<h4>Error al cambiar datos</h4> Su informaci&oacute;n no ha podido ser actualizada exitosamente.";
		$return_message[4012]				= "<h4>Datos Eliminados</h4> Su informaci&oacute;n ha sido eliminada exitosamente.";
		$return_message[4013]				= "<h4>Datos Ingresados</h4> Su informaci&oacute;n ha sido ingresada exitosamente.";
		//STAGE notifications
		$return_message[2020]				= "Esta orden ya se envío a aprobación al cliente y no puede ser modificada.";
		
		return $return_message[$key];
 	}
 }