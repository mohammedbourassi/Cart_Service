<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SumController
{
    #[Route('/sum', name: 'sum', methods: ['POST'])]
    public function sum(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate input
        if (!isset($data['a']) || !isset($data['b'])) {
            return new JsonResponse(['error' => 'Please provide a and b'], 400);
        }

        $a = $data['a'];
        $b = $data['b'];

        if (!is_numeric($a) || !is_numeric($b)) {
            return new JsonResponse(['error' => 'a and b must be numbers'], 400);
        }

        $result = $a + $b;

        return new JsonResponse(['sum' => $result]);
    }
}
