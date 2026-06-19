SELECT 'before_drop' AS phase,
       index_name,
       seq_in_index,
       column_name,
       cardinality
FROM information_schema.statistics
WHERE table_schema = DATABASE()
  AND table_name = 'tokens_tags_optimized'
  AND index_name = 'tokens_tags_optimized_stage_user_disamb_pos_token_base_idx'
ORDER BY seq_in_index;

SET @idx_exists := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'tokens_tags_optimized'
      AND index_name = 'tokens_tags_optimized_stage_user_disamb_pos_token_base_idx'
);

SET @drop_sql := IF(
    @idx_exists > 0,
    'ALTER TABLE `tokens_tags_optimized` DROP INDEX `tokens_tags_optimized_stage_user_disamb_pos_token_base_idx`',
    'SELECT ''tokens_tags_optimized_stage_user_disamb_pos_token_base_idx already absent'' AS info'
);

PREPARE stmt FROM @drop_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'after_drop' AS phase,
       index_name,
       seq_in_index,
       column_name,
       cardinality
FROM information_schema.statistics
WHERE table_schema = DATABASE()
  AND table_name = 'tokens_tags_optimized'
  AND index_name = 'tokens_tags_optimized_stage_user_disamb_pos_token_base_idx'
ORDER BY seq_in_index;
