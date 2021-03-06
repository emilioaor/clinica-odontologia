<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket de venta</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body style="color: #408b88;font-size: 12px;font-family: 'Open Sans', sans-serif;">

    <!-- HEADER -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;">
                <h1 style="margin: 0;">{{ Auth::user()->business_name }}</h1>
                <h3 style="margin-top: 0;font-weight: 300;">Implantology Specialist</h3>
            </td>
            <td style="width: 40%;text-align: right;">
                <img    style="width: 120px;"
                        src="{{ asset('uploads/' . Auth::user()->logo ) }}"
                >
            </td>
        </tr>
    </table>


    <!-- INFO -->
    <table style="width: 100%;margin-top: 15px;">
        <tr>
            <td style="width: 80px;">
                <strong>Name:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">{{ $ticketOfSell->patient->name }}</span>
            </td>
            <td style="width: 50px; padding-left: 10px;">
                <strong>Date:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">{{ $ticketOfSell->created_at->format('m/d/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">
                <strong>Cell Phone:</strong>
            </td>
            <td style="padding-top: 10px;border-bottom: solid 1px;">
                <span style="color: #333">{{ $ticketOfSell->patient->phone }}</span>
            </td>
            <td style="padding-top: 10px; width: 50px; padding-left: 10px;">
                <strong>Email:</strong>
            </td>
            <td style="padding-top: 10px;border-bottom: solid 1px;">
                <span style="color: #333">{{ $ticketOfSell->patient->email }}</span>
            </td>
        </tr>
    </table>

    <!-- DETAIL -->
    <table style="width: 100%;margin-top: 20px;">
        <tr style="background-color: #408b88; color: #ffffff;text-align: center;">
            <th style="width: 30%;">Tooth #</th>
            <th style="width: 50%;">Treatment</th>
            <th style="width: 20%;">Price</th>
        </tr>
        @foreach($ticketOfSell->ticketOfSellDetails as $detail)
            <tr style="color: #333333;">
                <td style="border: solid 1px #408b88;text-align: center;">{{ \App\TicketOfSell::textWrap($detail->patientHistory->tooth) }}</td>
                <td style="border: solid 1px #408b88;padding: 0 10px;">{{ $detail->patientHistory->product->name }}</td>
                <td style="border: solid 1px #408b88;text-align: center">
                    ${{ number_format($detail->patientHistory->price, 2) }}
                </td>
            </tr>
        @endforeach
    </table>

    <table style="width: 100%;margin-top: 5px;">
        @if($discount > 0)
            <tr>
                <td style="width: 80%; text-align: right;">
                    <strong>Subtotal U.S. $:</strong>
                </td>
                <td style="border-bottom: solid 1px; width: 20%;text-align: center;">
                    <span style="color: #333">{{ number_format($subtotal, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td style="width: 80%; text-align: right;">
                    <strong>Discount U.S. $:</strong>
                </td>
                <td style="border-bottom: solid 1px; width: 20%;text-align: center;">
                    <span style="color: #333">-{{ number_format($discount, 2) }}</span>
                </td>
            </tr>
        @endif
        <tr>
            <td style="width: 80%; text-align: right;">
                <strong>Total U.S. $:</strong>
            </td>
            <td style="border-bottom: solid 1px; width: 20%;text-align: center;">
                <span style="color: #333">{{ number_format($total, 2) }}</span>
            </td>
        </tr>
    </table>

    <table style="width: 100%;margin-top: 10px;">
        <tr>
            <td style="width: 80px;">
                <strong>Payment:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">{{ implode(', ', $paymentMethods) }}</span>
            </td>
            <td style="width: 80px;padding-left: 20px;">

            </td>
            <td style="">

            </td>
        </tr>
        <tr>
            <td style="width: 80px;">
                <strong>Cashier:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">{{ Auth::user()->name }}</span>
            </td>
            <td style="width: 80px;padding-left: 20px;">
                <strong>Paid:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">${{ number_format($paid, 2) }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 80px;">
                <strong>Date:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">{{ date('m/d/Y') }}</span>
            </td>
            <td style="width: 80px;padding-left: 20px;">
                <strong>Balance:</strong>
            </td>
            <td style="border-bottom: solid 1px;">
                <span style="color: #333">${{ number_format($balance, 2) }}</span>
            </td>
        </tr>
    </table>

    <!-- WARRANTY -->
    <table style="width: 100%;margin-top: 20px;">
        <tr>
            <td colspan="2" style="text-align: justify;background-color: #408b88; color: #ffffff;padding: 10px;">
                ***WARRANTY. Dental Solutions offers an extended warranty of up to 1
                year on dental treatments. I was explained and understand that I have to
                be seen at any of Dental Solutions dental clinic at least once a year for a
                regular cleaning and check up in order to maintain such warranty, if I
                were to fall on my responsibilities of such dental appointment I would
                lose my extended warranty.
            </td>
        </tr>
    </table>

    <!-- SIGNATURE -->
    <table style="width: 100%;margin-top: 25px;">
        <tr>
            <td style="width: 140px;">
                <strong>Patient's Signature:</strong>
            </td>
            <td style="border-bottom: solid 1px;">

            </td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- FOOTER -->
    <table style="width: 100%;margin-top: 10px;position: fixed;left: 0; bottom: 70px;">
        <tr>
            <td>
                <br><br>
                {{ Auth::user()->address }}
            </td>
            <td style="text-align: right">
                <br>
                {{ Auth::user()->phone }} <br>
                {{ str_replace('https://', '', str_replace('http://', '', Request::root()))  }}
            </td>
        </tr>
    </table>
</body>
</html>