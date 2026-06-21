<x-mail::message>
# Kode OTP Penggantian Email

Halo,

Anda menerima email ini karena ada permintaan untuk mengganti alamat email akun Anda di **{{ config('app.name') }}** ke alamat email ini.

Berikut adalah kode OTP Anda untuk memverifikasi alamat email baru ini:

<x-mail::panel>
# {{ $otpCode }}
</x-mail::panel>

Kode ini berlaku selama **10 menit**. Jangan membagikan kode ini kepada siapapun.

Jika Anda tidak meminta perubahan ini, Anda dapat mengabaikan email ini dengan aman.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
