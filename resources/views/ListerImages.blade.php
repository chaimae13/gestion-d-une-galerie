@extends('layout')
@section('title', 'Gallery')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .dream {
            column-width: 320px;
            column-gap: 15px;
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
        }

        .head {
            margin-top: 10px;
            
        }

        .dream figure {
            background: #fefefe;
            box-shadow: 0 1px 2px rgba(34, 25, 25, 0.4);
            margin: 0 2px 15px;
            padding: 5px;
            padding-bottom: 10px;
            transition: opacity .4s ease-in-out;
            display: inline-block;
        }

        .dream .form-group {
            display: none;
        }

        .dream figure img {
            width: 100%;
            height: auto;
           
        }
    </style>
</head>
@section('content')

<body>
    <div class="container">
        <form action="{{ route('feedback') }}" method="POST">
            @csrf
            <div class="head">
                <h1 class="mb-4" style=" margin-top: 10px; float: inline-start;" >Image Search Results</h1>
                <button class="btn btn-dark" id="toggleFeedback" type="button" style="margin-top: 10px;  float: inline-end;" >Feedback</button>
                <button class="btn btn-dark" id="saveButton" type="button" style="margin-top: 10px;  float: inline-end;display:none; " >Save</button>
            </div>
            <input type="hidden" name="photo_id" value="{{ $photo->id }}">
            <input type="hidden" name="topImageNames" value="{{ json_encode($topImageNames) }}">
            <div class="box">
                @if ($topImageNames)
                <div class="dream">
                    @foreach ($topImageNames as $nom)
                    <figure>
                        <img src="{{ asset('/storage/photos/' . $nom) }}" class="card-img-top" alt="Photo">
                        <div class="form-group" style="padding: 7px;">
                            <label for="feedbackSelect{{ $loop->iteration }}">Feedback</label>
                            <select class="form-control" id="feedbackSelect{{ $loop->iteration }}" name="feedback[]">
                                <option value="relevant">Relevant</option>
                                <option value="irrelevant">Irrelevant</option>
                                <option value="neutral">Neutral</option>
                            </select>
                        </div>
                    </figure>
                    @endforeach
                </div>
                @else
                <div class="col">
                    <p>No similar images found.</p>
                </div>
                @endif
            </div>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleFeedbackButton = document.getElementById('toggleFeedback');
        const saveButton = document.getElementById('saveButton');
        const formGroups = document.querySelectorAll('.dream .form-group');

        toggleFeedbackButton.addEventListener('click', function() {
            formGroups.forEach(formGroup => {
                formGroup.style.display = 'block';
            });
            saveButton.style.display = 'inline-block';
            toggleFeedbackButton.style.display = 'none';
        });
    });
</script>
</body>

</html>
@endsection
