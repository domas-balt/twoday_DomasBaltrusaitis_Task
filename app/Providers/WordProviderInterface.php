<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Word;

interface WordProviderInterface
{
    /**
     * @return Word[]
     */
    public function getWords(): array;
}
