-- Food Ordering and Delivery System
-- Author: Christy Ramos
-- Local: create a database named `food_delivery` in phpMyAdmin, select it, then Import this file.
-- InfinityFree: create the database in the control panel, open phpMyAdmin, select it, then Import this file.

DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;

CREATE TABLE menu_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    category    VARCHAR(50)   NOT NULL DEFAULT 'Main Dish',
    description TEXT,
    price       DECIMAL(8,2)  NOT NULL,
    available   TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100)  NOT NULL,
    phone         VARCHAR(20)   NOT NULL,
    address       VARCHAR(255)  NOT NULL,
    menu_item_id  INT           NOT NULL,
    quantity      INT           NOT NULL DEFAULT 1,
    total_price   DECIMAL(10,2) NOT NULL,
    status        ENUM('Pending','Preparing','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    notes         VARCHAR(255)  DEFAULT NULL,
    order_date    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_menu FOREIGN KEY (menu_item_id)
        REFERENCES menu_items(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO menu_items (name, category, description, price, available) VALUES
('Chicken Adobo Meal', 'Main Dish', 'Classic Filipino adobo served with garlic rice', 120.00, 1),
('Pork Sisig', 'Main Dish', 'Sizzling chopped pork with onions and chili', 150.00, 1),
('Chicken Burger', 'Burgers', 'Crispy chicken patty with lettuce and mayo', 95.00, 1),
('Halo-Halo', 'Dessert', 'Shaved ice with sweet beans, fruits and leche flan', 80.00, 1),
('Iced Tea (Large)', 'Drinks', 'Freshly brewed lemon iced tea', 45.00, 1),
('Pancit Canton', 'Noodles', 'Stir-fried noodles with vegetables and chicken', 100.00, 0);

INSERT INTO orders (customer_name, phone, address, menu_item_id, quantity, total_price, status, notes) VALUES
('Juan Dela Cruz', '09171234567', 'Brgy. Poblacion, Barotac Nuevo, Iloilo', 1, 2, 240.00, 'Delivered', 'Extra rice please'),
('Maria Santos', '09281234567', 'Brgy. San Jose, Dumangas, Iloilo', 2, 1, 150.00, 'Preparing', NULL),
('Pedro Reyes', '09391234567', 'Brgy. Bagumbayan, Barotac Nuevo, Iloilo', 3, 3, 285.00, 'Pending', 'No onions');
