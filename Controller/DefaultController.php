<?php

namespace RA\DocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RADocBundle:Default:index.html.twig');
    }
}
