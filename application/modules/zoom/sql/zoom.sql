CREATE TABLE IF NOT EXISTS  zoom (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	description  text  , 
	time  INT(11), 
	created_by INT(11), 
	modified_by INT(11), 
	created_on INT(11) , 
	modified_on INT(11) 
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;