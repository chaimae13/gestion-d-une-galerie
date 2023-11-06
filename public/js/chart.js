
var  xmlhttp = new XMLHttpRequest()
var url = "http://127.0.0.1:5000/image"
xmlhttp.open("GET",url,true)
xmlhttp.send()
xmlhttp.onreadystatechange = function(){
M    var data = JSON.parse(this.responseText);
    var histogram1 = data.histogram1;
    var histogram2 = data.histogram2;
    var histogram3 = data.histogram3;
  }
  console.log(histogram1)
}


// const ctx = document.getElementById('canvas');

// // Function to fetch data from the API
// async function fetchData() {
//   try {
//     console.log("am here0")

//     const response = await fetch('http://127.0.0.1:5000/image', {
//       method: 'POST',
//       headers: {
//         'Content-Type': 'application/json',
//       },
//       body: JSON.stringify({ imagePath: 'C:/Users/hp/Pictures/hey.jpeg' }), // Replace with the actual image path
//     });
//         console.log("am here0")

//     console.log("am here")
//     if (!response.ok) {
//       throw new Error(`HTTP error! Status: ${response.status}`);
//     }
//     console.log("am here2")

//     const data = await response.json();

//     // Create the chart using the fetched data
//     createChart(data);
//   } catch (error) {
//     console.error('Error fetching data:', error);
//   }
// }

// // Function to create the chart
// function createChart(data) {
//   const chartData = {
//     type: 'line',
//     data: {
//       labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
//       datasets: [
//         {
//           label: 'histogram 1',
//           data: data['histogram 1'][0],
//           borderWidth: 2,
//           borderColor: 'red',
//         },
//         {
//           label: 'histogram 2',
//           data: data['histogram 2'][0],
//           borderWidth: 2,
//           borderColor: 'green',
//         },
//         {
//           label: 'histogram 3',
//           data: data['histogram 3'][0],
//           borderWidth: 2,
//           borderColor: 'blue',
//         },
//       ],
//     },
//     options: {
//       elements: {
//         line: {
//           tension: 0,
//         },
//       },
//       scales: {
//         y: {
//           beginAtZero: true,
//         },
//       },
//     },
//   };

//   new Chart(ctx, chartData);
// }

// // Call the fetchData function to fetch and display the data
// fetchData();
