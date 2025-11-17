<?php
class PayMongoWebhookController {
    public function handle() {
        $payload = json_decode(file_get_contents("php://input"), true);
        $eventType = $payload["data"]["attributes"]["type"] ?? '';

        if ($eventType === "payment.paid") {
            $paymentId = $payload["data"]["id"];
            // TODO: update your order status in DB
        }
    }
}
