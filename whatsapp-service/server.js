import express from 'express';
import cors from 'cors';
import qrcode from 'qrcode';
import pino from 'pino';
import makeWASocket, {
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
} from '@whiskeysockets/baileys';

const app = express();
const port = Number(process.env.WHATSAPP_BRIDGE_PORT || 3020);
const authDir = process.env.WHATSAPP_AUTH_DIR || 'storage/app/whatsapp-session';

app.use(cors());
app.use(express.json({ limit: '1mb' }));

let sock = null;
let qrText = null;
let qrImage = null;
let status = 'starting';
let lastError = null;
let connectedNumber = null;
let connecting = null;

const logger = pino({ level: process.env.WHATSAPP_LOG_LEVEL || 'silent' });

function normalizePhone(input) {
  let number = String(input || '').replace(/\D/g, '');

  if (number.startsWith('0')) {
    number = `62${number.slice(1)}`;
  }

  if (!number.startsWith('62') && number.length >= 9) {
    number = `62${number}`;
  }

  return number;
}

async function connectWhatsapp() {
  if (connecting) return connecting;

  connecting = (async () => {
    status = sock ? status : 'connecting';
    lastError = null;

    const { state, saveCreds } = await useMultiFileAuthState(authDir);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
      auth: state,
      version,
      logger,
      printQRInTerminal: false,
      browser: ['ISP Manager', 'Chrome', '1.0.0'],
    });

    sock.ev.on('creds.update', saveCreds);
    sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr) {
        qrText = qr;
        qrImage = await qrcode.toDataURL(qr, {
          margin: 1,
          width: 320,
          color: { dark: '#0f172a', light: '#ffffff' },
        });
        status = 'qr';
      }

      if (connection === 'open') {
        qrText = null;
        qrImage = null;
        status = 'connected';
        connectedNumber = sock.user?.id?.split(':')[0] || sock.user?.id || null;
      }

      if (connection === 'close') {
        const code = lastDisconnect?.error?.output?.statusCode;
        const loggedOut = code === DisconnectReason.loggedOut;

        status = loggedOut ? 'logged_out' : 'disconnected';
        connectedNumber = null;
        sock = null;

        if (!loggedOut) {
          setTimeout(() => {
            connectWhatsapp().catch((error) => {
              lastError = error.message;
              status = 'error';
            });
          }, 2000);
        }
      }
    });
  })().finally(() => {
    connecting = null;
  });

  return connecting;
}

function currentState() {
  return {
    success: true,
    status,
    connected: status === 'connected',
    qr: qrImage,
    number: connectedNumber,
    message: statusMessage(),
    last_error: lastError,
  };
}

function statusMessage() {
  if (status === 'connected') return 'WhatsApp terhubung.';
  if (status === 'qr') return 'Scan QR dengan WhatsApp untuk menghubungkan perangkat.';
  if (status === 'logged_out') return 'Sesi WhatsApp keluar. Mulai ulang koneksi untuk QR baru.';
  if (status === 'error') return 'Service WhatsApp mengalami error.';
  if (status === 'connecting' || status === 'starting') return 'Service sedang menyiapkan koneksi.';
  return 'WhatsApp belum terhubung.';
}

app.get('/health', (_req, res) => {
  res.json({ success: true, service: 'whatsapp-bridge', status });
});

app.post('/connect', async (_req, res) => {
  try {
    await connectWhatsapp();
    res.json(currentState());
  } catch (error) {
    lastError = error.message;
    status = 'error';
    res.status(500).json({ success: false, message: error.message });
  }
});

app.get('/status', async (_req, res) => {
  if (!sock && status !== 'logged_out') {
    connectWhatsapp().catch((error) => {
      lastError = error.message;
      status = 'error';
    });
  }

  res.json(currentState());
});

app.post('/send', async (req, res) => {
  try {
    if (!sock || status !== 'connected') {
      return res.status(409).json({
        success: false,
        message: 'WhatsApp belum terhubung. Scan QR terlebih dahulu.',
      });
    }

    const number = normalizePhone(req.body.to);
    const message = String(req.body.message || '').trim();

    if (!number || number.length < 10) {
      return res.status(422).json({ success: false, message: 'Nomor tujuan tidak valid.' });
    }

    if (!message) {
      return res.status(422).json({ success: false, message: 'Pesan wajib diisi.' });
    }

    await sock.sendMessage(`${number}@s.whatsapp.net`, { text: message });

    res.json({
      success: true,
      message: `Pesan berhasil dikirim ke ${number}.`,
      to: number,
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

app.post('/logout', async (_req, res) => {
  try {
    if (sock) {
      await sock.logout();
    }

    sock = null;
    qrText = null;
    qrImage = null;
    connectedNumber = null;
    status = 'logged_out';

    res.json({ success: true, message: 'Sesi WhatsApp berhasil diputus.' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

app.listen(port, () => {
  connectWhatsapp().catch((error) => {
    lastError = error.message;
    status = 'error';
  });
  console.log(`WhatsApp bridge listening on http://127.0.0.1:${port}`);
});
