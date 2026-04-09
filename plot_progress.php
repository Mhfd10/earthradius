<?php
//include("pChart/class/pData.class.php");
//include("pChart/class/pDraw.class.php");
//include("pChart/class/pImage.class.php");

$data = new pData();
$data->addPoints([10, 20, 15, 7], "Sample Data");
$data->setAxisName(0, "Values");

$chart = new pImage(700, 230, $data);
$chart->drawFilledRectangle(0, 0, 700, 230, ["R"=>255, "G"=>255, "B"=>255]);
$chart->drawScale();
$chart->drawLineChart();
$chart->render("chart.png");
?>