-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 13 Kwi 2026, 15:53
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `zegowska_szama`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dostep`
--

CREATE TABLE `dostep` (
  `id` int(11) NOT NULL,
  `dostep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(30) NOT NULL,
  `skład` text NOT NULL,
  `cena` float NOT NULL,
  `typ` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `status_zamowienia`
--

CREATE TABLE `status_zamowienia` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `status_zamowienia`
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
-- Zrzut danych tabeli `typy_produktow`
--

INSERT INTO `typy_produktow` (`id`, `typ`) VALUES
(1, 'buła'),
(5, 'dodatek'),
(2, 'hot-dog'),
(4, 'napój'),
(3, 'tost');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `nazwa_uzytkownika` varchar(25) NOT NULL,
  `haslo` varchar(50) NOT NULL,
  `dostep` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `dostep`
--
ALTER TABLE `dostep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `status_zamowienia`
--
ALTER TABLE `status_zamowienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `typy_produktow`
--
ALTER TABLE `typy_produktow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `numer_zamowienia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `Produkty_fk4` FOREIGN KEY (`typ`) REFERENCES `typy_produktow` (`id`);

--
-- Ograniczenia dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD CONSTRAINT `Uzytkownicy_fk3` FOREIGN KEY (`dostep`) REFERENCES `dostep` (`id`);

--
-- Ograniczenia dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `Zamowienia_fk1` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownicy` (`id`),
  ADD CONSTRAINT `Zamowienia_fk3` FOREIGN KEY (`status`) REFERENCES `status_zamowienia` (`id`);

--
-- Ograniczenia dla tabeli `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  ADD CONSTRAINT `zamowienia_produkty_fk1` FOREIGN KEY (`numer_zamowienia`) REFERENCES `zamowienia` (`numer_zamowienia`),
  ADD CONSTRAINT `zamowienia_produkty_fk3` FOREIGN KEY (`produkt`) REFERENCES `produkty` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
