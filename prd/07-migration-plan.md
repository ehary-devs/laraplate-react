# 07 — Migration Plan (dari `ehary-devs/laraplate`)

Strategi: **fresh scaffold + copy-port backend**, bukan clone-then-ubah. Repo lama tetap hidup sebagai flavor Livewire.

## Kategori A — COPY apa adanya (hampir nol perubahan)

| Sumber | Catatan |
|---|---|
| `app/Core/` semuanya KECUALI `Livewire/` | BaseModel, BaseService, HasPublicId, HasRolesAndPermissions, IdempotentListener, MaskSensitiveDataProcessor, NotInPasswordHistory |
| `app/Models/` (User, PasswordHistory) | — |
| `app/Providers/ModuleServiceProvider.php` | — |
| `Modules/*/{Services,Models,Events,Listeners,Mail,Requests,Middleware}` | Semua modul |
| `Modules/*/module.json` + ServiceProvider | Hapus `loadViewsFrom` |
| `database/migrations`, `seeders`, `factories` | — |
| `config/security.php`, `config/seeder.php` | — |
| Test service-level & unit (HasRolesAndPermissions, AuthService, AccessService, NotificationService, Security/*) | — |
| `.github/workflows` | Tambah step eslint/tsc/vite build |
| `pint.json`, `phpstan.neon`, `.editorconfig` | — |

## Kategori B — EDIT ringan

| Sumber | Perubahan |
|---|---|
| Semua Controller di `Modules/*/Controllers` | `return view(...)` / redirect Livewire → `Inertia::render('modul/halaman', [...])`; flash via `->with('success', ...)` |
| `Modules/*/routes.php` | Hapus route Livewire; nama & middleware tetap |
| `bootstrap/app.php` / middleware | Daftarkan `HandleInertiaRequests` |
| Test controller-level | Assert view → `assertInertia` |
| `CLAUDE.md` | Hapus rules Livewire; tambah rules Inertia/React/shadcn/TypeScript |
| `README.md` | Update stack & quick start |

## Kategori C — REWRITE (frontend)

| Lama | Baru |
|---|---|
| `Modules/*/views/**` + `Modules/*/Livewire/**` | `resources/js/pages/{modul}/**` |
| `resources/views/components/**` (25 komponen) | shadcn + komposit (mapping di 04-frontend) |
| `app/Core/Livewire/{BaseComponent,DataTable}.php` | Pola query DataTable → helper di BaseService; UI → `components/data-table/` (TanStack) |
| `resources/views/layouts/*` | `layouts/app-layout.tsx`, `guest-layout.tsx` |
| Stub generator Livewire/Blade | Stub Inertia + page `.tsx` (lihat 05-generator) |
| `showcase.blade.php` | `pages/showcase.tsx` |

## Kategori D — BUANG

- Dependency: `livewire/livewire`, `livewire/blaze`, Alpine.
- `resources/views/**` kecuali: root `app.blade.php` (Inertia mount, satu-satunya Blade utama), `errors/*` (dipertahankan standalone).
- Blade directives RBAC.

## Urutan Eksekusi Port

1. Scaffold fresh (R0 di 08-roadmap) → commit awal.
2. Copy Kategori A → `php artisan migrate --seed` hijau, test unit hijau.
3. Edit Kategori B untuk modul Auth → halaman auth React → login jalan end-to-end.
4. Layout + navigation + shared props + `useCan`.
5. Modul per modul: Dashboard → Access → User → Notification → Settings → AuditLog. Tiap modul: controller edit → pages → test `assertInertia` hijau → baru lanjut.
6. Generator stubs + test generator.
7. Showcase + docs + CI penuh.

## Definisi Selesai Port

- Paritas fitur dengan laraplate lama (F1–F10 semua ✅).
- Seluruh test hijau (target ≥ 210 setara repo lama), coverage ≥ 80%.
- `module:make Blog` menghasilkan modul React fungsional.
