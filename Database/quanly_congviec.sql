-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 17, 2026 lúc 09:01 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanly_congviec`
--

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `fewgeg`
-- (See below for the actual view)
--
CREATE TABLE `fewgeg` (
`id` int(11)
,`user_id` int(11)
,`title` varchar(255)
,`description` text
,`start_date` date
,`due_date` date
,`priority` enum('Low','Medium','High')
,`status` enum('To-do','Doing','Done','Pending')
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('Tuần','Tháng','Quý') DEFAULT 'Tuần',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Đang tiến hành','Hoàn thành') DEFAULT 'Đang tiến hành'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `title`, `type`, `start_date`, `end_date`, `status`) VALUES
(2, 1, 'Đồ án chính', 'Tháng', '2026-04-25', '2026-06-30', 'Đang tiến hành'),
(3, 1, 'Nhím bím chua', 'Quý', '2026-02-15', '2026-04-30', 'Đang tiến hành'),
(4, 7, 'Đồ án môn Kỹ năng nghề', 'Tuần', '2026-03-13', '2026-05-18', 'Đang tiến hành');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'456\'', 1, '2026-05-16 12:13:56'),
(2, 3, 'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'213\'', 1, '2026-05-16 12:18:39'),
(3, 3, 'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'dừ\'', 1, '2026-05-16 12:24:35'),
(4, 3, 'CÓ VIỆC MỚI: Sếp vừa giao cho bạn việc \'fff\'', 0, '2026-05-16 12:25:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `manager_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `projects`
--

INSERT INTO `projects` (`id`, `team_id`, `name`, `description`, `start_date`, `end_date`, `manager_id`, `created_at`) VALUES
(4, 4, 'Mixi Shop', 'bán cốc mixi, áo mixi, bomber mixser', '2026-04-30', '2030-12-31', 7, '2026-05-13 19:09:34'),
(5, 5, 'Mixi cup', 'a lề a lế a lê', '2026-05-01', '2026-05-31', 1, '2026-05-13 19:13:27'),
(7, 6, 'Susan 0175', 'lol', '2026-05-02', '2026-05-31', 7, '2026-05-13 19:16:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `project_comments`
--

CREATE TABLE `project_comments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `project_comments`
--

INSERT INTO `project_comments` (`id`, `project_id`, `user_id`, `content`, `file_url`, `created_at`) VALUES
(1, 4, 1, 'dqdd', NULL, '2026-05-16 09:53:56'),
(2, 4, 1, 'alo alo', NULL, '2026-05-16 09:54:02'),
(3, 4, 1, 'em gửi file', '1778925254_task_detail.php', '2026-05-16 09:54:14'),
(4, 4, 1, 'case \'add-project-comment\':\r\n        require_once PROJECT_ROOT . \'/src/Controllers/TeamController.php\';\r\n        $chatController = new \\Tinhu\\TaskManager\\Controllers\\TeamController();\r\n        $chatController->addProjectComment();\r\n        break;', NULL, '2026-05-16 09:54:32'),
(5, 4, 3, 'nghe', NULL, '2026-05-16 09:58:28'),
(6, 4, 1, 'alo alo', NULL, '2026-05-16 10:07:24'),
(7, 4, 8, 'aloa lo', NULL, '2026-05-16 10:18:20'),
(8, 4, 1, 'oke oke', NULL, '2026-05-16 10:18:35'),
(9, 4, 8, '123', NULL, '2026-05-16 10:18:50'),
(10, 4, 1, 'a độ mixi', NULL, '2026-05-16 10:39:56'),
(11, 4, 8, 'fwvfwfhefb', NULL, '2026-05-16 10:43:28'),
(12, 4, 1, 'alo alo', NULL, '2026-05-16 11:00:17'),
(13, 4, 8, 'sdsg', NULL, '2026-05-16 11:00:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_configs`
--

CREATE TABLE `system_configs` (
  `config_key` varchar(50) NOT NULL,
  `config_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `system_configs`
--

INSERT INTO `system_configs` (`config_key`, `config_value`) VALUES
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', 'admin@taskmanager.com'),
('system_email_enabled', '1'),
('task_labels', 'Công việc, Học tập, Sức khỏe, Tài chính, Khác'),
('task_priorities', 'Low, Medium, High'),
('task_statuses', 'Backlog, To-do, Doing, Review, Pending, Done');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `status` enum('To-do','Doing','Done','Pending') DEFAULT 'To-do',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT 'Khác',
  `goal` varchar(50) DEFAULT 'Không',
  `is_reminded` tinyint(1) DEFAULT 0,
  `goal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `start_date`, `due_date`, `priority`, `status`, `created_at`, `category`, `goal`, `is_reminded`, `goal_id`) VALUES
(7, 1, 'Nguyễn Hồng Ngọc', 'gì cũng đc', '2026-04-26', '2026-05-02', 'Medium', 'Pending', '2026-04-26 10:45:38', 'Tài chính', 'Ngắn hạn', 1, 2),
(8, 1, 'Huỳnh Bá Việt Tín', 'Một tay làm tất', '2026-04-27', '2026-06-30', 'Medium', 'To-do', '2026-04-26 10:46:18', 'Công việc', 'Thói quen', 0, 2),
(9, 1, 'Dương Hồng Phát ', 'yesir', '2026-04-26', '2026-04-28', 'High', 'Pending', '2026-04-26 11:00:57', 'Tài chính', 'Dài hạn', 1, 2),
(16, 1, 'quản lý sv', 'bt ', '2026-04-01', '2026-04-27', 'Medium', 'Doing', '2026-04-28 10:47:38', 'Học tập', 'Dài hạn', 1, NULL),
(18, 1, 'Trần Minh Tân', 'Ăn nhiều vô elm', '2026-04-01', '2026-05-27', 'High', 'Done', '2026-04-28 11:56:17', 'Sức khỏe', 'Thói quen', 1, 2),
(19, 1, 'Mixi geming', 'Cảm ơn anh, anh Độ Mixi nà ná na na', '2026-04-01', '2027-01-29', 'Medium', 'Doing', '2026-04-28 12:06:03', 'Sức khỏe', 'Ngắn hạn', 1, NULL),
(21, 1, 'Nhím bím chua', 'anh tên là Nhím, anh tên là Nhím, anh tên là Nhím, anh tên là Nhím, anh tên là Nhím, anh tên là Nhím\r\n', '2026-02-01', '2026-04-30', 'Medium', 'Done', '2026-04-28 12:12:41', 'Công việc', 'Dài hạn', 0, 3),
(27, 3, 'qưe', 'qưe', '2026-04-09', '2026-04-27', 'High', 'To-do', '2026-04-28 12:39:20', 'Học tập', 'Thói quen', 1, NULL),
(30, 7, 'miximoi', 'na na na na phung thanh do', '2026-04-30', '2026-05-12', 'Medium', 'Done', '2026-05-13 16:17:30', 'Công việc', 'Không', 1, 4),
(31, 7, '123324', '3213124', '2026-05-01', '2026-05-17', 'High', 'Doing', '2026-05-13 20:17:33', 'Tài chính', 'Không', 0, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `created_by`, `created_at`) VALUES
(4, 'Team Refund', 'Nà ná na na anh Độ Mixi', 1, '2026-05-13 19:05:55'),
(5, '500Bros', 'Bomman', 1, '2026-05-13 19:10:43'),
(6, 'SBTC', 'Anh Trung Sa Đéc', 7, '2026-05-13 19:14:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_comments`
--

CREATE TABLE `team_comments` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `team_comments`
--

INSERT INTO `team_comments` (`id`, `team_id`, `user_id`, `content`, `file_url`, `created_at`) VALUES
(1, 4, 1, 'alo\r\n\\', NULL, '2026-05-16 10:33:09'),
(2, 4, 8, 'nghe', NULL, '2026-05-16 10:33:18'),
(3, 4, 8, 'alo alo', NULL, '2026-05-16 10:40:21'),
(4, 4, 1, 'hú hú ạc ạc', NULL, '2026-05-16 10:59:36'),
(5, 4, 8, 'acj acj', NULL, '2026-05-16 10:59:52'),
(6, 4, 1, 'alo', NULL, '2026-05-17 19:00:41'),
(7, 4, 4, 'hú hú', NULL, '2026-05-17 19:00:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_members`
--

CREATE TABLE `team_members` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('Leader','Member') DEFAULT 'Member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `team_members`
--

INSERT INTO `team_members` (`team_id`, `user_id`, `role`, `joined_at`) VALUES
(2, 1, 'Leader', '2026-05-13 16:26:35'),
(4, 1, 'Leader', '2026-05-13 19:05:55'),
(4, 3, 'Member', '2026-05-16 09:57:50'),
(4, 4, 'Member', '2026-05-17 19:00:23'),
(4, 7, 'Member', '2026-05-13 19:08:16'),
(4, 8, 'Member', '2026-05-16 10:17:22'),
(5, 1, 'Leader', '2026-05-13 19:10:43'),
(5, 7, 'Member', '2026-05-13 19:15:18'),
(6, 1, 'Member', '2026-05-13 19:14:23'),
(6, 7, 'Leader', '2026-05-13 19:14:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_tasks`
--

CREATE TABLE `team_tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Backlog','In Progress','Review','Done') DEFAULT 'Backlog',
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `team_tasks`
--

INSERT INTO `team_tasks` (`id`, `project_id`, `assigned_to`, `title`, `description`, `status`, `priority`, `due_date`, `created_at`) VALUES
(8, 4, 1, '123', '123', 'Done', 'High', '2026-05-30', '2026-05-13 19:11:56'),
(9, 4, 7, 'Bán cốc', '123', 'Review', 'Medium', '2030-12-31', '2026-05-13 19:20:45'),
(10, 4, 1, 'bán áo', '456', 'Backlog', 'High', '2030-12-14', '2026-05-13 19:41:47'),
(11, 5, 1, 'Bàn chuyện', 'Sắp xếp lịch', 'Review', 'High', '2026-05-31', '2026-05-13 19:46:20'),
(12, 4, 1, 'bán khô gà', 'ban khô gà', 'In Progress', 'High', '2026-05-15', '2026-05-16 09:15:06'),
(17, 4, 3, 'dadafa', '12332424', 'In Progress', 'High', '2026-05-09', '2026-05-16 12:18:39'),
(19, 4, 3, 'fff', 'qfqfqfqf', 'Review', 'Medium', NULL, '2026-05-16 12:25:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_task_comments`
--

CREATE TABLE `team_task_comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `team_task_comments`
--

INSERT INTO `team_task_comments` (`id`, `task_id`, `user_id`, `content`, `file_url`, `created_at`) VALUES
(1, 7, 1, 'lỗi rồi nè', '1778692102_Screenshot 2026-05-13 233833.png', '2026-05-13 17:08:22'),
(2, 4, 1, 'oke rồi nhá', NULL, '2026-05-13 17:57:08'),
(3, 4, 7, 'oke sếp', NULL, '2026-05-13 17:58:06'),
(4, 4, 7, 'oke oke oke oke', NULL, '2026-05-13 18:17:49'),
(5, 9, 1, 'alo a độ mixi', '1778923575_OIP.jpg', '2026-05-16 09:26:15'),
(6, 10, 8, 'alo alo', NULL, '2026-05-16 10:17:54'),
(7, 15, 1, 'alo alo', NULL, '2026-05-16 10:39:20'),
(8, 15, 1, 'dfbvwehjfbw', NULL, '2026-05-16 10:43:01'),
(9, 10, 8, '123', NULL, '2026-05-16 11:00:41'),
(10, 10, 1, '123', NULL, '2026-05-16 11:00:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `notifications` tinyint(1) DEFAULT 1,
  `language` varchar(10) DEFAULT 'vi',
  `timezone` varchar(50) DEFAULT 'Asia/Ho_Chi_Minh',
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `notify_deadline` tinyint(1) DEFAULT 1,
  `notify_alerts` tinyint(1) DEFAULT 1,
  `is_locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`, `phone`, `avatar`, `notifications`, `language`, `timezone`, `dob`, `address`, `bio`, `notify_deadline`, `notify_alerts`, `is_locked`) VALUES
(1, 'Huỳnh Bá Việt Tín', 'tinhuynhba1289@gmail.com', '$2y$10$XmclCqDSyK1/bXBmnvLYjOs9/rWt4x3lo6nLa5FY.nuF4W.UZLe/a', 'admin', '2026-04-26 09:15:25', '0868894087', '1777371925_Avata picrew.jpg', 1, 'vi', 'Asia/Ho_Chi_Minh', '2004-01-29', 'Ngõ 120, Yên Lãng, Hà Lội', 'Anh tên là Nhím, Anh tên là Nhím, Anh tên là Nhím.', 1, 1, 0),
(3, 'hbvtin-cntt17', 'hbvtin-cntt17@tdu.edu.vn', '$2y$10$/4jmgya9lAQe88g7jtqxBujVK1V8X5vv7IbMQrqGGyS6ZCzBRSln6', 'user', '2026-04-28 12:33:24', '0956789JQK', '1777379739_meomeo.jpg', 1, 'vi', 'Asia/Ho_Chi_Minh', '2010-01-27', 'Ngõ 120, Yên Lãng, Hà Lội', 'meo meo meo meo', 1, 0, 0),
(4, 'Mayday', 'maydayzzz7879@gmail.com', '$2y$10$CVVL896Svo1IG2upn8tUTOvEpfwYBeQyxQq078ctYqlH/TpF9vXeG', 'user', '2026-04-28 18:26:44', NULL, 'default.png', 1, 'vi', 'Asia/Ho_Chi_Minh', NULL, NULL, NULL, 1, 1, 0),
(6, 'Ngọc', 'nguyenhongngoc02102004@gmail.com', '$2y$10$.lEgVv1ez/g8ROsqN.zzU.GKbyeCzUem1ApFjUN9YBXevMy7ZwLAC', 'user', '2026-05-09 09:58:42', NULL, 'default.png', 1, 'vi', 'Asia/Ho_Chi_Minh', NULL, NULL, NULL, 1, 1, 0),
(7, 'Huynhbaviettinzzz', 'tinfclienquan@gmail.com', '$2y$10$b3bRbhJHNDsJXZsxCNvHueLK7jqIlHxZJPCV60fJV54L/SNyqaN5q', 'user', '2026-05-13 16:02:44', '0868894789', '1778688421_meomeo.jpg', 1, 'vi', 'Asia/Ho_Chi_Minh', '2014-01-28', 'fggsgdgsg', 'gằhtesettevhbawhfhabf', 1, 1, 0),
(8, 'baochou', 'dangngocbaochau9597@gmail.com', '$2y$10$WUt4s6cmvcN4CT6NqYIW9OnYH468LV94RgZpXz5QHOfhAHnU2xt0S', 'user', '2026-05-16 10:16:30', NULL, 'default.png', 1, 'vi', 'Asia/Ho_Chi_Minh', NULL, NULL, NULL, 1, 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `fewgeg`
--
DROP TABLE IF EXISTS `fewgeg`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY INVOKER VIEW `fewgeg`  AS SELECT `tasks`.`id` AS `id`, `tasks`.`user_id` AS `user_id`, `tasks`.`title` AS `title`, `tasks`.`description` AS `description`, `tasks`.`start_date` AS `start_date`, `tasks`.`due_date` AS `due_date`, `tasks`.`priority` AS `priority`, `tasks`.`status` AS `status`, `tasks`.`created_at` AS `created_at` FROM `tasks` ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `project_comments`
--
ALTER TABLE `project_comments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `system_configs`
--
ALTER TABLE `system_configs`
  ADD PRIMARY KEY (`config_key`);

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `team_comments`
--
ALTER TABLE `team_comments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`team_id`,`user_id`);

--
-- Chỉ mục cho bảng `team_tasks`
--
ALTER TABLE `team_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `team_task_comments`
--
ALTER TABLE `team_task_comments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `project_comments`
--
ALTER TABLE `project_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `team_comments`
--
ALTER TABLE `team_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `team_tasks`
--
ALTER TABLE `team_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `team_task_comments`
--
ALTER TABLE `team_task_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
