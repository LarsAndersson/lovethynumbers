<?
class Application {
	
	public $db = '';
	public $files_directory = '';

	public function __construct() {
		include(__DIR__."/../config.php");
		$this->db = $db;
		$this->files_directory = $global_user_files;
	}

	//gets the database defined in the global config file
	public function getDatabase() {
		include(__DIR__."/../config.php");
		return $db;
	}
	
	//gets the database defined in the global config file
	public function getFilesDirectory() {
		include(__DIR__."/../config.php");
		return $global_user_video_folder;
	}
	
	public static function deleteDir($dirPath) {
		print_r($dirPath);
	    if (! is_dir($dirPath)) {
	        throw new InvalidArgumentException("$dirPath must be a directory");
	    }
	    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
	        $dirPath .= '/';
	    }
	    $files = glob($dirPath . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            self::deleteDir($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($dirPath);
	}
}
?>