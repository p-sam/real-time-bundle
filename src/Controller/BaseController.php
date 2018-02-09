<?php

namespace SP\RealTimeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/realtime/", name="sp_real_time_endpoint")
     *
     * @return Response
     */
    public function subscribe(): Response
    {
        return new Response('', 204);
    }
}
