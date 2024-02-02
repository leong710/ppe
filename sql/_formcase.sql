-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-02-02 09:43:02
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
-- 資料表結構 `_formcase`
--

CREATE TABLE `_formcase` (
  `id` int(10) UNSIGNED NOT NULL,
  `_type` varchar(20) NOT NULL COMMENT '表單名稱',
  `title` varchar(255) NOT NULL COMMENT '分類註解',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_formcase`
--

INSERT INTO `_formcase` (`id`, `_type`, `title`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'issue', '1.issue請購需求', 'On', '2024-02-01 03:24:12', '2024-02-01 03:24:12', '陳建良'),
(2, 'trade', '2.trade出入作業', 'On', '2024-02-01 11:02:18', '2024-02-01 11:02:18', '陳建良'),
(3, 'receive', '3.receive領用', 'On', '2024-02-01 11:03:33', '2024-02-01 11:03:33', '陳建良'),
(4, 'stock', 'PPE庫存點檢表', 'On', '2024-02-01 11:09:56', '2024-02-01 11:09:56', '陳建良'),
(5, 'ptstock', '除汙劑庫存點檢表', 'On', '2024-02-01 11:10:11', '2024-02-01 11:10:11', '陳建良');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `_formcase`
--
ALTER TABLE `_formcase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cate_no` (`_type`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formcase`
--
ALTER TABLE `_formcase`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
