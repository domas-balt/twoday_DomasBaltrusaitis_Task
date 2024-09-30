<?php

declare(strict_types=1);

namespace App\Logger\Handler;

class LogHandler implements HandlerInterface
{
    private string $fileName;

    public function __construct(string $logFilePath)
    {
        $directoryFile = dirname(__DIR__, 2) . $logFilePath;

        if (!file_exists($directoryFile)) {
            $openFile = fopen($directoryFile, "w");
            fclose($openFile);
        }

        $this->fileName = $directoryFile;
    }

    function handle(array $variables): void
    {
        $output = self::DEFAULT_FORMAT;
        foreach ($variables as $variable => $value) {
            $output = str_replace('%' . $variable . '%', $value, $output);
        }
        file_put_contents($this->fileName, $output . PHP_EOL, FILE_APPEND);
    }
}
