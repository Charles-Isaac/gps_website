
@extends('layouts.app') 

@section('head')


<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4 log-level="warn"></script>
<script src="https://js.braintreegateway.com/web/3.31.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.31.0/js/paypal-checkout.min.js"></script>

<script src="https://js.braintreegateway.com/web/dropin/1.9.4/js/dropin.min.js"></script>


<script>
$(document).ready(function(){
	var button = document.querySelector('#submit-button');

	  braintree.dropin.create({
	    authorization: '{{ $braintree_key }}',
	    container: '#dropin-container',
	    paypal: {
	        flow: 'vault'
	      }
	  }, function (createErr, instance) {
	    button.addEventListener('click', function () {
	        instance.requestPaymentMethod(function (err, payload) {
	            if (err) {
	  			console.log(err);
	            } else {
	          	  $.ajax({
	                  url: '/controller/finishTransaction', 
	                  type: 'post', 
	                  data: {
	                    _token: $('meta[name="csrf-token"]').attr('content'),
	                    payment_methode_nonce: payload.nonce
	                  }, 
	          		success: function(data) {

	          		  window.location.href = "/controller/mail";
		          		
	              	  console.log(data);
	              	},
	              	error: function(err) {
	                    console.log(err);
	                    window.location.href = "/controller/mail";
	              	}
	                }
	              );
	            }
	          });
	        });
	  });

	});
  
  
</script>

@endsection 



@section('content')
<!--<div id="paypal-button"></div>
<div id="paypal-credit-button"></div> -->

  <div id="dropin-container"></div>
  <button id="submit-button">Request payment method</button>
@endsection

