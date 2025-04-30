@extends('admin.layouts.app')
@section('content')
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Page</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="pages.html" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    @include('admin.message')
    <!-- Default box -->
    <div class="container-fluid">
       <form action="" id="pageForm" name="pageForm">
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name">	
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug">	
                            <p></p>
                        </div>
                    </div>	
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="summernote" cols="30" rows="10"></textarea>
                        </div>								
                    </div>                                    
                </div>
            </div>							
        </div>
     
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="pages.html" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#pageForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('page.store') }}",  // Make sure the route is correct
        type: 'POST',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            // Clear previous errors
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

            if (response.status) {
                // Success response
               // alert("Category created successfully!");
               window.location.href="{{route('page.index')}}";

                // Optionally reset form or clear fields after success
               // $("#categoryForm")[0].reset();
            }
        },
        error: function(jqXHR, exception) {
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.name) {
                    $("#name").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.name);  // Show the first error message for name
                }

                if (errors.slug) {
                    $("#slug").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.slug);  // Show the first error message for slug
                }
            } else {
                console.log('Something went wrong.');
            }
        }
    });
});

$('#name').change(function(){
    element = $(this);
    $.ajax({
        url: "{{ route('getSlug') }}",  // Make sure the route is correct
        type: 'get',
        data: {title:element.val()},
        dataType: 'json',
        success: function(response) {
            if (response["status"] == true){
                    $("#slug").val(response["slug"]);
            }

        }
});
});


</script>
@endsection







