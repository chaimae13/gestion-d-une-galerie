$(document).ready(function() {
    $('#crop-icon').click(function() {
        // Get the image path from the clicked image
        var imagePath = $('#popup-image').attr('src');
        var caption = $('#popup-image').attr('alt');
        // Prepare the data to be sent
        var data = {
            imagePath: imagePath,
            caption: caption
        };

        // Make an AJAX POST request to your Flask API
        $.ajax({
            type: 'POST',
            url: 'http://127.0.0.1:5000/image', // Remplacez par l'URL réelle de votre service Flask
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                // Handle the API response here
                console.log(response);

                // Assuming the API response contains the image data
                var imageData = response.imageData; // Adjust this based on your API's response format

                // Now you can use the image data as needed
                // For example, you can display it in an image tag
                $('#display-image').attr('src', 'data:image/png;base64,' + imageData);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
    });
});