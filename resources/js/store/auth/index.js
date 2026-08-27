import ApiData from "@/api/server/modules/api.data";
import { TokenService } from "@/services";

export default {
    namespaced: true,
    state: { user: null, isLoggedIn: false, permission: {} },
    mutations: {
        set_user(state, data) {
            state.user = data;
            state.isLoggedIn = true;
        },
        reset_user(state) {
            state.user = null;
            state.isLoggedIn = false;
        },
        set_permission(state, data) {
            state.permission = data;
        },
    },
    getters: {
        isLoggedIn(state) {
            return state.isLoggedIn;
        },
        user(state) {
            return state.user;
        },
        permission(state) {
            return state.permission;
        },
    },
    actions: {
        // Sign Int
        async signInt({ dispatch, commit }, request) {
            try {
                const response = await ApiData.post("authentication/login", request);

                var data = response.data;
                TokenService.saveToken(data.token);
                TokenService.saveProfile(data.data);
                var verify = data.data.verify == true ? 1 : null;
                TokenService.saveVerify(verify); 
                TokenService.saveMerchant(data.data.merchant == true ? 1 : null);
                return data;
            } catch (error) {
                throw error;
            }
        },

        // Sign Out
        signOut({ dispatch, commit }) {
            ApiData.post("authentication/logout")
                .then((res) => {
                    var url = "/authentication/login"; 

                    TokenService.removeVerify();
                    TokenService.removeToken();
                    TokenService.removeMerchant();
                    TokenService.removeAll();
                    return (window.location = url);
                })
                .catch((err) => {
                    var url = "/authentication/login";

                    TokenService.removeVerify();
                    TokenService.removeToken();
                    TokenService.removeMerchant();
                    TokenService.removeAll();
                    return (window.location = url);
                });
        },

        // Ask Verify Code Email
        async askCodeForgetPassword({ dispatch, commit }, email) {
            try {
                const response = await ApiData.post(
                    "authentication/forget-password/ask-code",
                    {
                        email: email,
                    }
                );
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        // Email Forget Password Verification
        async emailForgetVerification({ dispatch, commit }, { code, email }) {
            try {
                const response = await ApiData.post(
                    "authentication/forget-password/verification-code",
                    {
                        two_factor_code: code,
                        email: email,
                    }
                );
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        // Reset Password
        async resetPassword({ dispatch, commit }, request) {
            try {
                const response = await ApiData.post(
                    "authentication/forget-password/change",
                    request
                );
                var data = response.data;
                TokenService.saveToken(data.token);
                TokenService.saveVerify(data.detail.verify == true ? 1 : null);
                TokenService.saveProfile(data.detail);
                TokenService.saveMerchant(data.data.merchant == true ? 1 : null);
                return data;
            } catch (error) {
                throw error;
            }
        },

        // Resend Ask Email Code Verification
        async resendAsk({ dispatch, commit }, request) {
            try {
                const response = await ApiData.post(
                    "authentication/register/send-verify-code",
                    request
                );
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        // Send Verification Email
        async verificationEmail({ dispatch, commit }, code) {
            try {
                const response = await ApiData.post(
                    "authentication/register/mail-verification",
                    {
                        two_factor_code: code,
                    }
                );
                TokenService.saveVerify(1);
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        async getPermission({ dispatch, commit }) {
            try {
                const response = await ApiData.get(
                    "authentication/get-my-permission"
                );
                const pagePermissions = {};

                if (response.data.status == true) {
                    response.data.permissions.forEach((permission) => {
                        pagePermissions[permission] = true;
                    });
                    commit("set_permission", pagePermissions);
                    return "success";
                } else {
                    return response.data.message;
                }
            } catch (error) {
                throw error;
            }
        },
    },
};
