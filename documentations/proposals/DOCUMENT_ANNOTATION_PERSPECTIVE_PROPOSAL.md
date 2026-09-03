# Propozycja nowej perspektywy dokumentowej

## Cel

Nowa perspektywa raportu ma byc podobna do `topic`, ale zamiast pojedynczego `reports.type` ma pozwalac:

- przypisac do dokumentu wiele kategorii anotacyjnych,
- wybierac kategorie z hierarchii `annotation_set -> annotation_subset -> annotation_type`,
- korzystac z wiecej niz jednego setu anotacji w ramach jednego widoku.

## Wniosek architektoniczny

Obecna perspektywa `topic` nie nadaje sie do rozszerzenia 1:1:

- `engine/page/report_perspectives/PerspectiveTopic.php` tylko laduje rekordy z `reports_types`,
- `engine/ajax/ajax_report_update_topic.php` zapisuje pojedyncza wartosc do `reports.type`,
- tabela `reports` ma tylko jedno pole `type`,
- `TableReport` tez modeluje tylko jedno pole `type`.

Dlatego najczystsze rozwiazanie to nowa perspektywa z osobna tabela relacyjna dla przypisan:

- `report_id`
- `annotation_type_id`
- `user_id`
- `creation_time`

Rekomendowana nazwa tabeli:

- `reports_document_annotation_types`

Rekomendowane ograniczenie:

- `UNIQUE(report_id, annotation_type_id)`

`annotation_set_id` i `annotation_subset_id` nie musza byc trzymane w tabeli, bo wynikaja z `annotation_types`.

## Pliki do zmiany

### 1. Model danych

Zmodyfikowac:

- `database/inforex-v1.0-changelog.sql`
- `database/inforex-v1.0.sql`

Zakres:

- dodanie tabeli `reports_document_annotation_types`,
- dodanie FK do `reports`, `annotation_types`, `users`,
- dodanie indeksow po `report_id` i `annotation_type_id`,
- dodanie nowej perspektywy do `report_perspectives`, np.:
  - id: `document_annotation_categories`
  - title: `Document annotation categories`
  - description: kategoryzacja dokumentu na bazie typow anotacji.

Uwaga:

- kod ustawien korpusu pobiera liste perspektyw dynamicznie z DB, wiec zwykle wystarczy insert do `report_perspectives` i przypisanie w `corpus_and_report_perspectives`.

### 2. Backend perspektywy

Dodac:

- `engine/page/report_perspectives/PerspectiveDocument_annotation_categories.php`

Zakres:

- ladowanie struktury `set -> subset -> type` dla korpusu,
- ladowanie juz przypietych kategorii dla biezacego dokumentu,
- przygotowanie danych do renderu podobnego do `topic`, ale z checkboxami i grupami.

Najlepszy punkt reuse:

- `DbAnnotation::getAnnotationStructureByCorpora($corpus_id)`

Mozliwe rozszerzenia:

- opcjonalny filtr dozwolonych setow,
- flatten helper zwracajacy wygodniejsza strukture do templatek.

### 3. Warstwa DB / helpery

Sa 2 sensowne warianty.

Wariant preferowany:

- dodac nowy plik `engine/include/database/CDbReportDocumentAnnotation.php`

Metody:

- `getReportDocumentAnnotationTypeIds($reportId)`
- `getReportDocumentAnnotations($reportId)`
- `replaceReportDocumentAnnotationTypeIds($reportId, array $typeIds, $userId)`

Wariant minimalny:

- dopisac analogiczne metody do `engine/include/database/CDbAnnotation.php`

Preferuje osobna klase, bo to nie sa klasyczne anotacje zasiegowe z `from/to`, tylko przypisania dokumentowe.

### 4. AJAX zapisu

Dodac:

- `engine/ajax/ajax_report_update_document_annotation_categories.php`

Zakres:

- przyjecie `report_id` i listy `annotation_type_ids[]`,
- walidacja, czy wszystkie typy naleza do setow przypietych do korpusu,
- zapis w trybie `replace`,
- kontrola uprawnien analogiczna do `ajax_report_update_topic.php`:
  - `CORPUS_ROLE_EDIT_DOCUMENTS`.

### 5. Widok

Dodac:

- `engine/templates/inc_report_document_annotation_categories.tpl`

Widok powinien byc wzorowany na `inc_report_topic.tpl`, ale z roznicami:

- lewa kolumna: tresc dokumentu,
- prawa kolumna: lista setow,
- w ramach setu: sekcje subsetow,
- w ramach subsetu: checkboxy kategorii (`annotation_type`),
- mozliwosc zaznaczenia wielu kategorii jednoczesnie,
- sekcja `Selected` z podsumowaniem przypietych kategorii.

### 6. Frontend JS

Dodac:

- `public_html/js/page_report_document_annotation_categories.js`

Zakres:

- obsluga wielokrotnego wyboru,
- zapis przez AJAX po kliknieciu `Save` albo autosave po zmianie,
- odswiezanie stanu zaznaczonych kategorii bez przechodzenia do nastepnego dokumentu,
- ewentualnie przycisk `Next and save`, jesli ma dzialac podobnie do `topic`.

Do ewentualnego reuse:

- `public_html/js/c_widget_annotation_type_tree.js`
- `public_html/js/c_widget_annotation_layers_and_subsets.js`

Jesli te widgety sa zbyt mocno zwiazane z annotatorem, prostszy bedzie dedykowany JS tylko dla tego widoku.

### 7. CSS

Dodac albo rozszerzyc:

- `public_html/css/page_report_document_annotation_categories.css`

Mozna startowac od ukladu podobnego do:

- `public_html/css/page_report_topic.css`

Ale nowy widok bedzie potrzebowal dodatkowo:

- stylowania grup set/subset,
- listy zaznaczonych kategorii,
- lepszego scrolla dla dlugich list,
- wyraznego rozdzielenia wielu setow.

### 8. Testy

Dodac:

- `phpunit/tests/engine/include/database/CDbReportDocumentAnnotationTest.php`
- `phpunit/tests/engine/page/report_perspectives/PerspectiveDocumentAnnotationCategoriesTest.php`

Zakres testow:

- pobranie struktury kategorii dla korpusu,
- pobranie przypiec dla dokumentu,
- zapis `replace`,
- odrzucenie typow spoza korpusu,
- render zaznaczonych kategorii.

## Pliki, ktorych najpewniej nie trzeba ruszac

Najpewniej bez zmian:

- `engine/page/page_report.php`
- `engine/include/class/TableReport.php`
- `engine/page/corpus_perspectives/PerspectivePerspectives.php`

Powod:

- `page_report` laduje perspektywy dynamicznie po nazwie klasy,
- CSS i JS perspektywy sa dolaczane automatycznie po nazwie `subpage`,
- ustawienia korpusu czytaja perspektywy z tabel DB.

## Rekomendowany przeplyw implementacji

1. Dodac nowa tabele i wpis perspektywy w SQL.
2. Dodac helper DB do pobrania i zapisu przypiec dokumentowych.
3. Dodac `PerspectiveDocument_annotation_categories.php`.
4. Dodac template, JS i CSS.
5. Dodac testy.

## Decyzje otwarte

Przed implementacja warto ustalic 3 rzeczy:

1. Czy zapis ma byc natychmiastowy po kliknieciu checkboxa, czy dopiero po `Save`.
2. Czy wolno przypinac wiele kategorii z tego samego subsetu, czy ma obowiazywac limit `1 per subset`.
3. Czy widok ma pokazywac wszystkie sety przypiete do korpusu, czy tylko wybrane sety skonfigurowane dla tej perspektywy.

## Najblizsze istniejace pliki referencyjne

- `engine/page/report_perspectives/PerspectiveTopic.php`
- `engine/templates/inc_report_topic.tpl`
- `public_html/js/page_report_topic.js`
- `engine/ajax/ajax_report_update_topic.php`
- `engine/include/database/CDbAnnotation.php`
- `database/inforex-v1.0.sql`
