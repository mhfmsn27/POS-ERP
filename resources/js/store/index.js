import Vuex from "vuex";
import general from "./general/index";
import auth from "./auth";

export default new Vuex.Store({
    modules: {
        general: general,
        auth: auth,
    },
});
