<?php
/**
 * Database-backed PHP sessions — required on serverless hosts (Vercel),
 * where the local filesystem is temporary and per-request. Enabled when
 * SESSION_DRIVER=db. Needs a `sessions` table (in both schema files).
 */
function cheapa_db_sessions(PDO $pdo): void {
    $handler = new class($pdo) implements SessionHandlerInterface {
        private PDO $pdo;
        public function __construct(PDO $pdo) { $this->pdo = $pdo; }
        public function open($path, $name): bool { return true; }
        public function close(): bool { return true; }
        public function read($id): string {
            $s = $this->pdo->prepare("SELECT data FROM sessions WHERE id = ?");
            $s->execute([$id]);
            $row = $s->fetch();
            return $row ? (string)$row['data'] : '';
        }
        public function write($id, $data): bool {
            if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
                $sql = "INSERT INTO sessions (id, data, updated_at) VALUES (?, ?, now())
                        ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, updated_at = now()";
            } else {
                $sql = "INSERT INTO sessions (id, data, updated_at) VALUES (?, ?, NOW())
                        ON DUPLICATE KEY UPDATE data = VALUES(data), updated_at = NOW()";
            }
            return $this->pdo->prepare($sql)->execute([$id, $data]);
        }
        public function destroy($id): bool {
            return $this->pdo->prepare("DELETE FROM sessions WHERE id = ?")->execute([$id]);
        }
        public function gc($max): int {
            $this->pdo->prepare("DELETE FROM sessions WHERE updated_at < (NOW() - INTERVAL " .
                (($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') ? "'1 day'" : "1 DAY") . ")")->execute();
            return 0;
        }
    };
    session_set_save_handler($handler, true);
}
