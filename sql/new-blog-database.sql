-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 16, 2025 at 03:13 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new-blog-database`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('view','like','share') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Food', NULL),
(2, 'Technology', NULL),
(3, 'Travel', NULL),
(9, 'Health', NULL),
(10, 'Education', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `post_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `is_hidden` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `username`, `post_id`, `parent_id`, `comment`, `is_hidden`, `created_at`) VALUES
(1, 6, 'Soyati Nishat', 15, NULL, 'hello', 0, '2025-08-05 15:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `comment_edits`
--

CREATE TABLE `comment_edits` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `old_comment` text NOT NULL,
  `new_comment` text NOT NULL,
  `edited_by` int(11) NOT NULL,
  `edited_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, NULL, 'Asmaul', 'soyati50@gmail.com', 'nothing', 'Helloooo', '2025-08-01 03:54:22'),
(2, NULL, 'Asmaul', 'soyati50@gmail.com', 'nothing', 'helooo', '2025-08-01 03:58:33'),
(3, NULL, 'Asmaul', 'nishatsoyati@gmail.com', 'nothing', 'Noooooo', '2025-08-01 04:00:02'),
(4, NULL, 'Nishat', 'soyati50@gmail.com', 'What?', 'Helloo!', '2025-08-01 04:04:55'),
(5, NULL, 'Nishat', 'nishatsoyati@gmail.com', 'What Happend?', 'Nothing! Haha', '2025-08-01 04:09:39'),
(6, NULL, 'Nishat', 'soyati50@gmail.com', 'nothing', 'Helloooooo', '2025-08-01 05:05:03'),
(7, NULL, 'Nishat', 'soyati50@gmail.com', 'What Happend?', 'Helloooo, its works', '2025-08-02 00:03:14'),
(8, NULL, 'Nishat', 'nishat@gmail.com', 'hello', 'Noooo', '2025-08-02 23:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `username`, `post_id`, `created_at`) VALUES
(56, 6, 'Soyati Nishat', 1, '2025-08-07 03:06:28'),
(57, 6, 'Soyati Nishat', 13, '2025-08-07 03:06:51'),
(59, 6, 'Soyati Nishat', 8, '2025-08-16 12:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `author` varchar(255) NOT NULL DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `image`, `category_id`, `status`, `created_at`, `updated_at`, `views`, `author`) VALUES
(1, NULL, 'World', 'world is...', '1753918802_alice-alinari-MS371wlcGPo-unsplash.jpg', NULL, 'published', '2025-07-30 23:40:02', '2025-08-07 11:39:28', 7, 'Admin'),
(8, NULL, 'Looking into the heart', 'It starts with building a relationship when I need to describe myself to someone who can’t see me. Honestly, I’m not into describing myself to someone in just a few words, as my whole being', '1754010368_freestocks-OfaDD5o8hpk-unsplash.jpg', NULL, 'published', '2025-08-01 01:06:08', '2025-08-16 12:30:21', 12, 'Admin'),
(9, NULL, 'Hearth', 'The still wind serenading the hillssweet rain that drenches the window sillsan echo of joy whispering through the nighta soothing...', '1754010452_aaron-burden-xG8IQMqMITM-unsplash.jpg', NULL, 'published', '2025-08-01 01:07:32', '2025-08-02 00:00:59', 2, 'Admin'),
(10, NULL, 'The World', 'Travel is the movement of people between different locations, either for leisure or other purposes. It can be done through various modes of transportation like walking, vehicles, or airplanes. The term can also refer to the act of going on a trip, often to a distant place.', '1754010968_karsten-winegeart-Quh5YoaAzfI-unsplash.jpg', 3, 'published', '2025-08-01 01:16:08', '2025-08-06 03:36:59', 18, 'Admin'),
(11, NULL, 'Bucket List Travel: The Top 20 Places In The World!', 'If you’re like most people, the allure of new horizons and uncharted adventures keeps you constantly adding destinations to your travel bucket list. Kuoni—a Britain-based travel company— recently did a study to rank the world’s best bucket list destinations. Kuoni conducted the comprehensive analysis by scouring Google search data for 119 bucket list places to visit in 219 countries. The results paint a vivid picture of travel aspirations, spanning from the breathtaking shores of the Maldives to the thundering majesty of Niagara Falls, encompassing iconic landmarks and awe-inspiring natural wonders along the way.\r\n\r\nAccording to Sheena Paton, senior program manager, this is the first time Kuoni has assembled this travel bucket list . “We’ve previously created travel stories that have analyzed the demand for different locations or the best landmarks to visit, but this unique study looks at every country&#039;s most popular bucket list item,” Paton told me in an interview. “We were intrigued to see how this might vary from country to country and which experiences would come out on top, so we decided to do this study to reveal what travelers around the world want to experience.”\r\n\r\nmaldives top bucket list trip Coming in at the top of the world’s best bucket list destinations is the idyllic cluster of islands known as the Maldives—which is known for its stunning white sands, bright blue waters and incredible sunsets. It topped the travel bucket list for 121 countries. “It’s great to see that a trip to the Maldives is considered a bucket list item for so many people,” says Paton. “It’s a tropical paradise popular amongst honeymooners, families and retirees alike.” Coincidentally, in 2021 Kuoni looked at where the world wants to travel most. “The top destinations to travel to that year were the Maldives, Mexico and Bali, which is interesting because visiting the Maldives also came top as the most searched-for travel bucket list experience in this most recent report,” Paton explains.', '1754093541_960x0.jpg', 3, 'published', '2025-08-02 00:12:21', '2025-08-07 03:06:18', 18, 'Admin'),
(12, NULL, 'Petra, Jordan', 'this city on the edge of the Wadi Araba was quite literally carved into the rose-colored sandstone of a deep canyon.\r\n\r\nCreated by the Nabateans, Petra (“stone” in Greek) controlled trade routes stretching from Africa to India and China. The Romans later conquered the area and it was eventually abandoned and forgotten until a Swiss explorer rediscovered it in 1812.\r\n\r\nFeatured in Indiana Jones and the Last Crusade, Petra’s famous Treasury was painstakingly chiseled from sand and stone. The hike to Petra’s magnificent Monastery is another must-do.', '1754096374_The-Monastery-Petra-Jordan.jpg', 3, 'published', '2025-08-02 00:59:34', '2025-08-07 10:50:02', 12, 'Admin'),
(13, NULL, 'Victoria Falls, Zambia/Zimbabwe!!', 'The UNESCO World Heritage site of Victoria Falls is the largest curtain of falling water in the world. More than twice the height of Niagara Falls, it’s matched only by Iguazu Falls in South America.\r\nExplorer David Livingstone is believed to have been the first European to view Victoria Falls in 1855. Livingstone named his discovery after Queen Victoria but the indigenous name, is Mosi-oa-Tunya, literally, “the smoke that thunders.”\r\n\r\nDuring the high water season (February to May) more than 19 million cubic feet of water plummets over the edge and the falls are at their most dramatic. Bonus – it’s rainbow season! \r\n\r\nDuring the dry season from June to January, the falls often dwindle to a trickle. The shoulder season between high and low is an ideal time for viewing (I visited in February and it was fabulous).', '1754097338_pars-sahin-NMFulKCYrkY-unsplash.jpg', 3, 'published', '2025-08-02 01:15:38', '2025-08-07 03:06:54', 11, 'Admin'),
(14, 6, 'Soyati', 'Nothing added', 'post_688d78c30d0d6.jpg', 1, 'published', '2025-08-02 02:32:35', '2025-08-07 10:49:18', 18, 'Admin'),
(15, NULL, 'Wonder the world', 'Element represents a section of a page whose purpose is to provide navigation links, either within the current document or to other documents. Common examples of navigation sections are menus, tables of contents, and indexes....', '1754176956_alexandra-tran-YvIXIBW6bJk-unsplash.jpg', 9, 'published', '2025-08-02 23:22:36', '2025-08-07 10:49:06', 135, 'Admin'),
(16, NULL, 'Welcome', 'Education is the ...', '1754561457_aaron-burden-xG8IQMqMITM-unsplash.jpg', 10, 'published', '2025-08-07 10:10:57', '2025-08-07 10:49:01', 41, 'Admin'),
(17, NULL, 'Switzerland', 'NATURE\r\n\r\nThe Swiss Alps are high, snow-covered mountains most of which are over 13,000 feet (4,000 meters). The most famous peak is the Matterhorn which is 14,692 feet (4,478 meters) tall, but the highest peak is Dufourspitze at 15,203 feet (4,634 meters).\r\n\r\nScientists are concerned that glaciers in the Swiss Alps have lost a lot of ice coverage in the past 40 years. This may be related to global climate change. Rapid melting of the glaciers could cause flooding to the villages below.\r\n\r\nMost animals in Switzerland live in the mountains. The ibex, a species of mountain goat, was hunted to near extinction in the early 1800s. The species has since been reintroduced and more than 15,000 ibex now live in the Swiss Alps. Hikers may also encounter chamois, another goatlike animal, and marmots. The forests of Switzerland are also home to deer, rabbits, foxes, badgers, squirrels, and many bird species.', '1755348635_4V3A4970-copy-3-1160x700.jpg', 3, 'published', '2025-08-16 12:50:35', '2025-08-16 12:50:35', 0, 'Admin'),
(18, NULL, 'Norway', 'Most of Norway has a maritime climate with mild winters and cool summers. Because of the influence of the North Atlantic Ocean, Norway has a much warmer climate than its latitudinal position would indicate.', '1755348756_annemarie-gruden-c0OckG40t2Q-unsplash.jpg', 3, 'published', '2025-08-16 12:52:36', '2025-08-16 12:52:36', 0, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `post_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `username`, `post_id`, `rating`, `created_at`) VALUES
(1, 6, 'Soyati Nishat', 15, 5, '2025-08-05 07:33:07'),
(10, 6, 'Soyati Nishat', 1, 4, '2025-08-05 09:30:30'),
(57, 6, 'Soyati Nishat', 11, 5, '2025-08-07 02:54:49'),
(68, 6, 'Soyati Nishat', 13, 4, '2025-08-07 03:07:02'),
(70, 6, 'Soyati Nishat', 14, 4, '2025-08-07 10:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('text','email','image','boolean','json') DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','blogger','reader') DEFAULT 'reader',
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `role`, `bio`, `profile_image`, `created_at`) VALUES
(6, 'Soyati Islam', 'Soyati Nishat', 'soyati50@gmail.com', '$2y$10$BqXYkUv8Sq43KQz95IdKkuqoz3CtR6.dMbXWOXtqvUrk04loq8MDu', 'admin', NULL, NULL, '2025-08-02 01:58:58'),
(8, 'Raya Islam', 'Raya', 'nishatsoyati@gmail.com', '$2y$10$bW4fLUdGYKUgTHIdnX1QTOxBASXNPaAQELvJRHdEQcCIftjIopJwi', 'blogger', NULL, NULL, '2025-08-16 12:43:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comment_edits`
--
ALTER TABLE `comment_edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `edited_by` (`edited_by`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_user` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `comment_edits`
--
ALTER TABLE `comment_edits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `analytics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_edits`
--
ALTER TABLE `comment_edits`
  ADD CONSTRAINT `comment_edits_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_edits_ibfk_2` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
