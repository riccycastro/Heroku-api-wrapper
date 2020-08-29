<?php

namespace App\Request\ParamConverter;

use App\Model\HerokuAppSearch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class HerokuAppSearchConverter extends ApiParamConverter
{
    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        parent::apply($request, $configuration);

        $herokuAppSearch = new HerokuAppSearch();

        $queryStringIndicatorIds = 'indicator_ids';

        if ($request->query->has($queryStringIndicatorIds)) {
            $herokuAppSearch->setIndicatorIds($this->getArrayQueryParam($queryStringIndicatorIds));
        }

        $request->attributes->set($configuration->getName(), $herokuAppSearch);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === HerokuAppSearch::class;
    }
}
