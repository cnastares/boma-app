<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.t_order_summary') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 600px;
            margin: 0 auto;
            border: 1px solid #eaeaea;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-header h2 {
            margin: 0;
            color: #333;
        }

        .order-summary,
        .tracking,
        .payment-info {
            margin-bottom: 20px;
        }

        .tracking ul {
            list-style-type: none;
            padding: 0;
        }

        .tracking ul li {
            margin-bottom: 10px;
            /* padding-left: 20px; */
            position: relative;
        }

        /* .tracking ul li::before {
            content: "●";
            color: #f39c12;
            position: absolute;
            left: 0;
            top: 0;
        } */

        .order-summary,
        .payment-info {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .order-summary p,
        .payment-info p {
            margin: 5px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .product {
            display: flex;
            align-items: center;
            padding: 5px;
        }

        .product img {
            border-radius: 10%;
            margin-right: 15px;
        }

        .bold {
            font-weight: bold;
        }

        .justify-between {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
            gap: 0 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <img src="{{ getSettingMediaUrl('general.logo_path', 'logo', asset('images/logo.svg')) }}" alt="Logo" />
        <div class="order-header" style="margin-top: 20px;">
            <h2>{{__('messages.t_placed_order_id')}}{{$order->order_number}}</h2>
            @if (!isEnablePointSystem())
            <a href="{{route('reservation.view-purchases', $order->id) }}" target="_blank" class="button">{{__('messages.t_view_order')}}</a>
            @endif
        </div>

        @foreach ($order->items as $item)
        <div class="order-summary">
            <a target="_blank" href="{{ route('ad.overview', $item->ad->slug) }}" class="product">
                @php
                $imageProperties = $item->ad->image_properties;
                $altText = $imageProperties['1'] ?? $item->ad->title;
                @endphp
                <img src="{{ $item->ad->primaryImage ?? asset('/images/placeholder.jpg') }}" alt="{{ $altText }}" width="100" height="100">
                <div>
                    <p class="bold">{{ $item->ad->title }} x {{$item->quantity}}</p>
                    <p>{{ $item->ad->category->name }}</p>
                    <p>{{__('messages.t_order_seller')}} {{ $item->ad->user->name }}</p>
                    <p>{{__('messages.t_order_price',['price'=> isEnablePointSystem() ? currencyToPointConversion(formatPriceWithCurrency($item->price)) : formatPriceWithCurrency($item->price)])}}</p>
                </div>
            </a>
        </div>
        @endforeach

        <div class="tracking">
            <h3>{{__('messages.t_order_track')}}</h3>
            <ul class="">
                @foreach ($histories as $history)
                <li class="justify-between"><span> {{ \Str::title(str_replace('_', ' ' , $history->action)) }}</span> <span> {{ $history->action_date ? \Carbon\Carbon::parse($history->action_date)->format('D, M d, Y h:m a') : 'update soon'}}<span></li>
                @endforeach
            </ul>
        </div>

        <div class="order-summary">
            <h3>{{__('messages.t_order_summary')}}</h3>
            <div class="justify-between">
                <span>{{__('messages.t_cart_quantity',['cartquantity'=>$order->items->count()])}}</span>
                <span>{{ isEnablePointSystem() ? currencyToPointConversion(formatPriceWithCurrency($order->subtotal_amount)) : formatPriceWithCurrency($order->subtotal_amount) }}</span>
            </div>
            {{-- <div class="justify-between"><span>{{__('messages.t_discount')}} </span><span>{{ isEnablePointSystem() ? currencyToPointConversion(formatPriceWithCurrency($order->discount_amount)) : formatPriceWithCurrency($order->discount_amount) }}</span></div> --}}
            @if (!isEnablePointSystem() && isECommerceTaxOptionEnabled())
            <div class="justify-between"><span>{{__('messages.t_tax')}} </span><span>{{  currencyToPointConversion(formatPriceWithCurrency($order->tax_amount))}}</span></div>
            @endif

            <div class="justify-between"><span>{{ __('messages.t_delivery_charges') }}</span><span>{{ __('messages.t_free_charge') }}</span></div>
            <div class="bold justify-between"><span>{{ __('messages.t_total_amount') }}</span><span>{{ isEnablePointSystem() ? currencyToPointConversion(formatPriceWithCurrency($order->total_amount)) : formatPriceWithCurrency($order->total_amount) }}</span></div>

            @if (!isEnablePointSystem() && $order->total_amount != $order->converted_amount)
            <div class="justify-between"><span>{{__('messages.t_my_purchase_converted_amount')}} </span><span>{{  currencyToPointConversion(formatPriceWithCurrency($order->converted_amount))}}</span></div>
            @endif
        </div>
        <div class="payment-info">
            <h3>{{__('messages.t_payment_info')}}</h3>
            <div class="justify-between"><span>{{__('messages.t_payment_method')}} </span><span>{{ \Str::title($order->payment_method) }}</span></div>
            <div class="justify-between"><span>{{__('messages.t_date_of_process')}} </span><span>{{ \Str::title(\Carbon\Carbon::parse($order->created_at)->format('d M Y h:m a') ) }}</span></div>
        </div>
        <div class="payment-info">
            <h3>{{__('messages.t_shipping_info')}}</h3>
            <div class="justify-between"><span>{{__('messages.t_order_contact_name')}}</span><span>{{ $order->contact_name }}</span></div>
            <div class="justify-between"><span>{{__('messages.t_order_address')}}</span><span>{{$order->shipping_address}}</span></div>
            <div class="justify-between"><span>{{__('messages.t_order_contact_no')}}</span><span>{{ $order->contact_phone_number }}</span></div>
        </div>
    </div>
</body>

</html>
