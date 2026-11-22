# Ngobrol (`chat-app`)

Aplikasi web chat *real-time* berbasis PHP dan JavaScript dengan dukungan tampilan responsif serta PWA (*installable*).

<p align="left">
  <img src="https://img.shields.io/badge/PHP-18181B?style=flat-square&logo=php&logoColor=8993BE" alt="PHP" />
  <img src="https://img.shields.io/badge/JavaScript-18181B?style=flat-square&logo=javascript&logoColor=F7DF1E" alt="JavaScript" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-18181B?style=flat-square&logo=tailwindcss&logoColor=38BDF8" alt="Tailwind CSS" />
</p>

---

## Fitur Utama

- Pengiriman dan penerimaan pesan secara *real-time*.
- Mendukung mode terang dan gelap (*dark mode*).
- Dilengkapi `manifest.json` dan Service Worker (`sw.js`) agar bisa dipasang seperti aplikasi mobile.

---

## Menjalankan Secara Lokal

1. Pastikan PHP, Composer, dan MySQL/MariaDB sudah terpasang.
2. Install dependensi PHP:
   ```bash
   composer install
   ```
3. Import skema database dari file `database.sql`.
4. Sesuaikan konfigurasi koneksi pada `config.php` / `connection.php`, lalu jalankan di local PHP server (XAMPP/Laragon/PHP built-in server).