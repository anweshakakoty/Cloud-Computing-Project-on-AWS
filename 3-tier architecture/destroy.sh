
MYID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep running | awk '{print $2}'`

aws dynamodb delete-table --table-name RecordsAK

aws dynamodb wait table-not-exists --table-name RecordsAK

aws autoscaling delete-auto-scaling-group --auto-scaling-group-name ak-asg --force-delete

aws ec2 wait instance-terminated --instance-ids $MYID

aws autoscaling delete-launch-configuration --launch-configuration-name ak-launch-config

aws elb delete-load-balancer --load-balancer-name ak-load-balancer

aws lambda delete-function --function-name ak-function






