-- Statystyki aktywności użytkowników z podziałem na miesiące.
-- Pola:
-- - ym -- rok i miesiąc
-- - activity_count -- łączna liczba aktywności,
-- - corpora_count -- liczba aktywnych korpusów, tj. korpusów, które były odwiedzane w danym miesiącu,
-- - user_count -- liczba aktywnych użytkowników, tj. użytkowników, którzy wykonali jakiekolwiek akcje na korpusie.
SELECT EXTRACT(YEAR_MONTH FROM a.datetime) as ym,
  COUNT(*) AS activity_count,
  COUNT(DISTINCT a.corpus_id) AS corpora_count,
  COUNT(DISTINCT a.user_id) AS user_count
FROM activities a
WHERE a.corpus_id IS NOT NULL AND a.user_id IS NOT NULL
GROUP BY ym

-- Statystyki użytkowników spoza PWr
SELECT EXTRACT(YEAR_MONTH FROM a.datetime) as ym,
  COUNT(*) AS activity_count,
  COUNT(DISTINCT a.corpus_id) AS corpora_count,
  COUNT(DISTINCT a.user_id) AS user_count
FROM activities a
WHERE a.corpus_id IS NOT NULL AND a.user_id IS NOT NULL AND a.user_id NOT IN (1, 11, 16, 18, 40, 55, 63, 65, 70, 74 )
GROUP BY ym

