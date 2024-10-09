<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Response;

interface ControllerInterface
{
    public function getAll(): Response;
    public function getById(int $id): Response;
}
