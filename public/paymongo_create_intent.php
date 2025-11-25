<?php
require_once __DIR__ . '/../app/Controllers/CheckoutControl.php';

$controller = new CheckoutController();
$controller->createPayment();
