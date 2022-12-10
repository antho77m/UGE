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
            text: 'Evolution du solde de votre compte des 15 derniers jours'
        },
        xAxis: {
            categories: <?php echo json_encode($array_date); ?>
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
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Solde',
            data: <?php echo json_encode($array_montant); ?>
        }]
    });
</script>