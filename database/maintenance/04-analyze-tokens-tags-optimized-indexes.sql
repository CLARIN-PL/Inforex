SELECT
    table_name,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS total_mb,
    ROUND(data_length / 1024 / 1024, 2) AS data_mb,
    ROUND(index_length / 1024 / 1024, 2) AS index_mb,
    table_rows
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN ('tokens_tags_optimized', 'tokens', 'bases', 'tokens_tags_ctags')
ORDER BY (data_length + index_length) DESC;

SELECT
    index_name,
    non_unique,
    seq_in_index,
    column_name,
    cardinality
FROM information_schema.statistics
WHERE table_schema = DATABASE()
  AND table_name = 'tokens_tags_optimized'
ORDER BY index_name, seq_in_index;

SELECT
    stage,
    CASE WHEN user_id IS NULL THEN 'NULL' ELSE 'NOT NULL' END AS user_bucket,
    COUNT(*) AS row_count
FROM tokens_tags_optimized
GROUP BY stage, user_bucket
ORDER BY stage, user_bucket;

EXPLAIN
SELECT tto.base_id AS id, b.text AS base, tto.pos, COUNT(DISTINCT t.token_id) AS c, COUNT(DISTINCT r.id) AS docs
FROM reports r
STRAIGHT_JOIN tokens t ON (t.report_id = r.id)
STRAIGHT_JOIN tokens_tags_optimized tto ON (tto.token_id = t.token_id)
JOIN bases b ON (b.id = tto.base_id)
WHERE r.corpora = 107
  AND tto.user_id IS NULL
  AND tto.stage = 'tagger'
  AND tto.disamb = 1
GROUP BY tto.base_id
ORDER BY c DESC
LIMIT 0, 25;

EXPLAIN
SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id AS ctag_id, ttc.ctag, b.text AS base_text,
       tto.base_id AS base_id, tto.user_id, tok.report_id AS report_id, tok.to, tok.from
FROM tokens_tags_optimized tto
JOIN tokens tok ON tto.token_id = tok.token_id
JOIN tokens_tags_ctags AS ttc ON tto.ctag_id = ttc.id
JOIN bases AS b ON b.id = tto.base_id
WHERE tto.user_id = 1
  AND tto.stage = 'agreement'
  AND tok.report_id IN (754183);

EXPLAIN
SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id AS ctag_id, ttc.ctag, b.text AS base_text,
       tto.base_id AS base_id, tto.user_id, tok.report_id AS report_id, tok.to, tok.from
FROM tokens_tags_optimized tto
JOIN tokens tok ON tto.token_id = tok.token_id
JOIN tokens_tags_ctags AS ttc ON tto.ctag_id = ttc.id
JOIN bases AS b ON b.id = tto.base_id
WHERE tto.stage = 'final'
  AND tto.disamb = 1
  AND tok.report_id IN (754183);
