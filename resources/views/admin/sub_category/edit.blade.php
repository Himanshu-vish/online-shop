@extends('admin.layouts.app')
@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">					
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('sub-categories.index')}}" class="btn btn-primary">Back</a>
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
            <form action="" name="subCategoryForm" id="subCategoryForm">
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">select Category</option>                                                                    
                                        @if ($categories->isNotEmpty())
                                        @foreach($categories as $category ) 
                                        <option {{($sub_category->category_id==$category->id) ? 'selected' : ''}} value="{{$category->id}}">{{$category->name}}</option>                                                                    
                                        @endforeach                                        
                                        @endif
                                        
                                        
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$sub_category->name}}">	
                                    <p></p>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug" value="{{$sub_category->slug}}">	
                                    <p></p>
                                </div>
                               
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{($sub_category->status==1) ? 'selected' : ''}} value="1">Active</option>
                                    <option {{($sub_category->status==0) ? 'selected' : ''}} value="0">Block</option>
                                </select>	
                                <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ShowHome">Show On Home</label>
                                    <select name="ShowHome" id="ShowHome" class="form-control">
                                        <option {{($sub_category->ShowHome=='Yes') ? 'selected' : ''}} value="Yes">Yes</option>
                                        <option {{($sub_category->ShowHome=="No") ? 'selected' : ''}} value="No">No</option>
                                    </select>	
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('sub-categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->

@endsection
@section('customJs')
 <script>
    $("#subCategoryForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('sub-categories.update',$sub_category->id) }}",  // Make sure the route is correct
        type: 'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            //  Clear previous errors
            
            if (response.status==true) {
                // Success response
               // alert("Category created successfully!");
               window.location.href="{{route('sub-categories.index')}}";

                // Optionally reset form or clear fields after success
                $("#subCategoryForm")[0].reset();
            
            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#category").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            }else{
                if(response['notFound']==true){
                    window.location.href="{{route('sub-categories.index')}}";

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

                if (errors.category) {
                    $("#category").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.category[0]);  // Show the first error message for slug
                }
            } else {
                
                console.log('Something went wrong.');
            }
        }
    });
});
///////////////////////////////////////////////
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