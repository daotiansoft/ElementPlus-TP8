from modules.api import Api
import time
import logging
import os

api = Api()
logger = logging.getLogger(__name__)

def log(str):
    str = "[自动回调]%s %s" % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()), str)
    print(str)

    filename = time.strftime("%Y%m%d%H", time.localtime())
    path = './log'
    if not os.path.exists(path):
        os.makedirs(path)
    logging.basicConfig(filename='%s/order_%s.log' % (path,filename), level=logging.INFO)
    logger.info(str)

while True:
    response = api.getTask()
    if response and response.get('code') == 1 and isinstance(response.get('data'), dict):
        log('新任务')
        task = response['data']
        task_id = str(task.get('task_id') or '').strip()
        data = task.get('data')
        log('TASK_ID：%s' % task_id)
        log("任务内容：%s" % data)
        time.sleep(3)
        ret = api.setReport(task_id,'执行完成')
        log("完成上传：%s" % ret)

    time.sleep(1)


