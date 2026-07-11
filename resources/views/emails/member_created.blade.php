<x-mail::message>
# Akses Akun SuperPosko KKN

Halo **{{ $memberName }}**,

Anda telah ditambahkan sebagai anggota posko KKN di platform **{{ config('app.name') }}**. 
Gunakan kredensial berikut untuk masuk ke sistem:

<x-mail::panel>
**Email:** {{ $email }}  
**Password:** {{ $password }}
</x-mail::panel>

Anda dapat masuk dan mengakses seluruh fitur kelompok posko Anda melalui tautan di bawah ini:

<x-mail::button :url="route('login')">
Login ke SuperPosko
</x-mail::button>

*Demi keamanan akun Anda, silakan ubah password Anda setelah berhasil masuk di halaman Pengaturan Profil.*

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
