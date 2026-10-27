<?php

class DebugLoggerTest extends PHPUnit_Framework_TestCase
{
    private $logFile;
    private $originalErrorLog;

    protected function setUp(): void
    {
        $this->logFile = tempnam(sys_get_temp_dir(), 'inforex-debug-logger-');
        $this->originalErrorLog = ini_get('error_log');
        ini_set('error_log', $this->logFile);
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->originalErrorLog);

        if (is_file($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testStaticLoggingMethodsWriteMessages(): void
    {
        DebugLogger::logVariable('plain', 'value');
        DebugLogger::logVariableAsJSON('json', array('ok' => true));

        $log = file_get_contents($this->logFile);

        $this->assertStringContainsString('plain = \'value\'', $log);
        $this->assertStringContainsString('json = {"ok":true}', $log);
    }
}
