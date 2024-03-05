import axios from 'axios';
import { useSessionStore } from '@/stores/session';

const BASE_URL = 'http://localhost:8000';

const instance = axios.create({
  baseURL: BASE_URL,
  timeout: 10000,
});

instance.interceptors.request.use(
  (request) => {
    const storeSession = useSessionStore();  
    request.headers['X-Token'] = storeSession.session ? storeSession.session.token : '';
    return request;
  },
  (error) => {
    console.error('Erro na requisicao da API:', error);
    return Promise.reject(error);
  }
);

instance.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    console.error('Erro na resposta da API:', error);
    return Promise.reject(error);
  }
);

export default instance;
