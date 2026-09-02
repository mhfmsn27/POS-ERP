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
import Vue3Transitions from "vue3-transitions";

const InputTextPromise = import("primevue/inputtext");
const DialogPromise = import("primevue/dialog");
const InputNumberPromise = import("primevue/inputnumber");
const MultiselectPromise = import("vue-multiselect");
const ConfirmationServicePromise = import("primevue/confirmationservice");
const storePromise = import("../../store");
const routerPromise = import("./router");
const ToastPromise = import("primevue/toast");
const DropdownPromise = import("primevue/dropdown");
const NprogressContainerPromise = import(
    "vue-nprogress/src/NprogressContainer"
);

const DataTable = import("primevue/datatable");
const Column = import("primevue/column");

Vue.use(Prime, { ripple: true });
Vue.use(ToastService);
Vue.use(VueTelInput);
Vue.use(Vue3Transitions);

Promise.all([
    InputTextPromise,
    DialogPromise,
    InputNumberPromise,
    MultiselectPromise,
    ToastPromise,
    ConfirmationServicePromise,
    storePromise,
    routerPromise,
    NprogressContainerPromise,
    DropdownPromise,
    DataTable,
    Column,
]).then(
    ([
        InputText,
        Dialog,
        InputNumber,
        Multiselect,
        Toast,
        ConfirmationService,
        store,
        router,
        NprogressContainer,
        Dropdown,
        DataTable,
        Column,
    ]) => {
        // ** Primevue
        Vue.mixin(errorHandlerMixin);
        Vue.use(VueLazyload);

        Vue.component("InputText", InputText.default);
        Vue.component("Dialog", Dialog.default);
        Vue.component("InputNumber", InputNumber.default);
        Vue.component("Multiselect", Multiselect.default);
        Vue.component("Form", Form);
        Vue.component("Field", Field);
        Vue.component("Toast", Toast.default);
        Vue.component("Dropdown", Dropdown.default);
        Vue.component("DataTable", DataTable.default);
        Vue.component("Column", Column.default);

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
    console.error("POS App Mount Error:", err);
    const loader = document.getElementById("global-loader") || document.getElementById("loading");
    if (loader) {
        loader.style.pointerEvents = "none";
        if (loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }
});
