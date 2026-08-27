import axios from "axios";
import { ApiData } from "@/api/server";
import { TokenService } from "./service.storage";

const UserFunctions = {
  _401interceptor: null,

  mount401Interceptor(router) {
    // console.log("api.intercept-mount401Interceptor");
    // console.log("interceptor id", this._401interceptor);
    // const instance = axios.create();
    // this._401interceptor = instance.interceptors.response.use(
    this._401interceptor = axios.interceptors.response.use(
      (response) => {
        return response;
      },
      async (error) => {
        if (error.request.status === 401) {
          axios.interceptors.response.eject(this._401interceptor);
          this.logout();
          return Promise.reject(await error);
        }

        // If error was not 401 just reject as is
        // throw error;
        return Promise.reject(await error);
      }
    );
  },

  unmount401Interceptor() {
    // Eject the interceptor
    // console.log("api.intercept-unmount401Interceptor", this._401interceptor);
    axios.interceptors.response.eject(this._401interceptor);
  },
  /**
   * Logout the current user by removing the token from storage.
   *
   * Will also remove `Authorization Bearer <token>` header from future requests.
   * */
  logout() {
    // Remove the token and remove Authorization header from Api Service as well
    TokenService.removeAll();
    TokenService.removeToken();
    ApiData.removeHeaders();
    this.unmount401Interceptor();
    window.location.href = "/auth/login";
  },
};

export default UserFunctions;
