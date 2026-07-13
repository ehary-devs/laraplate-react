# 05 — Generator Modular

Kontrak command SAMA dengan laraplate lama; yang berubah hanya stub (Inertia, bukan Livewire/Blade).

## Command

| Perintah | Output |
|---|---|
| `module:make Blog` | `Modules/Blog/{module.json, BlogServiceProvider.php, routes.php, Controllers/BlogController.php, Models/Blog.php}` + `resources/js/pages/blog/index.tsx` |
| `module:make-controller Blog Post` | Controller Inertia (`Inertia::render('blog/posts/index')`) |
| `module:make-model Blog Post` | Model extends BaseModel (+ opsi `--migration --factory`) |
| `module:make-service Blog PostService` | Service extends BaseService |
| `module:make-request Blog StorePostRequest` | Form Request |
| `module:make-event Blog PostCreated` | Event |
| `module:make-listener Blog NotifyPost --event=PostCreated` | Listener |
| `module:make-test Blog PostCrudTest` | Pest feature test dengan contoh `assertInertia` |
| `module:make-page Blog posts/create` | **[BARU]** Page `.tsx` dari stub (typed Props + layout) |
| `module:list` / `module:remove Blog` | Status modul / hapus bersih (folder + pages/blog + registrasi) |

## Prinsip Lazy (tidak berubah)

- Scaffold minimal — tidak ada folder kosong, tidak ada `.gitkeep`.
- `module:remove` juga menghapus `resources/js/pages/{modul}/` — tanpa sisa.

## Stub yang Wajib Diupdate dari Repo Lama

1. **Controller stub** — hapus view/Livewire, ganti `Inertia::render('{modul}/{resource}/index', [...])`, sertakan contoh `authorize()`.
2. **Routes stub** — named route `module.resource.action`, contoh middleware `permission:`.
3. **Page stub (baru)** — `.tsx`: interface Props, `Head` dari Inertia, persistent `AppLayout`, contoh `useCan`.
4. **Test stub** — Pest + `assertInertia(fn (Assert $page) => $page->component('blog/index')->has('posts'))`.
5. Hapus stub Livewire component + Blade view dari generator lama.

## Acceptance

1. `module:make Blog` → route `/blog` langsung merender halaman React tanpa edit manual apa pun.
2. Tidak ada folder kosong yang dihasilkan.
3. `module:remove Blog` → `git status` bersih dari jejak Blog (backend + frontend).
4. Semua generator punya Pest test (port dari `MakeModuleTest`, `SubGeneratorTest`, `RemoveModuleTest`, `ModuleListTest` — update ekspektasi stub).
