<?php

namespace Corals\Foundation\Transformers;

use League\Fractal\TransformerAbstract;

class APIBaseTransformer extends TransformerAbstract
{
    /**
     * @param array $transformedArray
     * @param null $model
     * @param array $extra
     * @return array
     */
    public function transformResponse(array $transformedArray, $model = null, $extra = [])
    {
        $requestOnly = request()->get('select');

        if (!empty($requestOnly)) {
            $onlyColumns = explode(',', $requestOnly);

            $transformedArray = array_filter($transformedArray, function ($key) use ($onlyColumns) {
                return in_array($key, $onlyColumns);
            }, array_FILTER_USE_KEY);
        }

        return $transformedArray;
    }
}