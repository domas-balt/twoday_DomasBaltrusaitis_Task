<?php

namespace App\Logger\Handler;

class FileHandler implements HandlerInterface
{
    private string $fileName;
    private const string LOG_FILE_PATH = "/Files/";

    public function __construct(string $fileName)
    {
        $directoryFile = dirname(__DIR__, 2) . self::LOG_FILE_PATH . $fileName;

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