<?php

	/**
	 * Modelo CRUD para utilización en framework
         * @author Guido Orellana <guido@grupoperinola.com>
         * @name Modelo CRUD (MY_Model)
	 * @since nov 5, 2012
	 */ 

	class MY_Model extends CI_Model{
			
		// Contiene el nombre de la tabla que representa el modelo
		protected $_table;
				
		// Establece el nombre de la tabla
		public function set_table($table_name) {
			$this->_table = $table_name;
		}
                /**
                 * función que obtiene datos de una columna de una fila específica, por default se obtiene la primera fila
                 * 
                 * @param type $data, envía la información a ser requerida:
                 *              $data[COLUMN_SELECT] = nombre de la columna a seleccionar, por default son todas (*)
                 *              $data[COLUMN_GET] = nombre de la columna a requerir, por default es ID
                 *              $data[COLUMN_VAR] = identificador o variable a requerir para obtener datos.
                 *              $data[ROW_ORDER] = orden de las columnas; ASC o DESC, para obtener primero o último dato.
                 *              $data[TABLE] = tabla a requerir la fila
                 */
                public function get_single_row($data){
                    //Set the defaults
                    $data['COLUMN_SELECT'] 	= (isset($data['COLUMN_SELECT']))? $data['COLUMN_SELECT']: '*';
                    $data['COLUMN_GET'] 	= (isset($data['COLUMN_GET']))? $data['COLUMN_GET']: 'ID';
                    $data['ROW_ORDER'] 		= (isset($data['ROW_ORDER']))? $data['ROW_ORDER']: 'ASC';
					$table					= (isset($data['TABLE']))? $data['TABLE']: $this->_table;
                    
                    $this->db->select($data['COLUMN_SELECT']);
                    $this->db->from($table);
                    
                    if(isset($data['COLUMN_VAR'])):
                    $this->db->where($data['COLUMN_GET'], $data['COLUMN_VAR']);
                    else:
                    $this->db->order_by($data['COLUMN_GET'], $data['ROW_ORDER']);
                    endif;
                    $sql = $this->db->get();
                    
                    return $sql->row();
                }
		
		/* Inserta un nuevo registro en la tabla
		 * $data - arreglo asociativo con las columnas => valores a insertar
		 * $tabla - nombre de una tabla diferente a la del modelo
		 */
		public function insert($data, $tabla = NULL) {
			//Insertar en una tabla diferente
			if($tabla == NULL):
				$table = $this->_table;
			else:
				$table = $tabla;
			endif;
			//Insertar en la tabla			
			$this->db->insert($table, $data);
			return $this->db->insert_id();
		}
		
		/* Actualiza un registro de la tabla
		 * $data - arrego asosciativo con columnas => nuevos valores
		 * $id - id del registro que se quiere actualizar
		 */
		public function update($data, $id = NULL, $where = NULL) {
			
			if(is_null($where)):$this->db->where('ID', $id);else:$this->db->where($where);endif;			
			return $this->db->update($this->_table, $data);
			
		}
		
		/* Elimina un registro de la tabla
		 * $id - id del registro que se quiere eliminar
		 */
		public function delete($id) {
			
			$this->db->where('id', $id);
			$this->db->delete($this->_table);
			
		}
		
		/*
		 * Devuelve el total de filas de la tabla, opcionalmente, se puede enviar
		 * un string con el 'where' para contar solo las filas que coinciden
		 */
		public function total_rows($where = '') {
			
			if ($where != '') :
				
				$this->db->select('COUNT(1) AS total');
				$this->db->from($this->_table);
				$this->db->where($where);
				
				$query = $this->db->get();
				$row = $query->row();
				return $row->total; 

				
			else :
				
				return $this->db->count_all($this->_table);
				
			endif;
			
		}
		
		/**
		 * Devuelve un conjunto de filas de la tabla
		 * @param $columns - string con los nombres de las columnas que se quieren obtener, separadas por comas
		 * @param $where - condiciones para aplicar a la consulta
		 * @param $limit - cantidad de rows que se quieren obtener
		 * @param $offset - # de fila inicial que se quiere obtener
		 * @param $orderby - orden de los resultados
		 * @param $table - especificar tabla a requerir datos 
		 */
		public function list_rows($columns = '', $where = '', $limit = NULL, $offset = NULL, $orderby = '', $table = NULL) {
			
			$table = ($table != NULL)? $table: $this->_table;
			
			if ($columns != '') :
				$this->db->select($columns);
			endif;
			
			$this->db->from($table);
			
			if ($where != '') :
				$this->db->where($where, NULL, FALSE);
			endif;
			
			if($orderby != ''):
				$this->db->order_by($orderby); 
			endif;
			
			if ($limit != NULL) :
				
				if ($offset != NULL) :
				
					$this->db->limit($limit, $offset);
					
				else :
					
					$this->db->limit($limit);
				endif; 
				
			endif;
			
			$query = $this->db->get();
			
			return $query->result();
		}

		/**
		 * Retorna una fila de la tabla en un objeto cuyas propiedades son las columnas
		 * 
		 * @var $id - el id en la tabla
		 * @var $format 	- el formato en el que se quiere obtener el objecto:
		 * 			- 'object' - retorna el objeto que devuelve CI->db->get()->row()
		 *      	- 'json'   - retorna el mismo objeto codificado en JSON con json_encode
		 *      	- 'xml'    - retorna el mismo objecto codificado como XML (el elemento raÃ­z se llama 'root')
		 * 
		 * Si el ID que se env�a no existe en la tabla, retorna FALSE
		 * 
		 */
		public function get($id, $format = 'object') {
				
			$this->db->where('id', $id);						
			$query = $this->db->get($this->_table);
			
			#verificamos si existe el id
			if ($query->num_rows > 0)  {
				
				$object =  $query->row();
				
				return $this->_format_object($object, $format);
				
			}
			
			#si no existe, devolvemos FALSE
			else {
				return FALSE;
			}
		
		}
		
		protected function _toXml($xmlobj, $array, $prefix = '') {
			
			foreach($array as $key => $value) {
				
				if (is_array($value) || is_object($value)) {
					
					if (is_numeric($key)) {
						
						$child = $xmlobj->addChild($prefix . $key);
						$this->_toXml($child, $value);
						
					}
					
					else {
					
						$child = $xmlobj->addChild($key);
						$this->_toXml($child, $value, $key);
						
					}
					
				}
				
				else {
					
					$xmlobj->addChild($prefix . $key, $value);
					
				}
				
			}
			
		}
		
		protected function _xml_encode($object) {
			
			#Creamos la raiz del xml
			$xml = new SimpleXMLElement('<root />');
			
			$this->_toXml($xml, $object);
			return utf8_encode(html_entity_decode($xml->asXML()));
			
			
		}
		

		protected function _format_object($object, $format = 'object'){
			switch ($format) {
					
					case 'json' :
						return json_encode($object);
						break;
					case 'xml' :
						$xml =  $this->_xml_encode($object);
						return $xml;
						break;
					default:
						return $object;
						break;
				}
			
		}
		
	}