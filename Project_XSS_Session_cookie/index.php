<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style_xss.css">
    <title>Xss</title>
    
    <style type="text/css">
        table, th, td{
            /*border:1px solid #ccc;*/
            border:1px solid #868585;
            text-align: center;
            padding: 20px;
        }
        table{
            border-collapse:collapse;
            overflow:auto;
            margin-top: 20px;
        }
        tr:hover{
            background-color:#ddd;
            cursor:pointer;
        }

        a{
            text-decoration: none;
        }

        a:hover{
            color: red;
        }
	</style>
</head>

<body>
    <div class="container">
        <strong><span style="font-size: 30px">Project Xss Session cookie</span> </strong>
        <div class="a">
           <a href="login.php"> <button class="button" type="button">Login</button></a>
           <a href="logout.php"> <button class="button" type="button">Logout</button></a>
        </div>
        <div id="menu">
                <ul>
                    <li><a href="#">Menu 1</a></li>
					<?php
						require_once 'class/edit.php';
						require_once 'config.php';
						require_once 'config2.php';

						// echo '<script type="text/javascript">alert("'.print_r($_SESSION).'");</script>';
						
						$edit = new edit();

						$user = new user();

						$edit->create_menu();

						if (!empty($_POST["submit"])) {
							switch ($_POST["submit"]) {
								case 'Delete':
									$name_menu = $_POST["name_delete"];
									$edit->delete_menu($name_menu);
									break;
								case 'Create':
									$name = $_POST["name_create"];
									$edit->save_name($name);
									break;
								case 'Rename':
									$name_old = $_POST["name_old"];
									$name_new = $_POST["name_new"];
									if ($edit->check_name_old($name_old)) {
										$edit->rename($name_old, $name_new);
									}
									break;
								case 'Rename user':
									$user_old = $_POST["user_old"];
									$user_new = $_POST["user_new"];
									if ($user->check_user_old($user_old)) {
										if($user->check_session() == 1) {
												if ($user->check_name_userid($user->check_session_userid()) == $user_old) {
													if ($user->anti_same($user_new)) {
														$user->rename_user($user_old, $user_new);
													}
												}
										}
									}
									break;
							}
						}
					?>
                </ul>
        </div>
        <div id="edit">
        	<a href="?create=c">Create new menu</a><br><br>
        	<a href="?create=d">Delete menu</a><br><br>
        	<a href="?create=r">Rename menu</a><br><br>
        	<a href="?rename=re">Rename user</a>
        	        <?php
        	        	require_once 'class/edit.php';
						require_once 'config.php';
						require_once 'config2.php';

		        		if (!empty($_GET["create"]) and $_SESSION["user"]["level"] == 3 or $_SESSION["user"]["level"] == 2) {

							$myfile = fopen("userid_admin_manager.txt", "r") or die("Unable to open file!");
							$id = array();
							while (!feof($myfile)) {
								$line = fgets($myfile);
								$id[] = substr($line, 8);
							}
							for ($i=0; $i < 2; $i++) { 
								if (trim($id[$i]) == $_SESSION["user"]["userid"]) {
				        			if ($_GET["create"] == "c") {
				        				require_once 'create.php';
				        			}elseif ($_GET["create"] == "d") {
				        				require_once 'delete.php';
				        			}elseif ($_GET["create"] == "r") {
				        				require_once 'rename.php';
				        			}
								}
							}
							fclose($myfile);
		        		}

		        		if (!empty($_GET["rename"]) and $_SESSION["user"]["level"] == 1) {
		        			if ($_GET["rename"] == "re") {
		        				require_once 'rename_user.php';
		        			}
		        		}

		        		if ($_SESSION["user"]["level"] == 3 or $_SESSION["user"]["level"] == 2) {
							$myfile = fopen("userid_admin_manager.txt", "r") or die("Unable to open file!");
							$userid = array();
							while (!feof($myfile)) {
								$line = fgets($myfile);
								$userid[] = substr($line, 8);
							}
							for ($i=0; $i < 2; $i++) { 
								if (trim($userid[$i]) == $_SESSION["user"]["userid"]) {
				        			echo '<br><br>'.'<a href="?show=u">Show user</a>';
				        			if (!empty($_GET["show"])) {
				        				if ($_GET["show"] == "u") {
				        					echo '<table border="1" width="100%">';
				        					$user->show_user();
				        					echo '<table>';
				        				}
				        			}
								}
							}
							fclose($myfile);
		        		}

		        		if ($_SESSION["user"]["level"] == 3) {
							$myfile = fopen("userid_admin_manager.txt", "r") or die("Unable to open file!");
							while (!feof($myfile)) {
								$line = fgets($myfile);
								$id = strpos($line, 'dmin');
								if (isset($id) and $id != 0) {
									$userid = substr($line, 8);
								}
							}
							if (trim($userid) ==  $_SESSION["user"]["userid"]) {
			        			echo '<br><br>'.'<a href="?admin=add">Add user</a>';
			        			echo '<br><br>'.'<a href="?admin=delete">Delete user</a>';
			        			if (!empty($_GET["admin"])) {
			        				if ($_GET["admin"] == "add") {
			        					$user->create_form_user();
			        					if (!empty($_POST["submit_create_user"])) {
			        						$name_create_user = $_POST["name_user_create"];
			        						$pass_user_create = $_POST["pass_user_create"];
			        						if ($user->anti_same($name_create_user)) {
			        							$user->add_user($name_create_user, $pass_user_create, $level_user_create );
			        						}
			        					}
			        				}

			        				if ($_GET["admin"] == "delete") {
			        					$user->create_form_delete();
			        					if (!empty($_POST["submit_delete_user"])) {
											echo '<script type="text/javascript">
													alert("kaitoryouga");
												</script>';
			        						$user_delete = $_POST["user_delete"];
											echo '<script type="text/javascript">
													alert("'.$user_delete.'");
												</script>';
			        						if ($user->check_name($user_delete)) {
			        							$user->delete_user($user_delete);
			        						}
			        					}
			        				}
			        			}
							}
							fclose($myfile);
		        		}

		        		if ($_SESSION["user"]["level"] == 1) {
		        			echo '<br><br>'.'<a href="?show=username">Show username</a>';
		        			if (!empty($_GET["show"])) {
		        				if ($_GET["show"] == "username") {
		        					$user->show_username($user->check_session_userid());
		        				}
		        			}
		        		}
		        		// session_destroy();
                    ?>
        </div>
    </div>
</body>
</html>