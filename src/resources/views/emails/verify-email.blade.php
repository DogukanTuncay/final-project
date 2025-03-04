<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Thank you for registering. Please click the button below to verify your email address.</p>
    <a href="{{ $verificationUrl }}"
       style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
        Verify Email
    </a>
    <p>If you did not create an account, no further action is required.</p>
</body>
</html>
