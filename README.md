# Framework POS — Starter Kit 🚀

> A premium, framework-agnostic **Point of Sale, Order Management & Inventory** starter kit built with **Laravel + Vue 3**.

Clone → Konfigurasi `.env` → Deploy. Tidak ada hardcoded branding — semua nama bisnis, slogan, dan tagline dikontrol lewat environment variables.

---

## Tech Stack 🛠️

| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP 8.3+) |
| Frontend | Vue 3 + Inertia.js |
| Styling | TailwindCSS + Shadcn-vue |
| Icons | Lucide Vue / Radix Icons |
| Database | MySQL / SQLite |
| PWA | vite-plugin-pwa |

---

## Core Modules ✨

### 1. POS / Kasir
- **Matrix Pricing** — Harga dinamis berbasis multi-atribut (Size × Type, dll.)
- **Inline Customer** — Registrasi pelanggan baru langsung dari layar kasir
- **Keyboard Shortcuts** — `F2` Search · `F4` Bayar · `F9` Simpan
- **Pinned Services** — Akses cepat layanan terpopuler

### 2. Order Lifecycle
- **Status Flow** — `pending` → `diproses` → `selesai` → `diambil`
- **Multi-Payment** — Cash · QRIS · Transfer Bank
- **Dual Invoice** — Thermal 80mm + A4 PDF (nama bisnis otomatis dari config)

### 3. Inventory Control
- Tracking bahan baku & konsumabel
- Log pergerakan stok terintegrasi dengan transaksi

### 4. RBAC Security
- **Admin** — Akses penuh (laporan, manajemen layanan, pengguna)
- **Kasir** — Operasional saja (kasir, pelanggan, update order)

---

## Kustomisasi Branding ⚙️

Semua branding dikontrol dari satu tempat: **`.env`**

```env
APP_NAME="Nama Bisnis Anda"
APP_SLOGAN="Tagline singkat"
APP_TAGLINE="Deskripsi lengkap untuk invoice"
APP_LOGO_TEXT="LOGO"

APP_CURRENCY=IDR
APP_CURRENCY_SYMBOL=Rp
```

Nilai ini akan otomatis muncul di:
- Header & footer invoice (thermal & A4)
- Halaman login / auth layout
- PWA manifest (nama app & shortcut)
- Seeder akun default

---

## Getting Started ⚡

### Prerequisites
- PHP 8.3+
- Node.js 22+
- Composer
- MySQL / MariaDB (atau SQLite untuk dev)

### Installation

```bash
# 1. Clone
git clone <repository_url>
cd <folder_name>

# 2. Dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate
# → Edit .env: isi DB_*, APP_NAME, APP_SLOGAN, dll.

# 4. Database
php artisan migrate --seed

# 5. Dev server
php artisan serve   # Terminal 1
npm run dev         # Terminal 2
```

Buka **http://localhost:8000** — login dengan akun dari seeder.

> **Akun seeder** dibuat berdasarkan `APP_URL`:
> - Admin: `admin@<domain>` / `password`
> - Kasir: `kasir@<domain>` / `password`

---

## Struktur Penting 📁

```
config/axiom.php          ← Single source of truth untuk labels & branding
resources/js/pages/       ← Halaman Vue (POS, Orders, Reports, dll.)
resources/views/invoices/ ← Template cetak thermal & A4
app/Http/Controllers/     ← Business logic
database/seeders/         ← Data awal (users, services, stocks)
```

---

## Documentation
Konteks arsitektur lengkap tersedia di [`AGENTS.md`](AGENTS.md).
