@extends('admin.layouts.app')
@section('content')
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Discount Coupons</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('coupons.create')}}" class="btn btn-primary">New Coupon</a>
            </div>
            
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
   
    <div class="container-fluid">
       @include('admin.message')
        <div class="card">
            <form action="" method="get">
                <div class="card-header">
                    <button type="button" onclick="window.location.href='{{route('coupons.index')}}'" class="btn btn-default btn-sm">Reset</button>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input type="text" value="{{Request::get('keyword')}}" name="keyword" class="form-control float-right" placeholder="Search">
        
                            <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body table-responsive p-0">								
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Coupon Code</th>
                            <th>Coupon Name</th>
                            <th>Max Uses</th>
                            <th>Max Uses User</th>
                            <th>Type</th>
                            <th>Discount Amount</th>
                            <th>Min Amount</th>
                            <th width="100">Status</th>
                            <th width="100">Starts At</th>
                            <th width="100">Expires At</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if ($discountcoupons->isNotEmpty())
                               @foreach ($discountcoupons as $dis )
                               <tr>
                                <td>{{$dis->id}}</td>
                                <td>{{$dis->code}}</td>
                                <td>{{$dis->name}}</td>
                                <td>{{$dis->max_uses}}</td>
                                <td>{{$dis->max_uses_user}}</td>
                                <td>{{$dis->type}}</td>
                                @if ($dis->type == 'percent')
                                   <td>{{$dis->discount_amount}}%</td>
                                @else
                                <td>₹{{$dis->discount_amount}}</td>
                                @endif
                                <td>{{$dis->min_amount}}</td>
                              
                                
                                <td>
                                    @if ( $dis->status == 1 )
                                    <svg class="text-success-500 h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @else
                                    <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @endif
                                </td>
                                <td>{{$dis->starts_at}}</td>
                                <td>{{$dis->expires_at}}</td>
                                
                                <td>
                                    <a href="{{route('coupons.edit',$dis->id)}}">
                                        <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                    </a>
                                    <a href="#" onclick="deleteDis({{$dis->id}})" class="text-danger w-4 h-4 mr-1">
                                        <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path	ath fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                          </svg>
                                    </a>
                                </td>
                            </tr>
                               @endforeach
                           @else
                               <tr>
                                  <td colspan="5">Record Not found</td>
                               </tr>
                           @endif

                       
                        
                      
    
                    </tbody>
                </table>										
            </div>
            <div class="card-footer clearfix">
                {{$discountcoupons->links()}}
                {{-- <ul class="pagination pagination m-0 float-right">
                  <li class="page-item"><a class="page-link" href="#">«</a></li>
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">»</a></li>
                </ul> --}}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>

@endsection
@section('customJs')
<script>
    function deleteDis(id){
        var url='{{route("coupons.delete","ID")}}';
        var newUrl=url.replace("ID",id);
        if(confirm("Are you sure you want to delete")){
            $.ajax({
            url: newUrl,  // Make sure the route is correct
            type: 'delete',
            data: {},
            dataType: 'json',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Clear previous errors
                $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                if (response.status) {
                    // Success response
                    // alert("Category Deleted successfully!");
                     window.location.href="{{route('coupons.index')}}";


                    // Optionally reset form or clear fields after success
                    $("#couponForm")[0].reset();
                }
            
            }
            });
        }
        
    }
</script>
@endsection