import Crypto from "crypto-js";
import VueCookies from "vue-cookies";

const SECRET_KEY = "secret_key_fakturco";
const PROFILE_KEY = "profile_key_fakturco";
const REFRESH_TOKEN_KEY = "refresh_token_fakturco";
const COOKIES_SECRET = "auth_app_secret_fakturco";
const COOKIES_TOKEN = "auth_app_token_fakturco";
const COOKIES_VERIFY = "auth_app_verify_fakturco";
const COOKIES_MERCHANT = 'auth_app_merchant_fakturco';
const PERMISSION_KEY = "permission_key";
const LANGDEFAULT = "lang_default_choosed";
const CURRENCYDEFAULT = "currency_option_default";
const TIMEZONESYSTEM = "timezone_default"; 
const STOREIDKEY = 'store_id_key';

const TokenService = {
    getLang() {
        const lang = localStorage.getItem(LANGDEFAULT);
        if (lang) {
            try {
                return lang;
            } catch (e) {
                this.removeStore();
            }
        }
        return "en";
    },

    getStore() {
        const store = localStorage.getItem(STOREIDKEY);
        if (store) {
          try {
            const bytes = Crypto.AES.decrypt(store, STOREIDKEY);
            return JSON.parse(bytes.toString(Crypto.enc.Utf8));
          } catch (e) {
            this.removeStore();
          }
        }
        return null;
      },


    getTimezone() {
        const timedata = localStorage.getItem(TIMEZONESYSTEM);
        if (timedata) {
            try {
                return timedata;
            } catch (e) {
                console.log("Error timezone");
            }
        }
        return "Asia/Dhaka";
    },

    getCurrency() {
        const currency = localStorage.getItem(CURRENCYDEFAULT);
        if (currency) {
            try {
                const bytes = Crypto.AES.decrypt(currency, CURRENCYDEFAULT);
                return JSON.parse(bytes.toString(Crypto.enc.Utf8));
            } catch (e) {
                console.log(e, "error currency");
            }
        }

        return {
            symbol: "Rp ",
            position: "before",
            code: "IDR",
        };
    },

    getToken() {
        const store = VueCookies.get(COOKIES_TOKEN);
        if (store) {
            try {
                const bytes = Crypto.AES.decrypt(store, COOKIES_TOKEN);
                return bytes.toString(Crypto.enc.Utf8);
            } catch (e) {
                this.removeToken();
            }
        }
        return null;
    },

    getVerify() {
        const store = VueCookies.get(COOKIES_VERIFY);
        if (store) {
            try {
                const bytes = store;
                return bytes;
            } catch (e) {
                this.removeVerify();
            }
        }
        return null;
    },

    getMerchant() {
        const store = VueCookies.get(COOKIES_MERCHANT);
        if (store) {
            try {
                const bytes = store;
                return bytes;
            } catch (e) {
                this.removeMerchant();
            }
        }
        return null;
    },

    getSecret() {
        const store = VueCookies.get(SECRET_KEY);
        if (store) {
            try {
                const bytes = Crypto.AES.decrypt(store, SECRET_KEY);
                return bytes.toString(Crypto.enc.Utf8);
            } catch (e) {
                this.removeSecret();
            }
        }
        return null;
    },

    getProfile() {
        const store = localStorage.getItem(PROFILE_KEY);
        if (store) {
            try {
                const bytes = Crypto.AES.decrypt(store, PROFILE_KEY);
                return JSON.parse(bytes.toString(Crypto.enc.Utf8));
            } catch (e) {
                this.removeAll();
            }
        }
        return null;
    },
 

    saveToken(accessToken) {
        const storage = Crypto.AES.encrypt(
            accessToken,
            COOKIES_TOKEN
        ).toString();
        VueCookies.set(COOKIES_TOKEN, storage);
    },

    saveVerify(verify) {
        verify = verify;
        if (verify != null) {
            VueCookies.set(COOKIES_VERIFY, verify);
        }
    },

    saveMerchant(merchant) {
        merchant = merchant;
        if (merchant != null) {
            VueCookies.set(COOKIES_MERCHANT, merchant);
        }
    },

    saveLang(lang) {
        localStorage.setItem(LANGDEFAULT, lang);
    },

    saveTimezone(timedata) {
        localStorage.setItem(TIMEZONESYSTEM, timedata);
    },

    saveSecret(secretKey) {
        const storage = Crypto.AES.encrypt(secretKey, SECRET_KEY).toString();
        VueCookies.set(COOKIES_SECRET, storage);
    },

    saveProfile(secretKey) {
        secretKey = JSON.stringify(secretKey);
        const storage = Crypto.AES.encrypt(secretKey, PROFILE_KEY).toString();
        localStorage.setItem(PROFILE_KEY, storage);
    },
 
    saveStore(stores) {
        var store = JSON.stringify(stores);
        const storedata = Crypto.AES.encrypt(store, STOREIDKEY).toString();
        localStorage.setItem(STOREIDKEY, storedata);
      },


    saveCurrency(currencySet) {
        currencySet = JSON.stringify(currencySet);
        const storage = Crypto.AES.encrypt(
            currencySet,
            CURRENCYDEFAULT
        ).toString();
        localStorage.setItem(CURRENCYDEFAULT, storage);
    },

    removeStore() {
        VueCookies.remove(STOREIDKEY);
      },

    removeToken() {
        VueCookies.remove(COOKIES_TOKEN);
    },

    removeVerify() {
        VueCookies.remove(COOKIES_VERIFY);
    },

    removeMerchant() {
        VueCookies.remove(COOKIES_MERCHANT);
    },

    removeSecret() {
        VueCookies.remove(SECRET_KEY);
    },

    removeProfile() {
        localStorage.removeItem(PROFILE_KEY);
    }, 

    getRefreshToken() {
        const store = localStorage.getItem(REFRESH_TOKEN_KEY);
        if (store) {
            try {
                const bytes = Crypto.AES.decrypt(store, REFRESH_TOKEN_KEY);
                return bytes.toString(Crypto.enc.Utf8);
            } catch (e) {
                this.removeRefreshToken();
            }
        }
        return null;
    },

    saveRefreshToken(refreshToken) {
        const storage = Crypto.AES.encrypt(
            refreshToken,
            REFRESH_TOKEN_KEY
        ).toString();
        localStorage.setItem(REFRESH_TOKEN_KEY, storage);
    },

    removeRefreshToken() {
        localStorage.removeItem(REFRESH_TOKEN_KEY);
    },

    removeProfile() {
        localStorage.removeItem(PROFILE_KEY);
    },

    removeAll() {
        localStorage.removeItem(REFRESH_TOKEN_KEY);
        localStorage.removeItem(PROFILE_KEY);
        VueCookies.remove(SECRET_KEY);
        VueCookies.remove(COOKIES_TOKEN);
        VueCookies.remove(COOKIES_VERIFY);
        VueCookies.remove(PROFILE_KEY); 
        VueCookies.remove(COOKIES_MERCHANT);
        VueCookies.remove(STOREIDKEY);
        localStorage.removeItem(PERMISSION_KEY);
        localStorage.clear();
    },
};

export { TokenService };
