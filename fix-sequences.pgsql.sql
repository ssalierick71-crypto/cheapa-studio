-- ============================================================
--  Fix auto-increment ID sequences after the data import.
--  Safe to run any time (idempotent). Run in the Supabase SQL editor.
--  This makes new inserts (add product/pack/portfolio/order) use the
--  next free ID instead of colliding with an existing one.
-- ============================================================
SELECT setval(pg_get_serial_sequence('admin_users','id'), COALESCE((SELECT MAX(id) FROM admin_users),1), true);
SELECT setval(pg_get_serial_sequence('packs','id'),       COALESCE((SELECT MAX(id) FROM packs),1),       true);
SELECT setval(pg_get_serial_sequence('products','id'),    COALESCE((SELECT MAX(id) FROM products),1),    true);
SELECT setval(pg_get_serial_sequence('portfolio','id'),   COALESCE((SELECT MAX(id) FROM portfolio),1),   true);
SELECT setval(pg_get_serial_sequence('leads','id'),       COALESCE((SELECT MAX(id) FROM leads),1),       true);
SELECT setval(pg_get_serial_sequence('orders','id'),      COALESCE((SELECT MAX(id) FROM orders),1),      true);
SELECT setval(pg_get_serial_sequence('order_items','id'), COALESCE((SELECT MAX(id) FROM order_items),1), true);
SELECT setval(pg_get_serial_sequence('pack_items','id'),  COALESCE((SELECT MAX(id) FROM pack_items),1),  true);
SELECT setval(pg_get_serial_sequence('visits','id'),      COALESCE((SELECT MAX(id) FROM visits),1),      true);
