<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('E-posta Doğrulama') }}</title>
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
        .email-icon {
            color: #3182ce;
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
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .button-secondary {
            display: inline-block;
            background-color: transparent;
            color: #4f46e5;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border: 1px solid #4f46e5;
            cursor: pointer;
            margin-right: 10px;
        }
        .button-secondary:hover {
            background-color: #f8f7ff;
        }
        .success-message {
            display: none;
            color: #48bb78;
            margin-top: 20px;
            padding: 10px;
            background-color: #f0fff4;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        
        <h1 class="title">{{ __('E-posta Adresinizi Doğrulayın') }}</h1>
        
        <p class="message">
            {{ __('Hesabınızı kullanmaya başlamadan önce, e-posta adresinizi doğrulamanız gerekmektedir. Size gönderdiğimiz e-postadaki doğrulama bağlantısına tıklayarak e-posta adresinizi doğrulayabilirsiniz.') }}
        </p>
        
        <form id="resendForm" action="{{ route('verification.resend') }}" method="POST" style="display: inline;">
            @csrf
            @if(auth()->check())
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
            @else
                <div style="margin-bottom: 20px; text-align: left;">
                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: 600;">{{ __('E-posta Adresiniz') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px;">
                    @error('email')
                        <p style="color: #e53e3e; margin-top: 5px; font-size: 14px;">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            <button type="submit" class="button-secondary" id="resendButton">
                {{ __('Doğrulama E-postasını Tekrar Gönder') }}
            </button>
        </form>
        
        <a href="{{ config('app.url') }}" class="button">
            {{ __('Ana Sayfaya Dön') }}
        </a>
        
        <div class="success-message" id="successMessage">
            {{ __('Doğrulama bağlantısı e-posta adresinize gönderildi!') }}
        </div>
    </div>

    <script>
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const button = document.getElementById('resendButton');
            const successMessage = document.getElementById('successMessage');
            const emailInput = document.querySelector('input[name="email"]');
            
            if (!emailInput.value) {
                alert('{{ __("Lütfen e-posta adresinizi girin.") }}');
                return;
            }
            
            button.disabled = true;
            button.innerText = '{{ __("Gönderiliyor...") }}';
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: emailInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                button.innerText = '{{ __("Doğrulama E-postasını Tekrar Gönder") }}';
                button.disabled = false;
                
                if (data.status === 'error') {
                    alert(data.message || '{{ __("Bir hata oluştu. Lütfen tekrar deneyin.") }}');
                    return;
                }
                
                successMessage.style.display = 'block';
                
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                button.innerText = '{{ __("Doğrulama E-postasını Tekrar Gönder") }}';
                button.disabled = false;
                alert('{{ __("Bir hata oluştu. Lütfen tekrar deneyin.") }}');
            });
        });
    </script>
</body>
</html> 