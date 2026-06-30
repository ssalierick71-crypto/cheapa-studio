-- ============================================================
--  CHEAPA STUDIO — Database Schema + Seed Data
--  Import via phpMyAdmin (creates cheapa_db).
-- ============================================================

CREATE DATABASE IF NOT EXISTS cheapa_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cheapa_db;

-- ------------------------------------------------------------
--  Admin users
-- ------------------------------------------------------------
DROP TABLE IF EXISTS admin_users;
CREATE TABLE admin_users (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(50) NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO admin_users (username, password) VALUES ('admin', '');

-- ------------------------------------------------------------
--  Business Growth Packs
-- ------------------------------------------------------------
DROP TABLE IF EXISTS packs;
CREATE TABLE packs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  slug        VARCHAR(140) NOT NULL UNIQUE,
  stage       ENUM('Starting','Growing','Established','Authority') NOT NULL DEFAULT 'Starting',
  price_ugx   INT NOT NULL DEFAULT 0,
  tagline     VARCHAR(255) DEFAULT '',
  best_for    VARCHAR(255) DEFAULT '',
  features    TEXT,                       -- one item per line
  image       VARCHAR(255) DEFAULT '',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO packs (name, slug, stage, price_ugx, tagline, best_for, features, is_featured, sort_order) VALUES
('Launch Pack', 'launch-pack', 'Starting', 100000,
 'Everything you need to open your doors',
 'Brand-new businesses getting started',
 "Logo design\n100 Business cards\n50 Flyers\nWhatsApp profile branding\n1 Social media post",
 0, 1),

('Visibility Pack', 'visibility-pack', 'Growing', 150000,
 'Get seen and remembered',
 'Businesses ready to be noticed',
 "Logo design\n200 Business cards\n100 Flyers\nBanner design\nReceipt book\n3 Social media posts",
 0, 2),

('Growth Pack', 'growth-pack', 'Established', 500000,
 'Scale your brand like a pro',
 'Growing businesses going professional',
 "Logo design\n200 Business cards\n100 Flyers\nReceipt book\nBanner design\nLetterhead\nCompany profile\n5 Social media posts\n3-page website\nGoogle Business Profile setup",
 1, 3),

('Authority Pack', 'authority-pack', 'Authority', 1000000,
 'Become the leader in your market',
 'Established brands claiming authority',
 "Logo design\n200 Business cards\n100 Flyers\nReceipt book\nWhatsApp branding\nBanner design\nLetterhead\nCompany profile\n5 Social media posts\n5-page website\nGoogle Business Profile setup",
 0, 4);

-- ------------------------------------------------------------
--  Design Shop products
-- ------------------------------------------------------------
DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  slug        VARCHAR(170) NOT NULL UNIQUE,
  category    ENUM('Branding','Print','Digital','Web') NOT NULL DEFAULT 'Branding',
  unit_type   ENUM('fixed','piece','meter') NOT NULL DEFAULT 'fixed',
  unit_label  VARCHAR(20) NOT NULL DEFAULT 'pieces',
  price_ugx   INT NOT NULL DEFAULT 0,            -- unit price (per piece/meter) or flat price
  moq         INT NOT NULL DEFAULT 1,            -- minimum order quantity
  step        INT NOT NULL DEFAULT 1,            -- quantity increment
  description VARCHAR(500) DEFAULT '',
  variants    TEXT,                              -- optional "Label=price" lines
  design_available TINYINT(1) NOT NULL DEFAULT 0,
  design_fee  INT NOT NULL DEFAULT 10000,
  image       VARCHAR(255) DEFAULT '',
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO products (name, slug, category, price_ugx, description, sort_order) VALUES
('Logo Design',            'logo-design',            'Branding', 60000,  'A clean, memorable logo in multiple formats (PNG, JPG, PDF).', 1),
('Business Cards (200pcs)', 'business-cards-200',     'Print',    50000,  'Designed and print-ready double-sided business cards.', 2),
('Flyers (100pcs)',        'flyers-100',             'Print',    70000,  'Eye-catching A5 flyers designed to convert.', 3),
('Poster Design',          'poster-design',          'Print',    40000,  'High-impact A3/A2 poster design for promotions.', 4),
('Receipt Book',           'receipt-book',           'Print',    45000,  'Branded carbon-copy receipt book design.', 5),
('Social Media Post Pack', 'social-media-post-pack', 'Digital',  35000,  '5 branded social media post designs for your feed.', 6),
('WhatsApp Branding Kit',  'whatsapp-branding-kit',  'Digital',  25000,  'Profile photo, status templates and catalog look.', 7),
('Landing Page',           'landing-page',           'Web',      250000, 'A single high-converting mobile-first landing page.', 8);

-- ------------------------------------------------------------
--  Portfolio case studies (transformation system)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS portfolio;
CREATE TABLE portfolio (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(180) NOT NULL,
  industry     VARCHAR(60) DEFAULT 'General',
  problem      VARCHAR(500) DEFAULT '',
  solution     VARCHAR(500) DEFAULT '',
  result       VARCHAR(500) DEFAULT '',
  before_image VARCHAR(255) DEFAULT '',
  after_image  VARCHAR(255) DEFAULT '',
  is_active    TINYINT(1) NOT NULL DEFAULT 1,
  sort_order   INT NOT NULL DEFAULT 0,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO portfolio (title, industry, problem, solution, result, sort_order) VALUES
('Glow Hair Salon Rebrand', 'Salon',
 'An outdated, hand-written sign and no consistent look across WhatsApp and flyers.',
 'New logo, colour system, branded flyers and a WhatsApp business profile.',
 'Walk-ins up and a recognisable brand customers now share online.', 1),

('Kampala Fresh Grocers', 'Shop',
 'Plain printed price lists and no social presence.',
 'Logo, branded poster set and a 5-post social media starter pack.',
 'A consistent shopfront and a feed that looks trustworthy to new buyers.', 2),

('Mama''s Kitchen Restaurant', 'Restaurant',
 'Menu photos taken on a phone with no branding, hard to read.',
 'Designed menu, banner for the entrance and matching social posts.',
 'A polished menu that raised perceived value and order confidence.', 3),

('Bright Smile Dental Clinic', 'Clinic',
 'No professional identity for a clinic that needed to signal trust.',
 'Clean medical-grade logo, letterhead, business cards and a 3-page website.',
 'A credible online presence patients can find and book through.', 4);

-- ------------------------------------------------------------
--  Leads — captured from Contact form, Services form & chatbot
-- ------------------------------------------------------------
DROP TABLE IF EXISTS leads;
CREATE TABLE leads (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) DEFAULT '',
  whatsapp      VARCHAR(40)  NOT NULL,
  business_name VARCHAR(150) DEFAULT '',
  service_type  VARCHAR(80)  DEFAULT '',
  budget        VARCHAR(80)  DEFAULT '',
  message       TEXT,
  source        ENUM('contact','services','chatbot','cart') NOT NULL DEFAULT 'contact',
  status        ENUM('New','Contacted','Deposit Paid','In Progress','Review','Completed') NOT NULL DEFAULT 'New',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO leads (name, whatsapp, business_name, service_type, budget, message, source) VALUES
('Sarah N.', '256712345678', 'Glow Salon', 'Business Growth Pack', 'UGX 150,000', 'Interested in the Visibility Pack for my salon.', 'contact');

-- ------------------------------------------------------------
--  Editable site settings (managed from the admin panel)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
  skey  VARCHAR(60) PRIMARY KEY,
  sval  TEXT
) ENGINE=InnoDB;

INSERT INTO settings (skey, sval) VALUES
('whatsapp_number', '256753168599'),
('phone_1',         '+256 753 168599'),
('email',           'hello@cheapastudio.com'),
('location',        'Kampala, Uganda'),
('site_tagline',    'Professional Branding Made Affordable');

-- ------------------------------------------------------------
--  Wire downloaded images to their records (files live in /uploads)
-- ------------------------------------------------------------
-- ------------------------------------------------------------
--  Per-unit pricing: cards & flyers per piece, banner per meter
-- ------------------------------------------------------------
UPDATE products SET
  name='Business Cards', unit_type='piece', unit_label='pieces',
  price_ugx=200, moq=100, step=100,
  description='Premium business cards, printed and ready. Choose single or double sided.',
  variants='Single sided=200\nDouble sided=300',
  design_available=1, design_fee=10000
WHERE slug='business-cards-200';

UPDATE products SET
  name='Flyers', unit_type='piece', unit_label='pieces',
  price_ugx=400, moq=100, step=100,
  description='Full-colour A5 flyers designed to bring in customers.',
  design_available=1, design_fee=10000
WHERE slug='flyers-100';

INSERT INTO products (name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, design_available, design_fee, image, is_active, sort_order)
VALUES ('Banner', 'banner', 'Print', 'meter', 'meters', 30000, 1, 1,
  'Durable outdoor banner, priced per meter. Bold, weatherproof and ready to hang.',
  1, 10000, 'banner.jpg', 1, 9);

-- ------------------------------------------------------------
--  Visitor tracking (privacy-friendly: hashed IPs, bots flagged)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS visits;
CREATE TABLE visits (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  day        DATE NOT NULL,
  ip_hash    CHAR(64) NOT NULL,
  path       VARCHAR(120) DEFAULT '',
  referrer   VARCHAR(150) DEFAULT '',
  device     VARCHAR(10) DEFAULT 'desktop',
  is_bot     TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_day (day),
  INDEX idx_day_ip (day, ip_hash)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Orders + order items (in-app & WhatsApp checkout records)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  order_no        VARCHAR(30) NOT NULL DEFAULT '',
  customer_name   VARCHAR(120) DEFAULT '',
  business_name   VARCHAR(150) DEFAULT '',
  whatsapp        VARCHAR(40)  NOT NULL,
  email           VARCHAR(150) DEFAULT '',
  delivery_method ENUM('pickup','delivery_kampala','delivery_far') NOT NULL DEFAULT 'pickup',
  delivery_address VARCHAR(300) DEFAULT '',
  subtotal        INT NOT NULL DEFAULT 0,
  design_total    INT NOT NULL DEFAULT 0,
  delivery_fee    INT NOT NULL DEFAULT 0,
  total           INT NOT NULL DEFAULT 0,
  deposit         INT NOT NULL DEFAULT 0,
  payment_method  VARCHAR(40) DEFAULT '',
  channel         ENUM('in-app','whatsapp') NOT NULL DEFAULT 'in-app',
  status          ENUM('New','Contacted','Deposit Paid','In Progress','Review','Completed') NOT NULL DEFAULT 'New',
  notes           TEXT,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  order_id    INT NOT NULL,
  product_id  INT NULL,
  name        VARCHAR(150) DEFAULT '',
  variant     VARCHAR(80)  DEFAULT '',
  unit_type   VARCHAR(20)  DEFAULT 'piece',
  unit_label  VARCHAR(20)  DEFAULT 'pieces',
  unit_price  INT NOT NULL DEFAULT 0,
  qty         INT NOT NULL DEFAULT 1,
  design      TINYINT(1) NOT NULL DEFAULT 0,
  design_fee  INT NOT NULL DEFAULT 0,
  line_total  INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Editable "what's inside a pack" items (managed in admin)
--  If empty for a pack, the pack page auto-maps library images.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS pack_items;
CREATE TABLE pack_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  pack_id    INT NOT NULL,
  label      VARCHAR(150) NOT NULL DEFAULT '',
  blurb      VARCHAR(500) DEFAULT '',
  image      VARCHAR(255) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_pi_pack FOREIGN KEY (pack_id) REFERENCES packs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

UPDATE products SET image = CONCAT(slug, '.jpg');
UPDATE packs    SET image = CONCAT(slug, '.jpg');

UPDATE portfolio SET before_image='salon-before.jpg',      after_image='salon-after.jpg'      WHERE title='Glow Hair Salon Rebrand';
UPDATE portfolio SET before_image='grocers-before.jpg',    after_image='grocers-after.jpg'    WHERE title='Kampala Fresh Grocers';
UPDATE portfolio SET before_image='restaurant-before.jpg', after_image='restaurant-after.jpg' WHERE title="Mama's Kitchen Restaurant";
UPDATE portfolio SET before_image='clinic-before.jpg',     after_image='clinic-after.jpg'     WHERE title='Bright Smile Dental Clinic';
