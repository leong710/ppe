-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-01-08 08:57:16
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
(1, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '流程測試', '{\"P-014\":{\"need\":\"10\",\"pay\":\"5\"},\"P-015-AG\":{\"need\":\"10\",\"pay\":\"5\"},\"P-015-OV\":{\"need\":\"10\",\"pay\":\"5\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:38:07\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6d41\\u7a0b\\u6e2c\\u8a66\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:38:23\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u4e3b\\u7ba1\\u540c\\u610f\"},{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:38:44\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"PM\\u540c\\u610f\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:39:19\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240104-1\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:46:36\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240104-1\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:54:30\",\"action\":\"\\u7d50\\u6848\",\"remark\":\"\"},{\"step\":\"\\u696d\\u52d9\\u627f\\u8fa6\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 15:54:30\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 5=665_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG + 5=980_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-OV + 5=995\"}]', '2024-01-04 15:38:07', '10008048', '陳建良', '2024-01-04 15:54:30', '陳建良', 10008048, 'PO20240104-1', NULL, 'PR20240104-1'),
(2, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '', '{\"P-025\":{\"need\":\"5\",\"pay\":\"5\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:25:22\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:27:22\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"Manager\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:31:04\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 10008048 \\u8f49\\u5448 10008048\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:31:15\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:31:28\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:31:59\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240104-1\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 16:55:15\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240104-1\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:02:11\",\"action\":\"\\u7d50\\u6848\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:02:11\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 +\\u65b0 P-025 + 5 = 5\"}]', '2024-01-04 16:25:22', '10008048', '陳建良', '2024-01-04 17:02:11', '陳建良', 10008048, 'PO20240104-1', NULL, 'PR20240104-1'),
(3, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"100\",\"pay\":\"100\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:12:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:12:23\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f ()\",\"datetime\":\"2024-01-04 17:22:34\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:27:12\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 11053914\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:27:22\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:29:28\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:29:53\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"\\u7cfb\\u7d71\\u7ba1\\u7406\\u54e1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-04 17:30:05\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:09:19\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240105-1\"},{\"step\":\"PPEpm\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:28:16\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240105-1\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:28:57\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\\u7533\\u8acb\\u4eba\\u540c\\u610f\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:28:57\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 100=770\"}]', '2024-01-04 17:12:14', '10008048', '陳建良', '2024-01-05 09:28:57', '陳建良', 10119798, 'PO20240105-1', NULL, 'PR20240105-1'),
(4, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '流程測試', '{\"P-014\":{\"need\":\"10\",\"pay\":\"5\"},\"P-015-AG\":{\"need\":\"10\",\"pay\":\"5\"},\"P-015-OV\":{\"need\":\"10\",\"pay\":\"5\"},\"P-015-D\":{\"need\":\"10\",\"pay\":\"5\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 08:12:57\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6d41\\u7a0b\\u6e2c\\u8a66\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-05 08:56:11\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\"\\u8f49\\u5448 \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 11053914\"},{\"step\":\"\\u8f49\\u5448\\u7c3d\\u6838\",\"cname\":\"\\u937e\\u4f73\\u742a (11053914)\",\"datetime\":\"2024-01-05 08:56:38\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"\\u9000\\u56de\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:01:27\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u9000\\u56de\\u91cd\\u9001\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-05 09:02:13\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\\u4e3b\\u7ba1\\u540c\\u610f\"},{\"step\":\"PPEpm\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:04:57\",\"action\":\"\\u9000\\u56de (Reject)\",\"remark\":\"pm\\u9000\\u56de\"},{\"step\":\"\\u7533\\u8acb\\u4eba-\\u7de8\\u8f2f\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:05:45\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u9000\\u56de\\u91cd\\u9001\"},{\"step\":\"\\u7533\\u8acb\\u4eba\\u4e3b\\u7ba1\",\"cname\":\"\\u9673\\u98db\\u826f (10010721)\",\"datetime\":\"2024-01-05 09:08:46\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:08:52\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:09:19\",\"action\":\"\\u8f49PR\",\"remark\":\"PR20240105-1\"},{\"step\":\"PPEpm\",\"cname\":\"\\u6c88\\u65fb\\u9821 (10119798)\",\"datetime\":\"2024-01-05 09:22:24\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO20240105-1\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:26:37\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\\u7533\\u8acb\\u4eba\\u540c\\u610f\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 09:26:37\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 5=670_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-AG + 5=985_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-OV + 5=1000_rn_## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-015-D + 5=1005\"}]', '2024-01-05 08:12:57', '10008048', '陳建良', '2024-01-05 09:26:37', '陳建良', 10119798, 'PO20240105-1', NULL, 'PR20240105-1'),
(5, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"999\",\"pay\":\"999\"}}', 10, '10010721', NULL, NULL, 'close', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"hrdb\\u5c0f\\u7ba1 (90000000)\",\"datetime\":\"2024-01-05 11:50:37\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:40:16\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:40:27\",\"action\":\"\\u540c\\u610f (Approve)\",\"remark\":\"\"},{\"step\":\"PR\\u958b\\u55ae\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:40:44\",\"action\":\"\\u8f49PR\",\"remark\":\"PR1112223334\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:41:14\",\"action\":\"\\u4ea4\\u8ca8 (Delivery)\",\"remark\":\"PO123456788\\uff1a(\\u8acb\\u8cfc\\u5165\\u5eab)\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:42:47\",\"action\":\"\\u9a57\\u6536\\u7d50\\u6848 (Close)\",\"remark\":\"\"},{\"step\":\"\\u7533\\u8acb\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-05 15:42:47\",\"action\":\"\\u5eab\\u5b58-\\u5165\\u5e33 (Account)\",\"remark\":\"## FAB1\\u68df_\\u5de5\\u5b89\\u5668\\u6750\\u5ba4 P-014 + 999=1769\"}]', '2024-01-05 11:50:37', '90000000', 'hrdb小管', '2024-01-05 15:42:47', '陳建良', 10008048, 'PO123456788', '2024-01-05 15:42:47', 'PR1112223334'),
(6, '南科環安處', '環安衛一部', '9T041500', 10008048, '陳建良', '5014-42117', 2, 1, '', '{\"P-014\":{\"need\":\"3\",\"pay\":\"3\"}}', 1, '10010721', '10008048', '陳建良', 'Forwarded', '[{\"step\":\"\\u586b\\u55ae\\u4eba\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-08 14:40:22\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"PPEpm\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-01-08 14:43:19\",\"action\":\"\\u8f49\\u5448 (Transmit)\",\"remark\":\" \\/\\/ \\u539f\\u5f85\\u7c3d 10010721 \\u8f49\\u5448 10008048\"}]', '2024-01-08 14:40:22', '10008048', '陳建良', '2024-01-08 14:43:19', '陳建良', NULL, NULL, NULL, NULL);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `_issue`
--
ALTER TABLE `_issue`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_issue`
--
ALTER TABLE `_issue`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '交易單號', AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
