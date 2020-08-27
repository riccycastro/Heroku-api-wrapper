<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DemographicController
 * @package App\Controller
 * @Route("/demographics")
 */
class DemographicController
{
    /**
     * @Route("/data", name="get_demographics_data")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function dataAction(Request $request)
    {
        var_dump($request->query);

        $number = random_int(0, 50);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}
