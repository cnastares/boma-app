<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; }
        .details { margin-top: 20px; }
        .total { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h1>Invoice #{{ test }}</h1>
            <p>Date: {{ test }}</p>
        </div>
        <div class="details">
            <p><strong>Amount Due:</strong> ${{ $amount_due }}</p>
            <p><strong>Status:</strong> {{ $status }}</p>
        </div>
        <div class="total">
            <p>Total: ${{ $amount_due }}</p>
        </div>
    </div>
</body>
</html>
