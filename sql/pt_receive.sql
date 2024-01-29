-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-01-29 09:32:12
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
-- 資料表結構 `pt_receive`
--

CREATE TABLE `pt_receive` (
  `id` int(10) UNSIGNED NOT NULL,
  `emp_id` varchar(10) NOT NULL COMMENT '申請人工號',
  `cname` varchar(20) NOT NULL COMMENT '申請人姓名',
  `fab_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬Fab',
  `ppty` int(10) UNSIGNED NOT NULL COMMENT '需求類別：1一般、3緊急',
  `receive_remark` varchar(255) NOT NULL COMMENT '用途說明',
  `item` longtext NOT NULL COMMENT '需求清單',
  `idty` int(10) UNSIGNED NOT NULL COMMENT 'status：表單狀態 0完成/1待收/2退貨/3取消',
  `app_date` datetime NOT NULL COMMENT '領用時間',
  `created_at` datetime NOT NULL COMMENT '開單日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_cname` varchar(10) NOT NULL COMMENT '更新人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pt_receive`
--

INSERT INTO `pt_receive` (`id`, `emp_id`, `cname`, `fab_id`, `ppty`, `receive_remark`, `item`, `idty`, `app_date`, `created_at`, `updated_at`, `updated_cname`) VALUES
(1, '10008048', '陳建良', 0, 1, '-1', '{\"PT-901-LIS,22\":{\"need\":\",2023-12-08\",\"pay\":\"1\"}}', 1, '2024-01-29 11:04:00', '2024-01-29 11:04:54', '2024-01-29 11:04:54', '陳建良'),
(2, '10008048', '陳建良', 0, 1, '-1-2-3-4', '{\"PT-901-LIS,18\":{\"need\":\",2024-12-08\",\"pay\":\"1\"},\"PT-901-LIS,22\":{\"need\":\",2023-12-08\",\"pay\":\"1\"}}', 1, '2024-01-29 11:04:00', '2024-01-29 11:05:17', '2024-01-29 15:44:46', '陳建良');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `pt_receive`
--
ALTER TABLE `pt_receive`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_receive`
--
ALTER TABLE `pt_receive`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
