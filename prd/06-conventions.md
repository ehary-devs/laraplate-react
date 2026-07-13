# 06 — Konvensi, Testing, Definition of Done

## Konvensi Backend

- Controller tipis: logika di Service, validasi di Form Request.
- Naming: permission `resource.action`; route name `module.resource.action`.
- Mass assignment: eksplisit `$fillable` (bukan `$guarded = []`).
- Soft delete: `User`, `Role`, `Permission`. Model lain case-by-case, catat di README modul.
- `DB::raw()` hanya bila Eloquent tak bisa — wajib komentar alasan; binding wajib; string concat di query = block PR.
- PHPDoc pada service & method publik; array shape di PHPDoc.
- Pint (preset Laravel) sebelum commit; Larastan level 7 (target naik ke max).

## Konvensi Frontend

- TypeScript strict, tanpa `any`.
- ESLint + Prettier (config dibawa boilerplate); `npm run lint` wajib hijau di CI.
- File kebab-case; komponen PascalCase; hooks `use-*.ts`.
- Semua UI dari shadcn/komposit — dilarang menambah UI library lain.
- Komponen komposit baru wajib masuk `showcase`.

## Testing (Pest) — WAJIB

Kebijakan non-negotiable: fitur baru tanpa test = tidak di-merge.

| Lapisan | Cara |
|---|---|
| Service/unit | Pest unit test, factory-based |
| HTTP + halaman | Feature test + `assertInertia`: component benar, props ada & bentuknya benar |
| Otorisasi | Assert 403 tanpa permission, 200 dengan permission; super-admin bypass |
| Event/listener | `Event::fake()` / assert row `notifications` & `audit_logs` |
| Security | Header hadir, lockout setelah N gagal, password history ditolak |
| Frontend murni (util/hook) | Vitest — hanya untuk logic non-trivial (mis. `useCan`), bukan snapshot UI |

Target: coverage backend ≥ 80% (gate CI). Port ~30 test file lama; test Livewire ditulis ulang sebagai `assertInertia`.

## Definition of Done per Fitur

1. Konvensi terpenuhi (controller tipis, service, request).
2. Pest test menyertai & hijau di CI; `assertInertia` untuk halaman baru.
3. Dokumentasi: PHPDoc + README modul + showcase (bila komponen UI) + tipe di `types/`.
4. Pint, PHPStan, ESLint, `tsc --noEmit` lulus.
5. `composer audit` + `npm audit` lulus (block high/critical).
6. Reviewed & approved via PR (conventional commits, branch protection).

## CI (GitHub Actions)

Job berurutan: `audit` (composer+npm audit) → `lint` (pint --test, phpstan, eslint, tsc) → `test` (pest --coverage, gate 80%) → `build` (vite build harus sukses). Merge diblok bila ada yang merah.
