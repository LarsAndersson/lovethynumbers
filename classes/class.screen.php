<?php
class Screen extends Application
{
	
	public $db;
	public $files_directory;
		
	public function __construct($id = 0)
	{
		//set the files directory
		$this->files_directory = parent::getFilesDirectory();

		//fetch db from Application for reference
		$this->db = parent::getDatabase();
		
		if($id != 0) {
			$query = $this->db->prepare("SELECT * FROM `screen` WHERE `screen_id`= :id");
			$query->execute(array(':id' => $id));
			$user = $query->fetch();
			if($user) {
				foreach($user as $n=>$v) {
					$this->$n = $v;
				}
			}
		}
	}
	
	public function addScreen($data = array()) {
    	if(!empty($data)) {
	    	$sql = "INSERT INTO `screen`(";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'screen_id',
				'screen_title',
				'user_id',
				'content_id',
				'content_last_updated',
				'screen_reference_id',
			);
			
			foreach($table_fields as $field) {
				if(isset($data[$field])) {
					$fields[$field] = $data[$field];
				}
			}
			
			//map the fields to the right area of sql query
			foreach($fields as $field_name=>$value) {
				$names[] = $field_name;
				$values[] = $value;
			}
	    	
			//build the columns list to update
			$i = 0;
			$len = count($names);
			foreach($names as $name) {
				$sql .= '`'.$name.'`';
				if ($i != $len - 1) {
			        $sql .= ", ";
			    } else {
			    	$sql .= ") VALUES (";	
				}
				$i++;
			}
			
			//build the values list to update
			$i = 0;
			$len = count($values);
			foreach($names as $name) {
				$sql .= ":". $name;
				if ($i != $len - 1) {
			        $sql .= ", ";
			    } else {
			    	$sql .= ")";	
				}
				$i++;
			}

			//set up all the pdo prepare statements
			$pdo_array = array();
			foreach($fields as $field_name=>$value) {
				$pdo_array[':'.$field_name] = $value;
			}
			
			//prepare the query
			$query = $this->db->prepare($sql);
			
			//execute query with all variables
			$query->execute($pdo_array);
		}
		return;
	}

	public function updateScreen($data = array()) {
    	if(!empty($data)) {
	    	$sql = "UPDATE `screen` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'screen_id',
				'screen_title',
				'user_id',
				'content_id',
				'content_last_updated',
				'screen_reference_id',
			);
			
			foreach($table_fields as $field) {
				if(isset($data[$field])) {
						$fields[$field] = $data[$field];
				}
			}
			
			//map the fields to the right area of sql query
			foreach($fields as $field_name=>$value) {
				$sql .= ' `' . $field_name . '`=:' . $field_name .',';
			}
			
			//set up all the pdo prepare statements
			$pdo_array = array();
			foreach($fields as $field_name=>$value) {
				$pdo_array[':'.$field_name] = $value;
			}
			
			//strip comman from last entry
			$sql = rtrim($sql, ",");
			
			$sql .= " WHERE `screen_id`= :screen_id"; 
			
			//prepare the query
			$query = $this->db->prepare($sql);
			
			//execute query with all variables
			$query->execute($pdo_array);
		}
		return;
	}
	
	public function deleteScreen($id) {
		//remove related files
		$sql = "DELETE FROM `screen` WHERE `screen_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		return;
	}
	
	public function getScreens() {
		//fetch db from Application for reference
		$videos = $this->db->query("SELECT * FROM `screen`")->fetchAll(PDO::FETCH_ASSOC);
		return $videos;
	}
	
	public function getScreensByUser($user_id) {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `screen` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$user_id));
		$videos[0] = $query->fetch();
		return $videos;
	}
	
	public function getScreenByRef($ref) {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `screen` WHERE `screen_reference_id`=:ref";
		$query = $this->db->prepare($sql);
		$query->execute(array('ref'=>$ref));
		$screen[0] = $query->fetch();
		return $screen[0];
	}
	
	public function getThisContent() {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `content` WHERE `content_id`=:content_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('content_id'=>$this->content_id));
		$content[0] = $query->fetch();
		if(!empty($content[0])) {
			return $content[0];
		} else {
			return false;
		}
	}
	
	public function getLastId() {
		$sql = "SELECT * FROM `screen` ORDER BY `screen_id` DESC LIMIT 1";
		$query = $this->db->query($sql)->fetch();
		if(!empty($query)) {
			return $query['screen_id'];
		} else {
			return false;
		}
	}
	
	public function getViewerPath() {
		if(!empty($this->screen_reference_id)) {
			return 'view?id='.$this->screen_reference_id;
		} else {
			return false;
		}
		
	}
	
}