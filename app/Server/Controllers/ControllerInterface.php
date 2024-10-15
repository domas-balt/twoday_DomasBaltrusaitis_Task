<?php

declare(strict_types=1);

namespace App\Server\Controllers;

use App\Server\Entities\Response;

interface ControllerInterface
{
    public function getAll(): Response;
    public function getById(int $id): Response;
}
