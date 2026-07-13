# 04 — Frontend (Inertia 2 + React 19 + TypeScript + shadcn/ui)

## Setup Dasar

- Scaffold dari starter kit React resmi Laravel (`laravel new` → React) lalu strip yang tidak dipakai; atau manual: `@inertiajs/react`, `react`, `react-dom`, TypeScript strict, Vite plugin laravel + react.
- shadcn/ui init: style default, base color zinc/slate (putuskan sekali, konsisten), CSS variables ON. Komponen di `resources/js/components/ui/` — milik repo, boleh diedit.
- Path alias `@/` → `resources/js/`.
- SSR: **OFF di v1** (dashboard admin di balik login, tidak butuh SEO). Struktur tetap SSR-ready (jangan akses `window` saat render awal tanpa guard).

## Layouts

- `layouts/app-layout.tsx` — sidebar (shadcn Sidebar block: collapsible, off-canvas mobile) + topbar (profil dropdown, notification bell, theme toggle) + content + footer. Persistent layout Inertia (`Page.layout = ...`) agar sidebar tidak remount antar navigasi.
- `layouts/guest-layout.tsx` — halaman auth terpusat.
- Menu sidebar: didefinisikan di satu file `lib/navigation.ts` dengan field `permission?: string`; difilter `useCan()` saat render.
- Dark mode: class strategy Tailwind, toggle simpan di localStorage (guard SSR), sinkron `prefers-color-scheme`.

## Shared Props & Types

`types/index.d.ts` mendefinisikan dan WAJIB dijaga sinkron dengan `HandleInertiaRequests`:

```ts
interface SharedProps {
  auth: {
    user: { id: number; uuid: string; name: string; email: string; roles: string[] } | null;
    permissions: string[];        // slug permission efektif (union role + direct)
  };
  flash: { success?: string; error?: string };
  settings: Record<string, string | number | boolean>;  // hanya is_public
  app: { name: string };
}
```

## Permission Hook

```ts
// hooks/use-can.ts
const { auth } = usePage<SharedProps>().props;
can('user.create')        // true jika super-admin ATAU slug ada di auth.permissions
canAny([...]); canAll([...]);
```

Aturan: `useCan` HANYA untuk show/hide UI. Enforcement tetap di backend (middleware + `authorize()`).

## Mapping Komponen (Blade lama → baru)

| Lama (Blade/Livewire) | Baru | Sumber |
|---|---|---|
| `<x-modal>`, confirm-modal | `Dialog`, `AlertDialog` + wrapper `components/confirm-dialog.tsx` | shadcn |
| Toast (Livewire dispatch) | `Sonner` — dipicu otomatis dari `flash` di app-layout | shadcn |
| `<x-alert>` | `Alert` | shadcn |
| `<x-card>`, `<x-stat>` | `Card` + `components/stat-card.tsx` | shadcn + komposit |
| DataTable Livewire | `components/data-table/` — TanStack Table v8 (lihat bawah) | komposit |
| `<x-form.*>` | `Input`, `Select`, `Checkbox`, `Textarea`, `Switch`, `Label` + `components/form-field.tsx` (label+error+input) | shadcn + komposit |
| `<x-button>` | `Button` (variants) | shadcn |
| `<x-badge>` | `Badge` | shadcn |
| `<x-dropdown>` | `DropdownMenu` | shadcn |
| `<x-breadcrumb>` | `Breadcrumb` | shadcn |
| `<x-pagination>` | `components/pagination.tsx` — konsumsi Laravel paginator links, navigasi via Inertia `Link` | komposit |
| `<x-empty>` | `components/empty-state.tsx` | komposit |
| Notification bell (Livewire) | `components/notification-bell.tsx` — `Popover` + list + partial reload polling | komposit |
| Sidebar nav | shadcn `Sidebar` block + `lib/navigation.ts` | shadcn block |
| Icons blade | `lucide-react` | lib |

## DataTable (komponen kunci)

`components/data-table/` berbasis TanStack Table v8, **server-side**: sorting/search/pagination dikirim sebagai query string via `router.get()` (preserveState, preserveScroll, debounce search 300ms), backend memproses lewat helper `BaseService` (port pola query dari DataTable Livewire lama).

Fitur wajib v1: search global, sort per kolom, per-page selector (10/25/50/100, reset page saat ganti), row actions slot (dropdown), empty state, loading state (`router` progress), kolom typed via `ColumnDef<T>`.

## Form Handling

- Default: Inertia `useForm` — errors otomatis dari Laravel Form Request, `form.processing` untuk disable tombol.
- Form kompleks (multi-step / dependent fields): react-hook-form + zod, submit tetap via `router.post`.
- Konvensi: satu halaman form `create.tsx`/`edit.tsx` berbagi komponen `form.tsx` per resource.

## Halaman per Modul (pages/)

```
auth/        login, register, forgot-password, reset-password, profile
dashboard/   index
users/       index, create, edit (+ form.tsx shared)
access/      roles/index, roles/form, permissions/index
notifications/ index
audit-logs/  index, show (diff view)
settings/    index (tabs per group)
showcase     semua komponen didemokan (dev only route)
```

## Error Pages

403/404/419/500 tetap Blade standalone tanpa Vite (lihat 03 F8). Di dalam SPA, error validasi & flash ditangani Inertia; error 419 di request Inertia → interceptor tampilkan dialog "sesi kadaluarsa" + tombol reload.

## Konvensi Frontend

- File & folder: kebab-case (`stat-card.tsx`); komponen export PascalCase.
- Tidak ada `any`; props halaman diketik per halaman (`interface Props`).
- Jangan tambah UI library lain (MUI/AntD/HeroUI) — semua dari shadcn/komposit.
- Komponen komposit baru → wajib ditambahkan ke showcase.
- `cn()` dari `lib/utils` untuk kombinasi class; jangan string concat manual.
