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
     * Get a wall based on random decade 
     * Gets a collection of Amazon, youtube, 7digital, flickr, google images listings and caches them if there is no existing cache
     * or the existing cache is stale.
     * Then returns 5 random results from each provider  
     * 
     * @return View
     */
    public function getMemorywallAction(){
        $mediaProviderFacade = $this->get('sk_media_api.media_provider_api');
        $wallData = $mediaProviderFacade->getMemoryWall();
        
        $view = View::create()
            ->setData(array('wallData' => $wallData));

        return $this->getViewHandler()->handle($view);
    }
}
