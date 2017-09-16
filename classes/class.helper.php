<?php
	class Helper extends Application {
		public function getNumberOfCustomers() {
			$rows = parent::getDatabase()->query('SELECT COUNT(*) FROM `user`')->fetchColumn();
			return $rows;
		}
		
		public function getNumberOfVideos() {
			$rows = parent::getDatabase()->query('SELECT COUNT(*) FROM `video`')->fetchColumn();
			return $rows;
		}
		
		public function recurseCopy($src,$dst) { 
		    $dir = opendir($src); 
		    @mkdir($dst); 
		    while(false !== ( $file = readdir($dir)) ) { 
		        if (( $file != '.' ) && ( $file != '..' )) { 
		            if ( is_dir($src . '/' . $file) ) { 
		                $this->recurseCopy($src . '/' . $file,$dst . '/' . $file); 
		            } 
		            else { 
		                copy($src . '/' . $file,$dst . '/' . $file); 
		            } 
		        } 
		    } 
		    closedir($dir); 
		} 
	}
