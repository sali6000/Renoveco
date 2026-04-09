-- 03_data.sql

USE monsite_db;

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
(7, 1, 'Connectivité', 'select', NULL, 0, NULL, 2),
(8, 1, 'Couleur', 'select', NULL, 1, NULL, 3),
(9, 1, 'Rouge', 'select', NULL, 0, 8, 3),
(10, 1, 'Bleu', 'select', NULL, 0, 8, 3),
(11, 1, 'Noir', 'select', NULL, 0, 8, 3);

--
-- Déchargement des données de la table `attribute_groups`
--

INSERT INTO `attribute_groups` (`id`, `domain_id`, `name`, `display_order`) VALUES
(1, 1, 'Dimensions', 1),
(2, 1, 'Capacités électricique', 2),
(3, 1, 'Apparence', 3);

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, NULL, 'Chassis de fenêtres', 'chassis-de-fenetres', 'Chassis de fenêtre de toutes dimensions et de plusieurs matériaux différents.'),
(2, NULL, 'Portes coulissantes', 'portes-coulissantes', 'DiffÃ©rentes portes coulissantes de diffÃ©rents diamÃ¨tres'),
(3, NULL, 'Devantures en verre', 'devantures-en-verre', 'Devantures en verre permettant de laisser entrer la lumiÃ¨re'),
(65, NULL, 'scdsdc', 'sdcsdc', ''),
(71, 2, 'sdcsdc', 'sdcccccxxxx', 'wcwxc'),
(72, 71, 'wxcwxc', 'cxwc', 'xcwcx'),
(74, 72, 'cxcx', 'wxcwxc', 'wxcc'),
(77, NULL, 'cxcxcxxxx', 'www', ''),
(78, NULL, 'zedde', 'zedzedd', ''),
(81, NULL, 'sdc', 'cc', ''),
(82, NULL, 'xxwqqqqqq', 'wwwwwwwww', 'x'),
(83, 82, 'qsxsxsx', 'qsx', ''),
(84, NULL, 'scsc', 'scsss', ''),
(85, NULL, 'sd', 'sdc', ''),
(86, NULL, 'csdc', 'dcsdc', ''),
(87, NULL, 'sdccccc', 'cccc', ''),
(88, 1, 'cdcdcfddsf', 'sssteven', ''),
(89, NULL, 'coucou', 'gfgf', ''),
(90, NULL, 'dfgdfgdfg', 'dfgddddd', ''),
(91, NULL, 'test', 'csd', ''),
(92, NULL, 'f', 'f', ''),
(94, NULL, 'f', 'ff', '');

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

INSERT INTO `products` (`id`, `reference`, `slug`, `name`, `description`, `composition`, `use_for`, `is_active`, `default_supplier_id`, `created_at`, `updated_at`) VALUES
(1, '105426586987', 'chassis-de-fenetres-pe50-105426586987', 'PE50', 'FenÃªtre en PVC de X diamÃ¨tres.', 'ComposÃ© de matÃ©riaux de haute qualitÃ© fabriquÃ©s en pologne.', 'Pour la protection thermique et la chaleur en hiver. ', 1, NULL, '2025-08-10 12:45:34', '2025-08-10 12:45:34'),
(2, '123456789987', 'chassis-de-fenetres-pe50-door-123456789987', 'PE50 DOOR\r\n', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:48:51', '2025-08-10 12:48:51'),
(3, 'PE50 DOOR CROSS', 'PE50-door-cross', 'PE50 DOOR CROSS', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:48:51', '2025-08-10 12:48:51'),
(4, 'PE78 ', 'PE78', 'PE78 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:21', '2025-08-10 12:49:21'),
(5, 'SL600 ', 'SL600', 'SL600 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:21', '2025-08-10 12:49:21'),
(6, 'SL1600 TT', 'SL1600-TT', 'SL1600 TT', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:42', '2025-08-10 12:49:42'),
(7, 'PF152 WG', 'PF152-WG', 'PF152 WG', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:42', '2025-08-10 12:49:42'),
(8, 'PF152 ', 'PF152-bidirection', 'PF152 ', NULL, NULL, NULL, 1, NULL, '2025-08-10 12:49:57', '2025-08-10 12:49:57');

--
-- Déchargement des données de la table `product_domains`
--

INSERT INTO `product_domains` (`id`, `name`, `description`) VALUES
(1, 'Construction', 'Construction et rénovation en tous genre: maisons, chassis, ...');

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
(1, 1),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(14, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(26, 3),
(28, 3);

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `created_at`, `last_login_at`, `email_verified_at`, `deleted_at`, `is_active`) VALUES
(1, 'superadmin@monsite.com', '$2y$12$9qctiMw8rcm5QDwgYBaAe.IwoPULOOMr2C5IpcHfDTG0A2ePVnkz6', '2025-09-28 20:07:16', '2025-12-09 22:52:47', NULL, NULL, 1),
(6, 'stevenfer@alors.com', '$2y$12$4v5bI9efaQ9SilydyKay5ulwYeYrQgxfzkQ1gGnnwXu/5kVbwjyym', '2025-11-15 23:49:09', NULL, NULL, NULL, 1),
(7, 'alors@moi.com', '$2y$12$EHvSY99KDL.nKSXBrbq61uGdIeGpEFVVwjV.sSZWmqpqPGP0elJDm', '2025-11-16 00:01:36', NULL, NULL, NULL, 1),
(8, 'qsdqsd@xcv.com', '$2y$12$Z7g1vS.jGQtPx3/c7WAiJODtJR1PL7dwcuer9ITfmESdDtCDpnObG', '2025-11-16 00:02:12', NULL, NULL, NULL, 1),
(9, 'michtodu@gmail.com', '$2y$12$PD.EZ5C4qidqasHCuKx0AuJ1/Da2N2kgc.iT3mQOXBWaNzYQ5PKmq', '2025-11-16 14:31:06', NULL, NULL, NULL, 1),
(10, 'steven.ferlazzo1@gmail.com', '$2y$12$lGi0FSl97c82tpYzocr08.nugi7XrQOqngaOY25/PObOxBrCHfThS', '2025-11-16 14:31:40', NULL, NULL, NULL, 1),
(14, 'steven.fernando@gmail.com', '$2y$12$ZNJ5a8HmVsVi2Ah6VHSgoODmK49o4Mv9O3Hs3mPIPZ56NK9TpmFwC', '2025-11-16 16:39:00', NULL, NULL, NULL, 1),
(16, 'user@monsite.com', '$2y$12$2Tf5AIxnRm8cVDaHKUSzzOUbn1KSV2Hy9e4U67PAxpI6xREuPgNbK', '2025-11-17 01:22:22', '2025-11-27 18:10:40', NULL, NULL, 1),
(17, 'bdbdfbd@bdfbdfff.com', '$2y$12$QmCkHr5uVyPPGDIeECO4GOqx5NleR4DtEPyisr56rc6CyswiH5UKO', '2025-11-17 20:31:57', NULL, NULL, NULL, 1),
(18, 'steferf@gmail.com', '$2y$12$t6K2nTobkUfFX2xsRxFqDeMDaCzOm8F9vAtSsreO8ApJarzHMWljy', '2025-11-18 14:08:40', NULL, NULL, NULL, 1),
(19, 'c@d.com', '$2y$12$5XDLM5VICM831whtJmC09.zzO2X7CE20MW1BETAcPAVy3jkQjJIKi', '2025-11-19 19:40:16', NULL, NULL, NULL, 1),
(20, 'sdf@fsdf.com', '$2y$12$pPTyolWWVYEKzBOXUBjLXuMHK9ZIZOpze/oKMekO8n9Fxo3D.fOva', '2025-11-19 19:46:20', NULL, NULL, NULL, 1),
(21, 'sdcsdc@csdcdc.com', '$2y$12$V3AVexCZNraftgUf7975z.RK6DDEkF6syyffPe/aJoRZ9oZTQYc6e', '2025-11-19 19:58:06', NULL, NULL, NULL, 1),
(22, 'sferfrf@gmaie.com', '$2y$12$vmHnUqPO8r471XXrqSoLjuyvfpjlshYygN0dVvEx4IJ8vWpRAloWa', '2025-11-19 20:01:40', NULL, NULL, NULL, 1),
(23, 'sdf@gmail.com', '$2y$12$tNqLft7q7gzup8F/rhfRuuaZsS6fy6g7O1KmnmbQbIV8ja3Edb2R.', '2025-11-19 20:02:21', NULL, NULL, NULL, 1),
(24, 'blabla@gmail.com', '$2y$12$gLnH89b2VMk4D/A68TdrOea/f3S0Zqr/CrdB.r5oi/K2bkUPnNVX.', '2025-11-19 20:05:35', NULL, NULL, NULL, 1),
(26, 'azd78@gmail.com', '$2y$12$NhYqZ5jOylopYkGLxkuwsuanGJyX/xvS592A15UPr73cSx7zv3Slq', '2025-11-19 20:37:41', NULL, NULL, NULL, 1),
(28, 'sdccs@gdfg.com', '$2y$12$m5QZkpVoJ9ZN0wdISRV8P.wd005WfrTEL1gnZPV3gJIb9zgMFAf8i', '2025-11-20 01:47:47', NULL, NULL, NULL, 1);
COMMIT;