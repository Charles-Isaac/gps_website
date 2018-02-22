@push('head_end')
    <script>
    var map;
    var map_init_position = map_init_position || {lat: 43.645206, lng: -115.993011};
    function initMap() {
    	map = new google.maps.Map(document.getElementById('map'), {
    		zoom: 20,
    		center: map_init_position,
    		mapTypeId: "hybrid"
    	});
    }
    </script>
	<script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&callback=initMap">
    </script>
@endpush

<div id="map"></div>
