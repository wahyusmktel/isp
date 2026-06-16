import express from 'express';
import cors from 'cors';
import qrcode from 'qrcode';
import pino from 'pino';
import fsSync from 'node:fs';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import makeWASocket, {
  Browsers,
  DisconnectReason,
  fetchLatestBaileysVersion,
  makeCacheableSignalKeyStore,
  useMultiFileAuthState,
} from '@whiskeysockets/baileys';

const app = express();
const port = Number(process.env.WHATSAPP_BRIDGE_PORT || 3020);
const serviceDir = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(serviceDir, '..');

loadEnvFile(path.resolve(projectRoot, '.env'));

const configuredAuthDir = process.env.WHATSAPP_AUTH_DIR || 'storage/app/whatsapp-session';
const authDir = path.isAbsolute(configuredAuthDir)
  ? configuredAuthDir
  : path.resolve(projectRoot, configuredAuthDir);

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
let reconnectTimer = null;
let reconnectAttempts = 0;

const logger = pino({ level: process.env.WHATSAPP_LOG_LEVEL || 'info' });
const setupTimeoutMs = Number(process.env.WHATSAPP_SETUP_TIMEOUT_MS || 60000);
const appUrl = process.env.APP_URL || 'http://127.0.0.1';
const commandWebhookUrl = process.env.WHATSAPP_COMMAND_WEBHOOK_URL || new URL('/api/whatsapp/bot-command', appUrl).toString();
const commandToken = process.env.WHATSAPP_COMMAND_TOKEN || '';

function loadEnvFile(filePath) {
  if (!fsSync.existsSync(filePath)) return;

  const lines = fsSync.readFileSync(filePath, 'utf8').split(/\r?\n/);
  for (const line of lines) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#') || !trimmed.includes('=')) continue;

    const index = trimmed.indexOf('=');
    const key = trimmed.slice(0, index).trim();
    let value = trimmed.slice(index + 1).trim();

    if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
      value = value.slice(1, -1);
    }

    if (!process.env[key]) {
      process.env[key] = value;
    }
  }
}

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

function normalizeRecipient(input) {
  const value = String(input || '').trim();

  if (value.endsWith('@g.us') || value.endsWith('@s.whatsapp.net')) {
    return value;
  }

  return `${normalizePhone(value)}@s.whatsapp.net`;
}

function extractMessageText(message) {
  if (!message) return '';

  return (
    message.conversation ||
    message.extendedTextMessage?.text ||
    message.imageMessage?.caption ||
    message.videoMessage?.caption ||
    message.documentMessage?.caption ||
    ''
  ).trim();
}

function disconnectReasonName(code) {
  return DisconnectReason[code] || `unknown_${code || 'none'}`;
}

function hasActiveSocket() {
  return Boolean(sock && status === 'connected');
}

function clearReconnectTimer() {
  if (reconnectTimer) {
    clearTimeout(reconnectTimer);
    reconnectTimer = null;
  }
}

async function closeSocket() {
  clearReconnectTimer();

  if (sock) {
    try {
      sock.end?.();
    } catch (error) {
      console.warn('Failed to close WhatsApp socket cleanly:', error.message);
    }
  }

  sock = null;
}

function clearRuntimeState(nextStatus = 'disconnected') {
  qrText = null;
  qrImage = null;
  connectedNumber = null;
  connecting = null;
  connectStartedAt = null;
  lastDisconnectCode = null;
  lastConnectionUpdate = null;
  lastError = null;
  reconnectAttempts = 0;
  status = nextStatus;
}

function scheduleReconnect(reason = 'unknown') {
  if (reconnectTimer || connecting || status === 'logged_out') return;

  reconnectAttempts += 1;
  const delay = Math.min(30000, 2000 * reconnectAttempts);
  console.log(`Scheduling WhatsApp reconnect in ${delay}ms. Reason: ${reason}. Attempt: ${reconnectAttempts}`);

  reconnectTimer = setTimeout(() => {
    reconnectTimer = null;
    connectWhatsapp().catch((error) => {
      console.error('WhatsApp reconnect failed:', error);
      lastError = error.message;
      status = 'error';
    });
  }, delay);
}

async function connectWhatsapp() {
  if (connecting) return connecting;

  connecting = (async () => {
    status = sock ? status : 'connecting';
    connectStartedAt = Date.now();
    lastError = null;
    clearReconnectTimer();

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
      auth: {
        creds: state.creds,
        keys: makeCacheableSignalKeyStore(state.keys, logger),
      },
      version,
      logger,
      printQRInTerminal: false,
      browser: Browsers.ubuntu('ISP Manager'),
      connectTimeoutMs: 60000,
      defaultQueryTimeoutMs: 60000,
      keepAliveIntervalMs: 20000,
      markOnlineOnConnect: false,
      syncFullHistory: false,
    });

    sock.ev.on('creds.update', saveCreds);
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
      if (type !== 'notify') return;

      for (const message of messages) {
        await handleIncomingMessage(message);
      }
    });
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
        reconnectAttempts = 0;
      }

      if (connection === 'open') {
        clearTimeout(setupTimeout);
        qrText = null;
        qrImage = null;
        status = 'connected';
        connectedNumber = sock.user?.id?.split(':')[0] || sock.user?.id || null;
        lastDisconnectCode = null;
        reconnectAttempts = 0;
        console.log(`WhatsApp connected as ${connectedNumber || 'unknown number'}`);
      }

      if (connection === 'close') {
        clearTimeout(setupTimeout);
        const code = lastDisconnect?.error?.output?.statusCode;
        const loggedOut = code === DisconnectReason.loggedOut;
        const restartRequired = code === DisconnectReason.restartRequired;
        lastDisconnectCode = code || null;

        console.log(`WhatsApp connection closed: ${disconnectReasonName(code)} (${code || 'no-code'})`);

        const reason = disconnectReasonName(code);
        status = loggedOut ? 'logged_out' : (restartRequired ? 'connecting' : 'disconnected');
        sock = null;

        if (loggedOut) {
          connectedNumber = null;
          qrText = null;
          qrImage = null;
          lastError = 'WhatsApp menolak sesi ini. Reset sesi lalu scan QR baru.';
        }

        if (!loggedOut) {
          scheduleReconnect(reason);
        }
      }
    });
  })().finally(() => {
    connecting = null;
  });

  return connecting;
}

async function handleIncomingMessage(message) {
  try {
    const groupId = message.key?.remoteJid || '';
    if (!groupId.endsWith('@g.us') || message.key?.fromMe) return;

    const text = extractMessageText(message.message);
    if (!text.startsWith('/')) return;

    console.log(`WhatsApp command received from ${groupId}: ${text}`);

    const response = await fetch(commandWebhookUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(commandToken ? { 'X-Command-Token': commandToken } : {}),
      },
      body: JSON.stringify({
        group_id: groupId,
        sender: message.key?.participant || '',
        message: text,
        token: commandToken,
      }),
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      console.warn('WhatsApp command webhook failed:', response.status, data);
      return;
    }

    if (data.reply && hasActiveSocket()) {
      await sock.sendMessage(groupId, { text: data.reply }, { quoted: message });
    }
  } catch (error) {
    console.warn('WhatsApp command handling failed:', error.message);
  }
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
    auth_dir: authDir,
    message: statusMessage(),
    last_error: lastError,
    last_disconnect_code: lastDisconnectCode,
    last_disconnect_reason: lastDisconnectCode ? disconnectReasonName(lastDisconnectCode) : null,
    last_connection_update: lastConnectionUpdate,
    reconnect_attempts: reconnectAttempts,
    connecting_for_seconds: connectStartedAt && status === 'connecting'
      ? Math.round((Date.now() - connectStartedAt) / 1000)
      : null,
  };
}

function statusMessage() {
  if (status === 'connected') return 'WhatsApp terhubung.';
  if (status === 'qr') return 'Scan QR dengan WhatsApp untuk menghubungkan perangkat.';
  if (status === 'logged_out') return lastError || 'Sesi WhatsApp keluar. Reset sesi lalu scan QR baru.';
  if (status === 'error') return lastError || 'Service WhatsApp mengalami error.';
  if (status === 'connecting' || status === 'starting') return 'WhatsApp sedang menyelesaikan koneksi. Tunggu sampai status Terhubung.';
  if (status === 'disconnected' && lastDisconnectCode) return `WhatsApp terputus: ${disconnectReasonName(lastDisconnectCode)}. Mencoba koneksi ulang.`;
  return 'WhatsApp belum terhubung.';
}

app.get('/health', (_req, res) => {
  res.json({ success: true, service: 'whatsapp-bridge', status });
});

app.get('/debug', (_req, res) => {
  res.json(currentState());
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
    scheduleReconnect('status-check');
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

    const recipient = normalizeRecipient(req.body.to);
    const message = String(req.body.message || '').trim();

    if (!recipient || (!recipient.endsWith('@g.us') && !recipient.endsWith('@s.whatsapp.net'))) {
      return res.status(422).json({ success: false, message: 'Tujuan pesan tidak valid.' });
    }

    if (!message) {
      return res.status(422).json({ success: false, message: 'Pesan wajib diisi.' });
    }

    await sock.sendMessage(recipient, { text: message });

    res.json({
      success: true,
      message: `Pesan berhasil dikirim ke ${recipient}.`,
      to: recipient,
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

app.get('/groups', async (_req, res) => {
  try {
    if (!hasActiveSocket()) {
      return res.status(409).json({
        success: false,
        status,
        groups: [],
        message: 'WhatsApp belum terhubung. Scan QR terlebih dahulu.',
      });
    }

    const groups = await sock.groupFetchAllParticipating();
    const data = Object.values(groups)
      .map((group) => ({
        id: group.id,
        name: group.subject || group.id,
        participants_count: Array.isArray(group.participants) ? group.participants.length : 0,
      }))
      .sort((a, b) => a.name.localeCompare(b.name));

    res.json({ success: true, groups: data });
  } catch (error) {
    res.status(500).json({ success: false, groups: [], message: error.message });
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

app.post('/reset', async (_req, res) => {
  try {
    await closeSocket();
    clearRuntimeState('resetting');
    await fs.rm(authDir, { recursive: true, force: true });
    console.log(`WhatsApp auth session reset: ${authDir}`);
    status = 'connecting';
    startConnect();

    res.json({
      success: true,
      status,
      connected: false,
      message: 'Sesi WhatsApp direset. QR baru sedang disiapkan.',
      auth_dir: authDir,
    });
  } catch (error) {
    lastError = error.message;
    status = 'error';
    res.status(500).json({ success: false, status, message: error.message, auth_dir: authDir });
  }
});

app.listen(port, () => {
  startConnect();
  console.log(`WhatsApp bridge listening on http://127.0.0.1:${port}`);
});
