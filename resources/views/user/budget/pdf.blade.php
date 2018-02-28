<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $budget->public_id }}</title>
</head>
<body style="font-family: Raleway, sans-serif; color: #303030; font-size: 14px;">

    <table style="width: 100%">
        <tr>
            <td style="width: 50%">
                @if(! empty($budget->business_logo))
                    <img src="{{ public_path('uploads') . '/' . $budget->business_logo }}" style="width: 150px">
                @endif
                <p style="font-weight: bold; font-size: 25px;margin-top: 10px;">
                    {{ $budget->business_name }}
                </p>

                <p style="margin-bottom: 0;margin-top: 50px; color: #777;">
                    {{ $budget->client_label }}
                </p>
                <p style="margin-top: 10px; font-weight: bold;">
                    {{ $budget->client_value }}
                </p>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <h1 style="text-align: right; color: #303030; font-size: 30px;margin-bottom: 0;margin-top: 0;">
                    {{ $budget->title }}
                </h1>
                <h3 style="text-align: right; color: #aaaaaa;margin-top: 0;font-size: 15px;">
                    #{{ $budget->public_id }}
                </h3>

                <!-- Anidada -->
                <table style="width: 100%;float: right;margin-top: 60px;">
                    @if(!empty($budget->creation_date_value))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0;">
                                    {{ $budget->creation_date_label }}
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0;">
                                    {{ (new \DateTime($budget->creation_date_value))->format('d-m-Y') }}
                                </p>
                            </td>
                        </tr>
                    @endif
                    @if(!empty($budget->expiration_date_value))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0;">
                                    {{ $budget->expiration_date_label }}
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0;">
                                    {{ (new \DateTime($budget->expiration_date_value))->format('d-m-Y') }}
                                </p>
                            </td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold;">
                        <td style="width: 60%;padding-right: 20px; font-size: 20px;padding-bottom: 10px;background-color: #f4f4f4;">
                            <p style="text-align: right;margin-bottom: 0;">
                                {{ $budget->total_head_label }}
                            </p>
                        </td>

                        <td style="width: 40%;background-color: #f4f4f4;">
                            <p style="margin-bottom: 0;padding-bottom: 10px;">
                                {{ $budget->currency_symbol . ' ' . $budget->total_head_value }}
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- /Anidada -->

            </td>
        </tr>
    </table>

    <table style="width: 100%;margin-top: 30px;" border="0">
        <thead style="background-color: #222; border: none;color: #FFFFFF">
        <tr>
            <th width="51%">
                <p style="margin: 0; padding: 3px;">
                    {{ $budget->table_description_label }}
                </p>
            </th>
            <th width="15%">
                <p style="margin: 0; padding: 3px;">
                    {{ $budget->table_quantity_label }}
                </p>
            </th>
            <th width="17%">
                <p style="margin: 0; padding: 3px;">
                    {{ $budget->table_price_label }}
                </p>
            </th>
            <th width="17%">
                <p style="margin: 0; padding: 3px;">
                    {{ $budget->table_total_label }}
                </p>
            </th>
        </tr>
        </thead>
        <tbody>
            @foreach($budget->budgetDetails as $detail)
                <tr style="font-size: 13px;">
                    <td>
                        <p style="margin: 0; padding: 3px;">
                            {{ $detail->product->name }}
                        </p>
                    </td>
                    <td>
                        <p style="margin: 0; padding: 3px;">
                            {{ $detail->quantity }}
                        </p>
                    </td>
                    <td>
                        <p style="margin: 0; padding: 3px;">
                            {{ $budget->currency_symbol . ' ' . $detail->price }}
                        </p>
                    </td>
                    <td>
                        <p style="margin: 0; padding: 3px;">
                            {{ $budget->currency_symbol . ' ' . ($detail->quantity * $detail->price) }}
                        </p>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%">
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%">

                <!-- Anidada -->
                <table style="width: 100%;margin-top: 60px;">
                    <tr>
                        <td style="width: 60%;padding-right: 20px; color: #777;">
                            <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                {{ $budget->subtotal_footer_label }}
                            </p>
                        </td>

                        <td style="width: 40%">
                            <p style="margin-bottom: 0; margin-top: 5px;">
                                {{ $budget->currency_symbol . ' ' . $budget->subtotal_footer_value }}
                            </p>
                        </td>
                    </tr>
                    @if(($discount = 0) || (!empty($budget->discount_value)))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->discount_label }}
                                    @if($budget->discount_type === 1)
                                        ({{ $budget->discount_value }}%)
                                    @endif
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->currency_symbol }}
                                    @if($budget->discount_type === 1)
                                        {{ $discount = $budget->subtotal_footer_value * ($budget->discount_value / 100) }}
                                    @elseif($budget->discount_type === 2)
                                        {{ $discount = $budget->discount_value }}
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endif
                    @if(($tax = 0) || (!empty($budget->tax_value)))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->tax_label }}
                                    @if($budget->tax_type === 1)
                                        ({{ $budget->tax_value }}%)
                                    @endif
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->currency_symbol }}
                                    @if($budget->tax_type === 1)
                                        {{ ($budget->subtotal_footer_value - $discount) * ($tax = $budget->tax_value / 100) }}
                                    @elseif($budget->tax_type === 2)
                                        {{ $tax = $budget->tax_value }}
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endif
                    @if(!empty($budget->shaping_value))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->shaping_label }}
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->currency_symbol . ' ' . $budget->shaping_value }}
                                </p>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="width: 60%;padding-right: 20px; color: #777;">
                            <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                {{ $budget->total_footer_label }}
                            </p>
                        </td>

                        <td style="width: 40%">
                            <p style="margin-bottom: 0; margin-top: 5px;">
                                {{ $budget->currency_symbol . ' ' . $budget->total_footer_value }}
                            </p>
                        </td>
                    </tr>

                    @if(!empty($budget->amount_paid_value))
                        <tr>
                            <td style="width: 60%;padding-right: 20px; color: #777;">
                                <p style="text-align: right;margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->amount_paid_label }}
                                </p>
                            </td>

                            <td style="width: 40%">
                                <p style="margin-bottom: 0; margin-top: 5px;">
                                    {{ $budget->currency_symbol . ' ' . $budget->amount_paid_value }}
                                </p>
                            </td>
                        </tr>
                    @endif
                </table>
                <!-- /Anidada -->

            </td>
        </tr>
    </table>

    @if(!empty($budget->notes_value))
        <table style="width: 100%;margin-top: 20px;">
            <tr>
                <td style="width: 100%">
                    <p style="margin-top: 10px;margin-bottom: 0;color: #777";>
                        {{ $budget->notes_label }}
                    </p>
                    <p style="margin-top: 10px;margin-bottom: 0">
                        {{ $budget->notes_value }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

    @if(!empty($budget->terms_value))
        <table style="width: 100%;margin-top: 20px;">
            <tr>
                <td style="width: 100%">
                    <p style="margin-top: 10px;margin-bottom: 0;color: #777";>
                        {{ $budget->terms_label }}
                    </p>
                    <p style="margin-top: 10px;margin-bottom: 0">
                        {{ $budget->terms_value }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

</body>
</html>