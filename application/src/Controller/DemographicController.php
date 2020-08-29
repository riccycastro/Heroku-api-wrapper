<?php

namespace App\Controller;

use App\Model\HerokuAppSearch;
use App\Service\Interfaces\DemographicServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DemographicController
 * @package App\Controller
 * @Route("/demographics")
 */
class DemographicController
{
    /**
     * @var DemographicServiceInterface
     */
    private $demographicService;

    /**
     * DemographicController constructor.
     * @param DemographicServiceInterface $demographicService
     */
    public function __construct(DemographicServiceInterface $demographicService)
    {
        $this->demographicService = $demographicService;
    }

    /**
     * @Route("/{endpointName}", name="get_demographics_data")
     * @ParamConverter("herokuAppSearch")
     * @param HerokuAppSearch $herokuAppSearch
     * @param string endpointName
     * @return JsonResponse
     */
    public function indexAction(HerokuAppSearch $herokuAppSearch, string $endpointName): JsonResponse
    {
        return new JsonResponse($this->demographicService->getData($endpointName, $herokuAppSearch));
    }
}
