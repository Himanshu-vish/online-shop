@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Brand</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('brands.index')}}" class="btn btn-primary">Back</a>
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
        
        <form action="" method="post" id="editBrandForm" name="editBrandForm">
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$brands->name}}">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug" value="{{$brands->slug}}">	
                                    <p></p>
                                </div>
                            </div>	
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{($brands->status==1) ? 'selected' : ''}} value="1">Active</option>
                                        <option {{($brands->status==0) ? 'selected' : ''}} value="0">Block</option>
                                    </select>	
                                </div>
                            </div>									
                        </div>
                    </div>							
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('brands.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
       </form>
    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#editBrandForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('brands.update',$brands->id) }}",  // Make sure the route is correct
        type: 'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            
            if (response.status) {
                // Success response
                //alert("Category Updated successfully!");
                window.location.href="{{route('brands.index')}}";

                // Optionally reset form or clear fields after success
                $("#editBrandForm")[0].reset();
                // Clear previous errors
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

            }else{
                if(response['notFound']==true){
                    window.location.href="{{route('brands.index')}}";

                }
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

                if (errors.slug) {
                    $("#slug").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.slug[0]);  // Show the first error message for slug
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