<?php
/**
 * Created by PhpStorm.
 * User: Dennis Eisold
 * Date: 18.08.2018
 * Time: 17:15
 */
require __DIR__ . '/../vendor/autoload.php';

$database = InfluxDB\Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', "localhost", 8086, "wowa"));

$result = $database->query('SELECT
                              mean("1") AS fan1,
                              mean("2") AS fan2,
                              mean("3") AS fan3,
                              mean("4") AS fan4,
                              mean("5") AS fan5,
                              mean("6") AS fan6
                            FROM "fans"
                            where time > now() - 1d
                            GROUP BY time(5m);');
$points = $result->getPoints();

function toFloat($number) {
    return (float)(number_format($number, 2));
}

foreach($points as $point) {
    $time = strtotime($point['time'])*1000;
    $fans[] = array(
        "x" => $time,
        "fan1" => toFloat($point['fan1']),
        "fan2" => toFloat($point['fan2']),
        "fan3" => toFloat($point['fan3']),
        "fan4" => toFloat($point['fan4']),
        "fan5" => toFloat($point['fan5']),
        "fan6" => toFloat($point['fan6']));
}
$data = array(
    "fans" => $fans,
);
echo json_encode($data);
?>