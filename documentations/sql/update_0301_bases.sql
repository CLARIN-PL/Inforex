CREATE TABLE `bases` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`text` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `text` (`text`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB;

INSERT IGNORE INTO bases (text) 
SELECT base FROM tokens_tags GROUP BY HEX(base);

ALTER TABLE `tokens_tags`
	ADD COLUMN `base_id` BIGINT(20) NOT NULL AFTER `token_id`;

SET @count = 0;
UPDATE `bases` SET `bases`.`id` = @count:= @count + 1;
ALTER TABLE bases AUTO_INCREMENT = 1;
UPDATE tokens_tags SET base_id = (SELECT id FROM bases WHERE text=tokens_tags.base COLLATE utf8_bin) WHERE base_id = 0;

RENAME TABLE `tokens_tags` TO `tokens_tags_optimized`;
ALTER TABLE `tokens_tags_optimized`
	ADD CONSTRAINT `FK_tokens_tags_optimized_bases` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;
	
ALTER TABLE `tokens_tags_optimized`
	DROP COLUMN `base`;	
	
CREATE ALGORITHM = UNDEFINED VIEW `tokens_tags` AS 
    SELECT token_tag_id,token_id,b.text as base,base_id,ctag,disamb
    FROM tokens_tags_optimized AS tt
    LEFT JOIN bases AS b ON b.id=tt.base_id;
    
DELIMITER $$
CREATE PROCEDURE `bases_delete_unused_bases`()
	LANGUAGE SQL
	NOT DETERMINISTIC
	MODIFIES SQL DATA
	SQL SECURITY INVOKER
	COMMENT ''
BEGIN
	DROP TABLE IF EXISTS bases_temp;
	
	CREATE TEMPORARY TABLE bases_temp (	`base_id` BIGINT NOT NULL,
		PRIMARY KEY (`base_id`)) ENGINE=MEMORY;
	
	INSERT INTO bases_temp (base_id)
		SELECT base_id
		FROM tokens_tags_optimized
		GROUP BY base_id;
		
	DELETE
	FROM bases
	WHERE id NOT IN (SELECT * FROM bases_temp);
	DROP TABLE bases_temp;
END$$
DELIMITER $$
