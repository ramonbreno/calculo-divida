<?php

session_start();

error_reporting(E_ALL | E_STRICT);
ini_set('display_erros','On');

require 'vendor/autoload.php';

require 'config/config.php';
require 'config/constants.php';

$app = new \Slim\App(["settings"=>$config]);

$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer("resource/views/");

$container['db'] = function($c){
	$db = $c['settings']['db'];
	$pdo = new PDO("mysql:host=".$db['host'].";dbname=".$db['dbname'], $db['user'], $db['pass']);

	return $pdo;
};

require 'App\routes.php';

$app->run();
?>