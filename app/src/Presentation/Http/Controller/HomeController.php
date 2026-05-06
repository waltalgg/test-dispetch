<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response(file_get_contents(__DIR__.'/../../../../public/index.html'), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
}
