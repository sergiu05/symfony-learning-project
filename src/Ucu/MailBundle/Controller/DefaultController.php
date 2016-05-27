<?php

namespace Ucu\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UcuMailBundle:Default:index.html.twig');
    }
}
