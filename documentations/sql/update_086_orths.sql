--
-- Struktura tabeli dla tabeli `orths`
--

CREATE TABLE `orths` (
  `orth_id` bigint(20) NOT NULL,
  `orth` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4_bin;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `orths`
--
ALTER TABLE `orths`
  ADD PRIMARY KEY (`orth_id`),
  ADD KEY `orth` (`orth`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `orths`
--
ALTER TABLE `orths`
  MODIFY `orth_id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orths` ADD UNIQUE(`orth`);

ALTER TABLE `tokens` ADD `orth_id` BIGINT NULL AFTER `eos`;

