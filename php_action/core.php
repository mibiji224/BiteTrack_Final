<?php 
session_start();
require_once 'db_connect.php';

// Fix: Use 'user_id' to match your login script
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
} 
?>