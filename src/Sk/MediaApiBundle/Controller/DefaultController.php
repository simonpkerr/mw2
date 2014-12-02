<?php

namespace Sk\MediaApiBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
    
    /**
     * @return View
     */
    public function getMediatypesAction(){
        $em = $this->getDoctrine()->getManager();
        $mediaTypes = $em->getRepository('SkMediaApiBundle:MediaType')->getMediaTypes();
        $view = View::create()
            ->setData(array('mediaTypes' => $mediaTypes));

        return $this->getViewHandler()->handle($view);
    }
    
    /**
     * Gets the media type for a given id.
     *
     * @param mixed $id
     *
     * @return View
     */
    public function getMediatypeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mediaType = $em->getRepository('SkMediaApiBundle:MediaType')->getMediaTypeById($id);
        
        if (null === $mediaType) {
            throw new NotFoundHttpException(sprintf("Media Type with id '%s' could not be found.", $id));
        }

        $view = View::create()
            ->setData(array('mediaType' => $mediaType));

        return $this->getViewHandler()->handle($view);
    }
    
    /**
     * Get a wall.
     * Gets a collection of Amazon listings and caches them.
     * Then returns 5 random results  
     * 
     * @return View
     */
    public function getMemoryWall(){
        
    }
}
