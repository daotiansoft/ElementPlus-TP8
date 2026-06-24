from modules.httpHelper import HttpClient
from modules.jsonHelper import JsonHelper
from modules.api import Api
import time

api = Api()

while True:
    data = api.getTask()
    code = JsonHelper.get_int(data,'code')
    if code == 1:
        print('新任务')
        task_id = JsonHelper.get_string(data,'data','task_id')
        expire_time = JsonHelper.get_int(data,'data','expire_time')
        data = JsonHelper.get_value(data,'data','data')
        print('TASK_ID：%s' % task_id)
        print("任务内容：%s" % data)
        time.sleep(3)
        ret = api.setReport(task_id,'执行完成');
        print("完成上传：%s" % ret)

    time.sleep(1)

    