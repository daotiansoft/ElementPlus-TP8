import request from '@/utils/request'

const overt = {
  init() {
    return request({
      url: '/common/overt/init'
    })
  },
  getCaptcha() {
    return request({
      url: '/common/overt/captcha'
    })
  },
  postLogin(data) {
    return request({
      url: '/common/overt/login',
      method: 'post',
      data
    })
  },
}

export default overt