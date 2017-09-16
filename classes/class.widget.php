<?php
class Widget extends Application
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
			$query = $this->db->prepare("SELECT * FROM `widget` WHERE `widget_id`= :id");
			$query->execute(array(':id' => $id));
			$user = $query->fetch();
			if($user) {
				foreach($user as $n=>$v) {
					$this->$n = $v;
				}
			}
		}
	}
	
	public function add($data = array()) {
    	if(!empty($data)) {
	    	$sql = "INSERT INTO `widget`(";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'widget_id',
				'widget_type_id',
				'widget_title',
				'widget_type_id',
				'user_id'
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

	public function update($data = array()) {
    	if(!empty($data)) {
	    	$sql = "UPDATE `widget` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'widget_id',
				'widget_type_id',
				'widget_title',
				'feed_id',
				'user_id'
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
			
			$sql .= " WHERE `widget_id`= :widget_id"; 
			
			//prepare the query
			$query = $this->db->prepare($sql);
			
			//execute query with all variables
			$query->execute($pdo_array);
		}
		return;
	}
	
	public function delete($id) {
		$sql = "DELETE FROM `widget` WHERE `widget_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		return;
	}
	
	
	public function getWidgets() {
		//fetch db from Application for reference
		$data = $this->db->query("SELECT * FROM `widget`")->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getWidgetTypes() {
		//fetch db from Application for reference
		$data = $this->db->query("SELECT * FROM `widget_type`")->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getFeeds() {
		//fetch db from Application for reference
		$data = $this->db->query("SELECT * FROM `feed`")->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getFeedsByUser($id) {
		$sql = "SELECT * FROM `feed` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$data = $query->fetchAll();
		return $data;
	}
	
	public function getWidgetsByUser($id) {
		$sql = "SELECT * FROM `widget` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$data = $query->fetchAll();
		return $data;
	}
	
	public function getLastId() {
		$sql = "SELECT * FROM `widget` ORDER BY `widget_id` DESC LIMIT 1";
		$query = $this->db->query($sql)->fetch();
		if(!empty($query)) {
			return $query['widget'];
		} else {
			return false;
		}
	}
}