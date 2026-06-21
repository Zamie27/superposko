<x-mail::message>
# Email Akun Anda Telah Diganti

Halo,

Kami ingin memberitahukan bahwa alamat email akun Anda di **{{ config('app.name') }}** telah berhasil diganti.

- **Email Lama:** {{ $oldEmail }}
- **Email Baru:** {{ $newEmail }}

Jika Anda memang melakukan perubahan ini, Anda tidak perlu mengambil tindakan apa pun.

# PENTING:
Jika Anda **TIDAK merasa mengganti email akun Anda**, harap segera laporkan masalah ini untuk mengamankan akun Anda kembali dengan mengeklik tombol di bawah ini:

<x-mail::button :url="$reportUrl" color="red">
Laporkan Masalah Ini
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
