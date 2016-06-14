<?php 
class Cms_header_model extends MY_Model{
					
	function __construct() {
		parent::__construct();
		$this->set_table("FRAMEWORK_RESOURCE");
	}
	
	public function get_menu_pages(){
		//Select the pages
		$this->db->select('ID, LABEL, PATH, FLAG_SECURE');
		$this->db->from('CMS_PAGES');
		$this->db->where('LIST_CATEGORIES >', 1);
		$this->db->where('PAGE_PARENT <', 0);
		$this->db->order_by('LIST_CATEGORIES', 'ASC');
		$this->db->order_by('PAGE_ORDER', 'ASC');
		$query = $this->db->get();
		
		$parents = $query->result();
		
		foreach($parents as $parent):
			$this->db->select('ID, LABEL, PATH');
			$this->db->where('PAGE_PARENT',$parent->ID);
			$this->db->order_by('PAGE_ORDER', 'ASC');
			$query = $this->db->get('CMS_PAGES');
			
			$childs = $query->result();
			
			$parent->CHILDS = $childs;
		endforeach;
		
		return $parents;
	}
	
}