@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('users.index')}}" class="btn btn-primary">Back</a>
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
        <form action="" method="post" id="userForm" name="userForm">
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Your Name">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Enter Your Email Here">	
                                    <p></p>
                                </div>
                            </div>	

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Create Your Own Password">	
                                    <p></p>
                                </div>
                            </div>	
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Your Phone">	
                                    <p></p>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>	
                                </div>
                            </div>								
                        </div>
                    </div>							
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{route('users.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
       </form>
    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#userForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('users.store') }}",  // Make sure the route is correct
        type: 'POST',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#phone").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                 window.location.href="{{route('users.index')}}";

              
            }
        },
        error: function(jqXHR, exception) {
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.name) {
                    $("#name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.name[0]);  // Show the first error message for name
                }

                if (errors.email) {
                    $("#email").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.email[0]);  // Show the first error message for slug
                }

                
                if (errors.password) {
                    $("#password").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.password[0]);  // Show the first error message for slug
                }

                if (errors.phone) {
                    $("#phone").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.phone[0]);  // Show the first error message for slug
                }
            } else {
                console.log('Something went wrong.');
            }
        }
    });
});

</script>
@endsection