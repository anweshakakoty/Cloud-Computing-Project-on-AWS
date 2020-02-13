<?php session_start(); ?>

<html>
<head><title>ITMO-544 Midterm Project</title>
<header><h1 style = "text-align: center"> MIDTERM PROJECT-2 </h1>
<h2 style = "text-align: center"> Name of student: Anwesha Kakoty </h2>
</head>
<body style = "background-color: plum; text-align:center">

<form enctype="multipart/form-data" action="submit.php" method ="POST">
    <input type ="hidden" name="MAX_FILE_SIZE" value ="1000000" />
    Upload file: <input type ="file" name="userfile" required/><br />
    <br />
    Name: <input type="text" name="name" required><br />
    Email: <input type="email" name="mailid" required><br />
    Phone: <input type="tel" name="phone" required> <br />

<input type="submit" value="Upload File" />
</form>
<hr />
<form enctype="multipart/form-data" action="gallery.php" method="POST">
Enter user email to browse gallery: <input type ="email" name="email">
<input type="submit" value="submit" required/>
</form>
</body>
</html>
