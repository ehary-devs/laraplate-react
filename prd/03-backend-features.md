# 03 — Backend Features (F1–F10)

Backend identik dengan `laraplate` (Livewire flavor). Perubahan hanya di lapisan Controller (return `Inertia::render()`) dan hal yang ditandai **[DELTA]**. Detail lengkap tiap fitur ada di README per modul repo lama — file ini merangkum kontrak yang WAJIB dipertahankan.

## F1 — Auth (Modul `Auth`)

Guard `web` kustom, session-based, tanpa starter kit. Logika di `AuthService`, controller tipis.

- Login (email+password, remember me, throttling), Logout (invalidate + regenerate), Register (toggle via Settings `auth.register_enabled`), Lupa/reset password, Ganti password, Profil.
- Events: `UserLoggedIn`, `UserLoggedOut`, `UserRegistered`, `PasswordReset`, `AccountLocked`.
- **[DELTA]** Controller render halaman: `auth/login`, `auth/register`, `auth/forgot-password`, `auth/reset-password`, `auth/profile`. Validation error otomatis lewat Inertia error bag → `useForm().errors`.

**Acceptance:** login/logout jalan, sesi regenerate, gagal login kena throttle, semua flow tercatat sebagai notifikasi + audit.

## F2 — RBAC (Modul `Access`)

Model: user → roles → permissions, DAN user → permissions (direct). Efektif = union.

- Trait `HasRolesAndPermissions`: `assignRole()`, `removeRole()`, `hasRole()`, `givePermissionTo()`, `revokePermissionTo()`, `hasPermissionTo()`, `getAllPermissions()`.
- `super-admin` bypass via `Gate::before`.
- Cache permission per-user, invalidation version-based O(1) via pivot observer (port apa adanya).
- Middleware: `role:admin`, `permission:user.create`. Gate terdaftar otomatis → `authorize()`, `$user->can()`.
- **[DELTA]** Blade directives (`@role`, `@permission`) DIHAPUS — digantikan hook `useCan()` di frontend (lihat 04-frontend). Permission efektif dishare via `HandleInertiaRequests`.
- **[DELTA]** Boleh adopsi bertahap atribut Laravel 13 `#[Authorize]` / `#[Middleware]` di controller modul baru; modul hasil port tetap pakai middleware route dulu.

**Acceptance:** editor tanpa `user.delete` tidak bisa hapus; diberi direct permission → langsung bisa tanpa ubah role; tombol delete di UI hilang/muncul mengikuti `useCan()`.

## F3 — Component Library

Pindah sepenuhnya ke shadcn/ui + komponen komposit. Spesifikasi di **04-frontend.md**. Kontrak yang dipertahankan: ada halaman `showcase` yang mendemokan semua komponen.

## F4 — Notification (Modul `Notification`)

Event-driven, otomatis via listener `ShouldQueue`. Skema tabel `notifications` tidak berubah (type, title, message, level, notifiable morphs, causer morphs, data json, read_at).

- `NotificationService`: create, markRead, markAllRead, unreadCount, listFor(user).
- **[DELTA]** UI bell: komponen React di topbar. Data awal via shared props ringan (`unread_count`) + halaman `notifications/index`. Refresh: Inertia partial reload polling (`router.reload({ only: ['unreadCount'] })`, interval dari Settings `notification.badge_poll_seconds`, 0 = off). Reverb/Echo tetap roadmap v2.

## F5 — Sistem Modular & Generator

Kontrak command sama; stub berubah ke Inertia. Detail di **05-generator.md**.

## F6 — User CRUD & Dashboard

- CRUD user + manajemen role/permission dari UI (`users/index`, `users/form`, `access/roles`, `access/permissions`).
- **[DELTA]** UserTable/UserForm Livewire → halaman React + DataTable + `useForm`.
- Dashboard: stat widgets + aktivitas terkini (props dari `DashboardController`).

## F8 — Security Hardening (Modul `Security`)

Port apa adanya, dengan satu penyesuaian CSP:

- `SecureHeaders` middleware: CSP, X-Frame-Options DENY, nosniff, Referrer-Policy, Permissions-Policy, HSTS (production only). Configurable via `config/security.php`.
- **[DELTA] CSP:** tidak ada lagi Alpine inline handler. `script-src 'self' 'nonce-{token}'` tetap (nonce via `Vite::useCspNonce()` untuk tag Vite di root Blade). React event handler bukan inline script, jadi aman. `style-src` tetap `'unsafe-inline'` (dibutuhkan Radix/shadcn untuk inline style positioning).
- Account lockout: 5 gagal / 10 menit → lock 15 menit (configurable, cache-based), email + event `AccountLocked`, unlock manual oleh super-admin.
- Password policy: min 8, mixedCase, numbers; produksi + symbols + uncompromised. History 3 password terakhir (`password_histories`, rule `NotInPasswordHistory`).
- Session hardening: http_only, same_site=lax, secure di produksi, lifetime 120m, concurrent session limit (toggle).
- Log masking: `MaskSensitiveDataProcessor` di channel single/daily.
- **[DELTA]** Custom error pages 403/404/419/500: tetap Blade standalone TANPA Vite dependency (aman saat server error) — TIDAK dijadikan halaman Inertia.
- CI: `composer audit` + `npm audit` block pada high/critical.

## F9 — AuditLog (Modul `AuditLog`)

Immutable forensic trail, terpisah dari Notification.

- Tabel `audit_logs` (user_id, event, auditable morphs, old_values, new_values, ip, user_agent, url, tags).
- Trait `Auditable` di model (User, Role, Permission, Setting) → auto-diff pada created/updated/deleted.
- `AuditService::record()` untuk aksi non-model (login, logout, permission grant/revoke, setting update).
- Immutable: tidak ada route DELETE, tidak ada method delete di UI.
- Retention: `audit:prune` (default 365 hari) — dijalankan via Scheduler.
- **[DELTA]** UI: `audit-logs/index` (DataTable + filter user/event/model/date-range) dan detail diff old|new dalam dua kolom. Export CSV (permission `audit-log.export`).

## F10 — Settings (Modul `Settings`)

Key-value store DB + cache, ubah dari UI tanpa deploy ulang.

- Tabel `settings` termasuk kolom `options` (untuk tipe select) dan `is_encrypted` (nilai dienkripsi via `Crypt`).
- `SettingsService` singleton cached: `get/set/group/all` + helper `setting()`.
- Perubahan setting → event `SettingUpdated` → audit log.
- **[DELTA]** UI `settings/index`: tab per group (shadcn Tabs), form `useForm`, render input sesuai `type` (string→Input, boolean→Switch, integer→Input number, select→Select, json→Textarea).
- Setting `is_public=true` dishare ke semua halaman via `HandleInertiaRequests`.

Default settings dibawa seeder: `app.name`, `auth.register_enabled`, `auth.lockout_attempts`, `auth.lockout_minutes`, `auth.session_lifetime`, `auth.password_history`, `auth.concurrent_sessions`, `security.csp_enabled`, `security.hsts_enabled`, `notification.badge_poll_seconds`.
