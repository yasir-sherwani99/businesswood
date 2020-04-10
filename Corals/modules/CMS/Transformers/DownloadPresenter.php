<?php

namespace Corals\Modules\CMS\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class DownloadPresenter extends FractalPresenter
{

    /**
     * @return DownloadTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new DownloadTransformer();
    }
}
