<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('successful_payment_title') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
        }
        .content {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="https://inspectorramburs.ro/mobilra.svg" class="logo-img" alt="Logo" style="max-width: 100px; margin-bottom: 10px;">
        <h1>{{ __('successful_payment_header') }}</h1>
    </div>
    <div class="content">
        <div style="margin-bottom: 20px; font-size: 20px">{{ __('successful_payment_hello', ['name' => $userName]) }}</div>
        <div style="margin-bottom: 10px;">
            {!! __('successful_payment_description', ['storeDomain' => $storeDomain]) !!}
        </div>
        <div style="margin-bottom: 10px;">
            {{ __('successful_payment_subscription_updated') }}
        </div>
    </div>
    <div class="footer">
        <div style="color:#0A58C2; font-size: 17px; font-weight: bold; margin-bottom: 20px">{{ __('successful_payment_footer_thank_you') }}</div>
        <div style="margin-bottom: 25px">
            {!! __('successful_payment_footer_contact_us') !!}
        </div>
        <img src="https://inspectorramburs.ro/logo.svg" class="logo-img" alt="Logo" style="max-width: 180px; margin-bottom: 10px;">
    </div>
</div>
</body>
</html>
