@extends('layouts.app')

@section('head')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&callback=initMap">
</script>

<style>
    #map {
        height: 100%;
    }
</style>
@endsection

@section('content')

<div class="container">
    <div class="row justify-content-center">
    	<div id="map"></div>
	</div>
</div>
@endsection