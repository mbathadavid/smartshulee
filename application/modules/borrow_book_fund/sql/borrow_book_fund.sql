CREATE TABLE `borrow_book_fund` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`borrow_date` INT(11) NULL DEFAULT NULL,
	`student` INT(11) NULL DEFAULT NULL,
	`book` INT(11) NULL DEFAULT NULL,
	`status` INT(11) NULL DEFAULT NULL,
	`remarks` TEXT NOT NULL,
	`created_by` INT(11) NULL DEFAULT NULL,
	`modified_by` INT(11) NULL DEFAULT NULL,
	`created_on` INT(11) NULL DEFAULT NULL,
	`modified_on` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3;
