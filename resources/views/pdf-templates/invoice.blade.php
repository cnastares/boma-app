<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ e($invoice->get('name')) }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css" media="screen">
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        th,
        tr,
        td,
        p,
        div {
            line-height: 1.1;
        }

        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: "Helvetica", "Courier", "Segoe UI", Roboto, Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #050038;
            text-align: left;
            background-color: #fff;
            font-size: 14px;
            margin: 0;
        }

        h1 {
            margin: 0;
        }

        p {
            margin: 0;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            text-align: inherit;
            font-weight: normal;
            border-bottom: 1px solid #050038;
        }

        td {
            padding: 0;
        }

        .container {
            margin: 36pt;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .pb-1 {
            padding-bottom: 0.25rem !important;
        }

        .pr-1 {
            padding-right: 0.25rem !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .py-1,
        .pt-1 {
            padding-top: 0.25rem !important;
        }

        .pr-2,
        .p-2 {
            padding-right: 0.5rem !important;
        }

        .pl-2,
        .p-2 {
            padding-left: 0.5rem !important;
        }

        .py-2,
        .pb-2,
        .p-2 {
            padding-bottom: 0.5rem !important;
        }

        .py-2,
        .pt-2,
        .p-2 {
            padding-top: 0.5rem !important;
        }

        .has-text-right {
            text-align: right !important;
        }

        .has-text-centered {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .mb-5 {
            margin-bottom: 1.5rem !important;
        }

        .mt-5 {
            margin-top: 1.5rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .mb-6 {
            margin-bottom: 3rem !important;
        }

        .align-top {
            vertical-align: top;
        }

        .heading {
            background-color: #050038;
            height: 10px;
            width: 100%;
        }

        .nowrap {
            white-space: nowrap;
        }

        .has-border-bottom-light {
            border-bottom: 1px solid #f0f0f0;
        }

        .preline {
            white-space: pre-line;
        }
    </style>
</head>

<body>
    <div class="heading"></div>
    <div class="container">
        <table class="mb-5">
            <tbody>
                <tr>
                    <td class="align-top">
                        <h1 class="mb-1">
                            <strong>{{ e($invoice->get('name')) }}</strong>
                        </h1>
                        @if ($invoice->get('status'))
                            <p class="mb-5">
                                <strong>{{ e($invoice->get('status')) }}</strong>
                            </p>
                        @endif

                        <table>
                            <tbody>
                                <tr class="">
                                    <td class="nowrap pb-1 pr-2">
                                        <strong>{{__('messages.t_pdf_invoice_number')}} </strong>
                                    </td>
                                    <td class="pb-1" width="100%">
                                        <strong>{{ e($invoice->get('invoice_id')) }}</strong>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="nowrap pb-1 pr-2">{{__('messages.t_pdf_date_of_issue')}} </td>
                                    <td class="pb-1" width="100%">
                                        {{ e($invoice->get('invoice_date')?->format('d/m/Y')) }}
                                    </td>
                                </tr>
                                @if (e($invoice->get('due_date')))
                                    <tr>
                                        <td class="nowrap pb-1 pr-2">{{__('messages.t_pdf_due_date')}}</td>
                                        <td class="pb-1" width="100%">
                                            {{ e($invoice->get('due_date')->format('d/m/Y')) }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                    @if (e($invoice->get('logo')))
                    <td class="align-top" width="30%">
                        <img src="{{ e($invoice->get('logo')) }}" alt="{{e(config('app.name'))}}" height="25" width="90">
                    </td>
                @endif

                </tr>

            </tbody>
        </table>

        <table class="mb-6">
            <tbody>
                <tr>
                    <td class="align-top" width="50%">
                            <p class="pb-1"><strong>{{ e(config('app.name')) }}</strong></p>
                    </td>
                    <td class="align-top" width="50%">
                        @if ($name = e(data_get($invoice->get('buyer'), 'name')))
                            <p class="pb-1"><strong>{{ e($name) }}</strong></p>
                        @endif

                        @if ($email = e(data_get($invoice->get('buyer'), 'email')))
                            <p class="pb-1">{{ e($email) }}</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="mb-5">
            <thead>
                <tr>
                    <th class="py-2 pr-2">{{__('messages.t_pdf_description')}}</th>
                    <th class="p-2">{{__('messages.t_pdf_quantity')}}</th>
                    <th class="p-2">{{__('messages.t_pdf_unit_price')}}</th>
                    {{-- @if ($displayTaxColumn)
                        <th class="p-2">tax</th>
                    @endif --}}
                    <th class="has-text-right py-2 pl-2">{{__('messages.t_pdf_amount')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->get('items') as $item)
                    <tr>
                        <td @class([
                            'align-top py-2 pr-2',
                            'has-border-bottom-light' =>!$loop->last,
                        ])>
                            <p><strong>{{ e(data_get($item,'name')) }}</strong></p>
                        </td>
                        <td @class(['nowrap align-top p-2', 'has-border-bottom-light'])>
                            <p>{{ e(data_get($item,'quantity')) }}</p>
                        </td>
                        <td @class(['nowrap align-top p-2', 'has-border-bottom-light'])>
                            <p>{{ e(data_get($item,'price')) }}</p>
                        </td>
                        <td @class([
                            'nowrap align-top has-text-right pl-2 py-2',
                            'has-border-bottom-light',
                        ])>
                            <p>{{ e(data_get($item,'total')) }}</p>
                        </td>
                    </tr>
                @endforeach

                @php
                    $colspan = false ? '3' : '2';
                @endphp

                <tr>
                    {{-- empty space --}}
                    <td class="py-2 pr-2"></td>
                    <td class="has-border-bottom-light p-2" colspan="{{ $colspan }}">
                        {{__('messages.t_pdf_subtotal')}}</td>
                    <td class="nowrap has-border-bottom-light has-text-right py-2 pl-2">
                        {{ e($invoice->get('subtotal')) }}
                    </td>
                </tr>
                @if ($invoice->get('discount'))
                <tr>
                    {{-- empty space --}}
                    <td class="py-2 pr-2"></td>
                    <td class="has-border-bottom-light p-2" colspan="{{ $colspan }}">
                        {{__('messages.t_total_discount')}}</td>
                        <td class="nowrap has-border-bottom-light has-text-right py-2 pl-2">
                            {{ e($invoice->get('discount')) }}
                        </td>
                </tr>
                @endif
                   @if ($invoice->get('tax'))

                <tr>
                    {{-- empty space --}}
                    <td class="py-2 pr-2"></td>
                    <td class="has-border-bottom-light p-2" colspan="{{ $colspan }}">
                        {{__('messages.t_tax')}}</td>
                    <td class="nowrap has-border-bottom-light has-text-right py-2 pl-2">
                        {{ e($invoice->get('tax')) }}
                    </td>
                </tr>
                @endif
                <tr>
                        <!-- empty space -->
                        <td class="py-2 pr-2"></td>
                    <td class="has-border-bottom-light p-2" colspan="{{ $colspan }}">
                        <strong>{{__('messages.t_pdf_total')}}</strong>
                    </td>
                    <td class="nowrap has-border-bottom-light has-text-right py-2 pl-2">
                        <strong>
                            {{ e($invoice->get('total')) }}
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- @if ($invoice->description)
            <p class="mb-1"><strong>{{ __('invoices::invoice.description') }}</strong></p>
            <p class="preline">{!! $invoice->description !!}</p>
        @endif --}}

    </div>
    <div class="container">
        <!-- Your existing invoice content -->
        <p class="mb-1">
            {{__('messages.t_pdf_footer_notes')}}
        </p>
    </div>
</body>

</html>
