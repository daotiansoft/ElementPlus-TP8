import { defineStore } from "pinia";
import user from "@/api/user";
import { useFetchCache } from "@/composables/useFetchCache";

export const useUserStore = defineStore("user", () => {
  // ========== State & 通用请求缓存 ==========
  const { cache, loading, fetchData, clearCache, getCache } = useFetchCache();

  // ========== Actions ==========

  /**
   * @param {boolean} useCache - 是否使用缓存，默认 true
   * @returns {Promise<any>}
   */
  const getUserInfo = async (useCache = true) => {
    return fetchData("userInfo", () => user.getUserInfo(), useCache);
  };
  const getRoutesItems = async (useCache = true) => {
    return fetchData("routerItems", () => user.getRoutes(), useCache);
  };

  return {
    cache,
    loading,
    getUserInfo,
    getRoutesItems,
    clearCache,
    getCache,
    fetchData,
  };
});
