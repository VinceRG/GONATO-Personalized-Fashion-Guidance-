<?php
require_once __DIR__ . '/../Core/PayMongo.php';

class CheckoutController {
    private $paymongo;

public function __construct() {
    // Let PayMongo read the secret key from $_ENV
    $this->paymongo = new PayMongo();
}


   public function createPayment() {
    $input = json_decode(file_get_contents("php://input"), true);
    $amount = $input['amount'] ?? 0;

    if ($amount <= 0) {
        echo json_encode(['error' => 'Invalid amount']);
        return;
    }

    try {
        // Call PayMongo with PHP amount (PayMongo class converts to cents)
        $paymentIntent = $this->paymongo->createPaymentIntent(
            $amount,
            ['card'] // allowed payment methods
        );

        $attributes = $paymentIntent['data']['attributes'] ?? [];
        $clientKey = $attributes['client_key'] ?? null;
        $redirectUrl = $attributes['next_action']['redirect']['url'] ?? null;

        echo json_encode([
            'redirect' => $redirectUrl,
            'client_key' => $clientKey
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

}
