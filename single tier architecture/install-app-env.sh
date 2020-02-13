#!/bin/bash


sudo apt-get -y update
sudo apt-get -y install php apache2 mysql-client-core-5.7 php-gd php7.2-xml php-curl unzip zip awscli 

git clone git@github.com:illinoistech-itm/akakoty.git
sudo cp /akakoty/midterm-project/itmo-544/*.php /var/www/html
sudo cp /akakoty/midterm-project/create-schema.sql /home/ubuntu
sudo cp /akakoty/midterm-project/my.cnf /etc
sudo chmod 777 /home/ubuntu/.my.cnf
sudo mkdir /home/ubuntu/.aws
sudo cp /akakoty/midterm-project/config /home/ubuntu/.aws

cd /home/ubuntu

#Get composer to install AWS PHP and SDK 
sudo -u ubuntu php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo -u ubuntu php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo -u ubuntu php composer-setup.php
sudo -u ubuntu php -r "unlink('composer-setup.php');"


sudo -u ubuntu php -d memory_limit=-1 composer.phar require aws/aws-sdk-php   #actual AWS SDK install

#enable and check status of apache2
sudo systemctl enable apache2
sudo systemctl start apache2
echo "------------------------------------------ apache started -------------------------------------------"
sudo aws configure set default.region us-east-1
echo "------------------------------------------- aws configure worked -----------------------------------------"

export $MYSQL_PWD = anweshak

echo `sudo aws rds describe-db-instances --output text --query DBInstances[*].Endpoint.Address`
echo "listing directory at /home/ubuntu"
echo `ls -a`
echo "displaying .my.cnf contents"
echo `sudo cat .my.cnf`
echo "-----------------------------------------------------------rds endpoint---------------------------------------"
echo "executing the command sudo mysql --host=`sudo aws rds describe-db-instances --output text --query DBInstances[*].Endpoint.Address` -u master \"records\"" 
sudo mysql --host=`sudo aws rds describe-db-instances --output text --query DBInstances[*].[DBInstanceStatus,Endpoint.Address] | grep available | awk '{print $2}'` -u master "records" < create-schema.sql
echo "------------------------------------------------ mysql worked -----------------------------------------------"

sudo apt install php-mysqli
sudo service apache2 restart
