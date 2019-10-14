<?php

	require_once 'class/edit.php';
	require_once 'class/user.php';

	session_start();

	// $edit = new edit();
	// $edit->connect();

	$user = new user();
	$user->connect();
?>