<?php 
//login

$app->get('/', 'App\Action\HomeAction:index');
$app->post('/calc', 'App\Action\HomeAction:calcular');


?>