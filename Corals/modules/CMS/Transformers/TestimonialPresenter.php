<?php

namespace Corals\Modules\CMS\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class TestimonialPresenter extends FractalPresenter
{

    /**
     * @return TestimonialTransformer
     */

    public function getTransformer()
    {
        return new TestimonialTransformer();
    }
}
