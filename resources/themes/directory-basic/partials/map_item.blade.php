<style type="text/css">
    .map-card-rainting {
        width: 230px;
    }
</style>
<div class="map-popup-wrap">
    <div class="map-popup">
        <div class="infoBox-close"><i class="fa fa-times"></i></div>
        @foreach($locationCategory as $category)
            <div class="map-popup-category">{!! $category !!}</div>
        @endforeach
        <a href="{{$locationURL}}" class="listing-img-content fl-wrap"><img
                    src="{{$locationImg}}"></a>
        <div class="listing-content fl-wrap">
            <div class="card-popup-raining map-card-rainting">
                @include('partials.components.rating',['rating'=> $locationStarRating,'rating_count'=>null])
                <span class="map-popup-reviews-count">{{$locationTitle}}</span>
            </div>
            <div class="listing-title fl-wrap"><h4><a
                            href="{{$locationURL}}">{{$locationTitle}}</a></h4><span
                        class="map-popup-location-info"><i
                            class="fa fa-map-marker"></i><span>{{$locationAddress}}</span></span><span
                        class="map-popup-location-phone"><i
                            class="fa fa-phone"></i><span>{{$locationPhone}}</span></span>
            </div>
        </div>
    </div>
</div>
