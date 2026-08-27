import axios from "axios";
import { TokenService } from "@/services/modules/service.storage";
import UserFunctions from "@/services/modules/service.user";
const thisStore = TokenService.getStore() != null ? TokenService.getStore().id : null;

const urlBase = '/service-api/';  
const thisLang = TokenService.getLang();

const ApiData = {
  init(baseURL) {
    axios.defaults.baseURL = baseURL;
  },

  setHeaders(authHeader, accept = "application/json", secret) {
    if (authHeader) axios.defaults.headers.common.Authorization = authHeader;
    if (accept) axios.defaults.headers.common.Accept = accept;
    if (secret) axios.defaults.headers.common.secret = secret;
  },

  removeHeaders() {
    axios.defaults.headers.common = {};
  },

  get(resource, params = {}) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    return axios.get(resource, {
      ...(params && { params }),
      headers: {
        Authorization: `Bearer ${TokenService.getToken()}`,
        Accept: "application/json", 
        storeId: thisStore,
        systemLang: thisLang
      },
    });
  },

  download(resource, params = {}) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    return axios.get(resource, {
      ...(params && { params }),
      responseType: "blob",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${TokenService.getToken()}`,
        Accept: "application/json", 
        storeId: thisStore,
        systemLang: thisLang
      },
    });
  },

  post(resource, data, withStoreId = true) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    console.log(thisStore)
    return axios.post(resource, data, {
      headers: {
        Authorization: `Bearer ${TokenService.getToken()}`,
        Accept: "application/json", 
        storeId: withStoreId ? thisStore : null,
        systemLang: thisLang
      },
    });
  },
 

  put(resource, data) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    return axios.put(resource, data, {
      headers: {
        Authorization: `Bearer ${TokenService.getToken()}`,
        "Content-Type": "application/json",
        Accept: "application/json", 
        storeId: thisStore,
        systemLang: thisLang
      },
    });
  },

  delete(resource) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    return axios.delete(resource, {
      headers: {
        Authorization: `Bearer ${TokenService.getToken()}`,
        Accept: "application/json", 
        storeId: thisStore,
        systemLang: thisLang
      },
    });
  },

  customRequest(data) {
    this.init(urlBase);
    UserFunctions.mount401Interceptor();
    return axios(data);
  },
};

export default ApiData;
