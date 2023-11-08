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
<div style="display: flex;">
    @foreach ($colors as $color)
        <div style="width: 100px; height: 100px; background-color: {{ $color }};">
        <p>{{ $color }}</p>
        </div>
    @endforeach
</div>

<h1>Histogram Charts</h1>
    <div class="container">
        <canvas id="combinedCanvas"></canvas>
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
