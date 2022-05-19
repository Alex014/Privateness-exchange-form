-- --------------------------------------------------------

CREATE TABLE `tokens` (
  `ID` int(10) UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gen_address` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `error` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('PARSED','CHECKED','ACTIVATED','PAYED','ERROR') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crc32` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `tokens`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `address` (`address`),
  ADD UNIQUE KEY `gen_address` (`gen_address`),
  ADD KEY `status` (`status`),
  ADD KEY `hours` (`hours`),
  ADD KEY `crc32` (`crc32`),
  ADD KEY `pay_address` (`pay_address`);


ALTER TABLE `tokens`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;