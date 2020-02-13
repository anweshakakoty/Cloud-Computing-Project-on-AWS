# MIDTERM PROJECT- 2
## CLOUD COMPUTING ITMO-544

AMI ID: ami-088aabd4ed96f31eb

### P.S
for the image manipulation, please consider that the function used to create thumbnails only supports certain file types and sizes, so kindly upload files which are jpg or png and try with a different image if the one you uploaded is not being modified successfully. 
Please enter phone number in the index page in +1XXXXXXXXXX format only, for example +13126782883. Also please make sure that every previous infrastructure is deleted before starting this project.

### create-env.sh

Creates the required infrastructure for the project.

First it checks if all the arguments are present.

Then it creates the DynamoDb table with Receipt as partition key and Email as sort key.

The load balancer is created and then it deploys the launch configuration and autoscaling group with proper arguments like the security group, key-pair, iam profile, user data file,Lambda function role and the subnet. 

The lambda function is then created and required permissions are added and notifications are put for s3 bucket to trigger the lambda function when an object is put.

### install-app-env-front-end.sh

Installs all the necessary dependencies like the composer (fetches aws php sdk), and the apache2 webserver, clones my github repository. It also copies the necessary php files from my repository into the apache web server folder. It also starts the apache webserver.

### destroy.sh

Destroys all the infrastructure starting with the DynamoDb and Lambda function then deletes the autoscaling group and then the launch configuration and load balancer.

### process.py

Lambda function to process the image into thumbnails, put the processed image in an s3 bucket, retrieve processed url and update it in the DynamoDB table the send the user a message using SNS alerting them that the image has been processed.

