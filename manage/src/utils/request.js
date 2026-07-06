import axios from 'axios'
import { ElMessage } from 'element-plus'

// 创建 axios 实例
const request = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// 请求拦截器
request.interceptors.request.use(
  (config) => {
    // 添加 token
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Api-Token'] = token
    }
    
    // 添加请求时间戳防止缓存
    if (config.method === 'get') {
      config.params = {
        ...config.params,
        _t: Date.now()
      }
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

request.interceptors.response.use(
  (response) => {
    const { code, data, msg } = response.data
    
    if (code === 1) {
      return data
    } else {
        if(code === -10001) {
            localStorage.removeItem('token')
            window.location.href = '/login'
            return Promise.reject(new Error('未登录或登录已过期，请重新登录'))
        }
      ElMessage.error(msg || '请求失败')
      
      return Promise.reject(new Error(msg))
    }
  },
  (error) => {
    // 处理 HTTP 错误
    console.error('HTTP Error:', error.response ? error.response.status : 'Network Error')
    ElMessage.error('网络异常，请检查网络连接')
    return Promise.reject(error)
  }
)

export default request