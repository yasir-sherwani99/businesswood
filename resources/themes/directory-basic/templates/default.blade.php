@extends('layouts.theme')


@section('editable_content')
    @include('partials.page_header')

    @php \Actions::do_action('pre_content',$item, $home??null) @endphp

    <div class="container">
        <div class="row">
            <div class="col-12">
                {!! $item->rendered !!}
            </div>
        </div>
    </div>
@stop