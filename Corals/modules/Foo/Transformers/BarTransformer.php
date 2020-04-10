<?php

namespace Corals\Modules\Foo\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Foo\Models\Bar;

class BarTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('foo.models.bar.resource_url');

        parent::__construct();
    }

    /**
     * @param Bar $bar
     * @return array
     * @throws \Throwable
     */
    public function transform(Bar $bar)
    {
        $show_url = url($this->resource_url . '/' . $bar->hashed_id);

        $transformedArray= [
            'id' => $bar->id,
            'name' => HtmlElement('a', ['href' => $bar->getShowURL()], $bar->name),
            'created_at' => format_date($bar->created_at),
            'updated_at' => format_date($bar->updated_at),
            'action' => $this->actions($bar)
        ];

        return parent::transformResponse($transformedArray);
    }
}