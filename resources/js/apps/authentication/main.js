// import "../../registerServiceWorker.js";
import { createApp } from "vue";
import App from "./App.vue";
import "primeicons/primeicons.css";  
import VueLazyload from "vue-lazyload";
import { errorHandlerMixin } from "../../handling/responseError";
import { Field, Form } from "vee-validate";

import "../../handling/rules";
import { formatAmount } from "../../handling/numberFormat";
import { currency } from "../../handling/currency";
import { timezone_system } from "../../handling/timezone"; 

const Vue = createApp(App);

Vue.config.globalProperties.$formatAmount = formatAmount;
Vue.config.globalProperties.$currency = currency;
Vue.config.globalProperties.$timesystem = timezone_system;

// Load PrimeVue modules dynamically
import Prime from "primevue/config";
import ToastService from "primevue/toastservice";
import VueTelInput from "vue-tel-input";
import Vue3Transitions from 'vue3-transitions'

const ConfirmationServicePromise = import("primevue/confirmationservice");
const storePromise = import("../../store");
const routerPromise = import("./router"); 
const ToastPromise = import("primevue/toast"); 
const DropdownPromise = import("primevue/dropdown"); 
const NprogressContainerPromise = import(
    "vue-nprogress/src/NprogressContainer"
);

Vue.use(Prime, { ripple: true });
Vue.use(ToastService);
Vue.use(VueTelInput); 
Vue.use(Vue3Transitions)

Promise.all([
    ToastPromise,
    ConfirmationServicePromise,
    storePromise,
    routerPromise,
    NprogressContainerPromise,  
    DropdownPromise, 
]).then(
    ([
        Toast,
        ConfirmationService,
        store,
        router,
        NprogressContainer,  
        Dropdown, 
    ]) => {
        // ** Primevue
        Vue.mixin(errorHandlerMixin); 
        Vue.use(VueLazyload);

        Vue.component("Form", Form);
        Vue.component("Field", Field);
        Vue.component("Toast", Toast.default); 
        Vue.component("Dropdown", Dropdown.default); 

        Vue.component("NprogressContainer", NprogressContainer.default);

        Vue.use(ConfirmationService.default);
        Vue.use(router.default); 
        Vue.use(store.default);

        Vue.mount("#app");

        // Immediately dismiss loader upon mount
        const loader = document.getElementById("global-loader");
        if (loader) {
            loader.style.transition = "opacity 0.2s ease";
            loader.style.opacity = "0";
            setTimeout(() => { loader.style.display = "none"; }, 200);
        }
    }
).catch((err) => {
    console.error("Authentication App Mount Error:", err);
    const loader = document.getElementById("global-loader");
    if (loader) {
        loader.style.display = "none";
    }
});
