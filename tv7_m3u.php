<?php

define("API_URL", "https://tv7api2.tv.init7.net/api/tvchannel/");
header("Content-Type: text/plain");

$data = file_get_contents(API_URL);
$json = json_decode($data);

?>
#EXTM3U
<?php foreach($json->results as $channel): ?>
#EXTINF:0 tvg-logo="<?= $channel->logo ?>" tvg-name="<?= $channel->canonical_name ?>" group-title="<?= $channel->language ?>", <?= $channel->name ?>

<?= $channel->hls_src ?>

<?php endforeach; ?>
