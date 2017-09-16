<?php
class Content extends Application
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
			$query = $this->db->prepare("SELECT * FROM `content` WHERE `content_id`= :id");
			$query->execute(array(':id' => $id));
			$user = $query->fetch();
			if($user) {
				foreach($user as $n=>$v) {
					$this->$n = $v;
				}
			}
		}
	}
	
	public function addContent($data = array()) {
    	if(!empty($data)) {
	    	$sql = "INSERT INTO `content`(";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'content_id',
				'user_id',
				'content_title',
				'content_image',
				'content_file',
				'content_length',
				'content_xml',
				'content_settings',
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

	public function updateContent($data = array()) {
    	if(!empty($data)) {
	    	$sql = "UPDATE `content` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'content_id',
				'user_id',
				'content_title',
				'content_image',
				'content_file',
				'content_length',
				'content_xml',
				'content_settings',
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
			
			$sql .= " WHERE `video_id`= :video_id"; 
			
			//prepare the query
			$query = $this->db->prepare($sql);
			
			//execute query with all variables
			$query->execute($pdo_array);
		}
		return;
	}
	
	public function getFullName() {
		return $this->user_first . " " . $this->user_last;
	}
	
	public function deleteContent($id) {
		//remove related files
		if (file_exists($this->content_file)) {
   			unlink($this->content_file);
		}
		
		if (file_exists($this->content_image)) {
   			unlink($this->content_image);
		}
		if (file_exists('user_files/'.$this->user_id."/".$this->content_id)) {
			parent::deleteDir('user_files/'.$this->user_id."/".$this->content_id);
		}
		$sql = "DELETE FROM `content` WHERE `content_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		return;
	}
	
	public function getContents() {
		//fetch db from Application for reference
		$videos = $this->db->query("SELECT * FROM `content`")->fetchAll(PDO::FETCH_ASSOC);
		return $videos;
	}
	
	public function getContentByUser($user_id) {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `content` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$user_id));
		$content[0] = $query->fetch();
		return $content;
	}
	
	public function getUserNameById($id) {
		$sql = "SELECT * FROM `user` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$user = $query->fetch();
		return $user['user_first'] . " " . $user['user_last'];
	}
	
	public function getLastId() {
		$sql = "SELECT * FROM `content` ORDER BY `content_id` DESC LIMIT 1";
		$query = $this->db->query($sql)->fetch();
		if(!empty($query)) {
			return $query['content_id'];
		} else {
			return false;
		}
	}
	
	public function getContentUrl() {
		include(__DIR__."/../config.php");
		return 'http://'. $_SERVER['SERVER_NAME'] . $global_site_path .$global_user_video_folder . $this->user_id. "/" . $this->content_id;
	}
	
	public function getViewerPath() {
		include(__DIR__."/../config.php");
		return 'screen?id='.$this->content_id;
	}
	
	public function getFilePath() {
		include(__DIR__."/../config.php");
		return $global_user_video_folder . $this->user_id. "/" . $this->content_id;
	}
	
	public function createContentFolder($path) {
		print_r($path);
	}
	
	public function createContentSettings ($settings) {
		
	} 
	
}