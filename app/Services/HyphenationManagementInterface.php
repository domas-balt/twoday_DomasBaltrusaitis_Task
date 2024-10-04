<?php

declare(strict_types=1);

namespace App\Services;

interface HyphenationManagementInterface
{
    public function manageHyphenation(array $words): array;
}
