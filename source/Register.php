<?php

namespace AnkitJain\RegistrationModule;
use AnkitJain\RegistrationModule\Validate;
use AnkitJain\RegistrationModule\Session;
require_once (dirname(__DIR__) . '/config/database.php');

class Register
{
	protected $error;
	protected $key;
	protected $obValidate;
	protected $connect;

	function __construct()
	{
		$this->error = array();
		$this->key = 0;
		$this->connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$this->obValidate = new Validate();

	}

	function authRegister($name, $email, $username, $password, $mob)
	{
		$name = trim($name);
		$email = trim($email);
		$username = trim($username);
		$password = trim($password);
		$mob = trim($mob);
		if (empty($name)) {
			$this->onError(["name" => " *Enter the name"]);
		}

		if(empty($email)) {
			$this->onError(["email" => " *Enter the email address"]);
		}
		elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
			$this->onError(["email" => " *Enter correct Email address"]);
		}
		else
		{
			if($this->obValidate->validateEmailInDb($email) === 1)
			{

				$this->onError(["email" => " *Email is already registered"]);
			}
		}

		if(empty($username)) {
			$this->onError(["username" => " *Enter the username"]);
		}
		else
		{
			if($this->obValidate->validateUsernameInDb($username) === 1)
			{

				$this->onError(["username" => " *Username is already registered"]);
			}
		}

		if(empty($password)) {
			$this->onError(["password" => " *Enter the password"]);
		}

		if(empty($mob)) {
			$this->onError(["mob" => " *Enter the Mobile Number"]);
		}
		elseif (!preg_match("/^[0-9]{10}$/", $mob)) {
			$this->onError(["mob" => " *Enter correct Mobile Number"]);
		}

		if($this->key == 1)
		{
			return json_encode($this->error);
		}
		else
		{
			$this->key = 0;
			$pass = md5($password);
			$query = "INSERT INTO register VALUES(null, '$email', '$username', '$pass')";
			if(!$this->connect->query($query)) {
				$this->key = 1;
				echo "You are not registered || Error in registration2";
			}
			else
			{
				$query = "SELECT id FROM register WHERE email = '$email'";
				if($result = $this->connect->query($query)) {
					$row = $result->fetch_assoc();
					$UserId = $row['id'];

					$query = "INSERT INTO login VALUES('$UserId', '$name', '$email', '$username', '$mob')";
					if(!$this->connect->query($query)) {
						$this->key = 1;
						echo "You are not registered || Error in registration1";
					}
				}
			}
		}
		if ($this->key == 0) {
			Session::put('start', $UserId);
			return json_encode([
				"location" => URL."/account.php"
			]);
		}
		else
		{
			return json_encode($this->error);
		}
	}

	public function onError($value)
	{
		$this->key = 1;
		$this->error = array_merge($this->error, $value);
	}
}
