@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit User</h1>
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
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$users->name}}">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{$users->email}}">	
                                    <p></p>
                                </div>
                            </div>	
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="{{$users->password}}">	
                                    <p></p>
                                </div>
                            </div>	 --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone" value="{{$users->phone}}">	
                                    <p></p>
                                </div>
                            </div>	
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{($users->status==1) ? 'selected' : ''}} value="1">Active</option>
                                        <option {{($users->status==0) ? 'selected' : ''}} value="0">Block</option>
                                    </select>	
                                </div>
                            </div>							
                        </div>
                    </div>							
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
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
        url: "{{ route('users.update',$users->id) }}",  // Make sure the route is correct
        type: 'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            // Clear previous errors
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#phone").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
//            $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

            if (response.status) {
                // Success response
                //alert("Category Updated successfully!");
                window.location.href="{{route('users.index')}}";

                // Optionally reset form or clear fields after success
                //$("#categoryForm")[0].reset();
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