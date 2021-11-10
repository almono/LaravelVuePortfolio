<!DOCTYPE html>
<html style="margin: 0; padding: 0">

<head>

<body>
<div class="email-content">
    <p>You requested a password reset, to continue with that request please click this link: <a href="{{ $resetUrl }}" target="_blank">RESET PASSWORD</a></p>
</div>
</body>

</html>

<style>
    .email-content {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>