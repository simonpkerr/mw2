<?php

namespace Sk\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template("SkCommentBundle:Default:index.html.twig")
     */
    public function indexAction($name = 'simon')
    {
        return array('name' => $name);
    }
}
