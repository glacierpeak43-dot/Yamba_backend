<div>
    <canvas id="articleChart" width="400" height="400"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const articleLabels = [
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

    var articles = @json($articles);

    const articleData = {
        labels: articleLabels,
        datasets: [{
            label: 'Articles',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: articles,
        }]
    };

    const articleConfig = {
        type: 'line',
        data: articleData,
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
    const articleChart = new Chart(
        document.getElementById('articleChart'),
        articleConfig
    );
</script>
