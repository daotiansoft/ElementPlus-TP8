import request from '@/utils/request'

const config = {
  getItems() {
    return request({
      url: '/super/config/items'
    })
  },
  postItems(data) {
    return request({
      url: '/super/config/save',
      method: 'post',
      data
    })
  },
}

export default config