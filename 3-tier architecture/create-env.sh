#!/bin/bash


if ( [ -z $1 ] || [ -z $2 ] || [ -z $3 ] || [ -z $4 ] || [ -z $5 ] || [ -z $6 ] || [ -z $7 ] ); then
    echo "Arguments missing!"
    echo "Format is: ./create-env.sh <ami-id> <count> <instance-type> <keypair-name> <security-group-ids> <iam-instance-profile-name> <subnet-id>"
	exit 1
fi
echo "Created DynamoDB instance"
echo "----------------------------------------------------------"

#create dynamodb instance
aws dynamodb create-table --table-name RecordsAK --attribute-definitions AttributeName=Receipt,AttributeType=S AttributeName=Email,AttributeType=S --key-schema AttributeName=Receipt,KeyType=HASH AttributeName=Email,KeyType=RANGE --provisioned-throughput ReadCapacityUnits=5,WriteCapacityUnits=5

aws dynamodb wait table-exists --table-name RecordsAK



echo "Creating load balancer"
echo "----------------------------------------------------------"

#create load balancer
aws elb create-load-balancer --load-balancer-name ak-load-balancer --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $5 --subnets $7



#create cookie policy
aws elb create-lb-cookie-stickiness-policy --load-balancer-name ak-load-balancer --policy-name my-duration-cookie-policy


echo "Running the auto scaling group"
echo "----------------------------------------------------------"


aws autoscaling create-launch-configuration --launch-configuration-name ak-launch-config --image-id $1 --instance-type $3 --user-data file://install-app-env-front-end.sh --key-name $4 --security-groups $5 --iam-instance-profile $6

az=`aws ec2 describe-subnets --query 'Subnets[*].[AvailabilityZone,SubnetId]' --output text | grep $7 | awk '{print $1}'`

aws autoscaling create-auto-scaling-group --auto-scaling-group-name ak-asg --launch-configuration-name ak-launch-config --min-size 2 --max-size 4 --desired-capacity 3 --termination-policies "OldestInstance" --availability-zones $az --load-balancer-name ak-load-balancer

ID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep pending | awk '{print $2}'`



#health check
#aws elb configure-health-check --load-balancer-name ak-load-balancer --health-check Target=HTTP:80/index.html,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3


echo ""
echo "Auto scaling group deployed"
echo "----------------------------------------------------------"

#get the instance ids of the running instances
MYID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep running | awk '{print $2}'`                                                                                                                                

#wait for instances to be registered
aws elb wait any-instance-in-service --load-balancer-name ak-load-balancer --instances $MYID


echo "Finished registering target instances"
echo "----------------------------------------------------------"



aws lambda create-function --function-name ak-function --zip-file fileb://function-latest.zip --handler process.handler --runtime python3.6 --role $8

aws lambda add-permission --function-name ak-function --action lambda:InvokeFunction --statement-id 1 --principal s3.amazonaws.com

lambda=`aws lambda list-functions --query 'Functions[*].FunctionArn' --output text`

aws s3api put-bucket-notification-configuration --bucket anwesha-bucket --notification-configuration '{"LambdaFunctionConfigurations": [{"LambdaFunctionArn": "'$lambda'","Events": ["s3:ObjectCreated:*"]}]}'