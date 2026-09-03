-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Sze 03. 12:27
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `webshop`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Feldolgozás alatt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 59.99, 'Feldolgozás alatt', '2026-08-18 19:57:48'),
(2, 1, 59.99, 'Feldolgozás alatt', '2026-08-18 19:59:18'),
(3, 2, 79.99, 'Feldolgozás alatt', '2026-08-18 20:00:00'),
(4, 4, 139.98, 'Feldolgozás alatt', '2026-08-19 06:52:53'),
(5, 7, 94.99, 'Feldolgozás alatt', '2026-08-19 06:55:58'),
(6, 1, 59.99, 'Feldolgozás alatt', '2026-08-19 15:18:46'),
(7, 1, 59.99, 'Feldolgozás alatt', '2026-08-19 15:21:27'),
(8, 1, 59.99, 'Feldolgozás alatt', '2026-08-19 15:27:09'),
(9, 1, 85.49, 'Feldolgozás alatt', '2026-08-19 15:27:32'),
(10, 1, 115.99, 'Feldolgozás alatt', '2026-08-19 15:28:18'),
(11, 1, 59.99, 'Feldolgozás alatt', '2026-08-19 15:37:05'),
(12, 8, 30.50, 'Feldolgozás alatt', '2026-08-20 08:41:31'),
(13, 8, 59.99, 'Feldolgozás alatt', '2026-08-22 06:25:08');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 59.99),
(2, 2, 1, 1, 59.99),
(3, 3, 2, 1, 79.99),
(4, 4, 1, 1, 59.99),
(5, 4, 2, 1, 79.99),
(6, 5, 5, 1, 35.00),
(7, 5, 6, 1, 59.99),
(8, 6, 1, 1, 59.99),
(9, 7, 1, 1, 59.99),
(10, 8, 1, 1, 59.99),
(11, 9, 1, 1, 59.99),
(12, 9, 2, 1, 25.50),
(13, 10, 1, 1, 59.99),
(14, 10, 2, 1, 25.50),
(15, 10, 3, 1, 30.50),
(16, 11, 1, 1, 59.99),
(17, 12, 3, 1, 30.50),
(18, 13, 1, 1, 59.99);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `rating` decimal(2,1) DEFAULT 5.0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `rating`, `is_featured`, `created_at`) VALUES
(1, 'Apex Mechanical Keyboard', 'RGB háttérvilágítású, barna kapcsolós mechanikus billentyűzet fém házzal.', 59.99, 'keyboard2.jpg', 'keyboard', 4, 4.8, 1, '2026-08-18 19:18:57'),
(2, 'Pro Wireless Mouse', '20 000 DPI-s ultra-könnyű vezeték nélküli gamer egér optikai kapcsolókkal.', 25.50, 'mouse.png', 'mouse', 21, 4.9, 1, '2026-08-18 19:18:57'),
(3, 'SoundStorm Headset', '7.1-es térhangzású, memóriahabos fülpárnás fejhallgató zajszűrős mikrofonnal.', 30.50, 'headset.png', 'headset', 6, 4.7, 1, '2026-08-18 19:18:57'),
(4, 'Lite Office Keyboard', 'Csendes, vékony kialakítású membrános billentyűzet fehér háttérvilágítással.', 45.00, 'keyboard.png', 'keyboard', 0, 4.2, 0, '2026-08-18 19:18:57'),
(5, 'Ergonomic Office Mouse', 'Kézbe simuló, vertikális kialakítású egér az egész napos irodai munkához.', 35.00, 'mouse2.png', 'mouse', 11, 4.5, 0, '2026-08-18 19:18:57'),
(6, 'Nitro Wired Headset', 'Kompakt, fülre illeszkedő könnyű gamer fejhallgató tiszta hangzással.', 49.99, 'headset2.png', 'headset', 18, 4.4, 0, '2026-08-18 19:18:57');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activation_token` varchar(64) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `activation_token`, `status`, `created_at`) VALUES
(1, 'szabi', 'keriszabi6@gmail.com', '$2y$10$z4kqTTYBeXlVBMeWkAec5.B6crtfALI2aHKJAtSVUpVfWsrWZdqJG', 'f16449ff023ed0424f4178328179317fa29b6d5d5f9f50fb8dd8a9c2711315f4', 1, '2026-08-20 08:10:39'),
(3, 'szabi2', 'keriszabi7@gmail.com', '$2y$10$/2zZ7lDoiC1xRNSuNNOc8e6lgf6wJBJ5O.x91hNQ7EMPX8q7dnsmy', '934a231e8ab48fdcb4f08f2af4c8233686993718e295194a73fdd722f7d52ef4', 0, '2026-08-20 08:21:46'),
(7, 'szabi3', 'keriszabi8@gmail.com', '$2y$10$EJc2dekry7GzOTxl1vZcge2OE6NWGfAVl4wApBTlfs9mwm1s1lA2i', '5df715b462423fd0aaa4e7633c3fc36eefb916c470ede843f1be3b2a0170e0cf', 0, '2026-08-20 08:23:46'),
(8, 'szabi4', 'keriszabi9@gmail.com', '$2y$10$LLs9MhKSF65UbZEXUH8zbOc9O2V6Y7oTcn1filpXGGEQm5BEPoAU2', NULL, 1, '2026-08-20 08:24:43'),
(9, 'szabi5', 'keriszabi10@gmail.com', '$2y$10$tb8cVWUzT9Aoy6roYDD.SuXkFZ1Wi.TWDrh4XKTCOqnvnOmpHITeS', NULL, 1, '2026-08-20 08:27:02'),
(10, 'szabi6', 'keriszabi12@gmail.com', '$2y$10$LTgU2EwpmaD7o9L6auQ3f.4UPdvI9t6MLtbkwhOqEfCJG1xGUcjJ6', NULL, 1, '2026-08-20 08:28:44'),
(11, 'asd4', 'keriszabi@gmail.co', '$2y$10$0QIW/ZTRr8fbg9mVC6rOp.sW3.N6G4J/5C9mIHEeJcyfUMHPR0UJG', '47ce89d61cd4ccb2e94bb7eb5348b9ca2af818d73da20b8beab3c96a4107ed4e', 0, '2026-08-20 08:32:18'),
(14, 'asd6', 'keriszabi@gmail.com', '$2y$10$7sDa9I86KHdsUgSh0lkZ5.lnmsov/2LTeDFYOpagOKmv8gqFFUDWi', '5d03756ded9f322e9636a1f922921c7a951ffa60b3268ad2e325538026d99fc2', 0, '2026-08-20 08:33:37'),
(15, 'asd7', 'keriszabi87@gmail.com', '$2y$10$OltHOA9At8PLDGg1iN/ebubFPP9GB/lBht.UaTITtpdrtNTdH9cpu', NULL, 1, '2026-08-20 08:34:26'),
(17, 'szabi5454', 'keriszabi63434@gmail.com', '$2y$10$G4/VU7umLNAE1ikOyVitfOMcTkIv2iNbw6AKrAH/lLPco4/GgZgo6', NULL, 1, '2026-08-20 08:40:47'),
(19, 'szabi9', 'keriszabi11@gmail.com', '$2y$10$apInWTJTiN3RI81X.BqxEufiymuK2KRJPi0jJ6PnLfWSvfUshEqnu', NULL, 1, '2026-08-22 06:27:11');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- A tábla indexei `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT a táblához `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
