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

CREATE TABLE IF NOT EXISTS `igcse_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_on` int(11) DEFAULT NULL,
  `modified_on` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `igcse_exams`
	CHANGE COLUMN `name` `title` TEXT NULL COLLATE 'utf8_general_ci' AFTER `id`,
	ADD COLUMN `term` INT NULL DEFAULT NULL AFTER `title`,
	ADD COLUMN `year` INT NULL DEFAULT NULL AFTER `term`,
	ADD COLUMN `start_date` INT NULL DEFAULT NULL AFTER `year`,
	ADD COLUMN `end_date` INT NULL DEFAULT NULL AFTER `start_date`,
	ADD COLUMN `recording_end` INT NULL DEFAULT NULL AFTER `end_date`,
	ADD COLUMN `description` TEXT NULL AFTER `recording_end`;

ALTER TABLE `igcse_exams`
	ADD COLUMN `tid` INT(11) NULL DEFAULT NULL AFTER `id`;

ALTER TABLE `igcse_exams`
	ADD COLUMN `type` INT(11) NULL DEFAULT NULL AFTER `tid`;

CREATE TABLE IF NOT EXISTS `gs_grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_id` int(11) NOT NULL,
  `grade` text NOT NULL,
  `minimum_marks` int(11) DEFAULT NULL,
  `maximum_marks` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_on` int(11) DEFAULT NULL,
  `modified_on` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;