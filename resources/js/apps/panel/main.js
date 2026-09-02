import { createApp } from "vue";
import App from "./App.vue";
import "primeicons/primeicons.css";
import VueLazyload from "vue-lazyload";
import { errorHandlerMixin } from "../../handling/responseError"; 
import LaravelPermissionToVueJS from '../../permissions'
import "../../handling/rules";
import { formatAmount } from "../../handling/numberFormat";
import { currency } from "../../handling/currency";
import { timezone_system } from "../../handling/timezone";
import "vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css";

const Vue = createApp(App);

Vue.config.globalProperties.$formatAmount = formatAmount;
Vue.config.globalProperties.$currency = currency;
Vue.config.globalProperties.$timesystem = timezone_system;

// Load PrimeVue modules dynamically
import Prime from "primevue/config";
import ToastService from "primevue/toastservice";
import VueTelInput from "vue-tel-input";
import Vue3Transitions from "vue3-transitions";

// New Component 
const ProgressSpinnerPromise = import("primevue/progressspinner"); 
const TooltipPromise = import("primevue/tooltip");

// Old Component
const ConfirmationServicePromise = import("primevue/confirmationservice");
const storePromise = import("../../store");
const routerPromise = import("./router");
const ToastPromise = import("primevue/toast"); 
const NprogressContainerPromise = import(
    "vue-nprogress/src/NprogressContainer"
); 

Vue.use(Prime, { ripple: true });
Vue.use(ToastService);
Vue.use(VueTelInput);
Vue.use(Vue3Transitions);
Vue.use(LaravelPermissionToVueJS)

Promise.all([
    ProgressSpinnerPromise, 
    TooltipPromise,
    ToastPromise,
    ConfirmationServicePromise,
    storePromise,
    routerPromise,
    NprogressContainerPromise, 
]).then(
    ([
       
        ProgressSpinner, 
        Tooltip,
        Toast,
        ConfirmationService,
        store,
        router,
        NprogressContainer,
    ]) => {
        // ** Primevue
        Vue.mixin(errorHandlerMixin);
        Vue.use(VueLazyload);

        Vue.component("ProgressSpinner", ProgressSpinner.default);
        Vue.directive("Tooltip", Tooltip.default);
        Vue.component("Toast", Toast.default);

        

        Vue.component("NprogressContainer", NprogressContainer.default);

        Vue.use(ConfirmationService.default);
        Vue.use(router.default);
        Vue.use(store.default);

        Vue.mount("#app");

        // Immediately dismiss loader upon mount
        const loader = document.getElementById("global-loader") || document.getElementById("loading");
        if (loader) {
            loader.style.opacity = "0";
            loader.style.pointerEvents = "none";
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
        }
    }
).catch((err) => {
    console.error("Panel App Mount Error:", err);
    const loader = document.getElementById("global-loader") || document.getElementById("loading");
    if (loader) {
        loader.style.pointerEvents = "none";
        if (loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }
});
