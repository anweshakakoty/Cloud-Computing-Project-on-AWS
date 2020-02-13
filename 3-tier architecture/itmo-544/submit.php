<html>
<head>
<title>ITMO-544 CLOUD COMPUTING MIDTERM</title>
</head>
<body style ="background-color: beige">
<header><h1 style = "text-align: center"> MIDTERM PROJECT-2 </h1>
<h2 style = "text-align: center"> Name of student: Anwesha Kakoty </h2>
<hr />
</body>
</html>

<?php


session_start();

$uploaddir = '/tmp/';
$key = $_FILES['userfile']['name'];
$key2 = "processedimg".$key;
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$processfile = $uploaddir . basename("processedimg.png");

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
echo "File is valid and successfully uploaded.\n";
} else {
echo "File couldn't be uploaded\n";
}


print "</pre>";
require '/home/ubuntu/vendor/autoload.php';
use Aws\S3\S3Client;

$s3 = new Aws\S3\S3Client([
'version' => 'latest',
'region' => 'us-east-1'
]);

$bucket="anwesha-bucket";

$receipt = uniqid();
$result = $s3->putObject([
'Bucket' => $bucket,
'Key' => $receipt.'-'.$key,
'SourceFile' => $uploadfile,
'ACL' => 'public-read'
]);
$email = $_POST['mailid'];
$phone = $_POST['phone'];
$filename = basename($_FILES['userfile']['name']);

$url = $result['ObjectURL'];


use Aws\DynamoDb\DynamoDbClient;

$clientdb = new DynamoDbClient([
'region'  => 'us-east-1',
'version' => 'latest'
]);


$result = $clientdb->putItem([
'Item' => [ // REQUIRED
'Receipt' => ['S' => $receipt],
'Email' => ['S' => $email],
'Phone' => ['S' => $phone],                                                                                    
'Filename' => ['S' => $uploadfile],
'S3rawurl' => ['S' => $url],
'S3finishedurl' => ['S' => 'na'],
'Status' => ['BOOL' => false],
'Issubscribed' => ['BOOL' => false]
 ],
'TableName' => 'RecordsAK', // REQUIRED
]);

print_r(" SUCCESS :)");
?>