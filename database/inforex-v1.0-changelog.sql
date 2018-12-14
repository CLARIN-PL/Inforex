--liquibase formatted sql

-- changeset czuk:0

ALTER TABLE  `flag_status_history` DROP FOREIGN KEY  `flag_status_history_ibfk_1` ,
ADD FOREIGN KEY (  `report_id` ) REFERENCES  `reports` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;