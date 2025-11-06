-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 06 Kas 2025, 18:12:11
-- Sunucu sürümü: 11.4.8-MariaDB-cll-lve
-- PHP Sürümü: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `muhenxju_deneme`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `page_views` int(11) DEFAULT 0,
  `unique_visitors` int(11) DEFAULT 0,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#4285F4',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `color`, `created_at`, `parent_id`, `sort_order`) VALUES
(6, 'Deneme 1', 'deneme-1', 'Deneme 1', '#4285f4', '2025-11-04 19:42:30', NULL, 1),
(19, 'Deneme 2', 'deneme-2', 'Deneme 2', '#4285f4', '2025-11-05 10:53:04', NULL, 2),
(20, 'Deneme 3', 'deneme-3', 'Deneme 3', '#4285f4', '2025-11-05 11:09:22', NULL, 3),
(21, 'Deneme 4', 'deneme-4', 'Deneme 4', '#4285f4', '2025-11-05 11:09:44', NULL, 4),
(22, 'Deneme 5', 'deneme-5', 'Deneme 5', '#4285f4', '2025-11-05 11:10:02', NULL, 5),
(24, 'Alt Deneme 1', 'alt-deneme-1', 'Alt Deneme 1', '#4285f4', '2025-11-05 11:10:40', 22, 7);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `author_website` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('approved','pending','spam') DEFAULT 'pending',
  `author_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `author_name`, `author_email`, `author_website`, `content`, `status`, `author_ip`, `user_agent`, `created_at`) VALUES
(2, 21, 'Deneme Yorum', 'deneme@dene.de', '', 'Deneme Yorum 2', 'approved', '78.172.19.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-05 15:13:18');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(1, 'deneme', 'deneme@dende.de', 'Teknik Destek', 'dednedeşş ş ş l ç ç  l ldffsd', '78.169.134.17', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'read', '2025-11-04 23:08:58');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'draft',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `like_count` int(11) DEFAULT 0,
  `comment_count` int(11) DEFAULT 0,
  `reading_time` int(11) DEFAULT 0,
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_id`, `category_id`, `status`, `meta_title`, `meta_description`, `meta_keywords`, `view_count`, `like_count`, `comment_count`, `reading_time`, `published_at`, `created_at`, `updated_at`) VALUES
(4, '🌿 De Veritate Temporis', 'de-veritate-temporis', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sit amet libero eu sapien egestas finibus. Morbi quis fermentum elit. Integer vel tellus a nibh luctus aliquet. Donec tincidunt eros sed massa laoreet, nec tincidunt ex fermentum. Cras mattis felis eu dictum tincidunt. Vivamus efficitur nisl ac neque finibus, eget facilisis mi luctus. Suspendisse vel justo quis neque posuere faucibus. Nulla ornare diam nec lorem tristique, vitae blandit urna gravida.</p><p>Sed cursus sapien sed mi suscipit gravida. Phasellus dignissim, elit nec feugiat imperdiet, metus justo volutpat justo, vitae convallis augue purus sit amet nulla. Mauris a commodo libero. Quisque tempor, lacus at dignissim cursus, odio nunc porta justo, ac luctus augue neque id erat.</p>', '🌿 De Veritate Temporis', 'uploads/featured/690b798047541_1762359680.webp', 1, 6, 'published', '🌿 De Veritate Temporis', '🌿 De Veritate Temporis', '', 26, 0, 0, 1, '2025-11-04 20:11:22', '2025-11-04 20:11:22', '2025-11-06 14:24:19'),
(5, '✨ Tempus Fugit, Verba Manent', 'tempus-fugit-verba-manent', '<p>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris convallis feugiat arcu, at vulputate arcu euismod vitae. Suspendisse nec diam ac massa faucibus lacinia. Donec feugiat augue ac orci consequat cursus. Etiam eget erat at arcu fermentum pulvinar. Proin a dui justo. Integer vitae nunc metus.</p><p>Aliquam erat volutpat. Ut gravida orci quis nisi commodo, eget congue nisl sagittis. Phasellus commodo ante sed suscipit iaculis. Fusce eget massa blandit, convallis lacus ut, tristique elit. In pretium felis in felis vulputate luctus. Sed ultricies, lorem non gravida pharetra, nulla sapien tempus purus, sit amet vestibulum arcu ipsum nec odio.</p>', '✨ Tempus Fugit, Verba Manent', 'uploads/featured/690a622476212_1762288164.jpg', 1, 6, 'published', '✨ Tempus Fugit, Verba Manent', '✨ Tempus Fugit, Verba Manent', '', 30, 0, 1, 1, '2025-11-04 20:29:24', '2025-11-04 20:29:24', '2025-11-05 16:13:47'),
(6, '🌌 In Silentio Veritas', 'in-silentio-veritas', '<p>Proin sed nunc eu nisl elementum pretium. Nam venenatis, turpis a tincidunt posuere, elit nunc egestas sapien, in aliquam ligula elit ac libero. Curabitur sed cursus felis. Suspendisse ut lacus vel augue feugiat hendrerit nec ac nisi. In id tincidunt ex, vitae laoreet enim. Etiam volutpat, elit et ultricies tincidunt, augue odio commodo ligula, in luctus eros neque nec erat.</p><p>Nunc viverra, erat eget iaculis luctus, risus enim interdum est, non viverra nisi justo nec magna. Curabitur vitae fringilla odio. Nulla euismod felis nec lorem finibus, eget posuere orci accumsan. Maecenas nec felis ut metus bibendum ultricies. Duis ultricies tempor risus, non accumsan sapien rhoncus nec. Vivamus dignissim est vitae mauris viverra, ut sagittis lacus imperdiet.</p>', '🌌 In Silentio Veritas', 'uploads/featured/690b79a8c8225_1762359720.webp', 1, 6, 'published', '🌌 In Silentio Veritas', '🌌 In Silentio Veritas', '', 27, 0, 0, 1, '2025-11-04 21:44:30', '2025-11-04 21:44:30', '2025-11-05 17:43:28'),
(7, '🔮 Mens Temporum – Zamanın Aklı', 'mens-temporum-zamanin-akli', '<p>Curabitur id risus lectus. Etiam porttitor dui sed lacus ultricies, vitae vehicula sapien facilisis. Mauris euismod, purus non facilisis volutpat, nunc justo facilisis est, a efficitur arcu lacus vitae lectus. Aenean imperdiet tortor non libero venenatis, id laoreet nisi volutpat. Nulla facilisi.</p><p>Suspendisse potenti. Duis id felis in elit interdum porttitor. Integer ultricies ex sit amet sem finibus, vitae vulputate metus vehicula. Nam porta bibendum nibh ac eleifend. Sed nec mauris ligula. Praesent convallis sapien in feugiat maximus. Curabitur vel egestas justo.</p>', '🔮 Mens Temporum – Zamanın Aklı', 'uploads/featured/690a804a8c83f_1762295882.webp', 1, 6, 'published', '🔮 Mens Temporum – Zamanın Aklı', '🔮 Mens Temporum – Zamanın Aklı', '', 18, 0, 0, 1, '2025-11-04 22:37:53', '2025-11-04 22:37:53', '2025-11-05 19:10:36'),
(8, '🕊️ Ad Astra – Yıldızlara Doğru', 'ad-astra-yildizlara-dogru', '<p>Fusce sit amet sapien eget sem tincidunt feugiat. Sed sodales justo vel felis aliquam, eget egestas enim tristique. Proin tempor lectus ut justo blandit cursus. Pellentesque consequat lectus vitae mi ultricies malesuada. Quisque a mauris id nulla ultrices fermentum.</p><p>Nullam pulvinar magna sed metus vehicula, a pretium felis porta. Aenean laoreet enim ut felis elementum, at dignissim lorem dictum. Cras fermentum nulla ac sapien pharetra, sit amet sodales nisl egestas. Ut imperdiet, erat a fringilla mattis, sem justo faucibus nisi, eget suscipit elit neque ut justo.</p><p>Vivamus id libero nec lacus maximus viverra. Integer feugiat augue quis orci cursus, et malesuada lorem iaculis. Pellentesque lacinia mi ac sodales egestas. Suspendisse sed neque non sapien porta consequat.</p>', '🕊️ Ad Astra – Yıldızlara Doğru', 'uploads/featured/690b79cc71d2f_1762359756.webp', 1, 6, 'published', '🕊️ Ad Astra – Yıldızlara Doğru', '🕊️ Ad Astra – Yıldızlara Doğru', '', 18, 0, 0, 1, '2025-11-04 23:29:05', '2025-11-04 23:29:05', '2025-11-05 18:52:40'),
(9, '⚙️ Memoria Mechanica', 'memoria-mechanica', '<p>Ut sed dui lorem. Phasellus ut nunc arcu. Donec tincidunt ex sed eros faucibus, sit amet elementum justo accumsan. Maecenas suscipit diam a interdum fermentum. Aliquam quis erat laoreet, cursus odio vel, aliquam mauris.</p><p>Curabitur sed lectus in leo scelerisque vehicula. In dictum justo quis velit ultricies, at ultricies nibh feugiat. Integer blandit justo id sem posuere, sed dapibus nulla sodales.</p><p>Suspendisse vel tortor accumsan, finibus justo nec, tincidunt nunc. In blandit justo vel dolor condimentum, at tincidunt tortor tempor. Donec non dictum ipsum, a tempor felis.</p>', '⚙️ Memoria Mechanica', 'uploads/featured/690b79e2e0e94_1762359778.webp', 1, 6, 'published', '⚙️ Memoria Mechanica', '⚙️ Memoria Mechanica', '', 1, 0, 0, 1, '2025-11-05 08:56:17', '2025-11-05 08:56:17', '2025-11-05 17:14:33'),
(10, '🌁 Civitas Nova – Yeni Şehrin Kalbi', 'civitas-nova-yeni-sehrin-kalbi', '<p>Ut vitae orci in justo posuere consequat. Suspendisse blandit nisi ut nulla pretium, sed euismod lectus commodo. Pellentesque suscipit sapien a tortor imperdiet, eget accumsan eros tincidunt. Phasellus a fermentum nulla. Sed euismod feugiat odio, quis lacinia mauris cursus in.</p><p> Curabitur ultricies, justo sed efficitur dictum, tellus tortor viverra est, sit amet varius orci velit nec ligula.</p><p>Morbi ornare, risus in posuere vehicula, mi justo congue velit, sed laoreet lectus turpis ac magna. In at luctus mauris, vitae tincidunt metus. Vivamus ac tortor erat. Mauris dictum fringilla diam, eget feugiat lorem tincidunt nec. Donec mattis, justo at laoreet bibendum, magna justo posuere lectus, ut viverra nulla mauris in ex.</p>', '🌁 Civitas Nova – Yeni Şehrin Kalbi', 'uploads/featured/690b79f17990a_1762359793.webp', 1, 6, 'published', '🌁 Civitas Nova – Yeni Şehrin Kalbi', '🌁 Civitas Nova – Yeni Şehrin Kalbi', '', 3, 0, 0, 1, '2025-11-05 08:57:13', '2025-11-05 08:57:13', '2025-11-05 16:23:13'),
(11, '🌹 Speculum Temporis – Zamanın Aynası', 'speculum-temporis-zamanin-aynasi', '<p>Sed imperdiet libero quis risus rhoncus dictum. Integer quis magna eget nulla cursus posuere nec non sem. Vestibulum malesuada, eros sed egestas tincidunt, metus ligula sollicitudin metus, in suscipit ex justo id magna.</p><p> Praesent feugiat sem vitae nibh pretium tincidunt.</p><p>Aliquam at malesuada enim. Aenean laoreet pulvinar arcu, nec tincidunt nibh dapibus ac. Sed sed justo lorem. Nunc non nulla non justo sodales hendrerit vitae ac elit. Cras vel lacinia velit, ut viverra orci. Phasellus nec sapien non libero rhoncus luctus.</p><p>Suspendisse ut fringilla urna. Quisque feugiat, purus eget faucibus vestibulum, augue justo egestas magna, at bibendum justo nibh at magna. Donec sodales orci non arcu hendrerit condimentum. Mauris vehicula massa a sem varius ultricies.</p>', '🌹 Speculum Temporis – Zamanın Aynası', 'uploads/featured/690b11a6a5b58_1762333094.webp', 1, 6, 'published', '🌹 Speculum Temporis – Zamanın Aynası', '🌹 Speculum Temporis – Zamanın Aynası', '', 3, 0, 0, 1, '2025-11-05 08:58:14', '2025-11-05 08:58:14', '2025-11-06 15:04:15'),
(12, '🌒 Post Tenebras Lux – Karanlıktan Sonra Işık', 'post-tenebras-lux-karanliktan-sonra-isik', '<p>Cras euismod, urna ut euismod imperdiet, elit lectus pulvinar mi, a laoreet lectus sapien nec lacus.</p><p> Pellentesque porttitor justo nec nisi euismod tristique. Donec fermentum sed libero id vestibulum. Nulla id nulla ac orci volutpat iaculis vel sed orci.</p><p>Etiam eget odio diam. In congue magna id luctus efficitur. Integer dignissim pretium tortor, sit amet commodo leo iaculis vitae. Phasellus aliquet sapien nec commodo tincidunt. Sed sed leo ac eros tincidunt porta.</p><p>Curabitur dignissim felis in tempor suscipit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nullam id libero non sem vehicula tempus. Aenean egestas vel mi vel tempor. Nam non viverra mi.</p>', '🌒 Post Tenebras Lux – Karanlıktan Sonra Işık', 'uploads/featured/690b7a062615b_1762359814.webp', 1, 6, 'published', '🌒 Post Tenebras Lux – Karanlıktan Sonra Işık', '🌒 Post Tenebras Lux – Karanlıktan Sonra Işık', '', 2, 0, 0, 1, '2025-11-05 08:59:05', '2025-11-05 08:59:05', '2025-11-05 16:23:34'),
(13, '⚡ Aurora Mundi – Dünyanın Şafağı', 'aurora-mundi-dunyanin-safagi', '<p>Vivamus accumsan eros ut ex interdum, vitae faucibus magna tincidunt. Nulla facilisi. Cras fermentum, turpis quis varius suscipit, mauris augue aliquam sapien, vel blandit elit velit sed enim.</p><p> Integer dignissim nisl at erat cursus, ut pulvinar justo faucibus. Proin ut ante nec urna pretium eleifend. Nulla condimentum, nisi sed tincidunt euismod, purus justo gravida ante, sed iaculis magna elit ut libero.</p><p>Suspendisse potenti. Ut euismod nulla et diam bibendum, ac tempus risus fermentum. Pellentesque finibus, tortor a pulvinar vestibulum, nibh eros dictum magna, nec tempor justo mauris in libero. Mauris facilisis blandit tortor, sit amet ultricies lectus vestibulum in.</p>', '⚡ Aurora Mundi – Dünyanın Şafağı', 'uploads/featured/690b7a24cb649_1762359844.webp', 1, 6, 'published', '⚡ Aurora Mundi – Dünyanın Şafağı', '⚡ Aurora Mundi – Dünyanın Şafağı', '', 8, 0, 0, 1, '2025-11-05 09:00:02', '2025-11-05 09:00:02', '2025-11-05 19:28:30'),
(14, '🌍 Homo Digitalis – Dijital İnsan Çağı', 'homo-digitalis-dijital-insan-cagi', '<p>Donec egestas sapien non suscipit volutpat. Sed gravida, purus nec tincidunt volutpat, velit erat eleifend nulla, id posuere lorem risus sit amet arcu. Integer nec dictum elit.</p><p> Sed sagittis euismod ipsum, sed dignissim risus luctus at. Maecenas eleifend justo sed sapien bibendum, non mattis lorem imperdiet.</p><p>Suspendisse dignissim mauris eu libero sodales, in bibendum orci bibendum. Vivamus malesuada vehicula nisi, sit amet euismod metus dignissim id.</p><p> Curabitur dapibus, libero ac ultricies imperdiet, arcu ligula tincidunt enim, at feugiat lectus sem ac mauris.</p><p>Nam auctor sagittis nisi nec elementum. Proin ultricies pulvinar mauris ac sodales. Morbi in fringilla erat. Ut suscipit elit vel commodo placerat.</p>', '🌍 Homo Digitalis – Dijital İnsan Çağı', 'uploads/featured/690b7a38c6297_1762359864.webp', 1, 6, 'published', '🌍 Homo Digitalis – Dijital İnsan Çağı', '🌍 Homo Digitalis – Dijital İnsan Çağı', '', 4, 0, 0, 1, '2025-11-05 09:13:45', '2025-11-05 09:13:45', '2025-11-05 17:35:46'),
(15, '🔥 Res Nova – Yeniden Doğuşun Dalgası', 'res-nova-yeniden-dogusun-dalgasi', '<p>Praesent sit amet arcu non ex finibus iaculis. Aliquam luctus enim at facilisis faucibus. Curabitur faucibus euismod lorem, ac suscipit erat porttitor et. Vestibulum tincidunt, libero in pharetra faucibus, est leo laoreet mi, in sagittis libero lectus et magna.</p><p>Vivamus tempor, metus eget maximus iaculis, libero leo suscipit est, ut faucibus odio neque eget lorem. Maecenas porttitor porttitor metus nec blandit. Nulla facilisi. Sed viverra diam et justo fringilla, ut tristique arcu vulputate.</p><p>Ut tempus erat at metus lacinia, in mattis lacus pretium. Pellentesque ullamcorper enim vitae sapien tempor, vitae ultrices odio sagittis. Etiam eu tortor leo. Suspendisse nec justo risus.</p><p> Integer ac sapien id velit facilisis dapibus. Donec sit amet magna in sem vehicula efficitur.</p>', '🔥 Res Nova – Yeniden Doğuşun Dalgası', 'uploads/featured/690b7a485e466_1762359880.webp', 1, 6, 'published', '🔥 Res Nova – Yeniden Doğuşun Dalgası', '🔥 Res Nova – Yeniden Doğuşun Dalgası', '', 2, 0, 0, 1, '2025-11-05 09:18:43', '2025-11-05 09:18:43', '2025-11-05 19:10:46'),
(16, '🌞 Anima Nova – Yeni Ruhun Uyanışı', 'anima-nova-yeni-ruhun-uyanisi', '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p><p> Aenean gravida, sapien ac lacinia rhoncus, nibh libero condimentum odio, et euismod sem turpis id est. Sed ac dictum eros. Suspendisse potenti.</p><p>Nam id turpis sed ex posuere fringilla. Nullam et nisi ut odio euismod porttitor.</p><p> Sed pharetra convallis lorem, vitae viverra nunc euismod ac. Integer feugiat sapien nec diam tincidunt, ut sagittis risus viverra.</p><p> Curabitur commodo massa in justo varius lacinia. Phasellus ac eros sit amet felis dapibus malesuada.</p><p>Donec non dignissim nisi. Aenean vel risus a nulla mattis elementum.</p><p> Nullam ut neque in ex vehicula convallis sed at lacus. Aliquam erat volutpat. Sed ut est et arcu pretium interdum at at orci.</p>', '🌞 Anima Nova – Yeni Ruhun Uyanışı', 'uploads/featured/690b16a26e89f_1762334370.webp', 1, 6, 'published', '🌞 Anima Nova – Yeni Ruhun Uyanışı', '🌞 Anima Nova – Yeni Ruhun Uyanışı', '', 12, 0, 0, 1, '2025-11-05 09:19:30', '2025-11-05 09:19:30', '2025-11-06 14:19:13'),
(17, '🕯 Verba Ventis – Rüzgârla Gelen Sözler', 'verba-ventis-ruzgrla-gelen-sozler', '<p>Vivamus sodales est in nulla lacinia, nec congue tortor sagittis.</p><p> Sed dignissim, eros sit amet cursus cursus, lacus augue scelerisque augue, quis viverra sapien tortor sit amet lorem. Etiam varius elit in turpis aliquet, ut tincidunt nisi luctus.</p><p>Proin malesuada sem at augue sollicitudin cursus. Curabitur egestas arcu ac purus ultrices, nec tincidunt sem vulputate.</p><p> Suspendisse consequat, nisl nec laoreet tincidunt, lectus erat aliquam lacus, vitae laoreet urna libero sit amet orci.</p><p>Mauris in mi quis velit rhoncus condimentum. Nam tincidunt, tortor ut hendrerit malesuada, est arcu malesuada enim, sit amet viverra lectus eros eget velit.</p><p> In et sapien eget lectus tristique dictum sit amet vel magna.</p>', '🕯 Verba Ventis – Rüzgârla Gelen Sözler', 'uploads/featured/690b16e01665a_1762334432.webp', 1, 6, 'published', '🕯 Verba Ventis – Rüzgârla Gelen Sözler', '🕯 Verba Ventis – Rüzgârla Gelen Sözler', '', 21, 0, 0, 1, '2025-11-05 09:20:32', '2025-11-05 09:20:32', '2025-11-06 15:00:51'),
(21, '⚜️ Finis Initium Est – Son, Başlangıcın Kendisidir', 'finis-initium-est-son-baslangicin-kendisidir', '<p>Nulla finem est ubi vis manet. (Hiçbir şey bitmez, istek yaşadıkça.)</p><p> Sed tempus, sicut flumen, omnia portat — ama bazı şeyleri geriye bırakmaz.</p><p>Ut inceptos hymenaeos!</p><p> Vivamus in novam aetatem, ubi sermo fit lumen et lumen fit actio.</p><p> Nam futurum non venit ex nihilo — futurum nascitur ex audacia.</p><p>Aenean eleifend nunc vel justo posuere, nec tempus neque cursus. Sed vitae purus id tortor aliquam tincidunt.</p><p> Etiam dictum magna sit amet neque faucibus, vel accumsan erat sodales.</p><p> Aliquam erat volutpat. Nulla in sapien ut nunc cursus tempus.</p><p>Suspendisse viverra, nunc a pretium accumsan, enim eros dictum justo, eget blandit metus neque vel libero.</p>', '⚜️ Finis Initium Est – Son, Başlangıcın Kendisidir', 'uploads/featured/690b1b91cea7f_1762335633.webp', 1, 6, 'published', '⚜️ Finis Initium Est – Son, Başlangıcın Kendisidir', '⚜️ Finis Initium Est – Son, Başlangıcın Kendisidir', '', 23, 0, 1, 1, '2025-11-05 09:40:06', '2025-11-05 09:40:06', '2025-11-06 14:28:51'),
(25, 'Merhaba Dünya!', 'merhaba-dunya', '<h1>Merhaba Dünya!</h1><p><br></p><h2>Merhaba Dünya!</h2><p><br></p><p>Merhaba Dünya!</p>', 'Merhaba Dünya!', 'uploads/featured/690cb86e9ef10_1762441326.webp', 1, 6, 'published', 'Merhaba Dünya!', 'Merhaba Dünya!', '', 1, 0, 0, 1, '2025-11-06 15:02:06', '2025-11-06 15:02:06', '2025-11-06 15:02:23'),
(26, 'Harika Dünyalar!', 'harika-dunyalar', '<h1>Harika Dünyalar!</h1><p><br></p><h2>Harika Dünyalar!</h2><p><br></p><h3>Harika Dünyalar!</h3><p><br></p><p>Harika Dünyalar!</p>', 'Harika Dünyalar!', 'uploads/featured/690cb8e256943_1762441442.webp', 1, 22, 'published', 'Harika Dünyalar!', 'Harika Dünyalar!', '', 7, 0, 0, 1, '2025-11-06 15:03:46', '2025-11-06 15:03:46', '2025-11-06 15:08:53');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `post_tags`
--

CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `post_tags`
--

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(4, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(21, 1),
(25, 1),
(26, 1),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(25, 2),
(26, 2),
(11, 3),
(13, 3),
(14, 3),
(15, 3),
(25, 3),
(26, 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','boolean','integer','array') DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `created_at`) VALUES
(1, 'site_name', 'Memur Blog', 'string', '2025-11-04 19:06:32'),
(2, 'site_description', 'Modern Blog Sistemi', 'string', '2025-11-04 19:06:32'),
(3, 'site_keywords', 'blog, teknoloji, yazılım', 'string', '2025-11-04 19:06:32'),
(4, 'posts_per_page', '10', 'integer', '2025-11-04 19:06:32'),
(5, 'comment_auto_approve', '0', 'boolean', '2025-11-04 19:06:32'),
(6, 'maintenance_mode', '0', 'boolean', '2025-11-04 19:06:32');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `count`, `created_at`) VALUES
(1, 'Deneme', 'deneme', 16, '2025-11-04 19:42:11'),
(2, 'Selam', 'selam', 11, '2025-11-05 08:44:52'),
(3, 'Merhaba', 'merhaba', 6, '2025-11-05 11:29:32');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor','author') DEFAULT 'author',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `display_name`, `role`, `avatar`, `bio`, `created_at`) VALUES
(1, 'admin', 'admin@memur.info', '$2y$10$j.33hFRkCf.90QGRExVcfOpBSOBRlmwwGfvJU3mCkc2mFppsAaXHy', 'Admin User', 'admin', NULL, NULL, '2025-11-04 19:06:32');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Tablo için indeksler `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- Tablo için indeksler `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Tablo için indeksler `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Tablo kısıtlamaları `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
