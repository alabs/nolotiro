DELIMITER |

SET FOREIGN_KEY_CHECKS=0 |

DROP PROCEDURE IF EXISTS fromToInThreads |

CREATE PROCEDURE fromToInThreads()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE tmp_message_id INT;
  DECLARE cur CURSOR FOR SELECT min(id) from messages group by thread_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  DECLARE CONTINUE HANDLER FOR 1060 BEGIN END;
  ALTER TABLE `threads` ADD COLUMN `user_from` int(11);
  ALTER TABLE `threads` ADD foreign key (`user_from`) REFERENCES `users`(`id`) ON DELETE SET NULL;
  ALTER TABLE `threads` ADD COLUMN `user_to` int(11);
  ALTER TABLE `threads` ADD foreign key (`user_to`) REFERENCES `users`(`id`) ON DELETE SET NULL;
  ALTER TABLE `threads` ADD COLUMN `deleted_from` int(1) NOT NULL DEFAULT '0';
  ALTER TABLE `threads` ADD COLUMN `deleted_to` int(1) NOT NULL DEFAULT '0';

  OPEN cur;
  REPEAT
    FETCH cur INTO tmp_message_id;
    UPDATE `threads` INNER JOIN `messages` ON `threads`.`id` = `messages`.`thread_id` SET `threads`.`user_from` = `messages`.`user_from` WHERE `messages`.`id` = tmp_message_id;
    UPDATE `threads` INNER JOIN `messages` ON `threads`.`id` = `messages`.`thread_id` SET `threads`.`user_to` = `messages`.`user_to` WHERE `messages`.`id` = tmp_message_id;
  UNTIL done END REPEAT;
  CLOSE cur;
END |

CALL fromToInThreads |

DROP TABLE IF EXISTS `messages_deleted` |

SET FOREIGN_KEY_CHECKS=1
