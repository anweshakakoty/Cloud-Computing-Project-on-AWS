import boto3
import os
import sys
import uuid
from urllib.parse import unquote_plus
from PIL import Image
import PIL.Image

s3_client = boto3.client('s3')
sns = boto3.client('sns')
dynamodb = boto3.client('dynamodb')

def resize_image(image_path, resized_path):
    with Image.open(image_path) as image:
        maxsize=(70,70)
        image.thumbnail(maxsize)
        image.save(resized_path)

def handler(event, context):
    for record in event['Records']:
        bucket = record['s3']['bucket']['name']
        key = unquote_plus(record['s3']['object']['key'])
        download_path = '/tmp/{}{}'.format(uuid.uuid4(), key)
        upload_path = '/tmp/resized-{}'.format(key)
        s3_client.download_file(bucket, key, download_path)
        resize_image(download_path, upload_path)
        s3_client.upload_file(upload_path, '{}resized'.format(bucket), key, ExtraArgs={'ACL': 'public-read'})
        urlraw = '{}/{}/{}'.format(s3_client.meta.endpoint_url, 'anwesha-bucket', key)
        urlprocess = '{}/{}/{}'.format(s3_client.meta.endpoint_url, 'anwesha-bucketresized', key)
        sns.publish(PhoneNumber='+13126780290',Message='Your image has been created! here is the link: ' + urlprocess)
        dynamodb2 = boto3.resource('dynamodb')
        table = dynamodb2.Table('RecordsAK')
        response = table.scan()
        item = response['Items']
        receipt= key.split('-')[0]
        print(urlraw)
        l=len(item)
        for i in range(l):
            if(response['Items'][i]['Receipt']==receipt):
                useremail = response['Items'][i]['Email']
                phone = response['Items'][i]['Phone']
                print(receipt)
                sns.publish(PhoneNumber=phone,Message='Your image has been created! here is the link: ' + urlprocess)
                dynamodb.update_item(TableName='RecordsAK',Key={'Receipt': {'S': receipt},'Email': {'S': useremail}},UpdateExpression="set S3finishedurl=:processed_url, #processed = :processed",ExpressionAttributeValues={':processed_url': {'S': urlprocess},':processed': {'S': "true"}},ExpressionAttributeNames={"#processed": "Status"},ReturnValues="UPDATED_NEW")