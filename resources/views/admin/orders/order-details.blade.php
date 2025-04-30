@extends('admin.layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order: {{$order->id}}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('orders.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                @include('admin.message')
                <div class="card">
                    <div class="card-header pt-3">
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                            <h1 class="h5 mb-3">Shipping Address</h1>
                            <address>
                                <strong>{{$order->first_name.' '.$order->last_name}}</strong><br>
                                {{$order->address}}, {{$order->countryName}}<br>
                                {{$order->city}}, {{$order->zip}}<br>
                                Phone: {{$order->mobile}}<br>
                                Email: {{$order->email}}
                            </address>
                            <strong>Shipped Date</strong>
                            <br>
                            @if (!empty($orderDetails->shipped_date))
                            {{\Carbon\Carbon::parse($orderDetails->created_at)->format('d M, Y')}}
                            @else
                                NA
                            @endif
                            </div>
                            
                            
                            
                            <div class="col-sm-4 invoice-col">
                                <b>Invoice #007612</b><br>
                                <br>
                                <b>Order ID:</b> {{$order->id}}<br>
                                <b>Total:</b> ₹{{number_format($order->grand_total,2)}}<br>
                                <b>Status:</b>@if ($order->order_status == 'pending')
                                            <span class="badge bg-danger">Pending</span>
                                                
                                            @elseif ($order->order_status == 'shipped')
                                            <span class="badge bg-info">Shipped</span>
                                            @elseif ($order->order_status == 'deliverd')
                                            <span class="badge bg-success">Delivered</span>
                                            @else
                                            <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-3">								
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Price</th>
                                    <th width="100">Qty</th>                                        
                                    <th width="100">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $orderItem)
                                <tr>
                                    <td>{{$orderItem->name}}</td>
                                    <td>₹{{number_format($orderItem->price)}}</td>                                        
                                    <td>{{$orderItem->qty}}</td>
                                    <td>₹{{number_format($orderItem->total,2)}}</td>
                                </tr>
                                    
                                @endforeach
                                  
                                <tr>
                                    <th colspan="3" class="text-right">Subtotal:</th>
                                    <td>₹{{number_format($order->subtotal,2)}}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Discount: {{(!empty($order->coupone_code)) ? '(' .$order->coupone_code.')' : ''}}</th>
                                    <td>₹{{number_format($order->discount,2)}}</td>
                                </tr>
                                
                                <tr>
                                    <th colspan="3" class="text-right">Shipping:</th>
                                    <td>₹{{$order->shipping}}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Grand Total:</th>
                                    <td>₹{{number_format($order->grand_total,2)}}</td>
                                </tr>
                            </tbody>
                        </table>								
                    </div>                            
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <form action="" method="POST" name="changeOrderStatusForm" id="changeOrderStatusForm">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Order Status</h2>
                            <div class="mb-3">
                                <select name="order_status" id="order_status" class="form-control">
                                    <option value="pending" {{($order->order_status == 'pending') ? 'selected' : ''}}>Pending</option>
                                    <option value="shipped" {{($order->order_status == 'shipped') ? 'selected' : ''}}>Shipped</option>
                                    <option value="deliverd" {{($order->order_status == 'deliverd') ? 'selected' : ''}}>Delivered</option>
                                    <option value="cancelled" {{($order->order_status == 'cancelled') ? 'selected' : ''}}>Cancelled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Shipped Date</label>
                                <input type="text" value="{{$order->shipped_date}}" id="shipped_date" name="shipped_date" placeholder="Shipped Date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" id="sendInvoiceEmail" name="sendInvoiceEmail">
                            <h2 class="h4 mb-3">Send Inovice Email</h2>
                            <div class="mb-3">
                                <select name="userType" id="userType" class="form-control">
                                    <option value="customer">Customer</option>                                                
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection
@section('customJs')
<script>
    $(document).ready(function(){
            $('#shipped_date').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
           
        });

    $("#changeOrderStatusForm").submit(function(event){
    event.preventDefault();
    if(confirm('Are You Sure Want to Change Order Status')){
        
    $.ajax({
        url: "{{route('orders.changeOrderStatus',$order->id)}}",  // Make sure the route is correct
        type: 'POST',
        data: $(this).serializeArray(),
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                window.location.href="{{route('orders.order-details',$order->id)}}";
                
            }
        }
    });
    }
});
     
$("#sendInvoiceEmail").submit(function(event){
    event.preventDefault();
    if(confirm('Are You Sure want to send Email')){
        
    $.ajax({
        url: "{{route('orders.sendInvoiceEmail',$order->id)}}",  // Make sure the route is correct
        type: 'POST',
        data: $(this).serializeArray(),
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                window.location.href="{{route('orders.order-details',$order->id)}}";
                
            }
        }
    });
    }
});
     

</script>
@endsection