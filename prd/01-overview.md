# 01 — Overview

## Ringkasan

`laraplate-react` adalah boilerplate pribadi untuk membangun admin dashboard dengan **Laravel 13 + Inertia 2 + React 19 + TypeScript + shadcn/ui + Tailwind CSS 4**. Ini adalah flavor SPA dari `laraplate` (Livewire). Keduanya berbagi arsitektur backend yang sama: modular, event-driven, secure by default.

**Masalah yang diselesaikan:**

- Proyek Inertia+React baru selama ini mulai dari nol — auth, RBAC, layout, komponen dibangun ulang tiap kali.
- Boilerplate lama (`laraplate`) hanya melayani stack Livewire; proyek berbasis React tidak punya fondasi setara.
- Konsistensi konvensi frontend (struktur Pages, form handling, permission check di client) belum terstandar.

## Prinsip Desain

1. **Self-contained & built-in** — auth, RBAC, sistem modular dibangun sendiri. Tanpa Breeze/Jetstream/Spatie/nwidart untuk fitur inti.
2. **Lazy modular** — folder dibuat hanya saat ada isinya. Tidak ada folder kosong / `.gitkeep`.
3. **Event-driven** — notifikasi & audit trail otomatis via event/listener, bukan manual di controller.
4. **shadcn/ui sebagai component system** — komponen di-copy ke repo (bukan dependency), full control, di-custom via Tailwind.
5. **Secure by default** — security headers, lockout, password policy aktif dari menit pertama.
6. **TypeScript strict** — semua kode frontend TypeScript, tipe untuk shared props Inertia.

## Tujuan (Goals)

- Clone → dashboard admin fungsional < 10 menit setup.
- Auth kustom: login, logout, register (toggle via Settings), reset password, remember me, profil.
- RBAC: user → roles → permissions + direct permission; permission efektif = union. Tersedia di backend (Gate/middleware) DAN frontend (`useCan()` hook).
- Sistem modular + generator yang menghasilkan Controller Inertia + Page `.tsx`.
- Notifikasi event-driven (bell) + Audit trail forensik immutable.
- Settings key-value via UI tanpa deploy ulang.
- Component library shadcn/ui + komponen komposit (DataTable, ConfirmDialog, StatCard, dsb) + halaman showcase.
- Testing wajib: Pest (backend + `assertInertia`), coverage ≥ 80%.

## Non-Goals v1

- Bukan CMS / bukan saingan Filament–Nova.
- Multi-tenancy, 2FA/TOTP, impersonation, queue monitor UI, global search, i18n → roadmap v2.
- SSR Inertia → opsional, off by default v1 (lihat 04-frontend).
- Mobile app / API publik (Sanctum) → v2 bila dibutuhkan.

## Persona

| Persona | Kebutuhan |
|---|---|
| Developer tim | Scaffold modul cepat, konvensi jelas, komponen siap pakai, tipe TypeScript lengkap |
| Admin aplikasi | Kelola user/role/permission, notifikasi, UI responsif |
| Super Admin | Akses penuh, bypass permission check |
| Security/Ops | Audit trail forensik, dependency scan di CI |

## Tech Stack

- **Backend:** Laravel 13.x, PHP 8.3+ (target 8.5)
- **Database:** MySQL/MariaDB default, kompatibel PostgreSQL
- **Auth:** guard `web` kustom session-based, tanpa starter kit
- **Frontend:** Inertia.js 2.x, React 19, TypeScript (strict), Tailwind CSS 4, shadcn/ui, Vite
- **Tabel data:** TanStack Table v8 (di dalam wrapper DataTable)
- **Form:** Inertia `useForm` (default); react-hook-form + zod hanya untuk form kompleks multi-step
- **Icons:** lucide-react
- **Testing:** Pest 4 + PHPUnit 12 (`assertInertia` untuk page props); Vitest opsional untuk util frontend murni
- **Queue:** database driver default (listener async), kompatibel Redis
- **Tooling:** Pint, Larastan v3 (level 7→max), ESLint + Prettier, GitHub Actions CI
- **Fitur Laravel 13 yang dipakai:** PHP attributes `#[Middleware]` / `#[Authorize]` di controller (opsional, bertahap), `Queue::route()` untuk routing job terpusat
