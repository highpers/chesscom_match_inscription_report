<link href="vendor/flot-master/examples.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.canvaswrapper.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.colorhelpers.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.saturated.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.browser.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.drawSeries.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.uiConstants.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.legend.js"></script>
<script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.flot.axislabels.js"></script>



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

		<?php
		}
		if ($match_type == '960') {
		?>
			we_classic = <?= $we_ch_classic ?>;
			they_classic = <?= $they_ch_classic ?>;
			diff_classic = <?= $diff_ch_classic ?>;
		 <?php 
		 	if(!empty($we_ch2_classic)){
				 ?>
			we2_classic = <?= $we_ch2_classic ?>;
			diff2_classic = <?= $diff_ch2_classic ?>;
		<?php	
			 }	
		}
		?>
		var datasets = {
			"<?= $team_label ?>": {
				color: "#ff0000",
				label: "<?= $team_label ?>",
				data: we,

			},
			"<?= $rival ?>": {
				label: "<?= $rival ?>",
				data: they

			},
			<?php
			if (!empty($we_ch2)) {
			?> "<?= $team_label ?>_2": {
					label: "<?= $team_label ?> (<?= $with_compromised ?>)",
					data: we2

				},
			<?php } ?>

			<?php
			if (!empty($we_ch2_classic)) {
			?> "<?= $team_label ?>_2_classic": {
					label: "<?= $team_label ?> (<?= $classic_label . ' ' . $with_compromised ?>)",
					data: we2_classic

				},
			<?php } ?>

			"<?= $diff_label ?>": {
				label: "<?= $diff_label ?>",
				data: diff
			},
			<?php
			if (!empty($we_ch2)) {
			?> "<?= $diff_label ?>_2": {
					label: "<?= $diff_label ?> (<?= $with_compromised ?>)",
					data: diff2,

				},

				<?php
				if (!empty($diff_ch2_classic)) {
				?> "diff_ch2_classic": {
						label: "<?= $diff_label ?> (<?= $classic_label . ' ' . $with_compromised ?>)",
						data: diff2_classic

					},
				<?php } ?>

			<?php
			}

			if ($match_type == '960') {
			?> "<?= $diff_label . ' (' . $classic_label . ')' ?>": {
					label: "<?= $diff_label . ' (' . $classic_label . ')' ?>",
					data: diff_classic,

				},

				"<?= $team_label ?> (<?= $classic_label ?>)": {
					label: "<?= $team_label ?> (<?= $classic_label ?>)",
					data: we_classic,
				},


				"<?= $rival ?>(<?= $classic_label ?>": {
					label: "<?= $rival ?> (<?= $classic_label ?>)",
					data: they_classic,
				}
			<?php
			}
			?>
		};
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
				"<label for='id" + key + "' class='checkboxes'>" +
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
					series: {
						lines: {
							lineWidth: 3,
						}
					},

					yaxis: {
						min: 0,
						axisLabel: '<?= $yRating ?>',
						color: '#999999',
						axisLabelPadding: 23,

					},
					xaxis: {
						tickDecimals: 0,
						axisLabel: '<?= $xBoard ?>',
						axisLabelPadding: 23,
						color: '#999999',
						labelWidth: 10
					},
					legend: {
						position: "se",

						<?php
						if ($match_type == '960') {
						  if(empty($we_ch2)){	
							$margin_left = '-268';
							$margin_top = '-11';
						  }else{
								$margin_left = '-268';
								$margin_top = '-122';  
						  }	
						} else {
							$margin_left = '-172';
							$margin_top = '18';
						}
						echo 'margin:[' . $margin_left . ',' . $margin_top . ']';
						?>,

						show: true,
					},
					colors: ["blue", "red", '#aaaaaa', '#00ff00', 'orange', '#BA2FC4', '#2FC4B5', 'yellow', '#CBBF0B', '#222222', '#CCCCFF'],

				});
			}
		}

		plotAccordingToChoices();

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});
</script>

<div id="content">

	<div class="demo-container">
		<div id="placeholder" class="demo-placeholder" style="float:left; width:785px;"></div>
		<p id="choices" style="float:right; width:300px;"></p>
	</div>
</div>