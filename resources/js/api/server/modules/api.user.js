// import ApiData from "./api.data";
// import { TokenService } from "@/services/modules/service.storage";
// import store from "@/store";
// import AuthenticationError from "@/services/modules/service.autherror";

/**
 * Services for user
 *
 * Fungsi2 utama seperti login dan logout
 * */
const ApiUser = {
  // /**
  //  * Login the user and store the access token to TokenService.
  //  *
  //  * @returns access_token
  //  * @throws AuthenticationError
  //  * */
  // async login(usr, pwd) {
  //   const requestData = {
  //     method: "post",
  //     url: "api/login",
  //     data: {
  //       email: usr,
  //       password: pwd,
  //     },
  //   };

  //   try {
  //     const response = await ApiData.customRequest(requestData);

  //     const dta = response.data;
  //     TokenService.saveToken(dta.token);
  //     TokenService.saveProfile(dta.data);
  //     ApiData.setHeaders(`Bearer ${dta.token}`);
  //     return dta;
  //   } catch (error) {
  //     if (error.response.status === 401) {
  //       throw new AuthenticationError(401, "Login Failed");
  //     } else if (error.response.status === 500) {
  //       throw new AuthenticationError(500, "Network Problem");
  //     } else {
  //       throw new AuthenticationError(
  //         error.response.status,
  //         error.response.statusText
  //       );
  //     }
  //   }
  // },

  // /**
  //  * Refresh the access token.
  //  * */
  // async refreshToken() {
  //   const refreshToken = TokenService.getRefreshToken();

  //   const requestData = {
  //     method: "post",
  //     url: "api/refresh-token",
  //     data: {
  //       refresh_token: refreshToken,
  //     },
  //   };

  //   try {
  //     ApiData.setHeaders(
  //       `Bearer ${TokenService.getToken()}`,
  //       "application/json",
  //       TokenService.getSecret()
  //     );
  //     const response = await ApiData.customRequest(requestData);
  //     const dta = response.data.data;
  //     TokenService.saveToken(dta.access_token);
  //     TokenService.saveSecret(dta.username);
  //     TokenService.saveRefreshToken(dta.refresh_token);
  //     TokenService.saveSession(dta.session);
  //     // Update the header in ApiService
  //     ApiData.setHeaders(
  //       `Bearer ${TokenService.getToken()}`,
  //       "application/json",
  //       TokenService.getSecret()
  //     );

  //     return response;
  //   } catch (error) {
  //     if (error.response.status === 401 || error.response.status === 400) {
  //       const router = this.$router;
  //       store.dispatch("login/logout", { router });
  //       // UserFunctions.logout();
  //     }
  //     throw new AuthenticationError(
  //       error.response.status || "",
  //       error.response.statusText || ""
  //     );
  //   }
  // },
};

export default ApiUser;
