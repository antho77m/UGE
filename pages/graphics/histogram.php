<script src="exports/exporting.js"></script>
<script src="exports/export-data.js"></script>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>
<script type="text/javascript">
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Impayés/Chiffre d\'affaires'
        },
        xAxis: {
            categories: <?php echo json_encode($array_dates); ?>,
            crosshair: true
        },
        yAxis: {
            title: {
                useHTML: true,
                text: 'Montant (€)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
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