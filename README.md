# Cheapa Studio — Mobile-First Creative Agency Platform

A premium, mobile-first sales platform for **Cheapa Studio** (cheapastudio.com), a digital
creative agency in Kampala, Uganda. Built with plain PHP, PDO/MySQL, custom CSS and Bootstrap
Icons — designed to run on XAMPP and deploy to any PHP host.

This is the **complete system** — the full mobile-first public platform on a real database, with real
managed images, plus a full admin back-office (CRM, order workflow, and CRUD for everything).

---

## What's included

**Public site (mobile-first, WhatsApp-driven):**
- **Home** — hero, quick-action selector, trust strip, featured packs, how-it-works, portfolio preview, final CTA
- **Packs** — business-stage selector, image-forward pack cards, ROI comparison, add-ons, monthly services
- **Shop** — searchable, category-filtered product feed with the premium image card, **cart** (localStorage) and **WhatsApp checkout**
- **Services** — service hub + web-design request form (saves to the database)
- **Portfolio** — before → after (real images), problem/solution/result case studies
- **Contact** — guided lead form (only WhatsApp required) saving to the database
- **Chatbot** — button-first 24/7 sales assistant that always routes to a pack, product, service or WhatsApp
- **Sticky mobile bottom nav** + slide-in cart + floating chat
- **Real images** for every pack, product and portfolio item, served from `/uploads` and stored in the DB

**Admin back-office (full):**
- Login (bcrypt) / logout, CSRF-protected forms, image-upload handling
- **Dashboard** — pipeline stats (new / in-progress / completed) + recent leads
- **Leads & Orders** — list with status filter, detail view, and the **order workflow**
  (New → Contacted → Deposit Paid → In Progress → Review → Completed) — this is the CRM
- **Packs** — add / edit / delete with image upload
- **Shop Products** — add / edit / delete with image upload
- **Portfolio** — add / edit / delete with before/after image upload
- **Settings** — edit the WhatsApp number, phone, email, location and tagline (drives the whole public
  site live), plus change admin password

Homepage content stays in sync automatically: featured packs and portfolio previews are driven by the
records you manage in the admin, so editing a pack or case study updates the homepage too.

---

## 1. Local setup (XAMPP)

1. The project lives at `C:\xampp\htdocs\Cheapa_Studio\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Visit `http://localhost/Cheapa_Studio/`.

## 2. Import the database

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin/`
2. Click **Import** → choose `C:\xampp\htdocs\Cheapa_Studio\database.sql` → **Go**.
3. This creates `cheapa_db` with all tables and seed data (4 packs, 8 products, 4 case studies, 1 sample lead).

## 3. Set the admin password

1. Open `http://localhost/Cheapa_Studio/generate-hash.php` (sets password to `admin123`).
2. **Delete `generate-hash.php` immediately afterwards.**
3. Log in at `http://localhost/Cheapa_Studio/admin/login.php` — user `admin`, pass `admin123`.

## 4. Set the real WhatsApp number & contact info

Edit `config.php`:
```php
define('PHONE_1',         '+256 7XX XXXXXX');
define('WHATSAPP_NUMBER', '2567XXXXXXXX');   // no '+', no spaces
define('EMAIL',           'hello@cheapastudio.com');
```
Every CTA, cart checkout and chatbot hand-off uses `WHATSAPP_NUMBER`.

---

## File structure

```
Cheapa_Studio/
├── index.php              Home
├── packs.php              Business Growth Packs
├── shop.php               Design Shop (cart)
├── services.php           Services + web design form
├── portfolio.php          Case studies
├── contact.php            Lead form
├── 404.php                Custom 404
├── config.php             Brand constants + helpers (ugx, e, wa_link)
├── database.sql           Schema + seed data
├── generate-hash.php      One-time admin password (DELETE after use)
├── .htaccess              404 routing + security headers
├── admin/
│   ├── login.php / logout.php   Admin auth (bcrypt)
│   ├── index.php                Dashboard (pipeline stats + recent leads)
│   ├── leads.php                Leads & Orders list (status filter)
│   ├── lead-view.php            Lead detail + order workflow + delete
│   ├── packs.php                Packs list
│   ├── pack-edit.php            Add/edit pack (+ image upload)
│   ├── pack-delete.php
│   ├── products.php             Products list
│   ├── product-edit.php         Add/edit product (+ image upload)
│   ├── product-delete.php
│   ├── portfolio.php            Portfolio list
│   ├── portfolio-edit.php       Add/edit case study (+ before/after images)
│   ├── portfolio-delete.php
│   └── settings.php             Contact/brand settings + change password
├── includes/
│   ├── db.php             PDO connection + loads editable settings
│   ├── auth.php           Admin session guard
│   ├── helpers.php        img_url, upload_image, CSRF, slugify, status helpers
│   ├── header.php         Navbar + mobile drawer
│   ├── footer.php         Footer (pulls in cart/chat/bottom-nav)
│   ├── admin-header.php   Admin sidebar layout
│   ├── admin-footer.php
│   ├── bottom-nav.php     Mobile sticky bottom nav
│   ├── cart-drawer.php    Slide-in cart
│   └── chatbot.php        Chat widget shell
├── assets/
│   ├── css/style.css      Full mobile-first design system (public + admin)
│   └── js/main.js         Nav, cart, chatbot, shop filters, stage selector
└── uploads/               Real managed images
    ├── packs/             (per-pack images, filename stored in DB)
    ├── products/          (per-product images)
    └── portfolio/         (before/after images)
```

## Using the admin

After importing `database.sql` and setting the password, log in at `/admin/login.php`. From the sidebar you can:
- **Leads & Orders** — every form/chatbot submission lands here; open one and move it through the
  pipeline (New → … → Completed). The status pills show at a glance where each deal is.
- **Packs / Products / Portfolio** — add, edit (including uploading a new image), or delete. Changes
  show on the public site immediately. Toggling "Active" hides/shows an item without deleting it.
- **Settings** — change the WhatsApp number once and every button across the site updates.

Images shipped with the seed data live in `/uploads`. Uploading a new image in the admin replaces the
record's image and stores the new filename in the database.

---

## How the conversion paths work

Every page routes the visitor toward one of three revenue systems — no dead ends:
- 📦 **A Pack** (high-value bundle) — Packs page, home, chatbot
- 🛍️ **A Product** (quick purchase) — Shop cart → WhatsApp checkout
- 🌐 **A Service** (custom work) — Services form, contact form

Cart and chatbot both hand off to WhatsApp with a pre-filled message so you close the sale in chat.

---

## Database tables

- `packs` — Business Growth Packs (name, stage, price, features, featured flag)
- `products` — Design Shop items (name, category, price, description)
- `portfolio` — case studies (problem / solution / result, industry)
- `leads` — captured from Contact, Services and chatbot (with `status` for the future order workflow)
- `admin_users` — admin login

## Deploy notes (e.g. Hostinger)

1. Create a MySQL database, import `database.sql` (or its tables) via phpMyAdmin.
2. Update credentials in `includes/db.php` and the domain auto-detects in `config.php`.
3. Upload files, run `generate-hash.php` once, then delete it.
4. Update `.htaccess` `ErrorDocument` path if the app is at the domain root (`/404.php`).

## Security notes

- All queries use PDO prepared statements.
- Admin passwords use `password_hash()` / `password_verify()` (bcrypt).
- `uploads/.htaccess` blocks script execution from the uploads folder.
- Errors are logged, never shown to visitors (`config.php`).
