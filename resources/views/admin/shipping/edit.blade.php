@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('shipping.create')}}" class="btn btn-primary">Back</a>
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
        <form action="" method="post" id="shippingForm" name="shippingForm">
            
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                   <select name="country" id="country" class="form-control">
                                     <option value="">select a country</option>
                                   @if ($countries->isNotEmpty())
                                         @foreach ($countries as $country)
                                             <option {{($shippingCharges->country_id == $country->id) ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                         @endforeach
                                         <option {{($shippingCharges->country_id == 'rest_of_world') ? 'selected' : ''}} value="rest_of_world">Rest Of the World</option>
                                   @endif
                                   </select>	
                                   <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" value="{{$shippingCharges->amount}}" name="amount" id="amount" class="form-control" placeholder="Amount">	
                                    <p></p>
                                </div>
                            </div>
                           								
                        </div>
                    </div>							
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('shipping.create')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
       </form>
      
       

    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#shippingForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{route('shipping.update',$shippingCharges->id)}}",  // Make sure the route is correct
        type: 'put',
        data: element.serializeArray(),
        dataType: 'json',
      
        success: function(response) {
            // Clear previous errors
            $("#country").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#amount").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

           
            window.location.href="{{route('shipping.create')}}";

           
            
        },
        error: function(jqXHR, exception) {
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.country_id) {
                    $("#country_id").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.country_id[0]);  // Show the first error message for name
                }

                if (errors.amount) {
                    $("#amount").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.amount[0]);  // Show the first error message for slug
                }
            } else {
                console.log('Something went wrong.');
            }
        }
     });
    });
    


</script>
@endsection