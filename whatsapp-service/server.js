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
let connectStartedAt = null;
let lastDisconnectCode = null;
let lastConnectionUpdate = null;

const logger = pino({ level: process.env.WHATSAPP_LOG_LEVEL || 'silent' });
const setupTimeoutMs = Number(process.env.WHATSAPP_SETUP_TIMEOUT_MS || 60000);

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

function disconnectReasonName(code) {
  return DisconnectReason[code] || `unknown_${code || 'none'}`;
}

function hasActiveSocket() {
  return Boolean(sock && status === 'connected');
}

async function connectWhatsapp() {
  if (connecting) return connecting;

  connecting = (async () => {
    status = sock ? status : 'connecting';
    connectStartedAt = Date.now();
    lastError = null;

    console.log(`Preparing WhatsApp session in ${authDir}`);

    const setupTimeout = setTimeout(() => {
      if (status === 'connecting') {
        lastError = 'QR belum muncul. Cek koneksi internet server dan log PM2 WhatsApp bridge.';
        status = 'error';
      }
    }, setupTimeoutMs);

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
      lastConnectionUpdate = {
        connection: connection || null,
        hasQr: Boolean(qr),
        isNewLogin: Boolean(update.isNewLogin),
        receivedPendingNotifications: update.receivedPendingNotifications ?? null,
      };

      if (connection || qr || update.isNewLogin) {
        console.log('WhatsApp connection update:', JSON.stringify(lastConnectionUpdate));
      }

      if (qr) {
        clearTimeout(setupTimeout);
        qrText = qr;
        qrImage = await qrcode.toDataURL(qr, {
          margin: 1,
          width: 320,
          color: { dark: '#0f172a', light: '#ffffff' },
        });
        status = 'qr';
      }

      if (connection === 'open') {
        clearTimeout(setupTimeout);
        qrText = null;
        qrImage = null;
        status = 'connected';
        connectedNumber = sock.user?.id?.split(':')[0] || sock.user?.id || null;
        lastDisconnectCode = null;
        console.log(`WhatsApp connected as ${connectedNumber || 'unknown number'}`);
      }

      if (connection === 'close') {
        clearTimeout(setupTimeout);
        const code = lastDisconnect?.error?.output?.statusCode;
        const loggedOut = code === DisconnectReason.loggedOut;
        const restartRequired = code === DisconnectReason.restartRequired;
        lastDisconnectCode = code || null;

        console.log(`WhatsApp connection closed: ${disconnectReasonName(code)} (${code || 'no-code'})`);

        status = loggedOut ? 'logged_out' : (restartRequired ? 'connecting' : 'disconnected');
        sock = null;

        if (loggedOut) {
          connectedNumber = null;
          qrText = null;
          qrImage = null;
        }

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

function startConnect() {
  connectWhatsapp().catch((error) => {
    console.error('WhatsApp connect failed:', error);
    lastError = error.message;
    status = 'error';
  });
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
    last_disconnect_code: lastDisconnectCode,
    last_disconnect_reason: lastDisconnectCode ? disconnectReasonName(lastDisconnectCode) : null,
    last_connection_update: lastConnectionUpdate,
    connecting_for_seconds: connectStartedAt && status === 'connecting'
      ? Math.round((Date.now() - connectStartedAt) / 1000)
      : null,
  };
}

function statusMessage() {
  if (status === 'connected') return 'WhatsApp terhubung.';
  if (status === 'qr') return 'Scan QR dengan WhatsApp untuk menghubungkan perangkat.';
  if (status === 'logged_out') return 'Sesi WhatsApp keluar. Mulai ulang koneksi untuk QR baru.';
  if (status === 'error') return lastError || 'Service WhatsApp mengalami error.';
  if (status === 'connecting' || status === 'starting') return 'WhatsApp sedang menyelesaikan koneksi. Tunggu sampai status Terhubung.';
  return 'WhatsApp belum terhubung.';
}

app.get('/health', (_req, res) => {
  res.json({ success: true, service: 'whatsapp-bridge', status });
});

app.post('/connect', (_req, res) => {
  if (!sock || ['error', 'disconnected', 'logged_out', 'starting'].includes(status)) {
    status = 'connecting';
    startConnect();
  }

  res.json(currentState());
});

app.get('/status', (_req, res) => {
  if (!sock && status !== 'logged_out') {
    startConnect();
  }

  res.json(currentState());
});

app.post('/send', async (req, res) => {
  try {
    if (!hasActiveSocket()) {
      return res.status(409).json({
        success: false,
        status,
        message: status === 'connecting'
          ? 'WhatsApp sedang menyelesaikan koneksi ulang setelah scan QR. Tunggu sampai status Terhubung.'
          : 'WhatsApp belum terhubung. Scan QR terlebih dahulu.',
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
  startConnect();
  console.log(`WhatsApp bridge listening on http://127.0.0.1:${port}`);
});
