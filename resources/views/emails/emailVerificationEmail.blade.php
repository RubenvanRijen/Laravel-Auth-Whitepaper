<!-- resources/views/emails/verify-email.blade.php -->
<html>
    <body>
        <h1>Email Verification</h1>
        <p>Click the following link to verify your email:</p>
        <a href="{{ $verificationLink }}">{{ $verificationLink }}</a>
    </body>
</html>