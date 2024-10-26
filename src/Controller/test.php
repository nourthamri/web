<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class test extends AbstractController {
     #[Route ('/hi', name:'app')]
    public function firstFunction() : Response{

        return new Response ("Bonjour mes étudiants");
    }


} 

