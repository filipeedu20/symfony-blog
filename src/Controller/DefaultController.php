<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use App\Mailer\SimpleMailer;


class DefaultController extends AbstractController{

    private $mailer;


    // public function __construct(SimpleMailer $mailer)
    // {   
    //       $this->mailer = $mailer;  
    // }

    // public function index(string $name): Response{
    //     $this->mailer->send('nomeEmail','Conteudo_mensagem');
    //     return new Response('ok');
    // }
}