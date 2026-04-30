# Deploy ke InfinityFree

## 1. Siapkan database di panel InfinityFree

1. Login ke panel/client area InfinityFree.
2. Buka menu `MySQL Databases`.
3. Buat database baru.
4. Catat 4 data ini:
   - `DB Host`
   - `DB Name`
   - `DB Username`
   - `DB Password`

Catatan:
- Host database InfinityFree biasanya bukan `localhost`.
- Nama database sering berbentuk seperti `if0_12345678_tabunganku`.

## 2. Import struktur database

1. Buka `phpMyAdmin` dari panel InfinityFree.
2. Pilih database yang baru dibuat.
3. Import file [database/tabunganku.sql](/c:/xampp/htdocs/tabungankuu/database/tabunganku.sql).

File SQL ini sudah disiapkan untuk shared hosting, jadi tidak lagi mencoba membuat database sendiri.

## 3. Siapkan konfigurasi koneksi

1. Di folder [tabunganku](/c:/xampp/htdocs/tabungankuu/tabunganku), copy `config.example.php` menjadi `config.php`.
2. Isi `config.php` dengan data database hosting Anda.

Contoh:

```php
<?php

return [
    'db_host' => 'sqlXXX.infinityfree.com',
    'db_name' => 'if0_XXXXXXXX_tabunganku',
    'db_user' => 'if0_XXXXXXXX',
    'db_pass' => 'password_database_anda',
    'db_port' => 3306,
];
```

Catatan:
- File `config.php` tidak ikut di-push ke Git karena sudah masuk `.gitignore`.
- Jika `config.php` tidak ada, aplikasi akan fallback ke konfigurasi lokal XAMPP.

## 4. Upload file ke `htdocs`

Upload isi project ini ke folder `htdocs` di InfinityFree dengan struktur seperti ini:

```text
htdocs/
  css/
  tabunganku/
```

Artinya:
- folder `css` ikut diupload
- folder `tabunganku` ikut diupload

Jangan upload ke folder home di atas `htdocs`.

## 5. URL akses

Jika struktur upload tetap sama, biasanya aplikasi dibuka dari:

```text
https://namadomainanda/tabunganku/login.php
```

## 6. Jika login atau data kosong

Cek hal berikut:

1. `config.php` sudah benar.
2. Database sudah di-import.
3. `DB Host` memakai host dari panel InfinityFree, bukan `localhost`.
4. File diupload ke `htdocs`, bukan ke root akun.
5. Permission file normal, biasanya `644` untuk file dan `755` untuk folder.

## 7. Setelah upload berhasil

1. Buka `login.php`.
2. Uji login.
3. Uji simpan target.
4. Uji tambah transaksi.
5. Uji halaman dashboard dan laporan.
