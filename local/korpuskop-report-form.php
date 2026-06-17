<?php
$enginePath = realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'engine']));
require_once($enginePath . DIRECTORY_SEPARATOR . 'settings.php');
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . '/../config/') . DIRECTORY_SEPARATOR . 'config.local.php');
require_once($enginePath . '/include/integration/KorpuskopRunner.php');

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$defaults = [
    'config_json' => '',
    'input' => '',
    'output' => '',
    'input_kind' => KorpuskopRunner::INPUT_KIND_AUTO,
    'threads' => '',
    'limit_corpus_size' => '',
];

$data = array_merge($defaults, $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : []);
$error = null;
$result = null;
$events = [];
$detectedKind = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $runner = new KorpuskopRunner();
        $inputKind = trim((string) ($data['input_kind'] ?? KorpuskopRunner::INPUT_KIND_AUTO));
        $inputPath = trim((string) ($data['input'] ?? ''));
        $outputPath = trim((string) ($data['output'] ?? ''));

        if ($inputPath === '') {
            throw new RuntimeException('Pole pliku wejściowego jest wymagane.');
        }
        if ($outputPath === '') {
            throw new RuntimeException('Pole wyjścia raportu jest wymagane.');
        }

        $detectedKind = $inputKind === KorpuskopRunner::INPUT_KIND_AUTO
            ? $runner->detectInputKind($inputPath)
            : $inputKind;

        $extraArgs = [];
        if (trim((string) ($data['threads'] ?? '')) !== '') {
            $extraArgs['threads'] = (int) $data['threads'];
        }
        if (trim((string) ($data['limit_corpus_size'] ?? '')) !== '') {
            $extraArgs['limit-corpus-size'] = (int) $data['limit_corpus_size'];
        }

        $overrideArgs = $runner->buildInforexExportArgs(
            $inputPath,
            $outputPath,
            $detectedKind,
            $extraArgs
        );

        $events[] = [
            'stage' => 'inforex_input_detection',
            'input' => $inputPath,
            'input_kind' => $detectedKind,
            'message' => 'Rozpoznano wariant eksportu Inforex.',
        ];

        $result = $runner->runWithProgress(
            trim((string) ($data['config_json'] ?? '')) !== '' ? trim((string) $data['config_json']) : null,
            $overrideArgs,
            static function (array $event) use (&$events): void {
                $events[] = $event;
            }
        );
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Inforex -> Korpuskop</title>
<style>
body { font-family: Arial, sans-serif; margin: 24px; background: #f5f7fb; color: #1d2a3a; }
.wrap { max-width: 980px; margin: 0 auto; }
.card { background: #fff; border: 1px solid #d8e0ea; border-radius: 12px; padding: 20px; box-shadow: 0 10px 24px rgba(16,24,40,0.05); }
h1 { margin: 0 0 8px; font-size: 26px; }
p.lead { margin: 0 0 20px; color: #5b6b80; }
.grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field.full { grid-column: 1 / -1; }
label { font-weight: 700; font-size: 14px; }
input, select { border: 1px solid #c9d4e1; border-radius: 8px; padding: 10px 12px; font-size: 14px; }
button { border: 0; border-radius: 10px; padding: 12px 18px; background: #1565c0; color: #fff; font-weight: 700; cursor: pointer; }
button:hover { background: #0f54a2; }
.note { margin-top: 16px; font-size: 13px; color: #5b6b80; }
.error { background: #fff1f1; border: 1px solid #f3c4c4; color: #a12a2a; padding: 12px; border-radius: 10px; margin-bottom: 16px; }
.success { background: #eef9f0; border: 1px solid #c7e9ce; color: #23663a; padding: 12px; border-radius: 10px; margin-top: 16px; }
pre { background: #0f1720; color: #d8e7ff; padding: 14px; border-radius: 10px; overflow: auto; font-size: 12px; }
.meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin: 16px 0; }
.meta-box { background: #f8fafc; border: 1px solid #d8e0ea; border-radius: 10px; padding: 12px; }
.meta-box strong { display: block; margin-bottom: 4px; }
@media (max-width: 760px) { .grid, .meta { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Uruchom raport Korpuskop z Inforex</h1>
    <p class="lead">To narzędzie przyjmuje wyłącznie eksporty Inforex w formacie <code>.parquet.zst</code>. Wariant wejścia możesz wskazać ręcznie albo zostawić autodetekcję po schemacie Parquet.</p>

    <?php if ($error !== null): ?>
      <div class="error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="grid">
        <div class="field full">
          <label for="config_json">Config JSON Korpuskop</label>
          <input type="text" id="config_json" name="config_json" value="<?php echo h($data['config_json']); ?>" placeholder="Domyślny config z Config::Cfg()" />
        </div>
        <div class="field full">
          <label for="input">Plik wejściowy .parquet.zst</label>
          <input type="text" id="input" name="input" value="<?php echo h($data['input']); ?>" placeholder="/data/inforex_export_123.parquet.zst" required />
        </div>
        <div class="field full">
          <label for="output">Wyjście raportu / ZIP</label>
          <input type="text" id="output" name="output" value="<?php echo h($data['output']); ?>" placeholder="/opt/korpuskop/var/output/dramaty" required />
        </div>
        <div class="field">
          <label for="input_kind">Wariant wejścia</label>
          <select id="input_kind" name="input_kind">
            <option value="auto" <?php echo $data['input_kind'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
            <option value="document" <?php echo $data['input_kind'] === 'document' ? 'selected' : ''; ?>>Dokumenty</option>
            <option value="dialog" <?php echo $data['input_kind'] === 'dialog' ? 'selected' : ''; ?>>Dialogi</option>
          </select>
        </div>
        <div class="field">
          <label for="threads">Wątki</label>
          <input type="number" id="threads" name="threads" value="<?php echo h($data['threads']); ?>" min="1" />
        </div>
        <div class="field">
          <label for="limit_corpus_size">Limit dokumentów</label>
          <input type="number" id="limit_corpus_size" name="limit_corpus_size" value="<?php echo h($data['limit_corpus_size']); ?>" min="1" />
        </div>
      </div>
      <div style="margin-top: 18px;">
        <button type="submit">Uruchom raport</button>
      </div>
    </form>

    <div class="note">Obsługiwane są tylko dwa warianty eksportu Inforex: dokumentowy <code>clarin-optimized-parquet</code> i dialogowy <code>dialog-parquet</code>.</div>

    <?php if ($result !== null): ?>
      <div class="success">Raport został uruchomiony. Poniżej masz wynik i przebieg postępu.</div>
      <div class="meta">
        <div class="meta-box">
          <strong>Rozpoznany wariant</strong>
          <?php echo h($detectedKind ?? ''); ?>
        </div>
        <div class="meta-box">
          <strong>Kod wyjścia</strong>
          <?php echo h((string) $result['exit_code']); ?>
        </div>
        <div class="meta-box">
          <strong>Plik postępu</strong>
          <?php echo h($result['progress_file']); ?>
        </div>
        <div class="meta-box">
          <strong>Dodatkowe stderr</strong>
          <?php echo h(implode("\n", $result['stderr_lines'])); ?>
        </div>
      </div>

      <h2>Przebieg postępu</h2>
      <pre><?php echo h(json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></pre>

      <?php if (trim((string) $result['stdout']) !== ''): ?>
        <h2>STDOUT procesu</h2>
        <pre><?php echo h($result['stdout']); ?></pre>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
