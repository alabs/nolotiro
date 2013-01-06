DELIMITER |

CREATE TABLE IF NOT EXISTS `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_speaker` int(11),
  `unread` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`last_speaker`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT |

ALTER TABLE messages ADD COLUMN thread_id int(11) NOT NULL after id |
SET foreign_key_checks = 0 |
ALTER TABLE messages ADD foreign key (thread_id) REFERENCES `threads`(`id`) ON DELETE CASCADE |
SET foreign_key_checks = 1 |

/* Estas foreign key no son necesarias para la migración pero está bien meterlas, creo */
ALTER TABLE messages MODIFY COLUMN user_from int(11) |
SET foreign_key_checks = 0 |
ALTER TABLE messages ADD FOREIGN KEY (`user_from`) REFERENCES `users`(`id`) ON DELETE SET NULL |
SET foreign_key_checks = 1 |
ALTER TABLE messages MODIFY COLUMN user_to int(11) |
SET foreign_key_checks = 0 |
ALTER TABLE messages ADD FOREIGN KEY (`user_to`) REFERENCES `users`(`id`) ON DELETE SET NULL |
SET foreign_key_checks = 1 |

DROP PROCEDURE IF EXISTS messages2threads |

CREATE PROCEDURE messages2threads()

BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE tmp_subject varchar(50);
  DECLARE tmp_last_speaker INT;
  DECLARE tmp_unread INT;
  DECLARE tmp_id INT;
  DECLARE cur CURSOR FOR SELECT id, subject, user_from, NOT readed from messages;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  OPEN cur;
  REPEAT
    FETCH cur INTO tmp_id, tmp_subject, tmp_last_speaker, tmp_unread;
    INSERT INTO threads(subject, last_speaker, unread) VALUES (tmp_subject, tmp_last_speaker, tmp_unread);
    UPDATE messages SET thread_id = LAST_INSERT_ID() where id = tmp_id;
  UNTIL done END REPEAT;
  CLOSE cur;
END |

CALL messages2threads |

ALTER TABLE messages DROP COLUMN subject |
ALTER TABLE messages DROP COLUMN readed |

