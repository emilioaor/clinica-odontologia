<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>

    <p>
        Hello,
    </p>

    <p>
        This email is a  confirmation of the appointment you have schedule with Dr.
        {{ $appointment->doctor->name }}. In Our office location:
    </p>

    <p>
        #160 3rd street between A and B, los Algodones, Baja California.
    </p>

    <p>
        <strong>
            Date: {{ $appointment->date->format('l jS F Y \a\t g:i a') }}
        </strong>
    </p>

    <p>
        If you can not keep this appointment please call Our office at: 928 328 1121 and
        we will be happy to re schedule you for a more convenient time.
    </p>

    <p>
        Our facility is located on the friendly border town of Los Algodones, Baja California,
        Mexico (<strong>seven miles west of Yuma, Arizona</strong>); the fastest growing place for
        medical tourism.Our clinic is located <strong>just two blocks from the border crossing</strong>, in a tourist
        area safe for walking. Is a very big difference compared to other border or big cities in comfort, attention
        and security especially. Attached you will find a map with directions to our facilities. If
        you wish, you can call us and <strong>a member of our staff will meet you at the border to direct you
        to our clinic</strong>. Please note that the proximity to the United States is a geographic advantage as
        well, that force us to meet high standards of safety, quality, English fluently, etc. Hard to
        find with other options.
    </p>

    <p>
        To reach Dental Solutions, cross the border into Los Algodones, take right on the first block which
        is "A" Street , then take left on the next block which is "3rd Street", keep heading straight
        approximately 50 meter almost reaching the middle of the block, and you will have our building on your right.
    </p>

    <img
            style="width: 100%"
            src="{{ asset('img/map.jpg') }}"
            alt="Mapa">
    
    <p>
        If you have further questions I'm glad to answer.
    </p>

    <p>
        Thanks in advance,
    </p>

    <p>
        Dental Solutions <br>
        (928) 328 1121
    </p>
</body>
</html>