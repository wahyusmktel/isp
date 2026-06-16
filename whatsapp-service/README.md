# WhatsApp Bridge

Service ini menghubungkan aplikasi Laravel dengan WhatsApp Web memakai scan QR.

## Menjalankan

```bash
npm run wa:start
```

Default service berjalan di:

```text
http://127.0.0.1:3020
```

Laravel membaca URL dari:

```env
WHATSAPP_BRIDGE_URL=http://127.0.0.1:3020
```

Sesi WhatsApp disimpan di:

```env
WHATSAPP_AUTH_DIR=storage/app/whatsapp-session
```

Jangan hapus folder sesi kecuali ingin memaksa scan QR ulang.
