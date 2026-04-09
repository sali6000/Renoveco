-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 17 juil. 2025 à 21:30
-- Version du serveur : 8.2.0
-- Version de PHP : 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Création du compte superadmin sur PhpMyAdmin (Tous les droits seulement sur les DB mentionnées)
CREATE USER IF NOT EXISTS 'superadmin_erp'@'%' IDENTIFIED BY 'LE,;Diplomate2190.'; -- ...56. (root)
GRANT ALL PRIVILEGES ON ma_base.* TO 'superadmin_erp'@'%';
GRANT ALL PRIVILEGES ON monsite_db.* TO 'superadmin_erp'@'%';
FLUSH PRIVILEGES;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;