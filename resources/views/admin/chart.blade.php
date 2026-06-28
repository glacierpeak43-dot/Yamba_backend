<div>
    <canvas id="myChart2" width="400" height="400"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    var array = @json($forums);

    const data = {
        labels: labels,
        datasets: [{
            label: 'Forums',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: array,
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            scale: {
                ticks: {
                    precision: 0,
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }

        }
    };
</script>
<script>
    const myChart = new Chart(
        document.getElementById('myChart2'),
        config
    );
</script>
