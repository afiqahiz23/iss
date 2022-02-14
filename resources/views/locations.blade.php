<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

    </style>
</head>

<body class="">
    <div class="">
        <h1 class="">Where was the International Space Station (ISS)?</h1>
        <h4 class="">The ISS orbits 400 km above the Earth at approximately 29,000 km/h. It is the third brightest object
in our sky, making it possible to catch a glimpse of the ISS from ground level</h4>
        <form action="/location">
            <h4 class="">Select past time and date to receive the city the ISS was over at that moment</h4>
            <div>
                <label for="start_date">Select Date </label>
                <input type="date" name="date">
            </div>
            <div>
                <label for="time">Select Time </label>
                <input type="time" name="time">
            </div>
            <button type="submit">Submit</button>
        </form>
        <br>
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Time</td>
                    <td>Coordinates</td>
                    <td>Country Code</td>
                    <td>Continent/City</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($positions as $i => $position)
                    <tr>

                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($position['timestamp'])->format('d-m-Y H:i') }}
                        </td>
                        <td>{{ $position['latitude'] }},{{ $position['longitude'] }}</td>
                        <td>{{ $position['location_details']['country_code'] }}</td>
                        <td>{{ $position['location_details']['timezone_id'] }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <br>
        <div id="map" style="width: 100vw;height:100vh;display:block"></div>

    </div>

    <script>
        // This example creates a 2-pixel-wide red polyline showing the path of
        // the first trans-Pacific flight between Oakland, CA, and Brisbane,
        // Australia which was made by Charles Kingsford Smith.
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 3,
                center: {
                    lat: 0,
                    lng: -180
                },
                mapTypeId: "terrain",
            });
            const flightPlanCoordinates = {!! json_encode(
    collect($positions)->map(function ($pos) {
        return [
            'lat' => $pos['latitude'],
            'lng' => $pos['longitude'],
        ];
    }),
) !!}
            const flightPath = new google.maps.Polyline({
                path: flightPlanCoordinates,
                geodesic: true,
                strokeColor: "#FF0000",
                strokeOpacity: 1.0,
                strokeWeight: 2,
            });

            flightPath.setMap(map);
        }
    </script>
    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9A7t9tTMdlG0N-9h6R1sJUkT_fVRbe8Y&callback=initMap&v=weekly"
        async></script>
</body>

</html>
