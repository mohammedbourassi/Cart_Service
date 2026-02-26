<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/hello', name: 'hello_list')]
    public function index()
    {
        // dd() is Symfony's dump + die
        dd('Hello world!');
    }
}
