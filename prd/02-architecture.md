# 02 — Arsitektur

## Arsitektur Modular (backend)

Identik dengan `laraplate` lama. Setiap fitur = modul mandiri di `Modules/`, terdaftar via ServiceProvider sendiri dengan auto-discovery internal (`app/Providers/ModuleServiceProvider.php` membaca `module.json`).

**Aturan modular:**

- Modul TIDAK mengakses internal modul lain langsung — komunikasi lintas modul via **Service (interface)** atau **Event**.
- Setiap modul punya `module.json` (name, description, version, active, dependencies).
- Modul inti (`Auth`, `Access`, `Security`) selalu aktif, tidak bisa dimatikan.
- Lazy: folder dibuat saat ada isinya. Flat dulu, nest saat > 3–4 file. Shared code di `app/Core/`.
- Migration global di `database/migrations/` (bukan per modul) untuk proyek dasar.

## Keputusan: Pages TERPUSAT, bukan per-modul

Halaman React TIDAK ditaruh di dalam `Modules/{X}/`. Semua di `resources/js/pages/{modul}/`.

Alasan: Vite code-splitting & `resolvePageComponent` bekerja natural dengan satu root glob; menghindari konfigurasi alias per modul; frontend developer cukup kerja di satu tree. Konsekuensi: nama folder di `pages/` WAJIB match nama modul lowercase (`Modules/Access` → `pages/access/`).

## Struktur Direktori

```
laraplate-react/
├── app/
│   ├── Core/
│   │   ├── Models/BaseModel.php
│   │   ├── Services/BaseService.php
│   │   ├── Traits/HasPublicId.php
│   │   ├── Traits/HasRolesAndPermissions.php
│   │   ├── Listeners/IdempotentListener.php
│   │   ├── Logging/MaskSensitiveDataProcessor.php
│   │   └── Rules/NotInPasswordHistory.php
│   ├── Console/Commands/          # module:make + sub-generator (stub Inertia)
│   ├── Http/Middleware/HandleInertiaRequests.php   # shared props
│   └── Providers/
├── Modules/
│   ├── Auth/        # Controllers, Services, Events, Listeners, Mail, Requests, routes.php
│   ├── Access/      # RBAC
│   ├── User/
│   ├── Notification/
│   ├── Dashboard/
│   ├── Security/    # Middleware
│   ├── AuditLog/
│   └── Settings/
├── resources/js/
│   ├── app.tsx
│   ├── components/ui/        # shadcn components (di-copy, milik repo)
│   ├── components/           # komponen komposit (data-table, confirm-dialog, stat-card, ...)
│   ├── layouts/              # app-layout.tsx, guest-layout.tsx
│   ├── hooks/                # use-can.ts, use-toast (sonner), ...
│   ├── lib/                  # utils.ts (cn), route helpers
│   ├── types/                # index.d.ts — SharedProps, User, Role, Permission, ...
│   └── pages/
│       ├── auth/             # login.tsx, register.tsx, forgot-password.tsx, ...
│       ├── dashboard/
│       ├── access/           # roles/, permissions/
│       ├── users/
│       ├── notifications/
│       ├── audit-logs/
│       ├── settings/
│       └── showcase.tsx
├── database/{migrations,seeders,factories}/
├── tests/
└── vite.config.ts
```

Catatan: modul TIDAK lagi punya folder `views/` maupun `Livewire/`. Controller me-render `Inertia::render('users/index', [...])`.

## Skema Database

Sama persis dengan laraplate lama (port migrations apa adanya):

```
users               (id, uuid, name, email, email_verified_at, password, remember_token, timestamps)
roles               (id, uuid, name, slug, description, timestamps)
permissions         (id, uuid, name, slug, group, description, timestamps)
role_user           (role_id, user_id)
permission_role     (permission_id, role_id)
permission_user     (permission_id, user_id)
notifications       (id, uuid, type, title, message, level, notifiable_*, causer_*, data, read_at, timestamps)
audit_logs          (id, user_id, event, auditable_type, auditable_id, old_values, new_values, ip_address, user_agent, url, tags, timestamps)
settings            (id, key, value, type, group, label, description, is_public, options, is_encrypted, timestamps)
password_histories  (id, user_id, password, created_at)
password_reset_tokens / sessions (Laravel default)
```

**Keputusan UUID:** PK bigint `id` untuk join; kolom `uuid` sebagai public identifier di URL (trait `HasPublicId`).

## Alur Event (tidak berubah)

```
Controller/Service ──fires──► Event (mis. UserCreated)
                                  │
                   ┌──────────────┼──────────────┐
                   ▼              ▼              ▼
          LogNotification     LogAudit      listener lain
          (ShouldQueue)       (ShouldQueue)
                   ▼              ▼
        INSERT notifications  INSERT audit_logs
```

## Inertia Data Flow

- **Shared props** via `HandleInertiaRequests::share()`: `auth.user` (id, uuid, name, email, roles slugs), `auth.permissions` (array slug permission efektif — dari cache RBAC), `flash` (success/error/toast), `settings.public` (setting `is_public=true`), `app.name`.
- Permission dikirim sebagai daftar slug, BUKAN object lengkap — kecil & cacheable.
- Frontend cek via hook `useCan('user.create')`; backend TETAP source of truth (middleware `permission:` + `authorize()`); frontend check hanya untuk show/hide UI.
- Flash → Sonner toast otomatis via listener di `app-layout.tsx`.
