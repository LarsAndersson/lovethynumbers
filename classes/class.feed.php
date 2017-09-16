<?php
class Feed extends Application
{
	
	public $db;
	public $files_directory;
	public $table_name;
		
	public function __construct($id = 0)
	{
		//set the files directory
		$this->files_directory = parent::getFilesDirectory();

		//fetch db from Application for reference
		$this->db = parent::getDatabase();
		
		if($id != 0) {
			$query = $this->db->prepare("SELECT * FROM `feed` WHERE `feed_id`= :id");
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
	    	$sql = "INSERT INTO `feed`(";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'feed_id',
				'feed_type_id',
				'feed_title',
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
	    	$sql = "UPDATE `feed` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'feed_id',
				'feed_type_id',
				'feed_title',
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
			
			$sql .= " WHERE `feed_id`= :feed_id"; 
			
			//prepare the query
			$query = $this->db->prepare($sql);
			
			//execute query with all variables
			$query->execute($pdo_array);
		}
		return;
	}
	
	public function delete($id) {
		$sql = "DELETE FROM `feed` WHERE `feed_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		return;
	}
	
	public function uploadFile($file, $feed_data) {
		if ($file['feed_file']['error'] == UPLOAD_ERR_OK               //checks for errors
		&& is_uploaded_file($file['feed_file']['tmp_name'])) { //checks that file is uploaded
			$this->table_name = $feed_data['user_id'] . '_' . $feed_data['feed_id'] . '_feed';
			$data_lines = array_map('str_getcsv', file($file['feed_file']['tmp_name']));
			$count = 0;
			$headers = array();
			//handle each line of data
			foreach($data_lines as $data) {
				//if first line, then this determines table structure
				if($count == 0) {
					$headers = $data;
					$this->createEmptyTable($data);
				} else {
					$this->insertData($data, $headers);
				}
				$count++;
			}
		}
	}
	
	public function createEmptyTable($headings) {
		$this->db->query('DROP TABLE IF EXISTS '.$this->table_name);
		$sql = 'CREATE TABLE '.$this->table_name.' (feed_table_id INT(11) AUTO_INCREMENT, ';
		foreach ($headings as $heading) {
			$sql .= "`" . $heading . "` BLOB, ";
		}
		$sql .= 'PRIMARY KEY (feed_table_id))';
		$query = $this->db->query($sql);
	}
	
	public function insertData($data, $headers) {
		$sql = 'INSERT INTO ' . $this->table_name.' SET ';
		$values = array();
		foreach ($data as $key => $value) {
			$values[] = '`' . $headers[$key] . "`='" . $value . "'";
		}
		$sql .= implode(',', $values);
		$query = $this->db->query($sql);
	}
	
	public function getHeadings($table) {
		$sql = "SELECT `COLUMN_NAME` 
				FROM `INFORMATION_SCHEMA`.`COLUMNS` 
				WHERE `TABLE_SCHEMA`='arcent_lovethynumbers' 
    			AND `TABLE_NAME`='".$table."'
    			AND `COLUMN_NAME` != 'feed_table_id'
    			";
				
		$query = $this->db->query($sql);
		//fetch the actual name from return data
		foreach($query->rows as $result) {
			$return_data[] = $result['COLUMN_NAME'];
		}
		return $return_data;
	}
	
	public function getFeeds() {
		//fetch db from Application for reference
		$data = $this->db->query("SELECT * FROM `feed`")->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getFeedTypes() {
		//fetch db from Application for reference
		$data = $this->db->query("SELECT * FROM `feed_type`")->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getFeedType($id) {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `feed_type` WHERE `feed_type_id`=:feed_type_id";
		$query = $this->db->prepare($sql);
		$data = $query->execute(array('feed_type_id'=>$id));
		$data = $query->fetch();
		return $data;
	}
	
	public function getFeedsByUser($id) {
		$sql = "SELECT * FROM `feed` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$data = $query->fetchAll();
		return $data;
	}
	
	public function setFeedTableName($user_id, $feed_id) {
		$this->table_name = $user_id.'_'.$feed_id.'_feed';
	}
	
	public function getLastId() {
		$sql = "SELECT * FROM `feed` ORDER BY `feed_id` DESC LIMIT 1";
		$query = $this->db->query($sql)->fetch();
		if(!empty($query)) {
			return $query['feed_id'];
		} else {
			return false;
		}
	}
}