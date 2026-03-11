-- 1. Create Hero Slides Table
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    button_text VARCHAR(50) DEFAULT 'Shop Now',
    button_link VARCHAR(255) DEFAULT '#',
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Ensure Categories Table has image support
-- Categories already have 'hero_image', let's also ensure a dedicated 'image' for the grid.
ALTER TABLE categories ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;

-- 3. Update/Insert Categories with provided list
-- We use INSERT IGNORE and then update names to ensure we match the user's list
INSERT IGNORE INTO categories (id, name, slug) VALUES 
(1, 'Kitchen Cabinet', 'kitchen-cabinet'),
(2, 'Kitchen Accessories', 'kitchen-accessories'),
(3, 'Kitchen Hood', 'kitchen-hood'),
(4, 'Gas Stove', 'gas-stove'),
(5, 'Sink', 'sink'),
(6, 'Dish Rack', 'dish-rack'),
(7, 'Geyser', 'geyser'),
(8, 'Water Purifier', 'water-purifier'),
(9, 'Bath Appliances', 'bath-appliances'),
(10, 'Kitchen Appliances', 'kitchen-appliances'),
(11, 'Home Appliance', 'home-appliance');

-- Ensure names are exactly as requested and add placeholder images for the grid
UPDATE categories SET name = 'Kitchen Cabinet', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Kitchen+Cabinet' WHERE id = 1;
UPDATE categories SET name = 'Kitchen Accessories', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Kitchen+Accessories' WHERE id = 2;
UPDATE categories SET name = 'Kitchen Hood', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Kitchen+Hood' WHERE id = 3;
UPDATE categories SET name = 'Gas Stove', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Gas+Strove' WHERE id = 4;
UPDATE categories SET name = 'Sink', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Sink' WHERE id = 5;
UPDATE categories SET name = 'Dish Rack', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Dish+Rack' WHERE id = 6;
UPDATE categories SET name = 'Geyser', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Geyser' WHERE id = 7;
UPDATE categories SET name = 'Water Purifier', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Water+Purifier' WHERE id = 8;
UPDATE categories SET name = 'Bath Appliances', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Bath+Appliances' WHERE id = 9;
UPDATE categories SET name = 'Kitchen Appliances', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Kitchen+Appliances' WHERE id = 10;
UPDATE categories SET name = 'Home Appliance', image = 'https://placehold.co/400x400/f5f5f5/aaa?text=Home+Appliance' WHERE id = 11;

-- 4. Initial dynamic slides (will be replaced by admin)
INSERT IGNORE INTO hero_slides (image_path, title, subtitle, button_text, button_link) VALUES 
('https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=1600', 'Transform Your Kitchen Space', 'Premium Kitchen Solutions trusted by 5,000+ homes.', 'Shop Now', '#featured'),
('https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=1600', 'Premium Sinks & Hoods', 'Modern designs for your dream kitchen.', 'View Collection', 'category/sink');
