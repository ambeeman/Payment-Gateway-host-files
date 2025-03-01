document.getElementById("payment-form").addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    // If the method is credit_card, ensure card token is provided
    if (data.method === 'credit_card') {
        // Assume card token is generated by the payment processor's SDK
        data.card_token = "example-card-token";
    }

    const response = await fetch('/backend/payment_gateway.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    });

    const result = await response.json();

    if (result.status === 'error') {
        alert(`Error: ${result.message}`);
        return;
    }

    if (data.method === 'pix') {
        // Display PIX QR code or link
        alert(`Scan this QR Code: ${result.pix_qr_code}`);
    } else {
        alert(`Payment Status: ${result.message}`);
    }
});
