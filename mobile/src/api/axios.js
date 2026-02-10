import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const BASE_URL = import.meta.env.VITE_BASE_URL;
const REQUEST_TIMEOUT = 10000;
const HTTP_STATUS = {
  UNAUTHORIZED: 401,
};
const ENDPOINTS = {
  REFRESH: '/auth/refresh',
};

const instance = axios.create({
  baseURL: BASE_URL,
  timeout: REQUEST_TIMEOUT,
});

let isRefreshingToken = false;
let requestsQueue = [];

const processRequestQueue = (error = null, newToken = null) => {
  requestsQueue.forEach(({ resolve, reject }) => {
    if (error) {
      reject(error);
    } else {
      resolve(newToken);
    }
  });
  requestsQueue = [];
};

const addAuthorizationHeader = (config) => {
  const authStore = useAuthStore();
  if (authStore.accessToken) {
    config.headers['Authorization'] = `Bearer ${authStore.accessToken}`;
  }
  return config;
};

const handleTokenRefresh = async (originalRequest) => {
  const authStore = useAuthStore();
  
  try {
    const refreshSuccessful = await authStore.refreshAccessToken();

    if (!refreshSuccessful) {
      throw new Error('Token refresh failed');
    }

    const newAccessToken = authStore.accessToken;
    originalRequest.headers['Authorization'] = `Bearer ${newAccessToken}`;
    processRequestQueue(null, newAccessToken);
    
    return instance(originalRequest);
  } catch (refreshError) {
    processRequestQueue(refreshError);
    authStore.handleSessionExpired();
    throw refreshError;
  } finally {
    isRefreshingToken = false;
  }
};

const queueRequest = (originalRequest) => {
  return new Promise((resolve, reject) => {
    requestsQueue.push({ resolve, reject });
  })
    .then(newToken => {
      originalRequest.headers['Authorization'] = `Bearer ${newToken}`;
      return instance(originalRequest);
    });
};

const isRefreshEndpointError = (error) => {
  return error.config?.url === ENDPOINTS.REFRESH;
};

const shouldRetryWithRefresh = (error) => {
  return (
    error.response?.status === HTTP_STATUS.UNAUTHORIZED &&
    !error.config?._retry &&
    !isRefreshEndpointError(error)
  );
};

instance.interceptors.request.use(
  addAuthorizationHeader,
  (error) => {
    console.error('Erro na requisição da API:', error);
    return Promise.reject(error);
  }
);

instance.interceptors.response.use(
  (response) => response.data,
  async (error) => {
    if (isRefreshEndpointError(error)) {
      const authStore = useAuthStore();
      authStore.handleSessionExpired();
      return Promise.reject(error);
    }

    if (shouldRetryWithRefresh(error)) {
      const originalRequest = error.config;
      originalRequest._retry = true;

      if (isRefreshingToken) {
        return queueRequest(originalRequest);
      }

      isRefreshingToken = true;
      return handleTokenRefresh(originalRequest);
    }

    return Promise.reject(error);
  }
);

export default instance;