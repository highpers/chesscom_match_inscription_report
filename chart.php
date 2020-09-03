
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

			var we = [
				[1, 2012],
				[2, 2003],
				[3, 1687],
				[4, 1654],
				[5, 1434]

			]

			var we2 = [
				[1, 2101],
				[2, 2012],
				[3, 1988],
				[4, 1923],
				[5, 1687],

			]



			var they = [
				[1, 2132],
				[2, 1996],
				[3, 1808],
				[4, 1744],
				[5, 1579]

			]

			var dif = [

				[1, -29],
				[2, 16],
				[3, 180],
				[4, 179],
				[5, 108],

			]


			function euroFormatter(v, axis) {
				return v.toFixed(axis.tickDecimals) + "€";
			}

			function doPlot(position) {
				$.plot("#placeholder", [{
						data: we,
						label: "Team Argentina"
					},
					{
						data: we2,
						label: "Team Argentina (with compromised)"
					},
					{
						data: they,
						label: "Doichlan forros",
					},
					{
						data: dif,
						label: "Difference",
						yaxis: 2

					}
				], {
					series: {
						lines: {
							lineWidth: 3
						}
					},
					xaxes: [{
						mode: "integer",
						timeBase: "1"
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
						position: "sw"
					}
				});
			}

			doPlot("right");

			$("button").click(function() {
				doPlot($(this).text());
			});

			// Add the Flot version string to the footer

			$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
		});
	</script>
</head>

<body>

	<div id="header">
		<h2>Multiple axes</h2>
	</div>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>

		<p>Multiple axis support showing the raw oil price in US $/barrel of crude oil vs. the exchange rate from US $ to €.</p>

		<p>As illustrated, you can put in multiple axes if you need to. For each data series, simply specify the axis number. In the options, you can then configure where you want the extra axes to appear.</p>

		<p>Position axis <button>left</button> or <button>right</button>.</p>

	</div>

	<div id="footer">
		Copyright &copy; 2007 - 2014 IOLA and Ole Laursen
	</div>

</body>

</html>