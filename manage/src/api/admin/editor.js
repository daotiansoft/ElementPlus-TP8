import request from '@/utils/request'

const editor = {
  getItems() {
    return request({
      url: '/super/editor/items'
    })
  },
  postItems(data) {
    return request({
      url: '/super/editor/save',
      method: 'post',
      data
    })
  },
}

export default editor