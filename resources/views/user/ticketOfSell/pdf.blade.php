<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket de venta</title>
</head>
<body style="color: #408b88;">

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
    <table style="width: 100%;margin-top: 40px;font-size: 16px;">
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
    <table style="width: 100%;margin-top: 40px;">
        <tr style="background-color: #408b88; color: #ffffff;text-align: center;">
            <th style="width: 20%;">Tooth #</th>
            <th style="width: 60%;">Treatment</th>
            <th style="width: 20%;">Price</th>
        </tr>
        @foreach($ticketOfSell->ticketOfSellDetails as $detail)
            <tr style="color: #333333;">
                <td style="border: solid 1px #408b88;text-align: center;">{{ $detail->patientHistory->tooth }}</td>
                <td style="border: solid 1px #408b88;padding: 0 10px;">{{ $detail->patientHistory->product->name }}</td>
                <td style="border: solid 1px #408b88;text-align: center">
                    ${{ number_format($detail->patientHistory->price, 2) }}
                </td>
            </tr>
        @endforeach
    </table>

    <table style="width: 100%;margin-top: 15px;">
        <tr>
            <td style="width: 80%; text-align: right;">
                <strong>Total U.S. $:</strong>
            </td>
            <td style="border-bottom: solid 1px; width: 20%;text-align: center;">
                <span style="color: #333">{{ number_format($total, 2) }}</span>
            </td>
        </tr>
    </table>

    <!-- WARRANTY -->
    <table style="width: 100%;margin-top: 50px;">
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
    <table style="width: 100%;margin-top: 35px;">
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
    <table style="width: 100%;margin-top: 10px;">
        <tr>
            <td>
                <br><br>
                {{ Auth::user()->address }}
            </td>
            <td style="text-align: right">
                <br>
                {{ Auth::user()->phone }} <br>
                www.dentalsolutionstijuana.com
            </td>
        </tr>
    </table>
</body>
</html>