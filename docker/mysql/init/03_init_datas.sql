-- Sélection de la base de données cible : requis car MySQL ne retient pas le contexte entre les fichiers d'initialisation
USE monsite_db;

-- Désactivation temporaire des contraintes de clés étrangères : permet l'insertion des données indépendamment de l'ordre des tables
SET FOREIGN_KEY_CHECKS=0;

-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : sam. 30 mai 2026 à 10:07
-- Version du serveur : 9.6.0
-- Version de PHP : 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `monsite_db`
--

--
-- Déchargement des données de la table `attributes`
--

INSERT INTO `attributes` (`id`, `domain_id`, `name`, `type`, `unit`, `is_required`, `parent_attribute_id`, `attribute_group_id`) VALUES
(1, 1, 'Poids', 'number', 'kg', 0, NULL, 1),
(2, 1, 'Largeur', 'number', 'cm', 0, NULL, 1),
(3, 1, 'Hauteur', 'number', 'cm', 0, NULL, 1),
(4, 1, 'Profondeur', 'number', 'cm', 0, NULL, 1),
(5, 1, 'Puissance', 'number', 'W', 1, NULL, 2),
(6, 1, 'Tension', 'number', 'V', 0, NULL, 2),
(7, 1, 'ConnectivitÃ©', 'select', NULL, 0, NULL, 2),
(8, 1, 'Couleur', 'select', NULL, 1, NULL, 3),
(9, 1, 'Rouge', 'select', NULL, 0, 8, 3),
(10, 1, 'Bleu', 'select', NULL, 0, 8, 3),
(11, 1, 'Noir', 'select', NULL, 0, 8, 3);

--
-- Déchargement des données de la table `attribute_groups`
--

INSERT INTO `attribute_groups` (`id`, `domain_id`, `name`, `display_order`) VALUES
(1, 1, 'Dimensions', 1),
(2, 1, 'CapacitÃ©s Ã©lectricique', 2),
(3, 1, 'Apparence', 3);

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, NULL, 'Chassis de fenêtres', 'chassis-de-fenetres', 'Chassis de fenêtre de toutes dimensions et de plusieurs matériaux différents.'),
(2, NULL, 'Portes coulissantes', 'portes-coulissantes', 'Différentes portes coulissantes de différents diamètres'),
(3, NULL, 'Devantures en verre', 'devantures-en-verre', 'Devantures en verre permettant de laisser entrer la lumiÃƒÂ¨re');

--
-- Déchargement des données de la table `category_product`
--

INSERT INTO `category_product` (`product_id`, `category_id`) VALUES
(1, 1),
(2, 1),
(7, 1),
(3, 2),
(5, 2),
(4, 3),
(6, 3),
(8, 3);

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `reference`, `slug`, `name`, `description`, `composition`, `use_for`, `is_active`, `default_supplier_id`, `created_at`, `updated_at`, `subtitle`, `meta_description`, `features`) VALUES
(1, '105426586987', 'chassis-de-fenetres-pe50-105426586987', 'PE50', 'Fenêtre en PVC de X diamêtres.', 'Composé de matériaux de haute qualité fabriqués en pologne.', 'Pour la protection thermique et la chaleur en hiver. ', 1, NULL, '2025-08-10 12:45:34', '2026-05-19 21:37:13', NULL, NULL, '["Triple chambre", "Grandes dimensions jusqu''à H 3500mm", "Isolation thermique renforcée"]'),
(2, '123456789987', 'chassis-de-fenetres-pe50-door-123456789987', 'PE50 DOOR\r\n', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:48:51', '2025-08-10 12:48:51', NULL, NULL, NULL),
(3, 'PE50 DOOR CROSS', 'PE50-door-cross', 'PE50 DOOR CROSS', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:48:51', '2025-08-10 12:48:51', NULL, NULL, NULL),
(4, 'PE78 ', 'PE78', 'PE78 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:21', '2025-08-10 12:49:21', NULL, NULL, NULL),
(5, 'SL600 ', 'SL600', 'SL600 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:21', '2025-08-10 12:49:21', NULL, NULL, NULL),
(6, 'SL1600 TT', 'SL1600-TT', 'SL1600 TT', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:42', '2025-08-10 12:49:42', NULL, NULL, NULL),
(7, 'PF152 WG', 'PF152-WG', 'PF152 WG', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:42', '2025-08-10 12:49:42', NULL, NULL, NULL),
(8, 'PF152 ', 'PF152-bidirection', 'PF152 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:57', '2025-08-10 12:49:57', NULL, NULL, NULL);

--
-- Déchargement des données de la table `product_attribute`
--

INSERT INTO `product_attribute` (`id`, `product_id`, `attribute_id`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '22', '2026-05-20 10:41:32', '2026-05-20 10:41:32');

--
-- Déchargement des données de la table `product_domains`
--

INSERT INTO `product_domains` (`id`, `name`, `description`) VALUES
(1, 'Construction', 'Construction et rÃ©novation en tous genre: maisons, chassis, ...');

--
-- Déchargement des données de la table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `file_path`, `is_main`, `alt_text`, `display_order`, `uploaded_at`) VALUES
(1, 4, 'pe78fold-20251008-145927-68e67c4f686d7.webp', 1, NULL, 0, '2025-08-10 12:53:06'),
(2, 6, 'sl600ttevo-20251008-150030-68e67c8e6f45c.webp', 1, NULL, 0, '2025-08-10 12:53:06'),
(3, 5, 'sl1600tthi-20251008-150108-68e67cb434aa2.webp', 1, NULL, 0, '2025-08-10 12:55:17'),
(4, 7, 'pf152wg-20251008-150215-68e67cf7e5401.webp', 1, NULL, 0, '2025-08-10 12:55:17'),
(5, 8, 'pf152hi-20251008-151046-68e67ef6acae8.webp', 1, NULL, 0, '2025-08-10 12:55:17'),
(6, 1, 'procural-pe50-20251008-144025-68e677d923203.webp', 1, NULL, 0, '2025-08-10 12:55:17'),
(7, 2, 'pe50-20251008-145547-68e67b73bb455.webp', 1, NULL, 0, '2025-08-10 12:55:17'),
(8, 3, 'pe50-20251008-145723-68e67bd3ba4d4.webp', 1, NULL, 0, '2025-08-10 12:55:17');

--
-- Déchargement des données de la table `product_inventory`
--

INSERT INTO `product_inventory` (`id`, `product_id`, `stock_quantity`, `stock_minimum`, `stock_maximum`, `last_stock_update`) VALUES
(1, 1, 4, 2, 12, '2025-12-06 03:11:55');

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `is_active`) VALUES
(1, 'superadmin', 1),
(2, 'admin', 1),
(3, 'user', 1),
(4, 'guest', 1);

--
-- Déchargement des données de la table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1);
--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `created_at`, `last_login_at`, `email_verified_at`, `deleted_at`, `is_active`) VALUES
(1, 'superadmin@demo.com', '$2y$12$5aB.GjTN66V2lfE0PvCjR.BNBVXowGrixn1a6sprzCt3boezkB4Am', '2025-09-28 20:07:16', '2026-05-29 12:25:33', NULL, NULL, 1);

-- Réactivation des contraintes de clés étrangères : permet l'insertion des données dépendamment de l'ordre des tables
SET FOREIGN_KEY_CHECKS=1;
