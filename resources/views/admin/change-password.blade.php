@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Change Password</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('admin.dashboard')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    @include('admin.message')
    <div class="container-fluid">
        <form action="" method="post" id="changePasswordForm" name="changePasswordForm">
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="old_password">Old Password</label>
                                <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password">	
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_password">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password">	
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password">	
                                <p></p>
                            </div>
                        </div>
                       								
                    </div>
                </div>							
            </div>

            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="{{route('admin.dashboard')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
            
       </form>
    </div>   
    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#changePasswordForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('admin.processchangePassword') }}",  // Make sure the route is correct
        type: 'POST',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
         
            if (response.status) {
                  // Clear previous errors
                $("#old_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#new_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#confirm_password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
               window.location.href="{{route('admin.showChangePasswordForm')}}";

                // Optionally reset form or clear fields after success
              //  $("#categoryForm")[0].reset();
            }
        },
        error: function(jqXHR, exception) {
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.old_password) {
                    $("#old_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.old_password[0]);  // Show the first error message for name
                }

                if (errors.new_password) {
                    $("#new_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.new_password[0]);  // Show the first error message for slug
                }

                if (errors.confirm_password) {
                    $("#confirm_password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.confirm_password[0]);  // Show the first error message for slug
                }
            } else {
                console.log('Something went wrong.');
            }
        }
    });
});

</script>
@endsection