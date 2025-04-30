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
                        <h2 class="h5 mb-0 pt-2 pb-2">Change Password</h2>
                    </div>
                    <form action="" name="changePasswordForm" id="changePasswordForm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="mb-3">               
                                <label for="name">Old Password</label>
                                <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">               
                                <label for="name">New Password</label>
                                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-3">               
                                <label for="name">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-dark">Change Password</button>
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
    $("#changePasswordForm").submit(function(event){
    event.preventDefault();
    $.ajax({
        url:'{{route("accounts.processChangePassword")}}',
        type:'post',
        data:$(this).serializeArray(),
        dataType:'json',
        success:function(response){
            if(response.status == true){
                $("#changePasswordForm #old_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#changePasswordForm #new_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#changePasswordForm #confirm_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                window.location.href='{{route("accounts.changePassword")}}';
           }

        },
        error:function(jqXHR, exception){
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.old_password) {
                    $("#changePasswordForm #old_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.old_password[0]);  // Show the first error message for name
                }
                if (errors.new_password) {
                    $("#changePasswordForm #new_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.new_password[0]);  // Show the first error message for email
                }
                if (errors.confirm_password) {
                    $("#changePasswordForm #confirm_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.confirm_password[0]);  // Show the first error message for password
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