-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pon 22. kvě 2023, 18:40
-- Verze serveru: 10.4.27-MariaDB
-- Verze PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `spizirna`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `druh`
--

CREATE TABLE `druh` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazev` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `druh`
--

INSERT INTO `druh` (`id`, `nazev`) VALUES
(1, 'ovoce'),
(2, 'zelenina'),
(3, 'maso'),
(4, 'mléčné výrobky'),
(5, 'ochucovadla'),
(6, 'ostatní');

-- --------------------------------------------------------

--
-- Struktura tabulky `potravina`
--

CREATE TABLE `potravina` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazev` text NOT NULL,
  `druh_id` int(10) UNSIGNED NOT NULL,
  `mnozstvi` double UNSIGNED NOT NULL,
  `jednotka` text NOT NULL,
  `minimum` double UNSIGNED DEFAULT NULL,
  `rozdil` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `potravina`
--

INSERT INTO `potravina` (`id`, `nazev`, `druh_id`, `mnozstvi`, `jednotka`, `minimum`, `rozdil`) VALUES
(1, 'mrkev', 2, 5.5, 'kg', 1, -4.5),
(3, 'petržel', 2, 4, 'kg', 2.5, -1.5),
(4, 'cibule', 2, 0.05, 'kg', 3, 2.95),
(5, 'pomeranče', 1, 7, 'ks', 5, -2),
(7, 'jablka', 1, 6, 'kg', 5, -1),
(8, 'sůl', 5, 2, 'kg', 3, 1),
(9, 'pepř', 5, 0.08, 'kg', 0.2, 0.12),
(10, 'mléko', 4, 4, 'l', 4.5, 0.5),
(11, 'máslo', 4, 1.5, 'kg', 2, 0.5),
(12, 'kuřecí prsa', 3, 6, 'kg', 5, 1),
(13, 'mleté maso', 3, 4.7, 'kg', 6, 1.3),
(14, 'rýže', 6, 2, 'kg', 3, 1),
(15, 'kukuřičné placky', 6, 12, 'ks', 10, -2),
(16, 'hovězí kližka', 3, 2, 'kg', 3, 1),
(17, 'olej slunečnicový', 6, 1, 'l', 3, 2),
(18, 'rajčatový protlak malý', 6, 5, 'ks', 10, 5),
(19, 'brambory', 2, 0.5, 'kg', 10, 9.5),
(20, 'ocet', 5, 2, 'l', 2, 0),
(21, 'vepřová krkovice', 3, 3, 'kg', 2, -1),
(22, 'vejce', 6, 12, 'ks', 20, 8),
(24, 'mouka hladká', 6, 1.5, 'kg', 2, 0.5),
(25, 'citrony', 1, 12, 'ks', 4, -8),
(36, 'strouhanka', 6, 2, 'kg', 1, -1),
(47, 'celer', 2, 7, 'kg', 3, -4),
(48, 'droždí', 6, 0.01, 'kg', 0.02, NULL),
(49, 'housky', 6, 20, 'ks', 10, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `potraviny_v_receptu`
--

CREATE TABLE `potraviny_v_receptu` (
  `id_receptu` int(10) UNSIGNED DEFAULT NULL,
  `id_potraviny` int(10) UNSIGNED DEFAULT NULL,
  `mnozstvi` double DEFAULT NULL,
  `jednotka` text DEFAULT NULL,
  `id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `potraviny_v_receptu`
--

INSERT INTO `potraviny_v_receptu` (`id_receptu`, `id_potraviny`, `mnozstvi`, `jednotka`, `id`) VALUES
(1, 4, 0.5, 'kg', 1),
(1, 16, 0.5, 'kg', 2),
(1, 17, 0.05, 'l', 3),
(1, 18, 1, 'ks', 4),
(1, 8, 0.002, 'kg', 5),
(1, 9, 0.002, 'kg', 6),
(2, 19, 1, 'kg', 7),
(2, 4, 0.1, 'kg', 8),
(2, 8, 0.001, 'kg', 9),
(2, 9, 0.001, 'kg', 10),
(2, 17, 0.08, 'kg', 11),
(3, 21, 0.5, 'kg', 12),
(3, 36, 0.3, 'kg', 13),
(3, 22, 3, 'ks', 14),
(3, 24, 0.3, 'kg', 15),
(3, 8, 0.001, 'kg', 16),
(3, 9, 0.001, 'kg', 17),
(4, 48, 0.01, 'kg', 18),
(4, 49, 2, 'ks', 19),
(4, 22, 1, 'ks', 20),
(4, 24, 0.35, 'kg', 21),
(4, 10, 0.25, 'l', 22),
(4, 8, 0.001, 'kg', 23);

-- --------------------------------------------------------

--
-- Struktura tabulky `recept`
--

CREATE TABLE `recept` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazev` text DEFAULT NULL,
  `postup` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `recept`
--

INSERT INTO `recept` (`id`, `nazev`, `postup`) VALUES
(1, 'Hovězí guláš', 'Cibuli oloupeme a nakrájíme nadrobno. V kastrole rozehřejeme sádlo a cibuli na něm restujeme dohněda, trvá to asi 15 minut.\nNa cibuli přidáme papriku, dobře promícháme a postupujeme rychle, aby paprika nezhořkla. Následně přidáme kousky masa, ze všech stran je opečeme a dobře mícháme (guláš se má přichytávat ke dnu hrnce, ale ne připalovat). Když je maso opečené, osolíme (cca 10 špetek) a opepříme.\nPřidáme rajčatový protlak, dobře promícháme a zalijeme malým množstvím vody - asi 2 dl a necháme dusit na co nejnižší teplotě.\nGuláš stále hlídáme a občas mícháme. Přiléváme vodu po menších částech, protože jak již bylo řečeno, měl by se přichytávat, ovšem nikoli připalovat.\nPo cca hodině a půl maso ochutnáme a pokud je měkké, přidáme v dlaních rozemnutou majoránku a povaříme ještě 10 minut.\nGuláš nemusíme (a ani bychom neměli) zahušťovat, postaral se o to rozvařený cibulový základ. Jediné povolené zahušťovadlo je v případě potřeby chlebová strouhanka.\nPodáváme posypané čerstvě nakrájenou cibulí, nejlépe s houskovým knedlíkem, případně s bramboráčky nebo těstovinami.'),
(2, 'Bramborový salát', 'Brambory uvaříme ve šlupce. Oloupané brambory nakrájíme na tenká půlkolečka a cibulku na drobno.\nVše dáme do mísy a osolíme a opepříme.\nOlej smícháme s octem a vmícháme do salátu.\nPřed podáváním necháme chvilku odležet.'),
(3, 'Vepřový řízek', 'Maso nakrájíme na plátky. Položíme na prkénko a z obou stran naklepeme. Poté z obou stran osolíme a opepříme.\nDo tří hlubokých talířů připravíme ingredience na obalování. Do jednoho vsypeme hladkou mouku. Do druhého vyklepneme dvě vejce a prošleháme je s mlékem, špetkou soli a špetkou pepře. Do třetího vsypeme strouhanku.\nMaso důkladně obalíme v mouce, pak ve vejcích a nakonec strouhance.\nPro lepší konzistenci obalu je dobré postupovat tak, že obalíme v mouce, pak vejcích, pak zase v mouce, opět ve vejcích a nakonec ve strouhance.\nSmažíme na pánvi v dostatečném množství oleje.'),
(4, 'Houskový knedlík', 'Droždí rozdrobíme do mísy, mírně posolíme a necháme pár minut stát, až se rozteče. Poté přilijeme vlažné mléko, přidáme vejce a rozmícháme. Přisypeme trochu mouky, rozmícháme, znovu přisypeme mouku smíchanou se solí a vypracujeme (vařečkou nebo robotem) hladké, nelepivé těsto.\nPoté do těsta zapracujeme na kostky nakrájené housky (vhodnější jsou již tvrdší). Těsto necháme kynout na teplém místě.\nVykynuté těsto přendáme na pomoučený vál a rozdělíme je na dva díly. Z každého vytvarujeme bochánek, necháme chvíli kynout (asi 5 minut) a vytvoříme šišky, které necháme kynout na válu.\nVe velkém (širokém) hrnci uvedeme do varu vodu, osolíme a vložíme nakynuté šišky knedlíků. Vaříme pod pokličkou 8 minut, potom oba knedlíky ve vodě obrátíme a dovaříme dalších 8 minut.\nHotové knedlíky vyjmeme na vál, ihned rozřízneme nití v půli, propícháme vidlicí a necháme odejít páru.\nJeště vlažné knedlíky potřeme shora máslem nebo olejem - knedlíky na povrchu neokorají a nelepí se k sobě při napařování.');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `druh`
--
ALTER TABLE `druh`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `potravina`
--
ALTER TABLE `potravina`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `potraviny_v_receptu`
--
ALTER TABLE `potraviny_v_receptu`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `recept`
--
ALTER TABLE `recept`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `druh`
--
ALTER TABLE `druh`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `potravina`
--
ALTER TABLE `potravina`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pro tabulku `potraviny_v_receptu`
--
ALTER TABLE `potraviny_v_receptu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pro tabulku `recept`
--
ALTER TABLE `recept`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
