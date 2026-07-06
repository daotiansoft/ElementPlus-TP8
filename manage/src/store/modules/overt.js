import { defineStore } from 'pinia'
import { ref } from 'vue'
import overt from '@/api/overt'

export const useOvertStore = defineStore('overt', () => {
  // ========== State ==========
  const cache = ref({})
  const loading = ref({})

  // ========== Actions ==========

  /**
   * 通用获取数据方法
   * @param {string} key - 缓存键名
   * @param {Function} fetchFn - 获取数据的函数
   * @param {boolean} useCache - 是否使用缓存，默认 true
   * @returns {Promise<any>}
   */
  const fetchData = async (key, fetchFn, useCache = true) => {
    // 如果使用缓存且有数据，直接返回缓存
    if (useCache && cache.value[key] !== undefined) {
      return cache.value[key]
    }

    // 防止重复请求
    if (loading.value[key]) {
      return new Promise((resolve) => {
        const timer = setInterval(() => {
          if (!loading.value[key]) {
            clearInterval(timer)
            resolve(cache.value[key])
          }
        }, 100)
      })
    }

    try {
      loading.value[key] = true
      const data = await fetchFn()
      cache.value[key] = data
      return data
    } catch (error) {
      console.error(`获取 ${key} 失败:`, error)
      throw error
    } finally {
      loading.value[key] = false
    }
  }

  /**
   * 获取站点信息
   * @param {boolean} useCache - 是否使用缓存，默认 true
   * @returns {Promise<any>}
   */
  const getWebsite = async (useCache = true) => {
    return fetchData('website', () => overt.init(), useCache)
  }

  /**
   * 清除指定缓存
   * @param {string} key - 缓存键名，不传则清除所有
   */
  const clearCache = (key) => {
    if (key) {
      delete cache.value[key]
    } else {
      cache.value = {}
    }
  }

  /**
   * 获取缓存状态
   * @param {string} key - 缓存键名
   * @returns {any}
   */
  const getCache = (key) => {
    return cache.value[key]
  }

  return {
    cache,
    loading,
    getWebsite,
    clearCache,
    getCache,
    fetchData
  }
})