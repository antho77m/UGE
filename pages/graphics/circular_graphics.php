<script src="exports/exporting.js"></script>
<script src="exports/export-data.js"></script>
<figure class="highcharts-figure POGraph__graph">
    <div id="container"></div>
</figure>
<div style="display: block; margin-top: 15vh; visibility: hidden;">ecart</div>

<script type="text/javascript">
    Highcharts.chart('container', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Représentation des impayés par motifs d\'impayés'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Nombre',
            colorByPoint: true,
            data: [{ 
                name: 'fraude à la carte',
                y: <?php echo $array_motifs['fraude a la carte']; ?>
            },  {
                name: 'compte à découvert',
                y: <?php echo $array_motifs['compte a decouvert']; ?>
            },  {
                name: 'compte clôturé',
                y: <?php echo $array_motifs['compte cloture']; ?>
            }, {
                name: 'compte bloqué',
                y: <?php echo $array_motifs['compte bloque']; ?>
            }, {
                name: 'provision insuffisante',
                y: <?php echo $array_motifs['provision insuffisante']; ?>
            }, {
                name: 'opération contestée par le débiteur',
                y: <?php echo $array_motifs['operation contestee par le debiteur']; ?>
            }, {
                name: 'titulaire décédé',
                y: <?php echo $array_motifs['titulaire decede']; ?>
            }, {
                name: 'raison non communiquée, contactez la banque du client',
                y: <?php echo $array_motifs['raison non communiquee, contactez la banque du client']; ?> 
            }]
        }]
    });
</script>