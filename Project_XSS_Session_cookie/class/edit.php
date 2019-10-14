<?php
	/**
	 * 
	 */
	class edit
	{
		public $pdo;
		public $msg;

		public function connect()
		{
			if (session_status() === PHP_SESSION_ACTIVE) {
				try {
					$dbhost = 'localhost';
					$dbname = 'xssuser';
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
        	$sql_array = array('SET @num := 0', 'UPDATE menu SET id = @num := (@num+1)', 'ALTER TABLE menu AUTO_INCREMENT = 1');
			for ($i=0; $i < 3; $i++) { 
				$sql = $pdo->prepare($sql_array[$i]);
				$sql->execute();
			}
        }

		public function create_menu()
		{
			if ($this->check_exist() != '') {
				$this->connect();
				$pdo = $this->pdo;
				$sql = $pdo->prepare('SELECT * FROM menu');
				$sql->execute();
				$user = $sql->fetchAll();
				foreach ($user as $key => $value) {
					echo '<li><a href="#">'.$value[1].'</a></li>';
				}	
			}
		}

		public function save_name($name)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('INSERT INTO menu (NAME_MENU) VALUES (?)');
			$sql->execute(array("$name"));
			echo '<script type="text/javascript">
					window.location.replace(\'./index.php\');
				</script>';
		}

		public function check_exist()
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT count(*) FROM menu');
			$sql->execute();
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				return $value[0];
			}
		}

		public function delete_menu($name_menu)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('DELETE FROM menu WHERE NAME_MENU = ?');
			if ($sql->execute(array("$name_menu"))) {
				$this->auto_fix();
				echo '<script type="text/javascript">
						window.location.replace(\'./index.php\');
					</script>';
			}else{
				echo '<script type="text/javascript">
						alert("menu no exist");;
					  </script>';
			}
		}

		public function rename($name_old, $name_new)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('UPDATE menu SET NAME_MENU = ? WHERE NAME_MENU = ?');
			$sql->execute(array("$name_new", "$name_old"));
			echo '<script type="text/javascript">
					window.location.replace(\'./index.php\');
				</script>';
			$this->msg = 'Change successfully';
			$this->print_msg();
		}

		public function check_name_old($name_old)
		{
			$this->connect();
			$pdo = $this->pdo;
			$sql = $pdo->prepare('SELECT NAME_MENU FROM menu');
			$sql->execute();
			$user = $sql->fetchAll();
			foreach ($user as $key => $value) {
				if ($value[0] == $name_old) {
					return true;
				}
			}
			return false;
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