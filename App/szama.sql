-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 22, 2026 at 11:43 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `szama`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dostep`
--

CREATE TABLE `dostep` (
  `id` int(11) NOT NULL,
  `dostep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dostep`
--

INSERT INTO `dostep` (`id`, `dostep`) VALUES
(2, 'administrator'),
(1, 'użytkownik');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kupony`
--

CREATE TABLE `kupony` (
  `id` int(11) NOT NULL,
  `nazwa` text DEFAULT NULL,
  `cena` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kupony`
--

INSERT INTO `kupony` (`id`, `nazwa`, `cena`) VALUES
(1, '2 hot-dogi', 10),
(2, 'Bułka szynka + Bułka ser', 5),
(3, 'Każdy rozmiar Tymbarków', 13);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kupony_produkty`
--

CREATE TABLE `kupony_produkty` (
  `id` int(11) NOT NULL,
  `id_kuponu` int(11) NOT NULL,
  `id_produktu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kupony_produkty`
--

INSERT INTO `kupony_produkty` (`id`, `id_kuponu`, `id_produktu`) VALUES
(1, 1, 11),
(2, 1, 11),
(3, 2, 4),
(4, 2, 5),
(5, 3, 17),
(6, 3, 19),
(7, 3, 20),
(8, 3, 21);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(30) NOT NULL,
  `skład` text NOT NULL,
  `cena` float NOT NULL,
  `typ` int(11) NOT NULL,
  `zdjecie` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produkty`
--

INSERT INTO `produkty` (`id`, `nazwa`, `skład`, `cena`, `typ`, `zdjecie`) VALUES
(3, 'Buła Gołosza', '', 6, 1, 'b1.jpg'),
(4, 'Bułka Ser', '', 3, 1, 'b2.jpg'),
(5, 'Bułka Szynka', '', 3, 1, 'b3.jpg'),
(6, 'Bułka Szynka Ser', '', 4, 1, 'b4.jpg'),
(7, 'Bułka Sos', '', 3, 1, 'b5.jpg'),
(8, 'Bułka Masło', '', 2, 1, 'b6.jpg'),
(9, 'Bułka Sucha', '', 1.5, 1, 'b7.jpg'),
(10, 'Bułka Ciemna', '', 0, 1, 'b8.jpg'),
(11, 'Hot-Dog', '', 6, 2, 'h1.jpg'),
(12, 'Double-Dog', '', 8, 2, 'h2.jpg'),
(13, 'Tost Ser', '', 2.5, 3, 't1.jpg'),
(14, 'Tost Szynka', '', 2.5, 3, 't2.jpg'),
(15, 'Tost Masło', '', 1.5, 3, 't3.jpg'),
(16, 'Tost Ser Szynka', '', 4, 3, 't4.jpg'),
(17, 'Tymbark Karton 1L', '', 4.5, 4, 'n1.jpg'),
(18, 'Woda Gaz/N-gaz', '', 2.5, 4, 'n2.jpg'),
(19, 'Tymbark 2L', '', 5, 4, 'n3.jpg'),
(20, 'Tymbark Szkło 0,25L', '', 2.5, 4, 'n4.jpg'),
(21, 'Tymbark Plastik 0,5L', '', 3, 4, 'n5.jpg'),
(22, 'Herbata', '', 2.5, 4, 'n6.jpg'),
(23, 'Espresso', '', 1.5, 6, 'k1.jpg'),
(24, 'Espresso Macchiato', '', 2.5, 6, 'k2.jpg'),
(25, 'Kawa Czarna', '', 2, 6, 'k3.jpg'),
(26, 'Kawa Biała', '', 2.5, 6, 'k4.jpg'),
(27, 'Cappuccino', '', 3.5, 6, 'k5.jpg'),
(28, 'Late Macchiato', '', 3.5, 6, 'k6.jpg'),
(29, 'Double Shot Espresso', '', 1.5, 6, 'k7.jpg'),
(30, 'BBQ', '', 1, 5, 's1.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `status_zamowienia`
--

CREATE TABLE `status_zamowienia` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_zamowienia`
--

INSERT INTO `status_zamowienia` (`id`, `status`) VALUES
(1, 'w trakcie'),
(2, 'ukończone'),
(3, 'anulowane');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typy_produktow`
--

CREATE TABLE `typy_produktow` (
  `id` int(11) NOT NULL,
  `typ` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `typy_produktow`
--

INSERT INTO `typy_produktow` (`id`, `typ`) VALUES
(1, 'buła'),
(2, 'hot-dog'),
(6, 'kawa'),
(4, 'napój'),
(5, 'sos'),
(3, 'tost');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `nazwa_uzytkownika` varchar(25) NOT NULL,
  `Email` varchar(35) DEFAULT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `haslo` varchar(255) DEFAULT NULL,
  `dostep` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `nazwa_uzytkownika`, `Email`, `telefon`, `haslo`, `dostep`, `status`) VALUES
(6, 'test2', 'ab@cd.ef', NULL, '$2y$10$uXemfutF9Cqo06fp2hnwrOsf3M0sKS5q2TYduHnOVlWRmWYymFjK.', 2, 1),
(7, 'Rogal', 'rogal@gmail.com', '987654321', '$2y$10$4yX6wxwJk8Fv5DcA8kCwEu8VeNSx6UaGNtQc4WPp.ulEnE1d3ieLm', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia`
--

CREATE TABLE `zamowienia` (
  `numer_zamowienia` int(11) NOT NULL,
  `uzytkownik_id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_produkty`
--

CREATE TABLE `zamowienia_produkty` (
  `id` int(11) NOT NULL,
  `numer_zamowienia` int(11) NOT NULL,
  `ilosc` int(11) NOT NULL,
  `produkt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dostep`
--
ALTER TABLE `dostep`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `dostep` (`dostep`);

--
-- Indeksy dla tabeli `kupony`
--
ALTER TABLE `kupony`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `kupony_produkty`
--
ALTER TABLE `kupony_produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kuponu` (`id_kuponu`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- Indeksy dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `nazwa` (`nazwa`),
  ADD KEY `Produkty_fk4` (`typ`);

--
-- Indeksy dla tabeli `status_zamowienia`
--
ALTER TABLE `status_zamowienia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeksy dla tabeli `typy_produktow`
--
ALTER TABLE `typy_produktow`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `typ` (`typ`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `nazwa_uzytkownika` (`nazwa_uzytkownika`),
  ADD KEY `Uzytkownicy_fk3` (`dostep`);

--
-- Indeksy dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`numer_zamowienia`),
  ADD UNIQUE KEY `numer_zamowienia` (`numer_zamowienia`),
  ADD KEY `Zamowienia_fk1` (`uzytkownik_id`),
  ADD KEY `Zamowienia_fk3` (`status`);

--
-- Indeksy dla tabeli `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `zamowienia_produkty_fk1` (`numer_zamowienia`),
  ADD KEY `zamowienia_produkty_fk3` (`produkt`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dostep`
--
ALTER TABLE `dostep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kupony`
--
ALTER TABLE `kupony`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kupony_produkty`
--
ALTER TABLE `kupony_produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `status_zamowienia`
--
ALTER TABLE `status_zamowienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `typy_produktow`
--
ALTER TABLE `typy_produktow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `numer_zamowienia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kupony_produkty`
--
ALTER TABLE `kupony_produkty`
  ADD CONSTRAINT `kupony_produkty_ibfk_1` FOREIGN KEY (`id_kuponu`) REFERENCES `kupony` (`id`),
  ADD CONSTRAINT `kupony_produkty_ibfk_2` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id`);

--
-- Constraints for table `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `Produkty_fk4` FOREIGN KEY (`typ`) REFERENCES `typy_produktow` (`id`);

--
-- Constraints for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD CONSTRAINT `Uzytkownicy_fk3` FOREIGN KEY (`dostep`) REFERENCES `dostep` (`id`);

--
-- Constraints for table `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `Zamowienia_fk1` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownicy` (`id`),
  ADD CONSTRAINT `Zamowienia_fk3` FOREIGN KEY (`status`) REFERENCES `status_zamowienia` (`id`);

--
-- Constraints for table `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  ADD CONSTRAINT `zamowienia_produkty_fk1` FOREIGN KEY (`numer_zamowienia`) REFERENCES `zamowienia` (`numer_zamowienia`),
  ADD CONSTRAINT `zamowienia_produkty_fk3` FOREIGN KEY (`produkt`) REFERENCES `produkty` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
