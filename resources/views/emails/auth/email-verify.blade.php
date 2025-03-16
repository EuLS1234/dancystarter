<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Email Verification - DancyLinks') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding: 40px 0;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <tr>
                    <td style="padding: 40px;">
                        <!-- Logo -->
                        <div style="text-align: center; margin-bottom: 24px;">
                            <img src="{{ Storage::url('images/logo.png') }}" alt="{{ __('DancyLinks') }}" style="width: 120px; height: auto;">
                        </div>

                        <!-- Header -->
                        <div style="text-align: center; margin-bottom: 24px;">
                            <h1 style="margin: 0; font-size: 24px; color: #222E50; font-weight: bold; margin-bottom: 8px;">{{ __('Welcome to DancyLinks') }}</h1>
                            <p style="margin: 0; color: #6B7280; font-size: 14px;">{{ __('Thank you for joining our community!') }}</p>
                        </div>

                        <!-- Main Content -->
                        <div style="text-align: center; margin-bottom: 32px;">
                            <p style="margin: 0 0 24px 0; color: #6B7280; font-size: 16px; line-height: 24px;">
                                {{ __('Please verify your email address by clicking the button below to start exploring DancyLinks.') }}
                            </p>

                            <!-- Button -->
                            <div style="margin-bottom: 32px;">
                                <a href="{{$url}}" style="display: inline-block; padding: 12px 24px; background-color: #FCCB06; color: #222E50; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px;">
                                    {{ __('Verify Email') }}
                                </a>
                            </div>

                            <!-- Footer Text -->
                            <p style="margin: 0; color: #6B7280; font-size: 14px;">
                                {{ __('If you didn\'t request this email, you can safely ignore it.') }}
                            </p>
                        </div>

                        <!-- Support Section -->
                        <div style="text-align: center; border-top: 1px solid #E5E7EB; margin-top: 24px; padding-top: 24px;">
                            <p style="margin: 0; color: #6B7280; font-size: 14px;">
                                {{ __('If you\'re experiencing issues, please contact') }}
                                <a href="mailto:dancylinks@gmail.com"
                                   style="color: #222E50; text-decoration: none; border-bottom: 1px solid #222E50;">
                                    {{ __('DancyLinks Support') }}
                                </a>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
