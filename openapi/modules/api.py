from modules.httpHelper import HttpClient
from modules.jsonHelper import JsonHelper
import hashlib
import time

class Api:
    BASE_URL = "http://admin.test.com"
    APPID = 'admin'
    SECRET = '1d3gweoqpnqyjponpzovna'

    def request(self,url = '',isPost = False,post_data = {}):
        client = HttpClient(
            base_url = self.BASE_URL,
            default_timeout = 10
        )
        headers = {}
        post_data = self.sign(post_data)
        if isPost:
            response = client.post(url,data=post_data,headers=headers)
        else:
            response = client.get(url)
        if response.is_success:
            return response.response.json()
        else:
            print(f"请求失败: {response.error_message}")
            return None

    def sign(self,params:dict):
        params['appid'] = self.APPID
        params['time'] = str(int(time.time()))
        sorted_params = "&".join([f"{k}={v}" for k, v in sorted(params.items())])
        sorted_params += '&secret=' + self.SECRET
        params['sign'] = hashlib.md5(sorted_params.encode('utf-8')).hexdigest()
        return params


    def getTask(self):
        url = '/task/api/getTask'
        data = {
        }

        req = self.request(url,True,data)
        return req

    def setReport(self,task_id,data):
        url = '/task/api/setReport'
        data = {
            'task_id':task_id,
            'data':data
        }
        req = self.request(url,True,data)
        return req