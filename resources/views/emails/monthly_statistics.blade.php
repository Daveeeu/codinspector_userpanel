<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('monthly_statistics_title') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e4e9f0;
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
            line-height: 1.8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #0A58C2;
            color: white;
            font-size: 14px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #eef5ff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #0A58C2;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header" style="text-align: center">
        <img src="https://inspectorramburs.ro/mobilra.svg" class="logo-img" alt="Logo" style="max-width: 100px; margin-bottom: 10px;">
        <h1>{{ __('monthly_statistics_header') }}</h1>
    </div>
    <div class="content">
        <div style="margin-bottom: 20px; font-size: 20px; text-align: center">{{ __('monthly_statistics_hello', ['name' => $userName]) }}</div>
        <p style="text-align: center">{{ __('monthly_statistics_description', ['cycleStartDate' => $cycleStartDate, 'cycleEndDate' => $cycleEndDate]) }}</p>
        <table>
            <thead>
            <tr>
                <th>{{ __('monthly_statistics_table_header_domain') }}</th>
                <th>{{ __('monthly_statistics_table_header_profit') }}</th>
                <th>{{ __('monthly_statistics_table_header_orders') }}</th>
                <th>{{ __('monthly_statistics_table_header_blocked') }}</th>
                <th>{{ __('monthly_statistics_table_header_received') }}</th>
                <th>{{ __('monthly_statistics_table_header_non_received') }}</th>
                <th>{{ __('monthly_statistics_table_header_feedbacks') }}</th>
                <th>{{ __('monthly_statistics_table_header_queries') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($stores as $store)
                <tr>
                    <td>{{ $store['domain'] }}</td>
                    <td>{{ $store['profit'] }} Ft</td>
                    <td>{{ $store['orders'] }}</td>
                    <td>{{ $store['blocked'] }}</td>
                    <td>{{ $store['received'] }}</td>
                    <td>{{ $store['non_received'] }}</td>
                    <td>{{ $store['feedbacks'] }}</td>
                    <td>{{ $store['queries'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        <div style="color:#0A58C2; font-size: 17px; font-weight: bold; margin-bottom: 20px">{{ __('monthly_statistics_footer_thank_you') }}</div>
        <div style="margin-bottom: 25px">
            {!! __('monthly_statistics_footer_contact_us') !!}
        </div>
        <img src="https://inspectorramburs.ro/logo.svg" class="logo-img" alt="Logo" style="max-width: 180px; margin-bottom: 10px;">
    </div>
</div>
</body>
</html>
