<?php

declare(strict_types=1);

namespace App\Server\Providers;

use App\Server\Entities\Word;

interface WordProviderInterface
{
    /**
     * @return Word[]
     */
    public function getWords(): array;
}
