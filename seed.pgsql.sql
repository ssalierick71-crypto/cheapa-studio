-- ============================================================
--  CHEAPA STUDIO — seed data (generated from live cheapa_db)
--  Run AFTER schema.pgsql.sql in the Supabase SQL editor.
-- ============================================================

-- admin_users (1 rows)
INSERT INTO admin_users (id, username, password, created_at) VALUES (1, $cs$admin$cs$, $cs$$2y$10$/CxE/hFi9yd9OJqjjshL4.5D2m/nGjO/NvGmik4KwZCXJkY1eAlOu$cs$, $cs$2026-06-28 12:00:26$cs$);

-- packs (4 rows)
INSERT INTO packs (id, name, slug, stage, price_ugx, tagline, best_for, features, image, is_featured, is_active, sort_order, created_at) VALUES (1, $cs$Launch Pack$cs$, $cs$launch-pack$cs$, $cs$Starting$cs$, 100000, $cs$Everything you need to open your doors$cs$, $cs$Brand-new businesses getting started$cs$, $cs$Logo design
100 Business cards
50 Flyers
WhatsApp profile branding
1 Social media post$cs$, $cs$5955dabafd97a868.jpg$cs$, 0, 1, 1, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO packs (id, name, slug, stage, price_ugx, tagline, best_for, features, image, is_featured, is_active, sort_order, created_at) VALUES (2, $cs$Visibility Pack$cs$, $cs$visibility-pack$cs$, $cs$Growing$cs$, 150000, $cs$Get seen and remembered$cs$, $cs$Businesses ready to be noticed$cs$, $cs$Logo design
200 Business cards
100 Flyers
Banner design
Receipt book
3 Social media posts$cs$, $cs$88653d6facdaec93.jpg$cs$, 0, 1, 2, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO packs (id, name, slug, stage, price_ugx, tagline, best_for, features, image, is_featured, is_active, sort_order, created_at) VALUES (3, $cs$Growth Pack$cs$, $cs$growth-pack$cs$, $cs$Established$cs$, 500000, $cs$Scale your brand like a pro$cs$, $cs$Growing businesses going professional$cs$, $cs$3-page website
200 Business cards
100 Flyers
Receipt book
Banner design
Letterhead
Company profile
5 Social media posts
logo Design
Google Business Profile setup$cs$, $cs$3ae99d14c78f2a12.jpg$cs$, 1, 1, 3, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO packs (id, name, slug, stage, price_ugx, tagline, best_for, features, image, is_featured, is_active, sort_order, created_at) VALUES (4, $cs$Authority Pack$cs$, $cs$authority-pack$cs$, $cs$Authority$cs$, 1000000, $cs$Become the leader in your market$cs$, $cs$Established brands claiming authority$cs$, $cs$Logo design
200 Business cards
100 Flyers
Receipt book
WhatsApp branding
Banner design
Letterhead
Company profile
5 Social media posts
5-page website
Google Business Profile setup$cs$, $cs$4e4484373d2f6361.jpg$cs$, 0, 1, 4, $cs$2026-06-28 12:00:26$cs$);

-- products (9 rows)
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (1, $cs$Logo Design$cs$, $cs$logo-design$cs$, $cs$Branding$cs$, $cs$fixed$cs$, $cs$$cs$, 20000, 1, 1, $cs$A clean, memorable logo in multiple formats (PNG, JPG, PDF).$cs$, $cs$$cs$, 0, 10000, $cs$77295da8d5cfa776.jpg$cs$, 1, 1, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (2, $cs$Business Cards$cs$, $cs$business-cards-200$cs$, $cs$Print$cs$, $cs$piece$cs$, $cs$pieces$cs$, 200, 100, 100, $cs$Premium business cards, printed and ready. Choose single or double sided.$cs$, $cs$Single sided=200
Double sided=300$cs$, 1, 10000, $cs$1e3cd6e4082d15b4.jpg$cs$, 1, 2, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (3, $cs$Flyers$cs$, $cs$flyers-100$cs$, $cs$Print$cs$, $cs$piece$cs$, $cs$pieces$cs$, 400, 100, 100, $cs$Full-colour A5 flyers designed to bring in customers.$cs$, $cs$$cs$, 1, 10000, $cs$5b3e5210010f13f4.jpg$cs$, 1, 3, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (4, $cs$Poster Design$cs$, $cs$poster-design$cs$, $cs$Print$cs$, $cs$fixed$cs$, $cs$$cs$, 20000, 1, 1, $cs$High-impact A3/A2 poster design for promotions.$cs$, $cs$$cs$, 0, 0, $cs$b29b471e836590c7.jpg$cs$, 1, 4, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (5, $cs$Receipt Book,Invoice,Debit note$cs$, $cs$receipt-book$cs$, $cs$Print$cs$, $cs$fixed$cs$, $cs$$cs$, 26000, 1, 1, $cs$Branded carbon-copy receipt book design. 50 X 50 Pages$cs$, $cs$$cs$, 0, 0, $cs$1dee9ee70723ada3.jpg$cs$, 1, 5, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (6, $cs$Social Media Post Pack$cs$, $cs$social-media-post-pack$cs$, $cs$Digital$cs$, $cs$fixed$cs$, $cs$$cs$, 35000, 1, 1, $cs$5 branded social media post designs for your feed.$cs$, $cs$$cs$, 0, 0, $cs$39c637bad99b0745.jpg$cs$, 1, 6, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (7, $cs$WhatsApp Branding Kit$cs$, $cs$whatsapp-branding-kit$cs$, $cs$Digital$cs$, $cs$fixed$cs$, $cs$$cs$, 25000, 1, 1, $cs$Profile photo, status templates and catalog look.$cs$, $cs$$cs$, 0, 10000, $cs$f7398dc9c0fed94a.jpg$cs$, 1, 7, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (8, $cs$Landing Page$cs$, $cs$landing-page$cs$, $cs$Web$cs$, $cs$fixed$cs$, $cs$$cs$, 250000, 1, 1, $cs$A single high-converting mobile-first landing page.$cs$, $cs$$cs$, 0, 10000, $cs$b85d49a01d106380.jpg$cs$, 1, 8, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO products (id, name, slug, category, unit_type, unit_label, price_ugx, moq, step, description, variants, design_available, design_fee, image, is_active, sort_order, created_at) VALUES (10, $cs$Banner$cs$, $cs$banner$cs$, $cs$Print$cs$, $cs$meter$cs$, $cs$meters$cs$, 30000, 1, 1, $cs$Durable outdoor banner, priced per meter. Bold, weatherproof and ready to hang.$cs$, $cs$$cs$, 1, 10000, $cs$3850eae23cb7203f.jpg$cs$, 1, 9, $cs$2026-06-28 13:37:07$cs$);

-- portfolio (4 rows)
INSERT INTO portfolio (id, title, industry, problem, solution, result, before_image, after_image, is_active, sort_order, created_at) VALUES (1, $cs$Clash Jewelry$cs$, $cs$Jewelry$cs$, $cs$Business had digital Posters to markets its work$cs$, $cs$New logo, colour system, branded flyers and a WhatsApp business profile.$cs$, $cs$Walk-ins up and a recognisable brand customers now share online.$cs$, $cs$salon-before.jpg$cs$, $cs$89f935e024185750.jpg$cs$, 1, 1, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO portfolio (id, title, industry, problem, solution, result, before_image, after_image, is_active, sort_order, created_at) VALUES (2, $cs$Kampala Fresh Grocers$cs$, $cs$Shop$cs$, $cs$Plain printed price lists and no social presence.$cs$, $cs$Logo, branded poster set and a 5-post social media starter pack.$cs$, $cs$A consistent shopfront and a feed that looks trustworthy to new buyers.$cs$, $cs$grocers-before.jpg$cs$, $cs$grocers-after.jpg$cs$, 1, 2, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO portfolio (id, title, industry, problem, solution, result, before_image, after_image, is_active, sort_order, created_at) VALUES (3, $cs$Mama's Kitchen Restaurant$cs$, $cs$Restaurant$cs$, $cs$Menu photos taken on a phone with no branding, hard to read.$cs$, $cs$Designed menu, banner for the entrance and matching social posts.$cs$, $cs$A polished menu that raised perceived value and order confidence.$cs$, $cs$restaurant-before.jpg$cs$, $cs$restaurant-after.jpg$cs$, 1, 3, $cs$2026-06-28 12:00:26$cs$);
INSERT INTO portfolio (id, title, industry, problem, solution, result, before_image, after_image, is_active, sort_order, created_at) VALUES (4, $cs$Bright Smile Dental Clinic$cs$, $cs$Clinic$cs$, $cs$No professional identity for a clinic that needed to signal trust.$cs$, $cs$Clean medical-grade logo, letterhead, business cards and a 3-page website.$cs$, $cs$A credible online presence patients can find and book through.$cs$, $cs$clinic-before.jpg$cs$, $cs$clinic-after.jpg$cs$, 1, 4, $cs$2026-06-28 12:00:26$cs$);

-- leads (1 rows)
INSERT INTO leads (id, name, whatsapp, business_name, service_type, budget, message, source, status, created_at) VALUES (1, $cs$Sarah N.$cs$, $cs$256712345678$cs$, $cs$Glow Salon$cs$, $cs$Business Growth Pack$cs$, $cs$UGX 150,000$cs$, $cs$Interested in the Visibility Pack for my salon.$cs$, $cs$contact$cs$, $cs$New$cs$, $cs$2026-06-28 12:00:26$cs$);

-- settings (6 rows)
INSERT INTO settings (skey, sval) VALUES ($cs$email$cs$, $cs$hello@cheapastudio.com$cs$);
INSERT INTO settings (skey, sval) VALUES ($cs$location$cs$, $cs$Kampala, Uganda$cs$);
INSERT INTO settings (skey, sval) VALUES ($cs$logo$cs$, $cs$$cs$);
INSERT INTO settings (skey, sval) VALUES ($cs$phone_1$cs$, $cs$+256 753 168599$cs$);
INSERT INTO settings (skey, sval) VALUES ($cs$site_tagline$cs$, $cs$Lets Grow Together$cs$);
INSERT INTO settings (skey, sval) VALUES ($cs$whatsapp_number$cs$, $cs$256753168599$cs$);

-- (no rows in orders)

-- (no rows in order_items)

-- pack_items (32 rows)
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (1, 1, $cs$Logo design$cs$, $cs$A memorable logo delivered in every format you need.$cs$, $cs$7dc4c0c14812ca4f.jpg$cs$, 1);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (2, 1, $cs$100 Business cards$cs$, $cs$Professionally designed, print-ready business cards.$cs$, $cs$29c743a5d0392c88.jpg$cs$, 2);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (3, 1, $cs$50 Flyers$cs$, $cs$Eye-catching flyers designed to bring in customers.$cs$, $cs$9f28e9c897fe0f39.jpg$cs$, 3);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (4, 1, $cs$WhatsApp profile branding$cs$, $cs$A polished company profile that builds trust with clients.$cs$, $cs$157de0f59759b5c7.jpg$cs$, 4);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (5, 1, $cs$1 Social media post$cs$, $cs$Ready-to-post social media designs for a consistent feed.$cs$, $cs$2fbb1b6bc40de1ba.jpg$cs$, 5);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (6, 2, $cs$Logo design$cs$, $cs$A memorable logo delivered in every format you need.$cs$, $cs$aaf056bdcda1.jpg$cs$, 1);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (7, 2, $cs$200 Business cards$cs$, $cs$Professionally designed, print-ready business cards.$cs$, $cs$efb0ad53a6a0.jpg$cs$, 2);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (8, 2, $cs$100 Flyers$cs$, $cs$Eye-catching flyers designed to bring in customers.$cs$, $cs$f1b0ab4ca316.jpg$cs$, 3);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (9, 2, $cs$Banner design$cs$, $cs$A bold banner for your shopfront or events.$cs$, $cs$556eb3149f09.jpg$cs$, 4);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (10, 2, $cs$Receipt book$cs$, $cs$A branded receipt book for clean, trusted transactions.$cs$, $cs$82ac331566a3.jpg$cs$, 5);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (11, 2, $cs$3 Social media posts$cs$, $cs$Ready-to-post social media designs for a consistent feed.$cs$, $cs$40665c714d3c.jpg$cs$, 6);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (12, 3, $cs$Logo design$cs$, $cs$A memorable logo delivered in every format you need.$cs$, $cs$43ea40ae609e.jpg$cs$, 1);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (13, 3, $cs$200 Business cards$cs$, $cs$Professionally designed, print-ready business cards.$cs$, $cs$6e236e3f1e1b.jpg$cs$, 2);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (14, 3, $cs$100 Flyers$cs$, $cs$Eye-catching flyers designed to bring in customers.$cs$, $cs$1959647c84a2.jpg$cs$, 3);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (15, 3, $cs$Receipt book$cs$, $cs$A branded receipt book for clean, trusted transactions.$cs$, $cs$d7a993d9651c.jpg$cs$, 4);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (16, 3, $cs$Banner design$cs$, $cs$A bold banner for your shopfront or events.$cs$, $cs$befcc8bc978e.jpg$cs$, 5);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (17, 3, $cs$Letterhead$cs$, $cs$Branded letterhead for official documents and quotes.$cs$, $cs$879b6b1285f5.jpg$cs$, 6);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (18, 3, $cs$Company profile$cs$, $cs$A polished company profile that builds trust with clients.$cs$, $cs$36d1b776a1f6.jpg$cs$, 7);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (19, 3, $cs$5 Social media posts$cs$, $cs$Ready-to-post social media designs for a consistent feed.$cs$, $cs$b02b89f67f6a.jpg$cs$, 8);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (20, 3, $cs$3-page website$cs$, $cs$A clean, mobile-friendly website that works on every phone.$cs$, $cs$ae3b1d176b12.jpg$cs$, 9);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (21, 3, $cs$Google Business Profile setup$cs$, $cs$Google Business Profile set up so customers find you on Maps and Search.$cs$, $cs$76591aa13673.jpg$cs$, 10);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (22, 4, $cs$Logo design$cs$, $cs$A memorable logo delivered in every format you need.$cs$, $cs$99ffc3391358.jpg$cs$, 1);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (23, 4, $cs$200 Business cards$cs$, $cs$Professionally designed, print-ready business cards.$cs$, $cs$a0a80f052b0e.jpg$cs$, 2);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (24, 4, $cs$100 Flyers$cs$, $cs$Eye-catching flyers designed to bring in customers.$cs$, $cs$15cbf91d0eca.jpg$cs$, 3);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (25, 4, $cs$Receipt book$cs$, $cs$A branded receipt book for clean, trusted transactions.$cs$, $cs$673bf2df283e.jpg$cs$, 4);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (26, 4, $cs$WhatsApp branding$cs$, $cs$WhatsApp profile, catalog and status branding.$cs$, $cs$aa1b6e5c0616.jpg$cs$, 5);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (27, 4, $cs$Banner design$cs$, $cs$A bold banner for your shopfront or events.$cs$, $cs$77cea05f22fa.jpg$cs$, 6);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (28, 4, $cs$Letterhead$cs$, $cs$Branded letterhead for official documents and quotes.$cs$, $cs$a8823a0e8a2c.jpg$cs$, 7);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (29, 4, $cs$Company profile$cs$, $cs$A polished company profile that builds trust with clients.$cs$, $cs$53639afd4abb.jpg$cs$, 8);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (30, 4, $cs$5 Social media posts$cs$, $cs$Ready-to-post social media designs for a consistent feed.$cs$, $cs$79ceb2e80e5b.jpg$cs$, 9);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (31, 4, $cs$5-page website$cs$, $cs$A clean, mobile-friendly website that works on every phone.$cs$, $cs$f8a15ed93171.jpg$cs$, 10);
INSERT INTO pack_items (id, pack_id, label, blurb, image, sort_order) VALUES (32, 4, $cs$Google Business Profile setup$cs$, $cs$Google Business Profile set up so customers find you on Maps and Search.$cs$, $cs$a77d98cc8958.jpg$cs$, 11);

-- Reset id sequences to MAX(id)
SELECT setval(pg_get_serial_sequence('admin_users','id'), COALESCE((SELECT MAX(id) FROM admin_users), 1), true);
SELECT setval(pg_get_serial_sequence('packs','id'), COALESCE((SELECT MAX(id) FROM packs), 1), true);
SELECT setval(pg_get_serial_sequence('products','id'), COALESCE((SELECT MAX(id) FROM products), 1), true);
SELECT setval(pg_get_serial_sequence('portfolio','id'), COALESCE((SELECT MAX(id) FROM portfolio), 1), true);
SELECT setval(pg_get_serial_sequence('leads','id'), COALESCE((SELECT MAX(id) FROM leads), 1), true);
SELECT setval(pg_get_serial_sequence('orders','id'), COALESCE((SELECT MAX(id) FROM orders), 1), true);
SELECT setval(pg_get_serial_sequence('order_items','id'), COALESCE((SELECT MAX(id) FROM order_items), 1), true);
SELECT setval(pg_get_serial_sequence('pack_items','id'), COALESCE((SELECT MAX(id) FROM pack_items), 1), true);
