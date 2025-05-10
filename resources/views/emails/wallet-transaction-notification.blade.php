<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Transaction Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 10px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            background: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            text-align: left;
            font-size: 16px;
            color: #333;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #777;
        }

        .amount {
            font-weight: bold;
            color: #28a745;
        }

        .deducted {
            color: #dc3545;
        }

        .button {
            display: inline-block;
            background: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <p>Dear <strong>{{ $transaction->user->name }}</strong>,</p>
            <p>Your wallet has been updated.</p>

            <p><strong>Transaction Details:</strong></p>
            <ul>
                @if (max($transaction->amount, 0) > 0)
                <li><strong>Amount:</strong> <span class="[TRANSACTION_TYPE]">{{ formatPriceWithCurrency($transaction->amount) }}</span></li>
                <li><strong>New Balance:</strong> {{ formatPriceWithCurrency($transaction->wallet->amount) }}</li>
                @endif

                @if (max($transaction->points, 0) > 0)
                <li><strong>Points:</strong> <span class="[TRANSACTION_TYPE]">{{ $transaction->points }}</span></li>
                <li><strong>New Balance:</strong> {{ $transaction->wallet->points }}</li>
                @endif

                <li><strong>Transaction Date:</strong> {{ $transaction->created_at }} </li>
            </ul>

            <p>If you did not authorize this transaction, please contact our support team immediately.</p>
        </div>

        <div class="footer">
            Thank you for using our services!<br>
            <strong>{{ env('APP_NAME') }}</strong><br>
            <a href="{{env('APP_URL')}}">Visit our website</a>
        </div>
    </div>

</body>

</html>
