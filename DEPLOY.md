# Deploying Cheapa Studio to Supabase + Vercel

This moves the site off XAMPP onto **Supabase** (Postgres database + file storage)
and **Vercel** (hosting), for development before going fully live.

The code already supports both stacks — you switch by setting environment
variables. Nothing in the PHP needs editing.

---

## 1. Create the Supabase project

1. Go to https://supabase.com → **New project**. Pick a name and a strong
   database password (save it).
2. When it finishes provisioning, open **SQL Editor** → **New query**.
3. Paste the contents of [`schema.pgsql.sql`](schema.pgsql.sql) and **Run**.
4. New query again → paste [`seed.pgsql.sql`](seed.pgsql.sql) → **Run**.
   This loads your packs, products, portfolio, settings and admin user.

You now have the full database in Postgres.

## 2. Create the Storage bucket (for images)

1. Supabase → **Storage** → **New bucket**.
2. Name it `cheapa` and tick **Public bucket** → Create.
3. Upload your existing images so the seeded records have pictures:
   recreate the same folders inside the bucket and upload the matching files:
   - `packs/`      ← everything in `uploads/packs/`
   - `products/`   ← `uploads/products/`
   - `portfolio/`  ← `uploads/portfolio/`
   - `items/`      ← `uploads/items/`  (pack "what's inside" images)
   - `brand/`      ← `uploads/brand/`  (if you uploaded a logo)
   (You can drag-and-drop folders in the Storage UI.)

## 3. Collect your Supabase credentials

From **Project Settings**:

- **Database → Connection string → "Connection pooling"** (Transaction mode):
  note the **host** (ends in `...pooler.supabase.com`), **port `6543`**,
  **database `postgres`**, **user** (looks like `postgres.xxxxxxxx`), and the
  **password** you set in step 1.
- **API**: copy the **Project URL** (`SUPABASE_URL`) and the
  **`service_role` secret key** (`SUPABASE_SERVICE_KEY`).

## 4. Push the code to GitHub

From `C:\xampp\htdocs\Cheapa_Studio`:

```bash
git init
git add .
git commit -m "Cheapa Studio — Supabase + Vercel ready"
git branch -M main
git remote add origin https://github.com/<you>/cheapa-studio.git
git push -u origin main
```

`.env`, `.vercel` and `generate-hash.php` are git-ignored, so no secrets leak.

## 5. Import into Vercel

1. https://vercel.com → **Add New → Project** → import the GitHub repo.
2. Framework preset: **Other** (the `vercel.json` already wires up the PHP runtime).
3. Before deploying, open **Settings → Environment Variables** and add:

   | Key | Value |
   |-----|-------|
   | `DB_DRIVER` | `pgsql` |
   | `DB_HOST` | your `...pooler.supabase.com` host |
   | `DB_PORT` | `6543` |
   | `DB_NAME` | `postgres` |
   | `DB_USER` | `postgres.xxxxxxxx` |
   | `DB_PASS` | your database password |
   | `DB_SSLMODE` | `require` |
   | `STORAGE_DRIVER` | `supabase` |
   | `SUPABASE_URL` | your project URL |
   | `SUPABASE_BUCKET` | `cheapa` |
   | `SUPABASE_SERVICE_KEY` | your `service_role` key |
   | `SESSION_DRIVER` | `db` |

4. **Deploy**. Every later `git push` auto-deploys.

## 6. Log in & verify

- Visit the Vercel URL — the public site should load with images from Supabase.
- Admin: `/admin/login.php`. The seeded admin password is whatever it currently
  is on XAMPP (run `generate-hash.php` locally once if you're unsure — it sets
  `admin123` — then re-export `seed.pgsql.sql`). Change it in **Settings** after
  first login.

---

## Why each setting

- **`DB_DRIVER=pgsql`** flips `includes/db.php` to a Postgres DSN that's already
  pgbouncer-safe (emulated prepares) — that's why we use the pooler host/port.
- **`STORAGE_DRIVER=supabase`** is required because Vercel's filesystem is
  read-only; uploads go to the bucket via `includes/helpers.php`.
- **`SESSION_DRIVER=db`** is required because Vercel is serverless — file
  sessions don't persist between requests, so they live in the `sessions` table.

## Keeping XAMPP working too

Local XAMPP keeps working with **no `.env` file** — the defaults in
`includes/db.php` (mysql / `cheapa_db` / root) still apply. The two environments
are fully separate: XAMPP+MySQL locally, Supabase+Postgres in the cloud.

## Note on Vercel + PHP

Vercel has no native PHP; `vercel.json` uses the community `vercel-php` runtime.
If the first deploy reports an unavailable runtime version, bump
`vercel-php@0.7.4` to the latest tag shown at
https://github.com/vercel-community/php/releases.
