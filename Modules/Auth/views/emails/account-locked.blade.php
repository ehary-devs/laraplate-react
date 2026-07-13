<x-mail::message>
# Percobaan Login Mencurigakan

Halo **{{ $user->name }}**,

Kami mendeteksi beberapa kali gagal login pada akun Anda. Akun Anda telah dikunci sementara selama **{{ $lockoutMinutes }} menit** sebagai langkah keamanan.

Jika ini bukan Anda, harap segera hubungi administrator atau ubah password Anda setelah masa kunci berakhir.

<x-mail::button :url="config('app.url')">
Kembali ke Aplikasi
</x-mail::button>

Terima kasih,
{{ config('app.name') }}
</x-mail::message>
