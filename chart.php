<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Flot Examples: Toggling Series</title>
	<link href="vendor/flot-master/examples.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.canvaswrapper.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.colorhelpers.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.saturated.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.browser.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.drawSeries.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.uiConstants.js"></script>
	<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.legend.js"></script>
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
					var datasets = {
						"<?= $team_label ?>": {
							label: "<?= $team_label ?>",
							data: we
						},
						"<?= $match->rival ?>": {
							label: "<?= $match->rival ?>",
							data: they

						},

						"<?=$diff_label ?>": {
							label: "<?=$diff_label ?>",
							data: diff 
						},
						<?php
						if (!empty($we_ch2)) {
						?> "<?= $team_label ?>_2": {
								label: "<?= $team_label ?> (<?=$with_compromised ?>)",
								data: we2 

							},

						"<?=$diff_label ?>_2": {
								label: "<?= $diff_label ?> (<?=$with_compromised ?>)",
								data : diff2

							},
						<?php } ?>
					};

					/* caballeria
					
					Bolbochini
					elmago2018
					elkisi
					gdibella
					estanciero
					miguelmessi
					pancho2015
					*/

								// hard-code color indices to prevent them from shifting as
								// countries are turned on/off

								var i = 0;
								$.each(datasets, function(key, val) {
									val.color = i;
									++i;
								});

								// insert checkboxes
								var choiceContainer = $("#choices");
								$.each(datasets, function(key, val) {
									choiceContainer.append("<br/><input type='checkbox' name='" + key +
										"' checked='checked' id='id" + key + "'></input>" +
										"<label for='id" + key + "'>" +
										val.label + "</label>");
								});

								choiceContainer.find("input").click(plotAccordingToChoices);

								function plotAccordingToChoices() {

									var data = [];

									choiceContainer.find("input:checked").each(function() {
										var key = $(this).attr("name");
										if (key && datasets[key]) {
											data.push(datasets[key]);
										}
									});

									if (data.length > 0) {
										$.plot("#placeholder", data, {
											legend: {
												show: true
											},
											yaxis: {
												min: 0
											},
											xaxis: {
												tickDecimals: 0
											}
										});
									}
								}

								plotAccordingToChoices();

								// Add the Flot version string to the footer

								$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
							});
	</script>
</head>

<body>

	<div id="content">

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder" style="float:left; width:675px;"></div>
			<p id="choices" style="float:right; width:135px;"></p>
		</div>

		<p>This example shows military budgets for various countries in constant (2005) million US dollars (source: <a href="http://www.sipri.org/">SIPRI</a>).</p>

		<p>Since all data is available client-side, it's pretty easy to make the plot interactive. Try turning countries on and off with the checkboxes next to the plot.</p>

	</div>

	<div id="footer">
		Copyright &copy; 2007 - 2014 IOLA and Ole Laursen
	</div>

</body>

</html>