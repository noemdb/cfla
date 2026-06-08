

--
-- Estructura de tabla para la tabla `activity_log`
--

CREATE TABLE `activity_log` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `log_name` varchar(191) DEFAULT NULL,
    `description` text NOT NULL,
    `subject_type` varchar(191) DEFAULT NULL,
    `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
    `causer_type` varchar(191) DEFAULT NULL,
    `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
    `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `created_at`, `updated_at`) VALUES
(41, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:05:02', '2025-05-03 14:05:02'),
(42, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:09:04', '2025-05-03 14:09:04'),
(43, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:09:09', '2025-05-03 14:09:09'),
(44, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:09:09', '2025-05-03 14:09:09'),
(45, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:10:40', '2025-05-03 14:10:40'),
(46, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:10:47', '2025-05-03 14:10:47'),
(47, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:10:47', '2025-05-03 14:10:47'),
(48, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:15:19', '2025-05-03 14:15:19'),
(49, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:15:53', '2025-05-03 14:15:53'),
(50, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:15:53', '2025-05-03 14:15:53'),
(51, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:21:49', '2025-05-03 14:21:49'),
(52, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:21:56', '2025-05-03 14:21:56'),
(53, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:21:56', '2025-05-03 14:21:56'),
(54, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:26:50', '2025-05-03 14:26:50'),
(55, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:29:27', '2025-05-03 14:29:27'),
(56, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:29:40', '2025-05-03 14:29:40'),
(57, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:29:40', '2025-05-03 14:29:40'),
(58, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:40:46', '2025-05-03 14:40:46'),
(59, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:40:56', '2025-05-03 14:40:56'),
(60, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:40:56', '2025-05-03 14:40:56'),
(61, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:43:30', '2025-05-03 14:43:30'),
(62, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:43:37', '2025-05-03 14:43:37'),
(63, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:43:37', '2025-05-03 14:43:37'),
(64, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:47:44', '2025-05-03 14:47:44'),
(65, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:47:51', '2025-05-03 14:47:51'),
(66, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:47:52', '2025-05-03 14:47:52'),
(67, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:49:57', '2025-05-03 14:49:57'),
(68, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:50:09', '2025-05-03 14:50:09'),
(69, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 14:50:09', '2025-05-03 14:50:09'),
(70, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:10:43', '2025-05-03 15:10:43'),
(71, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:10:52', '2025-05-03 15:10:52'),
(72, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:10:52', '2025-05-03 15:10:52'),
(73, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:16:36', '2025-05-03 15:16:36'),
(74, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:16:45', '2025-05-03 15:16:45'),
(75, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:16:46', '2025-05-03 15:16:46'),
(76, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:19:06', '2025-05-03 15:19:06'),
(77, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/update\\/65\",\"method\":\"PUT\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:19:15', '2025-05-03 15:19:15'),
(78, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:19:15', '2025-05-03 15:19:15'),
(79, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:12', '2025-05-03 15:22:12'),
(80, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"home\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:16', '2025-05-03 15:22:16'),
(81, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"logout\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:22', '2025-05-03 15:22:22'),
(82, 'default', 'Acceso a ruta', NULL, NULL, NULL, NULL, '{\"route\":\"\\/\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:22', '2025-05-03 15:22:22'),
(83, 'default', 'Acceso a ruta', NULL, NULL, NULL, NULL, '{\"route\":\"login\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:38', '2025-05-03 15:22:38'),
(84, 'default', 'Acceso a ruta', NULL, NULL, NULL, NULL, '{\"route\":\"login\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:41', '2025-05-03 15:22:41'),
(85, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"home\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:42', '2025-05-03 15:22:42'),
(86, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/home\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:44', '2025-05-03 15:22:44'),
(87, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/catchments\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:47', '2025-05-03 15:22:47'),
(88, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:51', '2025-05-03 15:22:51'),
(89, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"app\\/bienestars\\/matriculations\\/interviews\\/edit\\/65\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:22:58', '2025-05-03 15:22:58'),
(90, 'default', 'Acceso a ruta', NULL, NULL, 'App\\User', 2442, '{\"route\":\"logout\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/135.0.0.0 Safari\\/537.36\"}', '2025-05-03 15:23:31', '2025-05-03 15:23:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
COMMIT;
