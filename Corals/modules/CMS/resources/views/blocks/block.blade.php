@isset($block)
    <div class="row">
        @forelse($block->activeWidgets as $widget)
            <div class="col-md-{{ $widget->widget_width }}">
                {!! $widget->rendered !!}
            </div>
        @empty
        @endforelse
    </div>
@else
    <p class="text-center text-danger">
        <strong> {!! trans('CMS::labels.block.block_cannot_found',['block_key' => $block_key]) !!}</strong></p>
@endisset()
