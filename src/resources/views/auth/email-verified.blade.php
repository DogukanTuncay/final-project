<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('E-posta Doğrulandı') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f7fafc;
            color: #4a5568;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            color: #48bb78;
            font-size: 72px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .message {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h1 class="title">{{ __('E-posta Adresiniz Doğrulandı!') }}</h1>
        
        <p class="message">
            {{ __('Teşekkürler! E-posta adresiniz başarıyla doğrulandı. Artık hesabınızın tüm özelliklerini kullanabilirsiniz.') }}
        </p>
        
        <a href="{{ config('app.url') }}" class="button">
            {{ __('Ana Sayfaya Dön') }}
        </a>
    </div>
</body>
</html> 