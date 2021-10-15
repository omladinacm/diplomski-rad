/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `categories` VALUES (1,'Film & Animation'),
                                (2,'Autos & Vehicles'),
                                (3,'Music'),
                                (4,'Pets & Animals'),
                                (5,'Sports'),
                                (6,'Travel & Events'),
                                (7,'Gaming'),
                                (8,'People & Blogs'),
                                (9,'Comedy'),
                                (10,'Entertainment'),
                                (11,'News & Politics'),
                                (12,'How-to & Style'),
                                (13,'Education'),
                                (14,'Science & Technology'),
                                (15,'Nonprofits & Activism');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `postedBy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `videoId` int(11) DEFAULT NULL,
                            `responseToComment` int(11) DEFAULT 0,
                            `body` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `datePosted` timestamp NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dislikes` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `commentId` int(11) DEFAULT 0,
                            `videoId` int(11) DEFAULT 0,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `commentId` int(11) DEFAULT 0,
                         `videoId` int(11) DEFAULT 0,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribers` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `userTo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `userFrom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thumbnails` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `videoId` int(11) DEFAULT NULL,
                              `filePath` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `selected` int(11) DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `firstName` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `lastName` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `signUpDate` timestamp NULL DEFAULT current_timestamp(),
                         `profilePicture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `uploadedBy` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                          `privacy` tinyint(4) DEFAULT NULL,
                          `filePath` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                          `category` int(11) DEFAULT NULL,
                          `uploadDate` timestamp NULL DEFAULT current_timestamp(),
                          `views` int(11) DEFAULT 0,
                          `duration` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
