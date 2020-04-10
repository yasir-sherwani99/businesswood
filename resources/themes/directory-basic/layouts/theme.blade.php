<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    {!! \SEO::generate() !!}
    @yield('css')
    @include('partials.scripts.header')
</head>

<body>
@php \Actions::do_action('after_body_open') @endphp

<div class="loader-wrap">
    <div class="pin"></div>
    <div class="pulse"></div>
</div>

@include('partials.header')

<div id="wrapper">
    @yield('editable_content')
</div>

@include('partials.footer')

@include('partials.scripts.footer')

@include('components.modal',['id'=>'global-modal'])


@yield('js')
</body>
</html>