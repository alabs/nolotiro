/* this is the db initial schema */

DELIMITER |

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` date NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `token` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `locked` int(1) NOT NULL,
  `role` int(1) NOT NULL DEFAULT '0',
  `woeid` int(11) NOT NULL DEFAULT '766273',
  `lang` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |

CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_owner` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `woeid_code` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `photo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('delivered','booked','available') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'available',
  `comments_enabled` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_owner`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  KEY `woeid` (`woeid_code`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |


 CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `user_owner` int(11) NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ads_id` (`ads_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |


CREATE TABLE `commentsAdCount` (
  `id_comment` int(11) NOT NULL,
  `count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_comment`),
  KEY `idAd_comments` (`id_comment`,`count`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED |


CREATE TABLE `friends` (
  `id_user` int(11) NOT NULL,
  `id_friend` int(11) unsigned zerofill NOT NULL,
  UNIQUE KEY `iduser_idfriend` (`id_user`,`id_friend`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |


CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `user_from` int(11),
  `user_to` int(11),
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`thread_id`) REFERENCES `threads`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_from`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |

CREATE TABLE `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_speaker` int(11),
  `unread` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`last_speaker`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |

CREATE TABLE `messages_deleted` (
  `id_user` int(11) NOT NULL,
  `id_message` int(11) unsigned zerofill NOT NULL,
  UNIQUE KEY `iduser_idmessage` (`id_user`,`id_message`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED |

CREATE TABLE `readedAdCount` (
  `id_ad` int(11) NOT NULL,
  `counter` int(11) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id_ad`),
  UNIQUE KEY `id_ad_counter` (`id_ad`,`counter`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED |
