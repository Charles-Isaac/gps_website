@extends('layouts.app')

@push('head')
<style>
    .container_map {
        margin: 10px;
    }
</style>
@endpush


@section('content')
<div class="container grid-x">
	<div class="cell medium-6 container_map">
		@include('layouts.partials.map')
	</div>
	<div class="cell medium-6 container_content">
	
	</div>
</div>
@endsection