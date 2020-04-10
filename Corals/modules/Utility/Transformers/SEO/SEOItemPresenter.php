<?php

namespace Corals\Modules\Utility\Transformers\SEO;

use Corals\Foundation\Transformers\FractalPresenter;

class SEOItemPresenter extends FractalPresenter
{

    /**
     * @return SEOItemTransformer
     */
    public function getTransformer()
    {
        return new SEOItemTransformer();
    }
}