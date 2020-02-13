# MIDTERM PROJECT
## CLOUD COMPUTING ITMO-544

### create-env.sh

Creates the required infrastructure for the project.

First it syncs the clock and checks if all the arguments are present.

Then it creates the RDS with name test-instance and database name records and waits till it is available for the data to be stored. The engine used is mysql and db instance class is t2 micro.

Then it deploys the EC2 instances with proper arguments like the security group, key-pair, iam profile, user data file, the count of instances and the subnet. The wait command waits till they are running.

The load balancer is created and health check is performed for the instances, and waits till the ec2 instances are registered successfully to the load balancer.

### install-app-env.sh

Installs all the necessary dependencies like the composer (fetches aws php sdk), and the apache2 webserver, clones my github repository, creates the table in the database. It also copies the necessary php files from my repository into the apache web server folder. It also starts the apache webserver.

### destroy.sh

Destroys all the infrastructure starting with the RDS, then terminates the EC2 instances and finally the load balancer, and waits for them to be terminated.

### P.S
for the image manipulation, please consider that the function used to create thumbnails only supports certain file types and sizes, so kindly upload files which are jpg or png and try with a different image if the one you uploaded is not being modified successfully.