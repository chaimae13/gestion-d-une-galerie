<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
</head>
@extends('layout')
@section('title', 'Descriptors')
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('css/photos.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="shortcut icon" href="{{ asset('images/camera.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background-color: #f2f2f2;
        }
        
    </style>
   
</head>
@section('content')
<div class="container d-flex justify-content-center">
    <div class="box " id="photoGallery">
        <div class="dream ">
        @foreach ($path as $photo)
               
                    <div class="photo-item">
                        <figure>
                            
                            <img src="{{ asset('storage/photos/' . basename($photo)) }}" class="card-img-top" alt="Photo" >
                          
    
                        </figure>
                    </div>
                
            @endforeach
        </div>
         
    </div>
</div>
   
<div style="display: flex; flex-direction: column;">
<div style="display: flex; flex-direction: row;">
<div>
<h2>Couleurs Dominant</h2>
<div style="display: flex; margin-left: 10px;width: 90%;">
    @foreach ($colors as $color)
        <div style="width: 80px; height: 80px; background-color: {{ $color }};">
        <p>{{ $color }}</p>
        </div>
    @endforeach
</div></div>
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

    </div></div>

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
        console.error('Data is not in the expected format.');
    }
</script>

</body>
</html>