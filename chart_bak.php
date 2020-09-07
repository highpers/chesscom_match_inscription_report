<link href="vendor/flot-master/examples/examples.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.canvaswrapper.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.colorhelpers.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.saturated.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.browser.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.drawSeries.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.uiConstants.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.time.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/lib/globalize.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/lib/globalize.culture.en-US.js"></script>
<script type="text/javascript">
    $(function() {

        we = <?= $we_ch ?>;
        they = <?= $they_ch ?>;
        diff = <?= $diff_ch ?>

        <?php
        if (!empty($we_ch2)) {
        ?>
            diff2 = <?= $diff_ch2 ?>;
            we2 = <?= $we_ch2 ?>;

        <?php } ?>

        function doPlot(position) {
            $.plot("#placeholder", [{
                    data: we,
                    label: "<?= $team_label ?>"
                },
                {
                    data: they,
                    label: "<?= $match->rival ?>"
                },
                {
                    data: diff,
                    label: "<?= $diff_label ?>",
                    yaxis: 2

                },

                <?php

                if (!empty($we_ch2)) {
                ?> {
                        data: we2,
                        label: "<?= $team_label ?> (<?=$with_compromised?>)"
                    },
                    {
                        data: diff2,
                        label: "<?= $diff_label ?> (<?=$with_compromised?>)",
                        
                    },
                <?php   }
                ?>
            ], {
                series: {
                    lines: {
                        lineWidth: 4,

                    }
                },
                xaxes: [{
                    mode: "integer",

                }],
                yaxes: [{
                    min: 1
                }, {
                    // align if we are to the right
                    alignTicksWithAxis: position == "right" ? 1 : null,
                    position: position,
                    //tickFormatter: euroFormatter
                }],
                legend: {
                    position: "sw",
                    show: true,
                    labelFormatter: 'string',
                }
            });
        }

        doPlot("right");

        $("button").click(function() {
            var pos_text = 'left';
            if ($(this).text() == 'Derecha' || $(this).text() == 'Right') pos_text = 'right';
            doPlot(pos_text);
        });

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
    });
</script>
</head>

<body>



    <div id="content">

        <div class="demo-container">
            <div id="placeholder" class="demo-placeholder"></div>
        </div>


        <p><?= $position_axis ?>: &nbsp;<button><?= $left ?></button> - <button><?= $right ?></button>.</p>

    </div>

    <div id="footer">
        Copyright &copy; 2007 - 2014 IOLA and Ole Laursen
    </div>

</body>

</html>