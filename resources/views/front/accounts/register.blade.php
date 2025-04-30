@extends('front.layouts.app')

@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                <li class="breadcrumb-item">Register</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-10">
    <div class="container">
       
        <div class="login-form">    
            <form action="" method="post" id='registrationForm' name='registrationForm'>
                <h4 class="modal-title">Register Now</h4>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                    <p></p>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                    <p></p>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                    <p></p>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                    <p></p>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation">
                    <p></p>
                </div>
                <div class="form-group small">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div> 
                <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
            </form>			
            <div class="text-center small">Already have an account? <a href="{{route('accounts.login')}}">Login Now</a></div>
        </div>
    </div>
</section>
@endsection

@section('customJs')

<script type="text/javascript">

  $("#registrationForm").submit(function(event){
    event.preventDefault();
    $.ajax({
        url:'{{route("accounts.processRegister")}}',
        type:'post',
        data:$(this).serializeArray(),
        dataType:'json',
        success:function(response){
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

            window.location.href="{{route('accounts.login')}}";

        },
        error:function(jqXHR, exception){
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.name) {
                    $("#name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.name[0]);  // Show the first error message for name
                }
                if (errors.email) {
                    $("#email").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.email[0]);  // Show the first error message for email
                }
                if (errors.password) {
                    $("#password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.password[0]);  // Show the first error message for password
                }

            } else {
                console.log('Something went wrong.');
            }
        }

    });
  });  
</script>

@endsection