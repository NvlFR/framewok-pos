# Project Blueprint: Framework POS (Axiom)

This document is the **System Prompt & Architectural Blueprint** for AI Coding Agents. Read this carefully before writing or modifying any code in this repository.

## 1. Project Philosophy & Architecture
- **Product**: Generic Point of Sale (POS) & Order Management Framework.
- **Nature**: White-label starter kit. **NEVER** hardcode business names, addresses, or taglines in views.
- **Single Source of Truth (Branding)**: All branding is controlled via `.env` and retrieved through `config('axiom.brand')` or passed as Inertia `page.props`.
- **Primary Goal**: Provide a clean, high-performance, edge-case-handled base for retail and service-based businesses.

## 2. Tech Stack Ecosystem
- **Backend Framework**: Laravel 11 (PHP 8.3+)
- **Frontend SPA**: Vue 3 (Composition API, `<script setup>`) + Inertia.js v1
- **Styling system**: TailwindCSS v3 + Shadcn-vue (UI Primitives)
- **Icons**: `lucide-vue-next` / Radix Icons
- **State Management**: Vue reactivity (`ref`, `computed`, `watch`) + Inertia Form Helper (`useForm`)
- **Database**: Relational (MySQL / SQLite for dev).

## 3. Core Domains & Business Logic

### A. Point of Sale (POS) / `Transactions/Create.vue`
- **Matrix Pricing**: Highly flexible dynamic pricing logic based on multiple attributes (e.g., Size, Type). The model is `PriceMatrix`.
- **Inline Customer Registration**: Cashiers must be able to register new customers seamlessly without leaving the POS interface.
- **Keyboard-First Navigation**: Optimized for speed (`F2`: Search, `F4`: Pay, `F9`: Save). Agents building UI must respect tab-indexes and autofocus states.
- **Pinned Services**: Frequently used services can be pinned via configuration / database for quick access.

### B. Order Lifecycle (`Transaction` Model)
- **Status Flow**: Strict enumeration (`pending` → `diproses` → `selesai` → `diambil`).
- **Payment Modes**: Supports `cash`, `qris`, `transfer`.
- **Invoice Documents**: Dual support required for all sales.
  - **Thermal**: 80mm generic printing (`invoices/thermal.blade.php`).
  - **A4 PDF**: Standard document printing (`invoices/transaction.blade.php`).

### C. Inventory & Stock Control
- **Entity**: `Stock` / `Material`.
- **Behavior**: Every transaction line item may trigger a stock deduction. Logs must be immutable for audit purposes.

### D. Security & Roles (RBAC)
- Strictly role-based routing and middleware.
  - `Admin`: Full system access, settings, reporting, master data.
  - `Kasir` (Cashier): Restricted to POS, Customer ops, and viewing active orders.

## 4. Key Directory Structure
```text
app/
 ├── Http/Controllers/     # Application logic (Thin controllers preferred)
 ├── Http/Requests/        # Form validation rules
 ├── Models/               # Eloquent Models (Fat models with business scopes)
config/axiom.php           # Central configuration for branding & localization
resources/
 ├── js/
 │   ├── components/ui/    # Shadcn-vue primitive components (DO NOT MODIFY UNLESS NECESSARY)
 │   ├── layouts/          # Top-level Inertia Layouts (AppLayout, AuthLayout)
 │   ├── pages/            # View components routed via Inertia
 ├── views/
 │   ├── invoices/         # Printable Blade templates (must remain standalone HTML/CSS)
```

## 5. Agent Development Guidelines

When an AI Agent is tasked with generating or modifying code in this project, you MUST adhere to the following rules:

### Naming Conventions
- **Database / Tables**: `snake_case` (English plural, e.g., `price_matrices`).
- **PHP Classes / Models / Controllers**: `PascalCase` (e.g., `TransactionController`).
- **PHP Methods / Vars**: `camelCase` (e.g., `calculateTotal()`).
- **Vue Components**: `PascalCase` (e.g., `ProductCard.vue`).
- **Vue Props/Variables**: `camelCase`.

### Vue & Frontend Rules
- **Aesthetic**: Premium, minimalist, and professional. Use Shadcn-vue components (`<Button>`, `<Input>`, `<Card>`) instead of raw HTML elements.
- **Responsiveness**: Assume desktop-first for POS screens, but mobile-responsive for reports and dashboard.
- **Forms**: Always use Inertia's `useForm` for form handling to leverage built-in error tracking and loading states.
- **Reactivity Check**: Do not mutate props directly. Use `defineEmits` or `v-model` paradigms.

### Backend & Database Rules
- **Transactions**: Complex operations (like order creation + stock deduction) MUST be wrapped in `DB::transaction()`.
- **Validation**: Never trust user input. Always use Form Requests or `$request->validate()`.
- **Hardcoding**: ABSOLUTELY NO HARDCODING of brand names, domain names, or locale-specific monetary symbols in the code. Rely on `config('axiom.*')` or environment variables.

### Communication & Comments
- **Code Comments**: Keep comments concise, explaining the *why*, not the *what*. Use English for code-level comments.
- **User Discussion**: When explaining to the user, use casual but professional Indonesian (e.g., "Bro").

---
**Last Updated:** April 2026 (Refactored into a Fully Generic Starter Kit)
