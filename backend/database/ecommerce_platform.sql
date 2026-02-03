-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2026 at 12:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#064E3B',
  `parent_id` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `store_id`, `name`, `slug`, `description`, `icon`, `color`, `parent_id`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Electronics', 'electronics', NULL, 'devices', '#064e3b', NULL, 0, 'active', '2026-01-28 09:10:32', '2026-01-28 09:34:56'),
(2, 1, 'computer', 'computer', NULL, 'laptop', '#064e3b', NULL, 0, 'active', '2026-01-28 09:10:32', '2026-01-28 09:35:08'),
(4, 1, 'Fashion', 'fashion', 'Clothes', 'checkroom', '#064e3b', NULL, 0, 'active', '2026-01-28 09:17:24', '2026-01-28 09:34:26'),
(5, 3, 'Furniture', 'furniture', 'This includes office furniture', 'chair', '#064e3b', NULL, 0, 'active', '2026-01-29 12:06:33', '2026-01-29 12:06:33'),
(6, 3, 'Computers', 'computers', 'Computer accessories', 'laptop', '#064e3b', NULL, 0, 'active', '2026-01-29 12:07:26', '2026-01-29 12:07:26'),
(7, 3, 'Internet', 'internet', 'Internet accessories', 'phone_android', '#064e3b', NULL, 0, 'active', '2026-01-29 12:08:20', '2026-01-29 12:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subscription_plan` enum('basic','standard','premium') DEFAULT 'basic',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password`, `company_name`, `phone`, `subscription_plan`, `status`, `created_at`) VALUES
(1, 'Adedamola', 'gideontate58@gmail.com', '$2y$10$qfBm4cpwt3Jr7w1uh8M8reMCTGusvQShzUxGkuSXTBM7go4R4mPMy', 'Prodevx  Tech', '+445754324523', 'basic', 'active', '2026-01-26 09:10:27'),
(3, 'Adedamola Olawale', 'de@gn.co', '$2y$10$kljuskLceuHx2MyO7RMNyOAqXEbLFOInXOMDdKVAD.WYAVeUf9Yly', 'jdvnjcd', '45676543', 'standard', 'active', '2026-01-26 13:07:59'),
(4, 'Olawale Abraham', 'gnan2ugonsa@gmail.com', '$2y$10$hzBQH0unM4h9duLXzpyTe.b/F5qtwSaodEBb1qLcscmSeVeqcC.I2', 'Ugonsa', '08168082347', 'premium', 'active', '2026-01-26 13:39:54'),
(5, 'Olawale Abraham', 'devabrahamtech@gmail.com', '$2y$10$.yAnz0YTCPlnGjr3thY7OuCUQjjryN1QgA82ac/cWy7b12PPnBKo2', 'Livepetals', '08168082347', 'standard', 'active', '2026-01-29 11:32:33'),
(6, 'John Doe', 'john@example.com', '$2y$10$aMANiE2Rqd57xy7KbusQiu/uH7BF8HO9kp5ODZSUUMzv9nkh1VaK2', 'Acme Inc', '+1234567890', 'basic', 'active', '2026-01-30 10:33:01'),
(7, 'Daniel John', 'dj@gmail.com', '$2y$10$u9l07GIo3.zQpvduLXnqFukc1CAuiyqqxxdy/n.ENS/UjX5iRuWLK', 'Daniel Gadgets', '234678', 'standard', 'active', '2026-02-02 15:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_type` enum('shipping','billing','both') DEFAULT 'shipping',
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Nigeria',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Customer shipping and billing addresses';

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `customer_id`, `address_type`, `full_name`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 6, 'shipping', NULL, NULL, '', NULL, 'Ila Odo', 'Lagos', '340283', 'Nigeria', 1, '2026-02-03 10:22:12', '2026-02-03 10:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT 'Nigeria',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('cash_on_delivery','bank_transfer','card','wallet') DEFAULT 'cash_on_delivery',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_gateway` varchar(50) DEFAULT 'paystack',
  `payment_verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `store_id`, `customer_id`, `shipping_address_id`, `billing_address_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_postal_code`, `shipping_country`, `total_amount`, `shipping_cost`, `payment_method`, `payment_status`, `order_notes`, `tracking_number`, `status`, `created_at`, `payment_reference`, `payment_gateway`, `payment_verified_at`) VALUES
(10, 1, 6, NULL, NULL, 'John Doe', 'devabrahamtech@gmail.com', '+1234567890', '123 Main St, City, Country', NULL, NULL, NULL, 'Nigeria', 199.99, 0.00, '', 'pending', NULL, NULL, 'pending', '2026-02-03 10:41:24', NULL, 'paystack', NULL),
(11, 1, 6, NULL, NULL, 'Olawale Abraham', 'devabrahamtech@gmail.com', '08168082347', 'Pleasure', 'Alimosho', 'Lagos', '340283', 'Nigeria', 598125.00, 1500.00, 'card', 'pending', NULL, NULL, 'pending', '2026-02-03 10:46:13', NULL, 'paystack', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(7, 11, 2, 1, 555000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `store_id`, `category_id`, `name`, `description`, `price`, `category`, `image_url`, `stock_quantity`, `status`, `created_at`) VALUES
(1, 1, 4, 'Signature Headset', 'An modern headset', 250000.00, 'Electronics', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsd8KR7ghpIS5S-WLE2AdJCCHB3kDl28s84Q&s', 15, 'active', '2026-01-26 21:55:54'),
(2, 1, 2, 'curved monitor', 'a curved monitor', 555000.00, 'computer', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgZ59cy2RTK5gPYdM-3uc4dHT6--eznOSjXQ&s', 25, 'active', '2026-01-26 23:14:16'),
(3, 1, 1, 'hujwbnqw', 'qshyuqj', 123.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsd8KR7ghpIS5S-WLE2AdJCCHB3kDl28s84Q&s,https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsd8KR7ghpIS5S-WLE2AdJCCHB3kDl28s84Q&s,,,', 210, 'active', '2026-01-28 09:49:48'),
(4, 3, 5, 'Office Table', 'This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table, This is  a modern office table,.', 224000.00, NULL, NULL, 40, 'active', '2026-01-29 11:47:46'),
(5, 3, 5, 'Office Chair', 'This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, This is  a modern office chair, .', 250000.00, NULL, NULL, 34, 'active', '2026-01-29 11:51:12'),
(6, 3, 6, 'Desktop Computers', 'These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers, These are desktop computers.', 450000.00, NULL, NULL, 25, 'active', '2026-01-29 12:12:57'),
(7, 3, 6, 'Curved Monitor', 'This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, This is  a curved monitor, .', 420000.00, NULL, NULL, 44, 'active', '2026-01-29 12:17:39'),
(8, 4, NULL, 'A Mac Laptop', 'A gaming laptop', 450000.00, NULL, NULL, 24, 'active', '2026-02-02 15:24:55'),
(9, 4, NULL, 'Office chairs', 'An office chair', 120000.00, NULL, NULL, 35, 'active', '2026-02-02 15:26:41');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`) VALUES
(1, 2, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769609244/products/store-1/rfvf4ra9e90mxfucsqyx.png', 1),
(2, 2, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769609257/products/store-1/mu568jl07vsaowricsge.jpg', 0),
(3, 2, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769609265/products/store-1/a85dqinbnu3n5kf6w69n.jpg', 0),
(19, 4, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687217/products/store-3/fmphmgvvzycydyppgg8k.jpg', 1),
(20, 4, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687228/products/store-3/kv0ma9xt4ctkafbmfti1.jpg', 0),
(21, 4, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687237/products/store-3/i85jwlps0cgrpzxseunv.jpg', 0),
(22, 4, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687247/products/store-3/tqvijdur8yoxo9wzzclr.jpg', 0),
(23, 4, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687256/products/store-3/t5azti80axez1k7mle7i.jpg', 0),
(24, 6, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688719/products/store-3/lhmgioasaecxhwc1npka.jpg', 1),
(25, 6, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688731/products/store-3/gqaqd5ldhgukf8amgtoz.jpg', 0),
(26, 6, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688742/products/store-3/z9na4iglnu5evrcggxm0.jpg', 0),
(27, 6, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688751/products/store-3/zrqrcofvzxpj3ogibn33.jpg', 0),
(28, 6, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688763/products/store-3/s4bsoomrik5msf7zwo0m.jpg', 0),
(29, 7, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688957/products/store-3/uxlfv5tdfycuepibrep4.jpg', 1),
(30, 7, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688966/products/store-3/f51wz0h5s0rrveojltvl.jpg', 0),
(31, 7, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688975/products/store-3/wvryezkljq1aau0jctys.jpg', 0),
(32, 7, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769688990/products/store-3/tsq1lhjky9tnahmbu24t.jpg', 0),
(33, 7, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769689007/products/store-3/fl6cnn3rdglzldmkchud.jpg', 0),
(34, 5, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687416/products/store-3/wz2qjqib4z2l9nklss4c.jpg', 1),
(35, 5, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687424/products/store-3/lvl0sotkhhw8cr1fvwl3.jpg', 0),
(36, 5, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687433/products/store-3/setfoiaiuiaja8eeciyl.jpg', 0),
(37, 5, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687443/products/store-3/ckguqx3blrnkj5kmgf65.jpg', 0),
(38, 5, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1769687452/products/store-3/vwqnkw88mkx3rnenmuv9.jpg', 0),
(39, 8, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045856/products/store-4/zboe28bxzd6evwdujgrm.jpg', 1),
(40, 8, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045866/products/store-4/i53pnaehekt7iam9u84z.jpg', 0),
(41, 8, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045876/products/store-4/t0ilwdkh8sj0no3bakoh.jpg', 0),
(42, 9, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045964/products/store-4/fqa38lpipst19ttfmc1h.jpg', 1),
(43, 9, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045973/products/store-4/l8vixoatnnxq2tprupvq.jpg', 0),
(44, 9, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045983/products/store-4/jqt7jatjqm4vk7eeyvyi.jpg', 0),
(45, 9, 'https://res.cloudinary.com/dcxknkwjn/image/upload/v1770045993/products/store-4/lhajjxdkjh3mvrdxqlz2.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Persistent shopping cart items';

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `store_slug` varchar(100) NOT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `template_id` int(11) DEFAULT 1,
  `primary_color` varchar(7) DEFAULT '#064E3B',
  `accent_color` varchar(7) DEFAULT '#BEF264',
  `logo_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `group_by_category` tinyint(1) DEFAULT 0,
  `show_category_images` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tagline` varchar(255) DEFAULT NULL,
  `hero_background_url` varchar(255) DEFAULT NULL,
  `header_style` enum('default','centered','minimal') DEFAULT 'default',
  `product_grid_columns` int(11) DEFAULT 4,
  `font_family` varchar(50) DEFAULT 'Plus Jakarta Sans',
  `button_style` enum('rounded','square','pill') DEFAULT 'rounded',
  `show_search` tinyint(1) DEFAULT 1,
  `show_cart` tinyint(1) DEFAULT 1,
  `show_wishlist` tinyint(1) DEFAULT 0,
  `footer_text` text DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_twitter` varchar(255) DEFAULT NULL,
  `custom_css` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paystack_public_key` varchar(255) DEFAULT NULL,
  `paystack_secret_key` varchar(255) DEFAULT NULL,
  `payment_enabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `client_id`, `store_name`, `store_slug`, `domain`, `template_id`, `primary_color`, `accent_color`, `logo_url`, `description`, `status`, `group_by_category`, `show_category_images`, `created_at`, `tagline`, `hero_background_url`, `header_style`, `product_grid_columns`, `font_family`, `button_style`, `show_search`, `show_cart`, `show_wishlist`, `footer_text`, `social_facebook`, `social_instagram`, `social_twitter`, `custom_css`, `updated_at`, `paystack_public_key`, `paystack_secret_key`, `payment_enabled`) VALUES
(1, 1, 'Prodevx Tech Shop', 'prodevx-tech-hub', 'prodev.hub', 3, '#35e212', '#d3853c', 'https://tolkzspeakers.com/assets/images/logo.png', 'Your first stop for tech gadgets and merchs', 'active', 1, 1, '2026-01-26 14:08:23', 'We are the best', 'https://images.unsplash.com/photo-1664455340023-214c33a9d0bd?q=80&w=1332&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'minimal', 3, 'Plus Jakarta Sans', 'square', 1, 1, 0, '', 'fb.com', 'ig.com', 'x.com', '', '2026-01-29 15:53:43', NULL, NULL, 0),
(2, 4, 'techhub', 'jyugwhdvy', 'ywgxbs', 1, '#064e3b', '#63f2bc', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAJQAlAMBEQACEQEDEQH/', 'HN CHYGCSHBN SAU', 'active', 0, 1, '2026-01-27 15:25:08', 'THE BEST EVER', 'https://images.unsplash.com/photo-1688561808434-886a6dd97b8c?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'default', 4, 'Plus Jakarta Sans', 'rounded', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, '2026-01-28 07:59:41', NULL, NULL, 0),
(3, 5, 'Livepetals Hub', 'livepetals-hub', 'livepetals.hub', 4, '#064e3b', '#bef264', NULL, 'This is a store for livepetals to showcase their products.', 'active', 1, 1, '2026-01-29 11:35:43', NULL, 'https://images.unsplash.com/photo-1605902711622-cfb43c4437b5?q=80&w=1169&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'default', 4, 'Plus Jakarta Sans', 'rounded', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, '2026-01-29 15:27:00', NULL, NULL, 0),
(4, 7, 'laptops-hub', 'laptops-hub', 'laptops.com', 5, '#07064c', '#ecb336', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJQAAACUCAMAAABC4vDmAAABTVBMVEX///+RKipzMTF3MDCALi6cKCiYKSl6Ly+GLS2UKiqfJyeNKyvQAKOILCxsAL7i29twMzNlGhp0ALikJiZ9ALf9+v2vAKmsJCSJALW/AKTout+oJSWzIyOmAKy4AK2XALH88fnEAKBgALn66vbl1fD13vDYwujVueSZW8mfZsvp3/Tw6Pipg', 'We see the best laptops', 'active', 0, 1, '2026-02-02 15:16:28', NULL, 'https://images.unsplash.com/photo-1487014679447-9f8336841d58?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fGVjb21tZXJjZXxlbnwwfHwwfHx8MA%3D%3D', 'default', 4, 'Plus Jakarta Sans', 'rounded', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, '2026-02-02 15:20:40', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `store_customers`
--

CREATE TABLE `store_customers` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'NULL for guest customers',
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_guest` tinyint(1) DEFAULT 0 COMMENT 'TRUE for guest checkout, FALSE for registered',
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','blocked') DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Store-specific customer accounts';

--
-- Dumping data for table `store_customers`
--

INSERT INTO `store_customers` (`id`, `store_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_guest`, `email_verified`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(6, 1, 'devabrahamtech@gmail.com', '$2y$10$OHQBPB1yNgxcninkuxRWQuJ/uS103NR/9Nh1arQlLXOZxejmwEmHO', 'Olawale', 'Abraham', '08168082347', 0, 0, 'active', '2026-02-03 10:45:43', '2026-02-03 10:21:27', '2026-02-03 10:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `store_navigation`
--

CREATE TABLE `store_navigation` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_sections`
--

CREATE TABLE `store_sections` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `section_type` enum('hero','featured_products','categories','testimonials','newsletter','custom') NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `background_color` varchar(7) DEFAULT NULL,
  `text_color` varchar(7) DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_templates`
--

CREATE TABLE `store_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `html_template` longtext DEFAULT NULL,
  `css_template` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_templates`
--

INSERT INTO `store_templates` (`id`, `name`, `description`, `preview_image`, `html_template`, `css_template`, `created_at`) VALUES
(1, 'CampMart Style', 'Modern marketplace design with bold colors and clean layout inspired by campus commerce', '/assets/templates/campmart-preview.jpg', '<!DOCTYPE html>\r\n<html class=\"light\" lang=\"en\">\r\n<head>\r\n    <meta charset=\"utf-8\"/>\r\n    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\r\n    <title>{{store_name}} | Custom Ecommerce Store</title>\r\n    <script src=\"https://cdn.tailwindcss.com?plugins=forms,container-queries\"></script>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap\" rel=\"stylesheet\"/>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\"/>\r\n    <script>\r\n        tailwind.config = {\r\n            darkMode: \"class\",\r\n            theme: {\r\n                extend: {\r\n                    colors: {\r\n                        \"primary\": \"{{primary_color}}\",\r\n                        \"accent\": \"{{accent_color}}\",\r\n                        \"surface\": \"#F8FAFC\",\r\n                    },\r\n                    fontFamily: {\r\n                        \"display\": [\"Plus Jakarta Sans\", \"sans-serif\"]\r\n                    },\r\n                },\r\n            },\r\n        }\r\n    </script>\r\n</head>\r\n<body class=\"bg-white font-display text-slate-900 antialiased\">\r\n    <!-- Header -->\r\n    <header class=\"sticky top-0 z-50 bg-white border-b border-slate-100\">\r\n        <div class=\"max-w-[1440px] mx-auto px-6 h-20 flex items-center justify-between gap-8\">\r\n            <a class=\"flex items-center gap-2.5 shrink-0\" href=\"/\">\r\n                <div class=\"size-10 flex items-center justify-center bg-primary text-accent rounded-lg shadow-inner\">\r\n                    <span class=\"material-symbols-outlined font-bold text-2xl\">shopping_bag</span>\r\n                </div>\r\n                <span class=\"text-2xl font-extrabold tracking-tight text-primary uppercase\">{{store_name}}</span>\r\n            </a>\r\n            \r\n            <div class=\"hidden lg:flex flex-1 max-w-2xl items-center bg-slate-50 rounded-full border border-slate-200 p-1\">\r\n                <div class=\"flex-1 flex items-center px-4\">\r\n                    <span class=\"material-symbols-outlined text-slate-400 mr-2\">search</span>\r\n                    <input class=\"w-full bg-transparent border-none focus:ring-0 text-sm placeholder:text-slate-400\" placeholder=\"Search products...\" type=\"text\"/>\r\n                </div>\r\n                <button class=\"bg-primary text-white p-2 rounded-full mr-1\">\r\n                    <span class=\"material-symbols-outlined text-xl\">search</span>\r\n                </button>\r\n            </div>\r\n            \r\n            <div class=\"flex items-center gap-2\">\r\n                <button class=\"p-2.5 text-slate-500 hover:text-primary hover:bg-slate-50 rounded-full relative\">\r\n                    <span class=\"material-symbols-outlined\">shopping_cart</span>\r\n                    <span class=\"absolute top-2.5 right-2.5 size-2 bg-accent rounded-full border-2 border-white\"></span>\r\n                </button>\r\n                <button class=\"p-2.5 text-slate-500 hover:text-primary hover:bg-slate-50 rounded-full\">\r\n                    <span class=\"material-symbols-outlined\">person</span>\r\n                </button>\r\n            </div>\r\n        </div>\r\n    </header>\r\n\r\n    <main>\r\n        <!-- Hero Section -->\r\n        <section class=\"relative bg-primary min-h-[500px] flex items-center justify-center overflow-hidden\">\r\n            <div class=\"absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/40 to-primary/80\"></div>\r\n            <div class=\"relative z-10 text-center px-6 max-w-4xl mx-auto\">\r\n                <h1 class=\"text-5xl md:text-7xl font-extrabold text-white leading-none mb-8\">\r\n                    Welcome to <br/><span class=\"text-accent underline decoration-4 underline-offset-8\">{{store_name}}</span>\r\n                </h1>\r\n                <p class=\"text-xl md:text-2xl text-slate-100/90 mb-12 max-w-2xl mx-auto font-light\">\r\n                    {{store_description}}\r\n                </p>\r\n                <div class=\"flex flex-col sm:flex-row items-center justify-center gap-6\">\r\n                    <button class=\"bg-accent text-primary px-12 py-4 rounded-lg font-bold hover:brightness-105 transition-all text-lg w-full sm:w-auto\">\r\n                        Shop Now\r\n                    </button>\r\n                    <button class=\"bg-white/10 text-white border border-white/20 px-12 py-4 rounded-lg font-bold hover:bg-white/20 backdrop-blur-md transition-all text-lg w-full sm:w-auto\">\r\n                        Learn More\r\n                    </button>\r\n                </div>\r\n            </div>\r\n        </section>\r\n\r\n        <div class=\"max-w-[1440px] mx-auto px-6 py-12\">\r\n            <!-- Categories -->\r\n            <section class=\"mb-20\">\r\n                <h2 class=\"text-3xl font-extrabold text-primary mb-8\">Shop by Category</h2>\r\n                <div class=\"flex gap-4 overflow-x-auto pb-4\">\r\n                    <div class=\"flex flex-col items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-white hover:border-primary hover:shadow-md transition-all cursor-pointer min-w-[140px]\">\r\n                        <div class=\"size-14 rounded-full bg-slate-50 flex items-center justify-center text-primary\">\r\n                            <span class=\"material-symbols-outlined text-3xl\">devices</span>\r\n                        </div>\r\n                        <span class=\"text-sm font-bold\">Electronics</span>\r\n                    </div>\r\n                    <div class=\"flex flex-col items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-white hover:border-primary hover:shadow-md transition-all cursor-pointer min-w-[140px]\">\r\n                        <div class=\"size-14 rounded-full bg-slate-50 flex items-center justify-center text-primary\">\r\n                            <span class=\"material-symbols-outlined text-3xl\">apparel</span>\r\n                        </div>\r\n                        <span class=\"text-sm font-bold\">Fashion</span>\r\n                    </div>\r\n                    <div class=\"flex flex-col items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-white hover:border-primary hover:shadow-md transition-all cursor-pointer min-w-[140px]\">\r\n                        <div class=\"size-14 rounded-full bg-slate-50 flex items-center justify-center text-primary\">\r\n                            <span class=\"material-symbols-outlined text-3xl\">chair</span>\r\n                        </div>\r\n                        <span class=\"text-sm font-bold\">Furniture</span>\r\n                    </div>\r\n                </div>\r\n            </section>\r\n\r\n            <!-- Featured Products -->\r\n            <section class=\"mb-20\">\r\n                <div class=\"flex items-center justify-between mb-8\">\r\n                    <h2 class=\"text-3xl font-extrabold text-primary\">Featured Products</h2>\r\n                    <a href=\"/products\" class=\"text-primary font-semibold hover:underline\">View All</a>\r\n                </div>\r\n                <div id=\"products-container\">\r\n                    <!-- Products will be loaded here -->\r\n                </div>\r\n            </section>\r\n        </div>\r\n    </main>\r\n\r\n    <!-- Footer -->\r\n    <footer class=\"bg-slate-900 text-white pt-16 pb-8\">\r\n        <div class=\"max-w-[1440px] mx-auto px-6\">\r\n            <div class=\"grid grid-cols-1 md:grid-cols-4 gap-8 mb-12\">\r\n                <div>\r\n                    <div class=\"flex items-center gap-2 mb-6\">\r\n                        <div class=\"size-8 bg-accent text-primary rounded-lg flex items-center justify-center\">\r\n                            <span class=\"material-symbols-outlined text-lg font-bold\">shopping_bag</span>\r\n                        </div>\r\n                        <span class=\"text-xl font-extrabold text-white uppercase\">{{store_name}}</span>\r\n                    </div>\r\n                    <p class=\"text-slate-300 text-sm leading-relaxed\">\r\n                        {{store_description}}\r\n                    </p>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent text-lg mb-4\">Quick Links</h4>\r\n                    <ul class=\"space-y-2 text-sm text-slate-400\">\r\n                        <li><a href=\"/\" class=\"hover:text-white transition-colors\">Home</a></li>\r\n                        <li><a href=\"/products\" class=\"hover:text-white transition-colors\">Products</a></li>\r\n                        <li><a href=\"/about\" class=\"hover:text-white transition-colors\">About</a></li>\r\n                        <li><a href=\"/contact\" class=\"hover:text-white transition-colors\">Contact</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent text-lg mb-4\">Support</h4>\r\n                    <ul class=\"space-y-2 text-sm text-slate-400\">\r\n                        <li><a href=\"/help\" class=\"hover:text-white transition-colors\">Help Center</a></li>\r\n                        <li><a href=\"/shipping\" class=\"hover:text-white transition-colors\">Shipping Info</a></li>\r\n                        <li><a href=\"/returns\" class=\"hover:text-white transition-colors\">Returns</a></li>\r\n                        <li><a href=\"/privacy\" class=\"hover:text-white transition-colors\">Privacy Policy</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent text-lg mb-4\">Connect</h4>\r\n                    <div class=\"flex gap-4\">\r\n                        <a href=\"#\" class=\"w-8 h-8 bg-white/10 rounded-full flex items-center justify-center text-slate-300 hover:text-accent transition-colors\">\r\n                            <span class=\"material-symbols-outlined text-sm\">public</span>\r\n                        </a>\r\n                        <a href=\"#\" class=\"w-8 h-8 bg-white/10 rounded-full flex items-center justify-center text-slate-300 hover:text-accent transition-colors\">\r\n                            <span class=\"material-symbols-outlined text-sm\">alternate_email</span>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <div class=\"pt-8 border-t border-white/10 text-center\">\r\n                <p class=\"text-xs text-slate-500\">© 2024 {{store_name}}. All rights reserved.</p>\r\n            </div>\r\n        </div>\r\n    </footer>\r\n\r\n    <!-- Store JavaScript for product loading -->\r\n    <script src=\"customer-auth.js\"></script>\r\n    <script src=\"profile-header.js\"></script>\r\n    <script src=\"store.js\"></script>\r\n    <script>\r\n        // @ts-nocheck - Template placeholders will be replaced during generation\r\n        // Initialize store with configuration\r\n        const storeConfig = {\r\n            storeId: {{store_id}},\r\n            apiUrl: window.location.origin + \'/api\',\r\n            groupByCategory: false,\r\n            productGridColumns: 4,\r\n            showCategoryImages: false\r\n        };\r\n        \r\n        // Load products when page loads\r\n        if (typeof loadProducts === \'function\') {\r\n            loadProducts(storeConfig);\r\n        } else {\r\n            // Fallback: Load featured products\r\n            fetch(\'/api/products/featured\')\r\n                .then(response => response.json())\r\n                .then(products => {\r\n                    const container = document.getElementById(\'featured-products\');\r\n                    container.innerHTML = products.map(product => `\r\n                        <a href=\"product.html?id=${product.id}\" class=\"bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow block\">\r\n                            <div class=\"aspect-square bg-gray-200\">\r\n                                <img src=\"${product.image_url}\" alt=\"${product.name}\" class=\"w-full h-full object-cover\">\r\n                            </div>\r\n                            <div class=\"p-4\">\r\n                                <h3 class=\"font-bold text-slate-800 mb-2\">${product.name}</h3>\r\n                                <p class=\"text-primary font-black text-lg\">₦${product.price}</p>\r\n                                <button onclick=\"event.preventDefault(); event.stopPropagation();\" class=\"w-full mt-3 bg-primary text-white py-2 rounded-lg font-bold hover:bg-primary/90 transition-colors\">\r\n                                    Add to Cart\r\n                                </button>\r\n                            </div>\r\n                        </a>\r\n                    `).join(\'\');\r\n                });\r\n        }\r\n    </script>\r\n</body>\r\n</html>', NULL, '2026-01-26 09:01:26'),
(2, 'CampMart Style', 'Clean minimalist template with focus on whitespace and simplicity', '/assets/templates/campmart-preview.jpg', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"utf-8\"/>\r\n    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\r\n    <title>{{store_name}} - Minimalist Store</title>\r\n    <script src=\"https://cdn.tailwindcss.com\"></script>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\"/>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\"/>\r\n    <script>\r\n        tailwind.config = {\r\n            theme: {\r\n                extend: {\r\n                    colors: {\r\n                        \"primary\": \"{{primary_color}}\",\r\n                        \"accent\": \"{{accent_color}}\",\r\n                    },\r\n                    fontFamily: {\r\n                        \"sans\": [\"Inter\", \"sans-serif\"]\r\n                    },\r\n                },\r\n            },\r\n        }\r\n    </script>\r\n</head>\r\n<body class=\"bg-white font-sans text-gray-900 antialiased\">\r\n    <!-- Minimal Header -->\r\n    <header class=\"border-b border-gray-100\">\r\n        <div class=\"max-w-6xl mx-auto px-4 sm:px-6 py-6 flex items-center justify-between\">\r\n            <h1 class=\"text-xl font-light tracking-wide text-primary\">{{store_name}}</h1>\r\n            <nav class=\"hidden md:flex items-center gap-8 text-sm\">\r\n                <a href=\"#\" class=\"text-gray-600 hover:text-primary transition-colors\">Products</a>\r\n                <a href=\"#\" class=\"text-gray-600 hover:text-primary transition-colors\">About</a>\r\n                <a href=\"#\" class=\"text-gray-600 hover:text-primary transition-colors\">Contact</a>\r\n            </nav>\r\n            <button class=\"p-2 hover:bg-gray-50 rounded-full transition-colors\">\r\n                <span class=\"material-symbols-outlined text-gray-700\">shopping_bag</span>\r\n            </button>\r\n        </div>\r\n    </header>\r\n\r\n    <main>\r\n        <!-- Simple Hero -->\r\n        <section class=\"max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center\">\r\n            <h2 class=\"text-4xl md:text-5xl font-light text-gray-900 mb-6 tracking-tight\">\r\n                {{store_name}}\r\n            </h2>\r\n            <p class=\"text-lg text-gray-600 mb-12 max-w-2xl mx-auto font-light\">\r\n                {{store_description}}\r\n            </p>\r\n            <a href=\"#products\" class=\"inline-block px-8 py-3 bg-primary text-white font-medium hover:opacity-90 transition-opacity\">\r\n                Explore Collection\r\n            </a>\r\n        </section>\r\n\r\n        <!-- Clean Product Grid -->\r\n        <section id=\"products\" class=\"max-w-6xl mx-auto px-4 sm:px-6 py-16\">\r\n            <div class=\"mb-12 text-center\">\r\n                <h3 class=\"text-2xl font-light text-gray-900 mb-2\">Our Products</h3>\r\n                <div class=\"w-12 h-px bg-primary mx-auto\"></div>\r\n            </div>\r\n            \r\n            <div id=\"products-container\">\r\n                <!-- Products will be loaded here -->\r\n            </div>\r\n        </section>\r\n\r\n        <!-- Minimal Info Section -->\r\n        <section class=\"bg-gray-50 py-16\">\r\n            <div class=\"max-w-6xl mx-auto px-4 sm:px-6\">\r\n                <div class=\"grid grid-cols-1 md:grid-cols-3 gap-8 text-center\">\r\n                    <div>\r\n                        <div class=\"w-12 h-12 mx-auto mb-4 flex items-center justify-center\">\r\n                            <span class=\"material-symbols-outlined text-3xl text-primary\">local_shipping</span>\r\n                        </div>\r\n                        <h4 class=\"text-sm font-medium text-gray-900 mb-2\">Free Shipping</h4>\r\n                        <p class=\"text-xs text-gray-600\">On all orders</p>\r\n                    </div>\r\n                    <div>\r\n                        <div class=\"w-12 h-12 mx-auto mb-4 flex items-center justify-center\">\r\n                            <span class=\"material-symbols-outlined text-3xl text-primary\">verified</span>\r\n                        </div>\r\n                        <h4 class=\"text-sm font-medium text-gray-900 mb-2\">Quality Guaranteed</h4>\r\n                        <p class=\"text-xs text-gray-600\">Premium products</p>\r\n                    </div>\r\n                    <div>\r\n                        <div class=\"w-12 h-12 mx-auto mb-4 flex items-center justify-center\">\r\n                            <span class=\"material-symbols-outlined text-3xl text-primary\">support_agent</span>\r\n                        </div>\r\n                        <h4 class=\"text-sm font-medium text-gray-900 mb-2\">24/7 Support</h4>\r\n                        <p class=\"text-xs text-gray-600\">Always here to help</p>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </section>\r\n    </main>\r\n\r\n    <!-- Minimal Footer -->\r\n    <footer class=\"border-t border-gray-100 py-12\">\r\n        <div class=\"max-w-6xl mx-auto px-4 sm:px-6 text-center\">\r\n            <p class=\"text-sm text-gray-500 mb-4\">{{store_name}}</p>\r\n            <p class=\"text-xs text-gray-400\">© 2024 All rights reserved</p>\r\n        </div>\r\n    </footer>\r\n\r\n    <!-- Store JavaScript -->\r\n    <script src=\"customer-auth.js\"></script>\r\n    <script src=\"profile-header.js\"></script>\r\n    <script src=\"store.js\"></script>\r\n    <script>\r\n        // @ts-nocheck - Template placeholders will be replaced during generation\r\n        const storeConfig = {\r\n            storeId: {{store_id}},\r\n            apiUrl: window.location.origin + \'/api\',\r\n            groupByCategory: false,\r\n            productGridColumns: 3,\r\n            showCategoryImages: false\r\n        };\r\n        \r\n        if (typeof loadProducts === \'function\') {\r\n            loadProducts(storeConfig);\r\n        } else {\r\n            fetch(\'/api/products/featured\')\r\n                .then(response => response.json())\r\n                .then(products => {\r\n                    const container = document.getElementById(\'featured-products\');\r\n                    container.innerHTML = products.map(product => `\r\n                        <a href=\"product.html?id=${product.id}\" class=\"group block\">\r\n                            <div class=\"aspect-square bg-gray-100 mb-4 overflow-hidden\">\r\n                                <img src=\"${product.image_url}\" alt=\"${product.name}\" class=\"w-full h-full object-cover group-hover:scale-105 transition-transform duration-300\">\r\n                            </div>\r\n                            <h3 class=\"text-sm font-medium text-gray-900 mb-1\">${product.name}</h3>\r\n                            <p class=\"text-sm text-primary font-medium\">₦${product.price}</p>\r\n                        </a>\r\n                    `).join(\'\');\r\n                });\r\n        }\r\n    </script>\r\n</body>\r\n</html>', NULL, '2026-01-26 15:08:53'),
(3, 'Minimal Clean', 'Bold and vibrant design with energetic layout and strong CTAs for modern brands', '/assets/templates/minimal-preview.jpg', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"utf-8\"/>\r\n    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\r\n    <title>{{store_name}} - Bold & Modern</title>\r\n    <script src=\"https://cdn.tailwindcss.com\"></script>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap\" rel=\"stylesheet\"/>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\"/>\r\n    <script>\r\n        tailwind.config = {\r\n            theme: {\r\n                extend: {\r\n                    colors: {\r\n                        \"primary\": \"{{primary_color}}\",\r\n                        \"accent\": \"{{accent_color}}\",\r\n                    },\r\n                    fontFamily: {\r\n                        \"display\": [\"Poppins\", \"sans-serif\"]\r\n                    },\r\n                },\r\n            },\r\n        }\r\n    </script>\r\n</head>\r\n<body class=\"bg-gray-50 font-display text-gray-900 antialiased\">\r\n    <!-- Bold Header -->\r\n    <header class=\"bg-white shadow-sm sticky top-0 z-50\">\r\n        <div class=\"max-w-7xl mx-auto px-6\">\r\n            <div class=\"flex items-center justify-between h-20\">\r\n                <div class=\"flex items-center gap-3\">\r\n                    <div class=\"w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-xl flex items-center justify-center\">\r\n                        <span class=\"material-symbols-outlined text-white text-2xl font-bold\">bolt</span>\r\n                    </div>\r\n                    <span class=\"text-2xl font-black text-primary uppercase tracking-tight\">{{store_name}}</span>\r\n                </div>\r\n                \r\n                <nav class=\"hidden lg:flex items-center gap-8 text-sm font-semibold\">\r\n                    <a href=\"#\" class=\"text-gray-700 hover:text-primary transition-colors\">NEW IN</a>\r\n                    <a href=\"#\" class=\"text-gray-700 hover:text-primary transition-colors\">TRENDING</a>\r\n                    <a href=\"#\" class=\"text-gray-700 hover:text-primary transition-colors\">SALE</a>\r\n                </nav>\r\n                \r\n                <div class=\"flex items-center gap-2\">\r\n                    <button class=\"px-4 py-2 bg-primary text-white font-bold rounded-lg hover:brightness-110 transition-all\">\r\n                        <span class=\"material-symbols-outlined\">shopping_cart</span>\r\n                    </button>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </header>\r\n\r\n    <main>\r\n        <!-- Bold Split Hero -->\r\n        <section class=\"bg-gradient-to-br from-primary via-primary to-primary/90\">\r\n            <div class=\"max-w-7xl mx-auto px-6 py-20\">\r\n                <div class=\"grid lg:grid-cols-2 gap-12 items-center\">\r\n                    <div class=\"text-white\">\r\n                        <div class=\"inline-block px-4 py-1 bg-accent text-primary rounded-full text-sm font-bold mb-6\">\r\n                            🔥 NOW TRENDING\r\n                        </div>\r\n                        <h1 class=\"text-5xl lg:text-6xl font-black mb-6 leading-tight\">\r\n                            {{store_name}}\r\n                        </h1>\r\n                        <p class=\"text-xl mb-8 text-white/90 font-semibold\">\r\n                            {{store_description}}\r\n                        </p>\r\n                        <div class=\"flex gap-4\">\r\n                            <button class=\"px-8 py-4 bg-accent text-primary font-black rounded-xl hover:scale-105 transition-transform shadow-lg\">\r\n                                SHOP NOW\r\n                            </button>\r\n                            <button class=\"px-8 py-4 bg-white/20 text-white backdrop-blur-sm font-bold rounded-xl hover:bg-white/30 transition-colors border-2 border-white/30\">\r\n                                EXPLORE\r\n                            </button>\r\n                        </div>\r\n                    </div>\r\n                    \r\n                    <div class=\"hidden lg:flex items-center justify-center\">\r\n                        <div class=\"relative\">\r\n                            <div class=\"w-80 h-80 bg-accent/30 rounded-full blur-3xl absolute -top-10 -right-10\"></div>\r\n                            <div class=\"relative bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20\">\r\n                                <div class=\"grid grid-cols-2 gap-4\">\r\n                                    <div class=\"aspect-square bg-white/20 rounded-2xl\"></div>\r\n                                    <div class=\"aspect-square bg-accent/40 rounded-2xl\"></div>\r\n                                    <div class=\"aspect-square bg-accent/40 rounded-2xl\"></div>\r\n                                    <div class=\"aspect-square bg-white/20 rounded-2xl\"></div>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </section>\r\n\r\n        <!-- Trending Badge -->\r\n        <div class=\"bg-accent py-4\">\r\n            <div class=\"max-w-7xl mx-auto px-6\">\r\n                <div class=\"flex items-center justify-center gap-8 text-primary font-bold text-sm\">\r\n                    <span class=\"flex items-center gap-2\">\r\n                        <span class=\"material-symbols-outlined\">local_shipping</span>\r\n                        FREE DELIVERY\r\n                    </span>\r\n                    <span class=\"flex items-center gap-2\">\r\n                        <span class=\"material-symbols-outlined\">verified</span>\r\n                        100% AUTHENTIC\r\n                    </span>\r\n                    <span class=\"flex items-center gap-2\">\r\n                        <span class=\"material-symbols-outlined\">sell</span>\r\n                        BEST PRICES\r\n                    </span>\r\n                </div>\r\n            </div>\r\n        </div>\r\n\r\n        <!-- Dynamic Product Grid -->\r\n        <section class=\"py-16\">\r\n            <div class=\"max-w-7xl mx-auto px-6\">\r\n                <div class=\"text-center mb-12\">\r\n                    <h2 class=\"text-4xl font-black text-gray-900 mb-3\">FEATURED COLLECTION</h2>\r\n                    <div class=\"w-20 h-1 bg-primary mx-auto rounded-full\"></div>\r\n                </div>\r\n                \r\n                <div id=\"products-container\">\r\n                    <!-- Products will be loaded here -->\r\n                </div>\r\n            </div>\r\n        </section>\r\n\r\n        <!-- CTA Banner -->\r\n        <section class=\"bg-gradient-to-r from-primary to-primary/80 py-16\">\r\n            <div class=\"max-w-7xl mx-auto px-6 text-center text-white\">\r\n                <h3 class=\"text-3xl font-black mb-4\">JOIN THE MOVEMENT</h3>\r\n                <p class=\"text-lg mb-8 opacity-90\">Get exclusive deals and early access to new drops</p>\r\n                <button class=\"px-10 py-4 bg-accent text-primary font-black rounded-xl hover:scale-105 transition-transform shadow-lg\">\r\n                    SIGN UP NOW\r\n                </button>\r\n            </div>\r\n        </section>\r\n    </main>\r\n\r\n    <!-- Bold Footer -->\r\n    <footer class=\"bg-gray-900 text-white py-12\">\r\n        <div class=\"max-w-7xl mx-auto px-6\">\r\n            <div class=\"grid grid-cols-1 md:grid-cols-4 gap-8 mb-8\">\r\n                <div>\r\n                    <div class=\"flex items-center gap-2 mb-4\">\r\n                        <div class=\"w-8 h-8 bg-accent rounded-lg flex items-center justify-center\">\r\n                            <span class=\"material-symbols-outlined text-primary font-bold\">bolt</span>\r\n                        </div>\r\n                        <span class=\"text-xl font-black uppercase\">{{store_name}}</span>\r\n                    </div>\r\n                    <p class=\"text-sm text-gray-400\">{{store_description}}</p>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent mb-4\">SHOP</h4>\r\n                    <ul class=\"space-y-2 text-sm text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">New Arrivals</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Best Sellers</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Sale</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent mb-4\">INFO</h4>\r\n                    <ul class=\"space-y-2 text-sm text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">About Us</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Contact</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">FAQs</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold text-accent mb-4\">CONNECT</h4>\r\n                    <div class=\"flex gap-3\">\r\n                        <a href=\"#\" class=\"w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-accent hover:text-primary transition-all\">\r\n                            <span class=\"material-symbols-outlined\">tag</span>\r\n                        </a>\r\n                        <a href=\"#\" class=\"w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-accent hover:text-primary transition-all\">\r\n                            <span class=\"material-symbols-outlined\">share</span>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <div class=\"border-t border-white/10 pt-8 text-center\">\r\n                <p class=\"text-xs text-gray-500\">© 2024 {{store_name}}. All rights reserved.</p>\r\n            </div>\r\n        </div>\r\n    </footer>\r\n\r\n    <script src=\"customer-auth.js\"></script>\r\n    <script src=\"profile-header.js\"></script>\r\n    <script src=\"store.js\"></script>\r\n    <script>\r\n        // @ts-nocheck - Template placeholders will be replaced during generation\r\n        const storeConfig = {\r\n            storeId: {{store_id}},\r\n            apiUrl: window.location.origin + \'/api\',\r\n            groupByCategory: false,\r\n            productGridColumns: 4,\r\n            showCategoryImages: false\r\n        };\r\n        \r\n        if (typeof loadProducts === \'function\') {\r\n            loadProducts(storeConfig);\r\n        } else {\r\n            fetch(\'/api/products/featured\')\r\n                .then(response => response.json())\r\n                .then(products => {\r\n                    const container = document.getElementById(\'featured-products\');\r\n                    container.innerHTML = products.map(product => `\r\n                        <a href=\"product.html?id=${product.id}\" class=\"group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all hover:-translate-y-1\">\r\n                            <div class=\"aspect-square bg-gray-100 overflow-hidden\">\r\n                                <img src=\"${product.image_url}\" alt=\"${product.name}\" class=\"w-full h-full object-cover group-hover:scale-110 transition-transform duration-300\">\r\n                            </div>\r\n                            <div class=\"p-4\">\r\n                                <h3 class=\"font-bold text-gray-900 mb-2\">${product.name}</h3>\r\n                                <p class=\"text-primary font-black text-xl\">₦${product.price}</p>\r\n                            </div>\r\n                        </a>\r\n                    `).join(\'\');\r\n                });\r\n        }\r\n    </script>\r\n</body>\r\n</html>', NULL, '2026-01-26 15:08:53'),
(4, 'Bold Modern', 'Traditional ecommerce layout with sidebar navigation and proven conversion design', '/assets/templates/bold-preview.jpg', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"utf-8\"/>\r\n    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\r\n    <title>{{store_name}} - Classic Ecommerce</title>\r\n    <script src=\"https://cdn.tailwindcss.com\"></script>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap\" rel=\"stylesheet\"/>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\"/>\r\n    <script>\r\n        tailwind.config = {\r\n            theme: {\r\n                extend: {\r\n                    colors: {\r\n                        \"primary\": \"{{primary_color}}\",\r\n                        \"accent\": \"{{accent_color}}\",\r\n                    },\r\n                    fontFamily: {\r\n                        \"sans\": [\"Roboto\", \"sans-serif\"]\r\n                    },\r\n                },\r\n            },\r\n        }\r\n    </script>\r\n</head>\r\n<body class=\"bg-gray-100 font-sans text-gray-900\">\r\n    <!-- Classic Top Bar -->\r\n    <div class=\"bg-primary text-white text-xs py-2\">\r\n        <div class=\"max-w-7xl mx-auto px-6 flex items-center justify-between\">\r\n            <span>Welcome to {{store_name}} - Free shipping on orders over ₦5,000</span>\r\n            <div class=\"flex items-center gap-4\">\r\n                <a href=\"#\" class=\"hover:underline\">Help</a>\r\n                <a href=\"#\" class=\"hover:underline\">Track Order</a>\r\n            </div>\r\n        </div>\r\n    </div>\r\n\r\n    <!-- Classic Header -->\r\n    <header class=\"bg-white border-b border-gray-200\">\r\n        <div class=\"max-w-7xl mx-auto px-6 py-4\">\r\n            <div class=\"flex items-center justify-between mb-4\">\r\n                <h1 class=\"text-3xl font-bold text-primary\">{{store_name}}</h1>\r\n                \r\n                <div class=\"flex-1 max-w-xl mx-8 hidden md:block\">\r\n                    <div class=\"flex items-center bg-gray-100 rounded-md overflow-hidden\">\r\n                        <input type=\"text\" placeholder=\"Search products...\" class=\"flex-1 px-4 py-2 bg-transparent focus:outline-none text-sm\">\r\n                        <button class=\"px-4 py-2 bg-primary text-white\">\r\n                            <span class=\"material-symbols-outlined text-sm\">search</span>\r\n                        </button>\r\n                    </div>\r\n                </div>\r\n                \r\n                <div class=\"flex items-center gap-4\">\r\n                    <button class=\"flex items-center gap-1 text-gray-700 hover:text-primary\">\r\n                        <span class=\"material-symbols-outlined\">person</span>\r\n                        <span class=\"text-sm hidden lg:inline\">Account</span>\r\n                    </button>\r\n                    <button class=\"flex items-center gap-1 text-gray-700 hover:text-primary relative\">\r\n                        <span class=\"material-symbols-outlined\">shopping_cart</span>\r\n                        <span class=\"text-sm hidden lg:inline\">Cart</span>\r\n                        <span class=\"absolute -top-1 -right-1 bg-accent text-primary text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold\">0</span>\r\n                    </button>\r\n                </div>\r\n            </div>\r\n            \r\n            <!-- Navigation Menu -->\r\n            <nav class=\"flex items-center gap-6 text-sm font-medium border-t border-gray-200 pt-3\">\r\n                <a href=\"#\" class=\"text-primary font-semibold flex items-center gap-1\">\r\n                    <span class=\"material-symbols-outlined text-lg\">menu</span>\r\n                    All Categories\r\n                </a>\r\n                <a href=\"#\" class=\"text-gray-700 hover:text-primary\">New Arrivals</a>\r\n                <a href=\"#\" class=\"text-gray-700 hover:text-primary\">Best Sellers</a>\r\n                <a href=\"#\" class=\"text-gray-700 hover:text-primary\">Deals</a>\r\n                <a href=\"#\" class=\"text-red-600 font-bold\">Sale</a>\r\n            </nav>\r\n        </div>\r\n    </header>\r\n\r\n    <main class=\"max-w-7xl mx-auto px-6 py-6\">\r\n        <!-- Breadcrumb -->\r\n        <div class=\"text-sm text-gray-600 mb-6\">\r\n            <a href=\"#\" class=\"hover:text-primary\">Home</a> / \r\n            <span class=\"text-gray-900\">Products</span>\r\n        </div>\r\n\r\n        <div class=\"grid grid-cols-12 gap-6\">\r\n            <!-- Sidebar -->\r\n            <aside class=\"col-span-12 lg:col-span-3\">\r\n                <div class=\"bg-white rounded-lg p-4 mb-4\">\r\n                    <h3 class=\"font-bold text-gray-900 mb-4\">Categories</h3>\r\n                    <ul class=\"space-y-2 text-sm\">\r\n                        <li><a href=\"#\" class=\"text-gray-700 hover:text-primary flex items-center justify-between\">\r\n                            Electronics <span class=\"text-gray-400\">(45)</span>\r\n                        </a></li>\r\n                        <li><a href=\"#\" class=\"text-gray-700 hover:text-primary flex items-center justify-between\">\r\n                            Fashion <span class=\"text-gray-400\">(32)</span>\r\n                        </a></li>\r\n                        <li><a href=\"#\" class=\"text-gray-700 hover:text-primary flex items-center justify-between\">\r\n                            Home & Garden <span class=\"text-gray-400\">(28)</span>\r\n                        </a></li>\r\n                        <li><a href=\"#\" class=\"text-gray-700 hover:text-primary flex items-center justify-between\">\r\n                            Sports <span class=\"text-gray-400\">(19)</span>\r\n                        </a></li>\r\n                    </ul>\r\n                </div>\r\n                \r\n                <div class=\"bg-white rounded-lg p-4 mb-4\">\r\n                    <h3 class=\"font-bold text-gray-900 mb-4\">Price Range</h3>\r\n                    <div class=\"space-y-2 text-sm\">\r\n                        <label class=\"flex items-center gap-2\">\r\n                            <input type=\"checkbox\" class=\"rounded\">\r\n                            <span class=\"text-gray-700\">Under ₦1,000</span>\r\n                        </label>\r\n                        <label class=\"flex items-center gap-2\">\r\n                            <input type=\"checkbox\" class=\"rounded\">\r\n                            <span class=\"text-gray-700\">₦1,000 - ₦5,000</span>\r\n                        </label>\r\n                        <label class=\"flex items-center gap-2\">\r\n                            <input type=\"checkbox\" class=\"rounded\">\r\n                            <span class=\"text-gray-700\">Over ₦5,000</span>\r\n                        </label>\r\n                    </div>\r\n                </div>\r\n                \r\n                <div class=\"bg-accent/10 border border-accent rounded-lg p-4\">\r\n                    <h3 class=\"font-bold text-primary mb-2\">Special Offer!</h3>\r\n                    <p class=\"text-sm text-gray-700 mb-3\">Get 20% off your first purchase</p>\r\n                    <button class=\"w-full px-4 py-2 bg-primary text-white rounded font-medium hover:bg-primary/90\">\r\n                        Claim Now\r\n                    </button>\r\n                </div>\r\n            </aside>\r\n\r\n            <!-- Main Content -->\r\n            <div class=\"col-span-12 lg:col-span-9\">\r\n                <!-- Banner -->\r\n                <div class=\"bg-gradient-to-r from-primary to-primary/90 rounded-lg p-8 mb-6 text-white\">\r\n                    <h2 class=\"text-3xl font-bold mb-2\">{{store_name}}</h2>\r\n                    <p class=\"text-lg mb-4 opacity-90\">{{store_description}}</p>\r\n                    <button class=\"px-6 py-2 bg-accent text-primary font-bold rounded hover:brightness-110\">\r\n                        Shop Now\r\n                    </button>\r\n                </div>\r\n\r\n                <!-- Products Header -->\r\n                <div class=\"flex items-center justify-between mb-6\">\r\n                    <h2 class=\"text-2xl font-bold text-gray-900\">Featured Products</h2>\r\n                    <select class=\"px-4 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-primary\">\r\n                        <option>Sort by: Featured</option>\r\n                        <option>Price: Low to High</option>\r\n                        <option>Price: High to Low</option>\r\n                        <option>Newest</option>\r\n                    </select>\r\n                </div>\r\n\r\n                <!-- Product Grid -->\r\n                <div id=\"products-container\">\r\n                    <!-- Products will be loaded here -->\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n    <!-- Footer -->\r\n    <footer class=\"bg-gray-800 text-white mt-16 py-12\">\r\n        <div class=\"max-w-7xl mx-auto px-6\">\r\n            <div class=\"grid grid-cols-1 md:grid-cols-4 gap-8 mb-8\">\r\n                <div>\r\n                    <h3 class=\"text-xl font-bold mb-4\">{{store_name}}</h3>\r\n                    <p class=\"text-sm text-gray-400 leading-relaxed\">{{store_description}}</p>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold mb-4\">Customer Service</h4>\r\n                    <ul class=\"space-y-2 text-sm text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Contact Us</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Shipping Info</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Returns</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">FAQs</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold mb-4\">My Account</h4>\r\n                    <ul class=\"space-y-2 text-sm text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Sign In</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Order History</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Wishlist</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white\">Track Order</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"font-bold mb-4\">Newsletter</h4>\r\n                    <p class=\"text-sm text-gray-400 mb-3\">Subscribe for exclusive deals</p>\r\n                    <div class=\"flex gap-2\">\r\n                        <input type=\"email\" placeholder=\"Email\" class=\"flex-1 px-3 py-2 bg-gray-700 rounded text-sm focus:outline-none\">\r\n                        <button class=\"px-4 py-2 bg-accent text-primary font-bold rounded\">Go</button>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <div class=\"border-t border-gray-700 pt-6 text-center text-sm text-gray-400\">\r\n                <p>© 2024 {{store_name}}. All rights reserved.</p>\r\n            </div>\r\n        </div>\r\n    </footer>\r\n\r\n    <script src=\"customer-auth.js\"></script>\r\n    <script src=\"profile-header.js\"></script>\r\n    <script src=\"store.js\"></script>\r\n    <script>\r\n        // @ts-nocheck - Template placeholders will be replaced during generation\r\n        const storeConfig = {\r\n            storeId: {{store_id}},\r\n            apiUrl: window.location.origin + \'/api\',\r\n            groupByCategory: false,\r\n            productGridColumns: 3,\r\n            showCategoryImages: false\r\n        };\r\n        \r\n        if (typeof loadProducts === \'function\') {\r\n            loadProducts(storeConfig);\r\n        } else {\r\n            fetch(\'/api/products/featured\')\r\n                .then(response => response.json())\r\n                .then(products => {\r\n                    const container = document.getElementById(\'featured-products\');\r\n                    container.innerHTML = products.map(product => `\r\n                        <a href=\"product.html?id=${product.id}\" class=\"bg-white rounded-lg overflow-hidden hover:shadow-lg transition-shadow border border-gray-200 group\">\r\n                            <div class=\"aspect-square bg-gray-100 overflow-hidden\">\r\n                                <img src=\"${product.image_url}\" alt=\"${product.name}\" class=\"w-full h-full object-cover group-hover:scale-105 transition-transform duration-300\">\r\n                            </div>\r\n                            <div class=\"p-4\">\r\n                                <h3 class=\"font-medium text-gray-900 mb-2 line-clamp-2\">${product.name}</h3>\r\n                                <div class=\"flex items-center justify-between\">\r\n                                    <p class=\"text-primary font-bold text-lg\">₦${product.price}</p>\r\n                                    <button onclick=\"event.preventDefault(); event.stopPropagation();\" class=\"px-3 py-1 bg-primary text-white text-xs rounded hover:bg-primary/90\">\r\n                                        Add to Cart\r\n                                    </button>\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    `).join(\'\');\r\n                });\r\n        }\r\n    </script>\r\n</body>\r\n</html>', NULL, '2026-01-26 15:08:53');
INSERT INTO `store_templates` (`id`, `name`, `description`, `preview_image`, `html_template`, `css_template`, `created_at`) VALUES
(5, 'Classic Ecommerce', 'Elegant luxury template with sophisticated typography and refined spacing', '/assets/templates/classic-preview.jpg', '<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n    <meta charset=\"utf-8\"/>\r\n    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\r\n    <title>{{store_name}} - Luxury Collection</title>\r\n    <script src=\"https://cdn.tailwindcss.com\"></script>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Lato:wght@300;400;700&display=swap\" rel=\"stylesheet\"/>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\"/>\r\n    <script>\r\n        tailwind.config = {\r\n            theme: {\r\n                extend: {\r\n                    colors: {\r\n                        \"primary\": \"{{primary_color}}\",\r\n                        \"accent\": \"{{accent_color}}\",\r\n                    },\r\n                    fontFamily: {\r\n                        \"serif\": [\"Playfair Display\", \"serif\"],\r\n                        \"sans\": [\"Lato\", \"sans-serif\"]\r\n                    },\r\n                },\r\n            },\r\n        }\r\n    </script>\r\n</head>\r\n<body class=\"bg-neutral-50 text-gray-900 antialiased\">\r\n    <!-- Elegant Header -->\r\n    <header class=\"bg-primary text-white\">\r\n        <div class=\"max-w-[1400px] mx-auto px-8\">\r\n            <div class=\"flex items-center justify-between h-24\">\r\n                <nav class=\"hidden lg:flex items-center gap-8 text-sm tracking-widest font-light\">\r\n                    <a href=\"#\" class=\"hover:text-accent transition-colors\">COLLECTIONS</a>\r\n                    <a href=\"#\" class=\"hover:text-accent transition-colors\">NEW ARRIVALS</a>\r\n                </nav>\r\n                \r\n                <div class=\"flex-1 flex justify-center\">\r\n                    <h1 class=\"text-3xl font-serif font-light tracking-wider\">{{store_name}}</h1>\r\n                </div>\r\n                \r\n                <div class=\"flex items-center gap-6\">\r\n                    <button class=\"hover:text-accent transition-colors\">\r\n                        <span class=\"material-symbols-outlined\">search</span>\r\n                    </button>\r\n                    <button class=\"hover:text-accent transition-colors\">\r\n                        <span class=\"material-symbols-outlined\">person_outline</span>\r\n                    </button>\r\n                    <button class=\"hover:text-accent transition-colors\">\r\n                        <span class=\"material-symbols-outlined\">shopping_bag</span>\r\n                    </button>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </header>\r\n\r\n    <main>\r\n        <!-- Hero Section -->\r\n        <section class=\"relative h-[600px] bg-primary flex items-center justify-center overflow-hidden\">\r\n            <div class=\"absolute inset-0 bg-gradient-to-b from-transparent via-primary/50 to-primary\"></div>\r\n            <div class=\"relative z-10 text-center text-white px-6 max-w-4xl\">\r\n                <p class=\"text-sm tracking-[0.3em] font-light mb-6 text-accent uppercase\">Introducing</p>\r\n                <h2 class=\"text-6xl md:text-7xl font-serif mb-8 leading-tight font-light\">\r\n                    {{store_name}}\r\n                </h2>\r\n                <p class=\"text-xl font-light mb-12 max-w-2xl mx-auto leading-relaxed opacity-90\">\r\n                    {{store_description}}\r\n                </p>\r\n                <button class=\"px-12 py-4 bg-white text-primary font-light tracking-widest hover:bg-accent hover:text-primary transition-all\">\r\n                    EXPLORE COLLECTION\r\n                </button>\r\n            </div>\r\n            \r\n            <!-- Decorative Elements -->\r\n            <div class=\"absolute top-20 left-20 w-32 h-32 border border-white/20 rotate-45\"></div>\r\n            <div class=\"absolute bottom-20 right-20 w-24 h-24 border border-accent/30\"></div>\r\n        </section>\r\n\r\n        <!-- Featured Section -->\r\n        <section class=\"py-24\">\r\n            <div class=\"max-w-[1400px] mx-auto px-8\">\r\n                <div class=\"text-center mb-16\">\r\n                    <p class=\"text-sm tracking-[0.3em] font-light mb-4 text-primary uppercase\">Curated Selection</p>\r\n                    <h3 class=\"text-4xl font-serif font-light text-gray-900 mb-4\">Featured Collection</h3>\r\n                    <div class=\"w-24 h-px bg-accent mx-auto\"></div>\r\n                </div>\r\n                \r\n                <div id=\"products-container\">\r\n                    <!-- Products will be loaded here -->\r\n                </div>\r\n            </div>\r\n        </section>\r\n\r\n        <!-- Values Section -->\r\n        <section class=\"bg-white py-20\">\r\n            <div class=\"max-w-[1400px] mx-auto px-8\">\r\n                <div class=\"grid grid-cols-1 md:grid-cols-3 gap-16 text-center\">\r\n                    <div class=\"group\">\r\n                        <div class=\"w-16 h-16 mx-auto mb-6 flex items-center justify-center border border-primary rounded-full group-hover:bg-primary group-hover:text-white transition-all\">\r\n                            <span class=\"material-symbols-outlined text-2xl\">workspace_premium</span>\r\n                        </div>\r\n                        <h4 class=\"text-lg font-serif mb-3 text-gray-900\">Exceptional Quality</h4>\r\n                        <p class=\"text-sm font-light text-gray-600 leading-relaxed\">Handpicked luxury items crafted to perfection</p>\r\n                    </div>\r\n                    <div class=\"group\">\r\n                        <div class=\"w-16 h-16 mx-auto mb-6 flex items-center justify-center border border-primary rounded-full group-hover:bg-primary group-hover:text-white transition-all\">\r\n                            <span class=\"material-symbols-outlined text-2xl\">local_shipping</span>\r\n                        </div>\r\n                        <h4 class=\"text-lg font-serif mb-3 text-gray-900\">Complimentary Delivery</h4>\r\n                        <p class=\"text-sm font-light text-gray-600 leading-relaxed\">White-glove service on all orders</p>\r\n                    </div>\r\n                    <div class=\"group\">\r\n                        <div class=\"w-16 h-16 mx-auto mb-6 flex items-center justify-center border border-primary rounded-full group-hover:bg-primary group-hover:text-white transition-all\">\r\n                            <span class=\"material-symbols-outlined text-2xl\">support_agent</span>\r\n                        </div>\r\n                        <h4 class=\"text-lg font-serif mb-3 text-gray-900\">Personal Concierge</h4>\r\n                        <p class=\"text-sm font-light text-gray-600 leading-relaxed\">Dedicated assistance for your needs</p>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </section>\r\n\r\n        <!-- Newsletter -->\r\n        <section class=\"bg-primary text-white py-16\">\r\n            <div class=\"max-w-2xl mx-auto px-8 text-center\">\r\n                <h3 class=\"text-3xl font-serif font-light mb-4\">Join Our Circle</h3>\r\n                <p class=\"text-sm font-light mb-8 opacity-90 tracking-wide\">Receive exclusive invitations and previews</p>\r\n                <div class=\"flex gap-4 max-w-md mx-auto\">\r\n                    <input type=\"email\" placeholder=\"Your email address\" class=\"flex-1 px-6 py-3 bg-white/10 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:border-accent backdrop-blur-sm\">\r\n                    <button class=\"px-8 py-3 bg-accent text-primary font-light tracking-wider hover:bg-white transition-all\">\r\n                        SUBSCRIBE\r\n                    </button>\r\n                </div>\r\n            </div>\r\n        </section>\r\n    </main>\r\n\r\n    <!-- Elegant Footer -->\r\n    <footer class=\"bg-neutral-900 text-white py-16\">\r\n        <div class=\"max-w-[1400px] mx-auto px-8\">\r\n            <div class=\"grid grid-cols-1 md:grid-cols-4 gap-12 mb-12\">\r\n                <div>\r\n                    <h3 class=\"text-2xl font-serif font-light mb-6\">{{store_name}}</h3>\r\n                    <p class=\"text-sm font-light text-gray-400 leading-relaxed\">\r\n                        {{store_description}}\r\n                    </p>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"text-sm tracking-widest mb-6 text-accent\">CUSTOMER CARE</h4>\r\n                    <ul class=\"space-y-3 text-sm font-light text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Contact Us</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Shipping & Returns</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Size Guide</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Care Instructions</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"text-sm tracking-widest mb-6 text-accent\">ABOUT</h4>\r\n                    <ul class=\"space-y-3 text-sm font-light text-gray-400\">\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Our Story</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Craftsmanship</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Sustainability</a></li>\r\n                        <li><a href=\"#\" class=\"hover:text-white transition-colors\">Press</a></li>\r\n                    </ul>\r\n                </div>\r\n                <div>\r\n                    <h4 class=\"text-sm tracking-widest mb-6 text-accent\">FOLLOW US</h4>\r\n                    <div class=\"flex gap-4\">\r\n                        <a href=\"#\" class=\"w-10 h-10 border border-white/20 flex items-center justify-center hover:border-accent hover:text-accent transition-all\">\r\n                            <span class=\"material-symbols-outlined text-sm\">tag</span>\r\n                        </a>\r\n                        <a href=\"#\" class=\"w-10 h-10 border border-white/20 flex items-center justify-center hover:border-accent hover:text-accent transition-all\">\r\n                            <span class=\"material-symbols-outlined text-sm\">language</span>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <div class=\"border-t border-white/10 pt-8 flex items-center justify-between text-xs font-light text-gray-500\">\r\n                <p>© 2024 {{store_name}}. All rights reserved.</p>\r\n                <div class=\"flex gap-6\">\r\n                    <a href=\"#\" class=\"hover:text-white transition-colors\">Privacy Policy</a>\r\n                    <a href=\"#\" class=\"hover:text-white transition-colors\">Terms of Service</a>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </footer>\r\n\r\n    <script src=\"customer-auth.js\"></script>\r\n    <script src=\"profile-header.js\"></script>\r\n    <script src=\"store.js\"></script>\r\n    <script>\r\n        // @ts-nocheck - Template placeholders will be replaced during generation\r\n        const storeConfig = {\r\n            storeId: {{store_id}},\r\n            apiUrl: window.location.origin + \'/api\',\r\n            groupByCategory: false,\r\n            productGridColumns: 3,\r\n            showCategoryImages: false\r\n        };\r\n        \r\n        if (typeof loadProducts === \'function\') {\r\n            loadProducts(storeConfig);\r\n        } else {\r\n            fetch(\'/api/products/featured\')\r\n                .then(response => response.json())\r\n                .then(products => {\r\n                    const container = document.getElementById(\'featured-products\');\r\n                    container.innerHTML = products.map(product => `\r\n                        <a href=\"product.html?id=${product.id}\" class=\"group block\">\r\n                            <div class=\"aspect-[3/4] bg-neutral-100 mb-6 overflow-hidden\">\r\n                                <img src=\"${product.image_url}\" alt=\"${product.name}\" class=\"w-full h-full object-cover group-hover:scale-105 transition-transform duration-700\">\r\n                            </div>\r\n                            <h3 class=\"text-lg font-serif font-light text-gray-900 mb-2\">${product.name}</h3>\r\n                            <p class=\"text-primary font-light tracking-wider\">₦${product.price}</p>\r\n                        </a>\r\n                    `).join(\'\');\r\n                });\r\n        }\r\n    </script>\r\n</body>\r\n</html>', NULL, '2026-01-26 15:08:53'),
(6, 'Premium Luxury', 'Elegant template for high-end products', '/assets/templates/luxury-preview.jpg', NULL, NULL, '2026-01-26 15:08:53');

-- --------------------------------------------------------

--
-- Table structure for table `super_admins`
--

CREATE TABLE `super_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `super_admins`
--

INSERT INTO `super_admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@platform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-26 09:01:26'),
(2, 'superadmin', 'superadmin@livepetal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-26 12:05:33'),
(3, 'admin2026', 'admin@livepetal.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhqa', '2026-01-26 12:07:41'),
(4, 'myadmin', 'myadmin@livepetal.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFVZOauhJtZmvqPz7.DZ9n.oa4zCEG5a', '2026-01-26 12:07:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_store_slug` (`store_id`,`slug`),
  ADD KEY `idx_categories_store_id` (`store_id`),
  ADD KEY `idx_categories_parent_id` (`parent_id`),
  ADD KEY `idx_categories_status` (`status`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `fk_orders_shipping_address` (`shipping_address_id`),
  ADD KEY `fk_orders_billing_address` (`billing_address_id`),
  ADD KEY `idx_orders_customer_id` (`customer_id`),
  ADD KEY `idx_orders_payment_status` (`payment_status`),
  ADD KEY `idx_orders_tracking_number` (`tracking_number`),
  ADD KEY `idx_payment_reference` (`payment_reference`);

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
  ADD KEY `store_id` (`store_id`),
  ADD KEY `idx_products_category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_customer_product` (`customer_id`,`product_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_slug` (`store_slug`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `store_customers`
--
ALTER TABLE `store_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_store_email` (`store_id`,`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_store_id` (`store_id`),
  ADD KEY `idx_is_guest` (`is_guest`);

--
-- Indexes for table `store_navigation`
--
ALTER TABLE `store_navigation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_id` (`store_id`);

--
-- Indexes for table `store_sections`
--
ALTER TABLE `store_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_id` (`store_id`);

--
-- Indexes for table `store_templates`
--
ALTER TABLE `store_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `super_admins`
--
ALTER TABLE `super_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `store_customers`
--
ALTER TABLE `store_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `store_navigation`
--
ALTER TABLE `store_navigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_sections`
--
ALTER TABLE `store_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_templates`
--
ALTER TABLE `store_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `super_admins`
--
ALTER TABLE `super_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `store_customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_billing_address` FOREIGN KEY (`billing_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `store_customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD CONSTRAINT `shopping_carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `store_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_customers`
--
ALTER TABLE `store_customers`
  ADD CONSTRAINT `store_customers_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_navigation`
--
ALTER TABLE `store_navigation`
  ADD CONSTRAINT `store_navigation_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_sections`
--
ALTER TABLE `store_sections`
  ADD CONSTRAINT `store_sections_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
