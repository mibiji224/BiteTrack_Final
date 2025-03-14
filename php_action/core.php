<?php 

session_start();

require_once 'db_connect.php';

// echo $_SESSION['userId'];

if(!$_SESSION['userId']) {
	header('Location: http://nutritracker.fwh.is/index.php');
    exit();
} 



