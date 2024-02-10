DROP TABLE IF EXISTS `igcse`;
CREATE TABLE IF NOT EXISTS `igcse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_on` int(11) DEFAULT NULL,
  `modified_on` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `igcse`
	CHANGE COLUMN `name` `title` VARCHAR(256) NULL DEFAULT '' COLLATE 'utf8_general_ci' AFTER `id`,
	ADD COLUMN `term` INT NULL DEFAULT NULL AFTER `title`,
	ADD COLUMN `year` INT NULL DEFAULT NULL AFTER `term`,
	ADD COLUMN `cats_weight` INT NULL DEFAULT NULL AFTER `year`,
	ADD COLUMN `main_weight` INT NULL DEFAULT NULL AFTER `cats_weight`,
	ADD COLUMN `description` TEXT NULL AFTER `main_weight`;