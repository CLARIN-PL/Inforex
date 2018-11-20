/*Grupy zdarzeń
event_groups
* event_group_id INT KEY AUTO_INREMENT
* name VARCHAR(64)
* description TEXT
*/
CREATE TABLE `event_groups` (
	`event_group_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 64 ) NOT NULL ,
	`description` TEXT NOT NULL
);

/*Typy zdarzeń przypisane do grup:
event_types
* event_type_id INT KEY AUTO_INREMENT
* name VARCHAR(64)
* description TEXT
* event_group_id INT
*/
CREATE TABLE `event_types` (
	`event_type_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 64 ) NOT NULL ,
	`description` TEXT NOT NULL ,
	`event_group_id` INT NOT NULL
);

/*Typy slotów przypisane do typów zdarzeń:

event_type_slots
* event_type_slot INT KEY AUTO_INCREMENT
* name VARCHAR(64)
* description TEXT
* event_type_id INT*/
CREATE TABLE `event_type_slots` (
	`event_type_slot_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 64 ) NOT NULL ,
	`description` TEXT NOT NULL ,
	`event_type_id` INT NOT NULL
);



/*Przypisanie grup zdarzeń do korpusów:
corpus_event_groups
* corpus_id INT
* event_group_id INT*/
CREATE TABLE `corpus_event_groups` (
	`corpus_id` INT NOT NULL,
	`event_group_id` INT NOT NULL
);

/*Instancje zdarzeń:

reports_events
* report_event_id INT KEY AUTO_INCREMENT
* report_id INT
* event_type_id INT
* user_id INT
* creation_time DATETIME*/
CREATE TABLE `reports_events` (
	`report_event_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`report_id` INT NOT NULL ,
	`event_type_id` INT NOT NULL ,
	`user_id` INT NOT NULL ,
	`creation_time` DATETIME NOT NULL
);


/*Sloty instancji zdarzenia:
reports_events_slots
* report_event_slot_id INT KEY AUTO_INCREMENT
* report_event_id INT
* report_annotation_id INT*/
CREATE TABLE `reports_events_slots` (
	`report_event_slot_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`report_event_id` INT NOT NULL ,
	`report_annotation_id` INT NULL,
	`event_type_slot_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`creation_time` DATETIME NOT NULL,
 	`user_update_id` INT NOT NULL,
 	`update_time` DATETIME NOT NULL	
);
