-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `ID` int(10) UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gen_address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('FOUND','PARSED','ERROR','ACTIVATED','PAYED') COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `address` (`address`),
  ADD UNIQUE KEY `pay_address` (`pay_address`),
  ADD UNIQUE KEY `gen_address` (`gen_address`),
  ADD KEY `status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;