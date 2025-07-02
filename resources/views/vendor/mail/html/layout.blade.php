<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f8fafc;
            color: #374151;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }

        a {
            color: #1d4ed8;
        }

        /* Layout */
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 20px 0;
        }

        .content {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .content-cell {
            padding: 32px;
        }

        /* Button */
        .button {
            display: inline-block;
            background-color: #1d4ed8;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 24px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .inner-body, .content, .content-cell, .footer {
                width: 100% !important;
                padding: 16px !important;
            }

            .button {
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>

    {{ $head ?? '' }}
</head>
<body>
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table class="content" cellpadding="0" cellspacing="0" role="presentation">
                {{-- Header --}}
                {{ $header ?? '' }}

                {{-- Body --}}
                <tr>
                    <td class="content-cell">
                        {!! Illuminate\Mail\Markdown::parse($slot) !!}
                        {{ $subcopy ?? '' }}
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td class="footer">
                        {{ $footer ?? '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
