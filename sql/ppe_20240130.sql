-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-01-30 07:59:30
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
-- 資料表結構 `autolog`
--

CREATE TABLE `autolog` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `thisDay` varchar(10) NOT NULL COMMENT 'Log日期',
  `sys` varchar(50) NOT NULL COMMENT '系統名稱',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '記錄事項',
  `t_stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '記錄時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `autolog`
--

INSERT INTO `autolog` (`id`, `thisDay`, `sys`, `logs`, `t_stamp`) VALUES
(1, '2024/01/12', 'ppe/receive', '{\"thisDay\":\"2024\\/01\\/12\",\"autoLogs\":[{\"emp_id\":\"10007825\",\"cname\":\"\\u8b1d\\u5fb7\\u826f\",\"waiting\":2,\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u8b1d\\u5fb7\\u826f) \\u60a8\\u6709 2 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:30\",\"mapp_res\":\"OK\"},{\"emp_id\":\"10010721\",\"cname\":\"\\u9673\\u98db\\u826f\",\"waiting\":3,\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u9673\\u98db\\u826f) \\u60a8\\u6709 3 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:30\",\"mapp_res\":\"OK\"},{\"emp_id\":\"11053914\",\"cname\":\"\\u937e\\u4f73\\u742a\",\"waiting\":1,\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u937e\\u4f73\\u742a) \\u60a8\\u6709 1 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:30\",\"mapp_res\":\"OK\"},{\"emp_id\":\"10007825\",\"cname\":\"\\u8b1d\\u5fb7\\u826f\",\"issue_waiting\":\"0\",\"receive_waiting\":\"2\",\"waiting\":\"2\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u8b1d\\u5fb7\\u826f) \\u60a8\\u5171\\u6709 2 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u9818\\u75282\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"13:27\",\"mapp_res\":\"OK\"},{\"emp_id\":\"10010721\",\"cname\":\"\\u9673\\u98db\\u826f\",\"issue_waiting\":\"0\",\"receive_waiting\":\"3\",\"waiting\":\"3\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u9673\\u98db\\u826f) \\u60a8\\u5171\\u6709 3 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u9818\\u75283\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"13:27\",\"mapp_res\":\"OK\"},{\"emp_id\":\"10119798\",\"cname\":\"\\u6c88\\u65fb\\u9821\",\"issue_waiting\":\"1\",\"receive_waiting\":\"0\",\"waiting\":\"1\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u6c88\\u65fb\\u9821) \\u60a8\\u5171\\u6709 1 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u8acb\\u8cfc1\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"13:27\",\"mapp_res\":\"OK\"},{\"emp_id\":\"11053914\",\"cname\":\"\\u937e\\u4f73\\u742a\",\"issue_waiting\":\"1\",\"receive_waiting\":\"1\",\"waiting\":\"2\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u937e\\u4f73\\u742a) \\u60a8\\u5171\\u6709 2 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u8acb\\u8cfc1\\u4ef6\\u3001\\u9818\\u75281\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"13:27\",\"mapp_res\":\"OK\"}]}', '2024-01-12 05:28:21'),
(2, '2024/01/16', 'ppe', '{\"thisDay\":\"2024\\/01\\/16\",\"autoLogs\":[{\"emp_id\":\"10007825\",\"cname\":\"\\u8b1d\\u5fb7\\u826f\",\"issue_waiting\":\"0\",\"receive_waiting\":\"2\",\"waiting\":\"2\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u8b1d\\u5fb7\\u826f) \\u60a8\\u5171\\u6709 2 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u9818\\u75282\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:49\",\"mapp_res\":\"OK\"},{\"emp_id\":\"10010721\",\"cname\":\"\\u9673\\u98db\\u826f\",\"issue_waiting\":\"0\",\"receive_waiting\":\"3\",\"waiting\":\"3\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u9673\\u98db\\u826f) \\u60a8\\u5171\\u6709 3 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u9818\\u75283\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:49\",\"mapp_res\":\"OK\"},{\"emp_id\":\"11053914\",\"cname\":\"\\u937e\\u4f73\\u742a\",\"issue_waiting\":\"1\",\"receive_waiting\":\"1\",\"waiting\":\"2\",\"mg_msg\":\"** \\u6e2c\\u8a66 ~ \\u6e2c\\u8a66 **\\n\\u3010\\u74b0\\u5b89PPE\\u7cfb\\u7d71\\u3011\\u5f85\\u60a8\\u7c3d\\u6838\\u6587\\u4ef6\\u63d0\\u9192\\n(\\u937e\\u4f73\\u742a) \\u60a8\\u5171\\u6709 2 \\u4ef6\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\u5c1a\\u672a\\u8655\\u7406(\\u8acb\\u8cfc1\\u4ef6\\u3001\\u9818\\u75281\\u4ef6)\\uff0c\\u5982\\u5df2\\u7c3d\\u6838\\u5b8c\\u7562\\uff0c\\u8acb\\u5ffd\\u7565\\u6b64\\u8a0a\\u606f\\uff01\\n** \\u8acb\\u81f3\\u4ee5\\u4e0b\\u9023\\u7d50\\u67e5\\u770b\\u5f85\\u7c3d\\u6838\\u6587\\u4ef6\\uff1a\\nhttp:\\/\\/tw059332n.cminl.oa\\/ppe\\/receive\\/\\n\\u6eab\\u99a8\\u63d0\\u793a\\uff1a\\n1.\\u767b\\u9304\\u904e\\u7a0b\\u4e2d\\u5982\\u51fa\\u73fe\\u63d0\\u793a\\u8f38\\u5165\\u5e33\\u865f\\u5bc6\\u78bc\\uff0c\\u8acb\\u4ee5cminl\\\\NT\\u5e33\\u865f\\u683c\\u5f0f\\n<\\u6b64\\u8a0a\\u606f\\u70ba\\u7cfb\\u7d71\\u81ea\\u52d5\\u767c\\u51fa\\uff0c\\u8acb\\u52ff\\u56de\\u8986>\",\"thisTime\":\"11:49\",\"mapp_res\":\"OK\"}]}', '2024-01-16 03:49:00');

-- --------------------------------------------------------

--
-- 資料表結構 `checked_log`
--

CREATE TABLE `checked_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `fab_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬fab',
  `stocks_log` longtext NOT NULL COMMENT '儲存量紀錄',
  `emp_id` int(10) UNSIGNED NOT NULL COMMENT '創建者emp_id',
  `checked_remark` varchar(255) DEFAULT NULL COMMENT '備註說明',
  `checked_year` year(4) NOT NULL COMMENT '檢點年份',
  `half` text NOT NULL COMMENT '半年度',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '中文姓名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `checked_log`
--

INSERT INTO `checked_log` (`id`, `fab_id`, `stocks_log`, `emp_id`, `checked_remark`, `checked_year`, `half`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 2, '{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"C\",\"cate_title\":\"\\u982d\\u90e8\",\"cata_SN\":\"P-002-B\",\"pname\":\"\\u5de5\\u7a0b\\u5b89\\u5168\\u5e3d_\\u85cd\",\"stock_id\":49,\"size\":\"\\u85cd\",\"standard_lv\":10,\"amount\":10,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"C\",\"cate_title\":\"\\u982d\\u90e8\",\"cata_SN\":\"P-002-R\",\"pname\":\"\\u5de5\\u7a0b\\u5b89\\u5168\\u5e3d_\\u7d05\",\"stock_id\":48,\"size\":\"\\u7d05\",\"standard_lv\":10,\"amount\":10,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"C\",\"cate_title\":\"\\u982d\\u90e8\",\"cata_SN\":\"P-002-W\",\"pname\":\"\\u5de5\\u7a0b\\u5b89\\u5168\\u5e3d_\\u767d\",\"stock_id\":47,\"size\":\"\\u767d\",\"standard_lv\":10,\"amount\":10,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"C\",\"cate_title\":\"\\u982d\\u90e8\",\"cata_SN\":\"P-002-Y\",\"pname\":\"\\u5de5\\u7a0b\\u5b89\\u5168\\u5e3d_\\u9ec3\",\"stock_id\":50,\"size\":\"\\u9ec3\",\"standard_lv\":10,\"amount\":10,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"C\",\"cate_title\":\"\\u982d\\u90e8\",\"cata_SN\":\"P-004-W\",\"pname\":\"\\u7121\\u5875\\u5ba4\\u8f15\\u4fbf\\u5e3d_\\u767d\",\"stock_id\":51,\"size\":\"\\u767d\",\"standard_lv\":10,\"amount\":10,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"E\",\"cate_title\":\"\\u9762\\u90e8\\u773c\\u775b\",\"cata_SN\":\"P-010\",\"pname\":\"\\u9632UV\\u773c\\u93e1\",\"stock_id\":61,\"size\":\"\",\"standard_lv\":10,\"amount\":1,\"stock_remark\":\" * \\u5165\\u5e33 \\uff1a + 1\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 10:06:11\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-014\",\"pname\":\"\\u5e73\\u9762\\u5f0f\\u6d3b\\u6027\\u78b3\\u53e3\\u7f69\",\"stock_id\":6,\"size\":\"\",\"standard_lv\":1000,\"amount\":1662,\"stock_remark\":\" *PO20240109-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a3 *PO123456788\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a999 *PO20240105-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a100 *PO20240105-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5 *PO20240104-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2024-01-12 14:40:30\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-015-AG\",\"pname\":\"N95\\u53e3\\u7f69_AG(\\u9178\\u6027)\",\"stock_id\":15,\"size\":\"\",\"standard_lv\":100,\"amount\":975,\"stock_remark\":\" *PO20240105-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5 *PO20240104-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2024-01-12 14:40:30\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-015-D\",\"pname\":\"N95\\u53e3\\u7f69_\\u9632\\u5875\",\"stock_id\":13,\"size\":\"\",\"standard_lv\":100,\"amount\":1005,\"stock_remark\":\" *PO20240105-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2024-01-05 09:26:37\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-015-OV\",\"pname\":\"N95\\u53e3\\u7f69_OV(\\u6709\\u6a5f)\",\"stock_id\":14,\"size\":\"\",\"standard_lv\":100,\"amount\":1000,\"stock_remark\":\" *PO20240105-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5 *PO20240104-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2024-01-05 09:26:37\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-016-L\",\"pname\":\"\\u534a\\u9762\\u5f0f\\u9632\\u8b77\\u9762\\u5177\",\"stock_id\":36,\"size\":\"L\",\"standard_lv\":10,\"amount\":45,\"stock_remark\":\"\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 08:41:36\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-016-M\",\"pname\":\"\\u534a\\u9762\\u5f0f\\u9632\\u8b77\\u9762\\u5177\",\"stock_id\":35,\"size\":\"M\",\"standard_lv\":10,\"amount\":45,\"stock_remark\":\"\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 08:41:36\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-016-S\",\"pname\":\"\\u534a\\u9762\\u5f0f\\u9632\\u8b77\\u9762\\u5177\",\"stock_id\":34,\"size\":\"S\",\"standard_lv\":10,\"amount\":45,\"stock_remark\":\"\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 08:41:36\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-017\",\"pname\":\"\\u5168\\u9762\\u5f0f\\u9632\\u8b77\\u9762\\u5177\",\"stock_id\":58,\"size\":\"\",\"standard_lv\":10,\"amount\":5,\"stock_remark\":\"\",\"lot_num\":\"0000-00-00\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 08:41:36\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"A\",\"cate_title\":\"\\u547c\\u5438\",\"cata_SN\":\"P-025\",\"pname\":\"\\u6b63\\u58d3\\u96fb\\u52d5\\u904e\\u6ffe\\u5f0f\\u547c\\u5438\\u5668\",\"stock_id\":63,\"size\":\"\",\"standard_lv\":5,\"amount\":5,\"stock_remark\":\" *PO20240104-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-04 17:02:11\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"G\",\"cate_title\":\"\\u807d\\u529b\",\"cata_SN\":\"P-027\",\"pname\":\"\\u9577\\u6548\\u6027\\u8033\\u585e\",\"stock_id\":66,\"size\":\"\",\"standard_lv\":20,\"amount\":9,\"stock_remark\":\" *PO20240109-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10* \\u767c\\u653e\\u6b20\\u984d\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-09 17:00:53\",\"updated_user\":\"\\u6c88\\u65fb\\u9821\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"G\",\"cate_title\":\"\\u807d\\u529b\",\"cata_SN\":\"P-028\",\"pname\":\"\\u982d\\u639b\\u5f0f\\u8033\\u7f69\",\"stock_id\":64,\"size\":\"\",\"standard_lv\":5,\"amount\":4,\"stock_remark\":\" *PO20240109-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5* \\u767c\\u653e\\u6b20\\u984d\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-09 17:00:53\",\"updated_user\":\"\\u6c88\\u65fb\\u9821\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"D\",\"cate_title\":\"\\u624b\\u90e8\",\"cata_SN\":\"P-040\",\"pname\":\"\\u4f4e\\u6eab\\u4f5c\\u696d\\u624b\\u5957\",\"stock_id\":62,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":10,\"amount\":1,\"stock_remark\":\" * \\u5165\\u5e33 \\uff1a + 1\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 10:08:04\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"I\",\"cate_title\":\"\\u914d\\u4ef6\\/\\u8017\\u6750\",\"cata_SN\":\"P-041\",\"pname\":\"4H_\\u9632\\u5316\\u5b78\\u54c1\\u624b\\u5957\\u5167\\u896f\",\"stock_id\":37,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":10,\"amount\":200,\"stock_remark\":\"\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-27 16:03:27\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"D\",\"cate_title\":\"\\u624b\\u90e8\",\"cata_SN\":\"P-044\",\"pname\":\"\\u9632\\u9178\\u9e7c\\u624b\\u5957\",\"stock_id\":60,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":10,\"amount\":1,\"stock_remark\":\" * \\u5165\\u5e33 \\uff1a + 1\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 09:23:55\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"D\",\"cate_title\":\"\\u624b\\u90e8\",\"cata_SN\":\"P-045\",\"pname\":\"\\u9632\\u5316\\u5b78\\u624b\\u5957\",\"stock_id\":59,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":10,\"amount\":1,\"stock_remark\":\" * \\u5165\\u5e33 \\uff1a + 1\",\"lot_num\":\"9999-12-31\",\"po_no\":\"\",\"updated_at\":\"2023-12-28 09:06:02\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-050\",\"pname\":\"\\u9632\\u9178\\u9e7c\\u570d\\u88d9\",\"stock_id\":38,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":5,\"amount\":7,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-051\",\"pname\":\"\\u7f8e\\u5f0f\\u9632\\u9178\\u9e7c\\u9577\\u888d\",\"stock_id\":39,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":5,\"amount\":7,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-052\",\"pname\":\"C\\u7d1a_\\u9632\\u5316\\u5b78\\u54c1\\u5674\\u6ffa\\u9577\\u888d \",\"stock_id\":40,\"size\":\"\\u55ae\\u4e00\\n\\u5c3a\\u5bf8\",\"standard_lv\":5,\"amount\":7,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-053-2XL\",\"pname\":\"C\\u7d1a\\u9632\\u8b77\\u8863_Tychem 6000\",\"stock_id\":42,\"size\":\"2XL\",\"standard_lv\":10,\"amount\":12,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-053-XL\",\"pname\":\"C\\u7d1a\\u9632\\u8b77\\u8863_Tychem 6000\",\"stock_id\":41,\"size\":\"XL\",\"standard_lv\":10,\"amount\":12,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-054-3XL\",\"pname\":\"\\u9023\\u8eab\\u5f0fC\\u7d1a\\u9632\\u8b77\\u8863\",\"stock_id\":44,\"size\":\"3XL\",\"standard_lv\":10,\"amount\":12,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-054-XL\",\"pname\":\"\\u9023\\u8eab\\u5f0fC\\u7d1a\\u9632\\u8b77\\u8863\",\"stock_id\":43,\"size\":\"XL\",\"standard_lv\":10,\"amount\":12,\"stock_remark\":\" *PO20231227-2\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a2 *PO20231227-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:29:14\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-067-A\",\"pname\":\"\\u5168\\u8eab\\u5f0f\\u5b89\\u5168\\u5e36\",\"stock_id\":45,\"size\":\"\",\"standard_lv\":5,\"amount\":15,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-09 15:49:18\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-067-B\",\"pname\":\"\\u5168\\u8eab\\u5f0f\\u5b89\\u5168\\u5e36_\\u52a0\\u5927\\u578b\",\"stock_id\":46,\"size\":\"3XL\\n\\u4ee5\\u4e0a\",\"standard_lv\":5,\"amount\":5,\"stock_remark\":\" *PO20231227-3\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a5\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2023-12-27 16:34:21\",\"updated_user\":\"\\u9673\\u5efa\\u826f\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"G\",\"cate_title\":\"\\u807d\\u529b\",\"cata_SN\":\"P-072\",\"pname\":\"\\u8f15\\u4fbf\\u6027\\u8033\\u585e\",\"stock_id\":65,\"size\":\"\",\"standard_lv\":20,\"amount\":9,\"stock_remark\":\" *PO20240109-1\\u8acb\\u8cfc\\u5165\\u5e33\\uff1a10* \\u767c\\u653e\\u6b20\\u984d\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-09 17:00:53\",\"updated_user\":\"\\u6c88\\u65fb\\u9821\"}_,{\"local_id\":2,\"local_title\":\"\\u5de5\\u5b89\\u5668\\u6750\\u5ba4\",\"fab_id\":2,\"fab_title\":\"FAB1\\u68df\",\"cate_no\":\"B\",\"cate_title\":\"\\u8eab\\u9ad4\",\"cata_SN\":\"P-073-XL\",\"pname\":\"D\\u7d1a\\u9632\\u8b77\\u8863\",\"stock_id\":67,\"size\":\"XL\",\"standard_lv\":10,\"amount\":-2,\"stock_remark\":\"* \\u767c\\u653e\\u6b20\\u984d\",\"lot_num\":\"9999-12-31\",\"po_no\":null,\"updated_at\":\"2024-01-11 15:32:22\",\"updated_user\":\"\\u6c88\\u65fb\\u9821\"}', 10008048, '', '2024', 'H1', '2024-01-16 14:40:25', '2024-01-16 14:40:25', '陳建良');

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

-- --------------------------------------------------------

--
-- 資料表結構 `pt_local`
--

CREATE TABLE `pt_local` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `fab_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬Fab',
  `local_title` varchar(50) NOT NULL COMMENT '子區域名稱',
  `local_remark` varchar(255) NOT NULL COMMENT '子區域註解',
  `low_level` longtext DEFAULT NULL COMMENT '安全水位',
  `flag` varchar(5) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pt_local`
--

INSERT INTO `pt_local` (`id`, `fab_id`, `local_title`, `local_remark`, `low_level`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 5, '四樓 CF T-43柱位', '沖身洗眼器', NULL, 'Off', '2024-01-22 15:35:00', '2024-01-22 15:35:00', '陳建良'),
(2, 5, '四樓 CF B-59柱位', '沖身洗眼器', NULL, 'On', '2024-01-22 15:35:00', '2024-01-22 15:35:00', '陳建良'),
(3, 5, '四樓 CF B-39柱位', '沖身洗眼器', NULL, 'On', '2024-01-22 15:35:00', '2024-01-22 15:56:00', '陳建良'),
(4, 5, '四樓 CF 管制口', 'CF 管制口', NULL, 'On', '2024-01-22 15:36:00', '2024-01-22 15:36:00', '陳建良'),
(5, 5, '一樓 健康中心', '健康中心', '{\"PT-901-LIS\":\"10\",\"PT-901-MIC\":\"10\",\"PT-902-LPM\":\"0\",\"PT-902-DAP\":\"0\",\"PT-903\":\"0\"}', 'On', '2024-01-22 15:37:00', '2024-01-24 11:03:00', '陳建良'),
(6, 5, 'CUB B1F TMAH中繼站桶槽', '回收中繼站', NULL, 'On', '2024-01-22 15:37:00', '2024-01-22 15:37:00', '陳建良'),
(7, 5, '二樓 Array 管制口', 'Array 管制口', NULL, 'On', '2024-01-22 15:38:00', '2024-01-22 15:38:00', '陳建良'),
(8, 5, '一樓 Array M-37柱位', '沖身洗眼器', NULL, 'On', '2024-01-22 15:38:00', '2024-01-22 15:38:00', '陳建良'),
(9, 5, '一樓 Array M-27柱位', '沖身洗眼器', NULL, 'On', '2024-01-22 15:39:00', '2024-01-22 15:39:00', '陳建良'),
(10, 5, 'CUB二樓 廠務辦公室', '廠務辦公室', NULL, 'On', '2024-01-22 15:39:00', '2024-01-22 15:39:00', '陳建良'),
(11, 11, '1F 健康中心', '健康中心', '{\"PT-901-LIS\":\"1\",\"PT-901-MIC\":\"1\",\"PT-902-LPM\":\"1\",\"PT-902-DAP\":\"0\",\"PT-903\":\"0\"}', 'On', '2024-01-23 14:05:00', '2024-01-24 11:10:00', '陳建良'),
(12, 11, '1F 檢測中心', '檢測中心', NULL, 'On', '2024-01-23 14:06:00', '2024-01-23 14:06:00', '陳建良'),
(13, 2, '二樓 Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(14, 2, '廠務Utility三樓 B6柱位之TMAH機台', 'TMAH機台', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(15, 2, '一樓 B13氣化課化學房', '化學房', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(16, 2, '三樓 CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(17, 2, '三樓 CF BB-4柱位之TMAH機台', 'TMAH機台', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(18, 2, '一樓 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(19, 2, 'RD三樓 Z-8柱位', 'RD三樓', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(20, 3, '廢水處理廠', '3F實驗室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(21, 3, '氣化課值班室', '值班室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(22, 3, '四樓 CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(23, 3, '四樓 CF CNVR01 (25%TMAH tank)', 'CF CNVR01', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(24, 3, '四樓 CF CNVR06 (25%TMAH tank)', 'CF CNVR06', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(25, 3, '四樓 CF CNVR07 (25%TMAH tank)', 'CF CNVR07', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(26, 3, '一樓 防災中心', '防災中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(27, 3, '二樓 Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(28, 3, '一樓 無塵室25%TMAH tank E8柱位', 'TMAH tank旁', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(29, 3, '一樓 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(30, 4, 'Array 1F 無塵室 M-29柱 25%TMAH tank旁', 'Array TMAH tank旁', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(31, 4, 'Array 1F 無塵室 H-33柱 25%TMAH tank旁', 'Array TMAH tank旁', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(32, 4, 'Array 2F 管制口 (現場作業備用)', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(33, 4, 'CF 4F 管制口 (現場作業備用)', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(34, 4, 'CF 4F 無塵室 H-3柱 緊急沖身洗眼器旁', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(35, 4, 'CF 4F 無塵室 B-42柱 25%TMAH供液槽房外', 'TMAH供液槽房外', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(36, 4, 'CF 2F 無塵室 sC-24柱 緊急沖身洗眼器旁', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(37, 4, 'CUB B1F 水務課純水槽區', '純水槽區', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(38, 4, 'CUB 1F 氣化課化學房 (25%TMAH)', 'TMAH tank旁', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(39, 4, 'CUB 2F 廠務值班室', '廠務值班室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(40, 4, 'Office 1F 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(41, 6, '三樓 CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(42, 6, 'CUB化學房 (3F)) [For 25%TMAH]', '3F TMAH tank', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(43, 6, '廠務值班室', '廠務值班室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(44, 6, 'CUB化學房 (1F)) [For 25%TMAH]', '1F TMAH tank', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(45, 6, '廠務廢水處理廠', '廢水廠', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(46, 6, '一樓 Array G14.5柱位DVRC', 'DVRC', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(47, 6, '二樓 Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(48, 6, '一樓 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(49, 7, 'CUB 1F 廠務值班室', '廠務值班室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(50, 7, 'CUB 1F A39 酸鹼房', '酸鹼房', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(51, 7, 'CUB L03 D21TMAH防護櫃', 'TMAH防護櫃', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(52, 7, 'CUB L40 C09 鹼液加藥區 (原A07)', '鹼液加藥區', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(53, 7, 'CUB L03 C33柱 TMAH回收系統使用', 'TMAH回收系統', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(54, 7, 'B1樓 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(55, 7, '四樓 CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(56, 7, '二樓 Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(57, 7, 'CUB 1F 防災中心', '防災中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(58, 8, '廠務氣化值班室', '氣化', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(59, 8, '廠務水務值班室', '水務', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(60, 8, '廠務總值班室', '總值班', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(61, 8, '健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(62, 8, 'Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(63, 8, 'Array管制口', 'Array管制口-FAB 內', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(64, 8, '防災中心', '防災中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(65, 8, 'CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(66, 8, 'CF無塵室K05柱位', 'K05柱位', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(67, 8, 'CF無塵室C01柱位', 'C01柱位', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(68, 8, 'CF無塵室H47柱位', 'H47柱位', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(69, 9, '廠務洩漏處理台車、化學房 (G/C，G/Y)', '洩漏處理台車', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(70, 9, '廢水處理廠2F控制室 (水務)', '2F控制室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(71, 9, '一樓 健康中心', '健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(72, 9, '二樓 Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(73, 9, '六樓 CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(74, 9, '八樓 LCD管制口', 'LCD管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(75, 9, 'CUB一樓 防災中心', '防災中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(76, 15, '廠務管道間充身洗眼器', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:36:39', '陳建良'),
(77, 15, '化學房之沖身洗眼器', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:37:47', '陳建良'),
(78, 15, '廢水處理廠控制室', '控制室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:36:56', '陳建良'),
(79, 15, 'Array管制口', 'Array管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:37:07', '陳建良'),
(80, 15, '六樓 CELL & CF管制口', 'CF管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:37:22', '陳建良'),
(81, 15, '一樓 T6整合實驗室', '實驗室', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:37:31', '陳建良'),
(82, 15, '健康中心', 'OFFICE棟', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:36:30', '陳建良'),
(83, 15, '防災中心', 'CUB棟', NULL, 'On', '2024-01-24 10:15:00', '2024-01-26 16:36:16', '陳建良'),
(84, 10, 'B棟2F管制口', '2F管制口', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(85, 10, 'B棟健康中心', 'B棟2F健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(86, 10, 'A棟健康中心 ', 'A棟1F健康中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(87, 10, 'S2柱位之沖身洗眼器', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(88, 10, 'Q3柱位之沖身洗眼器', '沖身洗眼器', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(89, 10, 'AB棟交界_500T廢水廠', '500T廢水廠', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(90, 10, '東側化學房', '鹼房', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(91, 10, 'HF供酸區', '供酸區', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(92, 10, 'HF回收區', 'B棟B1F回收區', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(93, 10, 'HF廢液槽車充填區', '槽車充填區', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(94, 10, '防災中心', '防災中心', NULL, 'On', '2024-01-24 10:15:00', '2024-01-24 10:15:00', '陳建良'),
(95, 15, 'T6', '測試用', NULL, 'Off', '2024-01-26 16:35:11', '2024-01-26 16:35:11', '陳建良');

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

-- --------------------------------------------------------

--
-- 資料表結構 `pt_stock`
--

CREATE TABLE `pt_stock` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `local_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬local',
  `cata_SN` varchar(20) NOT NULL COMMENT '器材編號SN',
  `standard_lv` int(10) DEFAULT NULL COMMENT '安全存量STD_LV',
  `amount` int(10) NOT NULL COMMENT '現況數量',
  `stock_remark` varchar(255) DEFAULT NULL COMMENT '備註說明',
  `pno` varchar(20) DEFAULT NULL COMMENT 'part_no料號',
  `po_no` varchar(20) DEFAULT NULL COMMENT 'PO採購編號',
  `lot_num` date DEFAULT NULL COMMENT '批號/效期',
  `flag` varchar(5) DEFAULT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_cname` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pt_stock`
--

INSERT INTO `pt_stock` (`id`, `local_id`, `cata_SN`, `standard_lv`, `amount`, `stock_remark`, `pno`, `po_no`, `lot_num`, `flag`, `created_at`, `updated_at`, `updated_cname`) VALUES
(1, 1, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(2, 2, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(3, 3, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(4, 4, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(5, 5, 'PT-901-LIS', 0, 1, '', '', '', '2024-01-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(6, 6, 'PT-901-LIS', 2, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-25 15:21:04', '陳建良'),
(7, 7, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(8, 7, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(9, 7, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(10, 8, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(11, 9, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(12, 10, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(13, 11, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(14, 12, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(15, 13, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(16, 13, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(17, 14, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(18, 15, 'PT-901-LIS', 1, 1, '', '', '', '2024-12-08', 'On', '2024-01-23 13:11:00', '2024-01-29 16:06:08', '陳建良'),
(19, 16, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(20, 17, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(21, 18, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(22, 19, 'PT-901-LIS', 1, 1, '', '', '', '2023-12-08', 'On', '2024-01-23 13:11:00', '2024-01-29 16:06:07', '陳建良'),
(23, 20, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(24, 21, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(25, 22, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(26, 23, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(27, 24, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(28, 25, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(29, 26, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(30, 27, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(31, 28, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(32, 29, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(33, 30, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(34, 31, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(35, 32, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(36, 32, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(37, 33, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(38, 34, 'PT-901-LIS', 0, 1, '', '', '', '2024-06-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(39, 35, 'PT-901-LIS', 0, 1, '', '', '', '2024-03-03', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(40, 36, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(41, 37, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(42, 38, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(43, 39, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(44, 40, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(45, 41, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(46, 41, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(47, 42, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(48, 43, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(49, 44, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(50, 45, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(51, 46, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(52, 47, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(53, 47, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(54, 47, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(55, 48, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(56, 49, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(57, 49, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(58, 50, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(59, 51, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(60, 52, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(61, 53, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(62, 54, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(63, 55, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(64, 55, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(65, 56, 'PT-901-LIS', 0, 1, '', '', '', '2025-07-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(66, 56, 'PT-901-LIS', 0, 1, '', '', '', '2024-03-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(67, 57, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(68, 58, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(69, 59, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(70, 60, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(71, 60, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(72, 61, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(73, 62, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(74, 62, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(75, 63, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(76, 63, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(77, 64, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(78, 65, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(79, 65, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(80, 66, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(81, 67, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(82, 68, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(83, 69, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(84, 70, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(85, 71, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(86, 72, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(87, 72, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(88, 72, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-06', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(89, 73, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-06', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(90, 73, 'PT-901-LIS', 0, 1, '', '', '', '2024-03-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(91, 73, 'PT-901-LIS', 0, 1, '', '', '', '2025-05-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(92, 74, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-06', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(93, 74, 'PT-901-LIS', 0, 1, '', '', '', '2023-12-06', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(94, 75, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(95, 77, 'PT-901-LIS', 1, 1, '', '', '', '2024-03-11', 'Off', '2024-01-23 13:11:00', '2024-01-26 16:43:13', '陳建良'),
(96, 78, 'PT-901-LIS', 1, 1, '', '', '', '2024-03-11', 'Off', '2024-01-23 13:11:00', '2024-01-26 16:43:00', '陳建良'),
(98, 79, 'PT-901-LIS', 1, 5, '', '', '', '2025-05-22', 'Off', '2024-01-23 13:11:00', '2024-01-26 16:43:54', '陳建良'),
(99, 80, 'PT-901-LIS', 1, 1, '', '', '', '2024-03-11', 'Off', '2024-01-23 13:11:00', '2024-01-26 16:43:31', '陳建良'),
(100, 80, 'PT-901-LIS', 1, 1, '', '', '', '2025-05-22', 'Off', '2024-01-23 13:11:00', '2024-01-26 16:43:24', '陳建良'),
(101, 85, 'PT-901-LIS', 0, 1, '', '', '', '2024-10-11', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(102, 86, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(103, 87, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(104, 88, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(105, 89, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(106, 90, 'PT-901-LIS', 0, 1, '', '', '', '2025-01-17', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(107, 94, 'PT-901-LIS', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(108, 1, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(109, 2, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(110, 3, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(111, 4, 'PT-901-MICR', 0, 1, '', '', '', '2024-01-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(112, 5, 'PT-901-MICR', 0, 1, '', '', '', '2025-03-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(113, 6, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(114, 7, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(115, 7, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(116, 7, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(117, 8, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(118, 9, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(119, 10, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(120, 11, 'PT-901-MICR', 0, 1, '', '', '', '2025-03-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(121, 12, 'PT-901-MICR', 0, 1, '', '', '', '2024-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(122, 13, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(123, 13, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(124, 14, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(125, 15, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(126, 16, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(127, 17, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(128, 18, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(129, 19, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(130, 20, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(131, 21, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(132, 22, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(133, 23, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(134, 24, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(135, 25, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(136, 26, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(137, 27, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(138, 28, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(139, 29, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(140, 30, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(141, 31, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(142, 32, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(143, 32, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(144, 33, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(145, 34, 'PT-901-MICR', 0, 1, '', '', '', '2025-04-25', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(146, 35, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(147, 36, 'PT-901-MICR', 0, 1, '', '', '', '2024-08-29', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(148, 37, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(149, 38, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(150, 39, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(151, 40, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(152, 41, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(153, 41, 'PT-901-MICR', 0, 1, '', '', '', '2024-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(154, 42, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(155, 43, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(156, 44, 'PT-901-MICR', 0, 1, '', '', '', '2024-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(157, 45, 'PT-901-MICR', 0, 1, '', '', '', '2024-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(158, 46, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(159, 47, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(160, 47, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(161, 47, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(162, 48, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(163, 49, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(164, 49, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(165, 50, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(166, 51, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(167, 52, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(168, 53, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(169, 54, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(170, 55, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(171, 55, 'PT-901-MICR', 0, 1, '', '', '', '2024-08-29', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(172, 56, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(173, 56, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(174, 57, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(175, 58, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(176, 59, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(177, 60, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(178, 60, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(179, 61, 'PT-901-MICR', 0, 1, '', '', '', '2024-01-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(180, 62, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(181, 62, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(182, 63, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(183, 63, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(184, 64, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(185, 65, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(186, 65, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(187, 66, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(188, 67, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(189, 68, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(190, 69, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(191, 70, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(192, 71, 'PT-901-MICR', 0, 1, '', '', '', '2025-03-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(193, 72, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(194, 72, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(195, 72, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-03', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(196, 73, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-03', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(197, 73, 'PT-901-MICR', 0, 1, '', '', '', '2024-12-20', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(198, 73, 'PT-901-MICR', 0, 1, '', '', '', '2025-04-25', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(199, 74, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-03', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(200, 74, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-03', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(201, 75, 'PT-901-MICR', 0, 1, '', '', '', '2024-10-18', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(202, 77, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(203, 78, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(204, 79, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(205, 79, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(206, 80, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(207, 80, 'PT-901-MICR', 0, 1, '', '', '', '2024-03-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(208, 85, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(209, 86, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(210, 87, 'PT-901-MICR', 0, 1, '', '', '', '2024-08-29', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(211, 88, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(212, 89, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(213, 90, 'PT-901-MICR', 0, 1, '', '', '', '2023-12-08', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(214, 94, 'PT-901-MICR', 0, 1, '', '', '', '2025-03-10', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(215, 11, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(216, 12, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(217, 76, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(218, 78, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(219, 78, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(220, 79, 'PT-902-LPMF', 0, 1, '', '', '', '2025-01-12', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(221, 79, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(222, 79, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(223, 81, 'PT-902-LPMF', 0, 1, '', '', '', '2023-11-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(224, 82, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(225, 83, 'PT-902-LPMF', 0, 1, '', '', '', '2025-07-24', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(226, 87, 'PT-902-LPMF', 0, 3, '', '', '', '2024-02-28', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(227, 88, 'PT-902-LPMF', 0, 3, '', '', '', '2024-02-28', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(228, 94, 'PT-902-LPMF', 0, 1, '', '', '', '2024-03-04', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(229, 77, 'PT-902-DAPF', 0, 1, '', '', '', '2025-08-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(230, 91, 'PT-902-DAPF', 0, 1, '', '', '', '2025-08-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(231, 92, 'PT-902-DAPF', 0, 1, '', '', '', '2025-08-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(232, 93, 'PT-902-DAPF', 0, 1, '', '', '', '2025-08-22', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(233, 11, 'PT-903', 0, 3, '', '', '', '2025-05-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(234, 83, 'PT-903', 0, 5, '', '', '', '2025-05-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良'),
(235, 94, 'PT-903', 0, 5, '', '', '', '2025-05-01', NULL, '2024-01-23 13:11:00', '2024-01-23 13:11:00', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_cata`
--

CREATE TABLE `_cata` (
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
-- 傾印資料表的資料 `_cata`
--

INSERT INTO `_cata` (`id`, `cate_no`, `SN`, `pname`, `PIC`, `cata_remark`, `OBM`, `model`, `size`, `unit`, `SPEC`, `part_no`, `scomp_no`, `buy_a`, `buy_b`, `flag`, `updated_at`, `created_at`, `updated_user`) VALUES
(1, 'C', 'P-001-W', '通氣式工程安全帽_白', 'P-001-W.png', '白', '龍達', 'JFC-9034', '白', '頂', '1. 符合CNS1336認證。\r\n2. 顏色：白。\r\n3. 內襯為四點式，帽帶為鬆緊帶式。\r\n4. 報價含內襯與帽帶。\r\n5. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:27:00', '2023-07-05 15:02:00', '陳建良'),
(2, 'C', 'P-002-W', '工程安全帽_白', 'P-002-W.png', '白', '龍達', 'SM-901', '白', '頂', '1. 符合CNS1336認證。\r\n2. 顏色：白。\r\n3. 內襯為四點式，帽帶為鬆緊帶式。\r\n4. 報價含內襯與帽帶。\r\n5. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:00:00', '2023-07-05 15:02:00', '陳建良'),
(3, 'C', 'P-002-R', '工程安全帽_紅', 'P-002-R.png', '紅', '龍達', 'SM-901', '紅', '頂', '1. 符合CNS1336認證。\r\n2. 顏色：紅。\r\n3. 內襯為四點式，帽帶為鬆緊帶式。\r\n4. 報價含內襯與帽帶。\r\n5. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:00:00', '2023-07-05 15:02:00', '陳建良'),
(4, 'C', 'P-002-B', '工程安全帽_藍', 'P-002-B.png', '藍', '龍達', 'SM-901', '藍', '頂', '1. 符合CNS1336認證。\r\n2. 顏色：藍。\r\n3. 內襯為四點式，帽帶為鬆緊帶式。\r\n4. 報價含內襯與帽帶。\r\n5. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'Off', '2023-07-06 16:01:00', '2023-07-05 15:02:00', '陳建良'),
(5, 'C', 'P-002-Y', '工程安全帽_黃', 'P-002-Y.png', '黃', '龍達', 'SM-901', '黃', '頂', '1. 符合CNS1336認證。\r\n2. 顏色：黃。\r\n3. 內襯為四點式，帽帶為鬆緊帶式。\r\n4. 報價含內襯與帽帶。\r\n5. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:01:00', '2023-07-05 15:02:00', '陳建良'),
(6, 'I', 'P-003', '工程安全帽內襯', 'P-003.png', '', '龍達', '', '', '個', '1. 內襯為四點式。\r\n2. 無塵室可使用(不發塵材質)。\r\n3. 需可配合廠內目前使用之安全帽(龍達SM-901)。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:02:00', '2023-07-05 15:02:00', '陳建良'),
(7, 'C', 'P-004-W', '無塵室輕便帽_白', 'P-004-W.png', '白', '龍達', 'SM-903', '白', '頂', '1. 耐衝擊材質製造(符合EN812認證)。\r\n2. 顏色：白。\r\n3. 需有頂部防撞帶。\r\n4. 內襯前後端凸緣需平緩(如照片所示)。\r\n5. 與內襯結合方式為壓扣式(如照片所示)。\r\n6. 帽帶為鬆緊帶式。\r\n7. 報價含內襯與帽帶。\r\n8. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:01:00', '2023-07-05 15:02:00', '陳建良'),
(8, 'C', 'P-004-R', '無塵室輕便帽_紅', 'P-004-R.png', '紅', '龍達', 'SM-903', '紅', '頂', '1. 耐衝擊材質製造(符合EN812認證)。\r\n2. 顏色：紅。\r\n3. 需有頂部防撞帶。\r\n4. 內襯前後端凸緣需平緩(如照片所示)。\r\n5. 與內襯結合方式為壓扣式(如照片所示)。\r\n6. 帽帶為鬆緊帶式。\r\n7. 報價含內襯與帽帶。\r\n8. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-11 13:15:00', '2023-07-05 15:02:00', '陳建良'),
(9, 'C', 'P-004-B', '無塵室輕便帽_藍', 'P-004-B.png', '藍', '龍達', 'SM-903', '藍', '頂', '1. 耐衝擊材質製造(符合EN812認證)。\r\n2. 顏色：藍。\r\n3. 需有頂部防撞帶。\r\n4. 內襯前後端凸緣需平緩(如照片所示)。\r\n5. 與內襯結合方式為壓扣式(如照片所示)。\r\n6. 帽帶為鬆緊帶式。\r\n7. 報價含內襯與帽帶。\r\n8. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:02:00', '2023-07-05 15:02:00', '陳建良'),
(10, 'C', 'P-004-Y', '無塵室輕便帽_黃', 'P-004-Y.png', '黃', '龍達', 'SM-903', '黃', '頂', '1. 耐衝擊材質製造(符合EN812認證)。\r\n2. 顏色：黃。\r\n3. 需有頂部防撞帶。\r\n4. 內襯前後端凸緣需平緩(如照片所示)。\r\n5. 與內襯結合方式為壓扣式(如照片所示)。\r\n6. 帽帶為鬆緊帶式。\r\n7. 報價含內襯與帽帶。\r\n8. 出貨含內襯與帽帶。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:02:00', '2023-07-05 15:02:00', '陳建良'),
(11, 'I', 'P-005', '無塵室輕便帽內襯', 'P-005.png', '', '不指定', '', '', '個', '1. 需有頂部防撞帶。\r\n2. 無塵室可使用(不發塵材質)。\r\n3. 內襯前後端凸緣需平緩(如照片所示)。\r\n4. 與帽殼結合方式為壓扣式(如照片所示)。\r\n5. 需可配合廠內目前使用之輕便帽(龍達SM-903)。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:02:00', '2023-07-05 15:02:00', '陳建良'),
(12, 'I', 'P-006', '安全帽帽帶', 'P-006.png', '', '不指定', '', '', '個', '1. 鬆緊帶式。\r\n2. 無塵室可使用(不發塵材質)。\r\n3. 需可配合廠內目前使用之工程安全帽(龍達SM-901與龍達SM-903)與無塵室輕便帽。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:02:00', '2023-07-05 15:02:00', '陳建良'),
(13, 'I', 'P-007', '防化學品面擋架', 'P-007.png', '', '不指定', '', '', '個', '1. 框架需為非金屬材料。\r\n2. 需可配合廠內目前使用之面檔與安全帽(龍達SM-901)。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:53:00', '2023-07-05 15:02:00', '陳建良'),
(14, 'E', 'P-008', '防護頭盔', 'P-008.png', '', '', '', '', '個', '1. PP材質\r\n2. 符合ANSI Z87認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 16:53:00', '2023-07-05 15:02:00', '陳建良'),
(15, 'I', 'P-009', 'PC安全防護片', 'P-009.png', '', '不指定', '', '', '個', '1. 透明PC面罩\n2. 符合ANSI Z87+認證。\n3. 長：39cm、寬：20cm、厚：0.1cm\n4.需可配合廠內目前使用之頭盔。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(16, 'E', 'P-010', '防UV眼鏡', 'P-010.png', '', 'ACEST', 'CI-31', '', '個', '1. 符合EN/ANSI/CNS認證。\n2. 防UV/IR強光，紫外線波長00-500nm。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(17, 'E', 'P-011', '全罩式安全眼鏡', 'P-011.png', '', '', 'S-60', '', '個', '1. 符合CNS7177認證(或同等級國際規範)。    \n2. 鏡面具防霧功能。\n3. 需含本體與可調式鬆緊帶/鏡腳。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(18, 'E', 'P-012', '兩用式全罩式安全眼鏡', 'P-012.png', '2023年採購把17~18下成同一規格，將合併', 'MTC', '18033', '', '個', '1. 符合ANSI Z87認證(或同等級國際規範)。    \n2. 需含本體、鏡架與可調式鬆緊帶(不發塵)。', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(19, 'E', 'P-013', '一般型安全眼鏡', 'P-013.png', '', '不指定', '', '', '個', '1. PC鏡片，耐基本衝擊、噴濺。\n2. 符合CE/EN/ANSI/CNS認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(20, 'A', 'P-014', '平面式活性碳口罩', 'P-014.png', '', '不指定', '', '', '個', '1. 共計四層(防潮層、活性碳層、過濾層與纖維層)，鼻樑處附鐵絲以供固定。盒裝(50個/盒)。 出廠日期半年內，且包裝需標示有效日期。\r\n2.外包裝需有CNS14756認證標示或提供相關證明。', 'R1203053ZJ50', '04908814,明江貿易', '1.1', '1.5', 'On', '2023-07-21 15:45:00', '2023-07-05 15:02:00', '陳建良'),
(21, 'A', 'P-015-AG', 'N95口罩_AG(酸性)', 'P-015-AG.png', 'AG(酸性)', '3M', '8246', '', '個', '1. 需可防酸性氣體。\r\n2. 95%以上過濾效能，NIOSH認證。\r\n3. 出廠日期一年內，且需標示有效日期。', 'R12030635E60', '', '1.1', '1.5', 'On', '2023-07-21 15:59:00', '2023-07-05 15:02:00', '陳建良'),
(22, 'A', 'P-015-OV', 'N95口罩_OV(有機)', 'P-015-OV.png', 'OV(有機)', '3M', '8247', '', '個', '1. 需可防有機氣體。\r\n2. 95%以上過濾效能，NIOSH認證。\r\n3. 出廠日期一年內，且需標示有效日期。', 'R1203073ZDC0', '', '1.1', '1.5', 'On', '2023-07-21 15:59:00', '2023-07-05 15:02:00', '陳建良'),
(23, 'A', 'P-015-D', 'N95口罩_防塵', 'P-015-D.png', '防塵', '3M', '8210', '', '個', '1. 通過NIOSH 42CFR N95等級。\r\n2. 出廠日期一年內，且需標示有效日期。', 'R12032J3HWZ0', '', '1.1', '1.5', 'On', '2023-07-21 15:59:00', '2023-07-05 15:02:00', '陳建良'),
(24, 'A', 'P-016-S', '半面式防護面具', 'P-016-S.png', '', '3M', '6200', 'S', '個', '1. 雙濾罐式。\n2. KRATON材質，可搭配3M多種濾材使用。\n3. 四點式調整頭戴，採取固定式方式穿戴。\n4. 可重複清洗使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(25, 'A', 'P-016-M', '半面式防護面具', 'P-016-M.png', '', '3M', '6200', 'M', '個', '1. 雙濾罐式。\n2. KRATON材質，可搭配3M多種濾材使用。\n3. 四點式調整頭戴，採取固定式方式穿戴。\n4. 可重複清洗使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(26, 'A', 'P-016-L', '半面式防護面具', 'P-016-L.png', '', '3M', '6200', 'L', '個', '1. 雙濾罐式。\r\n2. KRATON材質，可搭配3M多種濾材使用。\r\n3. 四點式調整頭戴，採取固定式方式穿戴。\r\n4. 可重複清洗使用。', '', '', '1.1', '1.5', 'On', '2023-07-11 13:26:00', '2023-07-05 15:02:00', '陳建良'),
(27, 'A', 'P-017', '全面式防護面具', 'P-017.png', '', '3M', '6800', '', '個', '1. 雙濾罐式。\n2. 矽膠/熱塑性材質，可搭配多種濾材使用。\n3. 各項零配件可個別替換維修\n4. 鏡面符合美國ANSI Z87.1-2010標準，耐高速衝擊(Z87+)\n5.可重複清洗使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(28, 'I', 'P-018-A', '綜合性濾毒罐', 'P-018-A.png', '', '3M', '6003', '', '個', '1. 符合NIOSH認證或同等級。\n2. 可配合3M面具使用。\n3. 2個/組。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(29, 'I', 'P-018-B', '汞蒸氣/氯氣用濾毒罐', 'P-018-B.png', '', '3M', '6009', '', '個', '1. 符合NIOSH認證或同等級。\n2. 可配合3M面具使用。\n3. 2個/組。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(30, 'I', 'P-019-A', 'P100過濾棉片', 'P-019-A.png', '', '3M', '2091', '', '個', '1. 美國NIOSE P100等級，對粉塵過濾效率達99.97%以上。\n2. 可配合3M面具使用。\n3. 2片/組。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(31, 'I', 'P-019-B', 'N95過濾棉片', 'P-019-B.png', '', '3M', '5N11', '', '個', '1. 美國NIOSE N95等級，對粉塵過濾效率達95%以上。\n2. 需可搭配3M 501濾蓋使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(32, 'I', 'P-019-C', '濾棉蓋', 'P-019-C.png', '', '3M', '501', '', '個', 'N95過濾棉片(3M-5N11)專用濾棉蓋。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(33, 'I', 'P-020-A', 'PAPR P3等級濾罐', 'P-020-A.png', '', 'Clean AIR', '500048', '', '個', '1. EN認證P3等級濾罐。\n2. 防一般粉塵。\n3. 需可與本廠PAPR(Clean AIR)搭配使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(34, 'I', 'P-024-B', 'PAPR 綜合型濾毒罐_不含Hg', 'P-024-B.png', '不含Hg', 'Clean AIR', '500168', '', '個', '1. 防有機、無機與酸性氣體、蒸氣與氨氣。\n2. 防有害固態與液態微粒。\n3. 需可與本廠PAPR(Clean AIR)搭配使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(35, 'A', 'P-025', '正壓電動過濾式呼吸器', 'P-025.png', 'PAPR', 'Clean AIR', '510000FCA', '', '台', '1. 全套包含：主機、充電器及電池1個。\r\n2. 送氣?：160 L/min以上。\r\n3. 符合EN 12941及NIOSH標準。\r\n4. 主機本體防水，?需保護套可直接沖洗。\r\n5. 主機可隨?芯阻抗自動調整轉?，保持空氣???變。\r\n6. 電?指示器/?芯阻抗指示器、風速?常/電?過低蜂鳴器警報。', '', '', '1.1', '1.5', 'On', '2023-07-12 12:55:00', '2023-07-05 15:02:00', '陳建良'),
(36, 'I', 'P-025-A', '正壓電動過濾式呼吸器電池', 'P-025-A.png', '', 'Clean AIR', '510010', '', '個', '1. 需可與本廠PAPR(Clean AIR_PAPR_510000FCA) 搭配使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(37, 'I', 'P-025-B', 'PAPR_耐久型頭套', 'P-025-B.png', '', 'Clean AIR', '720202B', '', '個', '1. 頭套材質為可抗化尼龍具PU覆膜材質。\n2. EN認證，並需有可調式頸帶及傳聲膜片。\n3. 需可與本廠PAPR(Clean AIR_PAPR_510000FCA) 搭配使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(38, 'I', 'P-025-C', 'PAPR_頭套送氣管', 'P-025-C.png', '', 'Clean AIR', '710060', '', '個', '1. EN認證。\n2. 需可與上述頭套/面具搭配使用在本廠PAPR (Clean AIR_PAPR_510000FCA)上。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(39, 'I', 'P-026-A', '供氣式_轉接頭  (3M_ADP-03)', 'P-026-A.png', '3M_ADP-03', '3M', 'ADP-03', '', '個', '1.可相接3M呼吸管與SCOTT 正壓電動過?式呼吸器', '', '', '1.1', '1.5', 'On', '2023-07-06 17:00:00', '2023-07-05 15:02:00', '陳建良'),
(40, 'I', 'P-026-B', '供氣式_高階內襯  (3M_S-950)', 'P-026-B.png', '3M_S-950', '3M', 'S-950', '', '個', '1.內襯與S-605、S-807頭罩搭配使用', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(41, 'I', 'P-026-C', '供氣式_頭罩 (3M_S-605)', 'P-026-C.png', '3M_S-605', '3M', 'S-605', '', '個', '1.頭罩與S-950內襯搭配使用', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(42, 'I', 'P-026-D', '3M_供氣式_耐用型呼吸管', 'P-026-D.png', '', '3M', 'BT-40', '', '條', '1. 可與3M S-950高階內襯及3M Versaflo系列動力式呼吸防護具主機搭配使用。\n2. 合成橡膠材質，高耐用度，適用於高溫或較嚴峻的工作環境。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(43, 'G', 'P-027', '長效性耳塞', 'P-027.png', '', '3M', '1271', '', '組', '1. NRR值24dB。\n2. 帶線(附盒子)。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(44, 'G', 'P-072', '輕便性耳塞', 'P-072.png', '', '3M', '1100', '', '個', '1. NRR值29dB。\n2. 橘色子彈型。', '', '', '1.1', '1.5', 'On', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(45, 'G', 'P-028', '頭掛式耳罩', 'P-028.png', '', '3M', 'H10A', '', '個', '1. 適用在噪音值低於105分貝的場所。\n2. NRR值30dB\n3. 可更換耳罩內吸音材質。\n4. 不鏽鋼材質頭帶\n5. 配戴於頸後位置，方便與安全帽同時使用。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(46, 'G', 'P-029', '頸掛式防音耳罩', 'P-029.png', '', '3M', 'AO-H10B', '', '個', '1. 採彈性鋼架連接，可搭配任何安全帽。\n2. NRR值29dB。\n3.適用於噪音值低於105分貝的場所 \n4.符合ANZI 或CE標準認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(47, 'G', 'P-030', '加掛於安全帽之耳罩', 'P-030.png', '', '不指定', '', '', '個', '1. 需可與本廠安全帽(龍達SM-901)搭配使用，並附連結套件。\n2. NRR > 25 dB 或 SNR > 28 dB以上。\n3. 符合ANZI或CE標準認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(48, 'D', 'P-031-S', '薄型防切割手套', 'P-031-S.png', '', '不指定', '', 'S', '雙', '1. 符合CE認證，4344等級以上(認證須標示於手套上)\n2. 材質：單面NBR塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(49, 'D', 'P-031-M', '薄型防切割手套', 'P-031-M.png', '', '不指定', '', 'M', '雙', '1. 符合CE認證，4344等級以上(認證須標示於手套上)\n2. 材質：單面NBR塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(50, 'D', 'P-031-L', '薄型防切割手套', 'P-031-L.png', '', '不指定', '', 'L', '雙', '1. 符合CE認證，4344等級以上(認證須標示於手套上)\n2. 材質：單面NBR塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(51, 'D', 'P-031-XL', '薄型防切割手套', 'P-031-XL.png', '', '不指定', '', 'XL', '雙', '1. 符合CE認證，4344等級以上(認證須標示於手套上)\n2. 材質：單面NBR塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(52, 'D', 'P-032-S', '一般防切割手套', 'P-032-S.png', '', '不指定', '', 'S', '雙', '檢測通過EN388-4544之物理性能標準', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(53, 'D', 'P-032-M', '一般防切割手套', 'P-032-M.png', '', '不指定', '', 'M', '雙', '檢測通過EN388-4544之物理性能標準', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(54, 'D', 'P-032-L', '一般防切割手套', 'P-032-L.png', '', '不指定', '', 'L', '雙', '檢測通過EN388-4544之物理性能標準', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(55, 'D', 'P-032-XL', '一般防切割手套', 'P-032-XL.png', '', '不指定', '', 'XL', '雙', '檢測通過EN388-4544之物理性能標準', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(56, 'D', 'P-032-2XL', '一般防切割手套', 'P-032-2XL.png', '', '不指定', '', '2XL', '雙', '檢測通過EN388-4544之物理性能標準', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(57, 'D', 'P-033', '防切割手套 ', 'P-033.png', '', '百潤', '', '單一\n尺寸', '雙', '符合CE認證，EN388-4344', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(58, 'D', 'P-34-8', '防切割手套 (MAPA-586)', 'P-034-8.png', 'MAPA-586', 'MAPA', '586', '8', '雙', '1. 尺寸：長24-30cm。\n2. 符合CE認證(4X43D)\n3.材質：HDPE，單面PU塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(59, 'D', 'P-34-9', '防切割手套 (MAPA-586)', 'P-034-9.png', 'MAPA-586', 'MAPA', '586', '9', '雙', '1. 尺寸：長24-30cm。\n2. 符合CE認證(4X43D)\n3.材質：HDPE，單面PU塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(60, 'D', 'P-34-10', '防切割手套 (MAPA-586)', 'P-034-10.png', 'MAPA-586', 'MAPA', '586', '10', '雙', '1. 尺寸：長24-30cm。\n2. 符合CE認證(4X43D)\n3.材質：HDPE，單面PU塗層', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(61, 'D', 'P-35', '防穿刺手套\n (HexArmor SharpsMaster II 9014 )', 'P-035.png', 'HexArmor SharpsMaster II 9014', 'HexArmor', 'GHA-SIX-9014', '單一\n尺寸', '雙', '1. SuperFabric? brand材質。\n2. 提供最高防針刺防護25號針(ASTM 1342-05測試)。\n3. 符合CE與EN388認證：4544之物理性能標準。', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(62, 'D', 'P-036', '防切割袖套', 'P-036.png', '', 'MAPA', '', '單一\n尺寸', '雙', '1.材質：HDPE\n2.尺寸：長60cm\n3.CE認證(4X4XD)', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(63, 'D', 'P-037', '皮手套', 'P-037.png', '', '不指定', '', '單一\n尺寸', '雙', '1. 使用豬面皮製成。\n2. 長度35cm，厚度0.2 mm以上，可耐切割穿刺。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(64, 'D', 'P-038', '防熱手套_200度', 'P-038.png', '200度', 'Dailove', 'DL-200', '單一\n尺寸', '雙', '1. 外層矽膠材質(SILICONE)、內裡化學纖維材質\n2. 尺寸：長27cm、厚2.95mm\n3. 可用於 200℃(約7秒)、 150℃(約15秒)溫度之作業\n4. 具CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(65, 'D', 'P-039', '防熱手套_400度', 'P-039.png', '400度', '不指定', '', '單一\n尺寸', '雙', '1. 手心及姆指部份由杜邦防火材料對位芳香族聚毓酸纖維絲 (KEVLAR)NOR-FAB40O 被覆防輻射熱鋁箔構成，防火布向外，鋁箔向內，厚度1.5 mm。\n2. 手背及袖口亦由防火布料披覆鋁箔製成，鋁箔向外。\n3. 內襯手心及手背由3 mm厚隔熱棉製成。 \n4. 手套長度5指X14吋，手背寬145 mm，袖口寬170 mm。\n5. 以上防火系列產品全部以(KEVLAR)3/3O防火線縫製。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(66, 'D', 'P-040', '低溫作業手套', 'P-040.png', '', 'MAPA', '770', '單一\n尺寸', '雙', '1. 材質：PVC材質。\n2. 尺寸：長30cm。\n3. 耐低溫-30℃。\n4. 具CE認證。適用濃度40％ NaOH、65% 硝酸與25％  氨作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(67, 'I', 'P-041', '4H_防化學品手套內襯', 'P-041.png', '', '不指定', '', '單一\n尺寸', '雙', '1. 材質：PE+EV0H+PE 材質，5層淋膜\n2. 長度：40cm、厚度：0.1mm\n3. 適用多種化學物質之作業場合，使用時間可達480分鐘(具CE認證)\n4. 僅做一般化學手套的內層手套使用', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(68, 'D', 'P-042-8', '酸鹼暨溶劑作業防護手套', 'P-042-8.png', '', 'MAPA', '517', '8', '雙', '1. 長度：36cm、厚度：0.5mm\n2.Trionic(天然乳膠與Neoprene、Nitrile合成)\n3. 具CE認證。適用濃度氫氧化鈉45%、氫氟酸40%、甲醛作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(69, 'D', 'P-042-9', '酸鹼暨溶劑作業防護手套', 'P-042-9.png', '', 'MAPA', '517', '9', '雙', '1. 長度：36cm、厚度：0.5mm\n2.Trionic(天然乳膠與Neoprene、Nitrile合成)\n3. 具CE認證。適用濃度氫氧化鈉45%、氫氟酸40%、甲醛作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(70, 'D', 'P-042-10', '酸鹼暨溶劑作業防護手套', 'P-042-10.png', '', 'MAPA', '517', '10', '雙', '1. 長度：36cm、厚度：0.5mm\n2.Trionic(天然乳膠與Neoprene、Nitrile合成)\n3. 具CE認證。適用濃度氫氧化鈉45%、氫氟酸40%、甲醛作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(71, 'D', 'P-043', '防有機溶劑手套', 'P-043.png', '', 'MAPA', '491', '單一\n尺寸', '雙', '1.長37cm、厚0.38mm。\n2. Nitrile材質製成，手部具壓紋防滑處理，內有絨裡。\n3. 具CE認證。適用濃度甲醇、正庚烷、40％ NaOH、與96％ 硫酸作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(72, 'D', 'P-044', '防酸鹼手套', 'P-044.png', '', 'MAPA', '450', '單一\n尺寸', '雙', '1. 長41cm、厚0.75mm。\n2. Neoprene (氯丁橡膠)，內有絨裡，手部具壓紋防滑處理。\n3. 具CE認證。適用65％ 硝酸、99％ 乙酸、40％ 氫氟酸、與96％ 硫酸作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(73, 'D', 'P-045', '防化學手套', 'P-045.png', '', 'MAPA', '651', '單一\n尺寸', '雙', '1. 長35cm、厚0.5mm。\n2. Butyl(丁基橡膠)\n3. 具CE認證。適用濃度甲醇、丙酮、乙睛、醋酸乙脂、40％ NaOH、與96％ 硫酸作業。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(74, 'D', 'P-046-A', '防強酸鹼手套_36 cm', 'P-046-A.png', '', 'HANAKI', 'GCB-HK-A22', '單一\n尺寸', '雙', '1. 防王水、氫氟酸、鹽酸37%、磷酸75%、氫氧化鈉45%、酪酸50%、防氫硫酸鹽95%、重珞酸鉀、過氧化氫90%、硝酸20%+氫氟酸4%、硝酸70%\n2.美國杜邦Hypalon(有防滑絨內裡)。\n3. 長36 cm', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(75, 'D', 'P-046-B', '防強酸鹼手套_55 cm', 'P-046-B.png', '', 'HANAKI', 'GCB-HK-A55', '單一\n尺寸', '雙', '1. 防王水、氫氟酸、鹽酸37%、磷酸75%、氫氧化鈉45%、酪酸50%、防氫硫酸鹽95%、重珞酸鉀、過氧化氫90%、硝酸20%+氫氟酸4%、硝酸70%\n2.美國杜邦Hypalon(有防滑絨內裡)。\n3. 長55 cm', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(76, 'D', 'P-047', 'PVC手套_加長連袖套型', 'P-047.png', '', '', '', '單一\n尺寸', '雙', '1. 長60 cm，厚0.3 mm。\n2. 材質：手套為塑膠(PVC)。\n3. 適用HCl、HF、NaOH水溶液與2.38％  THAM作業。\n4. 檢附測試報告及5％TMAH不滲透性測試報告。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(77, 'D', 'P-048-S', '酸鹼溶劑用PVC手套', 'P-048-S.png', '', '百潤', 'GSO-ST-01', 'S', '雙', '1. 長30公分，厚0.25 mm。\n2. 塑膠(PVC)材質，指尖加厚。\n3. 適用HCl、HF、NaOH水溶液與2.38％  THAM作業。\n4. 檢附測試報告及5％TMAH不滲透性測試報告。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(78, 'D', 'P-048-M', '酸鹼溶劑用PVC手套', 'P-048-M.png', '', '百潤', 'GSO-ST-01', 'M', '雙', '1. 長30公分，厚0.25 mm。\n2. 塑膠(PVC)材質，指尖加厚。\n3. 適用HCl、HF、NaOH水溶液與2.38％  THAM作業。\n4. 檢附測試報告及5％TMAH不滲透性測試報告。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(79, 'D', 'P-048-L', '酸鹼溶劑用PVC手套', 'P-048-L.png', '', '百潤', 'GSO-ST-01', 'L', '雙', '1. 長30公分，厚0.25 mm。\n2. 塑膠(PVC)材質，指尖加厚。\n3. 適用HCl、HF、NaOH水溶液與2.38％  THAM作業。\n4. 檢附測試報告及5％TMAH不滲透性測試報告。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(80, 'D', 'P-048-XL', '酸鹼溶劑用PVC手套', 'P-048-XL.png', '', '百潤', 'GSO-ST-01', 'XL', '雙', '1. 長30公分，厚0.25 mm。\n2. 塑膠(PVC)材質，指尖加厚。\n3. 適用HCl、HF、NaOH水溶液與2.38％  THAM作業。\n4. 檢附測試報告及5％TMAH不滲透性測試報告。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(81, 'B', 'P-049', '皮革圍裙', 'P-049.png', '', '不指定', '', '單尺寸', '件', '1. 使用豬面皮製成\r\n2. 尺寸：0.6 mm(厚)*93 cm(長)*68 cm(寬)。\r\n3. 可耐切割穿刺。\r\n4.可耐熱60度以上', 'R12041106PG0', '', '1.1', '1.5', 'On', '2023-10-26 10:24:00', '2023-07-05 15:02:00', '陳建良'),
(82, 'B', 'P-050', '防酸鹼圍裙', 'P-050.png', '', '不指定', '', '單一\n尺寸', '件', '1. 長100 CM * 寬 72 CM\n2. PVC材質製成。\n3. 不發塵材質。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(83, 'B', 'P-051', '美式防酸鹼長袍', 'P-051.png', '', '不指定', '', '單一\n尺寸', '件', '1. 防多種酸鹼化學品，耐化性強。(含手袖，袖口鬆緊)\n2. 黃色，長 110 cm。\n3. 背後綁帶式。\n4. PVC材質。\n5. 無塵室可使用(不發塵材質)。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(84, 'B', 'P-052', 'C級_防化學品噴濺長袍 ', 'P-052.png', '', '不指定', '', '單一\n尺寸', '件', '1. 車縫線不得外露，必須加強接合；需採超音波銲接縫合或外貼防水條之不得有滲入風險之防護措施；臉部及肢體部份需具鬆緊帶束口。\n2. 使用Tychem C材質製成，防化學品穿透、滲透必須經過EN368、EN369測試完成。\n3. 布料抗化能力：依據 ASTM F739測試標準，滲透時間可抵抗48% 氫氟酸、93% 硫酸、氫氧化鈉等化學品480分鐘以上。\n4. 長 125cm。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(85, 'B', 'P-053-XL', 'C級防護衣_Tychem 6000', 'P-053-XL.png', '', 'Dupont', 'Thchem 6000', 'XL', '件', '1. 材質：Tychem F (Tyvek與防護膜複合而成)\n2. 車縫處貼合膠條、拉鏈加蓋。\n3. 布料抗化能力：滲透時間可抵抗正己烷化學品480分鐘以上。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(86, 'B', 'P-053-2XL', 'C級防護衣_Tychem 6000', 'P-053-2XL.png', '', 'Dupont', 'Thchem 6000', '2XL', '件', '1. 材質：Tychem F (Tyvek與防護膜複合而成)\n2. 車縫處貼合膠條、拉鏈加蓋。\n3. 布料抗化能力：滲透時間可抵抗正己烷化學品480分鐘以上。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(87, 'B', 'P-054-XL', '連身式C級防護衣', 'P-054-XL.png', '', '不指定', '', 'XL', '件', '1. 車縫線不得外露，必須加強接合，需採超音波銲接縫合或外貼防水條之不得有滲入風險之防護措施；臉部及肢體部份需具鬆緊帶束口。\n2. 使用Tychen C材質製成，防化學品穿透、滲透必須經過EN368、EN369測試完成。\n3. 布料抗化能力：依據 ASTM F739測試標準，滲透時間可抵抗48% 氫氟酸、93% 硫酸、氫氧化鈉等化學品480分鐘以上。\n4. 一包一件。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(88, 'B', 'P-054-3XL', '連身式C級防護衣', 'P-054-3XL.png', '', '不指定', '', '3XL', '件', '1. 車縫線不得外露，必須加強接合，需採超音波銲接縫合或外貼防水條之不得有滲入風險之防護措施；臉部及肢體部份需具鬆緊帶束口。\r\n2. 使用Tychen C材質製成，防化學品穿透、滲透必須經過EN368、EN369測試完成。\r\n3. 布料抗化能力：依據 ASTM F739測試標準，滲透時間可抵抗48% 氫氟酸、93% 硫酸、氫氧化鈉等化學品480分鐘以上。\r\n4. 一包一件。', '', '', '1.1', '1.5', 'On', '2023-07-11 13:26:00', '2023-07-05 15:02:00', '陳建良'),
(89, 'B', 'P-073-XL', 'D級防護衣', 'P-073-XL.png', '', '不指定', '', 'XL', '件', '1. 使用Tyvek材質製成。\n2. 可有效防護有害粉塵(TYPE 5)及輕微液體潑濺(TYPE 6)，符合ISO/ASTM 檢測。\n3.符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-11 13:26:00', '2023-07-05 15:02:00', '陳建良'),
(90, 'D', 'P-055', 'C級防酸鹼袖套', 'P-055.png', '', '不指定', '', '單一\n尺寸', '雙', '1. 車縫線不得外露，必須加強接合，需採超音波銲接縫合或外貼防水條之不得有滲入風險之防護措施；臉部及肢體部份需具鬆緊帶束口。\n2. 使用Tychem C材質製成，防化學品穿透、滲透必須經過EN368、EN369測試完成。\n3. 布料抗化能力：依據 ASTM F739測試標準，滲透時間可抵抗48% 氫氟酸、93% 硫酸、氫氧化鈉等化學品480分鐘以上。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(91, 'F', 'P-056', 'C級防護腳套', 'P-056.png', '', '不指定', '', '單一\n尺寸', '雙', '1. 車縫線不得外露，必須加強接合，需採超音波銲接縫合或外貼防水條之不得有滲入風險之防護措施。；臉部及肢體部份需具鬆緊帶束口。\n2. 使用Tychem C材質製成，防化學品穿透、滲透必須經過EN368、EN369測試完成。\n3. 布料抗化能力：依據 ASTM F739測試標準，滲透時間可抵抗48% 氫氟酸、93% 硫酸、氫氧化鈉等化學品480分鐘以上。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(92, 'F', 'P-057-6', 'C級化學品防護鞋', 'P-057-6.png', '', '不指定', '', '6', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(93, 'F', 'P-057-7', 'C級化學品防護鞋', 'P-057-7.png', '', '不指定', '', '7', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(94, 'F', 'P-057-8', 'C級化學品防護鞋', 'P-057-8.png', '', '不指定', '', '8', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(95, 'F', 'P-057-9', 'C級化學品防護鞋', 'P-057-9.png', '', '不指定', '', '9', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(96, 'F', 'P-057-10', 'C級化學品防護鞋', 'P-057-10.png', '', '不指定', '', '10', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(97, 'F', 'P-057-11', 'C級化學品防護鞋', 'P-057-11.png', '', '不指定', '', '11', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(98, 'F', 'P-057-12', 'C級化學品防護鞋', 'P-057-12.png', '', '不指定', '', '12', '雙', '1. 鋼頭安全型(長筒)，鋼片鞋底。\n2. 靴身能耐一般酸鹼。\n3. 符合EN/ANSI/CNS/CE認證。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(99, 'F', 'P-058-68', '皮鞋款安全鞋', 'P-058-68.png', '', '不指定', '', '68', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(100, 'F', 'P-058-70', '皮鞋款安全鞋', 'P-058-70.png', '', '不指定', '', '70', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(101, 'F', 'P-058-72', '皮鞋款安全鞋', 'P-058-72.png', '', '不指定', '', '72', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(102, 'F', 'P-058-74', '皮鞋款安全鞋', 'P-058-74.png', '', '不指定', '', '74', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(103, 'F', 'P-058-76', '皮鞋款安全鞋', 'P-058-76.png', '', '不指定', '', '76', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(104, 'F', 'P-058-78', '皮鞋款安全鞋', 'P-058-78.png', '', '不指定', '', '78', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(105, 'F', 'P-058-80', '皮鞋款安全鞋', 'P-058-80.png', '', '不指定', '', '80', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(106, 'F', 'P-058-82', '皮鞋款安全鞋', 'P-058-82.png', '', '不指定', '', '82', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(107, 'F', 'P-058-84', '皮鞋款安全鞋', 'P-058-84.png', '', '不指定', '', '84', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(108, 'F', 'P-058-86', '皮鞋款安全鞋', 'P-058-86.png', '', '不指定', '', '86', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(109, 'F', 'P-058-88', '皮鞋款安全鞋', 'P-058-88.png', '', '不指定', '', '88', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(110, 'F', 'P-058-90', '皮鞋款安全鞋', 'P-058-90.png', '', '不指定', '', '90', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(111, 'F', 'P-058-92', '皮鞋款安全鞋', 'P-058-92.png', '', '不指定', '', '92', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(112, 'F', 'P-058-94', '皮鞋款安全鞋', 'P-058-94.png', '', '不指定', '', '94', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(113, 'F', 'P-059-68', '運動款安全鞋', 'P-059-68.png', '', '不指定', '', '68', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(114, 'F', 'P-059-70', '運動款安全鞋', 'P-059-70.png', '', '不指定', '', '70', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(115, 'F', 'P-059-72', '運動款安全鞋', 'P-059-72.png', '', '不指定', '', '72', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(116, 'F', 'P-059-74', '運動款安全鞋', 'P-059-74.png', '', '不指定', '', '74', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(117, 'F', 'P-059-76', '運動款安全鞋', 'P-059-76.png', '', '不指定', '', '76', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(118, 'F', 'P-059-78', '運動款安全鞋', 'P-059-78.png', '', '不指定', '', '78', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(119, 'F', 'P-059-80', '運動款安全鞋', 'P-059-80.png', '', '不指定', '', '80', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(120, 'F', 'P-059-82', '運動款安全鞋', 'P-059-82.png', '', '不指定', '', '82', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(121, 'F', 'P-059-84', '運動款安全鞋', 'P-059-84.png', '', '不指定', '', '84', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(122, 'F', 'P-059-86', '運動款安全鞋', 'P-059-86.png', '', '不指定', '', '86', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(123, 'F', 'P-059-88', '運動款安全鞋', 'P-059-88.png', '', '不指定', '', '88', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(124, 'F', 'P-059-90', '運動款安全鞋', 'P-059-90.png', '', '不指定', '', '90', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(125, 'F', 'P-059-92', '運動款安全鞋', 'P-059-92.png', '', '不指定', '', '92', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(126, 'F', 'P-059-94', '運動款安全鞋', 'P-059-94.png', '', '不指定', '', '94', '雙', '1. 符合CNS20345測試標準\n2. 鋼頭需抗壓扁、耐衝擊性佳、具抗靜電功能\n3. 鞋底防穿刺鋼板採用不銹鋼材質', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(127, 'H', 'P-060', '堵漏處理條', 'P-060.png', '', '不指定', '', '', '條', '耐腐蝕塑鋼材質，每條長度：17 cm(7\")。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(128, 'H', 'P-061', 'pH試紙', 'P-061.png', '', '不指定', '', '', '盒', '1. 200片/盒\n2. 可於酸/鹼液測定質(pH 1.0 ~ 11.0)。\n3. 以顏色顯示測定質：紅色為酸，藍色為鹼。\n4. 出廠日期一年內，且外盒需標示有效日期。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(129, 'H', 'P-062', '化學品專用吸液棉片', 'P-062.png', '', '', '', '', '箱', '1. 需採用超細纖維材質，減少發塵狀況，並加厚處理增加吸液量。\n2. 100片/箱，25*38 cm。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(130, 'H', 'P-063', '化學品專用吸液棉條', 'P-063.png', '', '', '', '', '箱', '1. 表面需採用SMS複合材質，減少發塵狀況\n2. 12條/箱，吸液量12 Gal/箱，尺寸：7*10 cm(3\"*4\")。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(131, 'H', 'P-064', '化學品專用吸液棉枕', 'P-064.png', '', '', '', '', '箱', '1. 表面需採用SMS複合材質，減少發塵狀況\n2. 10-16個/箱，吸液量11 Gal/箱，尺寸：17*38 cm(7\"*15\")。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(132, 'H', 'P-065-S', '廢棄物處理袋_小型', 'P-065-S.png', '', '不指定', '', 'S', '個', '1. 黃色，HDPE材質。\n2. 需附一體成形綁帶(非獨立束帶)。\n3. 尺寸：55 cm * 45 cm 。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(133, 'H', 'P-065-M', '廢棄物處理袋_中型', 'P-065-M.png', '', '不指定', '', 'M', '個', '1. 黃色，HDPE材質。\n2. 需附一體成形綁帶(非獨立束帶)。\n3. 尺寸：90 cm *75 cm 。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(134, 'H', 'P-065-L', '廢棄物處理袋_大型', 'P-065-L.png', '', '不指定', '', 'L', '個', '1. 黃色，HDPE材質。\n2. 需附一體成形綁帶(非獨立束帶)。\n3. 尺寸：130 cm * 90cm 。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(135, 'I', 'P-066', '差速防墜器', 'P-066.png', '', '不指定', '', '', '組', '1. 通過ANSI/CE標準認證。\n2. 織帶長度1.8 m。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(136, 'B', 'P-067-A', '全身式安全帶', 'P-067-A.png', '', '不指定', '', '', '件', '1. 符合CNS14253-1~6規範，掛繩強度達1830 KGF不得破斷。\n2. 衝擊吸收性及強度第一次衝擊力不得超過900 KGF，且符合重複兩次不得斷裂。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(137, 'B', 'P-067-B', '全身式安全帶_加大型', 'P-067-B.png', '加大型', '不指定', '', '3XL\n以上', '件', '1. 符合CNS14253-1~6規範，掛繩強度達1830 KGF不得破斷。\n2. 衝擊吸收性及強度第一次衝擊力不得超過900 KGF，且符合重複兩次不得斷裂。', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(138, 'I', 'P-068', '雙大鉤連接繩', 'P-068.png', '', '不指定', '', '', '件', '1. 符合CNS14253-1~6規範，掛繩強度達1830 KGF不得破斷，並需附有減振包。\n2. 衝擊吸收性及強度第一次衝擊力不得超過900 KGF，且符合重複兩次不得斷裂。', '', '', '1.1', '1.5', 'Off', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(139, 'H', 'P-069', '警示帶', 'P-069.png', '', '不指定', '', '', '個', '1. 黃底紅字，尺寸：20 cm *150 m。\n2. 印刷禁止靠近或施工危險請勿靠近。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(140, 'H', 'P-070-A', '警示角錐', 'P-070-A.png', '', '不指定', '', '', '個', '橘紅色角椎', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(141, 'H', 'P-070-B', '警示角錐(含群創光電字樣)', 'P-070-B.png', '', '不指定', '', '', '個', '橘紅色角椎(含群創光電字樣)', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(142, 'H', 'P-071', '伸縮連桿', 'P-071.png', '', '不指定', '', '', '個', '具伸縮功能達2 m\n黃黑相間', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(143, 'H', 'P-074-A', '伸縮連桿接頭_4.0cm', 'P-074-A.png', '', '不指定', '', '4.0cm', '個', '1. 需配合現有伸縮連桿。\n2. 扣環端直徑4.0 cm。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(144, 'H', 'P-074-B', '伸縮連桿接頭_4.5cm', 'P-074-B.png', '', '不指定', '', '4.5cm', '個', '1. 需配合現有伸縮連桿。\n2. 扣環端直徑4.5 cm。', '', '', '1.1', '1.5', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(145, 'J', 'PT-901-LIS', '敵腐靈_眼杯_50ml', '6f014ca0d7dc5a2696b52340806b0936.jpg', '敵腐靈_LIS 50ml_眼杯', 'PREVOR', 'LIS', '50', 'ml', 'LIS 50ml_眼杯', '', '', '1.1', '1.5', 'Off', '2024-01-23 08:24:58', '2024-01-22 13:42:56', '陳建良'),
(146, 'J', 'PT-901-MIC', '敵腐靈_噴罐_100ml', 'cde7f5265d7f75f1366bf104dc10f81e.jpg', '敵腐靈_MICRO 100ml_噴罐', 'PREVOR', 'MICRO', '100', 'ml', 'MICRO 100ml_噴罐', '', '', '1.1', '1.5', 'Off', '2024-01-23 08:25:16', '2024-01-22 13:46:05', '陳建良'),
(147, 'J', 'PT-902-LPM', '六氟靈_瓶_500ml', '8c9fb0f2314928ad662e5d2d21a9307c.png', '六氟靈_LPMF 500ml_瓶', 'PREVOR', 'LPMF', '500', 'ml', 'LPMF 500ml_瓶', '', '', '1.1', '1.5', 'Off', '2024-01-23 08:25:37', '2024-01-22 14:03:51', '陳建良'),
(148, 'J', 'PT-902-DAP', '六氟靈_沖淋器_5L', 'decaf0a5919ef81eb81c9ad9a865ddc2.jpg', '六氟靈_DAPF 5L_沖淋器', 'PREVOR', 'DAPF', '5', 'L', 'DAPF 5L_沖淋器', '', '', '1.1', '1.5', 'Off', '2024-01-23 08:33:53', '2024-01-22 14:06:26', '陳建良'),
(149, 'J', 'PT-903', '葡萄糖酸鈣軟膏', '684b97a48bd089d60454f9748d0574a0.jpg', '葡萄糖酸鈣軟膏', 'PREVOR', '', '40', 'g', '葡萄糖酸鈣軟膏 gel 40g', '', '', '1.1', '1.5', 'Off', '2024-01-23 08:34:10', '2024-01-22 14:09:13', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_cate`
--

CREATE TABLE `_cate` (
  `id` int(10) UNSIGNED NOT NULL,
  `cate_title` varchar(30) NOT NULL COMMENT '分類名稱',
  `cate_remark` varchar(255) NOT NULL COMMENT '分類註解',
  `cate_no` varchar(10) NOT NULL COMMENT '分類代號',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_cate`
--

INSERT INTO `_cate` (`id`, `cate_title`, `cate_remark`, `cate_no`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, '呼吸', '呼吸', 'A', 'On', '2023-07-04 17:07:53', '2023-07-13 15:02:15', '陳建良'),
(2, '身體', '身體', 'B', 'On', '2023-07-04 17:09:54', '2023-07-04 17:09:54', 'mb 管理員'),
(3, '頭部', '頭部', 'C', 'On', '2023-07-04 17:10:05', '2023-07-04 17:21:00', ''),
(4, '手部', '手部', 'D', 'On', '2023-07-04 17:10:16', '2023-07-04 17:10:16', 'mb 管理員'),
(5, '面部眼睛', '面部眼睛', 'E', 'On', '2023-07-04 17:10:32', '2023-07-04 17:10:32', 'mb 管理員'),
(6, '足部', '足部', 'F', 'On', '2023-07-04 17:11:05', '2023-07-06 16:59:18', ''),
(7, '聽力', '聽力', 'G', 'On', '2023-07-04 17:11:19', '2023-07-06 17:07:10', ''),
(8, '其他', '其他', 'H', 'On', '2023-07-04 17:11:31', '2023-07-06 17:06:38', ''),
(9, '配件/耗材', '配件/耗材', 'I', 'On', '2023-07-06 17:06:28', '2023-07-06 17:06:28', '陳建良'),
(10, '除汙/應變', '除汙劑/應變器材', 'J', 'On', '2024-01-22 14:18:05', '2024-01-23 08:24:23', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_contact`
--

CREATE TABLE `_contact` (
  `id` int(10) UNSIGNED NOT NULL,
  `cname` varchar(30) NOT NULL COMMENT '聯絡人名稱',
  `phone` varchar(30) NOT NULL COMMENT '聯絡電話',
  `email` varchar(50) DEFAULT NULL COMMENT '電子信箱',
  `fax` varchar(30) DEFAULT NULL COMMENT '傳真',
  `comp_no` varchar(20) NOT NULL COMMENT '供應商/統編',
  `contact_remark` varchar(255) DEFAULT NULL COMMENT '註解說明',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_contact`
--

INSERT INTO `_contact` (`id`, `cname`, `phone`, `email`, `fax`, `comp_no`, `contact_remark`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, '林中強', '0930930341', 'zolalin@ms.mtcmercury.com.tw', '07-5224015', '04908814', NULL, 'On', '2023-11-30 14:50:24', '2023-11-30 14:50:24', '陳建良'),
(2, '麥可', '0930930341', 'leong710@gmail.com', '123456789', '53237150', 'test', 'On', '2023-11-30 14:50:24', '2023-11-30 14:50:24', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_fab`
--

CREATE TABLE `_fab` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `site_id` int(10) NOT NULL COMMENT '歸屬site',
  `fab_title` varchar(20) NOT NULL COMMENT 'fab名稱',
  `fab_remark` varchar(255) NOT NULL COMMENT 'fab備註',
  `buy_ty` varchar(10) NOT NULL COMMENT '安量倍數',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `sign_code` varchar(10) NOT NULL COMMENT '管理權責(部課)',
  `pm_emp_id` varchar(255) DEFAULT NULL COMMENT '轄區管理員',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_fab`
--

INSERT INTO `_fab` (`id`, `site_id`, `fab_title`, `fab_remark`, `buy_ty`, `flag`, `sign_code`, `pm_emp_id`, `created_at`, `updated_at`, `updated_user`) VALUES
(0, 1, '一般用戶', 'virtual', 'b', 'Off', '9T040500', '', '2023-07-03 14:30:51', '2023-11-13 08:21:52', '陳建良'),
(1, 1, '虛擬倉庫', 'virtual', 'b', 'Off', '9T040500', '', '2023-07-03 14:30:51', '2023-11-13 08:22:12', '陳建良'),
(2, 1, 'FAB1棟', '一廠', 'a', 'On', '9T041501', '10119798,沈旻頡', '2023-07-04 15:11:08', '2023-12-28 11:02:01', 'susu'),
(3, 1, 'FAB2棟', '二廠', 'a', 'On', '9T043501', NULL, '2023-07-04 15:11:30', '2023-10-26 13:56:51', '陳建良'),
(4, 1, 'FAB3棟', '三廠', 'a', 'On', '9T043501', NULL, '2023-07-04 15:11:59', '2023-10-26 13:56:57', '陳建良'),
(5, 1, 'TAC棟', '四廠', 'a', 'On', '9T042501', '10009261,施昱丞', '2023-07-04 15:12:32', '2023-12-29 10:39:20', '陳建良'),
(6, 1, 'FAB5棟', '五廠', 'a', 'On', '9T043502', NULL, '2023-07-04 15:12:52', '2023-10-26 13:57:02', '陳建良'),
(7, 1, 'FAB6棟', '六廠', 'a', 'On', '9T044501', NULL, '2023-07-04 15:13:11', '2023-10-26 13:54:28', '陳建良'),
(8, 1, 'FAB7棟', '七廠', 'a', 'On', '9T041502', '20020124,楊致豪', '2023-07-04 15:13:30', '2023-12-29 10:39:33', '陳建良'),
(9, 1, 'FAB8棟', '八廠', 'a', 'On', '9T044502', NULL, '2023-07-04 15:14:07', '2023-10-26 13:54:18', '陳建良'),
(10, 1, 'LCM棟', 'C廠區', 'a', 'On', '9T042502', '', '2023-07-04 15:14:29', '2023-12-19 11:14:31', '陳建良'),
(11, 1, 'TOC棟', 'TOC', 'a', 'On', '9T042501', '13085117,鄭羽淳', '2023-07-04 15:15:35', '2023-12-19 11:14:36', '陳建良'),
(12, 1, 'K9棟', '科九', 'a', 'Off', '9T042501', '', '2023-07-04 15:16:11', '2023-11-13 08:20:51', '陳建良'),
(15, 1, 'T6', 'T6', 'a', 'On', '9T044502', '10008048,陳建良', '2024-01-24 12:48:33', '2024-01-24 12:48:50', '陳建良');

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
(1, 'issue', '上半年請購時間', 'On', '2024-01-01 08:00:00', '2024-01-31 17:00:00', 'On', '2024-01-18 14:48:33', '2024-01-19 08:14:46', '陳建良'),
(2, 'issue', '下半年請購時間', 'Off', '2024-01-15 08:00:00', '2024-03-01 17:00:00', 'Off', '2024-01-18 15:16:43', '2024-01-19 08:24:20', '陳建良'),
(4, 'issue', '下半年請購時間', 'Off', '2024-07-01 08:00:00', '2024-07-31 17:00:00', 'On', '2024-01-18 15:16:43', '2024-01-19 15:55:50', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_issue`
--

CREATE TABLE `_issue` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '交易單號',
  `plant` varchar(30) NOT NULL COMMENT '申請單位',
  `dept` varchar(30) NOT NULL COMMENT '申請部門',
  `sign_code` varchar(20) NOT NULL COMMENT '部門代號',
  `in_user_id` int(10) UNSIGNED NOT NULL COMMENT '需求者emp_id',
  `cname` varchar(10) NOT NULL COMMENT '申請人姓名',
  `extp` varchar(10) NOT NULL COMMENT '申請人分機',
  `in_local` int(10) UNSIGNED NOT NULL COMMENT '需求廠區local',
  `ppty` int(10) UNSIGNED NOT NULL COMMENT '需求類別',
  `issue_remark` varchar(255) NOT NULL COMMENT '用途說明',
  `item` longtext NOT NULL COMMENT 'catalog_SN/數量',
  `idty` int(10) UNSIGNED NOT NULL COMMENT '交易狀態\r\n0完成/1待收/2退貨/3取消',
  `omager` varchar(10) NOT NULL COMMENT '主管工號',
  `in_sign` varchar(10) DEFAULT NULL COMMENT '等待簽核人員id',
  `in_signName` varchar(10) DEFAULT NULL COMMENT '待簽姓名',
  `flow` varchar(30) DEFAULT NULL COMMENT 'approval_steps：簽核流程中的步驟信息 step_name：步驟的名稱 approver：負責進行簽核的用戶或角色	',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '表單簽核紀錄',
  `create_date` datetime NOT NULL COMMENT '開單日期',
  `created_emp_id` varchar(10) NOT NULL COMMENT '開單人工號',
  `created_cname` varchar(10) NOT NULL COMMENT '開單人姓名',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '更新人員',
  `out_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT '發貨人emp_id',
  `out_local` text DEFAULT NULL COMMENT '發貨廠區 / PO單號12碼',
  `in_date` datetime DEFAULT NULL COMMENT '收貨日期',
  `_ship` varchar(12) DEFAULT NULL COMMENT '貨態 / PR單號12碼'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_issue`
--

INSERT INTO `_issue` (`id`, `plant`, `dept`, `sign_code`, `in_user_id`, `cname`, `extp`, `in_local`, `ppty`, `issue_remark`, `item`, `idty`, `omager`, `in_sign`, `in_signName`, `flow`, `logs`, `create_date`, `created_emp_id`, `created_cname`, `updated_at`, `updated_user`, `out_user_id`, `out_local`, `in_date`, `_ship`) VALUES
(7, '南科環安處/環安衛一部', '工安衛一課', '9T041501', 10119798, '沈旻頡', '5014-12119', 2, 1, '請購測試', '{\"P-028\":{\"need\":\"5\",\"pay\":\"5\"},\"P-027\":{\"need\":\"10\",\"pay\":\"10\"},\"P-072\":{\"need\":\"10\",\"pay\":\"10\"}}', 10, '11053914', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-09 16:52:37\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u8acb\\u8cfc\\u6e2c\\u8a66\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-09 16:53:17\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\"\\u8f49\\u5448\\u6e2c\\u8a66 \\/\\/ \\u539f\\u5f85\\u7c3d 11053914 \\u8f49\\u5448 10010721\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-09 16:54:51\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u8f49\\u5448\\u540c\\u610f\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 16:57:45\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"PM\\u540c\\u610f\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 16:58:12\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240109-1\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 16:59:54\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240109-1\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-09 17:00:53\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\\u7533\\u8acb\\u4eba\\u540c\\u610f\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-09 17:00:54\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-028 + 5=4_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-027 + 10=9_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-072 + 10=9\"}]', '2024-01-09 16:52:37', '10119798', '沈旻頡', '2024-01-09 17:00:53', '沈旻頡', 10008048, 'PO20240109-1', '2024-01-09 17:00:53', 'PR20240109-1'),
(8, '南科環安處/環安衛一部', '工安衛一課', '9T041501', 10119798, '沈旻頡', '5014-12119', 2, 1, '再次測試', '{\"P-014\":{\"need\":\"1000\",\"pay\":\"1000\"}}', 11, '11053914', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-09 17:14:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-09 17:14:22\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 17:24:35\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 17:25:04\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240109-2\"}]', '2024-01-09 17:14:14', '10119798', '沈旻頡', '2024-01-09 17:24:35', '陳建良', NULL, NULL, NULL, 'PR20240109-2'),
(9, '南科環安處/環安衛一部', '工安衛一課', '9T041501', 10119798, '沈旻頡', '5014-12119', 2, 1, '', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-AG\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-OV\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-D\":{\"need\":\"100\",\"pay\":\"100\"}}', 11, '11053914', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:18:27\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-11 15:19:41\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 11053914 \\u8f49\\u5448 11046016\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2024-01-11 15:21:53\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-11 15:23:16\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-11 15:25:49\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240111-1\"}]', '2024-01-11 15:18:27', '10119798', '沈旻頡', '2024-01-11 15:23:16', '陳建良', NULL, NULL, NULL, 'PR20240111-1'),
(10, '南科環安處/環安衛一部', '工安衛一課', '9T041501', 10119798, '沈旻頡', '5014-12119', 2, 1, '', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-AG\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-OV\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-D\":{\"need\":\"100\",\"pay\":\"100\"}}', 11, '11053914', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:18:35\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-11 15:25:04\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-11 15:25:22\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-11 15:25:49\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240111-1\"}]', '2024-01-11 15:18:35', '10119798', '沈旻頡', '2024-01-11 15:25:22', '陳建良', NULL, NULL, NULL, 'PR20240111-1'),
(11, '南科環安處/環安衛一部', '工安衛一課', '9T041501', 10119798, '沈旻頡', '5014-12119', 2, 1, '', '{\"P-016-S\":{\"need\":\"10\",\"pay\":\"10\"}}', 2, '11053914', NULL, NULL, 'Reject', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:54:05\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-19 10:57:22\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"}]', '2024-01-11 15:54:05', '10119798', '沈旻頡', '2024-01-19 10:57:22', '陳建良', NULL, NULL, NULL, NULL),
(12, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"2\",\"pay\":\"2\"}}', 11, '10119798', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-12 13:18:40\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:03:31\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:04:33\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:04:42\",\"action\":\"\\u8f49PR\",\"remark\":\"PR1112223334\"}]', '2024-01-12 13:18:40', '10008048', '陳建良', '2024-01-16 08:04:33', '陳建良', NULL, NULL, NULL, 'PR1112223334'),
(13, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 6, 1, '', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"}}', 11, '10010721', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 10:29:41\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 10:30:27\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 10:30:40\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:34:09\",\"action\":\"\\u8f49PR\",\"remark\":\"1000720752\"}]', '2024-01-16 10:29:41', '10008048', '陳建良', '2024-01-16 10:30:40', '陳建良', NULL, NULL, NULL, '1000720752'),
(14, '南科環安處/環安衛二部', '工安衛一課', '9T042501', 10009261, '施昱丞', '5014-44119', 3, 1, '', '{\"P-014\":{\"pay\":\"250\"}}', 13, '10015043', NULL, NULL, 'acceptance', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-24 15:31:57\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:32:13\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-24 15:32:39\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:33:05\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 10015043 \\u8f49\\u5448 10008048\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:33:15\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:33:26\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:34:09\",\"action\":\"\\u8f49PR\",\"remark\":\"1000720752\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 15:35:31\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"1000720752\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"}]', '2024-01-24 15:31:57', '10009261', '施昱丞', '2024-01-24 15:35:31', '陳建良', 10008048, '1000720752', NULL, '1000720752'),
(15, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 6, 1, '', '{\"P-014\":{\"need\":\"100\",\"pay\":\"9\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 10:41:37\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 10:41:49\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 10:42:01\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 10:42:37\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240125\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:17:47\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240125\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:18:31\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:18:31\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 + 9=258\"}]', '2024-01-25 10:41:37', '10008048', '陳建良', '2024-01-25 11:18:31', '陳建良', 10008048, 'PO20240125', '2024-01-25 11:18:31', 'PR20240125'),
(16, '南科環安處/環安衛二部', '工安衛一課', '9T042501', 10009261, '施昱丞', '5014-44119', 3, 1, '', '{\"P-015-OV\":{\"need\":\"299\",\"pay\":\"99\"},\"P-015-AG\":{\"need\":\"299\",\"pay\":\"99\"},\"P-014\":{\"need\":\"299\",\"pay\":\"99\"}}', 10, '10008048', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 11:51:22\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:51:35\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:51:44\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:52:05\",\"action\":\"\\u8f49PR\",\"remark\":\"PR1112223334\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 11:52:48\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO123456788\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 11:53:04\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 11:53:04\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## TAC\\u68df_\\u9632\\u707d\\u4e2d\\u5fc3 +\\u65b0 P-015-OV + 99 = 99_rn_## TAC\\u68df_\\u9632\\u707d\\u4e2d\\u5fc3 +\\u65b0 P-015-AG + 99 = 99_rn_## TAC\\u68df_\\u9632\\u707d\\u4e2d\\u5fc3 +\\u65b0 P-014 + 99 = 99\"}]', '2024-01-25 11:51:22', '10009261', '施昱丞', '2024-01-25 11:53:04', '施昱丞', 10008048, 'PO123456788', '2024-01-25 11:53:04', 'PR1112223334'),
(17, '南科環安處/環安衛二部', '工安衛一課', '9T042501', 10009261, '施昱丞', '5014-44119', 3, 1, '', '{\"P-014\":{\"need\":\"99999\",\"pay\":\"99999\"}}', 0, '10015043', NULL, NULL, 'waitPR', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 12:11:30\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 12:11:38\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 12:11:44\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-25 12:11:30', '10009261', '施昱丞', '2024-01-25 12:11:44', '施昱丞', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `_local`
--

CREATE TABLE `_local` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `fab_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬Fab',
  `local_title` varchar(50) NOT NULL COMMENT '子區域名稱',
  `local_remark` varchar(255) NOT NULL COMMENT '子區域註解',
  `low_level` longtext DEFAULT NULL COMMENT '安全水位',
  `flag` varchar(5) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_local`
--

INSERT INTO `_local` (`id`, `fab_id`, `local_title`, `local_remark`, `low_level`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 1, '防護具庫房', 'Office 1F發貨中心', '{\"P-014\":\"999\",\"P-015-AG\":\"100\",\"P-015-OV\":\"100\",\"P-015-D\":\"100\",\"P-016-S\":\"500\",\"P-016-M\":\"500\",\"P-016-L\":\"500\",\"P-017\":\"500\",\"P-025\":\"500\",\"P-049\":\"500\",\"P-050\":\"500\",\"P-051\":\"500\",\"P-052\":\"500\",\"P-053-XL\":\"500\",\"P-053-2XL\":\"500\",\"P-054-XL\":\"500\",\"P-054-3XL\":\"500\",\"P-073-XL\":\"0\",\"P-067-A\":\"500\",\"P-067-B\":\"500\",\"P-001-W\":\"500\",\"P-002-W\":\"500\",\"P-002-R\":\"500\",\"P-002-B\":\"500\",\"P-002-Y\":\"500\",\"P-004-W\":\"500\",\"P-004-R\":\"500\",\"P-004-B\":\"500\",\"P-004-Y\":\"500\",\"P-031-S\":\"0\",\"P-031-M\":\"0\",\"P-031-L\":\"0\",\"P-031-XL\":\"0\",\"P-032-S\":\"500\",\"P-032-M\":\"500\",\"P-032-L\":\"500\",\"P-032-XL\":\"500\",\"P-032-2XL\":\"500\",\"P-033\":\"0\",\"P-34-8\":\"500\",\"P-34-9\":\"500\",\"P-34-10\":\"500\",\"P-35\":\"500\",\"P-036\":\"0\",\"P-037\":\"0\",\"P-038\":\"0\",\"P-039\":\"0\",\"P-040\":\"500\",\"P-042-8\":\"500\",\"P-042-9\":\"500\",\"P-042-10\":\"500\",\"P-043\":\"500\",\"P-044\":\"500\",\"P-045\":\"500\",\"P-046-A\":\"500\",\"P-046-B\":\"500\",\"P-047\":\"500\",\"P-048-S\":\"500\",\"P-048-M\":\"500\",\"P-048-L\":\"500\",\"P-048-XL\":\"500\",\"P-055\":\"500\",\"P-008\":\"500\",\"P-010\":\"500\",\"P-011\":\"500\",\"P-012\":\"500\",\"P-013\":\"500\",\"P-056\":\"500\",\"P-057-6\":\"500\",\"P-057-7\":\"500\",\"P-057-8\":\"500\",\"P-057-9\":\"500\",\"P-057-10\":\"500\",\"P-057-11\":\"500\",\"P-057-12\":\"500\",\"P-058-68\":\"500\",\"P-058-70\":\"500\",\"P-058-72\":\"500\",\"P-058-74\":\"500\",\"P-058-76\":\"500\",\"P-058-78\":\"500\",\"P-058-80\":\"500\",\"P-058-82\":\"500\",\"P-058-84\":\"500\",\"P-058-86\":\"500\",\"P-058-88\":\"500\",\"P-058-90\":\"500\",\"P-058-92\":\"500\",\"P-058-94\":\"500\",\"P-059-68\":\"500\",\"P-059-70\":\"500\",\"P-059-72\":\"500\",\"P-059-74\":\"500\",\"P-059-76\":\"500\",\"P-059-78\":\"500\",\"P-059-80\":\"500\",\"P-059-82\":\"500\",\"P-059-84\":\"500\",\"P-059-86\":\"500\",\"P-059-88\":\"500\",\"P-059-90\":\"500\",\"P-059-92\":\"500\",\"P-059-94\":\"500\",\"P-027\":\"500\",\"P-072\":\"0\",\"P-028\":\"500\",\"P-029\":\"500\",\"P-030\":\"500\",\"P-060\":\"500\",\"P-061\":\"500\",\"P-062\":\"500\",\"P-063\":\"500\",\"P-064\":\"500\",\"P-065-S\":\"500\",\"P-065-M\":\"500\",\"P-065-L\":\"500\",\"P-069\":\"500\",\"P-070-A\":\"500\",\"P-070-B\":\"500\",\"P-071\":\"500\",\"P-074-A\":\"0\",\"P-074-B\":\"0\",\"P-003\":\"500\",\"P-005\":\"500\",\"P-006\":\"500\",\"P-007\":\"500\",\"P-009\":\"500\",\"P-018-A\":\"500\",\"P-018-B\":\"500\",\"P-019-A\":\"500\",\"P-019-B\":\"500\",\"P-019-C\":\"500\",\"P-020-A\":\"500\",\"P-024-B\":\"500\",\"P-025-A\":\"500\",\"P-025-B\":\"500\",\"P-025-C\":\"500\",\"P-026-A\":\"500\",\"P-026-B\":\"500\",\"P-026-C\":\"500\",\"P-026-D\":\"500\",\"P-041\":\"500\",\"P-066\":\"500\",\"P-068\":\"500\"}', 'Off', '2023-07-03 15:18:20', '2023-12-29 09:34:11', '陳建良'),
(2, 2, '工安器材室', 'SUP B1F ', '{\"P-014\":\"1000\",\"P-015-AG\":\"100\",\"P-015-OV\":\"100\",\"P-015-D\":\"100\",\"P-016-S\":\"10\",\"P-016-M\":\"10\",\"P-016-L\":\"10\",\"P-017\":\"10\",\"P-025\":\"5\",\"P-049\":\"5\",\"P-050\":\"5\",\"P-051\":\"5\",\"P-052\":\"5\",\"P-053-XL\":\"10\",\"P-053-2XL\":\"10\",\"P-054-XL\":\"10\",\"P-054-3XL\":\"10\",\"P-073-XL\":\"10\",\"P-067-A\":\"5\",\"P-067-B\":\"5\",\"P-001-W\":\"10\",\"P-002-W\":\"10\",\"P-002-R\":\"10\",\"P-002-B\":\"10\",\"P-002-Y\":\"10\",\"P-004-W\":\"10\",\"P-004-R\":\"10\",\"P-004-B\":\"10\",\"P-004-Y\":\"10\",\"P-031-S\":\"10\",\"P-031-M\":\"10\",\"P-031-L\":\"10\",\"P-031-XL\":\"10\",\"P-032-S\":\"10\",\"P-032-M\":\"10\",\"P-032-L\":\"10\",\"P-032-XL\":\"10\",\"P-032-2XL\":\"10\",\"P-033\":\"10\",\"P-34-8\":\"10\",\"P-34-9\":\"10\",\"P-34-10\":\"10\",\"P-35\":\"10\",\"P-036\":\"5\",\"P-037\":\"10\",\"P-038\":\"10\",\"P-039\":\"10\",\"P-040\":\"10\",\"P-042-8\":\"10\",\"P-042-9\":\"10\",\"P-042-10\":\"10\",\"P-043\":\"10\",\"P-044\":\"10\",\"P-045\":\"10\",\"P-046-A\":\"10\",\"P-046-B\":\"10\",\"P-047\":\"10\",\"P-048-S\":\"10\",\"P-048-M\":\"10\",\"P-048-L\":\"10\",\"P-048-XL\":\"10\",\"P-055\":\"10\",\"P-008\":\"10\",\"P-010\":\"10\",\"P-011\":\"10\",\"P-012\":\"10\",\"P-013\":\"10\",\"P-056\":\"10\",\"P-057-6\":\"2\",\"P-057-7\":\"2\",\"P-057-8\":\"2\",\"P-057-9\":\"2\",\"P-057-10\":\"2\",\"P-057-11\":\"2\",\"P-057-12\":\"2\",\"P-058-68\":\"2\",\"P-058-70\":\"2\",\"P-058-72\":\"2\",\"P-058-74\":\"2\",\"P-058-76\":\"2\",\"P-058-78\":\"2\",\"P-058-80\":\"2\",\"P-058-82\":\"2\",\"P-058-84\":\"2\",\"P-058-86\":\"2\",\"P-058-88\":\"2\",\"P-058-90\":\"2\",\"P-058-92\":\"2\",\"P-058-94\":\"2\",\"P-059-68\":\"2\",\"P-059-70\":\"2\",\"P-059-72\":\"2\",\"P-059-74\":\"2\",\"P-059-76\":\"2\",\"P-059-78\":\"2\",\"P-059-80\":\"2\",\"P-059-82\":\"2\",\"P-059-84\":\"2\",\"P-059-86\":\"2\",\"P-059-88\":\"2\",\"P-059-90\":\"2\",\"P-059-92\":\"2\",\"P-059-94\":\"2\",\"P-027\":\"20\",\"P-072\":\"20\",\"P-028\":\"5\",\"P-029\":\"5\",\"P-030\":\"5\",\"P-060\":\"10\",\"P-061\":\"10\",\"P-062\":\"10\",\"P-063\":\"10\",\"P-064\":\"5\",\"P-065-S\":\"10\",\"P-065-M\":\"10\",\"P-065-L\":\"10\",\"P-069\":\"10\",\"P-070-A\":\"10\",\"P-070-B\":\"10\",\"P-071\":\"10\",\"P-074-A\":\"10\",\"P-074-B\":\"10\",\"P-003\":\"20\",\"P-005\":\"20\",\"P-006\":\"100\",\"P-007\":\"10\",\"P-009\":\"10\",\"P-018-A\":\"20\",\"P-018-B\":\"20\",\"P-019-A\":\"100\",\"P-019-B\":\"100\",\"P-019-C\":\"100\",\"P-020-A\":\"10\",\"P-024-B\":\"10\",\"P-025-A\":\"5\",\"P-025-B\":\"5\",\"P-025-C\":\"5\",\"P-026-A\":\"5\",\"P-026-B\":\"5\",\"P-026-C\":\"5\",\"P-026-D\":\"5\",\"P-041\":\"10\",\"P-066\":\"5\",\"P-068\":\"5\"}', 'On', '2023-07-04 15:45:35', '2023-12-29 09:48:29', '陳建良'),
(3, 5, '防災中心', 'Office 1F工安器材室', '{\"P-014\":\"250\",\"P-015-AG\":\"50\",\"P-015-OV\":\"50\",\"P-015-D\":\"50\",\"P-016-S\":\"50\",\"P-016-M\":\"50\",\"P-016-L\":\"50\",\"P-017\":\"50\",\"P-025\":\"50\",\"P-049\":\"50\",\"P-050\":\"50\",\"P-051\":\"50\",\"P-052\":\"50\",\"P-053-XL\":\"50\",\"P-053-2XL\":\"50\",\"P-054-XL\":\"50\",\"P-054-3XL\":\"50\",\"P-073-XL\":\"0\",\"P-067-A\":\"50\",\"P-067-B\":\"50\",\"P-001-W\":\"50\",\"P-002-W\":\"50\",\"P-002-R\":\"50\",\"P-002-B\":\"50\",\"P-002-Y\":\"50\",\"P-004-W\":\"50\",\"P-004-R\":\"50\",\"P-004-B\":\"50\",\"P-004-Y\":\"50\",\"P-031-S\":\"0\",\"P-031-M\":\"0\",\"P-031-L\":\"0\",\"P-031-XL\":\"0\",\"P-032-S\":\"50\",\"P-032-M\":\"50\",\"P-032-L\":\"50\",\"P-032-XL\":\"50\",\"P-032-2XL\":\"50\",\"P-033\":\"0\",\"P-34-8\":\"50\",\"P-34-9\":\"50\",\"P-34-10\":\"50\",\"P-35\":\"50\",\"P-036\":\"0\",\"P-037\":\"0\",\"P-038\":\"0\",\"P-039\":\"0\",\"P-040\":\"50\",\"P-042-8\":\"50\",\"P-042-9\":\"50\",\"P-042-10\":\"50\",\"P-043\":\"50\",\"P-044\":\"50\",\"P-045\":\"50\",\"P-046-A\":\"50\",\"P-046-B\":\"50\",\"P-047\":\"50\",\"P-048-S\":\"50\",\"P-048-M\":\"50\",\"P-048-L\":\"50\",\"P-048-XL\":\"50\",\"P-055\":\"50\",\"P-008\":\"50\",\"P-010\":\"50\",\"P-011\":\"50\",\"P-012\":\"50\",\"P-013\":\"50\",\"P-056\":\"50\",\"P-057-6\":\"50\",\"P-057-7\":\"50\",\"P-057-8\":\"50\",\"P-057-9\":\"50\",\"P-057-10\":\"50\",\"P-057-11\":\"50\",\"P-057-12\":\"50\",\"P-058-68\":\"50\",\"P-058-70\":\"50\",\"P-058-72\":\"50\",\"P-058-74\":\"50\",\"P-058-76\":\"50\",\"P-058-78\":\"50\",\"P-058-80\":\"50\",\"P-058-82\":\"50\",\"P-058-84\":\"50\",\"P-058-86\":\"50\",\"P-058-88\":\"50\",\"P-058-90\":\"50\",\"P-058-92\":\"50\",\"P-058-94\":\"50\",\"P-059-68\":\"50\",\"P-059-70\":\"50\",\"P-059-72\":\"50\",\"P-059-74\":\"50\",\"P-059-76\":\"50\",\"P-059-78\":\"50\",\"P-059-80\":\"50\",\"P-059-82\":\"50\",\"P-059-84\":\"50\",\"P-059-86\":\"50\",\"P-059-88\":\"50\",\"P-059-90\":\"50\",\"P-059-92\":\"50\",\"P-059-94\":\"50\",\"P-027\":\"50\",\"P-072\":\"0\",\"P-028\":\"50\",\"P-029\":\"50\",\"P-030\":\"50\",\"P-060\":\"50\",\"P-061\":\"50\",\"P-062\":\"50\",\"P-063\":\"50\",\"P-064\":\"50\",\"P-065-S\":\"50\",\"P-065-M\":\"50\",\"P-065-L\":\"50\",\"P-069\":\"50\",\"P-070-A\":\"50\",\"P-070-B\":\"50\",\"P-071\":\"50\",\"P-074-A\":\"0\",\"P-074-B\":\"0\",\"P-003\":\"50\",\"P-005\":\"50\",\"P-006\":\"50\",\"P-007\":\"50\",\"P-009\":\"50\",\"P-018-A\":\"50\",\"P-018-B\":\"50\",\"P-019-A\":\"50\",\"P-019-B\":\"50\",\"P-019-C\":\"50\",\"P-020-A\":\"50\",\"P-024-B\":\"50\",\"P-025-A\":\"50\",\"P-025-B\":\"50\",\"P-025-C\":\"50\",\"P-026-A\":\"50\",\"P-026-B\":\"50\",\"P-026-C\":\"50\",\"P-026-D\":\"50\",\"P-041\":\"50\",\"P-066\":\"50\",\"P-068\":\"50\"}', 'On', '2023-07-04 15:59:09', '2023-12-29 09:34:42', '陳建良'),
(4, 11, 'ERC備品室', 'Office 1F 工安備品室', '{\"P-014\":\"50\",\"P-015-AG\":\"50\",\"P-015-OV\":\"50\",\"P-015-D\":\"50\",\"P-016-S\":\"50\",\"P-016-M\":\"50\",\"P-016-L\":\"50\",\"P-017\":\"50\",\"P-025\":\"50\",\"P-049\":\"50\",\"P-050\":\"50\",\"P-051\":\"50\",\"P-052\":\"50\",\"P-053-XL\":\"50\",\"P-053-2XL\":\"50\",\"P-054-XL\":\"50\",\"P-054-3XL\":\"50\",\"P-073-XL\":\"0\",\"P-067-A\":\"50\",\"P-067-B\":\"50\",\"P-001-W\":\"50\",\"P-002-W\":\"50\",\"P-002-R\":\"50\",\"P-002-B\":\"50\",\"P-002-Y\":\"50\",\"P-004-W\":\"50\",\"P-004-R\":\"50\",\"P-004-B\":\"50\",\"P-004-Y\":\"50\",\"P-031-S\":\"0\",\"P-031-M\":\"0\",\"P-031-L\":\"0\",\"P-031-XL\":\"0\",\"P-032-S\":\"50\",\"P-032-M\":\"50\",\"P-032-L\":\"50\",\"P-032-XL\":\"50\",\"P-032-2XL\":\"50\",\"P-033\":\"0\",\"P-34-8\":\"50\",\"P-34-9\":\"50\",\"P-34-10\":\"50\",\"P-35\":\"50\",\"P-036\":\"0\",\"P-037\":\"0\",\"P-038\":\"0\",\"P-039\":\"0\",\"P-040\":\"50\",\"P-042-8\":\"50\",\"P-042-9\":\"50\",\"P-042-10\":\"50\",\"P-043\":\"50\",\"P-044\":\"50\",\"P-045\":\"50\",\"P-046-A\":\"50\",\"P-046-B\":\"50\",\"P-047\":\"50\",\"P-048-S\":\"50\",\"P-048-M\":\"50\",\"P-048-L\":\"50\",\"P-048-XL\":\"50\",\"P-055\":\"50\",\"P-008\":\"50\",\"P-010\":\"50\",\"P-011\":\"50\",\"P-012\":\"50\",\"P-013\":\"50\",\"P-056\":\"50\",\"P-057-6\":\"50\",\"P-057-7\":\"50\",\"P-057-8\":\"50\",\"P-057-9\":\"50\",\"P-057-10\":\"50\",\"P-057-11\":\"50\",\"P-057-12\":\"50\",\"P-058-68\":\"50\",\"P-058-70\":\"50\",\"P-058-72\":\"50\",\"P-058-74\":\"50\",\"P-058-76\":\"50\",\"P-058-78\":\"50\",\"P-058-80\":\"50\",\"P-058-82\":\"50\",\"P-058-84\":\"50\",\"P-058-86\":\"50\",\"P-058-88\":\"50\",\"P-058-90\":\"50\",\"P-058-92\":\"50\",\"P-058-94\":\"50\",\"P-059-68\":\"50\",\"P-059-70\":\"50\",\"P-059-72\":\"50\",\"P-059-74\":\"50\",\"P-059-76\":\"50\",\"P-059-78\":\"50\",\"P-059-80\":\"50\",\"P-059-82\":\"50\",\"P-059-84\":\"50\",\"P-059-86\":\"50\",\"P-059-88\":\"50\",\"P-059-90\":\"50\",\"P-059-92\":\"50\",\"P-059-94\":\"50\",\"P-027\":\"50\",\"P-072\":\"0\",\"P-028\":\"50\",\"P-029\":\"50\",\"P-030\":\"50\",\"P-060\":\"50\",\"P-061\":\"50\",\"P-062\":\"50\",\"P-063\":\"50\",\"P-064\":\"50\",\"P-065-S\":\"50\",\"P-065-M\":\"50\",\"P-065-L\":\"50\",\"P-069\":\"50\",\"P-070-A\":\"50\",\"P-070-B\":\"50\",\"P-071\":\"50\",\"P-074-A\":\"0\",\"P-074-B\":\"0\",\"P-003\":\"50\",\"P-005\":\"50\",\"P-006\":\"50\",\"P-007\":\"50\",\"P-009\":\"50\",\"P-018-A\":\"50\",\"P-018-B\":\"50\",\"P-019-A\":\"50\",\"P-019-B\":\"50\",\"P-019-C\":\"50\",\"P-020-A\":\"50\",\"P-024-B\":\"50\",\"P-025-A\":\"50\",\"P-025-B\":\"50\",\"P-025-C\":\"50\",\"P-026-A\":\"50\",\"P-026-B\":\"50\",\"P-026-C\":\"50\",\"P-026-D\":\"50\",\"P-041\":\"50\",\"P-066\":\"50\",\"P-068\":\"50\"}', 'On', '2023-07-13 09:35:55', '2023-12-29 09:34:47', '陳建良'),
(5, 0, '集貨中心', 'default', NULL, 'Off', '2023-11-01 14:20:06', '2023-12-25 16:46:20', '陳建良'),
(6, 12, '收發室', '測試用', '{\"P-014\":\"250\",\"P-015-AG\":\"50\",\"P-015-OV\":\"50\",\"P-015-D\":\"50\",\"P-016-S\":\"500\",\"P-016-M\":\"500\",\"P-016-L\":\"500\",\"P-017\":\"500\",\"P-025\":\"500\",\"P-049\":\"500\",\"P-050\":\"500\",\"P-051\":\"500\",\"P-052\":\"500\",\"P-053-XL\":\"500\",\"P-053-2XL\":\"500\",\"P-054-XL\":\"500\",\"P-054-3XL\":\"500\",\"P-073-XL\":\"0\",\"P-067-A\":\"500\",\"P-067-B\":\"500\",\"P-001-W\":\"500\",\"P-002-W\":\"500\",\"P-002-R\":\"500\",\"P-002-B\":\"500\",\"P-002-Y\":\"500\",\"P-004-W\":\"500\",\"P-004-R\":\"500\",\"P-004-B\":\"500\",\"P-004-Y\":\"500\",\"P-031-S\":\"0\",\"P-031-M\":\"0\",\"P-031-L\":\"0\",\"P-031-XL\":\"0\",\"P-032-S\":\"500\",\"P-032-M\":\"500\",\"P-032-L\":\"500\",\"P-032-XL\":\"500\",\"P-032-2XL\":\"500\",\"P-033\":\"0\",\"P-34-8\":\"500\",\"P-34-9\":\"500\",\"P-34-10\":\"500\",\"P-35\":\"500\",\"P-036\":\"0\",\"P-037\":\"0\",\"P-038\":\"0\",\"P-039\":\"0\",\"P-040\":\"500\",\"P-042-8\":\"500\",\"P-042-9\":\"500\",\"P-042-10\":\"500\",\"P-043\":\"500\",\"P-044\":\"500\",\"P-045\":\"500\",\"P-046-A\":\"500\",\"P-046-B\":\"500\",\"P-047\":\"500\",\"P-048-S\":\"500\",\"P-048-M\":\"500\",\"P-048-L\":\"500\",\"P-048-XL\":\"500\",\"P-055\":\"500\",\"P-008\":\"500\",\"P-010\":\"500\",\"P-011\":\"500\",\"P-012\":\"500\",\"P-013\":\"500\",\"P-056\":\"500\",\"P-057-6\":\"500\",\"P-057-7\":\"500\",\"P-057-8\":\"500\",\"P-057-9\":\"500\",\"P-057-10\":\"500\",\"P-057-11\":\"500\",\"P-057-12\":\"500\",\"P-058-68\":\"500\",\"P-058-70\":\"500\",\"P-058-72\":\"500\",\"P-058-74\":\"500\",\"P-058-76\":\"500\",\"P-058-78\":\"500\",\"P-058-80\":\"500\",\"P-058-82\":\"500\",\"P-058-84\":\"500\",\"P-058-86\":\"500\",\"P-058-88\":\"500\",\"P-058-90\":\"500\",\"P-058-92\":\"500\",\"P-058-94\":\"500\",\"P-059-68\":\"500\",\"P-059-70\":\"500\",\"P-059-72\":\"500\",\"P-059-74\":\"500\",\"P-059-76\":\"500\",\"P-059-78\":\"500\",\"P-059-80\":\"500\",\"P-059-82\":\"500\",\"P-059-84\":\"500\",\"P-059-86\":\"500\",\"P-059-88\":\"500\",\"P-059-90\":\"500\",\"P-059-92\":\"500\",\"P-059-94\":\"500\",\"P-027\":\"500\",\"P-072\":\"0\",\"P-028\":\"500\",\"P-029\":\"500\",\"P-030\":\"500\",\"P-060\":\"500\",\"P-061\":\"500\",\"P-062\":\"500\",\"P-063\":\"500\",\"P-064\":\"500\",\"P-065-S\":\"500\",\"P-065-M\":\"500\",\"P-065-L\":\"500\",\"P-069\":\"500\",\"P-070-A\":\"500\",\"P-070-B\":\"500\",\"P-071\":\"500\",\"P-074-A\":\"0\",\"P-074-B\":\"0\",\"P-003\":\"500\",\"P-005\":\"500\",\"P-006\":\"500\",\"P-007\":\"500\",\"P-009\":\"500\",\"P-018-A\":\"500\",\"P-018-B\":\"500\",\"P-019-A\":\"500\",\"P-019-B\":\"500\",\"P-019-C\":\"500\",\"P-020-A\":\"500\",\"P-024-B\":\"500\",\"P-025-A\":\"500\",\"P-025-B\":\"500\",\"P-025-C\":\"500\",\"P-026-A\":\"500\",\"P-026-B\":\"500\",\"P-026-C\":\"500\",\"P-026-D\":\"500\",\"P-041\":\"500\",\"P-066\":\"500\",\"P-068\":\"500\"}', 'On', '2023-11-03 11:19:48', '2023-12-29 09:34:55', '陳建良'),
(7, 8, '工安備品室 ', 'Office 1F工安備品室', NULL, 'On', '2023-12-29 10:38:59', '2023-12-29 10:38:59', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_pno`
--

CREATE TABLE `_pno` (
  `id` int(10) UNSIGNED NOT NULL,
  `part_no` varchar(30) NOT NULL COMMENT '料號',
  `pno_remark` varchar(255) DEFAULT NULL COMMENT '料號註解',
  `_year` year(4) NOT NULL COMMENT '年度',
  `price` varchar(255) DEFAULT NULL COMMENT '年度/單價NT$',
  `cata_SN` varchar(10) NOT NULL COMMENT '器材編號',
  `size` varchar(10) DEFAULT NULL COMMENT '尺寸',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_pno`
--

INSERT INTO `_pno` (`id`, `part_no`, `pno_remark`, `_year`, `price`, `cata_SN`, `size`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, '未建立', '屬性清單新增透氣式', '2023', '0', 'P-001-W', '白', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(2, 'R1201023JLZ0', '', '2023', '0', 'P-002-W', '白', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(3, 'R1201023JLB0', '', '2023', '0', 'P-002-R', '紅', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(4, 'R1201023M9G0', '', '2023', '0', 'P-002-B', '藍', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(5, 'R1201023M9F0', '', '2023', '0', 'P-002-Y', '黃', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(6, 'R1201093JLE0', '', '2023', '0', 'P-003', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(7, 'R1201143JJ70', '', '2023', '0', 'P-004-W', '白', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(8, 'R1201143M9E0', '', '2023', '0', 'P-004-R', '紅', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(9, 'R1201143M9D0', '', '2023', '0', 'P-004-B', '藍', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(10, 'R1201143JJ80', '', '2023', '0', 'P-004-Y', '黃', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(11, 'R1201103JJ90', '', '2023', '0', 'P-005', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(12, 'R1201153JLD0', '', '2023', '0', 'P-006', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(13, 'R12031606S40', '', '2023', '0', 'P-007', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(14, 'R12032108KU0', '', '2023', '0', 'P-008', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(15, 'R1203193PB30', '', '2023', '0', 'P-009', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(16, 'R1202083HUE0', 'IST20230600079-凍結申請單\n料號待更新', '2023', '0', 'P-010', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(17, 'R1202093PUI0', 'IST20230600080-凍結申請單\n料號待更新', '2023', '0', 'P-011', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(18, 'R1202073XTI0', '', '2023', '0', 'P-012', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(19, 'R12020335BP0', '', '2023', '0', 'P-013', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(20, 'R1203053ZJ50', '', '2023', '{\"0\":0,\"2023\":\"0.5\"}', 'P-014', '', 'On', '2023-07-06 11:43:00', '2023-12-26 16:53:30', '陳建良'),
(21, 'R12030635E60', '料號待更新\nIR-1369007', '2023', '0', 'P-015-AG', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(22, 'R1203073ZDC0', 'IST20230600081-凍結申請單\n料號待更新\nIR-1369010', '2023', '0', 'P-015-OV', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(23, 'R12032J3HWZ0', '', '2023', '0', 'P-015-D', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(24, 'R12032K3XZR0', '', '2023', '0', 'P-016-S', 'S', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(25, 'R12032K3HX10', '', '2023', '0', 'P-016-M', 'M', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(26, 'R12032K3XZS0', '', '2023', '0', 'P-016-L', 'L', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(27, 'R12031706PR0', '', '2023', '0', 'P-017', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(28, 'R1208163HVX0', '', '2023', '0', 'P-018-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(29, 'R1208193P9J0', '', '2023', '0', 'P-018-B', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(30, 'R1208083HW10', '', '2023', '0', 'P-019-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(31, 'R12081535D50', '', '2023', '0', 'P-019-B', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(32, 'R12080906OZ0', '', '2023', '0', 'P-019-C', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(33, 'R12080649DG0', '', '2023', '0', 'P-020-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(34, 'R12080249DI0', '', '2023', '0', 'P-024-B', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(35, 'R12032549ZK0', '', '2023', '0', 'P-025', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(36, '未建立', '屬性清單新增透型號', '2023', '0', 'P-025-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(37, 'R12030949ZL0', '', '2023', '0', 'P-025-B', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(38, 'R12032649DF0', '', '2023', '0', 'P-025-C', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(39, 'R12032544G40', '', '2023', '0', 'P-026-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(40, 'R12032544G30', '', '2023', '0', 'P-026-B', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(41, 'R12032544G20', '', '2023', '0', 'P-026-C', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(42, 'R1203253PUJ0', '', '2023', '0', 'P-026-D', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(43, 'R1207063HWQ0', '', '2023', '0', 'P-027', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(44, 'R1207043PGS0', '', '2023', '0', 'P-028', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(45, 'R12070235BZ0', '', '2023', '0', 'P-029', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(46, 'R12070306P90', '', '2023', '0', 'P-030', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(47, 'R12050808KC0', '待確認\r\nIST20230600082-凍結申請單', '2023', '{\"0\":0,\"2022\":\"0\",\"2023\":\"0\"}', 'P-031', '單一尺寸', 'On', '2023-07-06 11:43:00', '2023-11-15 13:00:39', '陳建良'),
(48, 'R1205073PI90', '', '2023', '0', 'P-032-S', 'S', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(49, 'R1205073PIA0', '', '2023', '0', 'P-032-M', 'M', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(50, 'R12050708KD0', '', '2023', '0', 'P-032-L', 'L', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(51, 'R1205073PHS0', '', '2023', '0', 'P-032-XL', 'XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(52, 'R1205073PJG0', '', '2023', '0', 'P-032-2XL', '2XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(53, 'R12051D3HVY0', '', '2023', '0', 'P-33', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(54, 'R1205073XV30', '', '2023', '0', 'P-34-8', '8', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(55, 'R1205073XV40', '', '2023', '0', 'P-34-9', '9', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(56, 'R1205073XV50', '', '2023', '0', 'P-34-10', '10', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(57, 'R1205073PA20', 'IST20230600082-凍結申請單', '2023', '0', 'P-35', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(58, 'R12051G3PSY0', '', '2023', '0', 'P-36', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(59, 'R12050735BU0', '', '2023', '0', 'P-37', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(60, 'R12051504BJ0', '', '2023', '0', 'P-38', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(61, 'R12051102WN0', '', '2023', '0', 'P-39', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(62, 'R12051E3HWU0', '', '2023', '0', 'P-040', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(63, 'R1205063XV60', '', '2023', '{\"0\":0,\"2023\":\"0\"}', 'P-041', '單一尺寸', 'On', '2023-07-06 11:43:00', '2023-12-25 11:59:30', '陳建良'),
(64, 'R1205033PD10', '', '2023', '0', 'P-042-8', '8', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(65, 'R1205033PD20', '', '2023', '0', 'P-042-9', '9', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(66, 'R1205033PEE0', '', '2023', '0', 'P-042-10', '10', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(67, 'R1205063JJA0', '', '2023', '0', 'P-043', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(68, 'R1205033JJ50', '', '2023', '0', 'P-044', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(69, 'R1205063PSH0', '', '2023', '0', 'P-045', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(70, 'R1205013PD00', '', '2023', '0', 'P-046-A', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(71, 'R1205013PB50', '', '2023', '0', 'P-046-B', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(72, 'R12050135BT0', '', '2023', '0', 'P-047', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(73, 'R1205033PLK0', '', '2023', '0', 'P-048-S', 'S', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(74, 'R1205033PLL0', '', '2023', '0', 'P-048-M', 'M', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(75, 'R12050307FJ0', '', '2023', '0', 'P-048-L', 'L', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(76, 'R1205033PM30', '', '2023', '0', 'P-048-XL', 'XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(77, 'R12041106PG0', '', '2023', '0', 'P-049', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(78, 'R1204033PB40', '', '2023', '0', 'P-050', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(79, 'R1204163JJ60', '', '2023', '0', 'P-051', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(80, 'R12040108KK0', '', '2023', '0', 'P-052', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(81, 'R1204043XZT0', '', '2023', '0', 'P-053-XL', 'XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(82, 'R1204043PLJ0', '', '2023', '0', 'P-053-2XL', '2XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(83, 'R12040104680', '', '2023', '0', 'P-054-XL', 'XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(84, 'R1204013PL60', '', '2023', '0', 'P-054-3XL', '3XL', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(85, 'R120412052W0', '', '2023', '0', 'P-055', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(86, 'R12040906PF0', '', '2023', '0', 'P-056', '單一\n尺寸', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(87, 'R1206013PM40', '', '2023', '0', 'P-057-6', '6', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(88, 'R1206013PM60', '', '2023', '0', 'P-057-7', '7', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(89, 'R1206013PKS0', '', '2023', '0', 'P-057-8', '8', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(90, 'R1206013PKR0', '', '2023', '0', 'P-057-9', '9', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(91, 'R1206013PM50', '', '2023', '0', 'P-057-10', '10', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(92, 'R12060107EC0', '', '2023', '0', 'P-057-11', '11', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(93, 'R1206013PJI0', '', '2023', '0', 'P-057-12', '12', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(94, 'R1206043PHQ0', '', '2023', '0', 'P-058-68', '68', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(95, 'R1206043PHR0', '', '2023', '0', 'P-058-70', '70', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(96, 'R1206043PJH0', '', '2023', '0', 'P-058-72', '72', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(97, 'R1206043PHU0', '', '2023', '0', 'P-058-74', '74', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(98, 'R1206043PID0', '', '2023', '0', 'P-058-76', '76', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(99, 'R1206043PIE0', '', '2023', '0', 'P-058-78', '78', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(100, 'R1206043PHT0', '', '2023', '0', 'P-058-80', '80', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(101, 'R1206043PIF0', '', '2023', '0', 'P-058-82', '82', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(102, 'R1206043PJ40', '', '2023', '0', 'P-058-84', '84', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(103, 'R1206043PIC0', '', '2023', '0', 'P-058-86', '86', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(104, 'R1206043PIB0', '', '2023', '0', 'P-058-88', '88', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(105, 'R1206043PJE0', '', '2023', '0', 'P-058-90', '90', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(106, 'R1206043PJD0', '', '2023', '0', 'P-058-92', '92', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(107, 'R1206043PJF0', '', '2023', '0', 'P-058-94', '94', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(108, 'R1206043PGD0', '', '2023', '0', 'P-059-68', '68', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(109, 'R1206043PGC0', '', '2023', '0', 'P-059-70', '70', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(110, 'R1206043PGF0', '', '2023', '0', 'P-059-72', '72', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(111, 'R1206043PG40', '', '2023', '0', 'P-059-74', '74', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(112, 'R1206043PGE0', '', '2023', '0', 'P-059-76', '76', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(113, 'R1206043PGM0', '', '2023', '0', 'P-059-78', '78', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(114, 'R1206043PGB0', '', '2023', '0', 'P-059-80', '80', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(115, 'R1206043PGG0', '', '2023', '0', 'P-059-82', '82', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(116, 'R1206043PGH0', '', '2023', '0', 'P-059-84', '84', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(117, 'R1206043PGL0', '', '2023', '0', 'P-059-86', '86', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(118, 'R1206043PGI0', '', '2023', '0', 'P-059-88', '88', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(119, 'R1206043PGJ0', '', '2023', '0', 'P-059-90', '90', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(120, 'R1206043PGK0', '', '2023', '0', 'P-059-92', '92', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(121, 'R1206043PGR0', '', '2023', '0', 'P-059-94', '94', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(122, 'R12152035E50', '', '2023', '0', 'P-060', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(123, 'R1210010NEB0', '', '2023', '0', 'P-061', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(124, 'R12100208K60', '', '2023', '0', 'P-062', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(125, 'R1210033Y1E0', '', '2023', '0', 'P-063', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(126, 'R1210043PA40', '', '2023', '0', 'P-064', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(127, 'R1211013PGT0', '', '2023', '0', 'P-065-S', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(128, 'R1211013PFW0', '', '2023', '0', 'P-065-M', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(129, 'R1211013PFZ0', '', '2023', '0', 'P-065-L', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(130, 'R1209083PEF0', '', '2023', '0', 'P-066', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(131, 'R12090135BV0', '', '2023', '0', 'P-067-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(132, 'R1209013QA40', '', '2023', '0', 'P-067-B', '3XL\n以上', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(133, 'R12090306OX0', '', '2023', '0', 'P-068', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(134, 'R1212013JLC0', '', '2023', '0', 'P-069', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(135, 'R1212052SAJ0', '', '2023', '0', 'P-070-A', '', 'On', '2023-07-06 11:43:00', '2023-07-05 15:02:00', '陳建良'),
(136, 'R12120346UZ0', '', '2023', '{\"2023\":\"0\"}', 'P-070-B', '', 'On', '2023-07-06 11:43:00', '2023-11-15 12:58:56', '陳建良'),
(137, 'R12120402WE0', '', '2023', '{\"2023\":\"0\"}', 'P-071', '', 'On', '2023-07-06 11:43:00', '2023-11-15 13:41:57', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_receive`
--

CREATE TABLE `_receive` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL COMMENT '系統uuid',
  `plant` varchar(30) NOT NULL COMMENT '申請單位',
  `dept` varchar(30) NOT NULL COMMENT '申請部門',
  `sign_code` varchar(20) NOT NULL COMMENT '部門代號',
  `emp_id` varchar(10) NOT NULL COMMENT '申請人工號',
  `cname` varchar(20) NOT NULL COMMENT '申請人姓名',
  `extp` varchar(10) NOT NULL COMMENT '申請人分機',
  `local_id` int(10) UNSIGNED NOT NULL COMMENT '申請廠區(接案)',
  `ppty` int(10) UNSIGNED NOT NULL COMMENT '需求類別：1一般、3緊急',
  `receive_remark` varchar(255) NOT NULL COMMENT '用途說明',
  `cata_SN_amount` longtext NOT NULL COMMENT '需求清單',
  `idty` int(10) UNSIGNED NOT NULL COMMENT 'status：表單狀態 0完成/1待收/2退貨/3取消',
  `omager` varchar(10) NOT NULL COMMENT '主管工號',
  `in_sign` varchar(10) DEFAULT NULL COMMENT '等待簽核人員id',
  `in_signName` longtext DEFAULT NULL COMMENT '待簽姓名',
  `flow` longtext DEFAULT NULL COMMENT 'approval_steps：簽核流程中的步驟信息\r\nstep_name：步驟的名稱\r\napprover：負責進行簽核的用戶或角色',
  `logs` longtext NOT NULL COMMENT '表單簽核紀錄',
  `created_at` datetime NOT NULL COMMENT '開單日期',
  `created_emp_id` varchar(10) NOT NULL COMMENT '開單人工號',
  `created_cname` varchar(10) NOT NULL COMMENT '開單人姓名',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '更新人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_receive`
--

INSERT INTO `_receive` (`id`, `uuid`, `plant`, `dept`, `sign_code`, `emp_id`, `cname`, `extp`, `local_id`, `ppty`, `receive_remark`, `cata_SN_amount`, `idty`, `omager`, `in_sign`, `in_signName`, `flow`, `logs`, `created_at`, `created_emp_id`, `created_cname`, `updated_at`, `updated_user`) VALUES
(1, 'b3023379-a52e-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test-1', '{\"P-014\":{\"need\":\"100\",\"pay\":\"10\"},\"P-015-AG\":{\"need\":\"100\",\"pay\":\"10\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:10:48\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 11:12:45\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"reject-1\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:15:00\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"test-2\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 11:17:42\",\"action\":\"\\u8f49\\u5448 (Forwarded)\",\"remark\":\"test fw \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 11053914\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2023-12-28 11:18:35\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\\u91d1\\u9000\\u56de\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:19:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u826f\\u518d\\u9001\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 11:19:52\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"app\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 11:21:38\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"Del\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 11:21:38\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 960_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG - 10 = 975\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2023-12-28 14:15:54\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u74b0\\u5b89\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2023-12-28 14:16:19\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2023-12-28 11:10:48', '10008048', '陳建良', '2023-12-28 14:16:19', '鍾佳琪'),
(2, 'b6710879-a52e-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test-1', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"},\"P-015-OV\":{\"need\":\"10\",\"pay\":\"10\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:10:54\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 11:14:25\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"reject-2\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 14:22:09\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 14:22:29\",\"action\":\"\\u8f49\\u5448 (Forwarded)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 11053914\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2023-12-28 14:22:40\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2023-12-28 14:23:17\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2023-12-28 14:23:17\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 100 = 860_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-OV - 10 = 990\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2023-12-28 14:23:17\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"(\\u81ea\\u52d5\\u7c3d\\u6838)\"},{\"step\":\"\\u74b0\\u5b89\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2023-12-28 14:23:46\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2023-12-28 11:10:54', '10008048', '陳建良', '2023-12-28 14:23:46', '鍾佳琪'),
(3, 'ba5f20c7-a52e-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test-1', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"}}', 11, '10010721', '11053914', '鍾佳琪', 'ESHmanager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:11:01\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 14:34:33\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 14:34:55\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 14:34:55\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 100 = 760\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2023-12-28 15:07:48\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2023-12-28 11:11:01', '10008048', '陳建良', '2023-12-28 15:07:48', '沈旻頡'),
(4, 'be407b60-a52e-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test-1', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"}}', 13, '10010721', NULL, NULL, 'PPEpm', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:11:07\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2023-12-28 15:05:07\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 15:06:04\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2023-12-28 15:06:04\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 100 = 660\"}]', '2023-12-28 11:11:07', '10008048', '陳建良', '2023-12-28 15:06:04', '徐慈雅'),
(5, 'c16e215e-a52e-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test-1', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"}}', 1, '10010721', '10010721', '陳飛良', 'Manager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:11:13\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '2023-12-28 11:11:13', '10008048', '陳建良', '2023-12-28 11:11:13', '陳建良'),
(6, '1e4671e6-a52f-11ee-a7c2-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, 'test2', '{\"P-015-AG\":{\"need\":\"100\",\"pay\":\"100\"}}', 3, '10010721', NULL, NULL, 'abort', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 11:13:48\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"test2\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 14:31:00\",\"action\":\"\\u4f5c\\u5ee2 (Abort)\",\"remark\":\"\"}]', '2023-12-28 11:13:48', '10008048', '陳建良', '2023-12-28 14:31:00', '陳建良'),
(7, 'd464e0d7-a5e0-11ee-80fe-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"1\",\"pay\":\"1\"}}', 2, '10010721', NULL, NULL, 'Reject', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-29 08:25:54\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-29 08:26:09\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"}]', '2023-12-29 08:25:54', '10008048', '陳建良', '2023-12-29 08:26:09', '陳建良'),
(8, '93ee5b8c-aab2-11ee-a5dc-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, '1/4測試流程', '{\"P-014\":{\"pay\":\"10\"},\"P-015-AG\":{\"pay\":\"10\"}}', 13, '10010721', NULL, NULL, 'PPEpm', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 11:37:25\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"1\\/4\\u6e2c\\u8a66\\u6d41\\u7a0b\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 11:38:38\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u4ee3\\u4e3b\\u7ba1\\u540c\\u610f\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-12 14:40:30\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-12 14:40:30\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 1662_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG - 10 = 975\"}]', '2024-01-04 11:37:25', '10008048', '陳建良', '2024-01-12 14:40:30', '陳建良'),
(9, '49634e7c-ab85-11ee-b5a4-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"10\",\"pay\":\"10\"}}', 1, '10010721', '10010721', '陳飛良', 'Manager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 12:45:43\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '2024-01-05 12:45:43', '10008048', '陳建良', '2024-01-05 12:45:43', '陳建良'),
(10, '424c0d38-aec9-11ee-b6d9-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 2, 1, '領用測試', '{\"P-028\":{\"pay\":\"1\"},\"P-072\":{\"pay\":\"1\"},\"P-027\":{\"pay\":\"1\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-09 16:29:52\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u9818\\u7528\\u6e2c\\u8a66\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-09 16:42:00\",\"action\":\"\\u8f49\\u5448 (Forwarded)\",\"remark\":\"\\u8f49\\u5448\\u6e2c\\u8a66 \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 11053914\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-09 16:44:00\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u8f49\\u5448\\u540c\\u610f\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2024-01-09 16:44:51\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\\u540c\\u4e8b\\u4ee3\\u4ea4\\u8ca8\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2024-01-09 16:44:52\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-028 - 1 = -1_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-072 - 1 = -1_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-027 - 1 = -1\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-09 16:46:36\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u627f\\u8fa6\\u540c\\u610f\"},{\"step\":\"\\u74b0\\u5b89\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-09 16:47:02\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u4e3b\\u7ba1\\u540c\\u610f\"}]', '2024-01-09 16:29:52', '10008048', '陳建良', '2024-01-09 16:47:02', '鍾佳琪'),
(11, '7a6b835c-b048-11ee-914c-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10010721', '陳飛良', '5014-72112', 7, 1, 'test', '{\"P-015-AG\":{\"need\":\"999\",\"pay\":\"999\"}}', 1, '10007825', '10007825', '謝德良', 'Manager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-11 14:13:04\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '2024-01-11 14:13:04', '10010721', '陳飛良', '2024-01-11 14:13:04', '陳飛良'),
(12, '04b0583d-b04d-11ee-914c-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10010721', '陳飛良', '5014-72112', 3, 1, '', '{\"P-015-OV\":{\"need\":\"999\",\"pay\":\"999\"}}', 1, '10007825', '10007825', '謝德良', 'Manager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-11 14:45:34\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '2024-01-11 14:45:34', '10010721', '陳飛良', '2024-01-11 14:45:34', '陳飛良'),
(13, 'cb61b25e-b04e-11ee-914c-2cfda183ef4f', '南科環安處/環安衛一部', '工安衛一課', '9T041501', '11053914', '鍾佳琪', '5014-12114', 4, 1, '', '{\"P-015-D\":{\"need\":\"999\",\"pay\":\"999\"}}', 1, '10010721', '10010721', '陳飛良', 'Manager', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-11 14:58:16\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '2024-01-11 14:58:16', '11053914', '鍾佳琪', '2024-01-11 14:58:16', '鍾佳琪'),
(14, '30585e8c-b053-11ee-914c-2cfda183ef4f', '南科環安處/環安衛一部', '工安衛一課', '9T041501', '11046016', '徐慈雅', '5014-12117', 2, 1, '', '{\"P-073-XL\":{\"pay\":\"2\"}}', 10, '11053914', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u5f90\\u6148\\u96c5 (11046016)\",\"datetime\":\"2024-01-11 15:29:44\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-11 15:31:52\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:32:22\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:32:22\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-073-XL - 2 = -2\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:32:22\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"(\\u81ea\\u52d5\\u7c3d\\u6838)\"},{\"step\":\"\\u74b0\\u5b89\\u4e3b\\u7ba1\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-11 15:32:46\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-11 15:29:44', '11046016', '徐慈雅', '2024-01-11 15:32:46', '鍾佳琪'),
(15, '83ed9134-b513-11ee-8aac-2cfda183ef4f', '南科環安處', '環安衛一部', '9T041500', '10008048', '陳建良', '5014-42117', 6, 1, '', '{\"P-014\":{\"need\":\"3\",\"pay\":\"3\"}}', 12, '10008048', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-17 16:36:31\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-24 16:04:13\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-17 16:36:31', '10008048', '陳建良', '2024-01-24 16:04:13', '陳建良'),
(16, '21d9fe4f-ba8f-11ee-ae84-2cfda183ef4f', '南科環安處/環安衛二部', '工安衛一課', '9T042501', '10009261', '施昱丞', '5014-44119', 3, 1, '', '{\"P-014\":{\"need\":\"10\",\"pay\":\"10\"}}', 12, '10015043', NULL, NULL, 'collect', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-24 16:04:00\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-24 16:04:42\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-24 16:04:00', '10009261', '施昱丞', '2024-01-24 16:04:42', '施昱丞'),
(17, 'f6d43b4b-bb47-11ee-839d-2cfda183ef4f', '南科環安處/環安衛二部', '工安衛一課', '9T042501', '10009261', '施昱丞', '5014-44119', 3, 1, '', '{\"P-014\":{\"need\":\"99\",\"pay\":\"99\"},\"P-015-OV\":{\"need\":\"10\",\"pay\":\"10\"}}', 10, '10008048', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:07:05\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:07:29\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:07:52\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:07:52\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## TAC\\u68df_\\u9632\\u707d\\u4e2d\\u5fc3 P-014 - 99 = 0_rn_## TAC\\u68df_\\u9632\\u707d\\u4e2d\\u5fc3 P-015-OV - 10 = 89\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:07:52\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"(\\u81ea\\u52d5\\u7c3d\\u6838)\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:08:14\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-25 14:07:05', '10009261', '施昱丞', '2024-01-25 14:08:14', '陳建良'),
(18, '8a766518-bb48-11ee-839d-2cfda183ef4f', '南科環安處/環安衛二部', '工安衛一課', '9T042501', '10009261', '施昱丞', '5014-44119', 7, 1, '', '{\"P-016-S\":{\"need\":\"10\",\"pay\":\"10\"},\"P-016-M\":{\"need\":\"10\",\"pay\":\"10\"},\"P-016-L\":{\"need\":\"10\",\"pay\":\"10\"}}', 10, '10008048', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:11:13\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:11:40\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:12:26\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:12:26\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB7\\u68df_\\u5de5\\u5b89\\u5099\\u54c1\\u5ba4  +\\u65b0 P-016-S - 10 = -10_rn_## FAB7\\u68df_\\u5de5\\u5b89\\u5099\\u54c1\\u5ba4  +\\u65b0 P-016-M - 10 = -10_rn_## FAB7\\u68df_\\u5de5\\u5b89\\u5099\\u54c1\\u5ba4  +\\u65b0 P-016-L - 10 = -10\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:12:47\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:13:05\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-25 14:11:13', '10009261', '施昱丞', '2024-01-25 14:13:05', '陳建良'),
(19, 'fdb430d0-bb48-11ee-839d-2cfda183ef4f', '南科環安處/環安衛二部', '工安衛一課', '9T042501', '10009261', '施昱丞', '5014-44119', 4, 1, '', '{\"P-014\":{\"need\":\"105\",\"pay\":\"105\"},\"P-015-OV\":{\"need\":\"15\",\"pay\":\"15\"},\"P-015-D\":{\"need\":\"15\",\"pay\":\"15\"},\"P-016-S\":{\"need\":\"20\",\"pay\":\"20\"}}', 10, '10008048', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:14:26\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:14:35\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:14:41\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"\"},{\"step\":\"PPE\\u767c\\u653e\\u4eba\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:14:41\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## TOC\\u68df_ERC\\u5099\\u54c1\\u5ba4 +\\u65b0 P-014 - 105 = -105_rn_## TOC\\u68df_ERC\\u5099\\u54c1\\u5ba4 +\\u65b0 P-015-OV - 15 = -15_rn_## TOC\\u68df_ERC\\u5099\\u54c1\\u5ba4 +\\u65b0 P-015-D - 15 = -15_rn_## TOC\\u68df_ERC\\u5099\\u54c1\\u5ba4 +\\u65b0 P-016-S - 20 = -20\"},{\"step\":\"PPEpm\",\"cname\":\"\\u65bd\\u6631\\u4e1e (10009261)\",\"datetime\":\"2024-01-25 14:14:46\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-25 14:15:04\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"}]', '2024-01-25 14:14:26', '10009261', '施昱丞', '2024-01-25 14:15:04', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_site`
--

CREATE TABLE `_site` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `site_title` varchar(20) NOT NULL COMMENT 'site名稱',
  `site_remark` varchar(255) NOT NULL COMMENT 'site註解',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_site`
--

INSERT INTO `_site` (`id`, `site_title`, `site_remark`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'tnSite', '南科', 'On', '2023-07-03 14:22:27', '2023-11-07 10:21:31', '陳建良'),
(2, 'tempStorage', '暫存區', 'Off', '2023-07-04 15:10:00', '2023-11-27 10:27:13', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_stock`
--

CREATE TABLE `_stock` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `local_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬local',
  `cata_SN` varchar(20) NOT NULL COMMENT '器材編號SN',
  `standard_lv` int(10) DEFAULT NULL COMMENT '安全存量STD_LV',
  `amount` int(10) NOT NULL COMMENT '現況數量',
  `stock_remark` varchar(255) DEFAULT NULL COMMENT '備註說明',
  `pno` varchar(20) DEFAULT NULL COMMENT 'part_no料號',
  `po_no` varchar(20) DEFAULT NULL COMMENT 'PO採購編號',
  `lot_num` date DEFAULT NULL COMMENT '批號/效期',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_stock`
--

INSERT INTO `_stock` (`id`, `local_id`, `cata_SN`, `standard_lv`, `amount`, `stock_remark`, `pno`, `po_no`, `lot_num`, `created_at`, `updated_at`, `updated_user`) VALUES
(2, 6, 'P-015-AG', 50, 115, '\n', '', '', '9999-12-31', '2023-11-28 10:43:21', '2023-12-28 10:16:34', '陳建良'),
(6, 2, 'P-014', 1000, 1662, ' *PO20240109-2請購入帳：3 *PO123456788請購入帳：999 *PO20240105-1請購入帳：100 *PO20240105-1請購入帳：5 *PO20240104-1請購入帳：5', '', '', '9999-12-31', '2023-12-04 17:10:54', '2024-01-12 14:40:30', '陳建良'),
(13, 2, 'P-015-D', 100, 1005, ' *PO20240105-1請購入帳：5', '', '', '9999-12-31', '2023-12-06 09:59:18', '2024-01-05 09:26:37', '陳建良'),
(14, 2, 'P-015-OV', 100, 1000, ' *PO20240105-1請購入帳：5 *PO20240104-1請購入帳：5', '', '', '9999-12-31', '2023-12-06 09:59:18', '2024-01-05 09:26:37', '陳建良'),
(15, 2, 'P-015-AG', 100, 975, ' *PO20240105-1請購入帳：5 *PO20240104-1請購入帳：5', '', '', '9999-12-31', '2023-12-06 09:59:18', '2024-01-12 14:40:30', '陳建良'),
(17, 6, 'P-014', 250, 258, ' *PO20240125請購入帳：9\n', '', '', '9999-12-31', '2023-12-06 16:38:17', '2024-01-25 11:18:31', '陳建良'),
(20, 6, 'P-015-D', 50, 100, '', '', '', '9999-12-31', '2023-12-07 15:11:02', '2023-12-27 16:04:14', '陳建良'),
(21, 6, 'P-015-OV', 50, 100, '', '', '', '9999-12-31', '2023-12-07 15:11:02', '2023-12-27 16:04:15', '陳建良'),
(34, 2, 'P-016-S', 10, 45, '', '', '', '9999-12-31', '2023-12-08 15:23:55', '2023-12-28 08:41:36', '陳建良'),
(35, 2, 'P-016-M', 10, 45, '', '', '', '9999-12-31', '2023-12-08 15:23:55', '2023-12-28 08:41:36', '陳建良'),
(36, 2, 'P-016-L', 10, 45, '', '', '', '9999-12-31', '2023-12-08 15:23:55', '2023-12-28 08:41:36', '陳建良'),
(37, 2, 'P-041', 10, 200, '', '', '', '9999-12-31', '2023-12-11 14:59:05', '2023-12-27 16:03:27', '陳建良'),
(38, 2, 'P-050', 5, 7, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：5', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(39, 2, 'P-051', 5, 7, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：5', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(40, 2, 'P-052', 5, 7, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：5', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(41, 2, 'P-053-XL', 10, 12, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(42, 2, 'P-053-2XL', 10, 12, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(43, 2, 'P-054-XL', 10, 12, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(44, 2, 'P-054-3XL', 10, 12, ' *PO20231227-2請購入帳：2 *PO20231227-1請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:20:22', '2023-12-27 16:29:14', '陳建良'),
(45, 2, 'P-067-A', 5, 15, ' *PO20231227-3請購入帳：5', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2024-01-09 15:49:18', '陳建良'),
(46, 2, 'P-067-B', 5, 5, ' *PO20231227-3請購入帳：5', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2023-12-27 16:34:21', '陳建良'),
(47, 2, 'P-002-W', 10, 10, ' *PO20231227-3請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2023-12-27 16:34:21', '陳建良'),
(48, 2, 'P-002-R', 10, 10, ' *PO20231227-3請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2023-12-27 16:34:21', '陳建良'),
(49, 2, 'P-002-B', 10, 190, ' *PO20231227-3請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2024-01-18 11:16:56', '陳建良'),
(50, 2, 'P-002-Y', 10, 10, ' *PO20231227-3請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2023-12-27 16:34:21', '陳建良'),
(51, 2, 'P-004-W', 10, 10, ' *PO20231227-3請購入帳：10', NULL, NULL, '9999-12-31', '2023-12-27 16:34:21', '2023-12-27 16:34:21', '陳建良'),
(55, 6, 'P-016-L', 500, 5, '', '', '', '9999-12-31', '2023-12-28 08:34:44', '2023-12-28 08:34:44', 'susu'),
(56, 6, 'P-016-M', 500, 5, '', '', '', '9999-12-31', '2023-12-28 08:34:44', '2023-12-28 08:34:44', 'susu'),
(57, 6, 'P-016-S', 500, 5, '', '', '', '9999-12-31', '2023-12-28 08:34:44', '2023-12-28 08:34:44', 'susu'),
(58, 2, 'P-017', 10, 5, '', '', '', '0000-00-00', '2023-12-28 08:41:36', '2023-12-28 08:41:36', '陳建良'),
(59, 2, 'P-045', 10, 1, ' * 入帳 ： + 1', '', '', '9999-12-31', '2023-12-28 09:06:02', '2023-12-28 09:06:02', '陳建良'),
(60, 2, 'P-044', 10, 1, ' * 入帳 ： + 1', '', '', '9999-12-31', '2023-12-28 09:23:55', '2023-12-28 09:23:55', '陳建良'),
(61, 2, 'P-010', 10, 1, ' * 入帳 ： + 1', '', '', '9999-12-31', '2023-12-28 10:06:11', '2023-12-28 10:06:11', '陳建良'),
(62, 2, 'P-040', 10, 1, ' * 入帳 ： + 1', '', '', '9999-12-31', '2023-12-28 10:08:04', '2023-12-28 10:08:04', '陳建良'),
(63, 2, 'P-025', 5, 5, ' *PO20240104-1請購入帳：5', NULL, NULL, '9999-12-31', '2024-01-04 17:02:11', '2024-01-04 17:02:11', '陳建良'),
(64, 2, 'P-028', 5, 4, ' *PO20240109-1請購入帳：5* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-09 16:44:51', '2024-01-09 17:00:53', '沈旻頡'),
(65, 2, 'P-072', 20, 9, ' *PO20240109-1請購入帳：10* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-09 16:44:51', '2024-01-09 17:00:53', '沈旻頡'),
(66, 2, 'P-027', 20, 9, ' *PO20240109-1請購入帳：10* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-09 16:44:51', '2024-01-09 17:00:53', '沈旻頡'),
(67, 2, 'P-073-XL', 10, -2, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-11 15:32:22', '2024-01-11 15:32:22', '沈旻頡'),
(68, 3, 'P-015-OV', 50, 89, ' *PO123456788請購入帳：99', NULL, NULL, '9999-12-31', '2024-01-25 11:53:04', '2024-01-25 14:07:52', '施昱丞'),
(69, 3, 'P-015-AG', 50, 99, ' *PO123456788請購入帳：99', NULL, NULL, '9999-12-31', '2024-01-25 11:53:04', '2024-01-25 11:53:04', '施昱丞'),
(70, 3, 'P-014', 250, 0, ' *PO123456788請購入帳：99', NULL, NULL, '9999-12-31', '2024-01-25 11:53:04', '2024-01-25 14:07:52', '施昱丞'),
(71, 7, 'P-016-S', 0, -10, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:12:26', '2024-01-25 14:12:26', '陳建良'),
(72, 7, 'P-016-M', 0, -10, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:12:26', '2024-01-25 14:12:26', '陳建良'),
(73, 7, 'P-016-L', 0, -10, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:12:26', '2024-01-25 14:12:26', '陳建良'),
(74, 4, 'P-014', 50, -105, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:14:41', '2024-01-25 14:14:41', '施昱丞'),
(75, 4, 'P-015-OV', 50, -15, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:14:41', '2024-01-25 14:14:41', '施昱丞'),
(76, 4, 'P-015-D', 50, -15, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:14:41', '2024-01-25 14:14:41', '施昱丞'),
(77, 4, 'P-016-S', 50, -20, '* 發放欠額', NULL, NULL, '9999-12-31', '2024-01-25 14:14:41', '2024-01-25 14:14:41', '施昱丞');

-- --------------------------------------------------------

--
-- 資料表結構 `_supp`
--

CREATE TABLE `_supp` (
  `id` int(10) UNSIGNED NOT NULL,
  `sname` varchar(30) DEFAULT NULL COMMENT '供應商英文名稱',
  `scname` varchar(30) NOT NULL COMMENT '供應商中文名稱',
  `supp_remark` varchar(255) DEFAULT NULL COMMENT '註解說明',
  `inv_title` varchar(30) NOT NULL COMMENT '發票抬頭',
  `comp_no` varchar(20) NOT NULL COMMENT '統編',
  `_address` varchar(255) NOT NULL COMMENT '發票地址',
  `contact` varchar(20) DEFAULT NULL COMMENT '聯絡人',
  `phone` varchar(20) DEFAULT NULL COMMENT '連絡電話',
  `email` varchar(50) DEFAULT NULL COMMENT '電子郵件',
  `fax` varchar(20) DEFAULT NULL COMMENT '傳真',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_supp`
--

INSERT INTO `_supp` (`id`, `sname`, `scname`, `supp_remark`, `inv_title`, `comp_no`, `_address`, `contact`, `phone`, `email`, `fax`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'MTC-MERCURY TRADING', '明江貿易', '測試數據-明江貿易', '明江貿易股份有限公司', '04908814', '高雄市鼓山區中華一路820號12樓E棟', NULL, NULL, NULL, NULL, 'On', '2023-11-30 14:49:06', '2023-11-30 14:49:06', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_trade`
--

CREATE TABLE `_trade` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '交易單號',
  `form_type` varchar(30) NOT NULL COMMENT '表單分類',
  `out_date` datetime NOT NULL COMMENT '發貨日期',
  `item` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '批號/品項/數量',
  `out_user_id` int(10) UNSIGNED NOT NULL COMMENT '發貨人emp_id',
  `out_local` text NOT NULL COMMENT '發貨廠區',
  `in_local` int(10) UNSIGNED NOT NULL COMMENT '收貨廠區local',
  `in_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT '收貨人emp_id',
  `in_date` datetime DEFAULT NULL COMMENT '收貨日期',
  `idty` int(10) UNSIGNED NOT NULL COMMENT '交易狀態\r\n0完成/1待收/2退貨/3取消',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '表單簽核紀錄'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_trade`
--

INSERT INTO `_trade` (`id`, `form_type`, `out_date`, `item`, `out_user_id`, `out_local`, `in_local`, `in_user_id`, `in_date`, `idty`, `logs`) VALUES
(1, 'export', '2023-12-27 16:54:07', '{\"P-016-L,36\":\"5,,9999-12-31\",\"P-016-M,35\":\"5,,9999-12-31\",\"P-016-S,34\":\"5,,9999-12-31\"}', 10008048, '2', 6, 90000001, '2023-12-28 08:20:40', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-27 16:50:32\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"f1=>k9\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-27 16:50:32\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-L - 10 = 40_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-M - 10 = 40_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-S - 10 = 40\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-27 16:52:25\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"1.\\u9000\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-27 16:52:25\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-L + 10 = 50_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-M + 10 = 50_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-S + 10 = 50\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-27 16:54:07\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"1.\\u9000-\\u7de8\\u9001\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-27 16:54:07\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-L - 5 = 45_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-M - 5 = 45_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-S - 5 = 45\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:20:40\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:20:40\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-016-L + 5 = 20_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 P-016-M + 5 = 15_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 +\\u65b0 P-016-S + 5 = 5\"}]'),
(2, 'export', '2023-12-28 08:34:24', '{\"P-016-L,36\":\"5,,9999-12-31\",\"P-016-M,35\":\"5,,9999-12-31\",\"P-016-S,34\":\"5,,9999-12-31\"}', 10008048, '2', 6, 90000001, '2023-12-28 08:34:44', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:34:24\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"test2\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:34:24\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-L - 5 = 40_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-M - 5 = 40_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-S - 5 = 40\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:34:44\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:34:44\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 +\\u65b0 P-016-L + 5 = 5_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 +\\u65b0 P-016-M + 5 = 5_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 +\\u65b0 P-016-S + 5 = 5\"}]'),
(3, 'import', '2023-12-28 08:38:51', '{\"P-017,\":\"5,,\",\"P-016-L,\":\"5,,\",\"P-016-M,\":\"5,,\",\"P-016-S,\":\"5,,\"}', 10008048, 'PO20271228-t', 2, 10008048, '2023-12-28 08:41:36', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:38:51\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u8056\\u8a95\\u79ae\\u7269\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:38:51\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:41:36\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"t1\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:41:36\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-017 + 5 = 5_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-L + 5 = 45_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-M + 5 = 45_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-016-S + 5 = 45\"}]'),
(4, 'import', '2023-12-28 08:39:20', '{\"P-017,\":\"5,,\",\"P-016-L,\":\"5,,\",\"P-016-M,\":\"5,,\",\"P-016-S,\":\"5,,\"}', 10008048, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:20\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u8056\\u8a95\\u79ae\\u7269\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:20\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(5, 'import', '2023-12-28 08:39:26', '{\"P-017,\":\"5,,\",\"P-016-L,\":\"5,,\",\"P-016-M,\":\"5,,\",\"P-016-S,\":\"5,,\"}', 10008048, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:26\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u8056\\u8a95\\u79ae\\u7269\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:26\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(6, 'import', '2023-12-28 08:39:34', '{\"P-017,\":\"5,,\",\"P-016-L,\":\"5,,\",\"P-016-M,\":\"5,,\",\"P-016-S,\":\"5,,\"}', 10008048, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:34\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u8056\\u8a95\\u79ae\\u7269\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 08:39:34\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(7, 'import', '2023-12-28 08:41:01', '{\"P-016-S,\":\"1,,\",\"P-016-M,\":\"1,,\",\"P-016-L,\":\"1,,\",\"P-017,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:01\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:01\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(8, 'import', '2023-12-28 08:41:07', '{\"P-016-S,\":\"1,,\",\"P-016-M,\":\"1,,\",\"P-016-L,\":\"1,,\",\"P-017,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:07\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:07\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(9, 'import', '2023-12-28 08:41:13', '{\"P-016-S,\":\"1,,\",\"P-016-M,\":\"1,,\",\"P-016-L,\":\"1,,\",\"P-017,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:13\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:13\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(10, 'import', '2023-12-28 08:41:19', '{\"P-016-S,\":\"1,,\",\"P-016-M,\":\"1,,\",\"P-016-L,\":\"1,,\",\"P-017,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:19\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 08:41:19\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"}]'),
(11, 'import', '2023-12-28 09:05:52', '{\"P-045,\":\"1,,\"}', 10008048, 'PO20271228-t', 2, 10008048, '2023-12-28 09:06:02', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:05:52\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:05:52\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:06:02\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:06:02\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-045 + 1 = 1\"}]'),
(12, 'import', '2023-12-28 09:23:32', '{\"P-044,\":\"1,,\"}', 10008048, 'PO20271228-t', 2, 10008048, '2023-12-28 09:23:55', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:23:32\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:23:32\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:23:55\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:23:55\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-044 + 1 = 1\"}]'),
(13, 'import', '2023-12-28 09:29:30', '{\"P-010,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 2, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 09:29:30\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:30:24\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"reject\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 09:30:24\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"\"}]'),
(14, 'import', '2023-12-28 10:04:08', '{\"P-010,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, 10008048, '2023-12-28 10:06:11', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 09:29:59\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:03:37\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"ppe pm-\\u7de8\\u8f2f\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:04:08\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"ppe pm-\\u7de8\\u8f2f\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:04:08\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:06:11\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:06:11\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-010 + 1 = 1\"}]'),
(15, 'import', '2023-12-28 10:07:44', '{\"P-040,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, 10008048, '2023-12-28 10:08:04', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:07:09\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:07:24\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"ppe pm-\\u7de8\\u8f2f\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:07:44\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:08:04\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:08:04\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-040 + 1 = 1\"}]'),
(16, 'import', '2023-12-28 10:07:14', '{\"P-040,\":\"1,,\"}', 90000001, 'PO20271228-t', 2, NULL, NULL, 3, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:07:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:08:29\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:08:41\",\"action\":\"\\u4f5c\\u5ee2 (Abort)\",\"remark\":\"\"}]'),
(17, 'export', '2023-12-28 10:11:56', '{\"P-014,6\":\"10,,9999-12-31\"}', 10008048, '2', 6, NULL, NULL, 3, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:11:56\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:11:56\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 990\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:13:02\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:13:02\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 10 = 970\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:13:24\",\"action\":\"\\u4f5c\\u5ee2 (Abort)\",\"remark\":\"\"}]'),
(18, 'export', '2023-12-28 10:14:16', '{\"P-014,6\":\"10,,9999-12-31\",\"P-015-AG,15\":\"10,,9999-12-31\"}', 10008048, '2', 6, 90000001, '2023-12-28 10:14:30', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:03\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:03\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 980\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:13:39\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:13:39\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 10 = 980\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:14:16\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:14:16\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 970_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG - 10 = 990\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:14:30\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:14:30\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 + 10 = 210_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 P-015-AG + 10 = 110\"}]'),
(19, 'export', '2023-12-28 10:12:08', '{\"P-014,6\":\"10,,9999-12-31\"}', 10008048, '2', 6, 90000001, '2023-12-28 10:14:43', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:08\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:08\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 970\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:14:43\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:14:43\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 + 10 = 220\"}]'),
(20, 'export', '2023-12-28 10:16:11', '{\"P-014,6\":\"10,,9999-12-31\",\"P-015-AG,15\":\"5,,9999-12-31\"}', 10008048, '2', 6, 90000001, '2023-12-28 10:16:34', 10, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:12:14\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 960\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:15:28\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:15:28\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 10 = 980\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:16:11\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"ppe\\u7a97\\u53e3-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2023-12-28 10:16:11\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 10 = 970_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG - 5 = 985\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:16:34\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"susu (90000001)\",\"datetime\":\"2023-12-28 10:16:34\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u8cec (Account)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 + 10 = 230_rn_## K9\\u68df_\\u6536\\u767c\\u5ba4 P-015-AG + 5 = 115\"}]'),
(21, 'export', '2024-01-11 15:56:07', '{\"P-014,6\":\"100,,9999-12-31\"}', 10119798, '2', 4, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:56:07\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-11 15:56:07\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 - 100 = 1672\"}]'),
(22, 'export', '2024-01-16 08:33:32', '{\"P-014,17\":\"10,,9999-12-31\"}', 10008048, '6', 2, NULL, NULL, 2, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:33:32\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"test\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:33:32\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 - 10 = 220\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-16 09:31:50\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-16 09:31:50\",\"action\":\"\\u5eab\\u5b58-\\u56de\\u88dc (Replenish)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 + 10 = 220\"}]'),
(23, 'export', '2024-01-16 08:51:14', '{\"P-014,17\":\"10,,9999-12-31\"}', 10008048, '6', 4, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:51:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 08:51:14\",\"action\":\"\\u5eab\\u5b58-\\u6263\\u5e33 (Debit)\",\"remark\":\"## K9\\u68df_\\u6536\\u767c\\u5ba4 P-014 - 10 = 210\"}]'),
(24, 'import', '2024-01-16 09:26:21', '{\"P-016-S,\":\"10,,\"}', 10008048, 'po123456788', 2, NULL, NULL, 2, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-16 09:26:21\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPE\\u7a97\\u53e3\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-16 09:34:38\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"}]'),
(25, 'import', '2024-01-18 08:31:06', '{\"P-014,\":\"2,,\"}', 10008048, 'po123456788', 2, NULL, NULL, 1, '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-18 08:31:06\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]');

-- --------------------------------------------------------

--
-- 資料表結構 `_users`
--

CREATE TABLE `_users` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'ai',
  `emp_id` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '工號',
  `user` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '帳號',
  `cname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `fab_id` longtext CHARACTER SET utf8mb4 NOT NULL COMMENT 'fab_id',
  `sfab_id` longtext CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '複數廠區',
  `created_at` datetime DEFAULT NULL COMMENT '創建日期',
  `role` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '權限',
  `idty` varchar(2) CHARACTER SET utf8mb4 DEFAULT '1' COMMENT '身份'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `_users`
--

INSERT INTO `_users` (`id`, `emp_id`, `user`, `cname`, `fab_id`, `sfab_id`, `created_at`, `role`, `idty`) VALUES
(0, '90000000', 'admin', 'ppe管理員', '1', '12', '2022-08-17 09:39:31', '0', '1'),
(1, '90000001', 'susu', 'ppe測試01', '12', '', '2022-09-01 12:46:49', '2', '1'),
(3, '90000002', 'micro', 'ppe測試02', '0', '', '2022-12-07 15:14:40', '3', '1'),
(8, '13085117', 'dorise.cheng', '鄭羽淳', '11', '', '2023-07-12 16:53:57', '1', '1'),
(10, '10009261', 'YC.SHIH', '施昱丞', '5', '', '2023-10-17 11:20:21', '1', '1'),
(13, '10119798', 'CHIEH.SHEN', '沈旻頡', '2', '', '2023-12-11 17:11:13', '2', '1'),
(14, '10008048', 'LEONG.CHEN', '陳建良', '0', '', '2023-12-28 17:15:02', '0', '1'),
(15, '11046016', 'YA12.HSU', '徐慈雅', '0', '', '2023-12-29 08:43:48', '3', '1');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `autolog`
--
ALTER TABLE `autolog`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `checked_log`
--
ALTER TABLE `checked_log`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `pt_cata`
--
ALTER TABLE `pt_cata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `SN` (`SN`);

--
-- 資料表索引 `pt_local`
--
ALTER TABLE `pt_local`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `pt_receive`
--
ALTER TABLE `pt_receive`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `pt_stock`
--
ALTER TABLE `pt_stock`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_cata`
--
ALTER TABLE `_cata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `SN` (`SN`);

--
-- 資料表索引 `_cate`
--
ALTER TABLE `_cate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cate_no` (`cate_no`);

--
-- 資料表索引 `_contact`
--
ALTER TABLE `_contact`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_fab`
--
ALTER TABLE `_fab`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_formplan`
--
ALTER TABLE `_formplan`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_issue`
--
ALTER TABLE `_issue`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_local`
--
ALTER TABLE `_local`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_pno`
--
ALTER TABLE `_pno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_no` (`part_no`);

--
-- 資料表索引 `_receive`
--
ALTER TABLE `_receive`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_site`
--
ALTER TABLE `_site`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_stock`
--
ALTER TABLE `_stock`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_supp`
--
ALTER TABLE `_supp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `comp_no` (`comp_no`);

--
-- 資料表索引 `_trade`
--
ALTER TABLE `_trade`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_users`
--
ALTER TABLE `_users`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `autolog`
--
ALTER TABLE `autolog`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `checked_log`
--
ALTER TABLE `checked_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_cata`
--
ALTER TABLE `pt_cata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_local`
--
ALTER TABLE `pt_local`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=96;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_receive`
--
ALTER TABLE `pt_receive`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pt_stock`
--
ALTER TABLE `pt_stock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=237;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_cata`
--
ALTER TABLE `_cata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=150;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_cate`
--
ALTER TABLE `_cate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_contact`
--
ALTER TABLE `_contact`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_fab`
--
ALTER TABLE `_fab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formplan`
--
ALTER TABLE `_formplan`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_issue`
--
ALTER TABLE `_issue`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '交易單號', AUTO_INCREMENT=18;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_local`
--
ALTER TABLE `_local`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_pno`
--
ALTER TABLE `_pno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_receive`
--
ALTER TABLE `_receive`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_site`
--
ALTER TABLE `_site`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_stock`
--
ALTER TABLE `_stock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=78;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_supp`
--
ALTER TABLE `_supp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_trade`
--
ALTER TABLE `_trade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '交易單號', AUTO_INCREMENT=26;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_users`
--
ALTER TABLE `_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
