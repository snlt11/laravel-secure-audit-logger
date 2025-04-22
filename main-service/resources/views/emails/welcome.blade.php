<x-mail::message>

# Welcome to Our App

Hello, {{ $user->name }}!

Thank you for registering with our application. We are excited to have you on board!

If you have any questions, feel free to contact our support team.

Best regards,<br>
{{ config('app.name') }}

</x-mail::message>
