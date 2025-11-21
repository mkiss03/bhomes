-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost
-- Létrehozás ideje: 2025. Nov 21. 07:46
-- Kiszolgáló verziója: 10.11.14-MariaDB-0+deb12u2
-- PHP verzió: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `c88384bhe`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(2, 'besthomesespana', '$2a$12$j.s12TK1XOdOhTy7O4f0bO.pBKTO1zje66MwHFzXg/VyOdhVwM9de', 'admin@example.com', '2025-08-13 06:58:24');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `publish_at` datetime DEFAULT current_timestamp(),
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `cover_image`, `status`, `publish_at`, `seo_title`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 'Costa Blanca ingatlanpiac 2025-ben', 'costa-blanca-ingatlanpiac-2025', 'Fedezze fel a Costa Blanca ingatlanpiacának legfrissebb trendjeit és befektetési lehetőségeit 2025-ben.', '<p>A Costa Blanca ingatlanpiaca 2025-ben is folytatja növekedési pályáját. A mediterrán életstílus és a kedvező éghajlat továbbra is vonzza a nemzetközi befektetőket.</p><h2>Piaci trendek</h2><p>Az elmúlt évben 15%-os növekedést tapasztaltunk az ingatlanárakban, különösen a tengerparti területeken.</p><h2>Befektetési lehetőségek</h2><p>A legjobb befektetési lehetőségek Benidorm, Alicante és Calpe területén találhatók.</p>', './images/main1.jpg', 'published', '2025-01-15 10:00:00', 'Costa Blanca ingatlanpiac 2025 - Trendek és lehetőségek', 'Ismerje meg a Costa Blanca ingatlanpiacának legfrissebb trendjeit, befektetési lehetőségeit és prognózisait 2025-re.', '2025-08-18 10:57:15', '2025-08-18 10:57:15'),
(2, 'asd', 'asd', 'asd', 'asdwasd', '', 'published', '2025-08-18 11:26:00', '', '', '2025-08-18 11:27:03', '2025-08-18 11:27:03');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'teszt jakab', 'tesztj@gmail.hz', '012345678', 'TESZT', '2025-08-13 07:31:29'),
(2, 'teszt', 'a@gmail.teszt', 'a', 'Érdeklődés az ingatlannal kapcsolatban:\nIngatlan: Teszt apartman 1\nID: 6\n\nÜzenet: a', '2025-08-15 16:06:04');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `content_sections`
--

CREATE TABLE `content_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `content_type` enum('text','html','image') DEFAULT 'text',
  `content_value` text DEFAULT NULL,
  `page` varchar(50) NOT NULL,
  `section_group` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `content_sections`
--

INSERT INTO `content_sections` (`id`, `section_key`, `section_name`, `content_type`, `content_value`, `page`, `section_group`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'hero_title', 'Főcím - Hero szekció', 'text', 'Találd meg álmotthonod Costa Blancán!', 'home', 'hero', 1, 1, '2025-08-16 13:02:58', '2025-08-19 06:17:21'),
(2, 'hero_subtitle', 'Alcím - Hero szekció', 'text', 'Fedezd fel a lenyűgöző mediterrán panorámával rendelkező ingatlanokat!', 'home', 'hero', 2, 1, '2025-08-16 13:02:58', '2025-11-21 07:07:15'),
(3, 'nav_home', 'Navigáció - Főoldal', 'text', 'Főoldal', 'navigation', 'main_nav', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(4, 'nav_properties', 'Navigáció - Ingatlanok', 'text', 'Ingatlanok', 'navigation', 'main_nav', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(5, 'nav_costa_blanca', 'Navigáció - Costa Blanca', 'text', 'Costa Blanca', 'navigation', 'main_nav', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(6, 'nav_about', 'Navigáció - Rólunk', 'text', 'Rólunk', 'navigation', 'main_nav', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(7, 'nav_contact', 'Navigáció - Kapcsolat', 'text', 'Kapcsolat', 'navigation', 'main_nav', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(8, 'properties_title', 'Ingatlanok szekció címe', 'text', 'Kiemelt ingatlanok', 'home', 'properties', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(9, 'properties_subtitle', 'Ingatlanok szekció alcíme', 'text', 'Fedezd fel válogatott prémium ingatlanjainkat', 'home', 'properties', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(10, 'load_more_text', 'További betöltés gomb', 'text', 'További ingatlanok betöltése', 'home', 'properties', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(11, 'costa_blanca_title', 'Costa Blanca szekció címe', 'text', 'Fedezd fel Costa Blancá', 'home', 'costa_blanca', 1, 1, '2025-08-16 13:02:58', '2025-11-21 07:35:05'),
(12, 'costa_blanca_description', 'Costa Blanca leírása', 'html', 'Costa Blanca, Spanyolország lenyűgöző „Fehér Partja\", páratlan életstílust kínál: érintetlen strandok, egész éves napsütés és gazdag kulturális örökség. A pezsgő Alicante városától a bájos Altea és Calpe településekig tökéletesen ötvözi a kikapcsolódást és a kalandot.', 'home', 'costa_blanca', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(13, 'feature_sunshine', 'Costa Blanca jellemző - Napsütés', 'text', 'Évente több mint 300 napsütéses nap', 'home', 'costa_blanca_features', 1, 1, '2025-08-16 13:02:58', '2025-11-21 07:19:47'),
(14, 'feature_beaches', 'Costa Blanca jellemző - Strandok', 'text', 'Érintetlen strandok és kristálytiszta víz', 'home', 'costa_blanca_features', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(15, 'feature_investment', 'Costa Blanca jellemző - Befektetés', 'text', 'Erős befektetési potenciál és magas bérleti hozam', 'home', 'costa_blanca_features', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(16, 'feature_dining', 'Costa Blanca jellemző - Étkezés', 'text', 'Világszínvonalú éttermek és mediterrán konyha', 'home', 'costa_blanca_features', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(17, 'about_name', 'Ügynök neve', 'text', 'Balogh Eszter', 'home', 'about', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(18, 'about_title', 'Ügynök pozíciója', 'text', 'Megbízható ingatlanszakértőd', 'home', 'about', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(19, 'about_description', 'Ügynök leírása', 'html', 'Több mint 20 éves costa blancai ingatlanpiaci tapasztalattal nemzetközi ügyfeleknek segítek megtalálni a tökéletes otthont vagy befektetést Spanyolországban. Mély helyismeretem, többnyelvű kommunikációm és ügyfélközpontú hozzáállásom garantálja a gördülékeny és sikeres ügyintézést.', 'home', 'about', 3, 1, '2025-08-16 13:02:58', '2025-08-17 12:03:10'),
(20, 'about_feature_1_title', 'Szakmai jellemző 1 címe', 'text', 'Hivatalos spanyol ingatlanértékesítői engedéllyel rendelkező iroda és szakemberek vagyunk!', 'home', 'about_features', 1, 1, '2025-08-16 13:02:58', '2025-11-21 07:18:50'),
(21, 'about_feature_1_desc', 'Szakmai jellemző 1 leírása', 'text', 'Teljes körű hivatalos spanyol ingatlanértékesítői engedéllyel, tanúsítvánnyal, és biztosítással rendelkező ingatlanügynök', 'home', 'about_features', 2, 1, '2025-08-16 13:02:58', '2025-11-21 07:21:18'),
(22, 'about_feature_2_title', 'Szakmai jellemző 2 címe', 'text', 'Többnyelvű szolgáltatás', 'home', 'about_features', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(23, 'about_feature_2_desc', 'Szakmai jellemző 2 leírása', 'text', 'Folyékony angol, spanyol, német és francia nyelvtudás', 'home', 'about_features', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(24, 'about_feature_3_title', 'Szakmai jellemző 3 címe', 'text', '24/7 Support', 'home', 'about_features', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(25, 'about_feature_3_desc', 'Szakmai jellemző 3 leírása', 'text', '0–24 órás ügyféltámogatás', 'home', 'about_features', 6, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(26, 'contact_title', 'Kapcsolat szekció címe', 'text', 'Lépj kapcsolatba velünk!', 'home', 'contact', 1, 1, '2025-08-16 13:02:58', '2025-11-21 07:06:54'),
(27, 'contact_subtitle', 'Kapcsolat szekció alcíme', 'text', 'Készen állsz megtalálni álomingatlanod? Beszéljünk!', 'home', 'contact', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(28, 'contact_form_name', 'Űrlap - Név mező', 'text', 'Teljes név', 'contact', 'form', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(29, 'contact_form_email', 'Űrlap - Email mező', 'text', 'E-mail cím', 'contact', 'form', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(30, 'contact_form_phone', 'Űrlap - Telefon mező', 'text', 'Telefonszám', 'contact', 'form', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(31, 'contact_form_message', 'Űrlap - Üzenet mező', 'text', 'Üzenet', 'contact', 'form', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(32, 'contact_form_submit', 'Űrlap - Küldés gomb', 'text', 'Küldés', 'contact', 'form', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(33, 'contact_address_title', 'Kapcsolat - Cím címke', 'text', 'Irodánk címe', 'contact', 'info', 1, 1, '2025-08-16 13:02:58', '2025-11-21 07:19:18'),
(34, 'contact_address', 'Kapcsolat - Cím', 'text', 'Avenida de la Costa, 123\n03001 Alicante, Spain', 'contact', 'info', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(35, 'contact_phone_title', 'Kapcsolat - Telefon címke', 'text', 'Telefon', 'contact', 'info', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(36, 'contact_phone', 'Kapcsolat - Telefon szám', 'text', '+36706310000', 'contact', 'info', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(37, 'contact_email_title', 'Kapcsolat - Email címke', 'text', 'E-mail', 'contact', 'info', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(38, 'contact_email', 'Kapcsolat - Email cím', 'text', 'maria@costablancapremier.com', 'contact', 'info', 6, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(39, 'contact_hours_title', 'Kapcsolat - Nyitvatartás címke', 'text', 'Nyitvatartás', 'contact', 'info', 7, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(40, 'contact_hours', 'Kapcsolat - Nyitvatartás', 'text', 'Hétfő–Péntek: 9:00–19:00\nSzombat: 10:00–16:00', 'contact', 'info', 8, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(41, 'footer_company_name', 'Lábléc - Cégnév', 'text', 'Best Homes España\n', 'footer', 'company', 1, 1, '2025-08-16 13:02:58', '2025-08-19 06:39:21'),
(42, 'footer_company_desc', 'Lábléc - Cég leírása', 'text', 'A te megbízható ingatlan értékesítőd, Costa Blanca, Spain.', 'footer', 'company', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(43, 'footer_copyright', 'Lábléc - Szerzői jog', 'text', '© 2025 Best Homes Espana. Minden jog fenntartva.', 'footer', 'legal', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(44, 'footer_quicklinks_title', 'Lábléc - Gyorslinkek címe', 'text', 'Gyorslinkek', 'footer', 'links', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(45, 'footer_services_title', 'Lábléc - Szolgáltatások címe', 'text', 'Szolgáltatások', 'footer', 'services', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(46, 'footer_service_1', 'Lábléc - Szolgáltatás 1', 'text', 'Ingatlanértékesítés', 'footer', 'services', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(47, 'footer_service_2', 'Lábléc - Szolgáltatás 2', 'text', 'Ingatlanbérbeadás', 'footer', 'services', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(48, 'footer_service_3', 'Lábléc - Szolgáltatás 3', 'text', 'Befektetési tanácsadás', 'footer', 'services', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(49, 'footer_service_4', 'Lábléc - Szolgáltatás 4', 'text', 'Ingatlankezelés', 'footer', 'services', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(50, 'property_detail_breadcrumb', 'Ingatlan - Breadcrumb', 'text', 'Ingatlan részletek', 'property_detail', 'navigation', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(51, 'property_detail_no_images', 'Ingatlan - Nincs kép', 'text', 'Nincsenek elérhető képek', 'property_detail', 'images', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(52, 'property_detail_description_title', 'Ingatlan - Leírás címe', 'text', 'Leírás', 'property_detail', 'content', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(53, 'property_detail_features_title', 'Ingatlan - Jellemzők címe', 'text', 'Jellemzők', 'property_detail', 'content', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(54, 'property_detail_details_title', 'Ingatlan - Adatok címe', 'text', 'Ingatlan adatok', 'property_detail', 'content', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(55, 'property_detail_share_title', 'Ingatlan - Megosztás címe', 'text', 'Ingatlan megosztása', 'property_detail', 'content', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(56, 'quick_contact_title', 'Gyors kapcsolat - Cím', 'text', 'Gyors érdeklődés', 'property_detail', 'quick_contact', 1, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(57, 'quick_contact_name_placeholder', 'Gyors kapcsolat - Név placeholder', 'text', 'Név', 'property_detail', 'quick_contact', 2, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(58, 'quick_contact_email_placeholder', 'Gyors kapcsolat - Email placeholder', 'text', 'E-mail', 'property_detail', 'quick_contact', 3, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(59, 'quick_contact_phone_placeholder', 'Gyors kapcsolat - Telefon placeholder', 'text', 'Telefon', 'property_detail', 'quick_contact', 4, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(60, 'quick_contact_message_placeholder', 'Gyors kapcsolat - Üzenet placeholder', 'text', 'Üzenet', 'property_detail', 'quick_contact', 5, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58'),
(61, 'quick_contact_submit', 'Gyors kapcsolat - Küldés gomb', 'text', 'Küldés', 'property_detail', 'quick_contact', 6, 1, '2025-08-16 13:02:58', '2025-08-16 13:02:58');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `images`
--

INSERT INTO `images` (`id`, `property_id`, `image_path`, `image_order`, `created_at`) VALUES
(1, 6, 'uploads/689edf50c0d5e_1755242320.jpg', 1, '2025-08-15 07:18:49'),
(2, 6, 'uploads/689edf54d5735_1755242324.jpg', 2, '2025-08-15 07:18:49'),
(3, 4, 'uploads/689f654213787_1755276610.png', 1, '2025-08-15 16:50:29'),
(4, 4, 'uploads/689f65483c0f8_1755276616.png', 2, '2025-08-15 16:50:29');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('for_sale','for_rent','sold') DEFAULT 'for_sale',
  `property_id_code` varchar(50) DEFAULT NULL,
  `size_ownership_doc` int(11) DEFAULT NULL,
  `plot_size` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `building_type` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `overall_condition` varchar(100) DEFAULT NULL,
  `accessibility` varchar(100) DEFAULT NULL,
  `building_material` varchar(100) DEFAULT NULL,
  `furnished` tinyint(1) DEFAULT 0,
  `airbnb_suitable` tinyint(1) DEFAULT 0,
  `insulation` tinyint(1) DEFAULT 0,
  `special_offers` text DEFAULT NULL,
  `view` varchar(100) DEFAULT NULL,
  `orientation` varchar(100) DEFAULT NULL,
  `noise_level` varchar(100) DEFAULT NULL,
  `floor_level` varchar(50) DEFAULT NULL,
  `garden` tinyint(1) DEFAULT 0,
  `terrace` tinyint(1) DEFAULT 0,
  `parking` tinyint(1) DEFAULT 0,
  `utilities` tinyint(1) DEFAULT 0,
  `wheelchair_access` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `new_build` tinyint(1) DEFAULT 0,
  `ref_code` varchar(50) DEFAULT NULL,
  `virtual_tour_url` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'EUR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `price`, `status`, `property_id_code`, `size_ownership_doc`, `plot_size`, `rooms`, `building_type`, `city`, `overall_condition`, `accessibility`, `building_material`, `furnished`, `airbnb_suitable`, `insulation`, `special_offers`, `view`, `orientation`, `noise_level`, `floor_level`, `garden`, `terrace`, `parking`, `utilities`, `wheelchair_access`, `created_at`, `updated_at`, `new_build`, `ref_code`, `virtual_tour_url`, `latitude`, `longitude`, `currency`) VALUES
(3, 'Beachfront Penthouse', 'Exclusive penthouse with direct beach access. 3 bedrooms, 2 bathrooms, and spectacular ocean views from every room.', 1200000.00, 'for_sale', 'CB003', 180, 0, NULL, 'apartment', 'Benidorm', 'excellent', NULL, NULL, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, 0, 0, '2025-08-12 12:42:16', '2025-08-18 06:35:27', 0, NULL, NULL, NULL, NULL, 'EUR'),
(4, 'Charming Townhouse', 'Traditional Spanish townhouse with modern amenities. 3 bedrooms, patio, and rooftop terrace with mountain views.', 320000.00, 'for_sale', 'CB004', 140, 50, NULL, 'apartment', 'Denia', 'good', '', '', 0, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-08-12 12:42:16', '2025-09-15 08:27:31', 0, NULL, NULL, NULL, NULL, 'EUR'),
(6, 'Teszt apartman 1', 'WAOIDWOIUAHDOIUawdasd', 400.00, 'for_sale', 'ww3e12', 400, 123, NULL, 'house', NULL, 'excellent', '', 'Tégla', 1, 1, 1, '', 'Tengerre', '', '', '', 1, 0, 0, 1, 0, '2025-08-13 07:15:53', '2025-08-18 07:53:23', 0, NULL, NULL, NULL, NULL, 'EUR'),
(7, 'wad', 'wadsd', 500.00, 'for_sale', 'sw230', 312, 231432, 3, 'new_build_apartment', 'Torrevieja', 'good', '', 'Tégla', 0, 0, 1, '', 'Hegyre', '', '', '', 0, 0, 1, 1, 0, '2025-08-14 09:15:53', '2025-08-19 08:25:33', 0, NULL, NULL, NULL, NULL, 'EUR'),
(498, 'New Build \n Villa \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The villas in Finestrat Paradise Resort are situated on spacious plots from 527 m² to 688 m², with a total constructed area of 317.50 m². In addition, they have two open terraces of 28 m² and 8 m², as well as a 62 m² solarium to enjoy the wonderful weather and views. These exclusive villas offer 4 bedrooms and 4 bathrooms, as well as an underground parking area of 73 m² with capacity for 2 cars.&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 1150000.00, 'for_sale', '', NULL, NULL, 4, 'new_build_villa', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:49:55', 1, '624', '', 38.54356900, -0.18505400, 'EUR'),
(500, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The ground floor properties have generous private gardens ranging. These green areas offer direct access to the swimming pool and communal areas, creating a perfect space to relax and enjoy the surroundings to the full. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 395000.00, 'for_sale', '', NULL, NULL, 2, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:50:02', 1, '625', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(501, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The ground floor properties have generous private gardens ranging. These green areas offer direct access to the swimming pool and communal areas, creating a perfect space to relax and enjoy the surroundings to the full.&amp;nbsp;Each apartment comes with two parking spaces and two storage rooms in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 565000.00, 'for_sale', '', NULL, NULL, 3, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:50:12', 1, '626', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(503, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The apartments have 2 bedrooms and 2 bathrooms, all with spacious terraces to enjoy the fresh air, the views and the Mediterranean climate. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 375000.00, 'for_sale', '', NULL, NULL, 2, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:50:21', 1, '627', '', 38.54356900, -0.18505400, 'EUR'),
(504, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The apartments have 3 bedrooms and 2 bathrooms, all with spacious terraces to enjoy the fresh air, the views and the Mediterranean climate. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 445000.00, 'for_sale', '', NULL, NULL, 3, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:50:47', 1, '628', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(505, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The penthouses stand out for their private solarium, accessed via a private staircase from the main terrace. From here, you can enjoy breathtaking views of the sea and the iconic Benidorm skyline. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 485000.00, 'for_sale', '', NULL, NULL, 2, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:50:53', 1, '629', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(506, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The penthouses stand out for their private solarium, accessed via a private staircase from the main terrace. From here, you can enjoy breathtaking views of the sea and the iconic Benidorm skyline. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 575000.00, 'for_sale', '', NULL, NULL, 3, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:51:00', 1, '630', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(627, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The ground floor properties have generous private gardens ranging. These green areas offer direct access to the swimming pool and communal areas, creating a perfect space to relax and enjoy the surroundings to the full.&amp;nbsp; Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 515000.00, 'for_sale', '', NULL, NULL, 3, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:51:06', 1, '745', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR'),
(636, 'New Build \n Apartment \n in Finestrat', '\n Finestrat Paradise Resort is an exclusive residential complex consisting of 66 apartments and 14 villas, situated in a privileged location in Finestrat. Just a few minutes from the stunning beaches of Benidorm, surrounded by shopping centres, luxury hotels, golf courses and a wide range of leisure activities, this resort is ideal for those looking for quality of life and comfort.&#13;&#13; Each property has been designed to offer maximum privacy, with large sea-facing terraces, private gardens on the ground floors and solariums on the upper floors.&#13;&#13; &#13;&#13; The complex has extensive resort-style communal areas, including swimming pools, gymnasium, coworking areas, sports facilities and green spaces, designed for relaxation and enjoyment of the natural surroundings.&#13;&#13; &#13;&#13; The villas of Finestrat Paradise Resort are fully integrated into the residential complex, with access to all the services and amenities of the resort.&#13;&#13; &#13;&#13; Committed to sustainability, Finestrat Paradise Resort has properties with high energy efficiency, with A classification and various ecological measures that not only care for the environment, but also allow significant savings in household expense.&#13;&#13; &#13;&#13; The penthouses stand out for their private solarium, accessed via a private staircase from the main terrace. From here, you can enjoy breathtaking views of the sea and the iconic Benidorm skyline. Each apartment comes with one parking space and one storage room in the underground car park, included in the price and the complex comes with a parking area for bicycles.&amp;nbsp;&#13;&#13; Prices subject to availability. Contact us for more information.&#13;&#13; \n', 595000.00, 'for_sale', '', NULL, NULL, 3, 'new_build_apartment', 'Finestrat', '', '', '', 1, 0, 0, '', '', '', '', '', 1, 1, 1, 0, 0, '2025-09-15 07:45:12', '2025-09-15 08:51:12', 1, '754', 'https://my.matterport.com/show/?m=BTsEgim14pz', 38.54356900, -0.18505400, 'EUR');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `property_features`
--

CREATE TABLE `property_features` (
  `id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `feature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `property_features`
--

INSERT INTO `property_features` (`id`, `property_id`, `feature`) VALUES
(131, 498, 'Air conditioning'),
(132, 498, 'Home appliances'),
(133, 498, 'Garage'),
(134, 498, 'Garden'),
(135, 498, 'Private pool'),
(136, 498, 'Reinforced door'),
(137, 498, 'Solarium'),
(138, 498, 'Terrace'),
(139, 498, 'Storage room'),
(140, 498, 'Underfloor heating throughout the property'),
(141, 498, 'Domotics system'),
(142, 498, 'Aerothermal system'),
(143, 498, 'Alarm'),
(144, 498, 'Solar panels'),
(145, 498, 'Pre-installation electric vehicle charging point'),
(146, 498, 'Built-in wardrobes'),
(147, 498, 'Electric shutters'),
(148, 500, 'Air conditioning'),
(149, 500, 'Lift'),
(150, 500, 'Home appliances'),
(151, 500, 'Garden'),
(152, 500, 'Communal swimming pool'),
(153, 500, 'Reinforced door'),
(154, 500, 'Terrace'),
(155, 500, 'Storage room'),
(156, 500, 'Underfloor heating in bathrooms'),
(157, 500, 'Domotics system'),
(158, 500, 'Aerothermal system'),
(159, 500, 'Alarm'),
(160, 500, 'Built-in wardrobes'),
(161, 500, 'Electric shutters'),
(162, 501, 'Air conditioning'),
(163, 501, 'Lift'),
(164, 501, 'Home appliances'),
(165, 501, 'Garden'),
(166, 501, 'Communal swimming pool'),
(167, 501, 'Reinforced door'),
(168, 501, 'Terrace'),
(169, 501, 'Storage room'),
(170, 501, 'Underfloor heating in bathrooms'),
(171, 501, 'Domotics system'),
(172, 501, 'Aerothermal system'),
(173, 501, 'Alarm'),
(174, 501, 'Built-in wardrobes'),
(175, 501, 'Electric shutters'),
(176, 503, 'Air conditioning'),
(177, 503, 'Lift'),
(178, 503, 'Home appliances'),
(179, 503, 'Communal swimming pool'),
(180, 503, 'Reinforced door'),
(181, 503, 'Terrace'),
(182, 503, 'Storage room'),
(183, 503, 'Underfloor heating in bathrooms'),
(184, 503, 'Domotics system'),
(185, 503, 'Aerothermal system'),
(186, 503, 'Alarm'),
(187, 503, 'Built-in wardrobes'),
(188, 503, 'Electric shutters'),
(189, 504, 'Air conditioning'),
(190, 504, 'Lift'),
(191, 504, 'Home appliances'),
(192, 504, 'Communal swimming pool'),
(193, 504, 'Reinforced door'),
(194, 504, 'Terrace'),
(195, 504, 'Storage room'),
(196, 504, 'Underfloor heating in bathrooms'),
(197, 504, 'Domotics system'),
(198, 504, 'Aerothermal system'),
(199, 504, 'Alarm'),
(200, 504, 'Built-in wardrobes'),
(201, 504, 'Electric shutters'),
(202, 505, 'Air conditioning'),
(203, 505, 'Lift'),
(204, 505, 'Home appliances'),
(205, 505, 'Communal swimming pool'),
(206, 505, 'Reinforced door'),
(207, 505, 'Solarium'),
(208, 505, 'Terrace'),
(209, 505, 'Storage room'),
(210, 505, 'Underfloor heating in bathrooms'),
(211, 505, 'Domotics system'),
(212, 505, 'Aerothermal system'),
(213, 505, 'Alarm'),
(214, 505, 'Solar panels'),
(215, 505, 'Built-in wardrobes'),
(216, 505, 'Electric shutters'),
(217, 506, 'Air conditioning'),
(218, 506, 'Lift'),
(219, 506, 'Home appliances'),
(220, 506, 'Communal swimming pool'),
(221, 506, 'Reinforced door'),
(222, 506, 'Solarium'),
(223, 506, 'Terrace'),
(224, 506, 'Storage room'),
(225, 506, 'Underfloor heating in bathrooms'),
(226, 506, 'Domotics system'),
(227, 506, 'Aerothermal system'),
(228, 506, 'Alarm'),
(229, 506, 'Solar panels'),
(230, 506, 'Built-in wardrobes'),
(231, 506, 'Electric shutters'),
(232, 627, 'Air conditioning'),
(233, 627, 'Lift'),
(234, 627, 'Home appliances'),
(235, 627, 'Garden'),
(236, 627, 'Communal swimming pool'),
(237, 627, 'Reinforced door'),
(238, 627, 'Terrace'),
(239, 627, 'Storage room'),
(240, 627, 'Underfloor heating in bathrooms'),
(241, 627, 'Domotics system'),
(242, 627, 'Aerothermal system'),
(243, 627, 'Alarm'),
(244, 627, 'Built-in wardrobes'),
(245, 627, 'Electric shutters'),
(246, 636, 'Air conditioning'),
(247, 636, 'Lift'),
(248, 636, 'Home appliances'),
(249, 636, 'Communal swimming pool'),
(250, 636, 'Reinforced door'),
(251, 636, 'Solarium'),
(252, 636, 'Terrace'),
(253, 636, 'Storage room'),
(254, 636, 'Underfloor heating in bathrooms'),
(255, 636, 'Domotics system'),
(256, 636, 'Aerothermal system'),
(257, 636, 'Alarm'),
(258, 636, 'Solar panels'),
(259, 636, 'Built-in wardrobes'),
(260, 636, 'Electric shutters');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- A tábla adatainak kiíratása `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_id`, `image_url`, `is_primary`) VALUES
(132, 498, 1, 'https://silcestates.com/media/images/properties/o_1i7ds27eruhq1bga160f15ft1be71r.jpg', 1),
(133, 498, 2, 'https://silcestates.com/media/images/properties/o_1i7ds27er9ubg4cb2au5iham1s.jpg', 0),
(134, 498, 3, 'https://silcestates.com/media/images/properties/o_1i7ds27er4nv13q3j1v1t4t137l23.jpg', 0),
(135, 498, 4, 'https://silcestates.com/media/images/properties/o_1i9tuc23g8j4bvm17v6hpqng11g.JPG', 0),
(136, 498, 5, 'https://silcestates.com/media/images/properties/o_1i7ds27er1qnb1ib61smeehf1koq24.jpg', 0),
(137, 498, 6, 'https://silcestates.com/media/images/properties/o_1i7ds27er1d301t5n11oflb11iu41t.jpg', 0),
(138, 498, 7, 'https://silcestates.com/media/images/properties/o_1i7ds27erca6rbl1sv38en8rm1u.jpg', 0),
(139, 498, 8, 'https://silcestates.com/media/images/properties/o_1i7ds27er10cn6pd1pbmrac103p22.jpg', 0),
(140, 498, 9, 'https://silcestates.com/media/images/properties/o_1i7ds27er1qdq2oe1tek14851v811v.jpg', 0),
(141, 498, 10, 'https://silcestates.com/media/images/properties/o_1i7ds27er1mgrk9k1nau1vir10em20.jpg', 0),
(142, 498, 11, 'https://silcestates.com/media/images/properties/o_1i7ds27er1608dii16i11ldd7tu21.jpg', 0),
(143, 498, 12, 'https://silcestates.com/media/images/properties/o_1i7ds27erm041rskfnspsg16eg25.jpg', 0),
(144, 498, 13, 'https://silcestates.com/media/images/properties/o_1i7ds27er1nro1n8lfp5bv28jv26.jpg', 0),
(145, 500, 1, 'https://silcestates.com/media/images/properties/o_1i8fgh9i9md818u41o6eccv1t4q1r.jpg', 1),
(146, 500, 2, 'https://silcestates.com/media/images/properties/o_1i8fgh9i9v8bc6a1k69an7136i1s.jpg', 0),
(147, 500, 3, 'https://silcestates.com/media/images/properties/o_1i9tu47db1b8i16n0125us15mhd1g.JPG', 0),
(148, 500, 4, 'https://silcestates.com/media/images/properties/o_1i8fgh9i919h6jbn56q6m713561u.jpg', 0),
(149, 500, 5, 'https://silcestates.com/media/images/properties/o_1i8fgh9i91hvbmvv162ck5cdi01t.jpg', 0),
(150, 500, 6, 'https://silcestates.com/media/images/properties/o_1i8fgh9i9lqkps21i561e9noun21.jpg', 0),
(151, 500, 7, 'https://silcestates.com/media/images/properties/o_1i8fgh9i9dpm15fmlvg1pjquln22.jpg', 0),
(152, 500, 8, 'https://silcestates.com/media/images/properties/o_1i8fgh9i94l51do41odd1f5up1p23.jpg', 0),
(153, 500, 9, 'https://silcestates.com/media/images/properties/o_1i8fgh9i91puc4bo3vitdbg2t24.jpg', 0),
(154, 500, 10, 'https://silcestates.com/media/images/properties/o_1i8fgh9i91mlhsv02701tal1cu125.jpg', 0),
(155, 500, 11, 'https://silcestates.com/media/images/properties/o_1i8fgh9i919q61lm7fit1eoe15uu26.jpg', 0),
(156, 500, 12, 'https://silcestates.com/media/images/properties/o_1i8fgh9i9m8kvr1v3h1me2u4p1v.jpg', 0),
(157, 500, 13, 'https://silcestates.com/media/images/properties/o_1i8fgnpmvh8kf361or9i6m4ej53.jpg', 0),
(158, 500, 14, 'https://silcestates.com/media/images/properties/o_1i8fgnpmvk1v4ul1c6ui231i9m54.jpg', 0),
(159, 501, 1, 'https://silcestates.com/media/images/properties/o_1i9tu5ejh1edjnmh1uhotvv1rfo1g.JPG', 1),
(160, 501, 2, 'https://silcestates.com/media/images/properties/d66f170b16111166f170b16114e.jpg', 0),
(161, 501, 3, 'https://silcestates.com/media/images/properties/d66f170af8509866f170af850d5.jpg', 0),
(162, 501, 4, 'https://silcestates.com/media/images/properties/d66f170b51e41766f170b51e454.jpg', 0),
(163, 501, 5, 'https://silcestates.com/media/images/properties/d66f170b34939e66f170b3493ec.jpg', 0),
(164, 501, 6, 'https://silcestates.com/media/images/properties/d66f170baaaee366f170baaaf20.jpg', 0),
(165, 501, 7, 'https://silcestates.com/media/images/properties/d66f170bc7875766f170bc78794.jpg', 0),
(166, 501, 8, 'https://silcestates.com/media/images/properties/d66f170be4705666f170be470be.jpg', 0),
(167, 501, 9, 'https://silcestates.com/media/images/properties/d66f170c00f19066f170c00f1d9.jpg', 0),
(168, 501, 10, 'https://silcestates.com/media/images/properties/d66f170c1d163866f170c1d1675.jpg', 0),
(169, 501, 11, 'https://silcestates.com/media/images/properties/d66f170c39cad166f170c39cb0d.jpg', 0),
(170, 501, 12, 'https://silcestates.com/media/images/properties/d66f170b6e3a2a66f170b6e3a69.jpg', 0),
(171, 501, 13, 'https://silcestates.com/media/images/properties/d66f170c56907566f170c5690b1.jpg', 0),
(172, 501, 14, 'https://silcestates.com/media/images/properties/d66f170b8c3c9f66f170b8c3cd7.jpg', 0),
(173, 503, 1, 'https://silcestates.com/media/images/properties/d66f178930ea3666f178930ea73.jpg', 1),
(174, 503, 2, 'https://silcestates.com/media/images/properties/d66f17894e74a766f17894e74e4.jpg', 0),
(175, 503, 3, 'https://silcestates.com/media/images/properties/d66f178989eb2466f178989eb62.jpg', 0),
(176, 503, 4, 'https://silcestates.com/media/images/properties/o_1i9tu6ln5e3f1nk817tk15ll1ec41g.JPG', 0),
(177, 503, 5, 'https://silcestates.com/media/images/properties/o_1i8fkea281af9kacbhvb3ftjv1g.jpg', 0),
(178, 503, 6, 'https://silcestates.com/media/images/properties/d66f1789e3437e66f1789e343bb.jpg', 0),
(179, 503, 7, 'https://silcestates.com/media/images/properties/d66f1789ff1ff666f1789ff2034.jpg', 0),
(180, 503, 8, 'https://silcestates.com/media/images/properties/d66f178a1c329266f178a1c32ce.jpg', 0),
(181, 503, 9, 'https://silcestates.com/media/images/properties/d66f178a389ac166f178a389afe.jpg', 0),
(182, 503, 10, 'https://silcestates.com/media/images/properties/d66f178a5594b566f178a5594f2.jpg', 0),
(183, 503, 11, 'https://silcestates.com/media/images/properties/d66f178a7279ad66f178a7279ea.jpg', 0),
(184, 503, 12, 'https://silcestates.com/media/images/properties/d66f1789a712fe66f1789a7133b.jpg', 0),
(185, 503, 13, 'https://silcestates.com/media/images/properties/d66f178a8ec0ef66f178a8ec12d.jpg', 0),
(186, 503, 14, 'https://silcestates.com/media/images/properties/d66f1789c4f50466f1789c4f541.jpg', 0),
(187, 504, 1, 'https://silcestates.com/media/images/properties/d66f17c13013f766f17c1301435.jpg', 1),
(188, 504, 2, 'https://silcestates.com/media/images/properties/o_1i9tu7lpj1u9u1n7e1ortic11ujj1g.JPG', 0),
(189, 504, 3, 'https://silcestates.com/media/images/properties/d66f17c14d55a266f17c14d55e0.jpg', 0),
(190, 504, 4, 'https://silcestates.com/media/images/properties/d66f17c188402666f17c188407f.jpg', 0),
(191, 504, 5, 'https://silcestates.com/media/images/properties/d66f17c16b49dd66f17c16b4a1c.jpg', 0),
(192, 504, 6, 'https://silcestates.com/media/images/properties/d66f17c062b6e266f17c062b71f.jpg', 0),
(193, 504, 7, 'https://silcestates.com/media/images/properties/d66f17c07eff4e66f17c07eff9e.jpg', 0),
(194, 504, 8, 'https://silcestates.com/media/images/properties/d66f17c09c55b466f17c09c55f1.jpg', 0),
(195, 504, 9, 'https://silcestates.com/media/images/properties/d66f17c0d7186b66f17c0d718a8.jpg', 0),
(196, 504, 10, 'https://silcestates.com/media/images/properties/d66f17c0b9f53c66f17c0b9f579.jpg', 0),
(197, 504, 11, 'https://silcestates.com/media/images/properties/d66f17c0289d5b66f17c0289d98.jpg', 0),
(198, 504, 12, 'https://silcestates.com/media/images/properties/d66f17c0f3c51f66f17c0f3c55b.jpg', 0),
(199, 504, 13, 'https://silcestates.com/media/images/properties/d66f17c04594db66f17c0459537.jpg', 0),
(200, 504, 14, 'https://silcestates.com/media/images/properties/d66f17c111c0a966f17c111c0e7.jpg', 0),
(201, 505, 1, 'https://silcestates.com/media/images/properties/d66f17d69d5ae666f17d69d5b22.jpg', 1),
(202, 505, 2, 'https://silcestates.com/media/images/properties/d66f17d680c49c66f17d680c4d8.jpg', 0),
(203, 505, 3, 'https://silcestates.com/media/images/properties/d66f17d6d8d1ab66f17d6d8d1dc.jpg', 0),
(204, 505, 4, 'https://silcestates.com/media/images/properties/o_1i9tu8t0v7ajd2d1oi4169s1fgt1m.JPG', 0),
(205, 505, 5, 'https://silcestates.com/media/images/properties/d66f17d5b487e166f17d5b4881f.jpg', 0),
(206, 505, 6, 'https://silcestates.com/media/images/properties/d66f17d5d1b8b466f17d5d1b8f2.jpg', 0),
(207, 505, 7, 'https://silcestates.com/media/images/properties/d66f17d5ee52af66f17d5ee52ec.jpg', 0),
(208, 505, 8, 'https://silcestates.com/media/images/properties/d66f17d627ac6e66f17d627acab.jpg', 0),
(209, 505, 9, 'https://silcestates.com/media/images/properties/d66f17d60ab4e866f17d60ab525.jpg', 0),
(210, 505, 10, 'https://silcestates.com/media/images/properties/d66f17d57a389666f17d57a38d4.jpg', 0),
(211, 505, 11, 'https://silcestates.com/media/images/properties/o_1i9tu8t0v1aql1rjp42a1prvph71j.JPG', 0),
(212, 505, 12, 'https://silcestates.com/media/images/properties/o_1i9tu8t0vnsn1pelk5pfv3mgn1k.JPG', 0),
(213, 505, 13, 'https://silcestates.com/media/images/properties/o_1i9tu8t0v6e5bdrqis71dhd1l.JPG', 0),
(214, 505, 14, 'https://silcestates.com/media/images/properties/d66f17d644452066f17d644455e.jpg', 0),
(215, 505, 15, 'https://silcestates.com/media/images/properties/d66f17d59746a166f17d59746e3.jpg', 0),
(216, 505, 16, 'https://silcestates.com/media/images/properties/d66f17d662bb4366f17d662bb7f.jpg', 0),
(217, 506, 1, 'https://silcestates.com/media/images/properties/o_1i9tuahtp1uin126a1v4e103412u11m.JPG', 1),
(218, 506, 2, 'https://silcestates.com/media/images/properties/d66f1830fe7a2c66f1830fe7a73.jpg', 0),
(219, 506, 3, 'https://silcestates.com/media/images/properties/d66f18311c929966f18311c92ce.jpg', 0),
(220, 506, 4, 'https://silcestates.com/media/images/properties/d66f1830e1531f66f1830e15365.jpg', 0),
(221, 506, 5, 'https://silcestates.com/media/images/properties/d66f1831ea4dad66f1831ea4dea.jpg', 0),
(222, 506, 6, 'https://silcestates.com/media/images/properties/d66f183207148d66f18320714cb.jpg', 0),
(223, 506, 7, 'https://silcestates.com/media/images/properties/d66f183177a3a766f183177a3e6.jpg', 0),
(224, 506, 8, 'https://silcestates.com/media/images/properties/d66f1831b127ab66f1831b127e8.jpg', 0),
(225, 506, 9, 'https://silcestates.com/media/images/properties/d66f1831945ac466f1831945b03.jpg', 0),
(226, 506, 10, 'https://silcestates.com/media/images/properties/d66f183224197466f18322419b2.jpg', 0),
(227, 506, 11, 'https://silcestates.com/media/images/properties/o_1i9tuahtp2ibc7c6ftj8iaki1j.JPG', 0),
(228, 506, 12, 'https://silcestates.com/media/images/properties/o_1i9tuahtp1ntb1vcj1cml19i2ku61k.JPG', 0),
(229, 506, 13, 'https://silcestates.com/media/images/properties/o_1i9tuahtp17fp1v5p18s61kthhfs1l.JPG', 0),
(230, 506, 14, 'https://silcestates.com/media/images/properties/d66f18313aaa2066f18313aaa5d.jpg', 0),
(231, 506, 15, 'https://silcestates.com/media/images/properties/d66f1831cd1e6266f1831cd1eba.jpg', 0),
(232, 506, 16, 'https://silcestates.com/media/images/properties/d66f18315892be66f18315892fc.jpg', 0),
(233, 627, 1, 'https://silcestates.com/media/images/properties/d687f94a375de3687f94a375e21.JPG', 1),
(234, 627, 2, 'https://silcestates.com/media/images/properties/d687f949dd6192687f949dd61e1.jpg', 0),
(235, 627, 3, 'https://silcestates.com/media/images/properties/d687f94a191a7d687f94a191ab9.jpg', 0),
(236, 627, 4, 'https://silcestates.com/media/images/properties/d687f949821143687f949821182.jpg', 0),
(237, 627, 5, 'https://silcestates.com/media/images/properties/d687f949fb69ae687f949fb6a0c.jpg', 0),
(238, 627, 6, 'https://silcestates.com/media/images/properties/d687f9492872b5687f9492872fa.jpg', 0),
(239, 627, 7, 'https://silcestates.com/media/images/properties/d687f94946207d687f9494620c0.jpg', 0),
(240, 627, 8, 'https://silcestates.com/media/images/properties/d687f94963573e687f94963577f.jpg', 0),
(241, 627, 9, 'https://silcestates.com/media/images/properties/d687f948ebad0a687f948ebad54.jpg', 0),
(242, 627, 10, 'https://silcestates.com/media/images/properties/d687f9490a545a687f9490a5499.jpg', 0),
(243, 627, 11, 'https://silcestates.com/media/images/properties/d687f948addd81687f948adddc1.jpg', 0),
(244, 627, 12, 'https://silcestates.com/media/images/properties/d687f9499e9a9b687f9499e9ad8.jpg', 0),
(245, 627, 13, 'https://silcestates.com/media/images/properties/d687f948cb4167687f948cb41a5.jpg', 0),
(246, 627, 14, 'https://silcestates.com/media/images/properties/d687f949bec350687f949bec396.jpg', 0),
(247, 636, 1, 'https://silcestates.com/media/images/properties/d689064f181db7689064f181df4.jpg', 1),
(248, 636, 2, 'https://silcestates.com/media/images/properties/d689064fcb296d689064fcb29aa.JPG', 0),
(249, 636, 3, 'https://silcestates.com/media/images/properties/d689064efa21bb689064efa21fa.jpg', 0),
(250, 636, 4, 'https://silcestates.com/media/images/properties/d689064edca80a689064edca849.jpg', 0),
(251, 636, 5, 'https://silcestates.com/media/images/properties/d689064e26946a689064e2694a7.jpg', 0),
(252, 636, 6, 'https://silcestates.com/media/images/properties/d689064f3749f0689064f374a2e.jpg', 0),
(253, 636, 7, 'https://silcestates.com/media/images/properties/d689064e44c2eb689064e44c32c.jpg', 0),
(254, 636, 8, 'https://silcestates.com/media/images/properties/d689064e805c50689064e805c90.jpg', 0),
(255, 636, 9, 'https://silcestates.com/media/images/properties/d689064e631d9d689064e631e23.jpg', 0),
(256, 636, 10, 'https://silcestates.com/media/images/properties/d689064f5554ab689064f5554f1.jpg', 0),
(257, 636, 11, 'https://silcestates.com/media/images/properties/d689064f72d0f1689064f72d12f.JPG', 0),
(258, 636, 12, 'https://silcestates.com/media/images/properties/d689064f911b7a689064f911bbb.JPG', 0),
(259, 636, 13, 'https://silcestates.com/media/images/properties/d689064fadfec7689064fadff02.JPG', 0),
(260, 636, 14, 'https://silcestates.com/media/images/properties/d689064e9cecb1689064e9cecf0.jpg', 0),
(261, 636, 15, 'https://silcestates.com/media/images/properties/d689064e085084689064e0850c6.jpg', 0),
(262, 636, 16, 'https://silcestates.com/media/images/properties/d689064ebc1097689064ebc10f7.jpg', 0);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- A tábla indexei `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_publish_at` (`publish_at`);

--
-- A tábla indexei `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `content_sections`
--
ALTER TABLE `content_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`),
  ADD KEY `idx_page` (`page`),
  ADD KEY `idx_section_key` (`section_key`),
  ADD KEY `idx_active` (`is_active`);

--
-- A tábla indexei `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- A tábla indexei `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_properties_status` (`status`),
  ADD KEY `idx_properties_building_type` (`building_type`),
  ADD KEY `idx_properties_neighborhood` (`city`),
  ADD KEY `idx_properties_price` (`price`),
  ADD KEY `idx_properties_created_at` (`created_at`),
  ADD KEY `idx_properties_type_status` (`building_type`,`status`),
  ADD KEY `idx_properties_city` (`city`),
  ADD KEY `idx_properties_rooms` (`rooms`);

--
-- A tábla indexei `property_features`
--
ALTER TABLE `property_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_property_id` (`property_id`);

--
-- A tábla indexei `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_property_id` (`property_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `content_sections`
--
ALTER TABLE `content_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT a táblához `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT a táblához `property_features`
--
ALTER TABLE `property_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;

--
-- AUTO_INCREMENT a táblához `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
