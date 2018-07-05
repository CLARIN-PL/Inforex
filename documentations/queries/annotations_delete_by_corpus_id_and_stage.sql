SET @corpus_id=51;
SET @stage='new';
DELETE an FROM `reports_annotations_optimized` an JOIN reports r ON (an.report_id=r.id) WHERE r.corpora=@corpus_id AND an.stage=@stage;