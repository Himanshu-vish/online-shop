@extends('admin.layouts.app')
@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Coupons</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('coupons.index')}}" class="btn btn-primary">Back</a>
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
        <form action="" method="post" id="discountForm" name="discountForm">
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Coupon Code</label>
                                    <input type="text" name="code" id="code" class="form-control" placeholder="Coupon Code">	
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Coupon Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Coupon Code Name">	
                                    <p></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Description</label>
                                    <textarea name="disription" id="discription" cols="10" rows="5" class="form-control"></textarea>	
                                    <p></p>
                                </div>
                            </div>
                            	
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses</label>
                                    <input type="number" name="max_uses" id="max_uses" class="form-control" placeholder="Max Uses">	
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses User</label>
                                    <input type="text" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="Max Uses User">	
                                    <p></p>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">TYPE</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="percent">Percent</option>
                                        <option value="fixed">Fixed</option>
                                    </select>	
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Discount (P/F)</label>
                                    <input type="text" name="discount_amount" id="discount_amount" class="form-control" placeholder="Discount Amount">	
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Min Amount</label>
                                    <input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Min Amount">	
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
                            
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Starts At</label>
                                    <input type="text" name="starts_at" id="starts_at" class="form-control" placeholder="Starts At">	
                                    <p></p>
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Expires At</label>
                                    <input type="text" name="expires_at" id="expires_at" class="form-control" placeholder="Expires At">	
                                    <p></p>
                                </div>
                            </div>
                            									
                        </div>
                    </div>							
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{route('coupons.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
       </form>
    <!-- /.card -->
</section>
@endsection
@section('customJs')
<script>
$("#discountForm").submit(function(event){
    event.preventDefault();
    var element = $(this);

    $.ajax({
        url: "{{ route('coupons.store') }}",  // Make sure the route is correct
        type: 'POST',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response) {
            // Clear previous errors
            $("#code").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#discount_amount").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#starts_at").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            $("#expires_at").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

            if (response.status) {
                // Success response
               // alert("Category created successfully!");
               window.location.href="{{route('coupons.index')}}";

                // Optionally reset form or clear fields after success
                $("#discountForm")[0].reset();
            }
        },
        error: function(jqXHR, exception) {
            if (jqXHR.status === 422) {
                // Laravel validation errors
                var errors = jqXHR.responseJSON.errors;

                if (errors.code) {
                    $("#code").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.code[0]);  // Show the first error message for name
                }

                if (errors.discount_amount) {
                    $("#discount_amount").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.discount_amount[0]);  // Show the first error message for slug
                }
                if (errors.starts_at) {
                    $("#starts_at").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.starts_at
                    [0]);  // Show the first error message for slug
                }
                if (errors.expires_at) {
                    $("#expires_at").addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors.expires_at
                    [0]);  // Show the first error message for slug
                }
            } else {
                console.log('Something went wrong.');
            }
        }
    });
});

$(document).ready(function(){
            $('#expires_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
            $('#starts_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
        });
    
</script>
@endsection