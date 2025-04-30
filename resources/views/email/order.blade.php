<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">
    @if ($mailData['userType'] == 'customer')
    <h1>Thanks for Your Order</h1>
    <h3>Your Order Id : #{{$mailData['order']->id}}</h3>

    @else
    <h1>You Have Received an Order</h1>
    <h3>Order Id : #{{$mailData['order']->id}}</h3>

    @endif
    <h1>Thanks for Your Order</h1>
    <h3>Your Order Id : #{{$mailData['order']->id}}</h3>

    <h1>Shipping Address</h1>
                            <address>
                                <strong>{{$mailData['order']->first_name.' '.$mailData['order']->last_name}}</strong><br>
                                {{$mailData['order']->address}}, {{getCountryInfo($mailData['order']->country_id)->name}}<br>
                                {{$mailData['order']->city}}, {{$mailData['order']->zip}}<br>
                                Phone: {{$mailData['order']->mobile}}<br>
                                Email: {{$mailData['order']->email}}
                            </address>

    <h2>Products</h2>
    <table>
        <thead>
            <tr style="background: #ccc">
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>                                        
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['order']->items as $orderItem)
            <tr>
                <td>{{$orderItem->name}}</td>
                <td>₹{{number_format($orderItem->price)}}</td>                                        
                <td>{{$orderItem->qty}}</td>
                <td>₹{{number_format($orderItem->total,2)}}</td>
            </tr>
                
            @endforeach
              
            <tr>
                <th colspan="3" align="right">Subtotal:</th>
                <td>₹{{number_format($mailData['order']->subtotal,2)}}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Discount: {{(!empty($order->coupone_code)) ? '(' .$order->coupone_code.')' : ''}}</th>
                <td>₹{{number_format($mailData['order']->discount,2)}}</td>
            </tr>
            
            <tr>
                <th colspan="3" align="right">Shipping:</th>
                <td>₹{{$mailData['order']->shipping}}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Grand Total:</th>
                <td>₹{{number_format($mailData['order']->grand_total,2)}}</td>
            </tr>
        </tbody>
    </table>	
</body>
</html>