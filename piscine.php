<?php

require "_header.php"; 

use Symfony\Component\HttpClient\HttpClient;

// Bassins
$bassins = [
	'25' => 'https://cms.vaudoisearena.ch/api/calendar/aquatic-center/public/25',
	'50' => 'https://cms.vaudoisearena.ch/api/calendar/aquatic-center/public/50',
];

foreach ($bassins as $bassin => $url){
	$client = HttpClient::create();
	$response = $client->request('GET', $url);
	$content = $response->getContent();

	$content = explode("BEGIN:VEVENT", $content);
	$values = [];

	foreach ($content as $key => $content2) {
		$values = explode("\n", $content2);
		foreach ($values as $key2 => $value) {
			unset($values[$key2]);
			$value = explode(":", $value);

			if (sizeof($value) < 2) {
				continue;
			}
			$values[$value[0]] = $value[1];
		}

		$content[$key] = $values;
	}

	$bassins[$bassin] = $content;
}

?>

<h1 style="margin-bottom: 0;">Piscine</h1>

<?php foreach ($bassins as $bassin): ?>
	<?php $lastDate = ""; ?>

	<h3 style="margin-bottom: 5px;">
		<?= $bassin[0]["X-WR-CALNAME"] ?>
	</h3>

	<table>
		<?php foreach ($bassin as $key => $entry): ?>
			<?php
				if ($key == 0) continue;

				$datetime = explode("T", $entry["DTSTART"]);
				$date = $datetime[0];
				$time = $datetime[1];

				$datetimeEnd = explode("T", $entry["DTEND"]);
				$timeEnd = $datetimeEnd[1];

				if ($date < (new DateTime(date("Ymd")))->format("Ymd")) {
					continue;
				}

				if ($date > (new DateTime(date("Ymd")))->add(new DateInterval("P7D"))->format("Ymd")) {
					continue;
				}

				if ($date != $lastDate) {
					$lastDate = $date;
					?>
					<tr>
						<td colspan="3">
							<?= date("D, d-m-Y", strtotime($date)) ?>
						</td>
					</tr>
					<?php
				}
			?>

			<tr>
				<td><?= date("H:i", strtotime($time)) ?></td>
				<td><?= date("H:i", strtotime($timeEnd)) ?></td>
				<td><?= $entry["SUMMARY"] ?></td>
			</tr>

		<?php endforeach; ?>
	</table>
<?php endforeach; ?>

<?php require "_footer.php"; ?>