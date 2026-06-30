-- ============================================================
--  CHEAPA STUDIO — PostgreSQL schema (Supabase)
--  Run this in the Supabase SQL editor, then run seed.pgsql.sql.
--
--  Notes for the Postgres port:
--   * AUTO_INCREMENT  -> SERIAL
--   * TINYINT(1) flags -> SMALLINT (kept numeric so existing PHP
--     comparisons like `is_bot=0` / `is_active=1` work unchanged)
--   * ENUM(...)        -> VARCHAR + CHECK constraint
--   * inline INDEX     -> separate CREATE INDEX
--   * adds a `sessions` table for SESSION_DRIVER=db (Vercel)
-- ============================================================

-- Clean slate (safe to re-run)
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders      CASCADE;
DROP TABLE IF EXISTS pack_items  CASCADE;
DROP TABLE IF EXISTS packs       CASCADE;
DROP TABLE IF EXISTS products    CASCADE;
DROP TABLE IF EXISTS portfolio   CASCADE;
DROP TABLE IF EXISTS leads       CASCADE;
DROP TABLE IF EXISTS settings    CASCADE;
DROP TABLE IF EXISTS visits      CASCADE;
DROP TABLE IF EXISTS admin_users CASCADE;
DROP TABLE IF EXISTS sessions    CASCADE;

-- ── Admin users ─────────────────────────────────────────────
CREATE TABLE admin_users (
  id         SERIAL PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Business Growth Packs ───────────────────────────────────
CREATE TABLE packs (
  id          SERIAL PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  slug        VARCHAR(140) NOT NULL UNIQUE,
  stage       VARCHAR(20)  NOT NULL DEFAULT 'Starting'
              CHECK (stage IN ('Starting','Growing','Established','Authority')),
  price_ugx   INT NOT NULL DEFAULT 0,
  tagline     VARCHAR(255) DEFAULT '',
  best_for    VARCHAR(255) DEFAULT '',
  features    TEXT,
  image       VARCHAR(255) DEFAULT '',
  is_featured SMALLINT NOT NULL DEFAULT 0,
  is_active   SMALLINT NOT NULL DEFAULT 1,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Design Shop products ────────────────────────────────────
CREATE TABLE products (
  id          SERIAL PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  slug        VARCHAR(170) NOT NULL UNIQUE,
  category    VARCHAR(20)  NOT NULL DEFAULT 'Branding'
              CHECK (category IN ('Branding','Print','Digital','Web')),
  unit_type   VARCHAR(10)  NOT NULL DEFAULT 'fixed'
              CHECK (unit_type IN ('fixed','piece','meter')),
  unit_label  VARCHAR(20)  NOT NULL DEFAULT 'pieces',
  price_ugx   INT NOT NULL DEFAULT 0,
  moq         INT NOT NULL DEFAULT 1,
  step        INT NOT NULL DEFAULT 1,
  description VARCHAR(500) DEFAULT '',
  variants    TEXT,
  design_available SMALLINT NOT NULL DEFAULT 0,
  design_fee  INT NOT NULL DEFAULT 10000,
  image       VARCHAR(255) DEFAULT '',
  is_active   SMALLINT NOT NULL DEFAULT 1,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Portfolio case studies ──────────────────────────────────
CREATE TABLE portfolio (
  id           SERIAL PRIMARY KEY,
  title        VARCHAR(180) NOT NULL,
  industry     VARCHAR(60) DEFAULT 'General',
  problem      VARCHAR(500) DEFAULT '',
  solution     VARCHAR(500) DEFAULT '',
  result       VARCHAR(500) DEFAULT '',
  before_image VARCHAR(255) DEFAULT '',
  after_image  VARCHAR(255) DEFAULT '',
  is_active    SMALLINT NOT NULL DEFAULT 1,
  sort_order   INT NOT NULL DEFAULT 0,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Leads (Contact / Services / chatbot) ────────────────────
CREATE TABLE leads (
  id            SERIAL PRIMARY KEY,
  name          VARCHAR(120) DEFAULT '',
  whatsapp      VARCHAR(40)  NOT NULL,
  business_name VARCHAR(150) DEFAULT '',
  service_type  VARCHAR(80)  DEFAULT '',
  budget        VARCHAR(80)  DEFAULT '',
  message       TEXT,
  source        VARCHAR(20)  NOT NULL DEFAULT 'contact'
                CHECK (source IN ('contact','services','chatbot','cart')),
  status        VARCHAR(20)  NOT NULL DEFAULT 'New'
                CHECK (status IN ('New','Contacted','Deposit Paid','In Progress','Review','Completed')),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Editable site settings ──────────────────────────────────
CREATE TABLE settings (
  skey VARCHAR(60) PRIMARY KEY,
  sval TEXT
);

-- ── Visitor tracking ────────────────────────────────────────
CREATE TABLE visits (
  id         SERIAL PRIMARY KEY,
  day        DATE NOT NULL,
  ip_hash    CHAR(64) NOT NULL,
  path       VARCHAR(120) DEFAULT '',
  referrer   VARCHAR(150) DEFAULT '',
  device     VARCHAR(10) DEFAULT 'desktop',
  is_bot     SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_day    ON visits (day);
CREATE INDEX idx_day_ip ON visits (day, ip_hash);

-- ── Orders + order items ────────────────────────────────────
CREATE TABLE orders (
  id              SERIAL PRIMARY KEY,
  order_no        VARCHAR(30) NOT NULL DEFAULT '',
  customer_name   VARCHAR(120) DEFAULT '',
  business_name   VARCHAR(150) DEFAULT '',
  whatsapp        VARCHAR(40)  NOT NULL,
  email           VARCHAR(150) DEFAULT '',
  delivery_method VARCHAR(20) NOT NULL DEFAULT 'pickup'
                  CHECK (delivery_method IN ('pickup','delivery_kampala','delivery_far')),
  delivery_address VARCHAR(300) DEFAULT '',
  subtotal        INT NOT NULL DEFAULT 0,
  design_total    INT NOT NULL DEFAULT 0,
  delivery_fee    INT NOT NULL DEFAULT 0,
  total           INT NOT NULL DEFAULT 0,
  deposit         INT NOT NULL DEFAULT 0,
  payment_method  VARCHAR(40) DEFAULT '',
  channel         VARCHAR(10) NOT NULL DEFAULT 'in-app'
                  CHECK (channel IN ('in-app','whatsapp')),
  status          VARCHAR(20) NOT NULL DEFAULT 'New'
                  CHECK (status IN ('New','Contacted','Deposit Paid','In Progress','Review','Completed')),
  notes           TEXT,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id          SERIAL PRIMARY KEY,
  order_id    INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id  INT,
  name        VARCHAR(150) DEFAULT '',
  variant     VARCHAR(80)  DEFAULT '',
  unit_type   VARCHAR(20)  DEFAULT 'piece',
  unit_label  VARCHAR(20)  DEFAULT 'pieces',
  unit_price  INT NOT NULL DEFAULT 0,
  qty         INT NOT NULL DEFAULT 1,
  design      SMALLINT NOT NULL DEFAULT 0,
  design_fee  INT NOT NULL DEFAULT 0,
  line_total  INT NOT NULL DEFAULT 0
);

-- ── Editable "what's inside a pack" items ───────────────────
CREATE TABLE pack_items (
  id         SERIAL PRIMARY KEY,
  pack_id    INT NOT NULL REFERENCES packs(id) ON DELETE CASCADE,
  label      VARCHAR(150) NOT NULL DEFAULT '',
  blurb      VARCHAR(500) DEFAULT '',
  image      VARCHAR(255) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
);

-- ── DB-backed sessions (SESSION_DRIVER=db, required on Vercel) ─
CREATE TABLE sessions (
  id         VARCHAR(128) PRIMARY KEY,
  data       TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
