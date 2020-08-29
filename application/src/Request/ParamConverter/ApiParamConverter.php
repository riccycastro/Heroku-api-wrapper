<?php


namespace App\Request\ParamConverter;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiParamConverter implements ParamConverterInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->request = $request;
    }

    /**
     * @param string $parameterName
     * @return array
     */
    protected function getArrayQueryParam(string $parameterName): array
    {
        if (!$this->request->query->has($parameterName)) {
            return [];
        }

        $paramValue = $this->request->query->get($parameterName);

        if (!is_array($paramValue)) {
            return [];
        }

        return $paramValue;
    }
}
