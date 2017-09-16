<?php
class Video extends Application
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
			$query = $this->db->prepare("SELECT * FROM `video` WHERE `video_id`= :id");
			$query->execute(array(':id' => $id));
			$user = $query->fetch();
			if($user) {
				foreach($user as $n=>$v) {
					$this->$n = $v;
				}
			}
		}
	}
	
	public function addVideo($data = array()) {
    	if(!empty($data)) {
	    	$sql = "INSERT INTO `video`(";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'video_id',
				'user_id',
				'video_title',
				'video_image',
				'video_file',
				'video_length',
				'video_xml',
				'video_settings',
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

	public function updateVideo($data = array()) {
    	if(!empty($data)) {
	    	$sql = "UPDATE `video` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'video_id',
				'user_id',
				'video_title',
				'video_image',
				'video_file',
				'video_length',
				'video_xml',
				'video_settings',
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
	
	public function deleteVideo($id) {
		//remove related files
		if (file_exists($this->video_file)) {
   			unlink($this->video_file);
		}
		
		if (file_exists($this->video_image)) {
   			unlink($this->video_image);
		}
		
		parent::deleteDir('user_files/'.$this->user_id."/".$this->video_id);
		
		$sql = "DELETE FROM `video` WHERE `video_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		return;
	}
	
	public function getVideos() {
		//fetch db from Application for reference
		$videos = $this->db->query("SELECT * FROM `video`")->fetchAll(PDO::FETCH_ASSOC);
		return $videos;
	}
	
	public function getVideosByUser($user_id) {
		//fetch db from Application for reference
		$sql = "SELECT * FROM `video` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$user_id));
		$videos[0] = $query->fetch();
		return $videos;
	}
	
	public function getUserNameById($id) {
		$sql = "SELECT * FROM `user` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$user = $query->fetch();
		return $user['user_first'] . " " . $user['user_last'];
	}
	
	public function getLastId() {
		$sql = "SELECT * FROM `video` ORDER BY `video_id` DESC LIMIT 1";
		$query = $this->db->query($sql)->fetch();
		return $query;
	}
	
	public function getVideoUrl() {
		include(__DIR__."/../config.php");
		return 'http://'. $_SERVER['SERVER_NAME'] . $global_site_path .$global_user_video_folder . $this->user_id. "/" . $this->video_id;
	}
	
	public function getFilePath() {
		include(__DIR__."/../config.php");
		return $global_user_video_folder . $this->user_id. "/" . $this->video_id;
	}
	
	public function createVideoFolder($path) {
		print_r($path);
	}
	
	public function createVideoSettings ($settings) {
		
	} 
	
}