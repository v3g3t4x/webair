<?php
//index.php
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connect= GET_DB_CONNECTION();
$query = '
SELECT  report.VALUE as sensors_temperature_data, report.TIMESTAMP as datetime
FROM WEBAIR_DB.AIR_REPORT report 
WHERE report.ID_DEVICE="DEVICE_TEST" and report.REPORT_TYPE="INTERNAL_TEMP" order by report.TIMESTAMP asc';

$result = $connect-> prepare($query);
/*
$query = '
SELECT sensors_temperature_data,
UNIX_TIMESTAMP(CONCAT_WS(" ", sensors_data_date, sensors_data_time)) AS datetime
FROM tbl_sensors_data
ORDER BY sensors_data_date DESC, sensors_data_time DESC
';
*/
$result -> execute();
$rows = array();
$table = array();

$table['cols'] = array(
 array(
  'label' => 'datetime', 
  'type' => 'string',
  'pattern' => '',
  'id'=>'datetime'
 ),
 array(
  'label' => 'Temperature (°C)', 
  'type' => 'number',
  'pattern' => '',
  'id'=>'datetime'
 )
);
$num_rows = $result->rowCount();
echo "RISULTATI: " . $num_rows;

$rows = $result -> fetchAll();
//while($row = mysqli_fetch_array($result))



foreach ($rows as $row) 
{
	$temp = array();
	$temp[] = array('v' => $row['datetime']);
	$temp[] = array('v' =>  $row['sensors_temperature_data']);
	
	$output[] = array('c' => $temp);
}

$table['rows'] = $output;
$jsonTable = json_encode($table);
echo $jsonTable;
?>


<html>
 <head>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);
   function drawChart()
   {
	   /*
	   var jsonData = $.ajax({
	          url: "GetData.php",
	          dataType: "json",
	          async: false
	          }).responseText;
	          */
    //var data = new google.visualization.DataTable(jsonData);
    var data = new google.visualization.DataTable(<?php echo $jsonTable; ?>);

    var options = {
     title:'Sensors Data',
     legend:{position:'bottom'},
     chartArea:{width:'80%', height:'65%'}
    };

    var chart = new google.visualization.LineChart(document.getElementById('line_chart'));

    chart.draw(data, options);
   }
  </script>
  <style>
  .page-wrapper
  {
   width:1000px;
   margin:0 auto;
  }
  </style>
 </head>  
 <body>
  <div class="page-wrapper">
   <br />
   <h2 align="center">Test grafico temperature</h2>
   <div id="line_chart" style="width: 100%; height: 500px"></div>
  </div>
 </body>
</html>