
MYID=`aws ec2 describe-instances --query 'Reservations[*].Instances[*].[State.Name, InstanceId]' --output text | grep running | awk '{print $2}'`

aws rds delete-db-instance --db-instance-identifier test-instance --skip-final-snapshot 

aws rds wait db-instance-deleted --db-instance-identifier test-instance

aws ec2 terminate-instances --instance-ids $MYID

aws ec2 wait instance-terminated --instance-ids $MYID

aws elb delete-load-balancer --load-balancer-name ak-load-balancer







