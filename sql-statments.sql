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

ALTER TABLE `users_account` ADD `casino_id` INT NOT NULL AFTER `match_id`;

ALTER TABLE `casino_bet` ADD `extra` TEXT NULL AFTER `winner`;
ALTER TABLE `casino_bet` CHANGE `team_name` `team_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `casino_bet` CHANGE `winner` `winner` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
-- DONE

CREATE TABLE`exposer_deduct_log` ( `id` INT NOT NULL , `user_id` INT NOT NULL , `action` VARCHAR(200) NOT NULL , `current_exposer` VARCHAR(200) NULL DEFAULT NULL , `new_exposer` VARCHAR(200) NULL DEFAULT NULL , `exposer_deduct` VARCHAR(200) NULL DEFAULT NULL , `match_id` VARCHAR(200) NULL DEFAULT NULL , `bet_type` VARCHAR(200) NULL DEFAULT NULL , `bet_amount` VARCHAR(200) NULL DEFAULT NULL , `odds_value` VARCHAR(200) NULL DEFAULT NULL , `odds_volume` VARCHAR(200) NULL DEFAULT NULL , `profit` VARCHAR(200) NULL DEFAULT NULL , `lose` VARCHAR(200) NULL DEFAULT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE = MyISAM;

-- DONE

ALTER TABLE `user_exposure_log` ADD INDEX(`match_id`);
ALTER TABLE `user_exposure_log` ADD INDEX(`user_id`);
ALTER TABLE `user_exposure_log` ADD INDEX(`bet_type`);
ALTER TABLE `user_exposure_log` ADD INDEX(`profit`);
ALTER TABLE `user_exposure_log` ADD INDEX(`loss`);

ALTER TABLE `match` ADD `min_premium_limit` INT NOT NULL DEFAULT '0' AFTER `max_fancy_limit`, ADD `max_premium_limit` INT NOT NULL DEFAULT '0' AFTER `min_premium_limit`;

ALTER TABLE `my_bets` ADD `market_name` VARCHAR(555) NULL DEFAULT NULL AFTER `team_name`;
ALTER TABLE `my_bets` ADD `market_id` VARCHAR(10) NOT NULL DEFAULT '0' AFTER `market_name`;
ALTER TABLE `my_bets` ADD `winner` VARCHAR(500) NULL DEFAULT NULL AFTER `result_declare`;

ALTER TABLE `match` ADD `premium` INT NOT NULL DEFAULT '1' AFTER `fancy`;
ALTER TABLE `users` ADD `premium` INT NOT NULL DEFAULT '0' AFTER `tennis`;
ALTER TABLE `users` CHANGE `premium` `premium` INT NULL DEFAULT NULL;

CREATE TABLE `dashboard` ( `id` INT NOT NULL AUTO_INCREMENT , `title` VARCHAR(255) NOT NULL , `file_name` VARCHAR(255) NOT NULL , `status` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;
ALTER TABLE `dashboard` ADD `link` VARCHAR(500) NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `dashboard` ADD `width_type` VARCHAR(50) NOT NULL AFTER `link`;
