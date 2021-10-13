<?php
use API\TableGateways\UserGateway;
require('./db/bootstrap.php');
require('./tableGateways/UserGateway.php');

$userGateway = new UserGateway($dbConnection);
// $input = Array("email" => "kadeksuryam@gmail.com", "username" => "kadeksuryam",
// "password" => "kadeksuryam");

// print_r($userGateway->insert($input));
// print_r($userGateway->findAll());
// $updateIn = Array("email" => "kadeksuryam@gmail.com", "username" => "suryam");
// print_r($userGateway->update(2, $updateIn));
// print_r($userGateway->delete(2));