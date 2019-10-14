<?php
    require_once 'class/user.php';
    require_once 'config2.php';
	$_SESSION["user"]["userid"] = NULL;
	$_SESSION["user"]["level"] = NULL;
	session_destroy();
	header("location:index.php");
?>