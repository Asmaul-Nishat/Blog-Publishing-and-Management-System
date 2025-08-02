-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 02, 2025 at 03:56 AM
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
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `is_hidden` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
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
(7, NULL, 'Nishat', 'soyati50@gmail.com', 'What Happend?', 'Helloooo, its works', '2025-08-02 00:03:14');

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
(1, NULL, 'World', 'world is...', '1753918802_alice-alinari-MS371wlcGPo-unsplash.jpg', NULL, 'published', '2025-07-30 23:40:02', '2025-08-02 00:00:03', 2, 'Admin'),
(7, NULL, 'heloo', 'as', '1754009958_alice-alinari-MS371wlcGPo-unsplash.jpg', 2, 'published', '2025-08-01 00:59:18', '2025-08-02 00:01:22', 1, 'Admin'),
(8, NULL, 'Looking into the heart', 'It starts with building a relationship when I need to describe myself to someone who can’t see me. Honestly, I’m not into describing myself to someone in just a few words, as my whole being', '1754010368_freestocks-OfaDD5o8hpk-unsplash.jpg', NULL, 'published', '2025-08-01 01:06:08', '2025-08-02 00:07:16', 2, 'Admin'),
(9, NULL, 'Hearth', 'The still wind serenading the hillssweet rain that drenches the window sillsan echo of joy whispering through the nighta soothing...', '1754010452_aaron-burden-xG8IQMqMITM-unsplash.jpg', NULL, 'published', '2025-08-01 01:07:32', '2025-08-02 00:00:59', 2, 'Admin'),
(10, NULL, 'The World', 'Travel is the movement of people between different locations, either for leisure or other purposes. It can be done through various modes of transportation like walking, vehicles, or airplanes. The term can also refer to the act of going on a trip, often to a distant place.', '1754010968_karsten-winegeart-Quh5YoaAzfI-unsplash.jpg', 3, 'published', '2025-08-01 01:16:08', '2025-08-02 01:49:54', 12, 'Admin'),
(11, NULL, 'Bucket List Travel: The Top 20 Places In The World!', 'If you’re like most people, the allure of new horizons and uncharted adventures keeps you constantly adding destinations to your travel bucket list. Kuoni—a Britain-based travel company— recently did a study to rank the world’s best bucket list destinations. Kuoni conducted the comprehensive analysis by scouring Google search data for 119 bucket list places to visit in 219 countries. The results paint a vivid picture of travel aspirations, spanning from the breathtaking shores of the Maldives to the thundering majesty of Niagara Falls, encompassing iconic landmarks and awe-inspiring natural wonders along the way.\r\n\r\nAccording to Sheena Paton, senior program manager, this is the first time Kuoni has assembled this travel bucket list . “We’ve previously created travel stories that have analyzed the demand for different locations or the best landmarks to visit, but this unique study looks at every country&#039;s most popular bucket list item,” Paton told me in an interview. “We were intrigued to see how this might vary from country to country and which experiences would come out on top, so we decided to do this study to reveal what travelers around the world want to experience.”\r\n\r\nmaldives top bucket list trip Coming in at the top of the world’s best bucket list destinations is the idyllic cluster of islands known as the Maldives—which is known for its stunning white sands, bright blue waters and incredible sunsets. It topped the travel bucket list for 121 countries. “It’s great to see that a trip to the Maldives is considered a bucket list item for so many people,” says Paton. “It’s a tropical paradise popular amongst honeymooners, families and retirees alike.” Coincidentally, in 2021 Kuoni looked at where the world wants to travel most. “The top destinations to travel to that year were the Maldives, Mexico and Bali, which is interesting because visiting the Maldives also came top as the most searched-for travel bucket list experience in this most recent report,” Paton explains.', '1754093541_960x0.jpg', 3, 'published', '2025-08-02 00:12:21', '2025-08-02 01:02:41', 7, 'Admin'),
(12, NULL, 'Petra, Jordan', 'this city on the edge of the Wadi Araba was quite literally carved into the rose-colored sandstone of a deep canyon.\r\n\r\nCreated by the Nabateans, Petra (“stone” in Greek) controlled trade routes stretching from Africa to India and China. The Romans later conquered the area and it was eventually abandoned and forgotten until a Swiss explorer rediscovered it in 1812.\r\n\r\nFeatured in Indiana Jones and the Last Crusade, Petra’s famous Treasury was painstakingly chiseled from sand and stone. The hike to Petra’s magnificent Monastery is another must-do.', '1754096374_The-Monastery-Petra-Jordan.jpg', 3, 'published', '2025-08-02 00:59:34', '2025-08-02 00:59:37', 1, 'Admin'),
(13, NULL, 'Victoria Falls, Zambia/Zimbabwe', 'The UNESCO World Heritage site of Victoria Falls is the largest curtain of falling water in the world. More than twice the height of Niagara Falls, it’s matched only by Iguazu Falls in South America.\r\nExplorer David Livingstone is believed to have been the first European to view Victoria Falls in 1855. Livingstone named his discovery after Queen Victoria but the indigenous name, is Mosi-oa-Tunya, literally, “the smoke that thunders.”\r\n\r\nDuring the high water season (February to May) more than 19 million cubic feet of water plummets over the edge and the falls are at their most dramatic. Bonus – it’s rainbow season! \r\n\r\nDuring the dry season from June to January, the falls often dwindle to a trickle. The shoulder season between high and low is an ideal time for viewing (I visited in February and it was fabulous).', '1754097338_pars-sahin-NMFulKCYrkY-unsplash.jpg', 3, 'published', '2025-08-02 01:15:38', '2025-08-02 01:15:45', 1, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `site_description` text DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `footer_text` text DEFAULT NULL
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
(4, 'Asmaul Nishat', 'Nishat', 'nishatsoyati@gmail.com', '$2y$10$UpBX5QPiKwfqTlZGvKkf9.ksz4.lu1j7KL20/JgF25fZ8eRdcd0Pm', 'blogger', NULL, NULL, '2025-08-01 03:00:44');

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
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_user` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
