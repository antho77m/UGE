<script src="exports/exporting.js"></script>
<script src="exports/export-data.js"></script>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>
<script type="text/javascript">
    Highcharts.chart('container', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Impayés/Chiffre d\'affaires'
        },
        xAxis: {
            categories: <?php echo json_encode($array_dates); ?>        
        },
        yAxis: {
            title: {
            text: 'Montant (€)'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: 'Impayés',
            data: <?php echo json_encode($array_impayes); ?>
        }, {
            name: 'Chiffre d\'affaire',
            data: <?php echo json_encode($array_chiffre_affaires); ?>
        }]
    });
</script>