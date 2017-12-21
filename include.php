<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* SQL Config */
$host = "localhost";
$db = 'login';
$user = '#####';
$password = '#####';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$pdo = new PDO($dsn, $user, $password, $opt);
} 
catch (PDOException $Exception) {
	echo "Exception:" , $Exception->getMessage(), $Exception->getCode();
}
?>
