@extends('front.layouts.app')

@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('accounts.register')}}">My Account</a></li>
                <li class="breadcrumb-item">Settings</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-11 ">
    <div class="container  mt-5">
        <div class="row">
            <div class="col-md-12">
                @include('front.accounts.common.message')
            </div>
            <div class="col-md-3">
               @include('front.accounts.common.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                    </div>
                    <form action="" name="profileForm" id="profileForm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="mb-3">               
                                <label for="name">Name</label>
                                <input type="text" value="{{$user->name}}" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">            
                                <label for="email">Email</label>
                                <input type="text" value="{{$user->email}}" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">                                    
                                <label for="phone">Phone</label>
                                <input type="text" value="{{$user->phone}}" name="phone" id="phone" placeholder="Enter Your Phone" class="form-control">
                                <p></p>
                            </div>

                            <div class="d-flex">
                                <button class="btn btn-dark">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
            
            {{-- for Address Updated Form --}}

            <div class="col-md-9">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                    </div>
                    <form action="" name="addressForm" id="addressForm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">               
                                <label for="first_name">First Name</label>
                                <input type="text" value="{{(!empty($address)) ? $address->first_name : ''}}" name="first_name" id="first_name" placeholder="Enter Your First Name" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="last_name">Last Name</label>
                                <input type="text" value="{{(!empty($address)) ? $address->last_name : ''}}" name="last_name" id="last_name" placeholder="Enter Your Last Name" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="email">Email</label>
                                <input type="text" value="{{(!empty($address)) ? $address->email : ''}}" name="email" id="email" placeholder="Enter Your Email Here" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="mobile">Mobile</label>
                                <input type="text" value="{{(!empty($address)) ? $address->mobile : ''}}" name="mobile" id="mobile" placeholder="Enter Your Mobile Number" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select a Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option {{(!empty($address) && $address->country_id == $country->id) ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>            
                            </div>
                            <div class="mb-3">               
                                <label for="last_name">Address</label>
                                <textarea name="address" id="address" cols="5" rows="5" class="form-control">{{(!empty($address)) ? $address->address : ''}}</textarea>
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="apartment">Apartment</label>
                                <input type="text" value="{{(!empty($address)) ? $address->apartment : ''}}" name="apartment" id="apartment" placeholder="Enter Your Apartment" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="city">City</label>
                                <input type="text" value="{{(!empty($address)) ? $address->city : ''}}" name="city" id="city" placeholder="Enter Your City" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="state">State</label>
                                <input type="text" value="{{(!empty($address)) ? $address->state : ''}}" name="state" id="state" placeholder="Enter Your state" class="form-control">
                                <p></p>
                            </div>
                            <div class="col-md-6 mb-3">               
                                <label for="zip">PinCode</label>
                                <input type="text" value="{{(!empty($address)) ? $address->zip : ''}}" name="zip" id="zip" placeholder="Enter Your PinCode" class="form-control">
                                <p></p>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-dark">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('customJs')
<script>
    $("#profileForm").submit(function(event){
    event.preventDefault();
    $.ajax({
        url:'{{route("accounts.updateProfile")}}',
        type:'post',
        data:$(this).serializeArray(),
        dataType:'json',
        success:function(response){
            $("#profileForm #name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#profileForm #email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#profileForm #phone").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

           window.location.href='{{route("accounts.profile")}}';

        },
        error:function(jqXHR, exception){
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.name) {
                    $("#profileForm #name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.name[0]);  // Show the first error message for name
                }
                if (errors.email) {
                    $("#profileForm #email").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.email[0]);  // Show the first error message for email
                }
                if (errors.phone) {
                    $("#profileForm #phone").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.phone[0]);  // Show the first error message for password
                }

            } else {
                console.log('Something went wrong.');
            }
        }

       
    });
  });
  
  $("#addressForm").submit(function(event){
    event.preventDefault();
    $.ajax({
        url:'{{route("accounts.updateAddress")}}',
        type:'post',
        data:$(this).serializeArray(),
        dataType:'json',
        success:function(response){
            $("#addressForm #first_name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #last_name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #mobile").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #country").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #address").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #apartment").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #city").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #state").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#addressForm #pincode").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            

           window.location.href='{{route("accounts.profile")}}';

       },
        error:function(jqXHR, exception){
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.first_name) {
                    $("#addressForm #first_name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.first_name[0]);  // Show the first error message for name
                }
                if (errors.email) {
                    $("#addressForm #email").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.email[0]);  // Show the first error message for email
                }
                if (errors.mobile) {
                    $("#addressForm #mobile").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.mobile[0]);  // Show the first error message for password
                }

                
                if (errors.last_name) {
                    $("#addressForm #last_name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.last_name[0]);  // Show the first error message for name
                }
                if (errors.country) {
                    $("#addressForm #counrty").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.counrty[0]);  // Show the first error message for email
                }
                if (errors.city) {
                    $("#addressForm #city").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.city[0]);  // Show the first error message for password
                }

                
                if (errors.state) {
                    $("#addressForm #state").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.state[0]);  // Show the first error message for name
                }
                if (errors.zip) {
                    $("#addressForm #zip").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.zip[0]);  // Show the first error message for email
                }
                if (errors.apartment) {
                    $("#addressForm #apartment").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.apartment[0]);  // Show the first error message for password
                }

                
                if (errors.address) {
                    $("#addressForm #address").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.address[0]);  // Show the first error message for name
                }
                

            } else {
                console.log('Something went wrong.');
            }
        }

       
    });
  });
  

  //addressForm
</script>
@endsection