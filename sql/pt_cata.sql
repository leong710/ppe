-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-01-25 09:55:01
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
-- 資料表結構 `pt_cata`
--

CREATE TABLE `pt_cata` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `cate_no` varchar(10) NOT NULL COMMENT '分類代號',
  `SN` varchar(10) NOT NULL COMMENT '編號',
  `pname` varchar(255) NOT NULL COMMENT '品名',
  `PIC` varchar(255) NOT NULL COMMENT '圖片',
  `cata_remark` varchar(255) NOT NULL COMMENT '敘述',
  `OBM` varchar(255) NOT NULL COMMENT '品牌/製造商',
  `model` varchar(20) NOT NULL COMMENT '型號',
  `size` varchar(20) NOT NULL COMMENT '尺寸範圍',
  `unit` varchar(5) NOT NULL COMMENT '單位',
  `SPEC` text NOT NULL COMMENT '規格',
  `part_no` varchar(20) NOT NULL COMMENT '料號',
  `scomp_no` longtext NOT NULL COMMENT '供應商',
  `buy_a` text NOT NULL COMMENT '安量倍數A',
  `buy_b` text NOT NULL COMMENT '安量倍數B',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `updated_at` datetime NOT NULL COMMENT '更新日期時間',
  `created_at` datetime NOT NULL COMMENT '建檔日期時間',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pt_cata`
--

INSERT INTO `pt_cata` (`id`, `cate_no`, `SN`, `pname`, `PIC`, `cata_remark`, `OBM`, `model`, `size`, `unit`, `SPEC`, `part_no`, `scomp_no`, `buy_a`, `buy_b`, `flag`, `updated_at`, `created_at`, `updated_user`) VALUES
(1, 'J', 'P-901-LIS', '敵腐靈_眼杯_50ml', '6f014ca0d7dc5a2696b52340806b0936.jpg', '敵腐靈_LIS 50ml_眼杯', 'PREVOR', 'LIS', '50', 'ml', 'LIS 50ml_眼杯', '', '', '1.1', '1.5', 'On', '2024-01-22 14:18:51', '2024-01-22 13:42:56', '陳建良'),
(2, 'J', 'P-901-MICR', '敵腐靈_噴罐_100ml', 'cde7f5265d7f75f1366bf104dc10f81e.jpg', '敵腐靈_MICRO 100ml_噴罐', 'PREVOR', 'MICRO', '100', 'ml', 'MICRO 100ml_噴罐', '', '', '1.1', '1.5', 'On', '2024-01-22 14:19:01', '2024-01-22 13:46:05', '陳建良'),
(3, 'J', 'P-902-LPMF', '六氟靈_瓶_500ml', '8c9fb0f2314928ad662e5d2d21a9307c.png', '六氟靈_LPMF 500ml_瓶', 'PREVOR', 'LPMF', '500', 'ml', 'LPMF 500ml_瓶', '', '', '1.1', '1.5', 'On', '2024-01-22 14:19:11', '2024-01-22 14:03:51', '陳建良'),
(4, 'J', 'P-902-DAPF', '六氟靈_沖淋器_5L', 'decaf0a5919ef81eb81c9ad9a865ddc2.jpg', '六氟靈_DAPF 5L_沖淋器', 'PREVOR', 'DAPF', '5', 'L', 'DAPF 5L_沖淋器', '', '', '1.1', '1.5', 'On', '2024-01-22 14:19:23', '2024-01-22 14:06:26', '陳建良'),
(5, 'J', 'P-903', '葡萄糖酸鈣軟膏', '684b97a48bd089d60454f9748d0574a0.jpg', '葡萄糖酸鈣軟膏', 'PREVOR', '', '40', 'g', '葡萄糖酸鈣軟膏 gel 40g', '', '', '1.1', '1.5', 'On', '2024-01-22 14:19:33', '2024-01-22 14:09:13', '陳建良');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `pt_cata`
--
ALTER TABLE `pt_cata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `SN` (`SN`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_cata`
--
ALTER TABLE `pt_cata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
