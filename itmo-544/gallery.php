<html>
<head>
<title>ITMO-544 CLOUD COMPUTING MIDTERM</title>
</head>
<body style ="background-color: lavender">
<header><h1 style = "text-align: center"> MIDTERM PROJECT </h1>
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
$rdsClient = new Aws\Rds\RdsClient([
'version' => '2014-10-31',
'region' => 'us-east-1'
]);

try {
$result = $rdsClient->describeDBInstances([

]);
foreach ($result['DBInstances'] as $instance) {
print('</p>');
}

} catch (AwsException $e) {
echo $e->getMessage();
echo "\n";
}

$endpoint = $instance['Endpoint']["Address"];

$link = mysqli_connect($endpoint,"master","anweshak","records") or die("Error " . mysqli_error($link));

if (mysqli_connect_errno()) {
printf("Connect failed: %s\n", mysqli_connect_error());
exit();
}




$imgquery = "SELECT * from items";
$sth = $link->query($imgquery);

while ($res = mysqli_fetch_array($sth)) {
if ($res[1] == $email)
{
$f = $res[5];
echo "<img src= $res[5]>";
echo "      ";
}
}
if ($f == '')
{printf("Email does not exist");}


$link->close();


?>