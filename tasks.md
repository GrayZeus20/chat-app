# Tasks

## Plan — Pusher secret out of public repo (2026-10-07)
- [x] 1. Rewrite `config.php`: read PUSHER_* from `env.php`/getenv, placeholders fallback, `isPusherConfigured()` guards empty
- [x] 2. Append PUSHER_* defines to local `env.php` (gitignored) — local runtime stays up
- [x] 3. Extend `.github/workflows/deploy.yml`: write PUSHER_* into generated `env.php` from GitHub secrets
- [x] 4. Verify: `php -l` both files + runtime dump with env + runtime dump without env (placeholder path) + YAML parse
- [x] 5. Report: rotate Pusher secret (mandatory, history public), add 4 GH secrets, optional history purge

## Purge + chat test (2026-10-07)
- [x] git filter-repo --replace-text: secret → REMOVED_BY_PURGE, HEAD ac2be34, force push done
- [x] Fresh clone verify: 0 secret hits di seluruh history
- [x] Fix: index.php tambah <script> pusher SDK (sebelumnya `Pusher is not defined`)
- [x] Fix: api.php 3 catch Pusher silent → error_log (no silent swallow)
- [x] Chat test local: register → send → DB rows 26-30 → receiver view OK → realtime live OK (test3+test4 diterima Pusher live)
- [x] Cleanup: .playwright-mcp, /tmp/purge, /tmp/verify-clone, pusher_probe.php dihapus; env.php restored

## Findings (audit)
- LEAK: `PUSHER_APP_SECRET` in `config.php`, commits `53163f0`..`7e491ad` (HEAD), repo public github.com/GrayZeus20/chat-app
- CLEAN: DB creds (`env.php`) never in git history — exact-string scan all commits: 0 hits
- CLEAN: `env.php` / `.env` / `vendor/` gitignored, `database.sql` = schema only
