<html>
<head>
<title>ITMO-544 CLOUD COMPUTING MIDTERM</title>
</head>
<body style ="background-color: beige">
<header><h1 style = "text-align: center"> MIDTERM PROJECT </h1>
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
$bucket2="anwesha-bucket-processed";


$result = $s3->putObject([
'Bucket' => $bucket,
'Key' => $key,
'SourceFile' => $uploadfile,
'ACL' => 'public-read'
 ]);
$email = $_POST['mailid'];
$phone = $_POST['phone'];
$filename = basename($_FILES['userfile']['name']);


$src = $uploadfile;
$dest = $processfile;
$desired_width = 70;

/*function to create thumbnail of the image*/
function make_thumb($src, $dest, $desired_width) {

$source_image = imagecreatefromstring(file_get_contents($src));  
$width = imagesx($source_image);
$height = imagesy($source_image);                                                                                                                                                                        $width = imagesx($source_image);                                                                                                                                                                                   $height = imagesy($source_image);
$desired_height = $desired_width;
$virtual_image = imagecreatetruecolor($desired_width, $desired_height);                                                              

imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);                    
 
imagepng($virtual_image, $dest);
}

$url = $result['ObjectURL'];


make_thumb($src, $processfile, $desired_width);

$resultprocess = $s3->putObject([
'Bucket' => $bucket2,
'Key' => $key2,
'SourceFile' => $processfile,
'ACL' => 'public-read'
]);

$processurl = $resultprocess['ObjectURL'];





require '/home/ubuntu/vendor/autoload.php';

$rdsClient = new Aws\Rds\RdsClient([
'version' => '2014-10-31',
'region' => 'us-east-1'
]);

try {
$result = $rdsClient->describeDBInstances([

]);
foreach ($result['DBInstances'] as $instance) {
/*print('<p>DB Identifier: ' . $instance['DBInstanceIdentifier']);
print('<br />Endpoint: ' . $instance['Endpoint']["Address"]); */
print('</p>');
}


} catch (AwsException $e) {
echo $e->getMessage();
echo "\n";
}

$endpoint = $instance['Endpoint']["Address"];

$link = mysqli_connect($endpoint,"master","anweshak","records") or die("Error " . mysqli_error($link));

$link;

if (mysqli_connect_errno()) {
printf("Connect failed: %s\n", mysqli_connect_error());
exit();
}


if (!($stmt = $link->prepare("INSERT INTO items (id,email,phone,filename,s3rawurl,s3finishedurl,status,issubscribed) VALUES (NULL,?,?,?,?,?,?,?)"))) {
echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}


$s3rawurl = $url;
$s3finishedurl = $processurl;
$status =1;
$issubscribed=0;

$stmt->bind_param("sssssii",$email,$phone,$filename,$s3rawurl,$s3finishedurl,$status,$issubscribed);

if (!$stmt->execute()) {
echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
else
{echo "SUCCESS!\n";}


$stmt->close();


$link->close();

?>