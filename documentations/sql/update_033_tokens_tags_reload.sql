CREATE ALGORITHM = UNDEFINED VIEW `tokens_tags` AS 
SELECT token_tag_id,token_id,b.text as base,base_id,ctag,disamb
FROM tokens_tags_optimized AS tt
LEFT JOIN bases AS b ON b.id=tt.base_id
LEFT JOIN tokens_tags_ctags ON(tt.ctag_id = tokens_tags_ctags.id);