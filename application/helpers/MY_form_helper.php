<?php
/**
 * Uploader button generator
 */
if ( ! function_exists('form_uploader'))
{
	/**
	 * Crear botón del uploader
	 * @param 	$data_url URL a requerir para cargar los archivos
	 * @param 	$input_specs Array asociativo con los atributos del botón
	 * @param	$imagesArray Array con las imágenes para desplegar
	 * @param 	$btnTxt Texto del botón a mostrar
	 */
	function form_uploader($data_url = "", $imagesArray = array(), $input_specs = array(), $btnTxt = 'Examinar')
	{
		$data_url	= (empty($data_url))?site_url('cms/plugin_uploader'):$data_url;
		//Obtener los specs del input
		unset($input_specs['type']);
		unset($input_specs['data-url']);
		unset($input_specs['name']);
		unset($input_specs['value']);
		unset($input_specs['multiple']);
		
		$input_specs['class'] 	= (!empty($input_specs['class']))?'fileuploader '.$input_specs['class']:'fileuploader';
		$groupedImages			= (!empty($imagesArray))?_parse_group_images($imagesArray):array();
		
		
		$atts = _parse_form_attributes($input_specs, array());
		
		$result			=  '<div class="span6">';
		$result			.= 	'<div class="row-fluid">';
		$result			.= 		'<div class="span3">';
		$result			.= 			'<span class="btn btn-block btn-info fileinput-button">';
		$result			.= 				'<i class="icon-upload icon-white"></i>';
		$result			.= 				'<span> '.$btnTxt.'...</span>';
		$result			.= 				'<input type="file" data-url="'.$data_url.'" multiple '.$atts.' value="" name="uploader" id="inputUploader" />';
		$result			.= 				'<div id="imageInputs">';
		if(!empty($imagesArray)):
		foreach($imagesArray as $imgId => $imgName):
		$result			.= 					'<input type="hidden" value="'.$imgName.'" class="galleryImg" name="galleryImgs[]" id="closeImg'.$imgId.'">';
		endforeach;
		endif;
		$result			.= 				'</div>';
		$result			.= 			'</span>';
		$result			.= 		'</div>';
		$result			.= 		'<div class="span9" style="position:relative">';
		$result			.= 			'<div class="progress progress-info progress-striped active" id="progress">';
		$result			.= 				'<div class="bar" style=""></div>';
		$result			.= 			'</div>';
		$result			.=			'<span id="upload_success" class="label label-success" style="display:none; position:absolute; top:25px;">Archivos Subidos</span>';
		$result			.=			'<span id="upload_error" class="label label-important" style="display:none; position:absolute; top:25px;">Error al cargar archivo </span>';
		$result			.= 		'</div>';
		$result			.= 	'</div>';
		$result			.= 	'<div id="thumbs_container">';
		if(!empty($imagesArray)):
		foreach($groupedImages as $group => $imgArray):
		
		$result			.= 		'<ul id="thumbsGroup'.$group.'" class="thumbnails">';
		foreach($imgArray as $imgData):
		$result			.= 			'<li style="position:relative;" class="span4">';
		$result 		.= 				'<a style="position:absolute; right:10px;top:3px;" id="closeImg'.$imgData['ID'].'" class="close removegalleryimg" data-toggle="modal" role="button" href="#modalImgRemove">×</a>';
		$result 		.= 				'<div class="thumbnail">';
		$result 		.= 					'<img alt="" src="'.base_url('user_files/uploads').'/'.$imgData['IMAGE'].'">';
		$result 		.= 				'</div>';
		$result 		.= 			'</li>';
		endforeach;
		$result			.= 		'</ul>';
		endforeach;
		endif;
		$result			.= 	'</div>';
		$result			.= '</div>';
		$result			.= '<div id="modalImgRemove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
		$result			.= 	'<div class="modal-header">';
		$result			.= 		'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
		$result			.= 		'<h3 id="myModalLabel">Eliminar Imagen</h3>';
		$result			.= 	'</div>';
		$result			.= 	'<div class="modal-body">';
		$result			.= 		'<p>&#191;Confirma que realmente desea eliminar la imagen seleccionada?</p>';
		$result			.= 	'</div>';
		$result			.= 	'<div class="modal-footer">';
		$result			.= 		'<a class="btn btn-danger" id="deleteconfirm" data-dismiss="modal" aria-hidden="true">Eliminar Imagen</a>';
		$result			.= 		'<a class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</a>';
		$result			.= 	'</div>';
		$result			.= '</div>';
		
		return $result;
	}
}

/**
 * Parse the form attributes
 *
 * Helper function used by some of the form helpers
 *
 * @access	private
 * @param	array
 * @return	array
 */
if ( ! function_exists('_parse_group_images'))
{
	function _parse_group_images($imagesArray)
	{
		$imgGroups = ceil(count($imagesArray) / 3);
		for($i = 0; $i < $imgGroups; $i++):
			$groupedImgs[$i] = array(); 
		endfor;
		foreach($imagesArray as $i => $image):
			$group = floor($i / 3);
			array_push($groupedImgs[$group], array('IMAGE' => $image, 'ID' => $i));
		endforeach;
		
		return $groupedImgs;
	}
}