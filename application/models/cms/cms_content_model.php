<?php
/**
 * 
 */
class Cms_content_model extends MY_Model{
	
	function __construct() {
		parent::__construct();
		$this->set_table("FRAMEWORK_POSTS");
	}
	
	public function post_page_number($page_class){
		$this->db->select('ID, LABEL');
		$this->db->from('FRAMEWORK_PAGES');
		$this->db->where('CLASS', $page_class);
		$query = $this->db->get();
		
		return $query->row();
	}
	
	public function post_content($page_id, $page_key){
		$this->db->select('ID, POST_TITLE, POST_DETAIL');
		$this->db->from($this->_table);
//		$this->db->where('POST_PAGE', $page_id);
		$this->db->where('POST_KEY', $page_key);
		$this->db->limit(1);
		$query = $this->db->get();	
		
		return $query->row();
	}
}
