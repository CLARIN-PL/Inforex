# Integracja Inforex -> Korpuskop

Ten dokument opisuje produkcyjne podłączenie `Korpuskop` do aplikacji `Inforex`.

Integracja jest celowo zawężona: na wejściu do `Korpuskop` dopuszczamy wyłącznie pliki wyeksportowane z `Inforex` w formacie `.parquet.zst`.

## 1. Instalacja Korpuskop

Na serwerze docelowym przygotuj katalog, na przykład:

```text
/opt/korpuskop/
  bin/korpuskop
  dics/
  config/
  var/progress/
  var/output/
```

Z projektu `korpuskop` skopiuj:

- `target/release/korpuskop` -> `/opt/korpuskop/bin/korpuskop`
- katalog `dics/`
- wybrane pliki `config/*.json`

Budowa binarki:

```bash
cargo build --release
```

Pomocnicze pliki wdrożeniowe dodane do `Inforex`:

- `documentations/integrations/install_korpuskop_runtime.sh`
- `documentations/integrations/korpuskop-runtime.conf.example`
- `documentations/integrations/korpuskop-db.sql`
- `documentations/integrations/install_korpuskop_runtime_debian.sh`
- `documentations/integrations/korpuskop-db.sql`

## 2. Zalecana konfiguracja serwera WWW

Do szybkiego przygotowania serwera Debian/Ubuntu możesz użyć helpera:

```bash
bash documentations/integrations/install_korpuskop_runtime_debian.sh
```


Jeśli `Inforex` działa jako aplikacja WWW, rekomendowany jest taki model:

- `Korpuskop` jest osobną binarką, ale stanowi część tego samego środowiska serwera,
- proces PHP / worker webowy ma dostęp do `/opt/korpuskop`,
- katalogi `var/progress` i `var/output` są zapisywalne przez użytkownika serwera WWW,
- na serwerze są dostępne zależności runtime:
  - `zstd`
  - `python3`
  - `pyarrow`

Najbezpieczniejsza konfiguracja uprawnień:

- właściciel: `www-data:www-data` albo użytkownik, pod którym działa PHP-FPM / Apache,
- katalogi:
  - `/opt/korpuskop/var/progress`
  - `/opt/korpuskop/var/output`
  muszą mieć prawo zapisu,
- sama binarka może być tylko do odczytu i wykonywania.

## 3. Konfiguracja Inforex

Do `config/config.local.php` dodaj:

```php
Config::Cfg()->put_korpuskopBinary('/opt/korpuskop/bin/korpuskop');
Config::Cfg()->put_korpuskopDefaultConfig('/opt/korpuskop/config/document.report.json');
Config::Cfg()->put_korpuskopDocumentConfig('/opt/korpuskop/config/document.report.json');
Config::Cfg()->put_korpuskopDialogConfig('/opt/korpuskop/config/dialog.report.json');
Config::Cfg()->put_korpuskopProgressDir('/opt/korpuskop/var/progress');
Config::Cfg()->put_korpuskopOutputDir('/opt/korpuskop/var/output');
```

Mechanizm `Config::Cfg()` w Inforex obsłuży te pola dynamicznie, więc nie trzeba rozbudowywać klasy `Config`.

Dodatkowo zastosuj skrypt SQL historii uruchomień:

```bash
mysql -u USER -p DATABASE < documentations/integrations/korpuskop-db.sql
```

## 4. Wspierane warianty wejścia

Dopuszczalne są dokładnie dwa warianty wejścia z Inforex:

1. `document`
- plik: eksport dokumentowy `.parquet.zst` z Inforex
- mapowanie do Korpuskop: `--input-format clarin-optimized-parquet`

2. `dialog`
- plik: eksport dialogowy `.parquet.zst` z Inforex
- mapowanie do Korpuskop: `--input-format dialog-parquet`

Inne formaty wejścia nie są obsługiwane przez tę warstwę integracyjną.

## 5. Automatyczne rozpoznawanie wariantu eksportu

Integracja potrafi automatycznie rozpoznać, czy plik `.parquet.zst` jest:

- eksportem dokumentowym,
- czy eksportem dialogowym.

Detekcja odbywa się po schemacie Parquet, a nie po nazwie pliku.

Wykorzystywany jest helper:

- `local/detect-korpuskop-parquet-kind.py`
- autodetekcja wymaga środowiska Python z pakietem `pyarrow` oraz dostępnego `zstd`

Dzięki temu można zostawić `input-kind=auto` i nie pilnować ręcznie, czy plik ma być mapowany na `clarin-optimized-parquet`, czy `dialog-parquet`.

## 6. Uruchomienie z PHP

Dostępne są trzy pliki:

- `engine/include/integration/KorpuskopRunner.php`
- `local/cli-korpuskop-report.php`
- `local/korpuskop-report-form.php`

### Wariant A: użycie klasy w kodzie PHP

```php
$runner = new KorpuskopRunner();
$kind = $runner->detectInputKind('/data/dramaty.parquet.zst');

$result = $runner->runWithProgress(
    $kind === KorpuskopRunner::INPUT_KIND_DIALOG
        ? '/opt/korpuskop/config/dialog.report.json'
        : '/opt/korpuskop/config/document.report.json',
    $runner->buildInforexExportArgs(
        '/data/dramaty.parquet.zst',
        'dramaty',
        $kind,
        [
            'threads' => 8,
        ]
    ),
    function (array $event): void {
        // zapis progresu do DB / Redis / WebSocket
    }
);
```

### Wariant B: uruchomienie skryptu CLI z Inforex

```bash
php local/cli-korpuskop-report.php \
  --config-json /opt/korpuskop/config/document.report.json \
  --input /data/dramaty.parquet.zst \
  --output /opt/korpuskop/var/output/dramaty_report.zip
```

Jeśli chcesz wymusić wariant ręcznie, możesz dodać:

```bash
--input-kind document
```

albo:

```bash
--input-kind dialog
```

### Wariant C: strona w menu Inforex

Do interfejsu `Inforex` została dodana nowa strona korpusowa:

- `index.php?page=corpus_korpuskop&corpus=ID_KORPUSU`

Jest ona widoczna obok `Export` i używa tych samych uprawnień korpusowych.

Ta strona pozwala:

- wskazać plik eksportu `.parquet.zst`,
- zostawić autodetekcję albo wymusić wariant,
- ustawić `threads` i `limit-corpus-size`,
- zobaczyć wynik oraz przebieg postępu w jednym widoku,
- przeglądać historię ostatnich uruchomień,
- pobierać gotowy ZIP bezpośrednio z poziomu Inforex.

## 7. Postęp

Korpuskop działa w dwóch trybach jednocześnie:

- `--progress-json` -> stream NDJSON na `stderr`
- `--progress-file` -> plik z ostatnim stanem postępu

To pozwala:

- odbierać zdarzenia na żywo,
- albo odpytywać plik `*.json` z backendu / frontendu.

## 8. Wdrożenie produkcyjne

Rekomendowany układ:

- Inforex działa jako aplikacja WWW / worker,
- Korpuskop jest osobną binarką w `/opt/korpuskop`, ale logicznie stanowi część tego samego środowiska,
- raporty trafiają do `/opt/korpuskop/var/output`,
- pliki progresu są zapisywane do `/opt/korpuskop/var/progress`,
- formularz i strona korpusowa w Inforex odpalają Korpuskop bezpośrednio na tym serwerze.

Dobrze ustawić:

- osobnego użytkownika systemowego albo spójnie używać użytkownika serwera WWW,
- rotację starych raportów i plików progresu,
- ograniczenia CPU/RAM zależnie od wielkości korpusu,
- monitoring czasu wykonania i rozmiaru wynikowych ZIP-ów.

## 9. Docker / kontenery

Jeśli Inforex działa w Dockerze, najprościej:

- zbudować Korpuskop poza kontenerem albo w osobnym obrazie,
- zamontować `/opt/korpuskop` do kontenera PHP,
- trzymać `progress` i `output` na współdzielonym volume,
- upewnić się, że kontener PHP ma dostęp do:
  - `python3`
  - `pyarrow`
  - `zstd`

## 10. Uwagi

- do produkcji używaj `target/release/korpuskop`, nie `cargo run`
- jeśli używasz interpretacji LLM, ustaw odpowiednie zmienne środowiskowe,
- przy dużych korpusach warto sterować `--threads` i `--limit-corpus-size`.

## Docker WWW Runtime

Repozytorium Inforex zawiera teraz gotowy bundle runtime dla obrazu `docker/www`:

- `docker/www/korpuskop-runtime/bin/korpuskop`
- `docker/www/korpuskop-runtime/config/document.report.json`
- `docker/www/korpuskop-runtime/config/dialog.report.json`
- `docker/www/korpuskop-runtime/dics/`

Podczas budowy obrazu `www` katalog ten jest kopiowany bezpośrednio do `/opt/korpuskop`.
Jeżeli zaktualizujesz binarkę lub słowniki w repo `korpuskop`, odśwież także ten bundle przed budową obrazu Docker.
