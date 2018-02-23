@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header">Available trucks</div>

                <div class="card-body">
                
					@foreach($trucks as $truck)
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="/truck/chooseTruck/{{ $truck->id }}">{{ $truck->id }}</a>
                        </div>
                    </div>
					@endforeach
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection