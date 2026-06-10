# Tim-7 Net Mobile

Aplikasi Flutter Android untuk portal pelanggan Tim-7 Net.

## API production

Base URL sudah diset ke:

```text
https://tim-7.net/api/mobile
```

Endpoint yang dipakai:

- `POST /customer/login`
- `GET /customer/invoices?customer_number=...`

## Menjalankan

Pastikan Flutter SDK sudah terpasang, lalu jalankan:

```bash
flutter create . --platforms=android
flutter pub get
flutter run
```

Perintah `flutter create . --platforms=android` hanya diperlukan jika Gradle wrapper Android belum ada di folder ini.

Untuk build APK:

```bash
flutter build apk --release
```
