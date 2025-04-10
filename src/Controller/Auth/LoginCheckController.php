<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginCheckController extends AbstractController
{
    #[Route('/api/login_check', name: 'app_login_check', methods: ['POST'])]
    public function loginCheck()
    {
    }
}