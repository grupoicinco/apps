<?php
/**
 * Información de imágenes editables
 */
class Cms_plugin_images_model extends MY_Model {
	
	function __construct() {
		parent::__construct();
		
		$this->_table 		= "FRAMEWORK_IMAGES";
		$this->upload_path	= "./library/cms/uploads/images/"; //Este directorio si se cambia, debe cambiarse en el controlador también.
	}
	
	/**
	 * Elimina las imágenes extra de la base de datos
	 * @param 	array 			$separated_images			Array con las imagenes a eliminar de la db
	 */
	public function delete_removed_images($separated_images){
		$this->db->where_in('IMAGE_FILE', $separated_images);
		$query = $this->db->get($this->_table);
		
		$images_to_remove = $query->result();
		
		foreach($images_to_remove as $remove):
			$this->delete($remove->ID);
		endforeach;
	}
	public function get_pages_info(){
		
		//Get the pages
		$this->db->from('FRAMEWORK_PAGES');
		$this->db->order_by('ID', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}
	public function display_images_categorized(){
		//GET THE PAGES
		$pages_list_object = $this->get_pages_info();
		
		$categorized_images_array = array();
		foreach($pages_list_object as $page):
			
			//Get the images
			$this->db->from($this->_table);
			$this->db->where('IMAGE_PAGE', $page->ID);
			$query = $this->db->get();
			$images_list_object = $query->result();
			
			if(!empty($images_list_object)):
				$categorized_images_array[$page->ID]['PAGE'] 	= $page->LABEL;
				$categorized_images_array[$page->ID]['IMAGES']	= $images_list_object;
			endif;
		endforeach;
		
		return $categorized_images_array;
	}
	/**
	 * Editar información de la imagen
	 */
	 public function edit_image_data($image){
	 	
		//Si se ha subido una imagen
		$this->db->where('ID', $image);
		$query = $this->db->get($this->_table);
		$image_data = $query->row();
		
		$config['upload_path'] 		= $this->upload_path;
		$config['allowed_types']	= 'gif|jpg|png';
		$this->upload->initialize($config);
		
		$this->upload->do_upload('IMAGE_FILE');
		$uploaded_data = $this->upload->data();
		
		//Obtener los datos a actualizar
		$data = array(
//				'IMAGE_NAME' 	=> $this->input->post('IMAGE_NAME'),
				'IMAGE_PAGE' 	=> $this->input->post('IMAGE_PAGE')
		);
		//Si se cargo nueva imagen, se actualiza
		if(!empty($uploaded_data['file_name'])):
			unlink($this->upload_path.$image_data->IMAGE_FILE);
			$data['IMAGE_FILE'] = $uploaded_data['file_name'];
		endif;
		
		$this->db->where('ID', $image);
	 	return $this->db->update($this->_table,$data);
	 }
	 
	 //Despliega la ruta de la image para desplegarla
	 public function display_image_route(){
	 	return str_replace('./', '/', $this->upload_path);
	 }
	 //Desplegar archivo de la imagen
	 public function image_file_display($imgid){
	 	$query = $this->db->select('IMAGE_FILE')
		->from($this->_table)
		->where('ID', $imgid)->limit(1)->get();
		$imagename = $query->row();
		
		return $imagename->IMAGE_FILE;
	 }
}
