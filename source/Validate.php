<?php
namespace AnkitJain\RegistrationModule;
require_once (dirname(__DIR__) . '/config/database.php');

class Validate
{
	protected $connect;

	public function __construct()
	{
		$this->connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	}

	public function validateEmailInDb($email)
	{
		$query = "SELECT login_id FROM login WHERE email = '$email'";
		if ($result = $this->connect->query($query))
		{
			if ($result->num_rows > 0) {
				return 1;
			}
			return 0;
		}
	}

	public function validateUsernameInDb($username)
	{
		$query = "SELECT login_id FROM login WHERE username = '$username'";
		if ($result = $this->connect->query($query)) {
			if ($result->num_rows > 0) {
				return 1;
			}
			return 0;
		}
	}
}
