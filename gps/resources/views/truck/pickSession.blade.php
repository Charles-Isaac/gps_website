@extends('layouts.app')

@push('head')
<script src="{{ asset('js/map_picksessions.js') }}"></script>
<script>
	$(document).ready(function() {
    	$(".view_session_button").click(function() {
    		var sessionId = $(this).data().id;
    		console.log(sessionId);
    		$.ajax({
				url: "{{ route('getSessionPath') }}",
				type: 'POST',
				data: {
					id: sessionId,
					_token: '{{ csrf_token() }}'
				},
				success: function(stuff) {
					showDirections(JSON.parse(stuff));
				},
				error: function(stuff) {
					console.log(stuff);
				}
			});
    	});
	});
</script>
@endpush

@section('content')
<div class="container">
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
            <div class="medium-9 cell">
            	<fieldset class="fieldset">
            		<legend id="session_info">Session Info</legend>
            		<div class="cell medium-12" style="padding-bottom: 20px">
                        @include('layouts.partials.map')
                    </div>
                    <div class="grid-x grid-padding-x">
                        <div class="medium-12 cell">
                        	<div class="grid-x grid-padding-x">
                        		<!-- Tab contents -->
                        		<div class="medium-6 cell">
                        			<div style="border: 2px solid #e6e6e6; padding: 5px;">
                                		<div id="panel">
                                			<div class="grid-x grid-padding-x">
                                				<div class="cell medium-12">
                                        			<label for="client_name_textbox">Client : </label>
                                        			<input type="text" class="text" id="client_name_textbox" readonly
                                        				value="- Client -">
                                    			</div>
                                    			
                                				<div class="cell medium-12">
                                        			<label for="client_date_textbox">Date : </label>
                                        			<input type="text" class="text" id="client_date_textbox" readonly
                                        				value="- Date -">
                                    			</div>
                                    				
                                				<div class="cell medium-12">
                                        			<label for="item_count_textbox">Item count : </label>
                                        			<input type="text" class="text" id="item_count_textbox" readonly
                                        				value="- Item count -">
                                    			</div>
                                			</div>
                                        </div>
                                        <div class="medium-12 cell">
                                        	<div class="grid-x grid-padding-x">
                                            	<div class="medium-3 cell"></div>
                                            	<div class="medium-6 cell ">
                                                    <button type="button" class="button" style="width: 100%" id="submit_button">Pick</button>
                                                </div>
                                            	<div class="medium-3 cell"></div>
                                        	</div>
                                        </div>
                        		    <!-- End tab contents -->
                        		</div>
                        		<div class="medium-6 cell">
                        			<!-- Tabs -->
                        			<ul class="tab_alternate_colors vertical tabs" data-tabs id="commands">
                                      	<!-- <li class="tabs-title is_within" id=""></li> -->
                                    </ul>
                                    <!-- End tabs -->
                        		</div>
                        	</div>
                        </div>
                    </div>
    			</fieldset>
            </div>
            <div class="medium-3 cell">
                <fieldset class="fieldset">
                	<legend>Available sessions</legend>
                		<div class="grid grid-y">
                        	@foreach($sessions as $session)
                        		<div class="cell medium-3">
                            		<button type="button" class="button view_session_button" data-id="{{ $session->id }}">
                            			{{ $session->id }} - {{ $session->vehicleName }}
                            		</button>
                        		</div>
                        	@endforeach
                		</div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
@endsection