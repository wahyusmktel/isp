# Catatan Pekerjaan Terakhir

Tanggal catatan: 2026-06-17

## Ringkasan

Fitur Manajemen WiFi ONT sudah diarahkan memakai dua sumber:

- GenieACS tetap dipakai untuk mengubah Nama WiFi / SSID dan password WiFi pelanggan.
- HisFocus OLT dipakai untuk mengambil informasi optik ONT, terutama RxPower, TxPower, temperature, distance, dan status PON.

Masalah terakhir sudah selesai: data optik tidak ketemu karena `mac_ont` pelanggan kosong. Sekarang saat pelanggan dimapping dari halaman PPPoE Mapping, caller-id Mikrotik otomatis ikut tersimpan ke `customers.mac_ont`.

## Perangkat dan IP

- Server Laravel / GenieACS: `11.217.55.5`
- Mikrotik CCR1009: `11.217.55.2`
- OLT HisFocus internal: `192.168.0.88`
- Port forward OLT dari server Laravel: `http://11.217.55.2:8088`
- Endpoint daftar ONU OLT: `/onuAllPonOnuList.asp`
- GenieACS UI: `http://11.217.55.5:3000`
- GenieACS CWMP: `http://11.217.55.5:7547/`
- GenieACS NBI: `http://127.0.0.1:7557`

## Setting Laravel

Di menu Settings bagian Jaringan, konfigurasi OLT HisFocus:

```text
OLT Base URL: http://11.217.55.2:8088
Username OLT: admin
Password OLT: admin
Login Path: kosong
URL ONU List: /onuAllPonOnuList.asp
Timeout: 15
```

Konfigurasi GenieACS:

```text
GenieACS NBI URL: http://127.0.0.1:7557
API Token: kosong jika belum dipakai
SSID Parameter: InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID
Password Parameter: InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase
HTTP Timeout: 15
Task Timeout: 3000
```

## File yang Diubah

- `app/Services/GenieAcsService.php`
  - Service untuk komunikasi ke GenieACS.
  - Mengirim task ubah SSID dan password WiFi.
  - Masih bisa fallback ambil info ONT dari GenieACS kalau OLT tidak tersedia.

- `app/Services/HisfocusOltService.php`
  - Service baru untuk membaca data OLT HisFocus.
  - Sudah support Basic Auth.
  - Sudah support parser data JavaScript `onutable` dari OLT.
  - Matching pelanggan memakai `onu_id` atau `mac_ont`.
  - Untuk MAC Mikrotik dan OLT yang beda byte terakhir, service tetap match dengan prefix 5 byte pertama.

- `app/Http/Controllers/CustomerController.php`
  - Endpoint `ontInfo()` mengambil data optik dari OLT dulu.
  - Jika OLT gagal, fallback ke GenieACS.
  - Endpoint `updateWifi()` mengirim perintah ubah WiFi via GenieACS.

- `app/Http/Controllers/PppoeMappingController.php`
  - Mapping PPPoE sekarang ikut menyimpan caller-id Mikrotik ke `mac_ont`.

- `resources/views/customers/show.blade.php`
  - Panel Manajemen WiFi ONT.
  - Menampilkan Informasi Optik ONT.
  - Tombol ubah SSID/password WiFi.

- `resources/views/network/pppoe-mapping.blade.php`
  - Saat klik map pelanggan, `secret.mac` dikirim ke backend sebagai `mac_ont`.

- `resources/views/settings/index.blade.php`
  - Tambahan setting GenieACS dan OLT HisFocus.

- `routes/web.php`
  - Route `GET /customers/{customer}/ont-info`.
  - Route `POST /customers/{customer}/wifi`.

- `database/migrations/2026_06_17_000001_add_acs_fields_to_customers_table.php`
  - Tambahan kolom `acs_device_id`, `ont_serial_number`, dan `wifi_ssid`.

## RouterOS Port Forward OLT

Konsep NAT dari server Laravel ke OLT:

```routeros
/ip firewall nat
add chain=dstnat protocol=tcp dst-address=11.217.55.2 dst-port=8088 src-address=11.217.55.5 action=dst-nat to-addresses=192.168.0.88 to-ports=80 comment="Forward OLT web to Laravel only"

/ip firewall filter
add chain=forward protocol=tcp src-address=11.217.55.5 dst-address=192.168.0.88 dst-port=80 action=accept comment="Allow Laravel to OLT web"
```

Jika ada rule drop di forward chain, pindahkan rule allow ke atas:

```routeros
/ip firewall filter move [find comment="Allow Laravel to OLT web"] 0
```

Tes dari server Laravel:

```bash
curl -v -u admin:admin http://11.217.55.2:8088/onuAllPonOnuList.asp
```

Hasil yang benar:

- Connected ke `11.217.55.2:8088`.
- HTTP `200 OK`.
- Response berisi `var onutable=new Array(...)`.

## Catatan Matching MAC

Contoh kasus:

```text
Mikrotik caller-id: A4:F3:3B:81:2B:31
OLT MAC:            a4:f3:3b:81:2b:39
```

Ini tetap dianggap match karena 5 byte pertama sama:

```text
a4:f3:3b:81:2b
```

Yang penting `customers.mac_ont` harus terisi. Untuk pelanggan lama yang sudah dimapping sebelum patch terakhir, lakukan salah satu:

- Unmap lalu map ulang di halaman PPPoE Mapping saat pelanggan online.
- Isi MAC ONT manual dengan caller-id Mikrotik.

## Perintah Setelah Deploy

Jalankan di server production setelah update kode:

```bash
php artisan migrate
php artisan optimize:clear
php artisan view:clear
```

Jika halaman masih memakai cache lama:

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## Cara Cek Jika Data Optik Tidak Muncul

1. Buka halaman detail pelanggan.
2. Klik Refresh pada panel Manajemen WiFi ONT.
3. Buka DevTools browser, tab Network.
4. Cari request:

```text
/customers/{id}/ont-info
```

5. Cek response JSON.

Jika muncul:

```text
Data ONU pelanggan tidak ditemukan di tabel OLT HisFocus.
```

cek bagian `lookup`:

```json
{
  "onu_id": "...",
  "mac_ont": "..."
}
```

Jika `mac_ont` kosong, isi lewat PPPoE Mapping atau map ulang pelanggan.

## Status Terakhir

- GenieACS sudah berjalan.
- ONT sudah bisa inform ke GenieACS.
- Ubah WiFi tetap lewat GenieACS.
- OLT sudah bisa diakses dari server Laravel via port forward Mikrotik.
- Parser OLT HisFocus sudah membaca array `onutable`.
- Mapping PPPoE sekarang otomatis menyimpan caller-id Mikrotik ke `mac_ont`.
- Panel informasi optik ONT sudah berhasil setelah `mac_ont` terisi.
