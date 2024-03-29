CREATE TABLE IF NOT EXISTS  registration_details (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	registration_no  varchar(256)  DEFAULT '' NOT NULL, 
	date_reg  varchar(256)  DEFAULT '' NOT NULL, 
	institution_category  varchar(256)  DEFAULT '' NOT NULL, 
	institution_cluster  varchar(256)  DEFAULT '' NOT NULL, 
	county  varchar(256)  DEFAULT '' NOT NULL, 
	sub_county  varchar(256)  DEFAULT '' NOT NULL, 
	ward  varchar(256)  DEFAULT '' NOT NULL, 
	institution_type  varchar(256)  DEFAULT '' NOT NULL, 
	education_system  varchar(256)  DEFAULT '' NOT NULL, 
	education_level  varchar(256)  DEFAULT '' NOT NULL, 
	knec_code  varchar(256)  DEFAULT '' NOT NULL, 
	institution_accommodation  varchar(256)  DEFAULT '' NOT NULL, 
	scholars_gender  varchar(256)  DEFAULT '' NOT NULL, 
	locality  varchar(256)  DEFAULT '' NOT NULL, 
	kra_pin  varchar(256)  DEFAULT '' NOT NULL, 
	created_by INT(11), 
	modified_by INT(11), 
	created_on INT(11) , 
	modified_on INT(11) 
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;