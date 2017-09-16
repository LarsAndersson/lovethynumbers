<?php
class User extends Application
{
	
	public $db;
	public $files_directory;
	
	public function __construct($email)
	{
		//set the files directory
		$this->files_directory = parent::getFilesDirectory();

		//fetch db from Application for reference
		$this->db = parent::getDatabase();
		
		$query = $this->db->prepare("SELECT * FROM `user` WHERE `user_email`= :email OR `user_id`= :email");
		$query->execute(array(':email' => $email));
		$user = $query->fetch();
		if($user) {
			foreach($user as $n=>$v) {
				$this->$n = $v;
			}
		}
	}
	
	public function addUser($data = array()) {
    	if(!empty($data)) {
	    	$sql = "INSERT INTO `user`(";
			$fields = array();
			
			//list of fields in the table
			$clearing_fields = array(
				'user_level',
				'user_first',
				'user_last',
				'user_company',
				'user_city',
				'user_zip',
				'user_email',
				'user_phone',
				'user_fax',
				'user_active',
				'user_note',
				'user_image',
				'user_account',
				'user_password',
				'user_vat_number',
			);
			
			foreach($clearing_fields as $field) {
				if($field != 'user_password') {
					$fields[$field] = $data[$field];
				} else {
					$fields[$field] = md5($data[$field]);
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
			
			//make directory for the user to store videos
			if (!file_exists($this->files_directory.$this->db->lastInsertId())) {
    			mkdir($this->files_directory.$this->db->lastInsertId(), 0777, true);
			}
			
		}
		return;
	}

	public function updateUser($data = array()) {
    	if(!empty($data)) {
	    	$sql = "UPDATE `user` SET";
			$fields = array();
			
			//list of fields in the table
			$table_fields = array(
				'user_id',
				'user_level',
				'user_first',
				'user_last',
				'user_company',
				'user_city',
				'user_email',
				'user_zip',
				'user_phone',
				'user_fax',
				'user_active',
				'user_note',
				'user_image',
				'user_account',
				'user_vat_number',
				'user_password'
			);
			
			foreach($table_fields as $field) {
				if(isset($data[$field])) {
					if($field != 'user_password') {
						$fields[$field] = $data[$field];
					} else {
						$fields[$field] = md5($data[$field]);
					}
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
			
			$sql .= " WHERE `user_id`= :user_id"; 
			
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
	
	public function deleteUser($id) {
		$sql = "DELETE FROM `user` WHERE `user_id`=:id";
		$query = $this->db->prepare($sql);
		$query->execute(array('id'=>$id));
		
		//remove user directory
		if (file_exists($this->files_directory.$id)) {
   			parent::deleteDir($this->files_directory.$id);
		}
		
		return;
	}
	
	public function getUsers() {
		//fetch db from Application for reference
		$user = array();
		$user = $this->db->query("SELECT * FROM `user`")->fetchAll(PDO::FETCH_ASSOC);
		return $user;
	}
	
	public function getUserNameById($id) {
		$sql = "SELECT * FROM `user` WHERE `user_id`=:user_id";
		$query = $this->db->prepare($sql);
		$query->execute(array('user_id'=>$id));
		$user = $query->fetch();
		return $user['user_first'] . " " . $user['user_last'];
	}
}