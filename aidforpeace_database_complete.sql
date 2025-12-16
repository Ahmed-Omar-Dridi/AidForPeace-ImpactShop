-- ============================================
-- AIDFORPEACE - BASE DE DONNÉES COMPLÈTE
-- Version: 2.0
-- Date: Décembre 2025
-- ============================================
-- Ce fichier crée TOUTES les tables nécessaires
-- pour le fonctionnement COMPLET du site AidForPeace
-- Inclut: Users, Messagerie, Map, Donations, Shop, Loyalty
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- CRÉATION DE LA BASE DE DONNÉES
-- ============================================
CREATE DATABASE IF NOT EXISTS `aidforpeace_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aidforpeace_db`;

-- ============================================
-- ============================================
--          SECTION 1: UTILISATEURS
-- ============================================
-- ============================================

-- 1.1 TABLE UTILISATEURS PRINCIPALE (user)
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id_user` INT(11) NOT NULL AUTO_INCREMENT,
    `Prenom` VARCHAR(50) NOT NULL,
    `nom` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `date_naissance` DATE NOT NULL,
    `role` ENUM('user','admin') DEFAULT 'user',
    `points` INT(11) DEFAULT 0,
    `photo` VARCHAR(255) DEFAULT 'default.jpg',
    `bio` TEXT DEFAULT NULL,
    `bio_type` ENUM('text','audio') DEFAULT 'text',
    `bio_audio_path` VARCHAR(255) DEFAULT '',
    `location_country` VARCHAR(100) DEFAULT '',
    `location_city` VARCHAR(100) DEFAULT '',
    `location_latitude` DECIMAL(10,8) DEFAULT NULL,
    `location_longitude` DECIMAL(11,8) DEFAULT NULL,
    `location_timezone` VARCHAR(50) DEFAULT 'UTC',
    `location_public` TINYINT(1) DEFAULT 0,
    `two_factor_enabled` TINYINT(1) DEFAULT 0,
    `two_factor_secret` VARCHAR(255) DEFAULT '',
    `two_factor_method` ENUM('app','sms','email') DEFAULT 'app',
    `status` ENUM('online','offline','away','busy') DEFAULT 'offline',
    `status_message` VARCHAR(255) DEFAULT '',
    `last_activity` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `is_online` TINYINT(1) DEFAULT 0,
    `badges` VARCHAR(50) DEFAULT 'beginner',
    `rank` VARCHAR(50) DEFAULT 'bronze',
    `niveau` INT(11) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `facial_data` LONGTEXT DEFAULT NULL,
    `facial_descriptor` TEXT DEFAULT NULL,
    `is_banned` TINYINT(1) DEFAULT 0,
    `ban_reason` TEXT DEFAULT NULL,
    `banned_at` DATETIME DEFAULT NULL,
    `is_suspended` TINYINT(1) DEFAULT 0,
    `suspension_end` DATETIME DEFAULT NULL,
    `email_verified` TINYINT(1) DEFAULT 0,
    `email_verified_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id_user`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`, `is_online`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.2 TABLE UTILISATEURS LEGACY (utilisateurs) - Pour messagerie
DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user', 'ngo_partner') DEFAULT 'user',
    `phone` VARCHAR(20),
    `avatar_url` VARCHAR(255),
    `bio` LONGTEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_active` INT DEFAULT 1,
    `last_login` TIMESTAMP NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données utilisateurs
INSERT INTO `user` (`Prenom`, `nom`, `email`, `password`, `date_naissance`, `role`, `points`, `photo`) VALUES
('Admin', 'System', 'admin@aidforpeace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1990-01-01', 'admin', 1000, 'default.jpg'),
('Marie', 'Dupont', 'marie.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1995-05-15', 'user', 100, 'default.jpg'),
('Ahmed', 'Ben Ali', 'ahmed.benali@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1988-03-20', 'user', 50, 'default.jpg'),
('Sophie', 'Martin', 'sophie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1992-08-10', 'user', 75, 'default.jpg'),
('Karim', 'Trabelsi', 'karim.trabelsi@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1985-12-25', 'user', 200, 'default.jpg');

INSERT INTO `utilisateurs` (`nom`, `email`, `password`, `role`, `phone`) VALUES
('Admin Principal', 'admin@aidforpeace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+216 71 000 001'),
('Marie Dupont', 'marie.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '+216 71 000 002'),
('Ahmed Ben Ali', 'ahmed.benali@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '+216 71 000 003'),
('Sophie Martin', 'sophie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '+216 71 000 004'),
('Karim Trabelsi', 'karim.trabelsi@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ngo_partner', '+216 71 000 005');

-- ============================================
-- ============================================
--          SECTION 2: MESSAGERIE
-- ============================================
-- ============================================

-- 2.1 TABLE CONVERSATIONS
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `participant1_id` INT NOT NULL,
    `participant2_id` INT NOT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `last_message_id` INT NULL,
    `last_message_at` TIMESTAMP NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_conversation` (`participant1_id`, `participant2_id`),
    INDEX `idx_participant1` (`participant1_id`),
    INDEX `idx_participant2` (`participant2_id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2 TABLE MESSAGES
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `content` LONGTEXT NOT NULL,
    `sujet` VARCHAR(255) DEFAULT NULL,
    `message_type` ENUM('text', 'image', 'file', 'audio', 'video') DEFAULT 'text',
    `file_url` VARCHAR(500) NULL,
    `file_name` VARCHAR(255) NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL,
    `is_deleted` TINYINT(1) DEFAULT 0,
    `is_deleted_sender` TINYINT(1) DEFAULT 0,
    `is_deleted_receiver` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_conversation` (`conversation_id`),
    INDEX `idx_sender` (`sender_id`),
    INDEX `idx_receiver` (`receiver_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données messagerie
INSERT INTO `conversations` (`participant1_id`, `participant2_id`, `is_active`) VALUES 
(1, 2, 1), (1, 3, 1), (2, 3, 1);

INSERT INTO `messages` (`conversation_id`, `sender_id`, `receiver_id`, `content`, `is_read`) VALUES
(1, 2, 1, 'Bonjour, je voudrais savoir où en est ma commande?', 1),
(1, 1, 2, 'Bonjour! Votre commande a été expédiée hier.', 1),
(1, 2, 1, 'Parfait, merci beaucoup!', 0),
(2, 3, 1, 'Bonjour, avez-vous des t-shirts en taille XL?', 1),
(2, 1, 3, 'Oui, nous avons plusieurs modèles en XL.', 1),
(3, 2, 3, 'Salut! Tu as vu les nouveaux produits?', 1);

-- ============================================
-- ============================================
--          SECTION 3: TÉMOIGNAGES & FEEDBACK
-- ============================================
-- ============================================

-- 3.1 TABLE TÉMOIGNAGES
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `author` VARCHAR(100) NOT NULL,
    `rating` INT DEFAULT 5,
    `likes` INT DEFAULT 0,
    `shares` INT DEFAULT 0,
    `status` ENUM('pending','approved') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 TABLE COMMENTAIRES
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `testimonial_id` INT DEFAULT NULL,
    `author` VARCHAR(100) NOT NULL,
    `content` TEXT NOT NULL,
    `reactions` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('approved','pending','rejected') DEFAULT 'pending',
    FOREIGN KEY (`testimonial_id`) REFERENCES `testimonials`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données témoignages
INSERT INTO `testimonials` (`title`, `content`, `author`, `rating`, `likes`, `status`) VALUES
('Impact positif sur les jeunes', 'En tant qu''enseignant, j''ai vu l''impact incroyable des programmes jeunesse.', 'Thomas Martin', 4, 33, 'approved'),
('Transparence et efficacité', 'Ce qui m''impressionne le plus c''est la transparence dans l''utilisation des fonds.', 'Sophie Lambert', 5, 34, 'approved'),
('Bénévolat enrichissant', 'J''ai été bénévole pendant 6 mois et cette expérience a changé ma vision du monde.', 'Jean Petit', 4, 12, 'approved'),
('Projets durables', 'Les projets ne sont pas juste des solutions temporaires.', 'Alice Moreau', 5, 12, 'approved'),
('Super expérience', 'Très bon service, je recommande!', 'Jean Dupont', 5, 29, 'approved');

INSERT INTO `comments` (`testimonial_id`, `author`, `content`, `reactions`, `status`) VALUES
(1, 'Marc Lefebvre', 'En tant que parent, je confirme l''impact positif.', 1, 'approved'),
(2, 'Nathalie Simon', 'La transparence est effectivement un point fort.', 4, 'approved'),
(3, 'David Morel', 'Je souhaite devenir bénévole aussi!', 0, 'approved');

-- ============================================
-- ============================================
--          SECTION 4: MAP MONDIALE & ONG
-- ============================================
-- ============================================

-- 4.1 TABLE PAYS
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `crisis_level` ENUM('Stable','High','Critical') NOT NULL,
    `description` TEXT NOT NULL,
    `latitude` DECIMAL(10,8) NOT NULL,
    `longitude` DECIMAL(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2 TABLE ONG
DROP TABLE IF EXISTS `ngos`;
CREATE TABLE `ngos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `history` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `country_id` INT DEFAULT NULL,
    `mission` TEXT DEFAULT NULL,
    `contact_info` VARCHAR(255) DEFAULT NULL,
    `type_of_aid` VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données Map
INSERT INTO `countries` (`name`, `crisis_level`, `description`, `latitude`, `longitude`) VALUES
('Ukraine', 'Critical', 'Ongoing conflict requires humanitarian aid', 48.37940000, 31.16560000),
('Syria', 'Critical', 'Long-term humanitarian crisis', 34.80210000, 38.99680000),
('Yemen', 'Critical', 'Food insecurity and health crisis', 15.55270000, 48.51640000),
('Tunisia', 'High', 'Economic challenges and water scarcity', 33.88690000, 9.53750000),
('Palestine', 'Critical', 'Humanitarian emergency', 31.95220000, 35.23320000),
('Afghanistan', 'Critical', 'Ongoing humanitarian needs', 33.93910000, 67.71000000),
('Egypt', 'High', 'Economic development needs', 26.82060000, 30.80250000);

INSERT INTO `ngos` (`name`, `country`, `address`, `history`, `image`, `country_id`, `mission`, `contact_info`, `type_of_aid`) VALUES
('Red Cross Ukraine', 'Ukraine', 'Kyiv, Ukraine', 'La Croix-Rouge ukrainienne fournit une aide humanitaire essentielle aux victimes du conflit, incluant des soins médicaux, de la nourriture et un soutien psychologique aux familles déplacées.', 'ngo2.png', 1, 'Emergency relief', 'info@redcross.ua', 'Medical'),
('UNICEF Ukraine', 'Ukraine', 'Kyiv, Ukraine', 'UNICEF Ukraine protège les droits des enfants touchés par le conflit, assurant l\'accès à l\'éducation, aux soins de santé et au soutien psychosocial pour les plus vulnérables.', 'ngo3.png', 1, 'Child protection', 'ukraine@unicef.org', 'Education'),
('Doctors Without Borders', 'Syrie', 'Damascus, Syria', 'Médecins Sans Frontières fournit des soins médicaux d\'urgence dans les zones de conflit syriennes, opérant des hôpitaux et cliniques mobiles pour les populations civiles.', 'ngo4.png', 2, 'Medical aid', 'syria@msf.org', 'Medical'),
('World Food Programme', 'Yémen', 'Sanaa, Yemen', 'Le Programme Alimentaire Mondial combat la famine au Yémen en distribuant de la nourriture à des millions de personnes touchées par la crise humanitaire.', 'ngo5.png', 3, 'Food distribution', 'yemen@wfp.org', 'Food'),
('Red Cross Tunisia', 'Tunisie', 'Tunis, Tunisia', 'Le Croissant-Rouge tunisien aide les communautés vulnérables avec des programmes d\'accès à l\'eau potable, de santé communautaire et d\'aide aux migrants.', 'ngo6.png', 4, 'Water and aid', 'tunisia@redcross.org', 'Water');

-- ============================================
-- ============================================
--          SECTION 5: DONATIONS
-- ============================================
-- ============================================

-- 5.1 TABLE CAMPAGNES DE DONATION
DROP TABLE IF EXISTS `donation_campaigns`;
CREATE TABLE `donation_campaigns` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title_en` VARCHAR(255) NOT NULL,
    `title_fr` VARCHAR(255) NOT NULL,
    `description_en` LONGTEXT,
    `description_fr` LONGTEXT,
    `goal_amount` DECIMAL(12, 2) NOT NULL,
    `current_amount` DECIMAL(12, 2) DEFAULT 0,
    `currency` VARCHAR(3) DEFAULT 'TND',
    `image_url` VARCHAR(500),
    `category` ENUM('education', 'health', 'food', 'shelter', 'emergency', 'environment', 'other') DEFAULT 'other',
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_active` (`is_active`),
    INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.2 TABLE DONATIONS
DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(100),
    `last_name` VARCHAR(100),
    `donor_name` VARCHAR(255),
    `donor_email` VARCHAR(255),
    `email` VARCHAR(255),
    `donor_phone` VARCHAR(20),
    `phone` VARCHAR(20),
    `user_id` INT NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'TND',
    `message` LONGTEXT,
    `campaign_id` INT NULL,
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `transaction_id` VARCHAR(100),
    `payment_method` VARCHAR(50) DEFAULT 'card',
    `is_anonymous` TINYINT(1) DEFAULT 0,
    `newsletter` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_campaign` (`campaign_id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données Donations
INSERT INTO `donation_campaigns` (`title_en`, `title_fr`, `description_en`, `description_fr`, `goal_amount`, `current_amount`, `category`, `start_date`, `is_active`, `is_featured`) VALUES
('Education for All', 'Éducation pour Tous', 'Help provide education to children', 'Aidez à fournir une éducation aux enfants', 50000.00, 32500.00, 'education', '2024-01-01', 1, 1),
('Clean Water Initiative', 'Initiative Eau Propre', 'Bring clean water to communities', 'Apporter de l''eau propre aux communautés', 30000.00, 18750.00, 'health', '2024-03-01', 1, 1),
('Food Security Program', 'Programme Sécurité Alimentaire', 'Fight hunger in communities', 'Lutter contre la faim', 25000.00, 12000.00, 'food', '2024-02-01', 1, 0),
('Emergency Relief Fund', 'Fonds d''Aide d''Urgence', 'Rapid response to emergencies', 'Réponse rapide aux urgences', 100000.00, 45000.00, 'emergency', '2024-01-01', 1, 1);

INSERT INTO `donations` (`first_name`, `last_name`, `donor_name`, `donor_email`, `email`, `amount`, `message`, `campaign_id`, `status`, `transaction_id`, `payment_method`) VALUES
('Marie', 'Dupont', 'Marie Dupont', 'marie.dupont@email.com', 'marie.dupont@email.com', 50.00, 'Pour l''éducation des enfants', 1, 'completed', 'DON-2024-001', 'card'),
(NULL, NULL, 'Anonyme', NULL, NULL, 100.00, NULL, 1, 'completed', 'DON-2024-002', 'paypal'),
('Ahmed', 'Ben Ali', 'Ahmed Ben Ali', 'ahmed.benali@email.com', 'ahmed.benali@email.com', 75.00, 'Soutien à l''eau propre', 2, 'completed', 'DON-2024-003', 'card'),
('Sophie', 'Martin', 'Sophie Martin', 'sophie.martin@email.com', 'sophie.martin@email.com', 200.00, 'Pour les urgences', 4, 'completed', 'DON-2024-004', 'bank_transfer');


-- ============================================
-- ============================================
--          SECTION 6: BOUTIQUE (SHOP)
-- ============================================
-- ============================================

-- 6.1 TABLE CATÉGORIES
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(255) NOT NULL,
    `name_fr` VARCHAR(255) NOT NULL,
    `description_en` TEXT,
    `description_fr` TEXT,
    `img_name` VARCHAR(255),
    `parent_id` INT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.2 TABLE PRODUITS
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(255) NOT NULL,
    `name_fr` VARCHAR(255) NOT NULL,
    `description_en` TEXT,
    `description_fr` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `img_name` VARCHAR(255),
    `stock` INT DEFAULT 0,
    `category_id` INT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.3 TABLE CLIENTS
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `postal_code` VARCHAR(20),
    `country` VARCHAR(100) DEFAULT 'Tunisia',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_email` (`email`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.4 TABLE COMMANDES
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'paid', 'processing', 'shipped', 'completed', 'delivered', 'cancelled') DEFAULT 'pending',
    `payment_method` VARCHAR(50),
    `shipping_address` TEXT,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.5 TABLE ARTICLES DE COMMANDE
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_product` (`product_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.6 TABLE HISTORIQUE STATUTS COMMANDES
DROP TABLE IF EXISTS `order_status_history`;
CREATE TABLE `order_status_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `old_status` VARCHAR(50),
    `new_status` VARCHAR(50) NOT NULL,
    `changed_by` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.7 TABLE PAIEMENTS
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `method` VARCHAR(50),
    `transaction_id` VARCHAR(100),
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.8 TABLE LIVRAISONS
DROP TABLE IF EXISTS `shippings`;
CREATE TABLE `shippings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `tracking_number` VARCHAR(100),
    `carrier` VARCHAR(100) DEFAULT 'ImpactShop Express',
    `address` TEXT,
    `city` VARCHAR(100),
    `postal_code` VARCHAR(20),
    `country` VARCHAR(100) DEFAULT 'Tunisia',
    `shipping_cost` DECIMAL(10, 2) DEFAULT 0,
    `estimated_delivery` DATE NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'in_transit', 'delivered') DEFAULT 'pending',
    `shipped_at` TIMESTAMP NULL,
    `delivered_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_tracking` (`tracking_number`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.9 TABLE AVIS / REVIEWS
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `customer_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `title` VARCHAR(255),
    `comment` TEXT,
    `is_approved` TINYINT(1) DEFAULT 0,
    `is_verified` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_product` (`product_id`),
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_approved` (`is_approved`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.10 TABLE ZONES DE LIVRAISON
DROP TABLE IF EXISTS `delivery_zones`;
CREATE TABLE `delivery_zones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `regions` TEXT,
    `base_cost` DECIMAL(10, 2) NOT NULL,
    `free_shipping_threshold` DECIMAL(10, 2),
    `estimated_days` INT,
    `is_active` TINYINT(1) DEFAULT 1,
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données Boutique - Catégories
INSERT INTO `categories` (`name_en`, `name_fr`, `description_en`, `description_fr`, `is_active`) VALUES
('Clothing', 'Vêtements', 'T-shirts, hoodies and more', 'T-shirts, sweats et plus', 1),
('Accessories', 'Accessoires', 'Caps, bags, bracelets', 'Casquettes, sacs, bracelets', 1),
('Home', 'Maison', 'Posters, decorations', 'Affiches, décorations', 1),
('Eco-friendly', 'Écologique', 'Sustainable products', 'Produits durables', 1);

-- Données Boutique - Produits
INSERT INTO `products` (`name_en`, `name_fr`, `description_en`, `description_fr`, `price`, `img_name`, `stock`, `category_id`, `is_active`) VALUES
('Peace T-Shirt', 'T-Shirt Paix', 'Comfortable cotton t-shirt with peace symbol', 'T-shirt en coton avec symbole de paix', 25.00, 'image1.jpg', 50, 1, 1),
('Hope Hoodie', 'Sweat Espoir', 'Warm hoodie with hope message', 'Sweat chaud avec message d''espoir', 45.00, 'image2.jpg', 30, 1, 1),
('Unity Cap', 'Casquette Unité', 'Stylish cap promoting unity', 'Casquette stylée promouvant l''unité', 18.00, 'image3.jpg', 100, 2, 1),
('Charity Bracelet', 'Bracelet Charité', 'Handmade bracelet supporting charity', 'Bracelet fait main soutenant la charité', 12.00, 'image4.jpeg', 200, 2, 1),
('Eco Water Bottle', 'Gourde Écologique', 'Reusable water bottle', 'Gourde réutilisable', 22.00, 'water_filter.jpeg', 75, 4, 1),
('Solidarity Bag', 'Sac Solidarité', 'Eco-friendly tote bag', 'Sac fourre-tout écologique', 15.00, 'image6.jpg', 60, 2, 1),
('Peace Poster', 'Affiche Paix', 'Beautiful peace poster', 'Belle affiche de paix', 10.00, 'image7.jpeg', 150, 3, 1),
('Donation Box', 'Boîte de Don', 'Decorative donation box', 'Boîte de collecte décorative', 8.00, 'image8.jpeg', 40, 3, 1);

-- Données Boutique - Zones de livraison
INSERT INTO `delivery_zones` (`name`, `regions`, `base_cost`, `free_shipping_threshold`, `estimated_days`, `is_active`) VALUES
('Grand Tunis', 'Tunis, Ariana, Ben Arous, Manouba, La Marsa, Carthage', 7.00, 100.00, 3, 1),
('Nord', 'Bizerte, Béja, Jendouba, Le Kef, Siliana', 10.00, 100.00, 5, 1),
('Sahel', 'Sousse, Monastir, Mahdia, Sfax', 9.00, 100.00, 4, 1),
('Centre', 'Kairouan, Kasserine, Sidi Bouzid, Gafsa', 12.00, 100.00, 6, 1),
('Sud', 'Gabès, Médenine, Tataouine, Tozeur, Kébili', 15.00, 100.00, 7, 1),
('Cap Bon', 'Nabeul, Hammamet, Kelibia, Korba', 8.00, 100.00, 4, 1);

-- Données Boutique - Clients
INSERT INTO `customers` (`first_name`, `last_name`, `email`, `phone`, `address`, `city`, `postal_code`, `country`) VALUES
('Marie', 'Dupont', 'marie.dupont@email.com', '+216 71 000 002', '15 Rue de la Paix', 'Tunis', '1000', 'Tunisia'),
('Ahmed', 'Ben Ali', 'ahmed.benali@email.com', '+216 71 000 003', '22 Avenue Habib Bourguiba', 'Sousse', '4000', 'Tunisia'),
('Sophie', 'Martin', 'sophie.martin@email.com', '+216 71 000 004', '8 Rue des Oliviers', 'Sfax', '3000', 'Tunisia'),
('Karim', 'Trabelsi', 'karim.trabelsi@email.com', '+216 71 000 005', '45 Boulevard de l''Environnement', 'Nabeul', '8000', 'Tunisia');

-- Données Boutique - Commandes
INSERT INTO `orders` (`customer_id`, `total_amount`, `status`, `payment_method`, `shipping_address`) VALUES
(1, 70.00, 'completed', 'card', '15 Rue de la Paix, 1000 Tunis'),
(2, 45.00, 'processing', 'paypal', '22 Avenue Habib Bourguiba, 4000 Sousse'),
(3, 37.00, 'pending', 'card', '8 Rue des Oliviers, 3000 Sfax');

-- Données Boutique - Articles de commande
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 2, 25.00, 50.00),
(1, 4, 1, 12.00, 12.00),
(1, 8, 1, 8.00, 8.00),
(2, 2, 1, 45.00, 45.00),
(3, 1, 1, 25.00, 25.00),
(3, 4, 1, 12.00, 12.00);

-- Données Boutique - Livraisons
INSERT INTO `shippings` (`order_id`, `tracking_number`, `carrier`, `address`, `city`, `postal_code`, `country`, `shipping_cost`, `estimated_delivery`, `status`) VALUES
(1, 'IMP-ABC123-XY01', 'ImpactShop Express', '15 Rue de la Paix', 'Tunis', '1000', 'Tunisia', 7.00, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'delivered'),
(2, 'IMP-DEF456-ZW02', 'ImpactShop Express', '22 Avenue Habib Bourguiba', 'Sousse', '4000', 'Tunisia', 9.00, DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'shipped');

-- Données Boutique - Paiements
INSERT INTO `payments` (`order_id`, `amount`, `payment_method`, `method`, `transaction_id`, `status`, `paid_at`) VALUES
(1, 77.00, 'card', 'card', 'TXN-2024-001', 'completed', NOW()),
(2, 54.00, 'paypal', 'paypal', 'TXN-2024-002', 'completed', NOW());

-- Données Boutique - Avis
INSERT INTO `reviews` (`product_id`, `customer_id`, `rating`, `title`, `comment`, `is_approved`, `is_verified`) VALUES
(1, 1, 5, 'Excellent t-shirt!', 'Très confortable et le design est magnifique. Je recommande!', 1, 1),
(1, 2, 4, 'Bon produit', 'Bonne qualité, livraison rapide.', 1, 1),
(2, 3, 5, 'Sweat parfait', 'Chaud et confortable, parfait pour l''hiver.', 1, 1),
(4, 1, 5, 'Bracelet magnifique', 'Fait main avec soin, très joli.', 1, 1);


-- ============================================
-- ============================================
--          SECTION 7: PROGRAMME DE FIDÉLITÉ
-- ============================================
-- ============================================

-- 7.1 TABLE POINTS DE FIDÉLITÉ
DROP TABLE IF EXISTS `loyalty_points`;
CREATE TABLE `loyalty_points` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `points` INT NOT NULL,
    `type` ENUM('earned', 'redeemed', 'bonus', 'expired') NOT NULL,
    `order_id` INT NULL,
    `description` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_order` (`order_id`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.2 TABLE RÉCOMPENSES FIDÉLITÉ
DROP TABLE IF EXISTS `loyalty_rewards`;
CREATE TABLE `loyalty_rewards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `points_required` INT NOT NULL,
    `reward_type` ENUM('discount_percent', 'discount_fixed', 'free_shipping', 'free_product', 'bonus_points', 'exclusive_access') DEFAULT 'discount_percent',
    `value` DECIMAL(10, 2) DEFAULT 0,
    `icon` VARCHAR(50) DEFAULT 'fa-gift',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_active` (`is_active`),
    INDEX `idx_points` (`points_required`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.3 TABLE ÉCHANGES DE RÉCOMPENSES
DROP TABLE IF EXISTS `loyalty_redemptions`;
CREATE TABLE `loyalty_redemptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `reward_id` INT NOT NULL,
    `points_used` INT NOT NULL,
    `code` VARCHAR(50),
    `status` ENUM('active', 'used', 'expired') DEFAULT 'active',
    `order_id` INT NULL,
    `used_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_reward` (`reward_id`),
    INDEX `idx_code` (`code`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reward_id`) REFERENCES `loyalty_rewards`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données Fidélité - Points
INSERT INTO `loyalty_points` (`customer_id`, `points`, `type`, `order_id`, `description`) VALUES
(1, 70, 'earned', 1, 'Points gagnés pour la commande #000001'),
(2, 45, 'earned', 2, 'Points gagnés pour la commande #000002'),
(1, 50, 'bonus', NULL, 'Bonus de bienvenue'),
(3, 37, 'earned', 3, 'Points gagnés pour la commande #000003');

-- Données Fidélité - Récompenses
INSERT INTO `loyalty_rewards` (`name`, `description`, `points_required`, `reward_type`, `value`, `icon`, `is_active`) VALUES
('5% de réduction', 'Obtenez 5% de réduction sur votre prochaine commande', 100, 'discount_percent', 5.00, 'fa-percent', 1),
('10% de réduction', 'Obtenez 10% de réduction sur votre prochaine commande', 250, 'discount_percent', 10.00, 'fa-percent', 1),
('Livraison gratuite', 'Livraison gratuite sur votre prochaine commande', 150, 'free_shipping', 0.00, 'fa-truck', 1),
('5 TND de réduction', 'Obtenez 5 TND de réduction immédiate', 200, 'discount_fixed', 5.00, 'fa-money-bill', 1),
('10 TND de réduction', 'Obtenez 10 TND de réduction immédiate', 400, 'discount_fixed', 10.00, 'fa-money-bill', 1),
('Bracelet offert', 'Recevez un bracelet charité gratuit', 500, 'free_product', 12.00, 'fa-gift', 1),
('15% de réduction VIP', 'Réduction exclusive pour membres VIP', 750, 'discount_percent', 15.00, 'fa-crown', 1),
('Accès exclusif', 'Accès aux ventes privées et nouveautés', 1000, 'exclusive_access', 0.00, 'fa-star', 1);

-- ============================================
-- ============================================
--          SECTION 8: ANALYTICS & METRICS
-- ============================================
-- ============================================

-- 8.1 TABLE MÉTRIQUES D'ENGAGEMENT
DROP TABLE IF EXISTS `engagement_metrics`;
CREATE TABLE `engagement_metrics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `metric_date` DATE DEFAULT NULL,
    `total_visits` INT DEFAULT 0,
    `engaged_visits` INT DEFAULT 0,
    `likes_count` INT DEFAULT 0,
    `comments_count` INT DEFAULT 0,
    `shares_count` INT DEFAULT 0,
    INDEX `idx_metric_date` (`metric_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8.2 TABLE VISITES
DROP TABLE IF EXISTS `visits`;
CREATE TABLE `visits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `page_url` VARCHAR(255) DEFAULT NULL,
    `visit_date` DATE DEFAULT NULL,
    `visit_time` TIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_visit_date` (`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8.3 TABLE BADGES UTILISATEURS
DROP TABLE IF EXISTS `user_badges`;
CREATE TABLE `user_badges` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_name` VARCHAR(100) NOT NULL,
    `badge_type` VARCHAR(50) DEFAULT 'super_fan',
    `earned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_active` TINYINT(1) DEFAULT 1,
    UNIQUE KEY `unique_user_badge` (`user_name`, `badge_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ============================================
--          SECTION 9: CONTACT & SUPPORT
-- ============================================
-- ============================================

-- 9.1 TABLE MESSAGES DE CONTACT
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255),
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données Contact
INSERT INTO `contact_messages` (`name`, `email`, `subject`, `message`, `status`) VALUES
('Jean Dupont', 'jean.dupont@email.com', 'Question sur les donations', 'Bonjour, je voudrais savoir comment faire un don mensuel?', 'read'),
('Marie Martin', 'marie.martin@email.com', 'Problème de livraison', 'Ma commande n''est pas encore arrivée après 10 jours.', 'replied'),
('Ahmed Salah', 'ahmed.salah@email.com', 'Partenariat ONG', 'Notre ONG souhaite devenir partenaire.', 'new');

-- ============================================
-- ============================================
--          SECTION 10: SESSIONS & SÉCURITÉ
-- ============================================
-- ============================================

-- 10.1 TABLE SESSIONS UTILISATEURS
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `session_token` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_token` (`session_token`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10.2 TABLE TOKENS DE RÉINITIALISATION
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `used` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10.3 TABLE LOGS D'ACTIVITÉ
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50),
    `entity_id` INT,
    `details` JSON,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FIN DU SCRIPT
-- ============================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================
-- INFORMATIONS DE CONNEXION
-- ============================================
-- 
-- ADMIN:
--   Email: admin@aidforpeace.com
--   Password: password
--
-- UTILISATEUR TEST:
--   Email: marie.dupont@email.com
--   Password: password
--
-- ============================================
-- RÉSUMÉ DES TABLES (24 tables)
-- ============================================
-- 
-- UTILISATEURS:
--   - user (table principale)
--   - utilisateurs (legacy pour messagerie)
--
-- MESSAGERIE:
--   - conversations
--   - messages
--
-- TÉMOIGNAGES:
--   - testimonials
--   - comments
--
-- MAP MONDIALE:
--   - countries
--   - ngos
--
-- DONATIONS:
--   - donation_campaigns
--   - donations
--
-- BOUTIQUE:
--   - categories
--   - products
--   - customers
--   - orders
--   - order_items
--   - order_status_history
--   - payments
--   - shippings
--   - reviews
--   - delivery_zones
--
-- FIDÉLITÉ:
--   - loyalty_points
--   - loyalty_rewards
--   - loyalty_redemptions
--
-- ANALYTICS:
--   - engagement_metrics
--   - visits
--   - user_badges
--
-- CONTACT:
--   - contact_messages
--
-- SÉCURITÉ:
--   - user_sessions
--   - password_resets
--   - activity_logs
--
-- ============================================
-- COLONNES IMPORTANTES CORRIGÉES
-- ============================================
-- 
-- order_items: subtotal (pas total_price)
-- shippings: shipping_cost, estimated_delivery
-- payments: method, paid_at
-- messages: is_deleted, sujet
-- conversations: is_active, subject
-- donations: first_name, last_name, newsletter
-- orders: status inclut 'paid', 'processing', 'completed'
--
-- ============================================
-- INSTRUCTIONS D'INSTALLATION
-- ============================================
-- 
-- 1. Créer la base de données:
--    CREATE DATABASE aidforpeace_db;
--
-- 2. Importer ce fichier:
--    mysql -u root -p aidforpeace_db < aidforpeace_database_complete.sql
--
-- 3. Ou via phpMyAdmin:
--    - Sélectionner la base aidforpeace_db
--    - Onglet "Importer"
--    - Choisir ce fichier
--    - Cliquer "Exécuter"
--
-- ============================================
