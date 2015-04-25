<?php

Class ModelUsers{

	public $id;
	public $username;
	public $fullname;
	public $email;
	public $vat;
	public $phone_number;
	public $type;
	public $status;
	
	
	//function which validates the login data
	public static function login($username, $password){	

		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$pass = sha1($password);
		
		$query = "SELECT * FROM Users WHERE `username`=? AND `password`=? AND `status`='active'";	
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('ss', $username, $pass)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($id,$username,$password,$fullname,$vat,$phone_number,$email,$type,$status)){
			throw new Exception('QueryError');
		}
		
		if($count==1){
			$result->fetch();
			$user = new ModelUsers();
			$user->set_info($id, $username, $fullname, $email, $vat, $phone_number, $type, $status);
			return $user;
		}
		else {
			return null;
		}
	}
	
	//function which creates a new user
	public static function createNewUser($username, $password, $fullname, $vat, $phone_number, $email, $type){
		
		$connection = Database::createNewConnection();	
		
		$all_queries_done = true;
		
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if (!$connection->query("LOCK TABLES Users WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Users WHERE `username`=?) ,\"yes\" ,\"no\") AS `answer`";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_param('s', $username)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_result($answer)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$result->fetch();
		if($answer == 'yes'){
			$connection->autocommit(true);
			throw new FullnameAlreadyExists();
		}
		
		$query = "SELECT IF((SELECT COUNT(*) > 0 FROM Users WHERE `email`=?) ,\"yes\" ,\"no\") AS `answer`";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_param('s', $email)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->store_result()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_result($answer)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$result->fetch();
		if($answer == 'yes'){
			$connection->autocommit(true);
			throw new EmailAlreadyExists();
		}
		
		$pass = sha1($password);
		$query = "INSERT INTO Users(`username`,`password`,`fullname`,`vat`,`phone_number`,`email`,`type`)
				VALUES(?,?,?,?,?,?,?)";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->bind_param('sssisss', $username, $pass, $fullname, $vat, $phone_number, $email, $type)){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if(!$result->execute()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		if($all_queries_done == true){
			if (!$connection->commit()){
				$connection->autocommit(true);
				throw new QueryError();
			}
		}
		else{
			if (!$connection->rollback()){
				$connection->autocommit(true);
				throw new QueryError();
			}
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}		
	}
	
	//function which deletes a user
	public static function deleteUser($username){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
	
		$query = "UPDATE Users SET `status`='deleted' WHERE username=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('s', $username)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
	}
	
	//function which edits a user's profile
	public static function editProfile($username, $password, $fullname, $vat, $phone_number, $email){
		
		if($username == ''){
			throw new Exception('Username field is empty');
		}
		
		$all_queries_done = true;
		
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES Users WRITE")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		if($password != ''){
			$pass = sha1($password);
			$query = "UPDATE Users SET `password`=? WHERE `username`=?";
			
			$result = $connection->prepare($query);
			$result->bind_param('ss', $pass, $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if($fullname != ''){
			$query = "UPDATE Users SET `fullname`=? WHERE `username`=?";
			
			$result = $connection->prepare($query);
			$result->bind_param('ss', $fullname, $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if($vat != ''){
			$query = "UPDATE Users SET `vat`=? WHERE `username`=?";
			
			$result = $connection->prepare($query);
			$result->bind_param('is', $vat, $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if($phone_number != ''){
			$query = "UPDATE Users SET `phone_number`=? WHERE `username`=?";
			
			$result = $connection->prepare($query);
			$result->bind_param('ss', $phone_number, $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if($email != ''){
			$query = "UPDATE Users SET `email`=? WHERE `username`=?";
			
			$result = $connection->prepare($query);
			$result->bind_param('ss', $email, $username);
			$result->execute() ? null : $all_queries_done=false;
		}

		
		if($all_queries_done == true){
			if (!$connection->commit()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		else{
			if (!$connection->rollback()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
	}
	
	//function which change the type and the status of a user
	public static function modifyProfile($username, $type, $delete){
		
		if($username == ''){
			throw new Exception('Username field is empty');
		}
		
		$all_queries_done = true;
		
		$connection = Database::createNewConnection();
		
		if(!$connection->autocommit(false)){
			throw new Exception('QueryError');
		}
		
		if (!$connection->query("LOCK TABLES Users WRITE")) {
			$connection->autocommit(true);
			throw new Exception('QueryError');
		}
		
		if($type != ''){
			$query = "UPDATE Users SET `type`=? WHERE `username`=?";
		
			$result = $connection->prepare($query);
			$result->bind_param('ss', $type, $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if(!$delete){
			$query = "UPDATE Users SET `status`='active' WHERE `username`=?";
		
			$result = $connection->prepare($query);
			$result->bind_param('s', $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		else{
			$query = "UPDATE Users SET `status`='deleted' WHERE `username`=?";
		
			$result = $connection->prepare($query);
			$result->bind_param('s', $username);
			$result->execute() ? null : $all_queries_done=false;
		}
		
		if($all_queries_done == true){
			if (!$connection->commit()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		else{
			if (!$connection->rollback()){
				$connection->autocommit(true);
				throw new Exception('QueryError');
			}
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new Exception('QueryError');
		}
	}

	//function which returns all the users(or the active ones if $option="active"
	public static function list_all($option=null){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT * FROM Users";
		if($option == 'active'){
			$query.=" WHERE `status`='active'";
		}
		$query.=" ORDER BY `status` ASC, `username` ASC";
		
		if(!$result = $connection->query($query)){
			throw new Exception('QueryError');
		}

		while($row = $result->fetch_assoc()){
			$user = new ModelUsers();
			$user->set_info($row['id'], $row['username'], $row['fullname'], $row['email'], $row['vat'], $row['phone_number'], $row['type'], $row['status']);
			$users[$row['id']] = $user;
		}
		
		return $users; 
	}
	
	//function which returns the user with id=$id
	public static function get_user($id){
	
		$connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT * FROM Users WHERE `id`=?";
		
		if(!$result = $connection->prepare($query)){
			throw new Exception('QueryError');
		}
		
		if(!$result->bind_param('i', $id)){
			throw new Exception('QueryError');
		}
		
		if(!$result->execute()){
			throw new Exception('QueryError');
		}
		
		if(!$result->store_result()){
			throw new Exception('QueryError');
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($id,$username,$password,$fullname,$vat,$phone_number,$email,$type,$status)){
			throw new Exception('QueryError');
		}
		
		if($count==1){
			$result->fetch();
			$user = new ModelUsers();
			$user->set_info($id, $username, $fullname, $email, $vat, $phone_number, $type, $status);
			return $user;
		}
		else{
			return null;
		}
	}
	
	//function which returns the sellers
	public static function getAllSellers() {
	
        $connection = Database::createNewConnection();
		$connection->autocommit(true);
		
		$query = "SELECT * FROM Users WHERE `type` = 'seller' ORDER BY `status` ";
		
		if(!$result = $connection->query($query)){
			throw new Exception('QueryError');
		}

		while($row = $result->fetch_assoc()){
			$user = new ModelUsers();
			$user->set_info($row['id'], $row['username'], $row['fullname'], $row['email'], $row['vat'], $row['phone_number'], $row['type'], $row['status']);
			$users[$row['id']] = $user;
		}
		
		return $users;
    }

	
	public static function createConfirmationLink($email){
		global $_CONFIG;
		
		$connection = Database::createNewConnection();
	
		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		
		if (!$connection->query("LOCK TABLES Users READ, ConfirmationLink WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		
		$query = "SELECT `id` FROM Users WHERE `email`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->bind_param('s', $email)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->store_result()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($id)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if($count != 0){
			$result->fetch();
			
			$hash = hash_hmac('sha1', 'url is: '.$id, $id);
			$date = date('Y-m-d h:m:s');
			
			$query = "INSERT INTO ConfirmationLink(`url`, `email`, `creation_date`) VALUES(?,?,?) 
					  ON DUPLICATE KEY UPDATE `url`=?, `email`=?, `creation_date`=?";
			
			if(!$result = $connection->prepare($query)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
				
			if(!$result->bind_param('ssssss', $hash, $email, $date, $hash, $email, $date)){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
				
			if(!$result->execute()){
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new QueryError();
			}
			
			$subject = 'Retrieve Email!';
			$message = 'In order to retrieve your new password follow the link 
						 <a href=\'http://projects.codescar.eu/e-Supply/index.php?page=login&action=forgot-password&create=new&key='.$hash;
			$message.= '\'>Retrieve Password</a><br/>Or copy-paste the following url<br/><pre>'. $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] .'?page=login&action=forgot-password&create=new&key='.$hash .'</pre>';
			$headers = 'From: '. $_CONFIG['MAIL_ADDR_SEND'] . "\r\n" .
					   'Reply-To: '. $_CONFIG['MAIL_ADDR_REPLY'] . "\r\n" .
					   'Content-type: text/html'. "\r\n" . 
					   'X-Mailer: PHP/' . phpversion();
			
			if(!mail($email, $subject, $message, $headers)){
				$connection->rollback();
				$connection->autocommit(true);
				$connection->query("UNLOCK TABLES");
				throw new SendEmailFail();
			}		
		}
		else{
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new EmailNotExists();
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}			
	
	}
	
	public static function retrievePassword($validation_link){
		global $_CONFIG;
		
		$connection = Database::createNewConnection();

		if(!$connection->autocommit(false)){
			throw new QueryError();
		}
		

		if (!$connection->query("LOCK TABLES Users WRITE, ConfirmationLink WRITE")) {
			$connection->autocommit(true);
			throw new QueryError();
		}
		
		$date = date('Y-m-d h:m:s');
				  
		$query = "SELECT ConfirmationLink.`email`, TIMESTAMPDIFF(SECOND, ?, ?) AS `date`, `username` 
				  FROM ConfirmationLink, Users WHERE `url`=? AND Users.`email`=ConfirmationLink.`email`";		  
				  
		if(!$result = $connection->prepare($query)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if(!$result->bind_param('sss', $date, $date, $validation_link)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->execute()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->store_result()){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		$count = $result->num_rows;
		
		if(!$result->bind_result($email, $date_difference, $username)){
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}		 

		if($count == 0){
			throw new ValidationLinkNotExists();
		}
		
		$result->fetch();
		if($date_difference > 172800){	
			throw new ValidationPeriodHashExpired();
		}
		
		$query = "DELETE FROM ConfirmationLink WHERE `url`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->bind_param('s', $validation_link)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->execute()){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
	
		$pass = array();
		
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789-#_!+";
		$alphaLength = strlen($alphabet) - 1;
		
		for ($i = 0; $i < 10; $i++) {	
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		$password = implode($pass);
		
		
		$subject = 'New Password';
		$message = 'Your username is: <b>'.$username.'</b> and your new password is: <b>'.$password.'</b>';
		$message.= '<br/>Login <a href=\'http://projects.codescar.eu/e-Supply/index.php?page=login\'>here</a>.';
		$headers = 'From: '. $_CONFIG['MAIL_ADDR_SEND'] . "\r\n" .
				   'Reply-To: '. $_CONFIG['MAIL_ADDR_REPLY'] . "\r\n" .
				   'Content-type: text/html'. "\r\n" . 
				   'X-Mailer: PHP/' . phpversion();

		if(!mail($email, $subject, $message, $headers)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new SendEmailFail();
		}
		
		$password = sha1($password);
		
		$query = "UPDATE Users SET `password`=? WHERE `email`=?";
		
		if(!$result = $connection->prepare($query)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->bind_param('ss', $password, $email)){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
			
		if(!$result->execute()){
			$connection->rollback();
			$connection->autocommit(true);
			$connection->query("UNLOCK TABLES");
			throw new QueryError();
		}
		
		if (!$connection->commit()){
			$connection->autocommit(true);
			throw new QueryError();
		}
		$connection->autocommit(true);
		
		if (!$connection->query("UNLOCK TABLES")) {
			throw new QueryError();
		}	

	}
	
	public function set_info($id, $username, $fullname, $email, $vat, $phone_number, $type, $status){
		$this->id = $id;
		$this->username = $username;
		$this->fullname = $fullname;
		$this->email = $email;
		$this->vat = $vat;
		$this->phone_number = $phone_number;
		$this->type = $type;
		$this->status = $status;
	}
}

?>