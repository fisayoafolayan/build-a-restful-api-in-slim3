<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return mixed
     */
    public function index(Request $request, Response $response, $args)
    {
        return $this->container->view->render($response, 'home/index.twig', [
            'appName' => $this->container->settings['app']['name'],
        ]);
    }
}
