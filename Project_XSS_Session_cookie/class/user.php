<?php
	/**
	 * 
	 */
	class user
	{	
		public $pdo;
		public $msg;
		public $user;

		public function connect()
		{
			if (session_status() === PHP_SESSION_ACTIVE) {
				try {
					$dbhost = 'localhost';
					$dbname='xssuser';
					$dbuser = 'root';
					$dbpass = 'kaitoryouga';
					$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
					$this->pdo = $pdo;
					return true;
				}catch (PDOException $e) {
					echo "Error : " . $e->getMessage() . "<br/>";
					die();
				}
			}else{
				$this->msg = "Sever diconnect";
				return false;
			}
		}

		public function auto_fix()
        {
        	$this->connect();
        	$pdo = $this->pdo;
        	$sql_array = array('SET @num := 0', 'UPDATE user SET id = @num := (@num+1)', 'ALTER TABLE menu AUTO_INCREMENT = 1');
			for ($i=0; $i < 3; $i++) { 
				$sql = $pdo->prepare($sql_array[$i]);
				$sql->execute();
			}
        }

		public function login($username, $password)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USER,PASS FROM user');
			$sql->execute();
			$user = $sql->fetchAll();

			foreach ($user as $key => $value) {
				if ($value[0] == $username and $value[1] == $password) {
					return true;
				}
			}
			return false;
		}

		public function assign_session($user)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USERID, LEVEL FROM user WHERE USER = ?');
			$sql->execute(array("$user"));
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				$_SESSION["user"]["userid"] = $value[0];
				$_SESSION["user"]["level"] = $value[1];
			}
		}

		public function echo_head()
		{
			echo '
					<tr>
						<th>
							ID
						</th>
						<th>
							USER
						</th>
						<th>
							PASS
						</th>
						<th>
							LEVEL
						</th>
					</tr>';
		}

		public function show_user()
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT * FROM user');
			$sql->execute();
			$user = $sql->fetchAll();
			$flag = 1;
			foreach ($user as $key => $value) {
				if ($flag++ == 1) {
					$this->echo_head();
				}
				echo'
					<tr>
			        	<td>
			        		'.$value[0].'
			        	</td>
			        	<td>
							'.$value[1].'
			        	</td>
			        	<td>
			        		'.$value[2].'
			        	</td>
			        	<td>
			        		'.$value[4].'
			        	</td>
			        </tr>
		        ';
			}
		}

		public function rename_user($user_old, $user_new)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('UPDATE user SET USER = ? WHERE USER = ?');
			$sql->execute(array("$user_new", "$user_old"));
			echo '<script type="text/javascript">
					window.location.replace(\'./index.php\');
				</script>';
			$this->msg = 'Change successfully';
			$this->print_msg();
		}

		public function check_user_old($user_old)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USER FROM user');
			$sql->execute();
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				if ($value[0] == $user_old) {
					return true;
				}
			}
			return false;
		}

		public function check_session()
		{
			$level = $_SESSION["user"]["level"];
			return $level;
		}

		public function check_session_userid()
		{
			$userid = $_SESSION["user"]["userid"];
			return $userid;
		}

		public function check_name_userid($userid)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USER FROM user WHERE USERID = ?');
			$sql->execute(array("$userid"));
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				return $value[0];
			}
		}

		public function show_username($userid)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT * FROM user WHERE USERID = ?');
			$sql->execute(array("$userid"));
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
					echo'
					<table border="1" width="100%">
								<th>
							ID
						</th>
						<th>
							USER
						</th>
						<th>
							PASS
						</th>
						<tr>
				        	<td>
				        		1
				        	</td>
				        	<td>
				        		'.$value[1].'
				        	</td>
				        	<td>
				        		'.$value[2].'
				        	</td>
				        </tr>
					</table>';
			}
		}

		public function create_session_user()
		{
			$code_session = "aSBhbSBrYWl0byByeW91Z2EgYW5kIGkgd2lsbCBjcmVhdGUgY29kZSBzZXNzaW9u";
			$code = array();
			$code_session_lenght = strlen($code_session) - 1;
			for ($i=0; $i < 8; $i++) { 
				$code[] = $code_session[rand(0, $code_session_lenght)];
			}
			return implode($code);
		}

		public function create_form_user()
		{
			echo '	
				<form action="#" method="POST">
					<p>Enter name of user</p>
					<input type="text" name="name_user_create">
					<p>Enter pass of user</p>
					<input type="text" name="pass_user_create">
					<input type="submit" name="submit_create_user" value="accept">
				</form>';
		}

		public function create_form_delete()
		{
			echo '	
			<form action="#" method="POST">
				<p>Enter username of menu will delete</p>
				<input type="text" name="user_delete">
				<input type="submit" name="submit_delete_user" value="Delete user">
			</form>';
		}

		public function add_user($user, $pass)
		{
			$userid = $this->create_session_user();
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('INSERT INTO user(USER, PASS, USERID, LEVEL) VALUES(?, ?, ?, ?)');
			$sql->execute(array("$user", "$pass", "$userid", "1"));
			$this->auto_fix();
			header("location:index.php");
		}

		public function delete_user($user)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('DELETE FROM user WHERE USER = ?');
			$sql->execute(array("$user"));
			$this->auto_fix();
			header("location:index.php");
		}

		public function check_name($name_user)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USER FROM user');
			$sql->execute();
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				if ($name_user == $value[0]) {
					return true;
				}
			}
			return false;
		}

		public function anti_same($name_new)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT USER FROM user');
			$sql->execute();
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				if ($name_new == $value[0]) {
					return false;
				}
			}
			return true;
		}

		public function print_msg()
		{
			$msg = $this->msg;
			echo '
				<script type="text/javascript">
  					alert("'.$msg.'")
				</script>';
		}
	}
?>