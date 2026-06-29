<x-mail::message>
# Kode OTP Verifikasi Akun SuperPosko

Halo,

Selamat bergabung di **{{ config('app.name') }}**! Langkah terakhir untuk mengaktifkan akun Anda adalah dengan melakukan verifikasi kode OTP di bawah ini:

<x-mail::panel>
# {{ $otpCode }}
</x-mail::panel>

Kode ini berlaku selama **15 menit**. Harap masukkan kode ini di halaman verifikasi akun untuk melanjutkan.

*Catatan: Jika Anda tidak menerima email ini di kotak masuk utama, silakan periksa folder **Spam** atau **Junk** Anda.*

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
