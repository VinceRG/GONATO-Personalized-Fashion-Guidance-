// public/paymongo-webhook.php
<?php
require_once __DIR__ . '/../app/Controllers/PayMongoWebhookControl.php';
$controller = new PayMongoWebhookController();
$controller->handle();
