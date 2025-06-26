-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql308.byetcluster.com
-- Generation Time: Jun 26, 2025 at 08:53 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b9_39232587_us_imports`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'lara', '$2y$10$OcdGvT5tSbgzy481wD0fteOoThEYv/2iPzplgDKt6CFRt1LmpAbM2', '2025-06-26 00:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`) VALUES
(1, 'Nike', '2025-06-21 18:37:49'),
(2, 'Adidas', '2025-06-21 18:37:49'),
(3, 'Levi\'s', '2025-06-21 18:37:49'),
(4, 'Calvin Klein', '2025-06-21 18:37:49'),
(5, 'Zara', '2025-06-21 18:37:49'),
(6, 'H&M', '2025-06-21 18:37:49'),
(7, 'Lacoste', '2025-06-21 18:37:49'),
(8, 'Tommy Hilfiger', '2025-06-21 18:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `clothing_types`
--

CREATE TABLE `clothing_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clothing_types`
--

INSERT INTO `clothing_types` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'T-shirts', 't-shirts', '2025-06-21 18:37:49'),
(2, 'Pantalons', 'pantalons', '2025-06-21 18:37:49'),
(3, 'Robes', 'robes', '2025-06-21 18:37:49'),
(4, 'Vestes', 'vestes', '2025-06-21 18:37:49'),
(5, 'Sweats', 'sweats', '2025-06-21 18:37:49'),
(6, 'Jupes', 'jupes', '2025-06-21 18:37:49'),
(7, 'Shorts', 'shorts', '2025-06-21 18:37:49'),
(8, 'Sous-vêtements', 'sous-vetements', '2025-06-21 18:37:49'),
(9, 'Maillots de bain', 'maillots-de-bain', '2025-06-21 18:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `name`, `hex_code`, `created_at`) VALUES
(1, 'Noir', '#000000', '2025-06-21 18:37:49'),
(2, 'Blanc', '#FFFFFF', '2025-06-21 18:37:49'),
(3, 'Rouge', '#FF0000', '2025-06-21 18:37:49'),
(4, 'Bleu', '#0000FF', '2025-06-21 18:37:49'),
(5, 'Vert', '#00FF00', '2025-06-21 18:37:49'),
(6, 'Jaune', '#FFFF00', '2025-06-21 18:37:49'),
(7, 'Rose', '#FFC0CB', '2025-06-21 18:37:49'),
(8, 'Gris', '#808080', '2025-06-21 18:37:49'),
(9, 'Marron', '#A52A2A', '2025-06-21 18:37:49'),
(10, 'Beige', '#F5F5DC', '2025-06-21 18:37:49'),
(11, 'Violet', '#800080', '2025-06-21 18:37:49'),
(12, 'Orange', '#FFA500', '2025-06-21 18:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` varchar(50) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `total` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_phone`, `customer_address`, `status`, `total`, `notes`, `created_at`, `updated_at`) VALUES
('CMD-1750851096-9117', '', '', '', 'pending', '199.00', NULL, '2025-06-25 11:31:36', '2025-06-25 11:31:36'),
('CMD-1750855536-9975', '', '', '', 'pending', '398.00', NULL, '2025-06-25 12:45:36', '2025-06-25 12:45:36'),
('CMD-1750872709-1913', '', '', '', 'pending', '699.00', NULL, '2025-06-25 17:31:49', '2025-06-25 17:31:49'),
('CMD-1750899926-9460', '', '', '', 'pending', '170.00', NULL, '2025-06-26 01:05:26', '2025-06-26 01:05:26'),
('CMD-1750939360-6260', '', '', '', 'pending', '398.00', NULL, '2025-06-26 12:02:39', '2025-06-26 12:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(20) NOT NULL,
  `color` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `color`, `quantity`, `price`, `created_at`) VALUES
(3, 'CMD-1750675145-7679', 6, 'Sac à dos  Karl Lagerfeld Paris', '', '', 2, '500.00', '2025-06-23 10:39:05'),
(4, 'CMD-1750675145-7679', 9, 'Karl Lagerfeld Paris Sac à main ', '', '', 1, '400.00', '2025-06-23 10:39:05'),
(5, 'CMD-1750675145-7679', 5, 'Sac Karl lagerfeld', '', '', 6, '500.00', '2025-06-23 10:39:05'),
(6, 'CMD-1750675227-7632', 18, 'U.S. POLO ASSN. Sac ', '', '', 1, '300.00', '2025-06-23 10:40:27'),
(7, 'CMD-1750675430-8316', 5, 'Sac Karl lagerfeld', '', '', 1, '500.00', '2025-06-23 10:43:50'),
(8, 'CMD-1750712093-5807', 6, 'Sac à dos  Karl Lagerfeld Paris', '', '', 1, '500.00', '2025-06-23 20:54:53'),
(9, 'CMD-1750712107-7828', 6, 'Sac à dos  Karl Lagerfeld Paris', '', '', 1, '500.00', '2025-06-23 20:55:07'),
(10, 'CMD-1750714903-2656', 6, 'Sac à dos  Karl Lagerfeld Paris', '', '', 1, '500.00', '2025-06-23 21:41:43'),
(11, 'CMD-1750851096-9117', 35, 'Casquette Karl lagerfeld Paris ', '', '', 1, '199.00', '2025-06-25 11:31:36'),
(12, 'CMD-1750851877-6316', 5, 'Sac Karl lagerfeld', '', '', 1, '699.00', '2025-06-25 11:44:37'),
(13, 'CMD-1750855536-9975', 19, 'Sac Tommy', '', '', 1, '398.00', '2025-06-25 12:45:36'),
(14, 'CMD-1750872709-1913', 5, 'Sac Karl lagerfeld', '', '', 1, '699.00', '2025-06-25 17:31:49'),
(15, 'CMD-1750875345-1934', 6, 'Sac à dos  Karl Lagerfeld Paris', '', '', 1, '500.00', '2025-06-25 18:15:45'),
(16, 'CMD-1750899926-9460', 40, 'Ti shirt Karl lagerfeld ', '', '', 1, '170.00', '2025-06-26 01:05:26'),
(17, 'CMD-1750939360-6260', 19, 'Sac Tommy', '', '', 1, '398.00', '2025-06-26 12:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `images` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `clothing_type_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `images`, `price`, `clothing_type_id`, `brand_id`, `badge`, `stock`, `featured`, `created_at`, `updated_at`) VALUES
(5, 'Sac Karl lagerfeld', 'sacs', 'Sac bandoulière Karl Lagerfeld  avec porte-monnaie', '[\"uploads\\/685745337a3bd_da614c7d-500c-496f-9696-69b3f70b7947.jpeg\",\"uploads\\/685745337a6aa_26a6988d-7c8d-42c5-8a22-fa9ad294043a.jpeg\",\"uploads\\/685745337a8bd_8355f4d0-da34-457e-8a16-b63711867d24.jpeg\",\"uploads\\/685745337ab23_4534575d-2f22-491f-b366-bce3a86ad23f.jpeg\"]', '699.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-21 23:50:11', '2025-06-23 10:59:30'),
(6, 'Sac à dos  Karl Lagerfeld Paris', 'sacs', 'Sac à dos Karl Lagerfeld Paris', '[\"uploads\\/6857463297812_fe0e6e6f-d4fd-4500-95b7-a6fce352690e.jpeg\",\"uploads\\/6857463297b35_5b78c74a-1b9b-4542-9186-cfc99c8aa15e.jpeg\",\"uploads\\/6857463297d3e_9363ef9a-a2cc-4f52-bcb9-0241f6762b52.jpeg\",\"uploads\\/6857463297fe3_f2f46621-2044-4203-8767-274c725803a0.jpeg\"]', '500.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-21 23:54:26', '2025-06-22 20:36:10'),
(10, 'Sandales Karl lagerfeld', 'Sandales', 'Sandales tongs à bride arrière Karl Lagerfeld Paris', '[\"uploads\\/6857dbab04fe8_15b57dc2-3409-4732-a717-c87c4a5b1255.jpeg\",\"uploads\\/6857dbab054ad_f03783ad-5dad-4ae6-afb0-0ef9423ae72c.jpeg\"]', '398.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-22 10:32:11', '2025-06-23 10:48:48'),
(11, 'Cassettes Tommy', 'Casquettes ', 'Cassettes réglable Tommy ', '[\"uploads\\/68586b196439a_c368fa7d-a220-4a3e-9613-fd2b76f3a3bf.jpeg\",\"uploads\\/68586b1964604_f17d7fbc-ab08-4d88-b3c8-f0f682834fec.jpeg\",\"uploads\\/68586b1964859_fbdab5c5-8319-4053-a112-cb2f88fb5e91.jpeg\"]', '140.00', 0, 0, 'Seulement deux  en stock', 0, 0, '2025-06-22 20:44:09', '2025-06-22 22:25:11'),
(12, 'Casquette Tommy', 'Casquettes', 'Casquette Tommy réglable ', '[\"uploads\\/68586bc190e6f_c3574eaf-147b-415b-8acf-6de03966f838.jpeg\",\"uploads\\/68586bc191145_c76e160c-48ea-433e-a738-57be6865ed85.jpeg\",\"uploads\\/68586bc19133b_99494ee4-1c06-4822-8a40-7e47429918f3.jpeg\",\"uploads\\/68586bc1917a9_a333b6a5-5a1d-4275-856b-e3b01ec5b749.jpeg\"]', '140.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-22 20:46:57', '2025-06-22 20:46:57'),
(13, 'Sac Tommy', 'Sacs', 'Sac à bandoulière TH moyen imperméable beige', '[\"uploads\\/68586d1bdff36_c24c76e1-6c6f-4b21-a644-5f7940e16d0e.jpeg\",\"uploads\\/68586d1be021d_05505990-3523-434d-88df-893bebb1fd4a.jpeg\",\"uploads\\/68586d1be045c_36833c46-06c3-4668-8d49-676d09331a66.jpeg\"]', '400.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-22 20:52:43', '2025-06-22 20:52:43'),
(15, 'Des  écharpes CK', 'Écharpes', 'Grand écharpe \r\nDouble face ', '[\"uploads\\/68586f23d9236_5dc9aa83-a428-4776-b0a5-a4584f89927c.jpeg\",\"uploads\\/68586f23d95f7_14c37719-1352-41f7-9ad6-9ee38ef019a4.jpeg\",\"uploads\\/68586f23d9948_38f7da95-90ed-4a1c-998c-377c15a159f0.jpeg\",\"uploads\\/68586f23d9c81_056bba1e-4d77-472f-85ed-f9685baa8215.jpeg\",\"uploads\\/68586f23d9fbb_bdeb3f35-c52c-4924-ac7d-21458f874422.jpeg\",\"uploads\\/68586f23da287_19a2ff7b-1700-4c97-9ae2-6ca3ee57565e.jpeg\"]', '160.00', 0, 0, '', 0, 0, '2025-06-22 21:01:23', '2025-06-25 13:17:22'),
(19, 'Sac Tommy', 'Sacs ', 'Sacs et sacs à main Tommy Hilfiger PVC pour femme', '[\"uploads\\/685872b5818dd_0c54bee6-6387-4ddc-aa92-0102394361cf.jpeg\",\"uploads\\/685872b581b76_8594e845-929d-4e3b-ba74-be698b29b629.jpeg\",\"uploads\\/685872b581d76_1067acf5-1d5a-49e7-9026-4ff1060bed8b.jpeg\"]', '398.00', 0, 0, 'un seul en stock ', 0, 0, '2025-06-22 21:16:37', '2025-06-23 10:51:25'),
(20, 'Sac bandoulière Karl Lagerfeld ', 'Sacs', 'Sac bandoulière Karl Lagerfeld à carreaux multicolores pastel', '[\"uploads\\/6858753b3fb21_1c3b7376-3410-4740-b04d-8ebcb1af4560.jpeg\",\"uploads\\/6858753b400d5_ad2aae6d-0d40-475f-b04e-a00cd9f070ac.jpeg\"]', '500.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-22 21:27:23', '2025-06-22 22:26:33'),
(21, 'Sac Tommy', 'Sacs', 'Sacs de la marque Tommy Hilfiger', '[\"uploads\\/68587687ab418_219d3d12-590c-4613-b4ef-c47991c598cb.jpeg\",\"uploads\\/68587687ab6c5_c4180182-b349-4361-919b-98bfaa80dd9a.jpeg\",\"uploads\\/68587687ab8c4_98c7fdde-a812-4c40-bdf1-9ddcf1c877e6.jpeg\",\"uploads\\/68587687abacd_8a526dc0-e32b-4f9c-854a-8ee36fd78c8e.jpeg\"]', '399.00', 0, 0, ' un  seul en stock', 0, 0, '2025-06-22 21:32:55', '2025-06-23 10:57:58'),
(22, 'Cravate Calvin Klein', 'Cravates', 'Cravate en soie bleu ciel - Calvin Klein', '[\"uploads\\/6859bb1ebe56e_6c824468-cd78-491b-a7a5-6ca82f2d99c9.jpeg\",\"uploads\\/6859bb1ebebe9_316bc5b0-9ce0-4d5a-8f4f-9ace061ad25f.jpeg\"]', '205.00', 0, 0, 'disponible en deux couleurs', 0, 0, '2025-06-23 20:37:50', '2025-06-25 13:20:17'),
(23, 'Sandale Tommy', 'Sandales', 'Sandale Tommy couleur rose', '[\"uploads\\/6859bbdb175b6_eb6749a7-0b8b-45ff-a206-837f8b27e364.jpeg\",\"uploads\\/6859bbdb1787e_d9345152-6751-4da1-b86c-9057727eaf41.jpeg\",\"uploads\\/6859bbdb17a62_80aaace4-7051-4e44-ab4a-d0e62d89f15b.jpeg\"]', '399.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-23 20:40:59', '2025-06-25 13:15:37'),
(25, 'Ti-shirt Karl lagerfeld ', 'Vêtements', 'Ti shirt Karl lagerfeld pour homme Taille S', '[\"uploads\\/6859c191efc47_976923f2-c801-446e-aa50-e42089c6ef80.jpeg\",\"uploads\\/6859c191f0073_883a9b8f-23ee-4e6e-93b5-a1f93b8e942d.jpeg\",\"uploads\\/6859c191f044a_6be0d607-16f4-4617-9e44-df63bcb4d029.jpeg\"]', '255.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-23 21:05:21', '2025-06-25 13:12:20'),
(26, 'Sac Tommy', 'Sacs ', 'Sac à bandoulière et à rayures Signature', '[\"uploads\\/6859c21a392a4_d1998530-2cb0-4b3e-975c-b61359d424df.jpeg\",\"uploads\\/6859c21a39557_b80e2848-2665-4a01-ba66-440d4bec29fd.jpeg\",\"uploads\\/6859c21a397eb_4db97478-a5ca-4ab8-ab92-dce5900939cd.jpeg\"]', '345.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:07:38', '2025-06-23 21:07:38'),
(28, 'Sac bandoulière Tommy', 'Sacs', 'Tommy Hilfiger – Sac bandoulière imprimé pour femme', '[\"uploads\\/6859c421a8995_a11afa83-bdfc-4404-afaa-9c5d73ed5dab.jpeg\",\"uploads\\/6859c421a8d0e_fa77e3dc-1f8a-4d93-9e08-0d26641aa98f.jpeg\",\"uploads\\/6859c421a8fb7_a9241a83-3046-4676-8036-1b8766debb55.jpeg\"]', '355.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:16:17', '2025-06-23 21:16:17'),
(29, 'Chemise Carl lagerfeld ', 'Vêtements', 'Chemise Karl lagerfeld pour femme taille ', '[\"uploads\\/6859c500eeafc_5b807bc0-0c57-4636-af7c-31042f3d39fd.jpeg\",\"uploads\\/6859c500eef35_458c1b95-3406-4f8a-9dc5-f171fff12ec2.jpeg\",\"uploads\\/6859c500ef343_db92c40b-3d36-4b80-9f8b-d0f8d37c4848.jpeg\",\"uploads\\/6859c500ef66a_d736ab1d-475b-46db-8ff5-00422145605d.jpeg\"]', '350.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:20:00', '2025-06-25 13:11:45'),
(30, 'Cravate Micheal kors', 'Cravates', 'Cravate Micheal kors', '[\"uploads\\/6859c5b0d46c0_0c216119-3dd1-4df2-b531-863dcb000e0f.jpeg\",\"uploads\\/6859c5b0d49b4_dc0d1c79-9a48-4438-9c2b-4b5760b093ce.jpeg\"]', '205.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-23 21:22:56', '2025-06-23 21:22:56'),
(31, 'Portefeuille Tommy ', 'Portefeuilles', 'Portefeuille Tommy pour homme', '[\"uploads\\/6859c65682359_8941f5cc-27f4-4276-93ad-d7b0bdeba5ad.jpeg\",\"uploads\\/6859c656825d3_2a5d1cff-0a80-44a4-9afa-b197b88be31a.jpeg\",\"uploads\\/6859c656827f1_e421952a-3225-4dde-a87d-c24852027f9d.jpeg\"]', '199.00', 0, 0, 'Un seul  en stock', 0, 0, '2025-06-23 21:25:42', '2025-06-23 21:25:42'),
(32, 'Ceinture Calvinklein pour homme', 'Ceintures', 'Ceinture CK pour homme ', '[\"uploads\\/6859c72ccb7a5_e518e23c-f6ad-44df-8f04-22a2a38e0c35.jpeg\",\"uploads\\/6859c72ccb9ec_e1145636-beaa-4907-a110-bb35050b4fce.jpeg\",\"uploads\\/6859c72ccbc40_547ab20b-dc9e-4f40-b699-3ef2c76969e2.jpeg\",\"uploads\\/6859c72ccbe04_c4d36cea-311d-40fe-aff5-303b7b9d84e9.jpeg\"]', '199.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:29:16', '2025-06-23 21:29:16'),
(33, 'Ceinture guess pour Homme ', 'Ceintures', 'Ceinture guess pour homme', '[\"uploads\\/6859c79358d1d_dd58af6f-31ca-4f38-853a-69312a723555.jpeg\",\"uploads\\/6859c79358f2c_a413d8f0-3ebe-4286-aa31-8050936c3317.jpeg\"]', '199.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:30:59', '2025-06-25 13:16:35'),
(34, 'Sac Karl lagerfeld ', 'Sacs ', 'Sac Karl lagerfeld pour femme ', '[\"uploads\\/6859c81a1b928_ff633d16-aeb2-4381-9de9-f03bd2f0b471.jpeg\",\"uploads\\/6859c81a1bd98_57acd21c-a245-4db5-89c2-4a6381f95710.jpeg\",\"uploads\\/6859c81a1c079_47914951-3d85-45f9-821f-386c3da63b16.jpeg\",\"uploads\\/6859c81a1c36e_ccf798f1-2c54-497d-a1e7-eafa55faaa90.jpeg\"]', '899.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:33:14', '2025-06-23 21:33:14'),
(35, 'Casquette Karl lagerfeld Paris ', 'Casquettes', 'Casquette Karl lagerfeld réglable ', '[\"uploads\\/6859c89fb29ba_1a2e5786-6efc-4f8b-afb6-f5718e06d46d.jpeg\",\"uploads\\/6859c89fb2c7b_21943488-27a5-4486-bf0c-52b53e7b4939.jpeg\"]', '199.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-23 21:35:27', '2025-06-23 21:35:27'),
(36, 'Sac Tommy pour femme ', 'Sacs ', 'Tommy Hilfiger – Sac à main texturé beige pour femme', '[\"uploads\\/685be7d15982c_43cfd060-d7d6-4910-98cd-cd74be96393f.jpeg\",\"uploads\\/685be7d159bdf_4a2f1664-4b5a-43b1-b098-619d59620bdb.jpeg\"]', '399.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:13:05', '2025-06-25 12:13:05'),
(37, 'Sac guess pour femme ', 'Sacs', 'Sac guess pour femme ', '[\"uploads\\/685be88c7f641_9a910329-ece8-4086-980f-bad9fb2b3150.jpeg\",\"uploads\\/685be88c7f996_09782798-95bd-4f9f-ab49-c382a954714a.jpeg\"]', '499.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:16:12', '2025-06-25 12:16:12'),
(38, 'Ceinture Tommy pour femme', 'Ceintures ', 'Ceinture Tommy pour femme  taille M ', '[\"uploads\\/685be9b65c1a7_37d9df62-901f-4d65-8544-5c8bc81afafc.jpeg\",\"uploads\\/685be9b65c4af_fc868ab8-5954-457b-9859-6b4e5c5acded.jpeg\",\"uploads\\/685be9b65c74b_bc44eb6d-dae2-4ffb-97d1-5b3a1c6451e9.jpeg\"]', '199.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:21:10', '2025-06-25 12:21:10'),
(39, 'Écharpe Karl lagerfeld ', 'Écharpes', 'Écharpe Karl Lagerfeld Paris – Design graphique noir et blanc', '[\"uploads\\/685bead1734aa_15efd2c2-5dbd-41f9-a542-4774040b9ac3.jpeg\",\"uploads\\/685bead173859_d271b05a-eb46-46cb-9638-b0b98b5b427e.jpeg\"]', '170.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:25:53', '2025-06-25 12:25:53'),
(40, 'Ti shirt Karl lagerfeld ', 'Vêtements', 'T-shirt Karl Lagerfeld Paris – Illustration Love from Paris  taille L\r\n\r\n', '[\"uploads\\/685bebdbdf5c1_3e3af589-a66b-47d9-a0c5-41a17b787121.jpeg\"]', '170.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:30:19', '2025-06-25 13:12:44'),
(41, 'Sac Karl lagerfeld pour femme', 'Sacs', 'Sac bandoulière Karl Lagerfeld Paris – Édition pastel avec Karl & Choupette', '[\"uploads\\/685becd3320a3_ad2aae6d-0d40-475f-b04e-a00cd9f070ac.jpeg\",\"uploads\\/685becd332654_1c3b7376-3410-4740-b04d-8ebcb1af4560.jpeg\",\"uploads\\/685becd3328ae_8637c3f4-ea01-4c27-9a3d-471be09fee7f.jpeg\"]', '750.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:34:27', '2025-06-25 12:34:27'),
(42, 'Sac Tommy marron pour femme', 'Sacs', 'Sac à main pour femme Tommy Hilfiger – Couleur marron', '[\"uploads\\/685bee04322a8_0c54bee6-6387-4ddc-aa92-0102394361cf.jpeg\",\"uploads\\/685bee0432597_8594e845-929d-4e3b-ba74-be698b29b629.jpeg\",\"uploads\\/685bee0432802_1067acf5-1d5a-49e7-9026-4ff1060bed8b.jpeg\"]', '450.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 12:39:32', '2025-06-25 12:39:32'),
(43, 'Sac  US polo assn ', 'Sacs', 'Le sac à bandoulière classique zippé de US Polo Assn', '[\"uploads\\/685beea62bc4a_009fbc33-1238-47f4-a2e0-d9746e714271.jpeg\",\"uploads\\/685beea62bf46_b1b926c9-14da-426c-a1a3-2c081dbec152.jpeg\"]', '355.00', 0, 0, 'Disponible en deux couleurs ', 0, 0, '2025-06-25 12:42:14', '2025-06-25 12:42:14'),
(44, 'Casquette Tommy ', 'Casquettes', 'Casquette Tommy réglable ', '[\"uploads\\/685bf627bf675_fbdab5c5-8319-4053-a112-cb2f88fb5e91.jpeg\",\"uploads\\/685bf627bfac1_c368fa7d-a220-4a3e-9613-fd2b76f3a3bf.jpeg\"]', '129.00', 0, 0, 'Seulement deux en stock ', 0, 0, '2025-06-25 13:14:15', '2025-06-25 13:14:15'),
(45, 'Ti shirt Tommy', 'Vêtements', 'Ti shirt Tommy blanc taille S', '[\"uploads\\/685bf7dd45631_1d24fbe5-188d-4e88-806a-acc1d6e336f6.jpeg\",\"uploads\\/685bf7dd4594b_ce193d1c-65f0-4859-8af8-f646db2cf51f.jpeg\"]', '160.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-25 13:21:33', '2025-06-25 13:30:59'),
(46, 'Sac Tommy ', 'Sacs', 'Sac bandoulière uni bleu en polyester Tommy Hilfiger ', '[\"uploads\\/685bf9e105f18_5c9a0087-7779-4d4a-be58-d341c2ce12c3.jpeg\",\"uploads\\/685bf9e106e81_5d12b78a-944b-400a-a553-487701b6d751.jpeg\",\"uploads\\/685bf9e1070f6_0a804408-ff39-47c7-a311-60eee130af8f.jpeg\"]', '399.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-25 13:30:09', '2025-06-25 13:30:09'),
(47, 'Portefeuille Tommy', 'Portefeuilles', 'Portefeuille Tommy', '[\"uploads\\/685bfaa7007f2_c48a69c4-d775-47cc-9a36-13676123be40.jpeg\",\"uploads\\/685bfaa700c72_baf13c49-940b-44e7-8da0-b08c5efa901d.jpeg\",\"uploads\\/685bfaa701069_6e7fac83-8a48-4fcb-b1a3-0ca99da56fad.jpeg\"]', '129.00', 0, 0, 'Un seul en stock', 0, 0, '2025-06-25 13:33:27', '2025-06-25 13:33:27'),
(48, 'Portefeuille guess', 'Portefeuilles', 'Portefeuille  guess noir ', '[\"uploads\\/685bfb20a6d5c_2d069859-038b-454e-b14d-404d5437f52d.jpeg\",\"uploads\\/685bfb20a70ca_2255d8db-fa8e-47b7-9128-e7d179b93c23.jpeg\"]', '199.00', 0, 0, 'Un seul en stock ', 0, 0, '2025-06-25 13:35:28', '2025-06-25 13:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`product_id`, `color_id`) VALUES
(1, 1),
(3, 1),
(4, 1),
(1, 2),
(1, 3),
(3, 4),
(4, 4),
(2, 8),
(2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`product_id`, `size_id`) VALUES
(1, 2),
(1, 3),
(2, 3),
(4, 3),
(1, 4),
(2, 4),
(2, 5),
(3, 9),
(3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `name`, `created_at`) VALUES
(1, 'XS', '2025-06-21 18:37:49'),
(2, 'S', '2025-06-21 18:37:49'),
(3, 'M', '2025-06-21 18:37:49'),
(4, 'L', '2025-06-21 18:37:49'),
(5, 'XL', '2025-06-21 18:37:49'),
(6, 'XXL', '2025-06-21 18:37:49'),
(7, '36', '2025-06-21 18:37:49'),
(8, '38', '2025-06-21 18:37:49'),
(9, '40', '2025-06-21 18:37:49'),
(10, '42', '2025-06-21 18:37:49'),
(11, '44', '2025-06-21 18:37:49'),
(12, '46', '2025-06-21 18:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `metric` varchar(50) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `metric`, `value`, `updated_at`) VALUES
(1, 'total_sales', '9763.00', '2025-06-26 12:02:39'),
(2, 'total_orders', '13.00', '2025-06-26 12:02:39'),
(3, 'total_products_sold', '21.00', '2025-06-26 12:02:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `clothing_types`
--
ALTER TABLE `clothing_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clothing_type_id` (`clothing_type_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`product_id`,`color_id`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`product_id`,`size_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `metric` (`metric`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `clothing_types`
--
ALTER TABLE `clothing_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`clothing_type_id`) REFERENCES `clothing_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_colors_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
