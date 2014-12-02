<?php

namespace Sk\FrontEndBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template("SkFrontEndBundle:Default:index.html.twig")
     * 
     * DOES THIS NEED TO BE HERE ONLY TO INITIALISE THE CSRF TOKEN
     * THEN IT WORKS? WHEN INITIALISING THIS FOR THE FIRST TIME, IT DIDN'T
     * WORK WITHOUT THE FRONT END BUNDLE
     * 
     */
    public function indexAction()
    {
        return array();
    }
}
