<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            width: 95%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #F9F6F5;
        }
        body {
            background-color: #E8DFE2;
            margin-left: 15vw;
        }
        a{
            color: #12233C;
        }
        a:hover{
            color: #E63365;
        }
    </style>
</head>
<body>
<div style="display: flex; flex-direction: row;">
    <div class="card">
        <img src="{{ $path }}" alt="Image" style="width: 90%;" />
        <!-- Add a container for the button -->
        <div class="button-container text-center">
            <a class="btn btn-primary" href="/gallery">Gallery</a>
        </div>
    </div>
    <div style="display: flex; flex-direction: column;">
        <div style="display: flex; flex-direction: row;">
            <div>
                <h2>Couleurs Dominant</h2>
                <div style="display: flex; margin-left: 10px; width: 90%;">
                    @foreach ($colors as $color)
                        <div style="width: 80px; height: 80px; background-color: {{ $color }};">
                            <p>{{ $color }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="width: 90%;">
                <h2>Moments de Couleur</h2>
                <table>
                    <tr>
                        <th>Caractéristique</th>
                        <th>Valeur</th>
                    </tr>
                    @foreach($moment as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="container">
            <h1>Histogram Charts</h1>
            <canvas id="combinedCanvas"></canvas>
        </div>
    </div>
</div>

<script>
    var histogramData = <?php echo json_encode($data); ?>;
    console.log('histogramData:', histogramData);
    console.log('histogramData.histogram1:', histogramData.histogram1);
    if (histogramData && histogramData.histogram1) {
        var combinedData = {
            labels: Array.from({ length: histogramData.histogram1.length }, (_, i) => i),
            datasets: [
                {
                    label: 'Histogram 1',
                    data: histogramData.histogram1,
                    borderColor: 'blue',
                    borderWidth: 2,
                },
                {
                    label: 'Histogram 2',
                    data: histogramData.histogram2,
                    borderColor: 'green',
                    borderWidth: 2,
                },
                {
                    label: 'Histogram 3',
                    data: histogramData.histogram3,
                    borderColor: 'red',
                    borderWidth: 2,
                },
            ]
        };

        var ctx = document.getElementById('combinedCanvas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: combinedData,
            options: {
                responsive: true,
            },
        });
    } else {
        // Handle the case where the data is not as expected, e.g., display an error message.
        console.error('Data is not in the expected format.');
    }
</script>

</body>
</html>
