<?php 
class Cms_login_model extends MY_Model{
					
	function __construct() {
		parent::__construct();
		$this->set_table("FRAMEWORK_USERS");
	}
        /**
         * Comprueba al usuario y le da acceso o no a la sesión
         *
         * @param type $data[USERNAME] y $data[PASSWORD] 
         * @return boolean TRUE, FALSE
         */
	function comprobacion($data){
		
		$usuario 		= $data['USERNAME'];
		$pass 			= $data['PASSWORD'];
		
		$this->db->select('COUNT(1) AS total');
		$this->db->from($this->_table);
		$this->db->where('USERNAME', $usuario);
		$this->db->where('PASSWORD', md5($pass));
		$query = $this->db->get();
		$row = $query->row();
		
		if($row->total > 0):
		return TRUE;
		else:
		return FALSE;
		endif;
	}
        /**
         * Obtiene los datos del usuario ingresado a la sesión
         * 
         * @param type $data[USERNAME]
         * @return object type con los datos del usuario 
         */
	function datos($data){
            
		//Obtener datos del usuario registrado
		$this->db->where('USERNAME', $data['USERNAME']);		
		$query = $this->db->get($this->_table);
		
		return $query->row();
	}
        /**
         * Validar el acceso del usuario
         */
        public function user_access_validation($class_area){
            //Comprobar el inicio de sesión
            if($this->session->userdata('logged_in') == TRUE):
                
                //Get the necessary access to the area
                $this->db->select('FLAG_SECURE');
                $this->db->from('CMS_PAGES');
                $this->db->where('LIST_CATEGORIES', '2');
                $this->db->where('PATH', $class_area);
                $sql = $this->db->get();
                $result = $sql->row();
                
                $necessary_access = $result->FLAG_SECURE;
                //Get the user access
                $user_access = $this->session->userdata('ACCESS_LEVEL');
                
                $allow_access['PERMITTED'] = ($user_access >= $necessary_access)? TRUE: FALSE;
            else:
                $allow_access['PERMITTED'] = FALSE;
            endif;
            
            return $allow_access;
        }
}