<?php
require 'vendor/autoload.php'; // Include libraries installed via Composer
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $amount = $data['amount']; // Amount in BRL (e.g., 1000 = R$10.00)
    $method = $data['method'];
    $email = $data['email'];

    if ($method === 'pix') {
        // Integrate with MercadoPago or another payment processor
        \MercadoPago\SDK::setAccessToken($_ENV['MERCADOPAGO_ACCESS_TOKEN']);

        $payment = new \MercadoPago\Payment();
        $payment->transaction_amount = (float) $amount;
        $payment->description = "Payment for order";
        $payment->payment_method_id = "pix";
        $payment->payer = ["email" => $email];
        $payment->save();

        echo json_encode([
            'status' => $payment->status,
            'message' => $payment->status === 'approved' ? 'Payment successful!' : 'Payment pending.',
            'payment_id' => $payment->id,
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unsupported payment method.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
