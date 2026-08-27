import ApiData from "@/api/server/modules/api.data";
import { TokenService } from "@/services";

export default {
  namespaced: true,
  state: {},
  mutations: {},
  getters: {},
  actions: {
    async updateProfile({ dispatch, commit }, request) {
      try {
        const response = await ApiData.post(
          "authentication/profile/update",
          request
        );
        var data = response.data;
        TokenService.saveProfile(data.detail);
        return data;
      } catch (error) {
        throw error;
      }
    },

    async askCodeForProfile({ dispatch, commit }, request) {
      try {
        const response = await ApiData.post(
          "authentication/profile/ask-verification",
          request
        );
        var data = response.data;
        return data;
      } catch (error) {
        throw error;
      }
    },

    async updatePhone({ dispatch, commit }, { phone, code }) {
      try {
        const response = await ApiData.post("authentication/profile/phone", {
          phone: phone,
          code: code,
        });
        var data = response.data;
        return data;
      } catch (error) {
        throw error;
      }
    },

    async updatePassword({ dispatch, commit }, request) {
      try {
        const response = await ApiData.post(
          "authentication/profile/password",
          request
        );
        var data = response.data;
        return data;
      } catch (error) {
        throw error;
      }
    },

    async askCodeVerifyEmail({ dispatch, commit }, request) {
      try {
        const response = await ApiData.post(
          "authentication/profile/email/ask-code",
          request
        );
        var data = response.data;
        return data;
      } catch (error) {
        throw error;
      }
    },

    async updateEmail({ dispatch, commit }, request) {
      try {
        const response = await ApiData.post(
          "authentication/profile/email/change-email",
          request
        );
        var data = response.data;
        return data;
      } catch (error) {
        throw error;
      }
    },
  },
};
