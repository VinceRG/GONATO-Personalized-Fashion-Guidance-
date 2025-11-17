<?php
class PayMongo {
    private string $secretKey;

public function __construct(string $secretKey = null) {
    $this->secretKey = $secretKey ?? ($_ENV['PAYMONGO_SECRET_KEY'] ?? null);
    if (!$this->secretKey) {
        throw new Exception('PayMongo secret key not set');
    }
}


    /**
     * Create a payment intent
     * 
     * @param float $amount Amount in PHP
     * @param string $currency Currency code, default 'PHP'
     * @param array $paymentMethodTypes Allowed payment methods
     * @return array Response from PayMongo
     */
public function createPaymentIntent(float $amount, array $paymentMethodAllowed = ['card']): array {
    $amount_cents = (int) round($amount * 100);

    $payload = [
        "data" => [
            "attributes" => [
                "amount" => $amount_cents,
                "currency" => "PHP",                  // hardcoded
                "payment_method_allowed" => $paymentMethodAllowed
            ]
        ]
    ];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/payment_intents");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($this->secretKey . ":")
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);

    if ($httpCode >= 400) {
        throw new Exception('PayMongo API error: ' . json_encode($decoded));
    }

    return $decoded;
}

}
