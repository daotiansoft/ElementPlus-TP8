import request from '@/utils/request'

const user = {
  getUserInfo() {
    return request({
      url: '/common/account/info'
    })
  },
  logout() {
    return request({
      url: '/common/account/logout'
    })
  },
  postPassword(data) {
    return request({
      url: '/common/account/password',
      method: 'post',
      data
    })
  },
  getNotice(data) {
    return request({
      url: '/common/account/notice',
    })
  },
  getUploadInfo(data) {
    return request({
      url: '/common/account/uploadinfo',
    })
  },
}

export default user