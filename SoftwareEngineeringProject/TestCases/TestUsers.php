<?php

require_once('simpletest/autorun.php');
require_once('../config/configs.php');

require_once('../model/db.php');
require_once('../model/Users.php');
require_once('../model/Exceptions.php');

class TestUsers extends UnitTestCase {
	function TestUsers() {
		//$this->UnitTestCase(' User Test');
	}
	
	function testCreateNewUser(){
		
		ModelUsers::createNewUser("TestUser", "2013", "TestUser", "00000000", "00000000", "TestUser@TestMail.com", "admin");
		
		//second creation have to fail
		$this->expectException("Exception");
		ModelUsers::createNewUser("TestUser", "2013", "TestUser", "00000000", "00000000", "TestUser@TestMail.com", "admin");
	}
	
	function TestLogin(){
		
		$this->assertNotNull(ModelUsers::login("TestUser", "2013"), "Login Failed");
	}
	
	function TestDelete(){
		
		ModelUsers::deleteUser("TestUser");
		$this->assertNull(ModelUsers::login("TestUser", "2013"), "Login Failed");
	}
	
	function ClearDataBase(){
		//TODO Delete the user record
		//Why the hell it isn't working?
		
		$query = "DELETE FROM `Users` WHERE `username` = 'TestUser';";
		
		$connection = Database::createNewConnection();
		
		$connection->query($query);
		
		$this->assertTrue($connection->commit());
		
		$connection->disconnect();
	}
		
};

?>