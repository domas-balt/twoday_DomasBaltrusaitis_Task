<?php

declare(strict_types=1);

namespace App\Services;

use App\Enumerators\AppType;
use App\Logger\LogLevel;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use http\Exception\InvalidArgumentException;

readonly class UserInputService
{
    public function __construct(
        private WordRepository $wordRepository,
        private SyllableRepository $syllableRepository
    ) {
    }

    public function askAboutDatabaseFileUpdates(): void
    {
        echo 'Would you like to upload a new file which would change the existing files in the database?' .
            PHP_EOL . '<< Enter [Y/y] for approval or any other button to skip this step. >>' . PHP_EOL;

        $userInput = readline();

        switch ($userInput) {
            case 'Y' || 'y':
                $this->askFilenameAndTable();

                break;
            default:
                echo PHP_EOL . 'Continuing...' . PHP_EOL;

                break;
        }
    }

    public function chooseHyphenationSource(): bool
    {
        echo 'Would you like the program to use the Database as the source?' .
            PHP_EOL . '<< Enter [Y/y] for approval or any other button to use local files as the source.' . PHP_EOL;

        $userInput = readline();

        switch ($userInput) {
            case 'Y' || 'y':
                return true;
            default:
                return false;
        }
    }

    public function checkUserArgInput(string $applicationType): AppType
    {
        switch ($applicationType) {
            case 'file':
                return AppType::File;
            case 'word':
                return AppType::Word;
            case 'database':
                return AppType::Database;
            default:
                throw new InvalidArgumentException("The app is ran as depicted here: 'php Main.php file/word textFilePath'.
                 Choose one of the keywords 'file/word/database'.");
        }
    }

    public function readWordToHyphenate(): string
    {
        echo 'Enter the word that you want to hyphenate:' . PHP_EOL;
        return readline();
    }

    private function askFilenameAndTable(): void
    {
        echo 'Which table would you like to update?' .
            PHP_EOL . '<< Enter [1] for the Words table | Enter [2] for the Syllables table >>' . PHP_EOL;

        $userInput = readline();

        switch ($userInput) {
            case 1:
                echo 'Input filename (eg. /var/words.txt):' . PHP_EOL;
                $this->wordRepository->clearWordTable();
                $this->uploadWordsFromFile(readline());

                break;
            case 2:
                echo 'Input filename (eg. /var/hyphen.txt):' . PHP_EOL;
                $this->syllableRepository->clearSyllableTable();
                $this->loadSyllablesFromFile(readline());

                break;
            default:
                echo PHP_EOL . 'Unknown input. Continuing without database updates...' . PHP_EOL;

                break;
        }
    }

    private function loadSyllablesFromFile(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        $syllables = FileService::readDataFromFile($fileName);

        $this->syllableRepository->insertManySyllables($syllables);
    }

    private function uploadWordsFromFile(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        $words = FileService::readDataFromFile($fileName);

        $this->wordRepository->insertManyWords($words);
    }
}
