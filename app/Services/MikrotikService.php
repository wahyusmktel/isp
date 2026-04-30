<?php

namespace App\Services;

class MikrotikService
{
    private mixed $socket = null;
    private bool $loggedIn = false;

    public function connect(string $host, int $port, string $username, string $password, int $timeout = 5): array
    {
        $this->socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if (!$this->socket) {
            return ['success' => false, 'message' => "Tidak dapat terhubung ke {$host}:{$port} — {$errstr}"];
        }

        stream_set_timeout($this->socket, $timeout);

        $result = $this->doLogin($username, $password);
        if (!$result['success']) {
            $this->close();
        }

        return $result;
    }

    public function getSystemResource(): array
    {
        return $this->parseOne($this->query('/system/resource/print'));
    }

    public function getPppoeOnlineCount(): int
    {
        return count($this->parseAll($this->query('/ppp/active/print')));
    }

    public function getPppoeActives(): array
    {
        return $this->parseAll($this->query('/ppp/active/print'));
    }

    public function getInterfaces(): array
    {
        return $this->parseAll($this->query('/interface/print'));
    }

    public function getPppoeSecrets(): array
    {
        return $this->parseAll($this->query('/ppp/secret/print'));
    }

    public function close(): void
    {
        if ($this->socket) {
            @fclose($this->socket);
            $this->socket   = null;
            $this->loggedIn = false;
        }
    }

    // ─── private ────────────────────────────────────────────────────────────

    private function doLogin(string $username, string $password): array
    {
        // RouterOS 6.43+ plain-text login
        $response = $this->query('/login', ['=name=' . $username, '=password=' . $password]);

        if (in_array('!done', $response, true)) {
            $this->loggedIn = true;
            return ['success' => true, 'message' => 'Login berhasil'];
        }

        // Older RouterOS: challenge–response MD5
        foreach ($response as $word) {
            if (str_starts_with($word, '=ret=')) {
                return $this->loginMd5($username, $password, substr($word, 5));
            }
        }

        return ['success' => false, 'message' => 'Login gagal: ' . $this->extractMsg($response)];
    }

    private function loginMd5(string $username, string $password, string $challenge): array
    {
        $hash     = '00' . md5(chr(0) . $password . pack('H*', $challenge));
        $response = $this->query('/login', ['=name=' . $username, '=response=' . $hash]);

        if (in_array('!done', $response, true)) {
            $this->loggedIn = true;
            return ['success' => true, 'message' => 'Login berhasil (MD5)'];
        }

        return ['success' => false, 'message' => 'Login MD5 gagal: ' . $this->extractMsg($response)];
    }

    private function query(string $command, array $attrs = []): array
    {
        $this->writeSentence($command, $attrs);
        return $this->readAll();
    }

    private function writeSentence(string $cmd, array $attrs): void
    {
        $this->writeWord($cmd);
        foreach ($attrs as $a) {
            $this->writeWord($a);
        }
        $this->writeWord(''); // end of sentence
    }

    private function writeWord(string $word): void
    {
        $n = strlen($word);
        if ($n < 0x80) {
            fwrite($this->socket, chr($n));
        } elseif ($n < 0x4000) {
            fwrite($this->socket, chr((($n | 0x8000) >> 8) & 0xFF) . chr($n & 0xFF));
        } elseif ($n < 0x200000) {
            fwrite($this->socket, chr((($n | 0xC00000) >> 16) & 0xFF)
                . chr(($n >> 8) & 0xFF) . chr($n & 0xFF));
        } else {
            fwrite($this->socket, chr(0xE0)
                . chr(($n >> 24) & 0xFF) . chr(($n >> 16) & 0xFF)
                . chr(($n >> 8) & 0xFF) . chr($n & 0xFF));
        }
        fwrite($this->socket, $word);
    }

    // Reads words from ALL sentences until !done / !fatal / !trap
    private function readAll(): array
    {
        $words = [];
        while (true) {
            $word = $this->readWord();
            if ($word === '') {
                // empty word = sentence terminator; keep reading
                if (!empty($words) && in_array(end($words), ['!done', '!fatal'], true)) {
                    break;
                }
                continue;
            }
            $words[] = $word;
            if (str_starts_with($word, '!trap')) {
                // drain rest of trap sentence
                while ($this->readWord() !== '') {}
                break;
            }
        }
        return $words;
    }

    private function readWord(): string
    {
        $len = $this->readLen();
        if ($len === 0) return '';
        $buf = '';
        while (strlen($buf) < $len) {
            $chunk = fread($this->socket, $len - strlen($buf));
            if ($chunk === false || $chunk === '') break;
            $buf .= $chunk;
        }
        return $buf;
    }

    private function readLen(): int
    {
        $b = ord(fread($this->socket, 1));
        if (($b & 0x80) === 0x00) return $b;
        if (($b & 0xC0) === 0x80) return (($b & 0x3F) << 8) | ord(fread($this->socket, 1));
        if (($b & 0xE0) === 0xC0) {
            return (($b & 0x1F) << 16) | (ord(fread($this->socket, 1)) << 8) | ord(fread($this->socket, 1));
        }
        if (($b & 0xF0) === 0xE0) {
            return (($b & 0x0F) << 24)
                | (ord(fread($this->socket, 1)) << 16)
                | (ord(fread($this->socket, 1)) << 8)
                | ord(fread($this->socket, 1));
        }
        // 0xF0 prefix — next 4 bytes are the full length
        return (ord(fread($this->socket, 1)) << 24)
            | (ord(fread($this->socket, 1)) << 16)
            | (ord(fread($this->socket, 1)) << 8)
            | ord(fread($this->socket, 1));
    }

    private function parseOne(array $words): array
    {
        $data = [];
        foreach ($words as $w) {
            if (str_starts_with($w, '=')) {
                $parts        = explode('=', ltrim($w, '='), 2);
                $data[$parts[0]] = $parts[1] ?? '';
            }
        }
        return $data;
    }

    private function parseAll(array $words): array
    {
        $rows = [];
        $cur  = null;
        foreach ($words as $w) {
            if ($w === '!re') {
                if ($cur !== null) $rows[] = $cur;
                $cur = [];
            } elseif (str_starts_with($w, '=') && $cur !== null) {
                $parts      = explode('=', ltrim($w, '='), 2);
                $cur[$parts[0]] = $parts[1] ?? '';
            }
        }
        if ($cur !== null && count($cur) > 0) {
            $rows[] = $cur;
        }
        return $rows;
    }

    private function extractMsg(array $words): string
    {
        foreach ($words as $w) {
            if (str_starts_with($w, '=message=')) return substr($w, 9);
        }
        return 'Kesalahan tidak diketahui';
    }
}
