<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $budget->public_id }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background-color: #FFFFFF;">

    <table style="width: 100%;">
        <!-- HEADER -->
        <tr style="color: #FFFFFF;">
            <td style="width: 30%; background-color: #34a7ac">
                <!-- Title -->
                <h1 style="text-align: center;">
                    {{ $budget->title }}
                </h1>
            </td>
            <td style="width: 70%;">

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 100%; text-align: right;padding-bottom: 20px;" colspan="3">
                            <!-- Logo -->
                            <img    style="width: 150px;"
                                    src="{{ asset('uploads/' . Auth::user()->logo ) }}"
                                    >
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 33.3333333%">
                            <!-- Phone -->
                            <p style=";width: 96%; background-color: #38a9a8;height: 80px;margin: 0 0 0 4%;padding-top: 35px;text-align: center;font-size: 13px;position: relative;">
                                <i class="glyphicon glyphicon-phone" style="position:absolute;top: 20px;left: 65px;font-size: 30px;"></i>
                                <br>
                                {{ Auth::user()->phone }}
                            </p>
                        </td>
                        <td style="width: 33.3333333%">
                            <!-- Address -->
                            <p style=";width: 96%; background-color: #408b88;height: 80px;margin: 0 0 0 4%;padding-top: 35px;text-align: center;font-size: 13px;position: relative;">
                                <i class="glyphicon glyphicon-map-marker" style="position:absolute;top: 20px;left: 65px;font-size: 30px;"></i>
                                <br>
                                {{ Auth::user()->address }}
                            </p>
                        </td>
                        <td style="width: 33.3333333%">
                            <!-- Email -->
                            <p style=";width: 96%; background-color: #277f6a;height: 80px;margin: 0 0 0 4%;padding-top: 35px;text-align: center;font-size: 13px;position: relative;">
                                <i class="glyphicon glyphicon-envelope" style="position:absolute;top: 20px;left: 65px;font-size: 30px;"></i>
                                <br>
                                {{ Auth::user()->email }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
        <!-- /HEADER -->

        <!-- INFO -->
    <table style="width: 100%">
        <tr>
            <td style="width: 100%;">

                <!-- Client Info  -->
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;">

                            <!-- (Nombre, Telefono, Email) -->
                            <p style="margin: 25px 0 0 0;font-size: 16px;">
                                <strong>
                                    {{ Auth::user()->business_name }}
                                </strong>
                            </p>
                            <p style="margin: 0;">
                                <strong>
                                    {{ $budget->client_label }}
                                </strong>
                                {{ $budget->patient->name }}
                            </p>
                            <p style="margin: 0;">
                                <strong>
                                    {{ $budget->client_phone_label }}
                                </strong>
                                {{ $budget->patient->phone }}
                            </p>
                            <p style="margin: 0;">
                                @if(! empty($budget->patient->email))
                                    <strong>
                                        {{ $budget->client_email_label }}
                                    </strong>
                                    {{ $budget->patient->email }}
                                @endif
                            </p>
                            <!-- /(Nombre, Telefono, Email) -->

                        </td>
                        <td style="width: 50%;">

                            <!-- (Numero, Fecha de emision) -->
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 60%;">
                                        <p style="text-align: right;color: #699797;font-weight: 600;margin-top: 25px;margin-bottom: 0;">
                                            N°:
                                        </p>
                                    </td>
                                    <td style="width: 40%;">
                                        <p style="text-align: right;font-weight: 800;margin-top: 25px;margin-bottom: 0;">
                                            #{{ $budget->public_id }}
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 80%;">
                                        <p style="text-align: right;color: #699797;font-weight: 600;margin-bottom: 0;">
                                            {{ $budget->creation_date_label }}
                                        </p>
                                    </td>
                                    <td style="width: 20%;">
                                        <p style="text-align: right;font-weight: 800;margin-bottom: 0;">
                                            {{ (new \DateTime($budget->creation_date_value))->format('m/d/Y') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <!-- /(Numero, Fecha de emision) -->

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- /INFO -->
    </table>


    <!-- TABLE -->
    <table style="width: 100%;margin-top: 30px;">
        <tr style="background-color: #3ebbb5; text-align: center;color: #FFFFFF;height: 40px;">
            <td style="width: 10%;height: 40px;font-weight: bold;font-size: 16px;">
                TH
            </td>
            <td style="width: 36%;height: 40px;font-weight: bold;font-size: 16px;text-align: left;">
                {{ $budget->table_description_label }}
            </td>
            <td style="width: 15%;height: 40px;font-weight: bold;font-size: 16px;">
                {{ $budget->table_quantity_label }}
            </td>
            <td style="width: 20%;height: 40px;font-weight: bold;font-size: 16px;">
                {{ $budget->table_price_label }}
            </td>
            <td style="width: 20%;height: 40px;font-weight: bold;font-size: 16px;">
                {{ $budget->table_total_label }}
            </td>
        </tr>
        <!-- Detail -->
        @foreach($budget->budgetDetails as $i => $detail)
            <tr style="height: 30px;color: #222;{{ ($i + 1)%2 == 0 ? 'background-color:#dcdcdc;' : '' }}">
                <td style="text-align: center;height: 30px;vertical-align: middle;padding-bottom: 10px;">
                    {{ $detail->tooth > 0 ? $detail->tooth : '' }}
                </td>
                <td style="height: 30px;vertical-align: middle;padding-bottom: 10px;">
                    {{ $detail->product->name }}
                </td>
                <td style="text-align: center;height: 30px;vertical-align: middle;padding-bottom: 10px;">
                    {{ $detail->quantity }}
                </td>
                <td style="text-align: center;height: 30px;vertical-align: middle;padding-bottom: 10px;">
                    ${{ number_format($detail->price, 2) }} USD
                </td>
                <td style="text-align: center;height: 30px;vertical-align: middle;padding-bottom: 10px;">
                    ${{ number_format($detail->price * $detail->quantity, 2) }} USD
                </td>
            </tr>
        @endforeach
        <!-- /Detail -->

        <!-- Footer -->
        @if(!empty($budget->shaping_value) || !empty($budget->discount_value))
            <tr style="background-color: #66bcc8;color: #FFFFFF;height: 35px;font-weight: bold;">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;">
                    {{ $budget->subtotal_footer_label }}
                </td>
                <td style="text-align: center;">
                    ${{ number_format($budget->subtotal_footer_value, 2) }} USD
                </td>
            </tr>
        @endif
        @if(!empty($budget->discount_value))
            <tr style="background-color: #66bcc8;color: #FFFFFF;height: 35px;font-weight: bold;">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;height: 35px;">
                    {{ $budget->discount_label }}
                    @if($budget->discount_type === 1)
                        ({{ $budget->discount_value }}%)
                    @endif
                </td>
                <td style="text-align: center;height: 35px;">
                    @if($budget->discount_type === 1)
                        ${{ number_format($budget->subtotal_footer_value * ($budget->discount_value / 100), 2) }} USD
                    @else
                        ${{ number_format($budget->discount_value, 2) }} USD
                    @endif
                </td>
            </tr>
        @endif
        @if(!empty($budget->shaping_value))
            <tr style="background-color: #66bcc8;color: #FFFFFF;height: 35px;font-weight: bold;">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;height: 35px;">
                    {{ $budget->shaping_label }}
                </td>
                <td style="text-align: center;height: 35px;">
                    ${{ number_format($budget->shaping_value, 2) }} USD
                </td>
            </tr>
        @endif

        <tr style="background-color: #66bcc8;color: #FFFFFF;height: 35px;font-weight: bold;">
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: center;height: 35px;">
                {{ $budget->total_footer_label }}
            </td>
            <td style="text-align: center;height: 35px;">
                ${{ number_format($budget->total_footer_value, 2) }} USD
            </td>
        </tr>
        <!-- /Footer -->
    </table>
    <!-- /TABLE -->

    <table style="width: 100%;margin-top: 30px;">
        @if(!empty($budget->notes_value))
            <tr>
                <td>
                    <p style="margin: 0;font-weight: bold;font-size: 12px;">
                        {{ $budget->notes_label }}
                    </p>
                    <p style="margin: 0;">
                        {{ $budget->notes_value }}
                    </p>
                </td>
            </tr>
        @endif
        @if(!empty($budget->terms_value))
            <tr>
                <td style="padding-top: 30px;">
                    <p style="margin: 0;font-weight: bold;font-size: 12px;">
                        {{ $budget->terms_label }}
                    </p>
                    <p style="margin: 0;">
                        {{ $budget->terms_value }}
                    </p>
                </td>
            </tr>
        @endif
    </table>

    <h4 style="background-color: #66bcc8;color: #FFFFFF;padding: 10px;text-align: center;font-weight: bold;margin-top: 70px;position: absolute;bottom: 0;left: 0;width: 100%;">
        This estimate is valid for 10 days
    </h4>
</body>
</html>