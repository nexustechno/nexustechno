ALTER TABLE `users` ADD `partnership_perc` INT NOT NULL AFTER `commission`;
UPDATE `users` SET `partnership_perc` = '100' WHERE `users`.`id` = 1;
ALTER TABLE `websites` ADD `enable_partnership` INT NOT NULL AFTER `themeClass`;

-- DONE

CREATE TABLE `users_fav_matches` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `match_id` INT NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = MyISAM;

-- DONE

ALTER TABLE `websites` ADD `currency` VARCHAR(191) NOT NULL DEFAULT 'PTH' AFTER `enable_partnership`;
ALTER TABLE `websites` ADD `admin_status` INT NOT NULL AFTER `status`;
-- DONE

ALTER TABLE `casino` ADD `casino_title` VARCHAR(255) NOT NULL AFTER `id`;
ALTER TABLE `casino_bet` ADD `roundid` INT NOT NULL AFTER `user_id`;
ALTER TABLE `casino_bet` ADD `bet_side` VARCHAR(50) NOT NULL AFTER `casino_profit`, ADD `exposureAmt` DOUBLE(30,2) NOT NULL AFTER `bet_side`, ADD `winner` VARCHAR(100) NOT NULL AFTER `exposureAmt`;
ALTER TABLE `casino_bet` CHANGE `winner` `winner` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

