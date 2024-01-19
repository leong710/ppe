-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-01-19 04:26:46
-- 伺服器版本： 10.4.24-MariaDB
-- PHP 版本： 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `ppe`
--

-- --------------------------------------------------------

--
-- 資料表結構 `_formplan`
--

CREATE TABLE `_formplan` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `_type` varchar(20) NOT NULL COMMENT '表單類別',
  `remark` varchar(255) NOT NULL COMMENT '說明',
  `flag` varchar(3) NOT NULL COMMENT '計劃開關',
  `start_time` datetime NOT NULL COMMENT '起始時間',
  `end_time` datetime NOT NULL COMMENT '結束時間',
  `_inplan` varchar(10) NOT NULL COMMENT '啟動的起始值',
  `created_at` datetime NOT NULL COMMENT '建檔時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(10) NOT NULL COMMENT '更新人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_formplan`
--

INSERT INTO `_formplan` (`id`, `_type`, `remark`, `flag`, `start_time`, `end_time`, `_inplan`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'issue', '上半年請購時間', 'Off', '2024-01-01 08:00:00', '2024-01-31 17:00:00', 'On', '2024-01-18 14:48:33', '2024-01-19 08:14:46', '陳建良'),
(2, 'issue', '下半年請購時間', 'Off', '2024-01-15 08:00:00', '2024-03-01 17:00:00', 'Off', '2024-01-18 15:16:43', '2024-01-19 08:24:20', '陳建良'),
(4, 'trade', '下半年請購時間', 'Off', '2024-07-01 20:00:00', '2024-07-31 17:00:00', 'On', '2024-01-18 15:16:43', '2024-01-18 15:48:27', '陳建良');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `_formplan`
--
ALTER TABLE `_formplan`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formplan`
--
ALTER TABLE `_formplan`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
