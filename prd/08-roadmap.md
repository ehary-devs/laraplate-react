# 08 — Roadmap & Risiko

## Fase Eksekusi

| Fase | Lingkup | Output / Acceptance | Status |
|---|---|---|---|
| **R0 — Fondasi** | Laravel 13 + Inertia 2 + React 19 + TS + Tailwind 4 + shadcn init; layout app/guest; dark mode; HandleInertiaRequests; types dasar | `composer run dev` → dashboard kosong dengan sidebar/topbar jalan | 📋 |
| **R1 — Backend Port** | Copy Kategori A (07-migration-plan): Core, Modules backend, migrations, seeders, config, test unit | `migrate --seed` hijau; test unit/service hijau | 📋 |
| **R2 — Auth** | Controller Auth → Inertia; pages auth; lockout & password policy terverifikasi via UI | Login/logout/register/reset end-to-end + test | 📋 |
| **R3 — RBAC & Shared Props** | Share `auth.permissions`; `useCan`; sidebar filtered; middleware terpasang | Editor tidak lihat & tidak bisa akses menu terlarang | 📋 |
| **R4 — CRUD Inti** | DataTable komposit; modul User + Access UI; Dashboard widgets | CRUD user + role/permission dari UI + test `assertInertia` | 📋 |
| **R5 — Notification, Settings, AuditLog** | Bell + polling; Settings tabs; AuditLog index/detail/export | Acceptance F4/F9/F10 (03-backend-features) terpenuhi | 📋 |
| **R6 — Generator, Showcase, Polish** | Stub Inertia + `module:make-page`; showcase; CLAUDE.md; README; CI penuh; coverage ≥ 80% | `module:make Blog` → modul React jalan; siap jadi template org | 📋 |

## Roadmap v2

2FA/TOTP · Impersonation · Export Excel per modul · Queue Monitor UI · Global Search (Command palette shadcn `Cmd+K` — kandidat kuat karena murah di stack ini) · i18n · Multi-tenancy · Reverb/Echo real-time (ganti polling bell) · SSR opsional · API Sanctum.

## Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Scope migrasi membengkak (godaan redesign UI) | Kunci paritas fitur dulu (F1–F10); improvement UI setelah v1 tag |
| DataTable server-side kompleks | Port pola query dari DataTable Livewire lama; fitur v1 dikunci (search/sort/paginate/per-page) |
| Shared props membengkak → payload tiap navigasi berat | Hanya slug permission + setting publik; data berat per halaman, bukan shared |
| Permission cache stale di client | Client hanya show/hide; enforcement backend. Perubahan permission → efektif setelah navigasi berikutnya (acceptable) |
| CSP break Vite dev / Radix | Nonce via `Vite::useCspNonce()`; `style-src 'unsafe-inline'` dipertahankan; test staging |
| Test count turun drastis pasca port | Gate CI coverage 80% sejak R1; test Livewire lama jadi checklist paritas |
| Dua boilerplate (Livewire + React) bikin drift arsitektur | Perubahan arsitektur backend wajib di-mirror ke kedua repo; catat di README keduanya |

## Pertanyaan Terbuka

- Nama final repo: `laraplate-react` vs `reactplate` vs `laraplate` v2 branch? → putuskan sebelum R0.
- Base color shadcn (zinc/slate/neutral) → putuskan di R0, konsisten selamanya.
- `audit:prune` via Scheduler otomatis (usulan: ya, daily) → konfirmasi sebelum R5.
- Soft-deleted user boleh login? (usulan: tidak — cek di `AuthService::attempt`) → konfirmasi sebelum R2.
