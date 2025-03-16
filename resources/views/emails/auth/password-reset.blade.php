<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Your DancyLinks Password') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding: 40px 0;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <tr>
                    <td style="padding: 40px;">
                        <!-- Header -->
                        <div style="text-align: center; margin-bottom: 24px;">
                            <h1 style="margin: 0; font-size: 24px; color: #222E50; font-weight: bold; margin-bottom: 12px;">
                                {{ __('Secure link to reset your DancyLinks password') }}
                            </h1>
                            <p style="margin: 0; color: #6B7280; font-size: 14px;">
                                {{ now()->format('F j, Y, g:i a') }}
                            </p>
                        </div>

                        <!-- Logo -->
                        <div style="text-align: center; margin-bottom: 24px;">
                            <img src="{{ Storage::url('images/logo.png') }}" alt="{{ __('DancyLinks') }}" style="width: 96px; height: auto;">
                        </div>

                        <!-- Subheader -->
                        <div style="text-align: center; margin-bottom: 24px;">
                            <h2 style="margin: 0; font-size: 20px; color: #222E50; font-weight: 600;">
                                {{ __('Let\'s get you signed in') }}
                            </h2>
                        </div>

                        <!-- Main Content -->
                        <div style="text-align: center; margin-bottom: 32px;">
                            <p style="margin: 0 0 24px 0; color: #6B7280; font-size: 16px; line-height: 24px;">
                                {{ __('All you have to do is click this button and we\'ll help you reset your password with a secure link') }}
                            </p>

                            <!-- Button -->
                            <div style="margin-bottom: 32px;">
                                <a href="{{ route('password.reset', ['token' => $token]) }}"
                                   style="display: inline-block; padding: 12px 24px; background-color: #FCCB06; color: #222E50; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px;">
                                    {{ __('Reset DancyLinks Password') }}
                                </a>
                            </div>

                            <!-- Security Notice -->
                            <p style="margin: 0 0 16px 0; color: #6B7280; font-size: 14px;">
                                {{ __('If you didn\'t request this email, you can safely ignore it.') }}
                            </p>

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
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
