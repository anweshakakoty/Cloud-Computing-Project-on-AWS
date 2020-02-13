#!/bin/bash

#clock sync
sudo apt-get install ntpdate
sudo ntpdate 0.amazon.pool.ntp.org  

if ( [ -z $1 ] || [ -z $2 ] || [ -z $3 ] || [ -z $4 ] || [ -z $5 ] || [ -z $6 ] || [ -z $7 ] ); then
    echo "Arguments missing!"
    echo "Format is: ./create-env.sh <ami-id> <count> <instance-type> <keypair-name> <security-group-ids> <iam-instance-profile-name> <subnet-id>"
	exit 1
fi

#create db instance
aws rds create-db-instance --db-name records --allocated-storage 20 --db-instance-class db.t2.micro --db-instance-identifier test-instance --engine mysql --master-username master --master-user-password anweshak

aws rds wait db-instance-available

echo "Created RDS instance"
echo "----------------------------------------------------------"

echo "Running the EC2 instances"
echo "----------------------------------------------------------"
#run ec2 instances
aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --user-data file://install-app-env.sh --security-group-ids $5 --iam-instance-profile Name=$6 --subnet-id $7


ID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep pending | awk '{print $2}'`   
#wait till instances are in running state
aws ec2 wait instance-running --instance-ids $ID


echo ""
echo "EC2 Instances Deployed"
echo "----------------------------------------------------------"

#get the instance ids of the running instances
MYID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep running | awk '{print $2}'`                                                                                                                                
echo "Creating load balancer"
echo "----------------------------------------------------------"

#create load balancer
aws elb create-load-balancer --load-balancer-name ak-load-balancer --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $5 --subnets $7

echo "Performing health check"
echo "----------------------------------------------------------"
#health check
aws elb configure-health-check --load-balancer-name ak-load-balancer --health-check Target=HTTP:80/index.html,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

#create cookie policy
aws elb create-lb-cookie-stickiness-policy --load-balancer-name ak-load-balancer --policy-name my-duration-cookie-policy
#register ec2 instances with load balancer
aws elb register-instances-with-load-balancer --load-balancer-name ak-load-balancer --instances $MYID


#wait for instances to be registered
aws elb wait any-instance-in-service --load-balancer-name ak-load-balancer --instances $MYID

echo "Finished registering target instances"
echo "----------------------------------------------------------"


