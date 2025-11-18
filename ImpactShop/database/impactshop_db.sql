-- Base de données pour ImpactShop
-- Créer et utiliser la base de données

CREATE DATABASE IF NOT EXISTS impactshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE impactshop_db;

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(255) NOT NULL,
    name_fr VARCHAR(255) NOT NULL,
    description_en TEXT NOT NULL,
    description_fr TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    img_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_price (price),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des clients
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'paypal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des détails de commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données de test
INSERT INTO products (name_en, name_fr, description_en, description_fr, price, img_name) VALUES
('Water Filter', 'Filtre à Eau', 'Provides clean drinking water for families in need. Each filter can purify up to 1000 liters of water.', 'Fournit de l\'eau potable propre aux familles dans le besoin. Chaque filtre peut purifier jusqu\'à 1000 litres d\'eau.', 49.99, 'water-filter.jpeg'),
('Emergency Food Package', 'Paquet Alimentaire d\'Urgence', 'Contains essential nutrition for a family of 4 for one week. Includes rice, beans, oil, and canned goods.', 'Contient une nutrition essentielle pour une famille de 4 personnes pendant une semaine. Comprend du riz, des haricots, de l\'huile et des conserves.', 75.00, 'food-package.jpg'),
('Medical Kit', 'Kit Médical', 'Complete first aid kit with bandages, antiseptics, and basic medications for emergency situations.', 'Kit de premiers soins complet avec bandages, antiseptiques et médicaments de base pour les situations d\'urgence.', 89.99, 'medical-kit.jpg'),
('Blanket Set', 'Ensemble de Couvertures', 'Warm thermal blankets for families affected by cold weather or displacement. Set of 5 blankets.', 'Couvertures thermiques chaudes pour les familles touchées par le froid ou le déplacement. Ensemble de 5 couvertures.', 35.50, 'blanket-set.jpeg'),
('School Supplies Kit', 'Kit de Fournitures Scolaires', 'Complete school supplies for one student including notebooks, pens, pencils, and educational materials.', 'Fournitures scolaires complètes pour un élève, y compris cahiers, stylos, crayons et matériel éducatif.', 25.00, 'school-kit.jpg'),
('Hygiene Kit', 'Kit d\'Hygiène', 'Essential hygiene products including soap, toothpaste, shampoo, and sanitary items for a family.', 'Produits d\'hygiène essentiels comprenant savon, dentifrice, shampooing et articles sanitaires pour une famille.', 30.00, 'hygiene-kit.jpg');

-- Insertion d'un client de test
INSERT INTO customers (first_name, last_name, email, phone) VALUES
('Jean', 'Dupont', 'jean.dupont@example.com', '+33 6 12 34 56 78'),
('Marie', 'Martin', 'marie.martin@example.com', '+33 6 98 76 54 32');

-- Insertion d'une commande de test
INSERT INTO orders (customer_id, total_amount, status, payment_method) VALUES
(1, 124.99, 'paid', 'paypal');

-- Insertion des articles de la commande
INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES
(1, 1, 1, 49.99, 49.99),
(1, 4, 2, 35.50, 71.00);