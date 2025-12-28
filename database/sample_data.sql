-- ============================================
-- Waslah E-Commerce Sample Data
-- Run this after schema.sql
-- ============================================

USE waslah_ecom;

-- ============================================
-- SAMPLE PRODUCTS - Men's Category
-- ============================================

INSERT INTO products (store_id, category_id, name, slug, description, short_description, price, sale_price, sku, stock_quantity, is_featured, is_new, status) VALUES
-- Men's T-Shirts (category_id = 4)
(1, 4, 'Classic Cotton Crew Neck T-Shirt', 'classic-cotton-crew-neck-tshirt', 'Premium quality 100% cotton t-shirt with a comfortable fit. Perfect for everyday wear.', 'Soft, breathable cotton t-shirt for everyday comfort.', 29.99, 24.99, 'MEN-TS-001', 100, 1, 1, 'active'),
(1, 4, 'Premium V-Neck T-Shirt', 'premium-vneck-tshirt', 'Stylish V-neck t-shirt made from premium cotton blend. Modern fit for a sleek look.', 'Modern V-neck design with premium cotton blend.', 34.99, NULL, 'MEN-TS-002', 75, 0, 1, 'active'),
(1, 4, 'Graphic Print Urban Tee', 'graphic-print-urban-tee', 'Bold graphic print t-shirt with urban-inspired design. Stand out from the crowd.', 'Eye-catching graphic print for street style.', 39.99, 32.99, 'MEN-TS-003', 50, 1, 0, 'active'),

-- Men's Shirts (category_id = 5)
(1, 5, 'Oxford Button-Down Shirt', 'oxford-button-down-shirt', 'Classic Oxford shirt with button-down collar. Perfect for business casual occasions.', 'Timeless Oxford shirt for professional style.', 59.99, NULL, 'MEN-SH-001', 60, 1, 1, 'active'),
(1, 5, 'Slim Fit Dress Shirt', 'slim-fit-dress-shirt', 'Elegant slim fit dress shirt with wrinkle-resistant fabric. Ideal for formal events.', 'Sophisticated dress shirt with slim fit.', 69.99, 54.99, 'MEN-SH-002', 45, 0, 0, 'active'),
(1, 5, 'Casual Linen Shirt', 'casual-linen-shirt', 'Breathable linen shirt perfect for summer. Relaxed fit for maximum comfort.', 'Lightweight linen for warm weather comfort.', 54.99, NULL, 'MEN-SH-003', 40, 0, 1, 'active'),

-- Men's Jeans (category_id = 6)
(1, 6, 'Classic Straight Fit Jeans', 'classic-straight-fit-jeans', 'Timeless straight fit jeans with comfortable stretch denim. A wardrobe essential.', 'Classic straight fit for everyday wear.', 79.99, 64.99, 'MEN-JN-001', 80, 1, 0, 'active'),
(1, 6, 'Slim Fit Stretch Jeans', 'slim-fit-stretch-jeans', 'Modern slim fit jeans with stretch for comfort and mobility. Perfect fit guaranteed.', 'Slim fit with stretch comfort.', 84.99, NULL, 'MEN-JN-002', 65, 1, 1, 'active'),

-- Men's Jackets (category_id = 7)
(1, 7, 'Classic Leather Jacket', 'classic-leather-jacket', 'Genuine leather jacket with classic motorcycle styling. Timeless piece for any wardrobe.', 'Iconic leather jacket design.', 299.99, 249.99, 'MEN-JK-001', 25, 1, 0, 'active'),
(1, 7, 'Lightweight Bomber Jacket', 'lightweight-bomber-jacket', 'Versatile bomber jacket perfect for transitional weather. Modern urban style.', 'Stylish bomber for any occasion.', 129.99, NULL, 'MEN-JK-002', 35, 0, 1, 'active');

-- ============================================
-- SAMPLE PRODUCTS - Women's Category
-- ============================================

INSERT INTO products (store_id, category_id, name, slug, description, short_description, price, sale_price, sku, stock_quantity, is_featured, is_new, status) VALUES
-- Women's Dresses (category_id = 9)
(1, 9, 'Elegant Midi Wrap Dress', 'elegant-midi-wrap-dress', 'Beautiful wrap dress with flattering silhouette. Perfect for work or special occasions.', 'Versatile wrap dress for any occasion.', 89.99, 74.99, 'WOM-DR-001', 45, 1, 1, 'active'),
(1, 9, 'Floral Summer Maxi Dress', 'floral-summer-maxi-dress', 'Stunning floral print maxi dress for summer. Lightweight and comfortable all day.', 'Beautiful floral print for summer days.', 79.99, NULL, 'WOM-DR-002', 55, 1, 0, 'active'),
(1, 9, 'Little Black Cocktail Dress', 'little-black-cocktail-dress', 'Classic little black dress with modern twist. Every woman wardrobe essential.', 'Timeless LBD for elegant evenings.', 119.99, 99.99, 'WOM-DR-003', 30, 0, 1, 'active'),

-- Women's Tops (category_id = 10)
(1, 10, 'Silk Blend Blouse', 'silk-blend-blouse', 'Luxurious silk blend blouse with elegant draping. Elevate your office wardrobe.', 'Premium silk blend for sophisticated style.', 69.99, NULL, 'WOM-TP-001', 40, 0, 1, 'active'),
(1, 10, 'Casual Oversized Sweater', 'casual-oversized-sweater', 'Cozy oversized sweater perfect for layering. Soft knit for ultimate comfort.', 'Cozy oversized fit for relaxed days.', 59.99, 49.99, 'WOM-TP-002', 60, 1, 0, 'active'),
(1, 10, 'Fitted Crop Top', 'fitted-crop-top', 'Trendy fitted crop top with modern design. Perfect for high-waisted bottoms.', 'Stylish crop top for modern looks.', 34.99, NULL, 'WOM-TP-003', 70, 0, 1, 'active'),

-- Women's Jeans (category_id = 11)
(1, 11, 'High-Waist Skinny Jeans', 'high-waist-skinny-jeans', 'Flattering high-waist skinny jeans with stretch. Perfect fit every time.', 'High-waist design for flattering fit.', 74.99, 59.99, 'WOM-JN-001', 75, 1, 1, 'active'),
(1, 11, 'Wide Leg Palazzo Pants', 'wide-leg-palazzo-pants', 'Elegant wide leg pants for a sophisticated look. Comfortable and stylish.', 'Flowing wide leg for elegant style.', 69.99, NULL, 'WOM-JN-002', 50, 0, 0, 'active');

-- ============================================
-- SAMPLE PRODUCTS - Children's Category
-- ============================================

INSERT INTO products (store_id, category_id, name, slug, description, short_description, price, sale_price, sku, stock_quantity, is_featured, is_new, status) VALUES
-- Boys (category_id = 14)
(1, 14, 'Boys Graphic T-Shirt Set', 'boys-graphic-tshirt-set', 'Fun graphic t-shirt set for active boys. Durable and easy to wash.', '3-pack of colorful graphic tees.', 39.99, 29.99, 'BOY-TS-001', 60, 1, 1, 'active'),
(1, 14, 'Boys Denim Jacket', 'boys-denim-jacket', 'Cool denim jacket for stylish boys. Perfect for layering in any season.', 'Classic denim style for boys.', 49.99, NULL, 'BOY-JK-001', 35, 0, 1, 'active'),
(1, 14, 'Boys Jogger Pants', 'boys-jogger-pants', 'Comfortable jogger pants for active kids. Soft cotton blend with elastic waist.', 'Comfy joggers for everyday play.', 29.99, 24.99, 'BOY-PT-001', 55, 0, 0, 'active'),

-- Girls (category_id = 15)
(1, 15, 'Girls Floral Dress', 'girls-floral-dress', 'Pretty floral dress for special occasions. Comfortable cotton with beautiful print.', 'Sweet floral design for little girls.', 44.99, 34.99, 'GRL-DR-001', 45, 1, 1, 'active'),
(1, 15, 'Girls Leggings Set', 'girls-leggings-set', 'Colorful leggings set for active girls. Soft stretch fabric for all-day comfort.', '4-pack of fun patterned leggings.', 34.99, NULL, 'GRL-LG-001', 50, 0, 1, 'active'),
(1, 15, 'Girls Cardigan Sweater', 'girls-cardigan-sweater', 'Adorable cardigan sweater for layering. Soft knit with cute button details.', 'Cozy cardigan for chilly days.', 39.99, 32.99, 'GRL-SW-001', 40, 0, 0, 'active'),

-- Baby (category_id = 16)
(1, 16, 'Baby Onesie Pack', 'baby-onesie-pack', 'Soft cotton onesies for babies. Snap closures for easy diaper changes.', '5-pack of adorable onesies.', 29.99, 24.99, 'BAB-ON-001', 80, 1, 1, 'active'),
(1, 16, 'Baby Romper Set', 'baby-romper-set', 'Cute romper set for little ones. Soft organic cotton for sensitive skin.', 'Organic cotton rompers for babies.', 34.99, NULL, 'BAB-RM-001', 65, 0, 1, 'active');

-- ============================================
-- ADD PRODUCT IMAGES (placeholders)
-- ============================================

INSERT INTO product_images (product_id, image_path, is_primary) VALUES
(1, 'products/placeholder-tshirt-1.jpg', 1),
(2, 'products/placeholder-tshirt-2.jpg', 1),
(3, 'products/placeholder-tshirt-3.jpg', 1),
(4, 'products/placeholder-shirt-1.jpg', 1),
(5, 'products/placeholder-shirt-2.jpg', 1),
(6, 'products/placeholder-shirt-3.jpg', 1),
(7, 'products/placeholder-jeans-1.jpg', 1),
(8, 'products/placeholder-jeans-2.jpg', 1),
(9, 'products/placeholder-jacket-1.jpg', 1),
(10, 'products/placeholder-jacket-2.jpg', 1),
(11, 'products/placeholder-dress-1.jpg', 1),
(12, 'products/placeholder-dress-2.jpg', 1),
(13, 'products/placeholder-dress-3.jpg', 1),
(14, 'products/placeholder-top-1.jpg', 1),
(15, 'products/placeholder-top-2.jpg', 1),
(16, 'products/placeholder-top-3.jpg', 1),
(17, 'products/placeholder-jeans-3.jpg', 1),
(18, 'products/placeholder-pants-1.jpg', 1),
(19, 'products/placeholder-kids-1.jpg', 1),
(20, 'products/placeholder-kids-2.jpg', 1),
(21, 'products/placeholder-kids-3.jpg', 1),
(22, 'products/placeholder-kids-4.jpg', 1),
(23, 'products/placeholder-kids-5.jpg', 1),
(24, 'products/placeholder-kids-6.jpg', 1),
(25, 'products/placeholder-baby-1.jpg', 1),
(26, 'products/placeholder-baby-2.jpg', 1);

-- ============================================
-- ADD PRODUCT VARIANTS
-- ============================================

-- Add size variants for first few products
INSERT INTO product_variants (product_id, size, stock_quantity, status) VALUES
(1, 'S', 20, 1), (1, 'M', 30, 1), (1, 'L', 30, 1), (1, 'XL', 20, 1),
(2, 'S', 15, 1), (2, 'M', 25, 1), (2, 'L', 25, 1), (2, 'XL', 10, 1),
(7, '30', 15, 1), (7, '32', 25, 1), (7, '34', 25, 1), (7, '36', 15, 1),
(8, '30', 12, 1), (8, '32', 20, 1), (8, '34', 20, 1), (8, '36', 13, 1),
(11, 'XS', 10, 1), (11, 'S', 15, 1), (11, 'M', 10, 1), (11, 'L', 10, 1),
(17, '26', 15, 1), (17, '28', 25, 1), (17, '30', 25, 1), (17, '32', 10, 1);

-- Add color variants
INSERT INTO product_variants (product_id, color, color_code, stock_quantity, status) VALUES
(1, 'Black', '#000000', 40, 1), (1, 'White', '#FFFFFF', 30, 1), (1, 'Navy', '#1a1a2e', 30, 1),
(3, 'Black', '#000000', 25, 1), (3, 'Red', '#e94560', 25, 1),
(9, 'Black', '#000000', 15, 1), (9, 'Brown', '#8B4513', 10, 1);

-- ============================================
-- SAMPLE BANNERS
-- ============================================

INSERT INTO banners (store_id, title, subtitle, image, link, button_text, position, sort_order, status) VALUES
(1, 'New Season Collection', 'Discover the latest trends in fashion', 'banners/hero-1.jpg', 'shop', 'Shop Now', 'home_slider', 1, 1),
(1, 'Summer Sale', 'Up to 50% off on selected items', 'banners/hero-2.jpg', 'shop?sale=1', 'Shop Sale', 'home_slider', 2, 1),
(1, 'Kids Collection', 'Stylish clothes for your little ones', 'banners/hero-3.jpg', 'shop/category/children', 'Shop Kids', 'home_slider', 3, 1);

-- ============================================
-- SAMPLE COUPONS
-- ============================================

INSERT INTO coupons (store_id, code, type, value, minimum_amount, usage_limit, status) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 50.00, 100, 1),
(1, 'SUMMER20', 'percentage', 20.00, 100.00, 50, 1),
(1, 'FLAT15', 'fixed', 15.00, 75.00, NULL, 1);

SELECT 'Sample data inserted successfully!' as Message;
