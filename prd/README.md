# PRD — laraplate-react

**Laravel Admin Dashboard Boilerplate (Laravel 13 + Inertia 2 + React + shadcn/ui)**

| | |
|---|---|
| **Nama Produk** | laraplate-react (kerja — bebas rename) |
| **Pemilik** | Hary |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 13 Juli 2026 |
| **Status** | Draft — siap eksekusi |
| **Relasi** | Port dari `ehary-devs/laraplate` (Livewire flavor). Backend ~70% reusable. Repo lama tetap hidup untuk proyek Blade/Livewire. |

## Cara membaca dokumen ini

PRD dipecah per domain agar AI agent (Claude Code) cukup membaca file yang relevan dengan task-nya:

| File | Isi | Baca saat |
|---|---|---|
| [01-overview.md](01-overview.md) | Ringkasan, tujuan, non-goals, persona, tech stack | Mulai project / butuh konteks umum |
| [02-architecture.md](02-architecture.md) | Struktur modular, direktori, aturan lintas modul, skema DB | Bikin/ubah modul, migration |
| [03-backend-features.md](03-backend-features.md) | Spesifikasi F1–F10 sisi backend (Auth, RBAC, Notification, Security, AuditLog, Settings) | Kerja di Services/Models/Events |
| [04-frontend.md](04-frontend.md) | Inertia setup, layouts, shadcn component mapping, permission hook, DataTable | Kerja di resources/js |
| [05-generator.md](05-generator.md) | Generator `module:make` + sub-generator, stub Inertia | Kerja di Console/Commands + stubs |
| [06-conventions.md](06-conventions.md) | Konvensi kode, testing (Pest + assertInertia), Definition of Done, CI | Setiap PR |
| [07-migration-plan.md](07-migration-plan.md) | Rencana port dari laraplate lama: apa yang di-copy, edit, rewrite | Fase awal porting |
| [08-roadmap.md](08-roadmap.md) | Fase eksekusi R0–R6, roadmap v2, risiko | Planning / update status |

## Aturan untuk AI agent

- Baca `01-overview.md` sekali di awal sesi, lalu HANYA file yang relevan dengan task.
- Jangan mengubah keputusan arsitektur di `02-architecture.md` tanpa persetujuan eksplisit.
- Setiap fitur baru wajib mengikuti Definition of Done di `06-conventions.md`.
