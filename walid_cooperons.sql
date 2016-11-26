-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 10 Mars 2016 à 18:01
-- Version du serveur: 5.5.44-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `walid_cooperons`
--

-- --------------------------------------------------------

--
-- Structure de la table `account_points`
--

CREATE TABLE IF NOT EXISTS `account_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `participates_to_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `is_multi` tinyint(1) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `settlement_id` int(11) DEFAULT NULL,
  `affiliate_history_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F7A57EE93EB8070A` (`program_id`),
  KEY `IDX_F7A57EE92989F1FD` (`invoice_id`),
  KEY `IDX_F7A57EE98993B2CA` (`participates_to_id`),
  KEY `IDX_8E405AC2B9C425` (`settlement_id`),
  KEY `IDX_8E405A2F8ACCAF` (`affiliate_history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Contenu de la table `account_points`
--

INSERT INTO `account_points` (`id`, `program_id`, `participates_to_id`, `invoice_id`, `points`, `is_multi`, `description`, `created_date`, `discr`, `settlement_id`, `affiliate_history_id`, `type`, `status`) VALUES
(1, 1, 120, NULL, 500, 1, 'Premier Programme (Prospect Test) : 500 MultiPoints Member Test', '2014-01-01 11:21:35', 'prod', NULL, NULL, 'Premier Programme', 'confirmed'),
(4, 5, 127, NULL, 3000, 0, 'Bonus Client : 3000 Points APIMember2 Test', '2014-01-01 16:59:42', 'prod', 2, NULL, 'Bonus Client', 'confirmed'),
(5, 5, 127, NULL, 1000, 1, 'Apport d''un Prospect : 1000 MultiPoints APIMember2 Test', '2014-01-01 17:48:44', 'prod', 3, NULL, 'Apport d''un Prospect', 'confirmed'),
(6, 5, 128, NULL, 666, 1, '666 MultiPoints Filleul (APIMember2 Test)', '2014-01-01 17:48:44', 'prod', 3, 5, '__multi', 'confirmed'),
(7, 5, 128, NULL, 2000, 0, 'Bonus Client : 2000 Points APIMember1 Test', '2014-01-01 18:35:34', 'prod', 4, NULL, 'Bonus Client', 'confirmed'),
(9, 4, 146, NULL, 1, 1, 'Commission 1% (prestation Mohamed BEJI 20,00 €) : 1 MultiPoints Samir GUELBI', '2016-02-03 10:39:56', 'prod', NULL, NULL, 'Commission 1%', 'confirmed'),
(16, 4, 141, NULL, 1, 1, 'Commission 1% (prestation Member Test 30,00 €) : 1 MultiPoints Ahmed LAABIDI', '2016-11-25 16:51:06', 'prod', NULL, NULL, 'Commission 1%', 'confirmed'),
(19, 10, 148, NULL, 150, 0, 'Commission Simple (test) : 150 Points salah salah', '2014-01-01 16:26:53', 'prod', 71, NULL, 'Commission Simple', 'confirmed'),
(20, 10, 148, NULL, 150, 1, 'Commission Multi-Niveau (test) : 150 MultiPoints salah salah', '2014-01-01 16:26:53', 'prod', 72, NULL, 'Commission Multi-Niveau', 'confirmed');

-- --------------------------------------------------------

--
-- Structure de la table `affairs`
--

CREATE TABLE IF NOT EXISTS `affairs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `participates_to_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `cancellation_date` datetime DEFAULT NULL,
  `closing_date` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `cancel_msg` longtext COLLATE utf8_unicode_ci,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `simple_rate` int(11) NOT NULL,
  `multi_rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_68EBADBE3EB8070A` (`program_id`),
  KEY `IDX_68EBADBE8993B2CA` (`participates_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Contenu de la table `affairs`
--

INSERT INTO `affairs` (`id`, `participates_to_id`, `program_id`, `label`, `amount`, `created_date`, `cancellation_date`, `closing_date`, `finish_date`, `cancel_msg`, `discr`, `simple_rate`, `multi_rate`) VALUES
(4, 148, 10, 'int01', 8888878855, '2016-02-08 09:57:21', NULL, '2016-02-08 09:57:37', '2016-02-08 09:57:39', NULL, 'prod', 3, 3),
(5, 137, 7, 'test', NULL, '2016-02-23 14:50:26', '2016-02-23 14:50:33', NULL, NULL, 'aaaaaaaaaa', 'prod', 12, 9),
(6, 137, 7, 'test 2', 0, '2016-11-25 14:51:41', '2016-11-25 14:53:53', NULL, NULL, 'aaaaaaaaaaaa', 'prod', 12, 9),
(7, 137, 7, 'test3', 55566699, '2015-04-01 21:30:27', NULL, '2015-04-01 21:30:42', '2015-04-01 21:30:44', NULL, 'prod', 12, 9),
(8, 148, 10, 'test', 1000, '2014-01-01 16:26:33', NULL, '2014-01-01 16:26:51', '2014-01-01 16:26:53', NULL, 'prod', 3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `attestations`
--

CREATE TABLE IF NOT EXISTS `attestations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `corporate_id` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `quarter` int(11) NOT NULL,
  `total_avantage` double NOT NULL,
  `smic` double NOT NULL,
  `cotisation` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AD5A8CF07597D3FE` (`member_id`),
  KEY `IDX_AD5A8CF0CD147EEF` (`corporate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `attestations`
--

INSERT INTO `attestations` (`id`, `member_id`, `created_date`, `corporate_id`, `year`, `quarter`, `total_avantage`, `smic`, `cotisation`) VALUES
(1, 23, '2015-01-01 12:27:49', 4, 2014, 3, 660, 1457.52, 102.85),
(2, 24, '2015-01-01 12:27:49', 4, 2014, 4, 140, 1457.52, 0),
(3, 23, '2015-01-01 12:42:10', 4, 2014, 3, 660, 1457.52, 102.85),
(4, 24, '2015-01-01 12:42:10', 4, 2014, 4, 140, 1457.52, 0);

-- --------------------------------------------------------

--
-- Structure de la table `auto_entrepreneurs`
--

CREATE TABLE IF NOT EXISTS `auto_entrepreneurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `IBAN` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BIC` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `SIRET` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `party_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `cancel_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C8B162C37597D3FE` (`member_id`),
  UNIQUE KEY `UNIQ_C8B162C3213C1059` (`party_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Contenu de la table `auto_entrepreneurs`
--

INSERT INTO `auto_entrepreneurs` (`id`, `member_id`, `IBAN`, `BIC`, `SIRET`, `external_email`, `party_id`, `created_date`, `cancel_date`) VALUES
(1, 18, '132456789456', '123456789987', '123456777', 'member.test@cooperons.com', 9, '2016-02-02 16:18:12', NULL),
(2, 27, '132456789456', '123456789987', '123456777888', 'mohamed.beji@cooperons.com', 10, '2016-02-02 16:22:51', NULL),
(3, 28, '13245678945689', '123456987', '123456', 'bilel.bijaoui@cooperons.com', 11, '2016-02-02 16:49:01', NULL),
(4, 29, '1324567894568944', '123456789987896', '123456789987', 'ahmed.laabidi@cooperons.com', 12, '2016-02-03 07:25:40', NULL),
(5, 35, '13245678945689', '123456789987', '111222333444', 'commercial.commercial@cooperons.com', 14, '2016-02-09 07:55:42', NULL),
(6, 37, '1324567894568998', '12345678998798', '987654321', 'bilel.nomen@cooperons.com', 16, '2016-02-12 13:45:22', NULL),
(7, 32, '1324567894568932', '12345678998745', '111222333444999', 'samir.guelbi@cooperons.com', 17, '2016-02-16 14:26:51', NULL),
(8, 23, '77777777777777777', '777777888888888888', '8888888888888888888', 'apimember2.test@cooperons.com', 19, '2016-11-25 10:22:54', NULL),
(9, 30, '4445', '55555555', '555555555', 'imen.khazri@cooperons.com', 21, '2016-02-26 20:17:06', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `avantages`
--

CREATE TABLE IF NOT EXISTS `avantages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `corporate_id` int(11) DEFAULT NULL,
  `attestation_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `amount` double NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cumulated_total` double NOT NULL,
  `cumulated_year` double NOT NULL,
  `gift_order_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CBC7848D7597D3FE` (`member_id`),
  KEY `IDX_CBC7848D7EDC5B38` (`attestation_id`),
  KEY `IDX_CBC7848DCB9A8972` (`gift_order_id`),
  KEY `IDX_CBC7848DCD147EEF` (`corporate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Contenu de la table `avantages`
--

INSERT INTO `avantages` (`id`, `member_id`, `corporate_id`, `attestation_id`, `created_date`, `amount`, `description`, `cumulated_total`, `cumulated_year`, `gift_order_id`, `type`) VALUES
(1, 18, NULL, NULL, '2014-04-01 11:29:53', 100, 'Chèque cadeau: Printemps 2014', 100, 100, 1, 'gift'),
(2, 23, NULL, NULL, '2014-04-01 11:29:53', 140, 'Chèque cadeau: Printemps 2014', 140, 140, 1, 'gift'),
(3, 24, NULL, NULL, '2014-04-01 11:29:53', 140, 'Chèque cadeau: Printemps 2014', 140, 140, 1, 'gift'),
(4, 23, 4, 3, '2014-07-01 12:24:17', 660, 'Chèque cadeau: Été 2014', 660, 660, 2, 'gift'),
(5, 24, 4, 4, '2014-10-01 12:26:10', 140, 'Chèque cadeau: Automne 2014', 140, 140, 3, 'gift'),
(6, 24, NULL, NULL, '2016-02-05 11:07:06', 140, 'Chèque cadeau: Hiver 2016', 280, 140, 4, 'gift'),
(7, 32, NULL, NULL, '2016-02-05 11:07:06', 0.2, 'Chèque cadeau: Hiver 2016', 0.4, 0.2, 4, 'gift');

-- --------------------------------------------------------

--
-- Structure de la table `banks`
--

CREATE TABLE IF NOT EXISTS `banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_id` int(11) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AB063796213C1059` (`party_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `commissions`
--

CREATE TABLE IF NOT EXISTS `commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affair_id` int(11) DEFAULT NULL,
  `base` double DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7EA273CC5C4FEC5C` (`affair_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Contenu de la table `commissions`
--

INSERT INTO `commissions` (`id`, `affair_id`, `base`, `created_date`, `discr`) VALUES
(3, 4, 8888878855, '2016-02-08 09:57:39', 'prod'),
(4, 7, 55566699, '2015-04-01 21:30:44', 'prod'),
(5, 8, 1000, '2014-01-01 16:26:53', 'prod');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_departement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `second_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4C62E638BB93168B` (`num_departement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

--
-- Contenu de la table `contact`
--

INSERT INTO `contact` (`id`, `num_departement`, `phone`, `address`, `second_address`, `city`, `postal_code`) VALUES
(1, '75', '0664966475', '34 rue de la Tombe Issoire\r\n75014 Paris', NULL, 'Paris', 75014),
(3, '75', '0664966475', '34 rue de la Tombe Issoire', NULL, 'Paris', 75014),
(17, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(18, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(19, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(20, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(21, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(22, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(23, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(24, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(25, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(26, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(27, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(28, '12', '0123456789', '12 rue du Test', NULL, 'Test', 12345),
(29, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(30, '12', '0123456789', 'test', NULL, 'paris', 12345),
(31, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(32, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(33, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(34, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(35, '14', '0146600924', 'rue test', NULL, 'paris', 14785),
(36, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(37, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(38, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(39, '12', '0123456789', 'rue test', NULL, 'paris', 12345),
(40, '44', '0338588541', 'rue de test', 'z', 'tunis', 44578),
(41, '22', '0338588541', '135 rue du Mont Cenis', NULL, 'Paris', 22255),
(42, '12', '0123456789', '135 rue du Mont Cenis', 'z', 'Paris', 12345);

-- --------------------------------------------------------

--
-- Structure de la table `contracts`
--

CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_collaborator_id` int(11) DEFAULT NULL,
  `client_collaborator_id` int(11) DEFAULT NULL,
  `mandataire_id` int(11) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `agree_date` datetime DEFAULT NULL,
  `suspension_date` datetime DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `is_created_by_owner` tinyint(1) NOT NULL,
  `old_contract_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_950A973A919BF39` (`old_contract_id`),
  UNIQUE KEY `UNIQ_950A97358207E03` (`mandataire_id`),
  KEY `IDX_950A973AD9F84BB` (`owner_collaborator_id`),
  KEY `IDX_950A9734D2FD17F` (`client_collaborator_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=115 ;

--
-- Contenu de la table `contracts`
--

INSERT INTO `contracts` (`id`, `owner_collaborator_id`, `client_collaborator_id`, `mandataire_id`, `publish_date`, `agree_date`, `suspension_date`, `cancel_date`, `created_date`, `is_created_by_owner`, `old_contract_id`) VALUES
(11, 1, 1, NULL, '2015-12-01 00:00:00', '2015-12-01 00:00:00', NULL, NULL, '0000-00-00 00:00:00', 0, NULL),
(12, 1, 1, NULL, '2015-12-01 00:00:00', '2015-12-01 00:00:00', NULL, NULL, '0000-00-00 00:00:00', 0, NULL),
(13, 1, 8, 7, '2014-01-01 10:22:13', '2015-04-01 14:00:14', NULL, '2015-04-01 14:01:26', '2014-01-01 10:22:13', 1, NULL),
(14, 1, 14, 8, '2016-02-02 16:14:40', '2018-06-01 14:16:10', '2018-06-01 14:16:32', NULL, '2016-02-02 16:14:40', 0, NULL),
(16, NULL, 14, 11, '2016-02-02 16:37:06', '2016-02-02 16:38:23', NULL, NULL, '2016-02-02 16:35:56', 0, NULL),
(18, NULL, 14, 15, NULL, NULL, NULL, NULL, '2016-02-02 17:05:01', 0, NULL),
(23, 1, 16, 18, '2016-02-03 14:28:25', NULL, NULL, NULL, '2016-02-03 14:28:25', 0, NULL),
(25, 1, 14, 20, '2016-02-04 18:58:02', '2018-06-01 14:17:30', NULL, NULL, '2016-02-04 18:58:02', 0, NULL),
(26, 1, 14, 21, '2016-02-04 18:59:45', '2016-02-04 19:00:24', NULL, NULL, '2016-02-04 18:59:45', 0, NULL),
(27, 1, 14, 22, '2016-02-08 07:57:28', '2018-06-01 14:32:55', NULL, NULL, '2016-02-08 07:57:28', 0, NULL),
(36, NULL, 19, 29, NULL, NULL, NULL, NULL, '2016-02-09 08:01:25', 1, NULL),
(47, NULL, NULL, 40, '2016-02-12 13:13:22', '2016-02-12 13:23:01', NULL, NULL, '2016-02-12 13:13:01', 1, NULL),
(48, NULL, 17, 41, '2016-02-12 13:24:39', '2016-02-17 16:42:21', NULL, NULL, '2016-02-12 13:15:28', 1, NULL),
(59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2016-02-16 07:47:50', 1, NULL),
(69, NULL, NULL, 57, NULL, NULL, NULL, NULL, '2016-02-16 13:47:38', 0, NULL),
(76, NULL, 14, 58, '2016-02-18 08:51:39', NULL, NULL, NULL, '2016-02-16 14:09:21', 0, NULL),
(87, NULL, 16, 63, NULL, NULL, NULL, NULL, '2016-02-17 11:05:45', 1, NULL),
(88, NULL, 16, 64, NULL, NULL, NULL, NULL, '2016-02-17 13:54:27', 1, NULL),
(90, NULL, 17, 65, '2016-02-17 15:14:52', '2016-02-17 15:22:32', NULL, NULL, '2016-02-17 15:14:26', 1, NULL),
(91, NULL, 14, NULL, NULL, NULL, NULL, NULL, '2016-02-17 15:16:39', 0, NULL),
(93, NULL, NULL, 67, '2016-02-17 17:01:04', NULL, NULL, NULL, '2016-02-17 17:00:53', 1, NULL),
(94, NULL, 21, 68, '2016-02-18 08:59:24', '2016-02-18 09:00:01', NULL, NULL, '2016-02-18 08:56:10', 0, NULL),
(95, NULL, 21, 70, NULL, NULL, NULL, NULL, '2016-02-18 13:25:03', 1, NULL),
(96, 1, 14, 71, '2016-11-25 17:42:25', '2018-06-01 14:27:59', NULL, NULL, '2016-11-25 17:42:25', 0, NULL),
(98, 1, 14, 73, '2016-11-25 12:30:56', '2018-06-01 14:32:41', NULL, NULL, '2016-11-25 12:30:55', 0, NULL),
(99, 1, 14, 74, '2015-04-01 10:28:25', '2018-06-01 14:32:34', NULL, NULL, '2015-04-01 10:28:25', 0, NULL),
(100, 1, 14, 75, '2015-04-01 10:30:54', '2015-04-01 10:31:12', NULL, '2016-02-27 19:24:37', '2015-04-01 10:30:54', 0, NULL),
(101, 1, 8, 76, '2015-04-01 10:32:43', '2018-06-01 14:32:04', '2018-06-01 14:32:11', NULL, '2015-04-01 10:32:43', 0, NULL),
(102, 1, 8, 77, '2015-04-01 10:36:12', '2018-06-01 14:31:53', NULL, NULL, '2015-04-01 10:36:12', 0, NULL),
(103, 1, 8, 78, '2015-04-01 10:41:30', '2018-06-01 14:31:44', '2018-06-01 14:31:46', NULL, '2015-04-01 10:41:30', 0, NULL),
(104, 1, 8, 79, '2015-04-01 10:43:17', '2018-06-01 14:31:10', '2018-06-01 14:31:16', NULL, '2015-04-01 10:43:17', 0, NULL),
(105, 1, 8, 80, '2015-04-01 10:55:10', '2018-06-01 14:30:59', '2018-06-01 14:31:04', NULL, '2015-04-01 10:55:10', 0, NULL),
(106, 1, 8, 81, '2015-04-01 10:59:00', '2018-06-01 14:30:45', '2018-06-01 14:30:50', NULL, '2015-04-01 10:59:00', 0, NULL),
(107, 1, 8, 82, '2015-04-01 11:04:41', '2015-04-01 11:05:01', NULL, NULL, '2015-04-01 11:04:40', 0, NULL),
(108, 1, 8, 83, '2015-04-01 11:18:03', '2018-06-01 14:28:22', NULL, NULL, '2015-04-01 11:18:03', 0, NULL),
(109, 1, 14, 85, '2016-02-27 12:34:28', NULL, NULL, NULL, '2016-02-27 12:34:28', 0, NULL),
(110, 1, 14, 86, '2014-04-01 14:19:18', '2018-06-01 14:27:58', NULL, NULL, '2014-04-01 14:19:17', 0, NULL),
(111, NULL, NULL, 87, NULL, NULL, NULL, NULL, '2016-02-27 23:22:43', 1, 47),
(112, NULL, 21, 88, NULL, NULL, NULL, NULL, '2016-02-27 23:23:10', 1, 94),
(113, 1, 14, 89, '2014-04-01 09:37:09', '2018-06-01 14:27:59', NULL, NULL, '2014-04-01 09:37:09', 0, NULL),
(114, 1, 10, 90, '2014-01-01 18:02:43', NULL, NULL, NULL, '2014-01-01 18:02:43', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `corporates`
--

CREATE TABLE IF NOT EXISTS `corporates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `raison_social` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siren` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `second_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `phone` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tva_intracomm` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `administrator_id` int(11) DEFAULT NULL,
  `delegate_id` int(11) DEFAULT NULL,
  `accord_signed` tinyint(1) NOT NULL,
  `count_colleges` int(11) NOT NULL,
  `party_id` int(11) DEFAULT NULL,
  `cgv_coopae_agreed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_23C93E184B09E92C` (`administrator_id`),
  UNIQUE KEY `UNIQ_23C93E188A0BB485` (`delegate_id`),
  UNIQUE KEY `UNIQ_23C93E18213C1059` (`party_id`),
  KEY `IDX_23C93E18F92F3E70` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Contenu de la table `corporates`
--

INSERT INTO `corporates` (`id`, `country_id`, `raison_social`, `siren`, `address`, `second_address`, `city`, `postal_code`, `created_date`, `phone`, `tva_intracomm`, `administrator_id`, `delegate_id`, `accord_signed`, `count_colleges`, `party_id`, `cgv_coopae_agreed`) VALUES
(1, 69, 'SARL Coopérons', '492222302', '34 rue de la Tombe Issoire', NULL, 'Paris', 75014, '2014-11-03 00:00:00', '0664966475', 'FR28492222302', 1, NULL, 1, 0, 1, 0),
(3, 69, 'SAS EDATIS', '429386345', '135 rue du Mont Cenis', NULL, 'Paris', 75018, '2014-01-01 09:10:18', '0123456789', NULL, 8, NULL, 1, 0, 6, 0),
(4, 69, 'MonEntreprise', '123456789', '30 rue du Test', NULL, 'Test', 12345, '2014-04-01 12:17:50', '0123456789', NULL, 10, NULL, 1, 0, 7, 0),
(5, 69, 'Fondative', '123456777', 'rue test', NULL, 'paris', 12345, '2016-02-02 16:09:00', '0123456789', '123456', 14, 20, 1, 1, 8, 1),
(6, 69, 'Ste01', '123456789123', 'rue test', NULL, 'paris', 12345, '2016-02-03 12:26:52', '0123456789', '123456', 17, NULL, 1, 0, 13, 1),
(7, 69, 'société aaa', '1114222333', 'rue test', NULL, 'paris', 12345, '2016-02-09 08:00:56', '0123456789', '123147852', 19, NULL, 1, 0, 15, 0),
(8, 69, 'ste03', '111222333444', 'rue test', NULL, 'paris', 12245, '2016-02-18 08:54:33', '0123456789', '123456ss', 21, NULL, 1, 0, 18, 1),
(9, 69, 'test', '555888555', '135 rue du Mont Cenis', 'z', 'Paris', 78885, '2014-01-01 10:42:17', '0123456789', '4444', 29, NULL, 1, 0, 20, 0),
(10, 69, 'TEst', '111444777', 'test', 'test', 'Paris', 78885, '2016-02-27 11:42:29', '0123456789', '123456', NULL, NULL, 0, 0, 22, 0);

-- --------------------------------------------------------

--
-- Structure de la table `corporate_users`
--

CREATE TABLE IF NOT EXISTS `corporate_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `corporate_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `leave_date` datetime DEFAULT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_confirm_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_45EE9DB7597D3FE` (`member_id`),
  KEY `IDX_45EE9DBCD147EEF` (`corporate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

--
-- Contenu de la table `corporate_users`
--

INSERT INTO `corporate_users` (`id`, `member_id`, `corporate_id`, `created_date`, `leave_date`, `discr`, `last_confirm_date`) VALUES
(1, 1, 1, '2015-11-01 00:00:00', NULL, 'clb', NULL),
(8, 19, 3, '2014-01-01 16:36:30', NULL, 'clb', NULL),
(10, 26, 4, '2014-04-01 12:17:50', NULL, 'clb', NULL),
(12, 23, 4, '2014-04-01 12:18:43', '2014-10-01 12:26:39', 'clg', '2014-06-01 12:23:39'),
(13, 24, 4, '2014-07-01 12:24:46', '2014-10-01 12:27:15', 'clg', '2014-09-01 12:25:38'),
(14, 18, 5, '2016-02-02 16:09:00', NULL, 'clb', NULL),
(16, 32, 5, '2016-02-03 07:45:13', NULL, 'clb', NULL),
(17, 18, 6, '2016-02-03 12:26:52', NULL, 'clb', NULL),
(19, 36, 7, '2016-02-09 08:00:56', NULL, 'clb', NULL),
(20, 38, 5, '2016-02-17 09:52:10', NULL, 'clg', '2016-02-17 15:18:13'),
(21, 29, 8, '2016-02-18 08:54:33', NULL, 'clb', NULL),
(22, 20, 5, '2016-11-25 16:57:05', NULL, 'clg', NULL),
(29, 18, 9, '2014-01-01 10:42:17', NULL, 'clb', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ue` tinyint(1) NOT NULL,
  `rate_tva` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=235 ;

--
-- Contenu de la table `country`
--

INSERT INTO `country` (`id`, `code`, `label`, `ue`, `rate_tva`) VALUES
(1, 'af', 'Afghanistan', 0, NULL),
(2, 'za', 'Afrique du Sud', 0, NULL),
(3, 'al', 'Albanie', 0, NULL),
(4, 'dz', 'Algérie', 0, NULL),
(5, 'de', 'Allemagne', 1, NULL),
(6, 'ad', 'Andorre', 0, NULL),
(7, 'ao', 'Angola', 0, NULL),
(8, 'ai', 'Anguilla', 0, NULL),
(9, 'ag', 'Antigua-et-Barbuda', 0, NULL),
(10, 'sa', 'Arabie saoudite', 0, NULL),
(11, 'ar', 'Argentine', 0, NULL),
(12, 'am', 'Arménie', 0, NULL),
(13, 'aw', 'Aruba', 0, NULL),
(14, 'au', 'Australie', 0, NULL),
(15, 'at', 'Autriche', 1, NULL),
(16, 'az', 'Azerbaïdjan', 0, NULL),
(17, 'bs', 'Bahamas', 0, NULL),
(18, 'bh', 'Bahreïn', 0, NULL),
(19, 'bd', 'Bangladesh', 0, NULL),
(20, 'bb', 'Barbade', 0, NULL),
(21, 'be', 'Belgique', 1, NULL),
(22, 'bz', 'Belize', 0, NULL),
(23, 'bj', 'Bénin', 0, NULL),
(24, 'bm', 'Bermudes', 0, NULL),
(25, 'bt', 'Bhoutan', 0, NULL),
(26, 'by', 'Biélorussie', 0, NULL),
(27, 'bo', 'Bolivie', 0, NULL),
(28, 'bq', 'Bonaire, Saint-Eustache et Saba', 0, NULL),
(29, 'ba', 'Bosnie-Herzégovine', 0, NULL),
(30, 'bw', 'Botswana', 0, NULL),
(31, 'br', 'Brésil', 0, NULL),
(32, 'bn', 'Brunei', 0, NULL),
(33, 'bg', 'Bulgarie', 1, NULL),
(34, 'bf', 'Burkina Faso', 0, NULL),
(35, 'bi', 'Burundi', 0, NULL),
(36, 'kh', 'Cambodge', 0, NULL),
(37, 'cm', 'Cameroun', 0, NULL),
(38, 'ca', 'Canada', 0, NULL),
(39, 'cv', 'Cap-Vert', 0, NULL),
(40, 'cl', 'Chili', 0, NULL),
(41, 'cn', 'Chine', 0, NULL),
(42, 'cy', 'Chypre', 1, NULL),
(43, 'co', 'Colombie', 0, NULL),
(44, 'km', 'Comores et Mayotte', 0, NULL),
(45, 'cg', 'Congo', 0, NULL),
(46, 'kp', 'Corée du Nord', 0, NULL),
(47, 'kr', 'Corée du Sud', 0, NULL),
(48, 'cr', 'Costa Rica', 0, NULL),
(49, 'ci', 'Côte d''Ivoire', 0, NULL),
(50, 'hr', 'Croatie', 1, NULL),
(51, 'cu', 'Cuba', 0, NULL),
(52, 'cw', 'Curaçao', 0, NULL),
(53, 'dk', 'Danemark', 1, NULL),
(54, 'io', 'Diego Garcia', 0, NULL),
(55, 'dj', 'Djibouti', 0, NULL),
(56, 'dm', 'Dominique', 0, NULL),
(57, 'eg', 'Égypte', 0, NULL),
(58, 'sv', 'El Salvador', 0, NULL),
(59, 'ae', 'Émirats arabes unis', 0, NULL),
(60, 'ec', 'Équateur', 0, NULL),
(61, 'er', 'Érythrée', 0, NULL),
(62, 'es', 'Espagne', 1, NULL),
(63, 'ee', 'Estonie', 1, NULL),
(64, 'sx', 'État de Saint-Martin', 0, NULL),
(65, 'us', 'États-Unis', 0, NULL),
(66, 'et', 'Éthiopie', 0, NULL),
(67, 'fj', 'Fidji', 0, NULL),
(68, 'fi', 'Finlande', 1, NULL),
(69, 'fr', 'France', 1, 20),
(70, 'ga', 'Gabon', 0, NULL),
(71, 'gm', 'Gambie', 0, NULL),
(72, 'ge', 'Géorgie', 0, NULL),
(73, 'gh', 'Ghana', 0, NULL),
(74, 'gi', 'Gibraltar', 0, NULL),
(75, 'gr', 'Grèce', 1, NULL),
(76, 'gd', 'Grenade', 0, NULL),
(77, 'gl', 'Groenland', 0, NULL),
(78, 'gp', 'Guadeloupe', 0, NULL),
(79, 'gu', 'Guam', 0, NULL),
(80, 'gt', 'Guatemala', 0, NULL),
(81, 'gn', 'Guinée', 0, NULL),
(82, 'gq', 'Guinée équatoriale', 0, NULL),
(83, 'gw', 'Guinée-Bissau', 0, NULL),
(84, 'gy', 'Guyana', 0, NULL),
(85, 'gf', 'Guyane française', 0, NULL),
(86, 'ht', 'Haïti', 0, NULL),
(87, 'hn', 'Honduras', 0, NULL),
(88, 'hk', 'Hong Kong', 0, NULL),
(89, 'hu', 'Hongrie', 1, NULL),
(90, 'ac', 'Île de l''Ascension', 0, NULL),
(91, 'nf', 'Île Norfolk', 0, NULL),
(92, 'ky', 'Îles Caïmans', 0, NULL),
(93, 'ck', 'Îles Cook', 0, NULL),
(94, 'fo', 'Îles Féroé', 0, NULL),
(95, 'fk', 'Îles Malouines', 0, NULL),
(96, 'mp', 'Îles Mariannes du Nord', 0, NULL),
(97, 'mh', 'Îles Marshall', 0, NULL),
(98, 'sb', 'Îles Salomon', 0, NULL),
(99, 'vi', 'Îles Vierges américaines', 0, NULL),
(100, 'vg', 'Îles Vierges britanniques', 0, NULL),
(101, 'in', 'Inde', 0, NULL),
(102, 'id', 'Indonésie', 0, NULL),
(103, 'iq', 'Irak', 0, NULL),
(104, 'ir', 'Iran', 0, NULL),
(105, 'ie', 'Irlande', 1, NULL),
(106, 'is', 'Islande', 0, NULL),
(107, 'il', 'Israël', 0, NULL),
(108, 'it', 'Italie', 1, NULL),
(109, 'jm', 'Jamaïque', 0, NULL),
(110, 'jp', 'Japon', 0, NULL),
(111, 'jo', 'Jordanie', 0, NULL),
(112, 'kz', 'Kazakhstan', 0, NULL),
(113, 'ke', 'Kenya', 0, NULL),
(114, 'kg', 'Kirghizstan', 0, NULL),
(115, 'ki', 'Kiribati', 0, NULL),
(116, 'kw', 'Koweït', 0, NULL),
(117, 'la', 'Laos', 0, NULL),
(118, 'ls', 'Lesotho', 0, NULL),
(119, 'lv', 'Lettonie', 1, NULL),
(120, 'lb', 'Liban', 0, NULL),
(121, 'lr', 'Libéria', 0, NULL),
(122, 'ly', 'Libye', 0, NULL),
(123, 'li', 'Liechtenstein', 0, NULL),
(124, 'lt', 'Lituanie', 1, NULL),
(125, 'lu', 'Luxembourg', 1, NULL),
(126, 'mo', 'Macao', 0, NULL),
(127, 'mk', 'Macédoine', 0, NULL),
(128, 'mg', 'Madagascar', 0, NULL),
(129, 'my', 'Malaisie', 0, NULL),
(130, 'mw', 'Malawi', 0, NULL),
(131, 'mv', 'Maldives', 0, NULL),
(132, 'ml', 'Mali', 0, NULL),
(133, 'mt', 'Malte', 1, NULL),
(134, 'ma', 'Maroc', 0, NULL),
(135, 'mq', 'Martinique', 0, NULL),
(136, 'mu', 'Maurice', 0, NULL),
(137, 'mr', 'Mauritanie', 0, NULL),
(138, 'mx', 'Mexique', 0, NULL),
(139, 'fm', 'Micronésie', 0, NULL),
(140, 'md', 'Moldavie', 0, NULL),
(141, 'mc', 'Monaco', 0, NULL),
(142, 'mn', 'Mongolie', 0, NULL),
(143, 'me', 'Monténégro', 0, NULL),
(144, 'ms', 'Montserrat', 0, NULL),
(145, 'mz', 'Mozambique', 0, NULL),
(146, 'mm', 'Myanmar', 0, NULL),
(147, 'na', 'Namibie', 0, NULL),
(148, 'nr', 'Nauru', 0, NULL),
(149, 'np', 'Népal', 0, NULL),
(150, 'ni', 'Nicaragua', 0, NULL),
(151, 'ne', 'Niger', 0, NULL),
(152, 'ng', 'Nigeria', 0, NULL),
(153, 'nu', 'Niue', 0, NULL),
(154, 'no', 'Norvège', 0, NULL),
(155, 'nc', 'Nouvelle-Calédonie', 0, NULL),
(156, 'nz', 'Nouvelle-Zélande', 0, NULL),
(157, 'om', 'Oman', 0, NULL),
(158, 'ug', 'Ouganda', 0, NULL),
(159, 'uz', 'Ouzbékistan', 0, NULL),
(160, 'pk', 'Pakistan', 0, NULL),
(161, 'pw', 'Palaos', 0, NULL),
(162, 'ps', 'Palestine', 0, NULL),
(163, 'pa', 'Panama', 0, NULL),
(164, 'pg', 'Papouasie - Nouvelle-Guinée', 0, NULL),
(165, 'py', 'Paraguay', 0, NULL),
(166, 'nl', 'Pays-Bas', 1, NULL),
(167, 'pe', 'Pérou', 0, NULL),
(168, 'ph', 'Philippines', 0, NULL),
(169, 'pl', 'Pologne', 1, NULL),
(170, 'pf', 'Polynésie française', 0, NULL),
(171, 'pr', 'Porto Rico', 0, NULL),
(172, 'pt', 'Portugal', 1, NULL),
(173, 'qa', 'Qatar', 0, NULL),
(174, 'cf', 'République Centrafricaine', 0, NULL),
(175, 'cd', 'République démocratique du Congo', 0, NULL),
(176, 'do', 'République dominicaine', 0, NULL),
(177, 'cz', 'République tchèque', 1, NULL),
(178, 're', 'Réunion', 0, NULL),
(179, 'ro', 'Roumanie', 1, NULL),
(180, 'gb', 'Royaume-Uni', 1, NULL),
(181, 'ru', 'Russie', 0, NULL),
(182, 'rw', 'Rwanda', 0, NULL),
(183, 'bl', 'Saint-Barthélemy', 0, NULL),
(184, 'kn', 'Saint-Christophe-et-Nevis', 0, NULL),
(185, 'sm', 'Saint-Marin', 0, NULL),
(186, 'mf', 'Saint-Martin (Antilles françaises)', 0, NULL),
(187, 'pm', 'Saint-Pierre-et-Miquelon', 0, NULL),
(188, 'vc', 'Saint-Vincent-et-les-Grenadines', 0, NULL),
(189, 'sh', 'Sainte-Hélène', 0, NULL),
(190, 'lc', 'Sainte-Lucie', 0, NULL),
(191, 'ws', 'Samoa', 0, NULL),
(192, 'as', 'Samoa américaines', 0, NULL),
(193, 'st', 'Sao Tomé-et-Principe', 0, NULL),
(194, 'sn', 'Sénégal', 0, NULL),
(195, 'rs', 'Serbie', 0, NULL),
(196, 'sc', 'Seychelles', 0, NULL),
(197, 'sl', 'Sierra Leone', 0, NULL),
(198, 'sg', 'Singapour', 0, NULL),
(199, 'sk', 'Slovaquie', 1, NULL),
(200, 'si', 'Slovénie', 1, NULL),
(201, 'so', 'Somalie', 0, NULL),
(202, 'sd', 'Soudan', 0, NULL),
(203, 'ss', 'Soudan du Sud', 0, NULL),
(204, 'lk', 'Sri Lanka', 0, NULL),
(205, 'se', 'Suède', 1, NULL),
(206, 'ch', 'Suisse', 0, NULL),
(207, 'sr', 'Suriname', 0, NULL),
(208, 'sz', 'Swaziland', 0, NULL),
(209, 'sy', 'Syrie', 0, NULL),
(210, 'tj', 'Tadjikistan', 0, NULL),
(211, 'tw', 'Taïwan', 0, NULL),
(212, 'tz', 'Tanzanie', 0, NULL),
(213, 'td', 'Tchad', 0, NULL),
(214, 'th', 'Thaïlande', 0, NULL),
(215, 'tl', 'Timor-Oriental', 0, NULL),
(216, 'tg', 'Togo', 0, NULL),
(217, 'tk', 'Tokelau', 0, NULL),
(218, 'to', 'Tonga', 0, NULL),
(219, 'tt', 'Trinité-et-Tobago', 0, NULL),
(220, 'tn', 'Tunisie', 0, NULL),
(221, 'tm', 'Turkménistan', 0, NULL),
(222, 'tc', 'Turks-et-Caïcos', 0, NULL),
(223, 'tr', 'Turquie', 0, NULL),
(224, 'tv', 'Tuvalu', 0, NULL),
(225, 'ua', 'Ukraine', 0, NULL),
(226, 'uy', 'Uruguay', 0, NULL),
(227, 'vu', 'Vanuatu', 0, NULL),
(228, 'va', 'Vatican', 0, NULL),
(229, 've', 'Venezuela', 0, NULL),
(230, 'vn', 'Viêt Nam', 0, NULL),
(231, 'wf', 'Wallis-et-Futuna', 0, NULL),
(232, 'ye', 'Yémen', 0, NULL),
(233, 'zm', 'Zambie', 0, NULL),
(234, 'zw', 'Zimbabwe', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE IF NOT EXISTS `departement` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `num_region` int(11) NOT NULL,
  `label_departement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `limitrophe` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `departement`
--

INSERT INTO `departement` (`id`, `num_region`, `label_departement`, `limitrophe`) VALUES
('1', 22, 'Ain', '38,39,69,71,73,74'),
('10', 8, 'Aube', '21,51,52,77,89'),
('11', 13, 'Aude', '09,31,34,66,81'),
('12', 16, 'Aveyron', '15,30,34,46,48,81,82'),
('13', 18, 'Bouches du rhÃ´ne', '30,83,84'),
('14', 4, 'Calvados', '27,50,61,76'),
('15', 3, 'Cantal', '12,19,43,46,48,63'),
('16', 21, 'Charente', '17,24,79,86,87'),
('17', 21, 'Charente maritime', '16,24,33,79,85'),
('18', 7, 'Cher', '03,23,36,41,45,58'),
('19', 14, 'CorrÃ¨ze', '15,23,24,46,63,87'),
('2', 20, 'Aisne', '08,51,59,60,77,80'),
('21', 5, 'CÃ´te d''or', '10,39,52,58,70,71,89'),
('22', 6, 'CÃ´tes d''Armor', '29,35,56'),
('23', 14, 'Creuse', '03,18,19,36,63,87'),
('24', 2, 'Dordogne', '16,17,19,33,46,47,87'),
('25', 10, 'Doubs', '39,70,90'),
('26', 22, 'DrÃ´me', '04,05,07,38,84'),
('27', 11, 'Eure', '14,28,60,61,76,78,95'),
('28', 7, 'Eure et Loir', '27,41,45,61,72,78,91'),
('29', 6, 'FinistÃ¨re', '22,56'),
('2a', 9, 'Corse du Sud', '2b'),
('2b', 9, 'Haute Corse', '2a'),
('3', 3, 'Allier', '18,23,42,58,63,71'),
('30', 13, 'Gard', '07,12,13,34,48,84'),
('31', 16, 'Haute garonne', '09,11,32,65,81,82'),
('32', 16, 'Gers', '31,40,47,64,65,82'),
('33', 2, 'Gironde', '17,24,40,47'),
('34', 13, 'HÃ©rault', '11,12,30,81'),
('35', 6, 'Ile et Vilaine', '22,44,49,50,53,56'),
('36', 7, 'Indre', '18,23,37,41,86,87'),
('37', 7, 'Indre et Loire', '36,41,49,72,86'),
('38', 22, 'IsÃ¨re', '01,05,07,26,42,69,73'),
('39', 10, 'Jura', '01,21,25,70,71'),
('4', 18, 'Alpes de haute provence', '05,06,26,83,84'),
('40', 2, 'Landes', '32,33,47,64'),
('41', 7, 'Loir et Cher', '18,28,36,37,45,72'),
('42', 22, 'Loire', '03,07,38,43,63,69,71'),
('43', 3, 'Haute loire', '07,15,42,48,63'),
('44', 19, 'Loire Atlantique', '35,49,56,85'),
('45', 7, 'Loiret', '18,28,41,58,77,89,91'),
('46', 16, 'Lot', '12,15,19,24,47,82'),
('47', 2, 'Lot et Garonne', '24,32,33,40,46,82'),
('48', 13, 'LozÃ¨re', '07,12,15,30,43'),
('49', 19, 'Maine et Loire', '35,37,44,53,72,79,85,86'),
('5', 18, 'Hautes alpes', '04,26,38,73'),
('50', 4, 'Manche', '14,35,53,61'),
('51', 8, 'Marne', '02,08,10,52,55,77'),
('52', 8, 'Haute Marne', '10,21,51,55,70,88'),
('53', 19, 'Mayenne', '35,49,50,61,72'),
('54', 15, 'Meurthe et Moselle', '55,57,67,88'),
('55', 15, 'Meuse', '08,51,52,54,88'),
('56', 6, 'Morbihan', '22,29,35,44'),
('57', 15, 'Moselle', '54,67'),
('58', 5, 'NiÃ¨vre', '03,18,21,45,71,89'),
('59', 17, 'Nord', '02,62,80'),
('6', 18, 'Alpes maritimes', '04,83'),
('60', 20, 'Oise', '02,27,76,77,80,95'),
('61', 4, 'Orne', '14,27,28,50,53,72'),
('62', 17, 'Pas de Calais', '59,80'),
('63', 3, 'Puy de DÃ´me', '03,15,19,23,42,43'),
('64', 2, 'PyrÃ©nÃ©es Atlantiques', '32,40,65'),
('65', 16, 'Hautes PyrÃ©nÃ©es', '31,32,64'),
('66', 13, 'PyrÃ©nÃ©es Orientales', '09,11'),
('67', 1, 'Bas Rhin', '54,57,68,88'),
('68', 1, 'Haut Rhin', '67,88,90'),
('69', 22, 'RhÃ´ne', '01,38,42,71'),
('7', 22, 'ArdÃ¨che', '26,30,38,42,43,48,84'),
('70', 10, 'Haute SaÃ´ne', '21,25,39,52,88,90'),
('71', 5, 'SaÃ´ne et Loire', '01,03,21,39,42,58,69'),
('72', 19, 'Sarthe', '28,37,41,49,53,61'),
('73', 22, 'Savoie', '01,05,38,74'),
('74', 22, 'Haute Savoie', '01,73'),
('75', 12, 'Paris', '92,93,94'),
('76', 11, 'Seine Maritime', '14,27,60,80'),
('77', 12, 'Seine et Marne', '02,10,45,51,60,89,91,93,94,95'),
('78', 12, 'Yvelines', '27,28,91,92,95'),
('79', 21, 'Deux SÃ¨vres', '16,17,49,85,86'),
('8', 8, 'Ardennes', '02,51,55'),
('80', 20, 'Somme', '02,59,60,62,76'),
('81', 16, 'Tarn', '11,12,31,34,82'),
('82', 16, 'Tarn et Garonne', '12,31,32,46,47,81'),
('83', 18, 'Var', '04,06,13,84'),
('84', 18, 'Vaucluse', '04,07,13,26,30,83'),
('85', 19, 'VendÃ©e', '17,44,49,79'),
('86', 21, 'Vienne', '16,36,37,49,79,87'),
('87', 14, 'Haute Vienne', '16,19,23,24,36,86'),
('88', 15, 'Vosge', '52,54,55,67,68,70,90'),
('89', 5, 'Yonne', '10,21,45,58,77'),
('9', 16, 'AriÃ¨ge', '11,31,66'),
('90', 10, 'Territoire de Belfort', '25,68,70,88'),
('91', 12, 'Essonne', '28,45,77,78,92,94'),
('92', 12, 'Haut de seine', '75,78,91,93,94,95'),
('93', 12, 'Seine Saint Denis', '75,77,92,94,95'),
('94', 12, 'Val de Marne', '75,77,91,92,93'),
('95', 12, 'Val d''Oise', '27,60,77,78,92,93');

-- --------------------------------------------------------

--
-- Structure de la table `document`
--

CREATE TABLE IF NOT EXISTS `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_uploaded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8698A763EB8070A` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `easy_settings`
--

CREATE TABLE IF NOT EXISTS `easy_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `simple_rate` int(11) NOT NULL,
  `multi_rate` int(11) NOT NULL,
  `simple_operation_id` int(11) DEFAULT NULL,
  `multi_operation_id` int(11) DEFAULT NULL,
  `uploaded_file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_EF4F64033EB8070A` (`program_id`),
  UNIQUE KEY `UNIQ_EF4F6403BE5365A3` (`simple_operation_id`),
  UNIQUE KEY `UNIQ_EF4F6403C22A6A48` (`multi_operation_id`),
  KEY `IDX_EF4F6403276973A0` (`uploaded_file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- Contenu de la table `easy_settings`
--

INSERT INTO `easy_settings` (`id`, `program_id`, `simple_rate`, `multi_rate`, `simple_operation_id`, `multi_operation_id`, `uploaded_file_id`) VALUES
(1, 7, 12, 9, 9, 10, 3),
(2, 8, 3, 3, 11, 12, NULL),
(4, 10, 3, 3, 15, 16, 36),
(8, 17, 3, 3, 26, 27, 13),
(9, 15, 7, 16, 28, 29, 12),
(10, 19, 3, 3, 30, 31, 15),
(11, 20, 3, 3, 32, 33, 17),
(12, 21, 3, 3, 34, 35, 19),
(14, 23, 3, 3, 38, 39, 23),
(15, 24, 3, 3, 40, 41, 25),
(16, 25, 3, 3, 42, 43, 27),
(17, 26, 3, 3, 44, 45, 29),
(19, 28, 3, 3, 49, 50, 32),
(20, 22, 3, 3, 51, 52, 21),
(21, 29, 3, 3, 53, 54, NULL),
(23, 32, 3, 3, 60, 61, NULL),
(24, 33, 3, 3, 62, 63, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `gift_orders`
--

CREATE TABLE IF NOT EXISTS `gift_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `quarter` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `gift_orders`
--

INSERT INTO `gift_orders` (`id`, `amount`, `quarter`, `year`, `created_date`, `payment_date`) VALUES
(1, 380, 2, 2014, '2014-04-01 11:29:53', '2014-04-01 11:29:56'),
(2, 660, 3, 2014, '2014-07-01 12:24:17', '2014-07-01 12:24:22'),
(3, 140, 4, 2014, '2014-10-01 12:26:10', '2014-10-01 12:26:14'),
(4, 140.2, 1, 2016, '2016-02-05 11:07:06', '2016-11-25 13:40:08');

-- --------------------------------------------------------

--
-- Structure de la table `invitations`
--

CREATE TABLE IF NOT EXISTS `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `sponsor_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `date_validate` datetime NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `collaborator_id` int(11) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `infos` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_232710AE3EB8070A` (`program_id`),
  KEY `IDX_232710AE12F7FB51` (`sponsor_id`),
  KEY `IDX_232710AE30098C8C` (`collaborator_id`),
  KEY `IDX_232710AE770124B2` (`college_id`),
  KEY `IDX_232710AE2576E0FD` (`contract_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `invitations`
--

INSERT INTO `invitations` (`id`, `program_id`, `sponsor_id`, `token`, `first_name`, `last_name`, `email`, `created_date`, `date_validate`, `discr`, `collaborator_id`, `college_id`, `contract_id`, `infos`) VALUES
(1, 16, 169, 'VQ4bPc10z89gDtBijBA8t45UPWcUngOhtWv0WgtfDRE', 'Michel', 'Michel', 'michel@michel.michel', '2018-06-01 11:02:38', '2018-07-01 00:00:00', 'prod', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandataire_id` int(11) DEFAULT NULL,
  `amount_ht` double NOT NULL,
  `amount_ttc` double NOT NULL,
  `created_date` datetime NOT NULL,
  `last_invoice_id` int(11) DEFAULT NULL,
  `balance` double NOT NULL,
  `series` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `invoicing_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6A2F2F95C0FD9B17` (`last_invoice_id`),
  KEY `IDX_6A2F2F9558207E03` (`mandataire_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `invoices`
--

INSERT INTO `invoices` (`id`, `mandataire_id`, `amount_ht`, `amount_ttc`, `created_date`, `last_invoice_id`, `balance`, `series`, `year`, `month`, `rank`, `invoicing_date`) VALUES
(1, 7, 4510.5, 5412.6, '2016-02-02 16:13:18', NULL, 0, NULL, 2016, 1, 1, '2016-01-31 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `journal`
--

CREATE TABLE IF NOT EXISTS `journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `parameters` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C1A7E74D3EB8070A` (`program_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36 ;

--
-- Contenu de la table `journal`
--

INSERT INTO `journal` (`id`, `program_id`, `url`, `method`, `parameters`, `created_date`) VALUES
(9, 5, 'http://dev.plus/app_dev.php/api/members.json', 'POST', '{"userId":"2","email":"apimember2@test.com","firstName":"APIMember2","lastName":"Test","tokenInvitation":""}', '2014-01-01 16:59:10'),
(10, 5, 'http://dev.plus/app_dev.php/api/members.json', 'POST', '{"userId":"1","email":"apimember1@test.com","firstName":"APIMember1","lastName":"Test","tokenInvitation":""}', '2014-01-01 17:01:31'),
(11, 5, 'http://dev.plus/app_dev.php/api/members.json', 'POST', '{"userId":"3","email":"apimember3@test.com","firstName":"APIMember3","lastName":"Test","tokenInvitation":""}', '2014-01-01 17:29:36'),
(12, 5, 'http://dev.plus/app_dev.php/api/members/3.json', 'GET', '', '2014-01-01 17:49:52'),
(13, 5, 'http://dev.plus/app_dev.php/api/members/3/points.json', 'POST', '{"amount":"500","labelOperation":"Bonus Client","info":""}', '2014-01-01 17:50:58'),
(14, 16, 'http://api.cooperons/api/members.json', 'POST', 'email=eeeeeeeeee', '2014-01-01 17:34:45'),
(15, 16, 'http://api.cooperons/api/members.json', 'POST', 'email=eeeeeeeeee%40ddd', '2014-01-01 17:34:54'),
(16, 16, 'http://api.cooperons/api/members.json', 'POST', 'email=eeeeeeeeee%40ddd.com', '2014-01-01 17:34:58'),
(17, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:36:46'),
(18, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:36:57'),
(19, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:37:16'),
(20, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:37:17'),
(21, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:38:15'),
(22, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:38:17'),
(23, 16, 'http://api.cooperons/api/members.json', 'POST', '', '2014-01-01 17:38:36'),
(24, 16, 'http://api.cooperons/api/members/1.json', 'GET', '', '2014-01-01 17:42:14'),
(25, 16, 'http://api.cooperons/api/members/1.json', 'GET', '', '2014-01-01 17:46:27'),
(26, 16, 'http://api.cooperons/api/members/1.json', 'GET', '', '2014-01-01 17:47:14'),
(27, 16, 'http://api.cooperons/api/members/2.json', 'GET', '', '2014-01-01 17:47:16'),
(28, 16, 'http://api.cooperons/api/members.json', 'POST', 'firstName=test&lastName=test&email=test%40test.com&userId=1', '2014-01-01 18:09:30'),
(29, 16, 'http://api.cooperons/api/members.json', 'POST', 'firstName=test&lastName=test&email=test%40test.com&userId=1', '2014-01-01 18:09:39'),
(30, 16, 'http://api.cooperons/api/members.json', 'POST', 'firstName=test&lastName=test&email=test%40test.com&userId=2', '2014-01-01 18:09:49'),
(31, 16, 'http://api.cooperons/api/members/2.json', 'GET', '', '2014-01-01 18:10:36'),
(32, 16, 'http://api.cooperons/api/members/1.json', 'GET', '', '2014-01-01 18:10:41'),
(33, 16, 'http://ae.cooperons/api/invitations/1.json', 'POST', 'sendMail=1', '2018-06-01 11:02:13'),
(34, 16, 'http://ae.cooperons/api/invitations/1.json', 'POST', '{ "email":"michel@michel.michel", "firstName":"Michel", "lastName":"Michel" }', '2018-06-01 11:02:38'),
(35, 16, 'http://ae.cooperons/api/invitations/1.json', 'POST', '{ "email":"michel@michel.michel", "firstName":"Michel", "lastName":"Michel", "sendMail" : true }', '2018-06-01 11:03:28');

-- --------------------------------------------------------

--
-- Structure de la table `mail_invitations`
--

CREATE TABLE IF NOT EXISTS `mail_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `header` longtext COLLATE utf8_unicode_ci,
  `footer` longtext COLLATE utf8_unicode_ci,
  `created_date` datetime NOT NULL,
  `signature` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_635820053EB8070A` (`program_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

--
-- Contenu de la table `mail_invitations`
--

INSERT INTO `mail_invitations` (`id`, `program_id`, `code`, `subject`, `content`, `header`, `footer`, `created_date`, `signature`) VALUES
(1, 1, 'default', 'Le bon plan de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN% pour développer votre activité à moindre coût', 'Bonjour %FIRSTNAME_FILLEUL% %LASTNAME_FILLEUL%,\n\nJe me permets de vous contacter suite à la recommandation de <b>%FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%</b> - qui vous a certainement parlé de Coopérons !\n\nEn quelques mots, Coopérons ! vous permet de créer votre réseau d''apporteur d''affaires, rémunéré par Coopérons ! en chèques cadeau Amazon - jusqu''à 1''000 € par trimestre par bénéficiaire.\n\nEn plus, nous vous aidons à développer votre réseau grâce à la mécanique des MultiPoints, qui permet aux membres de votre réseau de gagner les 2/3 des avantages perçus par leurs filleuls !\n\nVoilà un moyen de développer facilement et à moindre coût votre activité - et de mobiliser vos clients, fournisseurs, partenaires autour de vrais incentives.\n\nCe service ne vous coûte en effet que 39 € HT / mois et une commission de 25% du montant des avantages offerts à votre réseau d''apporteur d''affaires. Cette commission nous permet de couvrir le paiement des charges sociales (20%) conformément à la réglementation en vigueur (rémunération versée par des tiers)\n\n<b><u>Il s''agit tout simplement du système de rémunération le moins cher dans le cadre de la législation actuelle !</u></b>\n\n<b>%FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%</b> a pensé que vous pourriez être intéressé par cette offre confidentielle: si vous souhaitez en savoir plus, vous pouvez vous rendre sur notre site privé qui vous fournira toutes les informations sur le fonctionnement de Coopérons !\n\n<a href="%LANDING_URL%" target="_blank">Cliquez ici pour en savoir plus sur Coopérons !</a>\n\nA très vite sur Coopérons !\n\nL''équipe Coopérons !\n\nPS: cette offre est à durée limitée - vous ne pourrez en effet plus accéder à notre site 30 jours à compter la réception de cet email …\n', '', '', '2014-11-25 10:00:00', NULL),
(2, 5, 'default', 'Invitation Edatis', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />Prospect Test<br />SAS EDATIS', NULL, NULL, '2014-01-01 11:21:35', NULL),
(4, 7, 'default', 'Invitation blocs', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2016-02-02 16:15:10', 'Member Test<br />Fondative'),
(5, 10, 'default', 'Invitation gggg', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br />', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2016-02-04 18:58:28', 'Member Test<br />Fondative'),
(6, 11, 'default', 'Invitation capture02', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />Member Test<br />Fondative', NULL, NULL, '2016-02-04 19:00:24', NULL),
(10, 15, 'default', 'Invitation ererre', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2016-11-25 18:08:40', 'Member Test<br />Fondative'),
(11, 16, 'default', 'Invitation Api', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />Member Test<br />Fondative', NULL, NULL, '2016-11-25 18:10:33', NULL),
(13, 17, 'default', 'Invitation Test', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 18:00:14', 'Member Test<br />Fondative'),
(14, 19, 'default', 'Invitation Testv2122', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:28:46', 'Member Test<br />Fondative'),
(15, 20, 'default', 'Invitation rrrrrrrrrrrr', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:31:12', 'Member Test<br />Fondative'),
(16, 21, 'default', 'Invitation aaaaaaaaaaaaaaaa', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:33:30', 'Prospect Test<br />SAS EDATIS'),
(17, 22, 'default', 'Invitation aaaaaaaaa', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:36:33', 'Prospect Test<br />SAS EDATIS'),
(18, 23, 'default', 'Invitation ssssssssss', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:41:49', 'Prospect Test<br />SAS EDATIS'),
(19, 24, 'default', 'Invitation aaaaaaaaaaaaaaa', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:43:37', 'Prospect Test<br />SAS EDATIS'),
(20, 25, 'default', 'Invitation ssssssssssss', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:57:14', 'Prospect Test<br />SAS EDATIS'),
(21, 26, 'default', 'Invitation fffffffffffff', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 10:59:20', 'Prospect Test<br />SAS EDATIS'),
(22, 27, 'default', 'Invitation aaaaaaaaaaa', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />Prospect Test<br />SAS EDATIS', NULL, NULL, '2015-04-01 11:05:01', NULL),
(23, 28, 'default', 'Invitation aaaaaaaaaa', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2015-04-01 11:18:25', 'Prospect Test<br />SAS EDATIS'),
(26, 16, 'rrrrrrrrrrr', 'rrr', 'rrrrrrrrrr', NULL, NULL, '2015-04-01 11:27:14', NULL),
(28, 30, 'default', 'Invitation Testtt Api', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />Member Test<br />Fondative', NULL, NULL, '2018-06-01 14:27:58', NULL),
(29, 32, 'default', 'Invitation tn', '', 'Bonjour %FIRSTNAME% %LASTNAME%,\n                    <br /><br />\n                    Je vous contacte suite à la recommandation de %FIRSTNAME_PARRAIN% %LASTNAME_PARRAIN%.\n                    <br /><br /> ', 'Vous pouvez en savoir plus sur notre offre:\n                    <br /><br />\n                    %LANDING_URL%\n                    <br /><br />\n                    A bientôt,\n                    <br /><br />', '2018-06-01 14:27:59', 'Member Test<br />Fondative');

-- --------------------------------------------------------

--
-- Structure de la table `mandataire`
--

CREATE TABLE IF NOT EXISTS `mandataire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `owner_label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `owner_account_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `client_account_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `owner_income_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `client_income_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `owner_account_id` int(11) NOT NULL,
  `client_account_id` int(11) NOT NULL,
  `depot` double NOT NULL,
  `liquidation_date` datetime DEFAULT NULL,
  `last_invoice_id` int(11) DEFAULT NULL,
  `stand_by_payment_out_id` int(11) DEFAULT NULL,
  `min_depot` double NOT NULL,
  `total_income_ht` double NOT NULL,
  `invoicing_frequency` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1DA4DCB4C0FD9B17` (`last_invoice_id`),
  UNIQUE KEY `UNIQ_1DA4DCB4107C4E51` (`stand_by_payment_out_id`),
  KEY `IDX_1DA4DCB47E3C61F9` (`owner_id`),
  KEY `IDX_1DA4DCB419EB6921` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=91 ;

--
-- Contenu de la table `mandataire`
--

INSERT INTO `mandataire` (`id`, `owner_id`, `client_id`, `owner_label`, `client_label`, `owner_account_ref`, `client_account_ref`, `owner_income_ref`, `client_income_ref`, `owner_account_id`, `client_account_id`, `depot`, `liquidation_date`, `last_invoice_id`, `stand_by_payment_out_id`, `min_depot`, `total_income_ht`, `invoicing_frequency`) VALUES
(6, 5, 1, 'Compte Banque: Coopérons', 'Compte Banque', '517', '517', '706', '622', 2, 2, 5601356, NULL, NULL, NULL, 1051.72, 0, 0),
(7, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 3, 2, 0, '2015-04-01 14:01:42', 1, NULL, 2000, 4510.5, 2),
(8, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 4, 2, 438.4, NULL, NULL, NULL, 50000000000, 936, 2),
(9, 1, 9, 'Compte Mandataire: Member Test', 'Compte Mandataire', '517', '517', '706', '622', 5, 2, 779, NULL, NULL, NULL, 9, 0, 3),
(10, 1, 10, 'Compte Mandataire: Mohamed BEJI', 'Compte Mandataire', '517', '517', '706', '622', 6, 2, 185.34, NULL, NULL, NULL, 6, 0, 3),
(11, 10, 8, 'Contrat Fondative', 'Contrat Mohamed BEJI', '419', '409', '706', '622', 3, 3, 179.34, NULL, NULL, NULL, 20, 20, 2),
(12, 1, 8, 'Compte Mandataire: Fondative', 'Compte Mandataire', '517', '517', '706', '622', 7, 4, 0, NULL, NULL, NULL, 0, 0.55, 2),
(13, 1, 11, 'Compte Mandataire: Bilel BIJAOUI', 'Compte Mandataire', '517', '517', '706', '622', 8, 2, 40, NULL, NULL, NULL, 0, 0, 3),
(15, 9, 8, 'Contrat Fondative', 'Contrat Member Test', '419', '409', '706', '622', 3, 6, 223, NULL, NULL, NULL, 45, 0, 2),
(16, 1, 12, 'Compte Mandataire: Ahmed LAABIDI', 'Compte Mandataire', '517', '517', '706', '622', 9, 2, 0, NULL, NULL, NULL, 0, 0, 3),
(18, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 10, 8, 0, NULL, NULL, NULL, 500, 0, 2),
(20, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 12, 10, 5555341.8, NULL, NULL, NULL, 500, 1479, 2),
(21, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 13, 11, 1000, NULL, NULL, NULL, 500, 948, 2),
(22, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 14, 12, 315.2, NULL, NULL, NULL, 500, 2340, 2),
(27, 1, 14, 'Compte Mandataire: commercial commercial', 'Compte Mandataire', '517', '517', '706', '622', 15, 2, 0, NULL, NULL, NULL, 0, 0, 3),
(29, 9, 15, 'Contrat société aaa', 'Contrat Member Test', '419', '409', '706', '622', 8, 2, 0, NULL, NULL, NULL, 0, 0, 2),
(40, 9, 11, 'Contrat Bilel BIJAOUI', 'Contrat Member Test', '419', '409', '706', '622', 18, 15, 0, NULL, NULL, NULL, 0, 0, 2),
(41, 11, 13, 'Contrat Ste01', 'Contrat Bilel BIJAOUI', '419', '409', '706', '622', 16, 2, 40, NULL, NULL, NULL, 20, 0, 2),
(43, 1, 16, 'Compte Mandataire: bilel nomen', 'Compte Mandataire', '517', '517', '706', '622', 16, 2, 0, NULL, NULL, NULL, 0, 0, 3),
(57, 12, 9, 'Contrat Member Test', 'Contrat Ahmed LAABIDI', '419', '409', '706', '622', 18, 31, 0, NULL, NULL, NULL, 0, 0, 2),
(58, 12, 8, 'Contrat Fondative', 'Contrat Ahmed LAABIDI', '419', '409', '706', '622', 19, 18, 0, NULL, NULL, NULL, 100, 0, 2),
(59, 1, 17, 'Compte Mandataire: Samir GUELBI', 'Compte Mandataire', '517', '517', '706', '622', 17, 2, 25, NULL, NULL, NULL, 0, 0, 3),
(63, 9, 8, 'Contrat Fondative', 'Contrat Member Test', '419', '409', '706', '622', 35, 19, 0, NULL, NULL, NULL, 0, 0, 2),
(64, 9, 8, 'Contrat Fondative', 'Contrat Member Test', '419', '409', '706', '622', 36, 20, 468, NULL, NULL, NULL, 0, 0, 2),
(65, 17, 13, 'Contrat Ste01', 'Contrat Samir GUELBI', '419', '409', '706', '622', 3, 6, 25, NULL, NULL, NULL, 0, 0, 2),
(66, 1, 13, 'Compte Mandataire: Ste01', 'Compte Mandataire', '517', '517', '706', '622', 18, 7, 0, NULL, NULL, NULL, 0, 0, 2),
(67, 11, 12, 'Contrat Ahmed LAABIDI', 'Contrat Bilel BIJAOUI', '419', '409', '706', '622', 17, 21, 0, NULL, NULL, NULL, 0, 0, 2),
(68, 9, 18, 'Contrat ste03', 'Contrat Member Test', '419', '409', '706', '622', 37, 2, 14, NULL, NULL, NULL, 22, 30, 2),
(69, 1, 18, 'Compte Mandataire: ste03', 'Compte Mandataire', '517', '517', '706', '622', 19, 3, 0, NULL, NULL, NULL, 0, 0, 2),
(70, 11, 18, 'Contrat ste03', 'Contrat Bilel BIJAOUI', '419', '409', '706', '622', 18, 4, 0, NULL, NULL, NULL, 0, 0, 2),
(71, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 20, 21, 2000, NULL, NULL, NULL, 500, 1896, 2),
(72, 1, 19, 'Compte Mandataire: APIMember2 Test', 'Compte Mandataire', '517', '517', '706', '622', 21, 2, 0, NULL, NULL, NULL, 0, 0, 3),
(73, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 22, 22, 876.8, NULL, NULL, NULL, 500, 1872, 2),
(74, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 23, 23, 876.8, NULL, NULL, NULL, 500, 1872, 2),
(75, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 24, 24, 0, '2018-06-01 14:27:58', NULL, NULL, 500, 468, 2),
(76, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 25, 3, 438.4, NULL, NULL, NULL, 500, 936, 2),
(77, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 26, 4, 315.2, NULL, NULL, NULL, 500, 2340, 2),
(78, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 27, 5, 438.4, NULL, NULL, NULL, 500, 936, 2),
(79, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 28, 6, 438.4, NULL, NULL, NULL, 500, 936, 2),
(80, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 29, 7, 438.4, NULL, NULL, NULL, 500, 936, 2),
(81, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 30, 8, 438.4, NULL, NULL, NULL, 500, 936, 2),
(82, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 31, 9, 1000, NULL, NULL, NULL, 500, 948, 2),
(83, 1, 6, 'Contrat SAS EDATIS', 'Contrat SARL Coopérons', '419', '409', '706', '622', 32, 10, 315.2, NULL, NULL, NULL, 500, 2340, 2),
(84, 1, 21, 'Compte Mandataire: Imen KHAZRI', 'Compte Mandataire', '517', '517', '706', '622', 33, 2, 0, NULL, NULL, NULL, 0, 0, 3),
(85, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 34, 25, 0, NULL, NULL, NULL, 500, 0, 2),
(86, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 35, 26, 1000, NULL, NULL, NULL, 500, 948, 2),
(87, 9, 11, 'Contrat Bilel BIJAOUI', 'Contrat Member Test', '419', '409', '706', '622', 18, 15, 0, NULL, NULL, NULL, 0, 0, 2),
(88, 9, 18, 'Contrat ste03', 'Contrat Member Test', '419', '409', '706', '622', 37, 2, 44, NULL, NULL, NULL, 22, 0, 2),
(89, 1, 8, 'Contrat Fondative', 'Contrat SARL Coopérons', '419', '409', '706', '622', 36, 27, 1000, NULL, NULL, NULL, 500, 468, 2),
(90, 1, 7, 'Contrat MonEntreprise', 'Contrat SARL Coopérons', '419', '409', '706', '622', 37, 2, 0, NULL, NULL, NULL, 500, 0, 2);

-- --------------------------------------------------------

--
-- Structure de la table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `firstName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `last_avantage_id` int(11) DEFAULT NULL,
  `remaining_points` int(11) NOT NULL,
  `is_preprod` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_45A0D2FF6B3CA4B` (`id_user`),
  UNIQUE KEY `UNIQ_45A0D2FFAAA0CD20` (`last_avantage_id`),
  KEY `IDX_45A0D2FF770124B2` (`college_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;

--
-- Contenu de la table `members`
--

INSERT INTO `members` (`id`, `id_user`, `firstName`, `lastName`, `email`, `created_date`, `college_id`, `last_avantage_id`, `remaining_points`, `is_preprod`) VALUES
(1, 3, 'Emmanuel', 'Ifergan', 'ei@test.com', '0000-00-00 00:00:00', NULL, NULL, 0, 0),
(18, 21, 'Member', 'Test', 'member@test.com', '2014-01-01 16:30:35', NULL, 1, 0, 0),
(19, 22, 'Prospect', 'Test', 'prospect@test.com', '2014-01-01 09:08:15', NULL, NULL, 0, 0),
(20, 23, 'ProspectNew', 'Test', 'prospectnew@test.com', '2014-01-01 16:32:56', 22, NULL, 0, 0),
(23, 25, 'APIMember2', 'Test', 'apimember2@test.com', '2014-01-01 16:59:10', NULL, 4, 0, 0),
(24, 26, 'APIMember1', 'Test', 'apimember1@test.com', '2014-01-01 17:01:31', NULL, 6, -134, 0),
(25, 24, 'APIMember3', 'Test', 'apimember3@test.com', '2014-01-01 17:29:36', NULL, NULL, 0, 0),
(26, 27, 'DAF', 'Test', 'daf@test.com', '2014-04-01 12:16:37', NULL, NULL, 0, 0),
(27, 28, 'Mohamed', 'BEJI', 'mbj@test.com', '2016-02-02 16:21:11', NULL, NULL, 0, 0),
(28, 29, 'Bilel', 'BIJAOUI', 'bbi@test.com', '2016-02-02 16:43:13', NULL, NULL, 0, 0),
(29, 30, 'Ahmed', 'LAABIDI', 'ali@test.com', '2016-02-03 07:23:56', NULL, NULL, 1, 0),
(30, 31, 'Imen', 'KHAZRI', 'ik@test.com', '2016-02-03 07:35:39', NULL, NULL, 0, 0),
(31, 32, 'Yassine', 'EZZINE', 'ye@test.com', '2016-02-03 07:39:19', NULL, NULL, 0, 0),
(32, 33, 'Samir', 'GUELBI', 'sg@test.com', '2016-02-03 07:45:51', NULL, 7, -1, 0),
(34, NULL, 'salah', 'salah', 'salah@test.com', '2016-02-08 09:57:15', NULL, NULL, 300, 0),
(35, 34, 'commercial', 'commercial', 'commercial@test.com', '2016-02-09 07:54:39', NULL, NULL, 0, 0),
(36, 35, 'aaa', 'aaa', 'aaa@test.com', '2016-02-09 07:59:20', NULL, NULL, 0, 0),
(37, 36, 'bilel', 'nomen', 'bn@test.com', '2016-02-12 13:43:33', NULL, NULL, 0, 0),
(38, 37, 'presta', 'presta', 'presta@test.com', '2016-02-17 09:51:41', 20, NULL, 0, 0),
(39, 38, 'salah', 'salah', 'salah@test.com', '2016-02-17 15:17:18', NULL, NULL, 0, 0),
(40, NULL, NULL, NULL, 'test456@test.com', '2015-04-01 10:00:31', NULL, NULL, 0, 0),
(41, NULL, 'test', 'test', 'test@test.com', '2016-02-27 17:03:01', NULL, NULL, 0, 0),
(42, NULL, NULL, NULL, 'test2@test.com', '2014-04-01 19:38:29', NULL, NULL, 0, 0),
(43, NULL, NULL, NULL, 'test3@test.com', '2016-02-27 19:40:58', NULL, NULL, 0, 0),
(44, NULL, 'test', NULL, 'test4@gmail.com', '2014-04-01 20:10:32', NULL, NULL, 0, 0),
(45, 39, 'test', 'test', 'test@test.com', '2016-02-27 21:07:32', NULL, NULL, 0, 0),
(46, 40, 'test', 'test', 'test78@test.com', '2016-02-27 21:10:53', NULL, NULL, 0, 0),
(48, 41, 'test', NULL, 'test2@test.com', '2014-01-01 21:47:49', NULL, NULL, 0, 0),
(49, NULL, 'test', 'test', 'test@test.com', '2014-01-01 18:09:31', NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `operations`
--

CREATE TABLE IF NOT EXISTS `operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `default_amount` double DEFAULT NULL,
  `is_multi` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_281453483EB8070A` (`program_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=64 ;

--
-- Contenu de la table `operations`
--

INSERT INTO `operations` (`id`, `program_id`, `label`, `description`, `default_amount`, `is_multi`, `created_date`) VALUES
(3, 1, 'Premier Programme', NULL, 500, 1, '2015-08-24 00:00:00'),
(4, 4, 'Commission 1%', NULL, NULL, 1, '2016-01-01 00:00:00'),
(7, 5, 'Apport d''un Prospect', 'lorsqu''un Prospect effectue le règlement de sa première Commande, pour un montant de …', 500, 1, '2014-01-01 10:22:45'),
(8, 5, 'Bonus Client', 'si votre entreprise est déjà cliente du Promoteur, pour un montant de …', 0, 0, '2014-01-01 10:22:54'),
(9, 7, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 12 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-02-02 16:14:40'),
(10, 7, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 9 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-02-02 16:14:40'),
(11, 8, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-02-03 14:28:25'),
(12, 8, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-02-03 14:28:25'),
(15, 10, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-02-04 18:58:02'),
(16, 10, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-02-04 18:58:02'),
(17, 11, 'oprt1', 'dfggfdgdfgdfgdfgd', 0, 1, '2016-02-04 18:59:56'),
(25, 16, '87778878', 'sdvdddf', NULL, 1, '2016-11-25 18:10:12'),
(26, 17, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-11-25 12:30:55'),
(27, 17, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-11-25 12:30:55'),
(28, 15, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 7 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-02-08 07:57:28'),
(29, 15, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 16 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-02-08 07:57:28'),
(30, 19, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:28:25'),
(31, 19, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:28:25'),
(32, 20, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:30:54'),
(33, 20, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:30:54'),
(34, 21, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:32:43'),
(35, 21, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:32:43'),
(38, 23, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:41:30'),
(39, 23, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:41:30'),
(40, 24, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:43:17'),
(41, 24, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:43:17'),
(42, 25, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:55:10'),
(43, 25, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:55:10'),
(44, 26, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:59:00'),
(45, 26, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:59:00'),
(46, 27, 'aaaaaaaaaa', 'aaaaaaaaaaa', NULL, 1, '2015-04-01 11:04:45'),
(49, 28, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 11:18:03'),
(50, 28, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 11:18:03'),
(51, 22, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2015-04-01 10:36:11'),
(52, 22, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2015-04-01 10:36:11'),
(53, 29, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2016-02-27 12:34:28'),
(54, 29, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2016-02-27 12:34:28'),
(55, 30, 'test1', '7777777777777777777', 0, 1, '2014-04-01 14:19:27'),
(56, 30, 'test 2', '66666666666666666666666666', 0, 1, '2014-04-01 14:19:33'),
(57, 30, 'test3', '55555555555555555555555555', 0, 1, '2014-04-01 14:19:40'),
(60, 32, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2014-04-01 09:37:08'),
(61, 32, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2014-04-01 09:37:08'),
(62, 33, 'Commission Simple', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 0, '2014-01-01 18:02:43'),
(63, 33, 'Commission Multi-Niveau', 'dont la valeur (au taux de 20 € pour 100 Points) correspond à 3 % du montant de chaque Réglement Commissionné effectué dans le cadre d''une Nouvelle Affaire.', NULL, 1, '2014-01-01 18:02:43');

-- --------------------------------------------------------

--
-- Structure de la table `participates_to`
--

CREATE TABLE IF NOT EXISTS `participates_to` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `mail_invitation_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `member_program_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sponsorship_id` int(11) DEFAULT NULL,
  `total_multipoints` int(11) NOT NULL,
  `total_points` int(11) NOT NULL,
  `count_affiliates` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_14F4238E7597D3FE` (`member_id`),
  KEY `IDX_14F4238E3EB8070A` (`program_id`),
  KEY `IDX_14F4238E8ACED59A` (`sponsorship_id`),
  KEY `IDX_14F4238EB97F78FC` (`mail_invitation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=170 ;

--
-- Contenu de la table `participates_to`
--

INSERT INTO `participates_to` (`id`, `member_id`, `program_id`, `mail_invitation_id`, `token`, `member_program_id`, `created_date`, `discr`, `sponsorship_id`, `total_multipoints`, `total_points`, `count_affiliates`) VALUES
(108, 1, 1, 1, 'sd8fgsdhbb36dsdfasg', 3, '2014-04-27 00:00:00', 'prod', NULL, 0, 0, 0),
(120, 18, 1, 1, 'oCw_UMA0aUDx0fElTTFyjT4vjg9ijoNOfvVXWIpb0-Y', 21, '2014-01-01 16:30:35', 'prod', NULL, 500, 500, 10),
(121, 1, 4, NULL, 'sd8fgsdhpb36dodfasx', 3, '2014-04-27 00:00:00', 'prod', NULL, 0, 0, 0),
(122, 19, 1, 1, '2tg2gIiZaWkPqJ8KZU_Bg7j1AmVkXbjgzHE2-PpA_E0', 22, '2014-01-01 09:08:15', 'prod', 6, 0, 0, 1),
(123, 20, 1, 1, '0Vt9ui367sRxANwnt0WR3cjHBilapWBUDWH31n960vY', 23, '2014-01-01 16:32:56', 'prod', 7, 0, 0, 0),
(127, 23, 5, 2, 'XoqXM4i2yvf4zz7-Sn5XCLgjZrNcVyHnHwB9J8v-EwA', 2, '2014-01-01 16:59:10', 'prod', 9, 1000, 4000, 1),
(128, 24, 5, 2, '1TGJGa9Plk7G4QSW68hAMA5bseYAQ6IunPJnVZRvORk', 1, '2014-01-01 17:01:31', 'prod', NULL, 666, 2666, 1),
(129, 25, 5, 2, 'r2DHLqoOX_uAMta0ihecq06mzl6JZVI2az73RfUFJVQ', 3, '2014-01-01 17:29:36', 'prod', 10, 0, 0, 0),
(130, 25, 1, 1, 'X4xsGIuo-cMfbx8oh_1hCsul-vlkROkOlrZGoj8zVBs', 24, '2014-01-01 17:30:41', 'prod', 11, 0, 0, 0),
(131, 23, 1, 1, 'lRWnqKpkhE0vM1yZO5dpKD_6n3lue8eFk1OGCNIVUGg', 25, '2014-01-01 11:21:49', 'prod', 12, 0, 0, 2),
(132, 24, 1, 1, 'fX-pRPO7mvrq1lmAFbbmCH5hlLtlTQM_hp7UZxNvjvM', 26, '2014-01-01 11:22:43', 'prod', NULL, 0, 0, 1),
(133, 26, 1, 1, 'F-qWStfxLJIzpfje9mN1C82wzxD_UxGHz_ez4TXCxlI', 27, '2014-04-01 12:16:37', 'prod', 13, 0, 0, 0),
(134, 18, 4, NULL, '1eQpDqM2EVEmBCp5Uhj7S0huCMYkx13COfgEx-ijpic', 21, '2016-02-02 16:18:12', 'prod', 27, 0, 0, 0),
(135, 27, 1, 1, 'i3pBOmQwRLrzgtYmqhE357SOfIkjKq-1_rDGyD4wBJ8', 28, '2016-02-02 16:21:11', 'prod', 21, 0, 0, 0),
(136, 27, 4, NULL, 'CfKJHM0MwbA1e_LnzGLcvvzL6r9uk8YvdayALRxcWvI', 28, '2016-02-02 16:22:51', 'prod', 20, 0, 0, 0),
(137, 28, 7, 4, 'mt_rit_M6WeK-eREpErXmtloQWaTuIq7S_-KNqpojxE', 28, '2016-02-02 16:43:13', 'prod', NULL, 0, 0, 2),
(138, 28, 1, 1, 'IYQTRkV9P58afwt1RaaAp8UKE0FiLGV0GfWTpHBJzbA', 29, '2016-02-02 16:45:14', 'prod', NULL, 0, 0, 1),
(139, 28, 4, NULL, 'mzLfmYB0TlTtAv6Gx1WulfAqmuJA0ijH8mwnm-CDeBQ', 29, '2016-02-02 16:49:01', 'prod', NULL, 0, 0, 0),
(140, 29, 1, 1, '2YU0h0Utz0Z9QlFTEDPn1z0jfHS7JCiEboGuQfA_f2Y', 30, '2016-02-03 07:23:56', 'prod', 15, 0, 0, 0),
(141, 29, 4, NULL, 'XaOvX_A_zcpgnNEkPdLaiCb-5_LzYyJ_Itm_AY65luY', 30, '2016-02-03 07:25:40', 'prod', NULL, 1, 1, 1),
(142, 30, 1, 1, 'SyZSup0_hfVd_Iwr3XJicfMQ1TeBu632_d3ODFPKRUU', 31, '2016-02-03 07:35:39', 'prod', 16, 0, 0, 0),
(143, 31, 7, 4, 'T1jSx3fCl5fZbORG_56dN7IJEWayQXvvjkDGKgG3ulE', 31, '2016-02-03 07:39:19', 'prod', 17, 0, 0, 0),
(144, 31, 1, 1, '7qhwX70bnphlrwWAZ2q7HXkFyslEQQdS3pxkoTY-9FU', 32, '2016-02-03 07:39:55', 'prod', 18, 0, 0, 0),
(145, 32, 1, 1, '0yEbrb475Eb96p6rEoJIW2NxGxnZuGJ2mDVDQnAeeGo', 33, '2016-02-03 07:45:51', 'prod', 19, 0, 0, 1),
(146, 32, 4, NULL, 'Ik5-BoDorH4tYKv6t37JUiYghTt1YtBWFOWTGE9q4M8', 33, '2016-02-03 10:39:55', 'prod', NULL, 1, 1, 1),
(148, 34, 10, 5, 'G9k3BaFpPZKs3OLtD9-OeEIF-HE4gqK8yfs6ANEEC_g', 34, '2016-02-08 09:57:15', 'prod', NULL, 150, 300, 0),
(149, 35, 1, 1, 'RtPlR5RV_Scrmr1jqk4ZPsyQH3a4a06-yfzAN9z7oOE', 34, '2016-02-09 07:54:39', 'prod', 22, 0, 0, 0),
(150, 35, 4, NULL, 'XVpm_IFpgX0D5awDEzCbTsCEAAeD73CHYiFz2sTZCAQ', 34, '2016-02-09 07:55:42', 'prod', NULL, 0, 0, 0),
(151, 36, 1, 1, 'ndtVpLlNTvBuUS4UTfGSYPZt2Lf3tTN7gTJ6d6lop-E', 35, '2016-02-09 07:59:20', 'prod', 23, 0, 0, 0),
(152, 37, 1, 1, 'DzuEM46sjU2X6JENGOKU1qC_119joSaH9QJQCXgi2iI', 36, '2016-02-12 13:43:33', 'prod', 24, 0, 0, 0),
(153, 37, 4, NULL, 'w0vgQrQkxcaPS_uhys7X6SEt5L1D95szhb3_XyB3DZc', 36, '2016-02-12 13:45:22', 'prod', NULL, 0, 0, 0),
(154, 32, 4, NULL, 'mQDxNnqlHRUfhKG3sdmKDbYh9KspqIWHAakrg_2t7P4', 33, '2016-02-16 14:26:52', 'prod', NULL, 0, 0, 0),
(155, 38, 1, 1, 'bWtciDGhkUah26xDkPB3bLTW___NI-UCDltXz_knL4w', 37, '2016-02-17 09:51:41', 'prod', 25, 0, 0, 0),
(156, 39, 1, 1, 'WgRwxRz8Nhi7xPVLafocNFMP_rQhpVVnWmAt_4RyO7s', 38, '2016-02-17 15:17:18', 'prod', 26, 0, 0, 0),
(157, 23, 4, NULL, 'nrFLCvQ4t-A92AxFKYMd4CahkIo7bO7relqOTNvRW24', 25, '2016-11-25 10:22:55', 'prod', NULL, 0, 0, 0),
(158, 40, 7, 4, 'Fg2y8A00x4TvpekXgeCFifFD6WgJUCKdH7Q5yEsqHSI', 40, '2015-04-01 10:00:32', 'prod', 28, 0, 0, 0),
(159, 30, 4, NULL, 'ql_rYfM8MtSdiPJ2YKWoLywJ1FxqDKsBowWgNBU3lC8', 31, '2016-02-26 20:17:06', 'prod', NULL, 0, 0, 0),
(160, 41, 17, 13, 'GjNdzC3GzQ83pB4X_f13fd9IwMzdkV7jvDhoyulE9as', 41, '2016-02-27 17:03:02', 'prod', NULL, 0, 0, 3),
(161, 42, 17, 13, '9YeQdKBsrZDFgDQc7p8kVz-WpKIFBM12A6TlzJgEAus', 42, '2014-04-01 19:38:29', 'prod', 29, 0, 0, 0),
(162, 43, 17, 13, 'nN-OcYWYiBiBpmxk3likS3NGsOWbIe6b5f2DMtNbP14', 43, '2016-02-27 19:40:58', 'prod', 30, 0, 0, 0),
(163, 44, 17, 13, 'qQ8a8a-ydjcnHV6y8utJX_odNiaBJD8OubbgwohP91M', 44, '2014-04-01 20:10:32', 'prod', 31, 0, 0, 0),
(164, 45, 1, 1, 'mKvxnl4M5fFT4MpkwUNDX06qTJWRGoV77Pvw-97C20M', 39, '2016-02-27 21:07:32', 'prod', NULL, 0, 0, 1),
(165, 46, 1, 1, 'ex6GozrIApt_0KYqBj10ntYmw_6VXrkCM0dFuDC0MtA', 40, '2016-02-27 21:10:53', 'prod', NULL, 0, 0, 0),
(166, 45, 15, 10, 'PuQlNy7VNWXwNGYAph6FD8wyTXFwmGIjicJQMuBeTzE', 47, '2014-01-01 21:41:50', 'prod', NULL, 0, 0, 1),
(167, 48, 15, 10, 'CtCWLouK89VsZLANYPzDKTNCIVoTYfBt4tgvjsAP1vk', 48, '2014-01-01 21:47:49', 'prod', 32, 0, 0, 0),
(168, 48, 1, 1, 'sMlZV3kwlxGVTuS_U-KE7QzjsSudJ40yxyE2ZMLFbfg', 41, '2014-01-01 21:48:50', 'prod', 33, 0, 0, 0),
(169, 49, 16, 11, 'vzx77uQECtQXbTOX6BKEy5yYyUvz7jlULOzsHFvDHwA', 1, '2014-01-01 18:09:31', 'prod', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `parties`
--

CREATE TABLE IF NOT EXISTS `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `balance` double NOT NULL,
  `mandataire_id` int(11) DEFAULT NULL,
  `last_account_id` int(11) NOT NULL,
  `last_invoice_id` int(11) DEFAULT NULL,
  `total_income_ht` double NOT NULL,
  `total_released` double NOT NULL,
  `provision_rate` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4363180558207E03` (`mandataire_id`),
  UNIQUE KEY `UNIQ_43631805C0FD9B17` (`last_invoice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

--
-- Contenu de la table `parties`
--

INSERT INTO `parties` (`id`, `balance`, `mandataire_id`, `last_account_id`, `last_invoice_id`, `total_income_ht`, `total_released`, `provision_rate`) VALUES
(1, 33655.26, 6, 37, 1, 28046.05, 0, NULL),
(5, -5601356, NULL, 2, NULL, 0, 0, NULL),
(6, 3822.4, NULL, 10, NULL, 0, 0, NULL),
(7, 0, NULL, 2, NULL, 0, 0, NULL),
(8, 5563719.34, 12, 27, NULL, 0, 0, NULL),
(9, 30, 9, 37, NULL, 30, 0, 30),
(10, 6, 10, 3, NULL, 20, 0, 30),
(11, 0, 13, 18, NULL, 0, 0, 30),
(12, 0, 16, 21, NULL, 0, 0, 30),
(13, 65, 66, 7, NULL, 0, 0, NULL),
(14, 0, 27, 3, NULL, 0, 0, 30),
(15, 0, NULL, 2, NULL, 0, 0, NULL),
(16, 0, 43, 3, NULL, 0, 0, 30),
(17, 0, 59, 3, NULL, 0, 0, 30),
(18, 58, 69, 4, NULL, 0, 0, NULL),
(19, 0, 72, 2, NULL, 0, 0, 30),
(20, 0, NULL, 1, NULL, 0, 0, NULL),
(21, 0, 84, 2, NULL, 0, 0, 30),
(22, 0, NULL, 1, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandataire_id` int(11) DEFAULT NULL,
  `mode` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `auth_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6D28840D58207E03` (`mandataire_id`),
  KEY `IDX_6D28840D4DFD750C` (`record_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=66 ;

--
-- Contenu de la table `payment`
--

INSERT INTO `payment` (`id`, `mandataire_id`, `mode`, `amount`, `status`, `created_date`, `auth_code`, `record_id`) VALUES
(1, 7, 'virement', 2137.6, 1, '2014-01-01 11:21:08', NULL, 1),
(2, 7, 'virement', 1499.8, 1, '2014-01-01 18:26:21', NULL, 8),
(3, 7, 'virement', 2137.6, 1, '2015-01-01 18:43:54', NULL, 15),
(4, 7, 'virement', 5137.6, 1, '2015-04-01 14:00:03', NULL, 20),
(5, 7, 'virement', -5500, 1, '2015-04-01 14:01:26', NULL, 25),
(6, 8, 'virement', 1561.6, 1, '2016-02-02 16:14:59', NULL, 28),
(7, 9, 'virement', 500, 1, '2016-02-03 10:30:38', NULL, 33),
(8, 11, 'virement', 200, 1, '2016-02-03 10:38:34', NULL, 36),
(9, 10, 'virement', -14, 1, '2016-02-03 10:59:00', NULL, 47),
(10, 20, 'virement', 1561.6, 1, '2016-02-04 18:58:14', NULL, 50),
(11, 9, 'virement', -500, 1, '2016-02-04 18:58:56', NULL, 60),
(12, 21, 'virement', 2137.6, 1, '2016-02-04 19:00:10', NULL, 55),
(13, 20, 'virement', 5555555, 1, '2016-02-04 19:16:55', NULL, 63),
(27, 15, 'virement', 23, 1, '2016-02-12 13:17:29', NULL, 66),
(28, 15, 'virement', 200, 1, '2016-02-17 11:03:29', NULL, 70),
(29, 63, 'virement', 30, 1, '2016-02-17 11:25:12', NULL, 74),
(30, 64, 'virement', 24, 1, '2016-02-17 13:56:07', NULL, 78),
(31, 63, 'virement', -30, 1, '2016-02-17 13:59:11', NULL, 86),
(32, 64, 'virement', 444, 1, '2016-02-17 14:00:08', NULL, 82),
(33, 65, 'virement', 25, 1, '2016-02-17 15:23:06', NULL, 90),
(34, 41, 'virement', 40, 1, '2016-02-17 16:41:38', NULL, 94),
(35, 68, 'virement', 44, 1, '2016-02-18 09:29:26', NULL, 98),
(36, 22, 'virement', 1561.6, 1, '2016-11-25 17:18:36', NULL, 104),
(37, 71, 'virement', 2137.6, 1, '2016-11-25 18:10:25', NULL, 109),
(39, 22, 'virement', 1561.6, 1, '2015-04-01 17:56:18', NULL, 114),
(40, 73, 'virement', 1561.6, 1, '2015-04-01 18:00:06', NULL, 119),
(41, 74, 'virement', 1561.6, 1, '2015-04-01 10:28:36', NULL, 124),
(42, 75, 'virement', 1561.6, 1, '2015-04-01 10:31:03', NULL, 129),
(44, 76, 'virement', 1561.6, 1, '2015-04-01 10:33:12', NULL, 134),
(45, 77, 'virement', 1561.6, 1, '2015-04-01 10:36:20', NULL, 139),
(46, 78, 'virement', 1561.6, 1, '2015-04-01 10:41:39', NULL, 144),
(47, 79, 'virement', 1561.6, 1, '2015-04-01 10:43:29', NULL, 149),
(48, 80, 'virement', 1561.6, 1, '2015-04-01 10:57:06', NULL, 154),
(49, 81, 'virement', 1561.6, 1, '2015-04-01 10:59:11', NULL, 159),
(50, 82, 'virement', 2137.6, 1, '2015-04-01 11:04:55', NULL, 164),
(51, 83, 'virement', 1561.6, 1, '2015-04-01 11:18:12', NULL, 169),
(52, 83, 'virement', 1561.6, 1, '2015-04-01 11:19:03', NULL, 174),
(53, 77, 'virement', 1561.6, 1, '2015-04-01 11:19:43', NULL, 179),
(55, 75, 'virement', -1000, 1, '2016-02-27 19:24:37', NULL, 196),
(56, 88, 'virement', 44, 1, '2016-02-27 23:26:21', NULL, 199),
(57, 86, 'virement', 2137.6, 1, '2014-04-01 09:36:26', NULL, 203),
(60, 89, 'virement', 1561.6, 1, '2016-01-01 09:49:43', NULL, 208),
(61, 71, 'virement', 2137.6, 1, '2018-06-01 14:27:08', NULL, 213),
(62, 73, 'virement', 1561.6, 1, '2018-06-01 14:27:29', NULL, 218),
(63, 74, 'virement', 1561.6, 1, '2018-06-01 14:27:46', NULL, 223),
(64, 12, 'virement', 500, 0, '2018-06-01 14:47:35', NULL, NULL),
(65, 90, 'virement', 1561.6, 0, '2014-01-01 18:05:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `uploaded_file_id` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `created_date` datetime NOT NULL,
  `date_validate` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `settlement_abonnement_id` int(11) DEFAULT NULL,
  `easy_setting_id` int(11) DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sender_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sender_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `landing_url` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `old_program_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F1496545EA750E8` (`label`),
  UNIQUE KEY `UNIQ_F14965456DCD8F7E` (`settlement_abonnement_id`),
  UNIQUE KEY `UNIQ_F1496545EF9CC119` (`easy_setting_id`),
  UNIQUE KEY `UNIQ_F14965452576E0FD` (`contract_id`),
  UNIQUE KEY `UNIQ_F1496545829B3054` (`old_program_id`),
  KEY `IDX_F1496545276973A0` (`uploaded_file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=34 ;

--
-- Contenu de la table `programs`
--

INSERT INTO `programs` (`id`, `label`, `status`, `contract_id`, `uploaded_file_id`, `description`, `created_date`, `date_validate`, `date_expiration`, `settlement_abonnement_id`, `easy_setting_id`, `api_key`, `sender_name`, `sender_email`, `landing_url`, `old_program_id`) VALUES
(1, 'Cooperons Plus', 'prod', 11, NULL, '', '2015-02-19 00:00:00', NULL, '2016-10-01 16:51:46', NULL, NULL, 'NGJjZGQ3YjA1NzJhMDFkNjQ0NzgwOGQwMDJlMTVlYWYyNmY4NTQwNQ', 'COOPERONS PLUS !', 'service@test.com', NULL, NULL),
(4, 'Cooperons AE', 'prod', 12, NULL, '', '2015-02-19 00:00:00', NULL, '2016-10-01 16:51:46', NULL, NULL, 'ZGJjZGQ3gjA1NzJhMDFkNjl0NzgwOGQwM4JlMTVlYWYyNm54NTQwNQ', 'Coopérons !', 'service@test.com', NULL, NULL),
(5, 'Edatis', 'cancel', 13, 1, 'Le Promoteur fournit des services de production Web aux entreprises.\r\nDans le cadre du Programme, Vous pouvez informer le Promoteur si une entreprise souhaite bénéficier de ses services.', '2014-01-01 10:22:13', NULL, NULL, NULL, NULL, 'MzgyZDY5Mjg0MmNjZTIzYjdjMWJlYTBmZTg3NTQ1NmE5MmQ4MDA0Mw', 'Prospect TEST', 'prospect@test.com', 'www.edatis.com', NULL),
(7, 'blocs', 'cancel', 14, 2, 'Dans le cadre du Programme blocs, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\r\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\r\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\r\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-02-02 16:14:40', NULL, NULL, NULL, 1, 'OGI1ZWZiMWUwZDUzNjIyMTI2Njg1YjJiMzY1MzQ5MmY5ZjEzYzdhOA', 'Member TEST', 'member@test.com', NULL, NULL),
(8, 'arbre', 'standby', 23, 4, 'Dans le cadre du Programme arbre, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-02-03 14:28:25', NULL, NULL, NULL, 2, 'MTNjOGJlNDc2NjViZmJjZjlmYjNhMTliZTY3OGM0ZWZlNjgxZTdiYg', 'Samir GUELBI', 'sg@test.com', NULL, NULL),
(10, 'gggg', 'prod', 25, 6, 'Dans le cadre du Programme gggg, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\r\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\r\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\r\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-02-04 18:58:02', '2016-02-08 09:56:51', '2019-02-04 00:00:00', 40, 4, 'NzkxMGUzNWE3NmJkMDI5YWNiNTk0ODYzNzQ1NjEyZDMzYzYxNWRkNA', 'Member TEST', 'member@test.com', NULL, NULL),
(11, 'capture02', 'cancel', 26, 7, 'fgdfgdgdgdg', '2016-02-04 18:59:45', NULL, NULL, NULL, NULL, 'MGNlODMyMjY0ODJhYTllZDc2MzFlZGE5YTE1YmVmNmU1YWJiMGI4Mg', 'Member TEST', 'member@test.com', 'http://front.cooperons/program/11/configuration', NULL),
(15, 'ererre', 'prod', 27, 8, 'Dans le cadre du Programme ererre, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\r\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\r\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\r\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-02-08 07:57:28', '2015-04-01 17:57:18', '2019-04-01 00:00:00', 70, 9, 'ZWNkNDNjMTE4ZTQyMmU4NDEzZjEzMTYxYjBiZTliMGJmNGMxNjA1Zg', 'Member TEST', 'member@test.com', NULL, NULL),
(16, 'Api', 'prod', 96, 10, 'ffffff', '2016-11-25 17:42:24', '2015-04-01 10:27:22', '2018-11-25 00:00:00', 43, NULL, 'ZDlhNDIxN2MwZDE2MDVkNWMxNTMxNGY4ZDg4MDdhYTRjMzgwZmM3NA', 'Member TEST', 'member@test.com', 'http://ae.cooperons/program/16/configuration', NULL),
(17, 'Test', 'prod', 98, 11, 'Dans le cadre du Programme Test, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\r\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\r\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\r\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-11-25 12:30:55', '2015-04-01 18:01:00', '2019-04-01 00:00:00', 68, 8, 'MzhlYmE3ZTg0NjY1N2E3YmNkNDI2ZTFmNDllMmRmNWEzMGZmMzc3OA', 'Member TEST', 'member@test.com', NULL, NULL),
(19, 'Testv2122', 'prod', 99, 14, 'Dans le cadre du Programme Testv2122, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:28:25', '2015-04-01 10:29:23', '2019-04-01 00:00:00', 67, 10, 'Mzk3NmFhNTA2NGRkODgyMWM5ODc1MWMwYjZkODJhNDA5ZDIzYTJmNg', 'Member TEST', 'member@test.com', NULL, NULL),
(20, 'rrrrrrrrrrrr', 'cancel', 100, 16, 'Dans le cadre du Programme rrrrrrrrrrrr, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:30:54', NULL, NULL, NULL, 11, 'NDZjYTlhYTU1Zjg0ZTc4YTFhY2Q1M2FmNTEzN2Q2MDdlZjRkYTcwMg', 'Member TEST', 'member@test.com', NULL, NULL),
(21, 'aaaaaaaaaaaaaaaa', 'prod', 101, 18, 'Dans le cadre du Programme aaaaaaaaaaaaaaaa, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:32:43', '2015-04-01 10:33:59', '2017-04-01 00:00:00', 66, 12, 'Y2Y1MWE1MDcxMDEwOTNmMTc1ZWU0MmYzZTRhMjkwNzJjNTNhNzc4ZQ', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(22, 'aaaaaaaaa', 'prod', 102, 20, 'Dans le cadre du Programme aaaaaaaaa, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:36:11', '2015-04-01 11:20:03', '2019-04-01 00:00:00', 64, 20, 'MDQ1ZTVkN2M3ZmRkNzc2N2ZlNDE1NDgyNzQxMDgxY2U5OTFiNWYwZg', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(23, 'ssssssssss', 'prod', 103, 22, 'Dans le cadre du Programme ssssssssss, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:41:30', '2015-04-01 10:42:17', '2017-04-01 00:00:00', 61, 14, 'NTg3NjQ3MmNiZGU5MzNlMGIxNjlkMmIzN2JjNmFiODUwODE1ODkxNg', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(24, 'aaaaaaaaaaaaaaa', 'prod', 104, 24, 'Dans le cadre du Programme aaaaaaaaaaaaaaa, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:43:17', '2015-04-01 10:44:07', '2017-04-01 00:00:00', 59, 15, 'NWM0ZTU1ZDgyYmQ1YmZiOTM2NzVhZTQxOTkyYmM5ZTAxYzJmMTllYw', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(25, 'ssssssssssss', 'prod', 105, 26, 'Dans le cadre du Programme ssssssssssss, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:55:10', '2015-04-01 10:58:09', '2017-04-01 00:00:00', 57, 16, 'OTFmNTFlOGViNDk0YmM2YTRhOGU1M2VkNjc5ZjUzYTIxNmZiODQ2Nw', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(26, 'fffffffffffff', 'prod', 106, 28, 'Dans le cadre du Programme fffffffffffff, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 10:59:00', '2015-04-01 10:59:58', '2017-04-01 00:00:00', 55, 17, 'NzYyMWQ5YWE1MmNjOWNlZDhhMmQzODIxY2JjMzM0MTkzZjkwZmM1YQ', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(27, 'aaaaaaaaaaa', 'prod', 107, 30, 'aaaaaaaa', '2015-04-01 11:04:40', '2015-04-01 11:17:52', '2016-04-01 00:00:00', 53, NULL, 'M2MwNDU0ZWFmN2E4OTEyODc4MjM1YTIwMTQ3ZTM5YTZlOWIxMTJlYw', 'Prospect TEST', 'prospect@test.com', 'http://front.cooperons/program/27/configuration', NULL),
(28, 'aaaaaaaaaa', 'prod', 108, 31, 'Dans le cadre du Programme aaaaaaaaaa, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2015-04-01 11:18:03', '2015-04-01 11:19:25', '2019-04-01 00:00:00', 52, 19, 'ZmM4MGNjYjEwNmFhMmFhNzRlOGM2M2ZlZWJhNTgxYzZmNTdiNzNmNQ', 'Prospect TEST', 'prospect@test.com', NULL, NULL),
(29, 'dddddddd', 'standby', 109, 33, 'Dans le cadre du Programme dddddddd, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2016-02-27 12:34:28', NULL, NULL, NULL, 21, 'YjBkYTA4MDA0NjQ1MDA2OGRkMTg1OTcyZDc4NzZkNjNhYjQ4ZDQzYg', 'Member TEST', 'member@test.com', NULL, NULL),
(30, 'Testtt Api', 'preprod', 110, 34, '8888888888888888888888888888888888', '2014-04-01 14:19:17', NULL, '2019-06-01 00:00:00', 48, NULL, 'NGFkNDllYWM1OGNmZmZiNDIzY2Y1ZDRkZTM3MzEyOGU0OTQ3ODZkMQ', 'Member TEST', 'member@test.com', NULL, NULL),
(32, 'tn', 'preprod', 113, 35, 'Dans le cadre du Programme tn, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\r\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\r\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\r\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2014-04-01 09:37:08', NULL, '2019-06-01 00:00:00', 49, 23, 'YTBkY2U1NTQ5YTNmNGRiMjljOWQ5NmI1YzQyNDk1ZGE4Yjg4NTU0Nw', 'Member TEST', 'member@test.com', NULL, NULL),
(33, 'Test 23', 'standby', 114, 37, 'Dans le cadre du Programme Test 23, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>"Nouvelle Affaire"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>"le Prospect"</b>).</br></br>\n        Lorsqu''une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>"Montant Maximum"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>"Réglement Commissionné"</b>).</br></br>\n        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l''ensemble des Réglements Commissionés d''une même Nouvelle Affaire, du Montant Maximum.</br></br>\n        Le Promoteur se réserve le droit d''annuler à tout moment une Nouvelle Affaire, quel que soit son stade d''avancement, notamment en cas de manque d''intérêt de la part du Prospect pour l''offre du Promoteur ou de non paiement par le Prospect d''une facture émise dans le cadre de ladite Nouvelle Affaire.', '2014-01-01 18:02:43', NULL, NULL, NULL, 24, 'N2M5MTM0MGIyMWNjNDljNTc2ZGI1NWNiODNkOWJiZmMwMDJlODY1Ng', 'DAF TEST', 'daf@test.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `records`
--

CREATE TABLE IF NOT EXISTS `records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_id` int(11) DEFAULT NULL,
  `debit_mandataire_id` int(11) DEFAULT NULL,
  `credit_mandataire_id` int(11) DEFAULT NULL,
  `debit_account_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `credit_account_ref` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `debit_account_id` int(11) DEFAULT NULL,
  `credit_account_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `created_date` datetime NOT NULL,
  `next_record_id` int(11) DEFAULT NULL,
  `first_record_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9C9D5846A77A03F` (`next_record_id`),
  KEY `IDX_9C9D5846B2D54BB3` (`debit_mandataire_id`),
  KEY `IDX_9C9D5846CE0F2397` (`credit_mandataire_id`),
  KEY `IDX_9C9D5846213C1059` (`party_id`),
  KEY `IDX_9C9D5846B1F0E542` (`first_record_id`),
  KEY `IDX_9C9D58462989F1FD` (`invoice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=260 ;

--
-- Contenu de la table `records`
--

INSERT INTO `records` (`id`, `party_id`, `debit_mandataire_id`, `credit_mandataire_id`, `debit_account_ref`, `credit_account_ref`, `debit_account_id`, `credit_account_id`, `amount`, `created_date`, `next_record_id`, `first_record_id`, `invoice_id`) VALUES
(1, 6, 7, NULL, '409', '100', 2, 1, 2137.6, '2014-01-01 11:21:35', 2, 1, NULL),
(2, 1, 6, 7, '517', '419', 2, 3, 2137.6, '2014-01-01 11:21:35', 3, 1, 1),
(3, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2014-01-01 11:21:35', NULL, 1, NULL),
(4, 6, 7, 7, '622', '409', 2, 2, 1137.6, '2014-01-01 11:21:35', 5, 4, 1),
(5, 1, 7, 7, '419', '706', 3, 3, 1137.6, '2014-01-01 11:21:35', NULL, 4, 1),
(6, 6, 7, 7, '622', '409', 2, 2, 900, '2014-01-01 16:59:42', 7, 6, 1),
(7, 1, 7, 7, '419', '706', 3, 3, 900, '2014-01-01 16:59:42', NULL, 6, 1),
(8, 6, 7, NULL, '409', '100', 2, 1, 1499.8, '2014-01-01 18:34:30', 9, 8, NULL),
(9, 1, 6, 7, '517', '419', 2, 3, 1499.8, '2014-01-01 18:34:30', 10, 8, 1),
(10, 5, NULL, 6, '100', '517', 1, 2, 1499.8, '2014-01-01 18:34:30', NULL, 8, NULL),
(11, 6, 7, 7, '622', '409', 2, 2, 499.8, '2014-01-01 18:34:30', 12, 11, 1),
(12, 1, 7, 7, '419', '706', 3, 3, 499.8, '2014-01-01 18:34:30', NULL, 11, 1),
(13, 6, 7, 7, '622', '409', 2, 2, 600, '2014-01-01 18:35:35', 14, 13, 1),
(14, 1, 7, 7, '419', '706', 3, 3, 600, '2014-01-01 18:35:35', NULL, 13, 1),
(15, 6, 7, NULL, '409', '100', 2, 1, 2137.6, '2015-01-01 18:44:08', 16, 15, NULL),
(16, 1, 6, 7, '517', '419', 2, 3, 2137.6, '2015-01-01 18:44:08', 17, 15, 1),
(17, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2015-01-01 18:44:08', NULL, 15, NULL),
(18, 6, 7, 7, '622', '409', 2, 2, 1137.6, '2015-01-01 18:44:08', 19, 18, 1),
(19, 1, 7, 7, '419', '706', 3, 3, 1137.6, '2015-01-01 18:44:08', NULL, 18, 1),
(20, 6, 7, NULL, '409', '100', 2, 1, 5137.6, '2015-04-01 14:00:14', 21, 20, NULL),
(21, 1, 6, 7, '517', '419', 2, 3, 5137.6, '2015-04-01 14:00:14', 22, 20, 1),
(22, 5, NULL, 6, '100', '517', 1, 2, 5137.6, '2015-04-01 14:00:14', NULL, 20, NULL),
(23, 6, 7, 7, '622', '409', 2, 2, 1137.6, '2015-04-01 14:00:14', 24, 23, 1),
(24, 1, 7, 7, '419', '706', 3, 3, 1137.6, '2015-04-01 14:00:14', NULL, 23, 1),
(25, 6, NULL, 7, '100', '409', 1, 2, 5500, '2015-04-01 14:01:42', 26, 25, NULL),
(26, 1, 7, 6, '419', '517', 3, 2, 5500, '2015-04-01 14:01:42', 27, 25, 1),
(27, 5, 6, NULL, '517', '100', 2, 1, 5500, '2015-04-01 14:01:42', NULL, 25, NULL),
(28, 8, 8, NULL, '409', '100', 2, 1, 1561.6, '2016-02-02 16:15:10', 29, 28, NULL),
(29, 1, 6, 8, '517', '419', 2, 4, 1561.6, '2016-02-02 16:15:10', 30, 28, NULL),
(30, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2016-02-02 16:15:10', NULL, 28, NULL),
(31, 8, 8, 8, '622', '409', 2, 2, 561.6, '2016-02-02 16:15:10', 32, 31, NULL),
(32, 1, 8, 8, '419', '706', 4, 4, 561.6, '2016-02-02 16:15:10', NULL, 31, NULL),
(33, 9, 9, NULL, '517', '100', 2, 1, 500, '2016-02-03 10:30:57', 34, 33, NULL),
(34, 1, 6, 9, '517', '517', 2, 5, 500, '2016-02-03 10:30:57', 35, 33, NULL),
(35, 5, NULL, 6, '100', '517', 1, 2, 500, '2016-02-03 10:30:57', NULL, 33, NULL),
(36, 8, 11, NULL, '409', '100', 3, 1, 200, '2016-02-03 10:39:55', 37, 36, NULL),
(37, 10, 10, 11, '517', '419', 2, 3, 200, '2016-02-03 10:39:55', 38, 36, NULL),
(38, 1, 6, 10, '517', '517', 2, 6, 200, '2016-02-03 10:39:55', 39, 36, NULL),
(39, 5, NULL, 6, '100', '517', 1, 2, 200, '2016-02-03 10:39:55', NULL, 36, NULL),
(40, 8, 11, 11, '622', '409', 3, 3, 20, '2016-02-03 10:39:56', 41, 40, NULL),
(41, 10, 11, 11, '419', '706', 3, 3, 20, '2016-02-03 10:39:56', NULL, 40, NULL),
(42, 10, 11, 10, '419', '517', 3, 2, 0.66, '2016-02-03 10:39:56', 43, 42, NULL),
(43, 1, 10, 12, '517', '517', 6, 7, 0.66, '2016-02-03 10:39:56', 44, 42, NULL),
(44, 8, 12, 11, '517', '409', 4, 3, 0.66, '2016-02-03 10:39:56', NULL, 42, NULL),
(45, 8, 12, 12, '622', '517', 4, 4, 0.66, '2016-02-03 10:39:56', 46, 45, NULL),
(46, 1, 12, 12, '517', '706', 7, 7, 0.66, '2016-02-03 10:39:56', NULL, 45, NULL),
(47, 10, NULL, 10, '100', '517', 1, 2, 14, '2016-02-03 10:59:29', 48, 47, NULL),
(48, 1, 10, 6, '517', '517', 6, 2, 14, '2016-02-03 10:59:29', 49, 47, NULL),
(49, 5, 6, NULL, '517', '100', 2, 1, 14, '2016-02-03 10:59:29', NULL, 47, NULL),
(50, 8, 20, NULL, '409', '100', 10, 1, 1561.6, '2016-02-04 18:58:28', 51, 50, NULL),
(51, 1, 6, 20, '517', '419', 2, 12, 1561.6, '2016-02-04 18:58:28', 52, 50, NULL),
(52, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2016-02-04 18:58:28', NULL, 50, NULL),
(53, 8, 20, 20, '622', '409', 10, 10, 561.6, '2016-02-04 18:58:28', 54, 53, NULL),
(54, 1, 20, 20, '419', '706', 12, 12, 561.6, '2016-02-04 18:58:28', NULL, 53, NULL),
(55, 8, 21, NULL, '409', '100', 11, 1, 2137.6, '2016-02-04 19:00:24', 56, 55, NULL),
(56, 1, 6, 21, '517', '419', 2, 13, 2137.6, '2016-02-04 19:00:24', 57, 55, NULL),
(57, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2016-02-04 19:00:24', NULL, 55, NULL),
(58, 8, 21, 21, '622', '409', 11, 11, 1137.6, '2016-02-04 19:00:24', 59, 58, NULL),
(59, 1, 21, 21, '419', '706', 13, 13, 1137.6, '2016-02-04 19:00:24', NULL, 58, NULL),
(60, 9, NULL, 9, '100', '517', 1, 2, 500, '2016-02-04 19:00:32', 61, 60, NULL),
(61, 1, 9, 6, '517', '517', 5, 2, 500, '2016-02-04 19:00:32', 62, 60, NULL),
(62, 5, 6, NULL, '517', '100', 2, 1, 500, '2016-02-04 19:00:32', NULL, 60, NULL),
(63, 8, 20, NULL, '409', '100', 10, 1, 5555555, '2016-02-04 19:17:13', 64, 63, NULL),
(64, 1, 6, 20, '517', '419', 2, 12, 5555555, '2016-02-04 19:17:13', 65, 63, NULL),
(65, 5, NULL, 6, '100', '517', 1, 2, 5555555, '2016-02-04 19:17:13', NULL, 63, NULL),
(66, 8, 15, NULL, '409', '100', 6, 1, 23, '2016-02-12 13:18:01', 67, 66, NULL),
(67, 9, 9, 15, '517', '419', 2, 3, 23, '2016-02-12 13:18:01', 68, 66, NULL),
(68, 1, 6, 9, '517', '517', 2, 5, 23, '2016-02-12 13:18:01', 69, 66, NULL),
(69, 5, NULL, 6, '100', '517', 1, 2, 23, '2016-02-12 13:18:01', NULL, 66, NULL),
(70, 8, 15, NULL, '409', '100', 6, 1, 200, '2016-02-17 11:03:38', 71, 70, NULL),
(71, 9, 9, 15, '517', '419', 2, 3, 200, '2016-02-17 11:03:38', 72, 70, NULL),
(72, 1, 6, 9, '517', '517', 2, 5, 200, '2016-02-17 11:03:38', 73, 70, NULL),
(73, 5, NULL, 6, '100', '517', 1, 2, 200, '2016-02-17 11:03:38', NULL, 70, NULL),
(74, 8, 63, NULL, '409', '100', 19, 1, 30, '2016-02-17 11:25:26', 75, 74, NULL),
(75, 9, 9, 63, '517', '419', 2, 35, 30, '2016-02-17 11:25:26', 76, 74, NULL),
(76, 1, 6, 9, '517', '517', 2, 5, 30, '2016-02-17 11:25:26', 77, 74, NULL),
(77, 5, NULL, 6, '100', '517', 1, 2, 30, '2016-02-17 11:25:26', NULL, 74, NULL),
(78, 8, 64, NULL, '409', '100', 20, 1, 24, '2016-02-17 13:56:21', 79, 78, NULL),
(79, 9, 9, 64, '517', '419', 2, 36, 24, '2016-02-17 13:56:21', 80, 78, NULL),
(80, 1, 6, 9, '517', '517', 2, 5, 24, '2016-02-17 13:56:21', 81, 78, NULL),
(81, 5, NULL, 6, '100', '517', 1, 2, 24, '2016-02-17 13:56:21', NULL, 78, NULL),
(82, 8, 64, NULL, '409', '100', 20, 1, 444, '2016-02-17 14:00:18', 83, 82, NULL),
(83, 9, 9, 64, '517', '419', 2, 36, 444, '2016-02-17 14:00:18', 84, 82, NULL),
(84, 1, 6, 9, '517', '517', 2, 5, 444, '2016-02-17 14:00:18', 85, 82, NULL),
(85, 5, NULL, 6, '100', '517', 1, 2, 444, '2016-02-17 14:00:18', NULL, 82, NULL),
(86, 8, NULL, 63, '100', '409', 1, 19, 30, '2016-02-17 14:00:18', 87, 86, NULL),
(87, 9, 63, 9, '419', '517', 35, 2, 30, '2016-02-17 14:00:18', 88, 86, NULL),
(88, 1, 9, 6, '517', '517', 5, 2, 30, '2016-02-17 14:00:18', 89, 86, NULL),
(89, 5, 6, NULL, '517', '100', 2, 1, 30, '2016-02-17 14:00:18', NULL, 86, NULL),
(90, 13, 65, NULL, '409', '100', 6, 1, 25, '2016-02-17 15:23:13', 91, 90, NULL),
(91, 17, 59, 65, '517', '419', 2, 3, 25, '2016-02-17 15:23:13', 92, 90, NULL),
(92, 1, 6, 59, '517', '517', 2, 17, 25, '2016-02-17 15:23:13', 93, 90, NULL),
(93, 5, NULL, 6, '100', '517', 1, 2, 25, '2016-02-17 15:23:13', NULL, 90, NULL),
(94, 13, 41, NULL, '409', '100', 2, 1, 40, '2016-02-17 16:41:52', 95, 94, NULL),
(95, 11, 13, 41, '517', '419', 2, 16, 40, '2016-02-17 16:41:52', 96, 94, NULL),
(96, 1, 6, 13, '517', '517', 2, 8, 40, '2016-02-17 16:41:52', 97, 94, NULL),
(97, 5, NULL, 6, '100', '517', 1, 2, 40, '2016-02-17 16:41:52', NULL, 94, NULL),
(98, 18, 68, NULL, '409', '100', 2, 1, 44, '2016-02-18 09:29:36', 99, 98, NULL),
(99, 9, 9, 68, '517', '419', 2, 37, 44, '2016-02-18 09:29:36', 100, 98, NULL),
(100, 1, 6, 9, '517', '517', 2, 5, 44, '2016-02-18 09:29:36', 101, 98, NULL),
(101, 5, NULL, 6, '100', '517', 1, 2, 44, '2016-02-18 09:29:36', NULL, 98, NULL),
(102, 18, 68, 68, '622', '409', 2, 2, 30, '2016-11-25 16:51:06', 103, 102, NULL),
(103, 9, 68, 68, '419', '706', 37, 37, 30, '2016-11-25 16:51:06', NULL, 102, NULL),
(104, 8, 22, NULL, '409', '100', 12, 1, 1561.6, '2016-11-25 18:08:40', 105, 104, NULL),
(105, 1, 6, 22, '517', '419', 2, 14, 1561.6, '2016-11-25 18:08:40', 106, 104, NULL),
(106, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2016-11-25 18:08:40', NULL, 104, NULL),
(107, 8, 22, 22, '622', '409', 12, 12, 561.6, '2016-11-25 18:08:40', 108, 107, NULL),
(108, 1, 22, 22, '419', '706', 14, 14, 561.6, '2016-11-25 18:08:40', NULL, 107, NULL),
(109, 8, 71, NULL, '409', '100', 21, 1, 2137.6, '2016-11-25 18:10:33', 110, 109, NULL),
(110, 1, 6, 71, '517', '419', 2, 20, 2137.6, '2016-11-25 18:10:33', 111, 109, NULL),
(111, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2016-11-25 18:10:33', NULL, 109, NULL),
(112, 8, 71, 71, '622', '409', 21, 21, 1137.6, '2016-11-25 18:10:33', 113, 112, NULL),
(113, 1, 71, 71, '419', '706', 20, 20, 1137.6, '2016-11-25 18:10:33', NULL, 112, NULL),
(114, 8, 22, NULL, '409', '100', 12, 1, 1561.6, '2015-04-01 17:56:44', 115, 114, NULL),
(115, 1, 6, 22, '517', '419', 2, 14, 1561.6, '2015-04-01 17:56:44', 116, 114, NULL),
(116, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 17:56:44', NULL, 114, NULL),
(117, 8, 22, 22, '622', '409', 12, 12, 561.6, '2015-04-01 17:56:44', 118, 117, NULL),
(118, 1, 22, 22, '419', '706', 14, 14, 561.6, '2015-04-01 17:56:44', NULL, 117, NULL),
(119, 8, 73, NULL, '409', '100', 22, 1, 1561.6, '2015-04-01 18:00:14', 120, 119, NULL),
(120, 1, 6, 73, '517', '419', 2, 22, 1561.6, '2015-04-01 18:00:14', 121, 119, NULL),
(121, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 18:00:14', NULL, 119, NULL),
(122, 8, 73, 73, '622', '409', 22, 22, 561.6, '2015-04-01 18:00:14', 123, 122, NULL),
(123, 1, 73, 73, '419', '706', 22, 22, 561.6, '2015-04-01 18:00:14', NULL, 122, NULL),
(124, 8, 74, NULL, '409', '100', 23, 1, 1561.6, '2015-04-01 10:28:46', 125, 124, NULL),
(125, 1, 6, 74, '517', '419', 2, 23, 1561.6, '2015-04-01 10:28:46', 126, 124, NULL),
(126, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:28:46', NULL, 124, NULL),
(127, 8, 74, 74, '622', '409', 23, 23, 561.6, '2015-04-01 10:28:46', 128, 127, NULL),
(128, 1, 74, 74, '419', '706', 23, 23, 561.6, '2015-04-01 10:28:46', NULL, 127, NULL),
(129, 8, 75, NULL, '409', '100', 24, 1, 1561.6, '2015-04-01 10:31:11', 130, 129, NULL),
(130, 1, 6, 75, '517', '419', 2, 24, 1561.6, '2015-04-01 10:31:11', 131, 129, NULL),
(131, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:31:11', NULL, 129, NULL),
(132, 8, 75, 75, '622', '409', 24, 24, 561.6, '2015-04-01 10:31:12', 133, 132, NULL),
(133, 1, 75, 75, '419', '706', 24, 24, 561.6, '2015-04-01 10:31:12', NULL, 132, NULL),
(134, 6, 76, NULL, '409', '100', 3, 1, 1561.6, '2015-04-01 10:33:30', 135, 134, NULL),
(135, 1, 6, 76, '517', '419', 2, 25, 1561.6, '2015-04-01 10:33:30', 136, 134, NULL),
(136, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:33:30', NULL, 134, NULL),
(137, 6, 76, 76, '622', '409', 3, 3, 561.6, '2015-04-01 10:33:30', 138, 137, NULL),
(138, 1, 76, 76, '419', '706', 25, 25, 561.6, '2015-04-01 10:33:30', NULL, 137, NULL),
(139, 6, 77, NULL, '409', '100', 4, 1, 1561.6, '2015-04-01 10:36:33', 140, 139, NULL),
(140, 1, 6, 77, '517', '419', 2, 26, 1561.6, '2015-04-01 10:36:33', 141, 139, NULL),
(141, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:36:33', NULL, 139, NULL),
(142, 6, 77, 77, '622', '409', 4, 4, 561.6, '2015-04-01 10:36:33', 143, 142, NULL),
(143, 1, 77, 77, '419', '706', 26, 26, 561.6, '2015-04-01 10:36:33', NULL, 142, NULL),
(144, 6, 78, NULL, '409', '100', 5, 1, 1561.6, '2015-04-01 10:41:48', 145, 144, NULL),
(145, 1, 6, 78, '517', '419', 2, 27, 1561.6, '2015-04-01 10:41:48', 146, 144, NULL),
(146, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:41:48', NULL, 144, NULL),
(147, 6, 78, 78, '622', '409', 5, 5, 561.6, '2015-04-01 10:41:49', 148, 147, NULL),
(148, 1, 78, 78, '419', '706', 27, 27, 561.6, '2015-04-01 10:41:49', NULL, 147, NULL),
(149, 6, 79, NULL, '409', '100', 6, 1, 1561.6, '2015-04-01 10:43:37', 150, 149, NULL),
(150, 1, 6, 79, '517', '419', 2, 28, 1561.6, '2015-04-01 10:43:37', 151, 149, NULL),
(151, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:43:37', NULL, 149, NULL),
(152, 6, 79, 79, '622', '409', 6, 6, 561.6, '2015-04-01 10:43:37', 153, 152, NULL),
(153, 1, 79, 79, '419', '706', 28, 28, 561.6, '2015-04-01 10:43:37', NULL, 152, NULL),
(154, 6, 80, NULL, '409', '100', 7, 1, 1561.6, '2015-04-01 10:57:14', 155, 154, NULL),
(155, 1, 6, 80, '517', '419', 2, 29, 1561.6, '2015-04-01 10:57:14', 156, 154, NULL),
(156, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:57:14', NULL, 154, NULL),
(157, 6, 80, 80, '622', '409', 7, 7, 561.6, '2015-04-01 10:57:14', 158, 157, NULL),
(158, 1, 80, 80, '419', '706', 29, 29, 561.6, '2015-04-01 10:57:14', NULL, 157, NULL),
(159, 6, 81, NULL, '409', '100', 8, 1, 1561.6, '2015-04-01 10:59:20', 160, 159, NULL),
(160, 1, 6, 81, '517', '419', 2, 30, 1561.6, '2015-04-01 10:59:20', 161, 159, NULL),
(161, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 10:59:20', NULL, 159, NULL),
(162, 6, 81, 81, '622', '409', 8, 8, 561.6, '2015-04-01 10:59:20', 163, 162, NULL),
(163, 1, 81, 81, '419', '706', 30, 30, 561.6, '2015-04-01 10:59:20', NULL, 162, NULL),
(164, 6, 82, NULL, '409', '100', 9, 1, 2137.6, '2015-04-01 11:05:01', 165, 164, NULL),
(165, 1, 6, 82, '517', '419', 2, 31, 2137.6, '2015-04-01 11:05:01', 166, 164, NULL),
(166, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2015-04-01 11:05:01', NULL, 164, NULL),
(167, 6, 82, 82, '622', '409', 9, 9, 1137.6, '2015-04-01 11:05:01', 168, 167, NULL),
(168, 1, 82, 82, '419', '706', 31, 31, 1137.6, '2015-04-01 11:05:01', NULL, 167, NULL),
(169, 6, 83, NULL, '409', '100', 10, 1, 1561.6, '2015-04-01 11:18:25', 170, 169, NULL),
(170, 1, 6, 83, '517', '419', 2, 32, 1561.6, '2015-04-01 11:18:25', 171, 169, NULL),
(171, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 11:18:25', NULL, 169, NULL),
(172, 6, 83, 83, '622', '409', 10, 10, 561.6, '2015-04-01 11:18:25', 173, 172, NULL),
(173, 1, 83, 83, '419', '706', 32, 32, 561.6, '2015-04-01 11:18:25', NULL, 172, NULL),
(174, 6, 83, NULL, '409', '100', 10, 1, 1561.6, '2015-04-01 11:19:13', 175, 174, NULL),
(175, 1, 6, 83, '517', '419', 2, 32, 1561.6, '2015-04-01 11:19:13', 176, 174, NULL),
(176, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 11:19:13', NULL, 174, NULL),
(177, 6, 83, 83, '622', '409', 10, 10, 561.6, '2015-04-01 11:19:14', 178, 177, NULL),
(178, 1, 83, 83, '419', '706', 32, 32, 561.6, '2015-04-01 11:19:14', NULL, 177, NULL),
(179, 6, 77, NULL, '409', '100', 4, 1, 1561.6, '2015-04-01 11:19:53', 180, 179, NULL),
(180, 1, 6, 77, '517', '419', 2, 26, 1561.6, '2015-04-01 11:19:53', 181, 179, NULL),
(181, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2015-04-01 11:19:53', NULL, 179, NULL),
(182, 6, 77, 77, '622', '409', 4, 4, 561.6, '2015-04-01 11:19:53', 183, 182, NULL),
(183, 1, 77, 77, '419', '706', 26, 26, 561.6, '2015-04-01 11:19:53', NULL, 182, NULL),
(184, 8, 8, 8, '622', '409', 2, 2, 561.6, '2018-06-01 14:16:10', 185, 184, NULL),
(185, 1, 8, 8, '419', '706', 4, 4, 561.6, '2018-06-01 14:16:10', NULL, 184, NULL),
(186, 8, 20, 20, '622', '409', 10, 10, 561.6, '2018-06-01 14:17:10', 187, 186, NULL),
(187, 1, 20, 20, '419', '706', 12, 12, 561.6, '2018-06-01 14:17:10', NULL, 186, NULL),
(188, 8, 20, 20, '622', '409', 10, 10, 561.6, '2018-06-01 14:17:30', 189, 188, NULL),
(189, 1, 20, 20, '419', '706', 12, 12, 561.6, '2018-06-01 14:17:30', NULL, 188, NULL),
(190, 8, 22, 22, '622', '409', 12, 12, 561.6, '2018-06-01 14:24:09', 191, 190, NULL),
(191, 1, 22, 22, '419', '706', 14, 14, 561.6, '2018-06-01 14:24:09', NULL, 190, NULL),
(192, 8, 73, 73, '622', '409', 22, 22, 561.6, '2018-06-01 14:24:59', 193, 192, NULL),
(193, 1, 73, 73, '419', '706', 22, 22, 561.6, '2018-06-01 14:24:59', NULL, 192, NULL),
(194, 8, 74, 74, '622', '409', 23, 23, 561.6, '2018-06-01 14:25:34', 195, 194, NULL),
(195, 1, 74, 74, '419', '706', 23, 23, 561.6, '2018-06-01 14:25:34', NULL, 194, NULL),
(196, 8, NULL, 75, '100', '409', 1, 24, 1000, '2018-06-01 14:27:58', 197, 196, NULL),
(197, 1, 75, 6, '419', '517', 24, 2, 1000, '2018-06-01 14:27:58', 198, 196, NULL),
(198, 5, 6, NULL, '517', '100', 2, 1, 1000, '2018-06-01 14:27:58', NULL, 196, NULL),
(199, 18, 88, NULL, '409', '100', 2, 1, 44, '2018-06-01 14:27:58', 200, 199, NULL),
(200, 9, 9, 88, '517', '419', 2, 37, 44, '2018-06-01 14:27:58', 201, 199, NULL),
(201, 1, 6, 9, '517', '517', 2, 5, 44, '2018-06-01 14:27:58', 202, 199, NULL),
(202, 5, NULL, 6, '100', '517', 1, 2, 44, '2018-06-01 14:27:58', NULL, 199, NULL),
(203, 8, 86, NULL, '409', '100', 26, 1, 2137.6, '2018-06-01 14:27:58', 204, 203, NULL),
(204, 1, 6, 86, '517', '419', 2, 35, 2137.6, '2018-06-01 14:27:58', 205, 203, NULL),
(205, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2018-06-01 14:27:58', NULL, 203, NULL),
(206, 8, 86, 86, '622', '409', 26, 26, 1137.6, '2018-06-01 14:27:58', 207, 206, NULL),
(207, 1, 86, 86, '419', '706', 35, 35, 1137.6, '2018-06-01 14:27:58', NULL, 206, NULL),
(208, 8, 89, NULL, '409', '100', 27, 1, 1561.6, '2018-06-01 14:27:58', 209, 208, NULL),
(209, 1, 6, 89, '517', '419', 2, 36, 1561.6, '2018-06-01 14:27:58', 210, 208, NULL),
(210, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2018-06-01 14:27:58', NULL, 208, NULL),
(211, 8, 89, 89, '622', '409', 27, 27, 561.6, '2018-06-01 14:27:59', 212, 211, NULL),
(212, 1, 89, 89, '419', '706', 36, 36, 561.6, '2018-06-01 14:27:59', NULL, 211, NULL),
(213, 8, 71, NULL, '409', '100', 21, 1, 2137.6, '2018-06-01 14:27:59', 214, 213, NULL),
(214, 1, 6, 71, '517', '419', 2, 20, 2137.6, '2018-06-01 14:27:59', 215, 213, NULL),
(215, 5, NULL, 6, '100', '517', 1, 2, 2137.6, '2018-06-01 14:27:59', NULL, 213, NULL),
(216, 8, 71, 71, '622', '409', 21, 21, 1137.6, '2018-06-01 14:27:59', 217, 216, NULL),
(217, 1, 71, 71, '419', '706', 20, 20, 1137.6, '2018-06-01 14:27:59', NULL, 216, NULL),
(218, 8, 73, NULL, '409', '100', 22, 1, 1561.6, '2018-06-01 14:27:59', 219, 218, NULL),
(219, 1, 6, 73, '517', '419', 2, 22, 1561.6, '2018-06-01 14:27:59', 220, 218, NULL),
(220, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2018-06-01 14:27:59', NULL, 218, NULL),
(221, 8, 73, 73, '622', '409', 22, 22, 561.6, '2018-06-01 14:27:59', 222, 221, NULL),
(222, 1, 73, 73, '419', '706', 22, 22, 561.6, '2018-06-01 14:27:59', NULL, 221, NULL),
(223, 8, 74, NULL, '409', '100', 23, 1, 1561.6, '2018-06-01 14:27:59', 224, 223, NULL),
(224, 1, 6, 74, '517', '419', 2, 23, 1561.6, '2018-06-01 14:27:59', 225, 223, NULL),
(225, 5, NULL, 6, '100', '517', 1, 2, 1561.6, '2018-06-01 14:27:59', NULL, 223, NULL),
(226, 8, 74, 74, '622', '409', 23, 23, 561.6, '2018-06-01 14:27:59', 227, 226, NULL),
(227, 1, 74, 74, '419', '706', 23, 23, 561.6, '2018-06-01 14:27:59', NULL, 226, NULL),
(228, 6, 83, 83, '622', '409', 10, 10, 561.6, '2018-06-01 14:28:10', 229, 228, NULL),
(229, 1, 83, 83, '419', '706', 32, 32, 561.6, '2018-06-01 14:28:10', NULL, 228, NULL),
(230, 6, 83, 83, '622', '409', 10, 10, 561.6, '2018-06-01 14:28:19', 231, 230, NULL),
(231, 1, 83, 83, '419', '706', 32, 32, 561.6, '2018-06-01 14:28:19', NULL, 230, NULL),
(232, 6, 83, 83, '622', '409', 10, 10, 561.6, '2018-06-01 14:28:21', 233, 232, NULL),
(233, 1, 83, 83, '419', '706', 32, 32, 561.6, '2018-06-01 14:28:21', NULL, 232, NULL),
(234, 6, 81, 81, '622', '409', 8, 8, 561.6, '2018-06-01 14:30:45', 235, 234, NULL),
(235, 1, 81, 81, '419', '706', 30, 30, 561.6, '2018-06-01 14:30:45', NULL, 234, NULL),
(236, 6, 80, 80, '622', '409', 7, 7, 561.6, '2018-06-01 14:30:59', 237, 236, NULL),
(237, 1, 80, 80, '419', '706', 29, 29, 561.6, '2018-06-01 14:30:59', NULL, 236, NULL),
(238, 6, 79, 79, '622', '409', 6, 6, 561.6, '2018-06-01 14:31:10', 239, 238, NULL),
(239, 1, 79, 79, '419', '706', 28, 28, 561.6, '2018-06-01 14:31:10', NULL, 238, NULL),
(240, 6, 78, 78, '622', '409', 5, 5, 561.6, '2018-06-01 14:31:44', 241, 240, NULL),
(241, 1, 78, 78, '419', '706', 27, 27, 561.6, '2018-06-01 14:31:44', NULL, 240, NULL),
(242, 6, 77, 77, '622', '409', 4, 4, 561.6, '2018-06-01 14:31:50', 243, 242, NULL),
(243, 1, 77, 77, '419', '706', 26, 26, 561.6, '2018-06-01 14:31:50', NULL, 242, NULL),
(244, 6, 77, 77, '622', '409', 4, 4, 561.6, '2018-06-01 14:31:52', 245, 244, NULL),
(245, 1, 77, 77, '419', '706', 26, 26, 561.6, '2018-06-01 14:31:52', NULL, 244, NULL),
(246, 6, 77, 77, '622', '409', 4, 4, 561.6, '2018-06-01 14:31:53', 247, 246, NULL),
(247, 1, 77, 77, '419', '706', 26, 26, 561.6, '2018-06-01 14:31:53', NULL, 246, NULL),
(248, 6, 76, 76, '622', '409', 3, 3, 561.6, '2018-06-01 14:32:04', 249, 248, NULL),
(249, 1, 76, 76, '419', '706', 25, 25, 561.6, '2018-06-01 14:32:04', NULL, 248, NULL),
(250, 8, 74, 74, '622', '409', 23, 23, 561.6, '2018-06-01 14:32:34', 251, 250, NULL),
(251, 1, 74, 74, '419', '706', 23, 23, 561.6, '2018-06-01 14:32:34', NULL, 250, NULL),
(252, 8, 73, 73, '622', '409', 22, 22, 561.6, '2018-06-01 14:32:41', 253, 252, NULL),
(253, 1, 73, 73, '419', '706', 22, 22, 561.6, '2018-06-01 14:32:41', NULL, 252, NULL),
(254, 8, 22, 22, '622', '409', 12, 12, 561.6, '2018-06-01 14:32:46', 255, 254, NULL),
(255, 1, 22, 22, '419', '706', 14, 14, 561.6, '2018-06-01 14:32:46', NULL, 254, NULL),
(256, 8, 22, 22, '622', '409', 12, 12, 561.6, '2018-06-01 14:32:55', 257, 256, NULL),
(257, 1, 22, 22, '419', '706', 14, 14, 561.6, '2018-06-01 14:32:55', NULL, 256, NULL),
(258, 8, 20, 20, '622', '409', 10, 10, 90, '2014-01-01 16:26:54', 259, 258, NULL),
(259, 1, 20, 20, '419', '706', 12, 12, 90, '2014-01-01 16:26:54', NULL, 258, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `recruitments`
--

CREATE TABLE IF NOT EXISTS `recruitments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recruiter_corp_contract_id` int(11) DEFAULT NULL,
  `recruitment_settings_id` int(11) DEFAULT NULL,
  `recruitee_corp_contract_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `cumulated_billings` double NOT NULL,
  `cumulated_rebate` double NOT NULL,
  `is_expired` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B1C3A0D8F0E694CE` (`recruitee_corp_contract_id`),
  KEY `IDX_B1C3A0D83F6BC2B2` (`recruiter_corp_contract_id`),
  KEY `IDX_B1C3A0D8C37DE46A` (`recruitment_settings_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `recruitments`
--

INSERT INTO `recruitments` (`id`, `recruiter_corp_contract_id`, `recruitment_settings_id`, `recruitee_corp_contract_id`, `created_date`, `expiry_date`, `cumulated_billings`, `cumulated_rebate`, `is_expired`) VALUES
(1, 94, 13, 95, '2016-02-18 13:25:03', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `recruitment_settings`
--

CREATE TABLE IF NOT EXISTS `recruitment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) DEFAULT NULL,
  `range_1` int(11) DEFAULT NULL,
  `range_2` int(11) DEFAULT NULL,
  `rate_1` int(11) DEFAULT NULL,
  `rate_2` int(11) DEFAULT NULL,
  `rate_beyond` int(11) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `new_recruitment_settings_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_A908220B2576E0FD` (`contract_id`),
  UNIQUE KEY `UNIQ_A908220B6A568316` (`new_recruitment_settings_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Contenu de la table `recruitment_settings`
--

INSERT INTO `recruitment_settings` (`id`, `contract_id`, `range_1`, `range_2`, `rate_1`, `rate_2`, `rate_beyond`, `duration`, `new_recruitment_settings_id`) VALUES
(13, 47, 2000, 2000, 15, 10, 5, 12, NULL),
(25, 69, 1000, 2000, 15, 10, 5, 12, NULL),
(27, 93, 1000, 2000, 15, 10, 5, 12, NULL),
(28, 111, 2000, 2000, 15, 10, 5, 12, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `service_types`
--

CREATE TABLE IF NOT EXISTS `service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit_default_amount` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F04264D72576E0FD` (`contract_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Contenu de la table `service_types`
--

INSERT INTO `service_types` (`id`, `contract_id`, `label`, `unit_label`, `unit_default_amount`) VALUES
(1, 16, 'intprestation', 'heure', 20),
(3, 18, 'sujetcontr', 'heure', 20),
(5, 18, 'qsd', 'heure', 1111),
(6, 18, 'zdzdzd', 'heure', 200),
(8, 88, 'qsdsdqsd', 'heure', 42),
(9, 94, 'intitu01', 'heure', 30),
(10, 94, 'int02', 'jours', 60),
(11, 112, 'intitu01', 'heure', 30),
(12, 112, 'int02', 'jours', 60);

-- --------------------------------------------------------

--
-- Structure de la table `settlement`
--

CREATE TABLE IF NOT EXISTS `settlement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandataire_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `amount_ht` double NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `priority` int(11) NOT NULL,
  `amount` double NOT NULL,
  `invoiceable` tinyint(1) NOT NULL,
  `rate_tva` double NOT NULL,
  `unit_amount` double NOT NULL,
  `quantity` double NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DD9F1B5158207E03` (`mandataire_id`),
  KEY `IDX_DD9F1B512989F1FD` (`invoice_id`),
  KEY `IDX_DD9F1B514DFD750C` (`record_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=73 ;

--
-- Contenu de la table `settlement`
--

INSERT INTO `settlement` (`id`, `mandataire_id`, `invoice_id`, `amount_ht`, `type`, `status`, `description`, `created_date`, `priority`, `amount`, `invoiceable`, `rate_tva`, `unit_amount`, `quantity`, `record_id`) VALUES
(1, 7, 1, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/01/2015)', '2014-01-01 11:21:35', 0, 1137.6, 1, 20, 79, 12, 4),
(2, 7, 1, 750, 'points', 2, '3000 Points suite à l''opération Bonus Client ayant attribué 3000 Points à APIMember2 Test', '2014-01-01 16:59:42', 0, 900, 1, 20, 0.25, 3000, 6),
(3, 7, 1, 416.5, 'points', 2, '1666 MultiPoints suite à l''opération Apport d''un Prospect ayant attribué 1000 MultiPoints à APIMember2 Test (+ 1 parrain )', '2014-01-01 17:48:44', 0, 499.8, 1, 20, 0.25, 1666, 11),
(4, 7, 1, 500, 'points', 2, '2000 Points suite à l''opération Bonus Client ayant attribué 2000 Points à APIMember1 Test', '2014-01-01 18:35:34', 0, 600, 1, 20, 0.25, 2000, 13),
(5, 7, 1, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/01/2016)', '2015-01-01 18:42:57', 0, 1137.6, 1, 20, 79, 12, 18),
(6, 7, 1, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/04/2016)', '2015-04-01 14:00:14', 0, 1137.6, 1, 20, 79, 12, 23),
(8, 8, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 02/02/2017)', '2016-02-02 16:15:10', 0, 561.6, 1, 20, 39, 12, 31),
(9, 11, NULL, 20, 'service', 2, 'intprestation (1 heure)', '2016-02-03 10:29:42', 0, 20, 1, 0, 20, 1, 40),
(10, 12, NULL, 0.55, 'fee', 2, 'Frais Coopérons AE (2.75% CA Mohamed BEJI 20,00 €)', '2016-02-03 10:39:56', 0, 0.66, 1, 20, 0.55, 1, 45),
(11, 20, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 04/02/2017)', '2016-02-04 18:58:28', 0, 561.6, 1, 20, 39, 12, 53),
(12, 21, NULL, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 04/02/2017)', '2016-02-04 19:00:24', 0, 1137.6, 1, 20, 79, 12, 58),
(15, 68, NULL, 30, 'service', 2, 'intitu01 (1 heure)', '2016-11-25 16:51:06', 0, 30, 1, 0, 30, 1, 102),
(17, 22, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 25/11/2017)', '2016-11-25 18:08:40', 0, 561.6, 1, 20, 39, 12, 107),
(18, 71, NULL, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 25/11/2017)', '2016-11-25 18:10:33', 0, 1137.6, 1, 20, 79, 12, 112),
(21, 22, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 17:56:44', 0, 561.6, 1, 20, 39, 12, 117),
(22, 73, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 18:00:14', 0, 561.6, 1, 20, 39, 12, 122),
(25, 74, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:28:46', 0, 561.6, 1, 20, 39, 12, 127),
(26, 75, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:31:11', 0, 561.6, 1, 20, 39, 12, 132),
(27, 76, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:33:30', 0, 561.6, 1, 20, 39, 12, 137),
(28, 77, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:36:33', 0, 561.6, 1, 20, 39, 12, 142),
(29, 78, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:41:48', 0, 561.6, 1, 20, 39, 12, 147),
(30, 79, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:43:37', 0, 561.6, 1, 20, 39, 12, 152),
(31, 80, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:57:14', 0, 561.6, 1, 20, 39, 12, 157),
(32, 81, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 10:59:20', 0, 561.6, 1, 20, 39, 12, 162),
(33, 82, NULL, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/04/2016)', '2015-04-01 11:05:01', 0, 1137.6, 1, 20, 79, 12, 167),
(34, 83, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 11:18:25', 0, 561.6, 1, 20, 39, 12, 172),
(35, 83, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 11:19:13', 0, 561.6, 1, 20, 39, 12, 177),
(36, 77, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2016)', '2015-04-01 11:19:53', 0, 561.6, 1, 20, 39, 12, 182),
(37, 8, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 02/02/2018)', '2018-06-01 14:16:10', 0, 561.6, 1, 20, 39, 12, 184),
(39, 20, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 04/02/2018)', '2018-06-01 14:17:10', 0, 561.6, 1, 20, 39, 12, 186),
(40, 20, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 04/02/2019)', '2018-06-01 14:17:30', 0, 561.6, 1, 20, 39, 12, 188),
(42, 22, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:24:09', 0, 561.6, 1, 20, 39, 12, 190),
(43, 71, NULL, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 25/11/2018)', '2018-06-01 14:24:34', 0, 1137.6, 1, 20, 79, 12, 216),
(44, 73, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:24:59', 0, 561.6, 1, 20, 39, 12, 192),
(45, 73, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:25:25', 0, 561.6, 1, 20, 39, 12, 221),
(46, 74, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:25:34', 0, 561.6, 1, 20, 39, 12, 194),
(47, 74, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:25:36', 0, 561.6, 1, 20, 39, 12, 226),
(48, 86, NULL, 948, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/06/2019)', '2018-06-01 14:27:58', 0, 1137.6, 1, 20, 79, 12, 206),
(49, 89, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/06/2019)', '2018-06-01 14:27:58', 0, 561.6, 1, 20, 39, 12, 211),
(50, 83, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:28:10', 0, 561.6, 1, 20, 39, 12, 228),
(51, 83, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:28:19', 0, 561.6, 1, 20, 39, 12, 230),
(52, 83, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2019)', '2018-06-01 14:28:21', 0, 561.6, 1, 20, 39, 12, 232),
(53, 82, NULL, 948, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (Option API) (expirant le 01/04/2017)', '2018-06-01 14:30:33', 0, 1137.6, 1, 20, 79, 12, NULL),
(54, 81, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:30:45', 0, 561.6, 1, 20, 39, 12, 234),
(55, 81, NULL, 468, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:30:50', 0, 561.6, 1, 20, 39, 12, NULL),
(56, 80, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:30:59', 0, 561.6, 1, 20, 39, 12, 236),
(57, 80, NULL, 468, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:31:04', 0, 561.6, 1, 20, 39, 12, NULL),
(58, 79, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:31:10', 0, 561.6, 1, 20, 39, 12, 238),
(59, 79, NULL, 468, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:31:16', 0, 561.6, 1, 20, 39, 12, NULL),
(60, 78, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:31:44', 0, 561.6, 1, 20, 39, 12, 240),
(61, 78, NULL, 468, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:31:46', 0, 561.6, 1, 20, 39, 12, NULL),
(62, 77, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:31:50', 0, 561.6, 1, 20, 39, 12, 242),
(63, 77, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:31:51', 0, 561.6, 1, 20, 39, 12, 244),
(64, 77, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2019)', '2018-06-01 14:31:53', 0, 561.6, 1, 20, 39, 12, 246),
(65, 76, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2017)', '2018-06-01 14:32:04', 0, 561.6, 1, 20, 39, 12, 248),
(66, 76, NULL, 468, 'abonnement', 1, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:32:11', 0, 561.6, 1, 20, 39, 12, NULL),
(67, 74, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2019)', '2018-06-01 14:32:34', 0, 561.6, 1, 20, 39, 12, 250),
(68, 73, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2019)', '2018-06-01 14:32:41', 0, 561.6, 1, 20, 39, 12, 252),
(69, 22, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2018)', '2018-06-01 14:32:46', 0, 561.6, 1, 20, 39, 12, 254),
(70, 22, NULL, 468, 'abonnement', 2, 'abonnement annuel au service Coopérons Plus (expirant le 01/04/2019)', '2018-06-01 14:32:54', 0, 561.6, 1, 20, 39, 12, 256),
(71, 20, NULL, 37.5, 'points', 2, '150 Points suite à l''opération Commission Simple (test) ayant attribué 150 Points à salah salah', '2014-01-01 16:26:53', 0, 45, 1, 20, 0.25, 150, 258),
(72, 20, NULL, 37.5, 'points', 2, '150 MultiPoints suite à l''opération Commission Multi-Niveau (test) ayant attribué 150 MultiPoints à salah salah', '2014-01-01 16:26:53', 0, 45, 1, 20, 0.25, 150, 258);

-- --------------------------------------------------------

--
-- Structure de la table `sponsorship`
--

CREATE TABLE IF NOT EXISTS `sponsorship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sponsor_id` int(11) DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `upline` longtext COLLATE utf8_unicode_ci NOT NULL,
  `king_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C0F10CD43EB8070A` (`program_id`),
  KEY `IDX_C0F10CD45F1160A4` (`sponsor_id`),
  KEY `IDX_C0F10CD4D94F19E8` (`affiliate_id`),
  KEY `IDX_C0F10CD422D58347` (`king_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=34 ;

--
-- Contenu de la table `sponsorship`
--

INSERT INTO `sponsorship` (`id`, `program_id`, `created_date`, `discr`, `sponsor_id`, `affiliate_id`, `upline`, `king_id`) VALUES
(6, 1, '2014-01-01 09:08:15', 'prod', 120, 122, '#21#', 120),
(7, 1, '2014-01-01 16:32:56', 'prod', 122, 123, '#22#21#', 120),
(9, 5, '2014-01-01 17:02:00', 'prod', 128, 127, '#1#', 128),
(10, 5, '2014-01-01 17:30:01', 'prod', 127, 129, '#2#1#', 128),
(11, 1, '2014-01-01 11:21:49', 'prod', 131, 130, '#25#26#', 132),
(12, 1, '2014-01-01 11:22:43', 'prod', 132, 131, '#26#', 132),
(13, 1, '2014-04-01 12:16:37', 'prod', 131, 133, '#25#26#', 132),
(14, 1, '2016-02-02 16:21:11', 'prod', 120, 135, '#21#', 120),
(15, 1, '2016-02-03 07:23:56', 'prod', 120, 140, '#21#', 120),
(16, 1, '2016-02-03 07:35:39', 'prod', 120, 142, '#21#', 120),
(17, 7, '2016-02-03 07:39:19', 'prod', 137, 143, '#28#', 137),
(18, 1, '2016-02-03 07:39:55', 'prod', 138, 144, '#29#', 138),
(19, 1, '2016-02-03 07:45:51', 'prod', 120, 145, '#21#', 120),
(20, 4, '2016-02-03 10:39:56', 'prod', 146, 136, '#33#', 146),
(21, 1, '2016-02-03 10:39:56', 'prod', 145, 135, '#33#21#', 120),
(22, 1, '2016-02-09 07:54:39', 'prod', 120, 149, '#21#', 120),
(23, 1, '2016-02-09 07:59:20', 'prod', 120, 151, '#21#', 120),
(24, 1, '2016-02-12 13:43:33', 'prod', 120, 152, '#21#', 120),
(25, 1, '2016-02-17 09:51:41', 'prod', 120, 155, '#21#', 120),
(26, 1, '2016-02-17 15:17:18', 'prod', 120, 156, '#21#', 120),
(27, 4, '2016-11-25 16:51:06', 'prod', 141, 134, '#30#', 141),
(28, 7, '2015-04-01 10:00:32', 'prod', 137, 158, '#28#', 137),
(29, 17, '2014-04-01 19:38:29', 'prod', 160, 161, '#41#', 160),
(30, 17, '2016-02-27 19:40:58', 'prod', 160, 162, '#41#', 160),
(31, 17, '2014-04-01 20:10:32', 'prod', 160, 163, '#41#', 160),
(32, 15, '2014-01-01 21:47:49', 'prod', 166, 167, '#47#', 166),
(33, 1, '2014-01-01 21:48:51', 'prod', 164, 168, '#39#', 164);

-- --------------------------------------------------------

--
-- Structure de la table `transfers`
--

CREATE TABLE IF NOT EXISTS `transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `debit_mandataire_id` int(11) DEFAULT NULL,
  `credit_mandataire_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_802A39184DFD750C` (`record_id`),
  KEY `IDX_802A3918B2D54BB3` (`debit_mandataire_id`),
  KEY `IDX_802A3918CE0F2397` (`credit_mandataire_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `transfers`
--

INSERT INTO `transfers` (`id`, `debit_mandataire_id`, `credit_mandataire_id`, `amount`, `created_date`, `record_id`) VALUES
(1, 11, 12, 0.66, '2016-02-03 10:39:56', 42);

-- --------------------------------------------------------

--
-- Structure de la table `uploaded_files`
--

CREATE TABLE IF NOT EXISTS `uploaded_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_uploaded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38 ;

--
-- Contenu de la table `uploaded_files`
--

INSERT INTO `uploaded_files` (`id`, `title`, `filename`, `date_uploaded`) VALUES
(1, 'img_Edatis', 'ProgramLogos/img_Edatis.png', '2014-01-01 10:22:13'),
(2, 'img_blocs', 'ProgramLogos/img_blocs.png', '2016-02-02 16:14:40'),
(3, 'doc_blocs', 'EasyDocs/doc_blocs.pdf', '2016-02-02 16:25:03'),
(4, 'img_arbre', 'ProgramLogos/img_arbre.png', '2016-02-03 14:28:25'),
(6, 'img_gggg', 'ProgramLogos/img_gggg.png', '2016-02-04 18:58:02'),
(7, 'img_capture02', 'ProgramLogos/img_capture02.png', '2016-02-04 18:59:45'),
(8, 'img_ererre', 'ProgramLogos/img_ererre.jpeg', '2016-11-25 17:18:27'),
(9, 'doc_gggg', 'EasyDocs/doc_gggg.pdf', '2016-02-08 09:56:52'),
(10, 'img_Api', 'ProgramLogos/img_Api.jpeg', '2016-11-25 17:42:25'),
(11, 'img_Test', 'ProgramLogos/img_Test.jpeg', '2016-02-23 14:06:57'),
(12, 'doc_ererre', 'EasyDocs/doc_ererre.pdf', '2015-04-01 17:54:59'),
(13, 'doc_Test', 'EasyDocs/doc_Test.pdf', '2015-04-01 18:01:01'),
(14, 'img_Testv2122', 'ProgramLogos/img_Testv2122.png', '2015-04-01 10:28:25'),
(15, 'doc_Testv2122', 'EasyDocs/doc_Testv2122.pdf', '2015-04-01 10:29:17'),
(16, 'img_rrrrrrrrrrrr', 'ProgramLogos/img_rrrrrrrrrrrr.jpeg', '2015-04-01 10:30:54'),
(17, 'doc_rrrrrrrrrrrr', 'EasyDocs/doc_rrrrrrrrrrrr.pdf', '2015-04-01 10:31:34'),
(18, 'img_aaaaaaaaaaaaaaaa', 'ProgramLogos/img_aaaaaaaaaaaaaaaa.jpeg', '2015-04-01 10:32:43'),
(19, 'doc_aaaaaaaaaaaaaaaa', 'EasyDocs/doc_aaaaaaaaaaaaaaaa.pdf', '2015-04-01 10:33:54'),
(20, 'img_aaaaaaaaa', 'ProgramLogos/img_aaaaaaaaa.png', '2015-04-01 10:36:11'),
(21, 'doc_aaaaaaaaa', 'EasyDocs/doc_aaaaaaaaa.pdf', '2015-04-01 10:37:48'),
(22, 'img_ssssssssss', 'ProgramLogos/img_ssssssssss.jpeg', '2015-04-01 10:41:30'),
(23, 'doc_ssssssssss', 'EasyDocs/doc_ssssssssss.pdf', '2015-04-01 10:42:08'),
(24, 'img_aaaaaaaaaaaaaaa', 'ProgramLogos/img_aaaaaaaaaaaaaaa.png', '2015-04-01 10:43:17'),
(25, 'doc_aaaaaaaaaaaaaaa', 'EasyDocs/doc_aaaaaaaaaaaaaaa.pdf', '2015-04-01 10:44:00'),
(26, 'img_ssssssssssss', 'ProgramLogos/img_ssssssssssss.png', '2015-04-01 10:55:10'),
(27, 'doc_ssssssssssss', 'EasyDocs/doc_ssssssssssss.pdf', '2015-04-01 10:58:04'),
(28, 'img_fffffffffffff', 'ProgramLogos/img_fffffffffffff.png', '2015-04-01 10:59:00'),
(29, 'doc_fffffffffffff', 'EasyDocs/doc_fffffffffffff.pdf', '2015-04-01 10:59:53'),
(30, 'img_aaaaaaaaaaa', 'ProgramLogos/img_aaaaaaaaaaa.jpeg', '2015-04-01 11:04:40'),
(31, 'img_aaaaaaaaaa', 'ProgramLogos/img_aaaaaaaaaa.png', '2015-04-01 11:18:03'),
(32, 'doc_aaaaaaaaaa', 'EasyDocs/doc_aaaaaaaaaa.pdf', '2015-04-01 11:18:42'),
(33, 'img_dddddddd', 'ProgramLogos/img_dddddddd.jpeg', '2016-02-27 12:34:28'),
(34, 'img_Testtt_Api', 'ProgramLogos/img_Testtt_Api.jpeg', '2016-02-27 15:22:17'),
(35, 'img_tn', 'ProgramLogos/img_tn.jpeg', '2014-04-01 09:37:08'),
(36, 'doc_gggg', 'EasyDocs/doc_gggg.pdf', '2014-01-01 16:25:40'),
(37, 'img_Test_23', 'ProgramLogos/img_Test_23.jpeg', '2014-01-01 18:02:43');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mail_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_1483A5E9A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_1483A5E992FF4F48` (`id_contact`),
  UNIQUE KEY `UNIQ_1483A5E97597D3FE` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=42 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `id_contact`, `member_id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `status`, `mail_status`, `last_name`, `first_name`, `created_date`) VALUES
(1, 1, NULL, 'adminuser', 'adminuser', 'admin@admin.com', 'admin@admin.com', 1, 'rm6wu2xajlco00gskgwgko4oo0cs0so', 'bEz/iCWAXOuOcxxUTUiET3AAOKWhFfJWxMimGRsyb154bkoQ9e2dvacaOeqhcZ8BJtSHtesBdZiOqSGbUCgp5A==', '2016-03-09 17:34:11', 0, 0, NULL, 'kmXyOREtAu7VtHBFDEse-Rs6tSQh2TV6kMp7fBBLWK8', '2016-02-02 16:10:28', 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', 0, NULL, 'MPO', 'Active', NULL, NULL, '2014-11-04 15:32:47'),
(3, 3, 1, 'ei@test.com', 'ei@test.com', 'ei@test.com', 'ei@test.com', 1, 'rm6wu2xajlco00gskgwgko4oo0cs0so', 'bEz/iCWAXOuOcxxUTUiET3AAOKWhFfJWxMimGRsyb154bkoQ9e2dvacaOeqhcZ8BJtSHtesBdZiOqSGbUCgp5A==', '2016-02-26 17:05:47', 0, 0, NULL, 'wfwXS63P6fWyg2OaVP3oMd5u3tlt7KJNB5Yp6gWwmkY', '2016-02-03 12:23:38', 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'MPO', 'Active', 'Ifergan', 'Emmanuel', '2014-10-01 16:02:33'),
(21, 21, 18, 'member@test.com', 'member@test.com', 'member@test.com', 'member@test.com', 1, 'k5km7s8ay9csws4co444k8o8c40k440', 'IGARPpodjt25br2lBHPrlQsBUh2Tjeyf8i7G9R8Rm1uhfYtWyRiaynuicL8iR+L0iHXEcvoxZTwc0sbmO0nHoQ==', '2016-03-09 17:30:50', 0, 0, NULL, 'p3SXLifWrPl8PsOhhigYzjzLLMOsk1xy9F7eVTt0maY', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'Test', 'Member', '2014-01-01 16:30:35'),
(22, 22, 19, 'prospect@test.com', 'prospect@test.com', 'prospect@test.com', 'prospect@test.com', 1, '6hxikekfj50kg8s8k4koww0sks4soo0', 'sertEq7pzjePC5Mwh5WS5jRaBxI7YYuKNgyM1OBBZzMrzl8Yst2GVijGkVMbnDtbU6yucGxw0/CvzrtTvd2R8A==', '2016-01-11 14:01:53', 0, 0, NULL, 'n01EHKwyIpx34HKE4werKZfjyOulvSAeuPornEPumQk', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'Test', 'Prospect', '2014-01-01 09:08:15'),
(23, 23, 20, 'prospectnew@test.com', 'prospectnew@test.com', 'prospectnew@test.com', 'prospectnew@test.com', 1, 's3ik4q0lys0cgcg8s84kwk80wggcwc4', 'kNpXS9m7ngaN1rT9tasR7ATEN72WW0XG6z/lCXeWFRH/TnjnCQuhgl7yelRqrldfovOa8B5Ouvd+wN+n83rhkg==', '2016-01-08 16:37:41', 0, 0, NULL, 'SvhSZRcfmajxayeEnq9aaP-5HFHgql7iUhu9kcIv1bM', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'Test', 'ProspectNew', '2014-01-01 16:32:56'),
(24, 25, 25, 'apimember3@test.com', 'apimember3@test.com', 'apimember3@test.com', 'apimember3@test.com', 1, '98brzysg0v0gco4ww4sso404s4wsk08', 'SsQ/JVgSoYZTCsIe0OD18aEI2VCct1+Qf03FRbWltLFOdNoRA0wuDKG4J7j1wknb2opMHKm5sV8b/+lI81epMg==', NULL, 0, 0, NULL, 'ztMecVBSYPCnTu-rdfnLNqstXIeJp7LmDjVtvLvfs28', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'Test', 'APIMember3', '2014-01-01 17:30:41'),
(25, 26, 23, 'apimember2@test.com', 'apimember2@test.com', 'apimember2@test.com', 'apimember2@test.com', 1, 'hr6j98gzpi0co0oco00co0go44g8ocs', 'OpbXluSw21yyQMOa5TTdgHbh95yryBB5pGhCJKVFIEWfysC1AR62ngVsVdAOaPWv53SQqOo79eLLR/gR4P7Z5g==', '2016-02-23 11:30:38', 0, 0, NULL, 'J051Siil2YlZntz2tKcvP5eMxaXzrDXFyLhCOLip4Yo', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'Test', 'APIMember2', '2014-01-01 11:21:48'),
(26, 27, 24, 'apimember1@test.com', 'apimember1@test.com', 'apimember1@test.com', 'apimember1@test.com', 1, '4cc9bvzizcyswo4g0oocc8s0s8scs4s', 'hfTAX/zP/BXRKEQwIfRD4B7EMR4/floHi/O7NuzwsA7poZJV0LNtek0IosvlCs/YgxXOyw09VLZhhogE1WQHsw==', '2016-01-11 12:27:29', 0, 0, NULL, '9-_XbssBNna9sgFInzNpztc2d5sc1jbr-UKxs5kiY2o', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'Test', 'APIMember1', '2014-01-01 11:22:43'),
(27, 28, 26, 'daf@test.com', 'daf@test.com', 'daf@test.com', 'daf@test.com', 1, 'j0mcr4wc5ag40c4ogs0o800okk4wk4w', 'A7KrsHf/5galxXH8PSnKqZ83gqmS0akEgiWDrWGE2m0x63aOy+/Ujxw1tPBNa47kSLsYpCzNoYvVsujk71Pq1Q==', '2016-01-11 12:29:29', 0, 0, NULL, 'sf7z2wo0oD8d7cqMdD7qIrYzFPT3ljU-hg9KJouxXTM', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'Active', 'standby', 'Test', 'DAF', '2014-04-01 12:16:37'),
(28, 29, 27, 'mbj@test.com', 'mbj@test.com', 'mbj@test.com', 'mbj@test.com', 1, 'tjdzt1wk88go084g8k0scs4c4k0k484', 'E7WuAYllJMb/MGRBRIJwembezeReEoZJIDSYOL5zaAI5/zJjkvNiUy5BfMruxaj7Lf38cEHCRWgLvi5+zPul3g==', '2016-02-22 16:24:22', 0, 0, NULL, 'F_V-c-UTkieWEadjWXQjCZnqSBW4CYtSD315cRI5OYc', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'BEJI', 'Mohamed', '2016-02-02 16:21:11'),
(29, 30, 28, 'bbi@test.com', 'bbi@test.com', 'bbi@test.com', 'bbi@test.com', 1, 'h7mwgf658t4c4s0c4ss8s8owg40o4oo', 'a42R1QzgPx86HpC+gSMuCsJ4MhVc3pOy5b7iGtbrb9SBOoTq72PSb/GahC7YmwWsqniML6ATL6SHMigpyv5Vdw==', '2016-02-22 16:23:52', 0, 0, NULL, 'HMQMqlhNM441bfBHxzoY9G9giC-aiYtKzhbDxr3vHo4', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'BIJAOUI', 'Bilel', '2016-02-02 16:45:14'),
(30, 31, 29, 'ali@test.com', 'ali@test.com', 'ali@test.com', 'ali@test.com', 1, 'e1rvmx1typwgo8gcggw8s4kscow0ckw', 'HifBFWY+MuYyKfm+ue62OCvigXk+d3hZIEia0Dd0vyP3wSE5CUXk49n25MmewlyC50KJ7r65hUI0xDmeYwGB0A==', '2016-02-22 16:24:41', 0, 0, NULL, 'nrTptcjtzKzzL8sdzl8Ky_FoEAUV7RtbOWUGEQF0yJQ', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'LAABIDI', 'Ahmed', '2016-02-03 07:23:56'),
(31, 32, 30, 'ik@test.com', 'ik@test.com', 'ik@test.com', 'ik@test.com', 1, 'o1qr71rcf400oskoscwgwo0scsgwksk', 'vH5hVml6yYa226nE3ezSSCcR9mAG5DOQd9nYoj3mILSzb/CCbDtXSt0srNeBT9UgbzZN9SFHEyHk07nVnlNPMQ==', NULL, 0, 0, NULL, 'UPOVlOT3xOAoyr5R2BKX5dA9bF67ccmuqPa6dByt9tU', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'KHAZRI', 'Imen', '2016-02-03 07:35:39'),
(32, 33, 31, 'ye@test.com', 'ye@test.com', 'ye@test.com', 'ye@test.com', 1, 'ktz03l7p4nkccsswc0sowgskcw84400', 'LPjN86VPIgtYFUuyOSeh1FuLZAoVYp1vlowPmIDh8a80bvjRzwyfTX2BDvZ3e6NLnULNPfjXIVfNpvHHuoSZow==', NULL, 0, 0, NULL, 'RRdAaktBuudtw5oXfRUKv5xRjFgx5SxGiMXbv_BXlT4', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'Active', 'Active', 'EZZINE', 'Yassine', '2016-02-03 07:39:55'),
(33, 34, 32, 'sg@test.com', 'sg@test.com', 'sg@test.com', 'sg@test.com', 1, '35a3vbbuqhuso4css0k4wsg4gww0owo', 'Ht0uqqVtAWBKvSg9M9+DnJwEJErZVYtFMd/DqsqFDPS3JCmsx7AeGOgrrWwOAN74x3VFVz1zHeNT3FpvL8A6Eg==', '2016-02-22 16:25:27', 0, 0, NULL, 'SpXebi1cOKYZj3L6_IMPcgEorqQpu5-iCgJ1Z_YVbEI', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'GUELBI', 'Samir', '2016-02-03 07:45:51'),
(34, 35, 35, 'commercial@test.com', 'commercial@test.com', 'commercial@test.com', 'commercial@test.com', 1, 'jcchrgp9p6gwooco0w8wgs000o4wg8s', 'Dxz+5105P4CUreTI08o6/WTi1MZTpMZwvRgnc+HfZ7aCETaB9EH2uYzcc/1TInUgsYvTByxlEMGzK+sC/Ij7Bw==', '2016-02-12 13:30:52', 0, 0, NULL, 'MhATLk6hCzKQnd4ZcZVBDr12wLiy4j0fbBOkPIPUJ5I', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'commercial', 'commercial', '2016-02-09 07:54:39'),
(35, 36, 36, 'aaa@test.com', 'aaa@test.com', 'aaa@test.com', 'aaa@test.com', 1, '20kphjdhcsm8k8k8ccggskko4w84csw', 'Qden+9UvK1x6Rp7Ne7Q+d9wrN1noJNfJUV7Vu6Ty8Y8Y5rHrFsuuE0LmdswYa/vo9tXDC/d70ORIu+8TK9UO9A==', '2016-02-09 08:03:35', 0, 0, NULL, 'GviZSwh01mj6Cwhjqs9HrL4i9e6MfUWA4C3XNlQ8GWA', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'Active', 'Active', 'aaa', 'aaa', '2016-02-09 07:59:20'),
(36, 37, 37, 'bn@test.com', 'bn@test.com', 'bn@test.com', 'bn@test.com', 1, 'n3txeysi6dcwo8cccs0ks40s0www4gw', '6gr6otSlGUi762oUDVV5Ia+a+cdD4wIqZ7iFG/YYrM2mzCAsDsF5+XKsAopkJRXLKcgWi++ZV+Mb9ezhCQq0Kw==', '2016-02-17 13:49:50', 0, 0, NULL, '0eNQLgi_FO0YY9rvGP6j_Z2aVF4cC7sSJeY3ou7BZlU', NULL, 'a:2:{i:0;s:11:"ROLE_MEMBER";i:1;s:7:"ROLE_AE";}', 0, NULL, 'Active', 'Active', 'nomen', 'bilel', '2016-02-12 13:43:32'),
(37, 38, 38, 'presta@test.com', 'presta@test.com', 'presta@test.com', 'presta@test.com', 1, 'o60ebzoeg2s4sg8wscs8008co4sco04', 'ADUZJROxfZm+ekuWiav418bGboyNT8ysKyMSRG0hblGXfOvVUPhNYNLpvjhMDQhNrR//q7NxYgf1KNSRTT9tCQ==', '2016-02-22 16:25:50', 0, 0, NULL, '4JIHF4Npbvo7e1SKXCr_l6wlROfG8iSV07ikqFie8O4', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'presta', 'presta', '2016-02-17 09:51:41'),
(38, 39, 39, 'salah@test.com', 'salah@test.com', 'salah@test.com', 'salah@test.com', 1, 'kng70hpykwgck4g408c0ckcckko0scw', 'ZVP2PXYj/5C8bpSCFAan8In0WP1EVn8auVADJEtclbl5n3qX2xDcra1POeXmfDug7v0MOxlCCZM1DleRpqHCpg==', NULL, 0, 0, NULL, 'AIM3U8PF38VVbO0jmXQGTnVBRgVNR85Zc-zGqjGfwcM', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'Active', 'Active', 'salah', 'salah', '2016-02-17 15:17:18'),
(39, 40, 45, 'test@test.com', 'test@test.com', 'test@test.com', 'test@test.com', 1, 'fu7ih9cfi8oco8wkcwwk40ogk4socgk', 'zjkB2EMY7JCLQ1nCQJi0JgbKRrlrS0AOY7BXIRCDwJFx3D72AGv22gg3bp1hcvMtW9lBnvx888UBW/AmcX67dg==', '2016-03-07 17:24:50', 0, 0, NULL, '-LOScTx7zCiVOExNZBrKBKSoYRtHulBQRkUx2ahOKTk', '2014-01-01 17:26:54', 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'MPO', 'standby', 'test', 'test', '2016-02-27 21:07:32'),
(40, 41, 46, 'test78@test.com', 'test78@test.com', 'test78@test.com', 'test78@test.com', 1, 'ecbb24jmv6gc8k0gcg8gs8gg8wwg44g', 'FSMGGQcLbbSNQeABwGr3Dyi36r3wWCby47wtoRnHzRSpPOx7omWvhxuZlpjoppHv4KWVkhgES3E8ZRfELeKtcQ==', NULL, 0, 0, NULL, 'hVhZk91iL1DNVavBvX_hfqY3dkIMAUToAh8L3C7PZEE', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'test', 'test', '2016-02-27 21:10:53'),
(41, 42, 48, 'test2@test.com', 'test2@test.com', 'test2@test.com', 'test2@test.com', 1, 'hiab7ve68xcskkcsow0w4scgsw8gogs', '1C5nr0j1xOWNJ+udRmFHwrScNcTZndl34c8KSxaDw+VNVS1juRA9KXUUKvmcB5jtftcTKrjdgw56Pgh0EA9lFw==', NULL, 0, 0, NULL, '1ncElKHdc6DRBpiu-EjEnPoXko-IM7G3U6uRIPEJp0Q', NULL, 'a:1:{i:0;s:11:"ROLE_MEMBER";}', 0, NULL, 'standby', 'standby', 'test', 'test', '2014-01-01 21:48:50');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
