from modules.httpHelper import HttpClient
import hashlib
import time
from urllib.parse import urlencode
import json

BASE_URL = "http://1.test.com"
APPID = 'wmuauefp0wap8ktv9fv2uk'
SECRET = 'z5g475zyjvls5h8ppqvqqn'

def request(url = '',isPost = False,post_data = {}):
    client = HttpClient(
        base_url = BASE_URL,
        default_timeout = 10
    )
    headers = {}
    post_data = sign(post_data)
    if isPost:
        response = client.post(url,data=post_data,headers=headers)
    else:
        response = client.get(url)
    if response.is_success:
        return response.response.json()
    else:
        print(f"请求失败: {response.error_message}")
        return None

def sign(params:dict):
    params['appid'] = APPID
    params['time'] = str(int(time.time()))
    sorted_params = "&".join([f"{k}={v}" for k, v in sorted(params.items())])
    sorted_params += '&secret=' + SECRET
    params['sign'] = hashlib.md5(sorted_params.encode('utf-8')).hexdigest()
    return params



def getOrderTask():
    url = '/openapi/command/getOrderTask'
    data = {
    }

    req = request(url,True,data)
    return req

def setReport(order_id,report):
    url = '/openapi/command/setReport'
    data = {
        'order_id':order_id,
        'report':json.dumps(report)
    }
    req = request(url,True,data)
    return req


    