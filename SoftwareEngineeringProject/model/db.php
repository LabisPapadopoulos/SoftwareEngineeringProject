<?php

class Database extends mysqli{

		const DBserver   = "db20.grserver.gr";
		const DBusername = "software";
		const DBpassword = "engineering";
		const DBname     = "kostis_SoftEngin";

		private static $database = null;	

        private function __construct(){

		
            parent::__construct(self::DBserver, self::DBusername, self::DBpassword, self::DBname);
			
            if ($this->connect_error) {
				die('Connect Error (' . $this->connect_errno . ') '. $this->connect_error);
			}
			$this->set_charset("utf8");
        }
		
        public static function createNewConnection(){
		
            if(!self::$database){
                self::$database = new Database();
            }
            return self::$database;
        }
		
		public static function disconnect() {
		
			if(self::$database != null){
				self::$database->mysqli_close();
			}
			self::$database = null; 
		}
		
        public function __clone() {
		
            die(__CLASS__ . ' class can\'t be instantiated. Please use the method called createNewConnection.');
        }
}

?>