<html>
<head>
<title>ITMO-544 CLOUD COMPUTING MIDTERM</title>
</head>
<body style ="background-color: lavender">
<header><h1 style = "text-align: center"> MIDTERM PROJECT-2 </h1>
<h2 style = "text-align: center"> Name of student: Anwesha Kakoty </h2>
<h3 style= "text-align: center"> DISPLAYING YOUR GALLERY IMAGE THUMBNAILS </h3>
<hr />
</body>
</html>


<?php
require '/home/ubuntu/vendor/autoload.php';
echo "Email id entered: ";
echo $_POST['email'];
$email = $_POST['email'];


use Aws\DynamoDb\DynamoDbClient;

$client = new DynamoDbClient([
'region'  => 'us-east-1',
'version' => 'latest'
]);
$result = $client->scan([
'ExpressionAttributeNames' => [
'#S3R' => 'S3finishedurl',
'#S3F' => 'S3rawurl',
],
'ExpressionAttributeValues' => [
':e' => [
'S' => $email,
 ],
],
'FilterExpression' => 'Email = :e',
'ProjectionExpression' => '#S3F, #S3R',
'TableName' => 'RecordsAK',
]);


$len = $result['Count'];

echo "\n";

for ($i=0; $i < $len; $i++) {
echo "\n";
echo "\n";
print_r("\n");
$res=$result['Items'][$i]['S3finishedurl']['S'];
echo "<img src= $res>";
echo "      ";
}

?>