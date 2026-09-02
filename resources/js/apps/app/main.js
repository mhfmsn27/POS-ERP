import { createApp } from "vue";
import App from "./App.vue";
import "primeicons/primeicons.css";
import VueLazyload from "vue-lazyload";
import { errorHandlerMixin } from "../../handling/responseError";
import { Field, Form } from "vee-validate";
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
const InputTextPromise = import("primevue/inputtext");
const ButtonPromise = import("primevue/button");
const BadgePromise = import("primevue/badge");
const VueCtkDateTimePickerPromise = import("vue-ctk-date-time-picker");
const InputSwitchPromise = import("primevue/inputswitch");
const FileUploadPromise = import("primevue/fileupload");
const MultiselectPromise = import("vue-multiselect");
const DialogPromise = import("primevue/dialog");
const ProgressSpinnerPromise = import("primevue/progressspinner");
const InputNumberPromise = import("primevue/inputnumber");
const DividerPromise = import("primevue/divider");
const CalendarPromise = import("primevue/calendar");
const TooltipPromise = import("primevue/tooltip");

// Old Component
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
Vue.use(LaravelPermissionToVueJS)

Promise.all([
    InputTextPromise,
    ButtonPromise,
    BadgePromise,
    VueCtkDateTimePickerPromise,
    InputSwitchPromise,
    FileUploadPromise,
    MultiselectPromise,
    DialogPromise,
    ProgressSpinnerPromise,
    InputNumberPromise,
    DividerPromise,
    CalendarPromise,
    TooltipPromise,

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
        Button,
        Badge,
        VueCtkDateTimePicker,
        InputSwitch,
        FileUpload,
        Multiselect,
        Dialog,
        ProgressSpinner,
        InputNumber,
        Divider,
        Calendar,
        Tooltip,
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
        Vue.component("Button", Button.default);
        Vue.component("Badge", Badge.default);
        Vue.component("VueCtkDateTimePicker", VueCtkDateTimePicker.default);
        Vue.component("InputSwitch", InputSwitch.default);
        Vue.component("FileUpload", FileUpload.default);
        Vue.component("Multiselect", Multiselect.default);
        Vue.component("Dialog", Dialog.default);
        Vue.component("ProgressSpinner", ProgressSpinner.default);
        Vue.component("InputNumber", InputNumber.default);
        Vue.component("Calendar", Calendar.default);
        Vue.component("Divider", Divider.default);
        Vue.directive("Tooltip", Tooltip.default);

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
    console.error("Main Backoffice App Mount Error:", err);
    const loader = document.getElementById("global-loader") || document.getElementById("loading");
    if (loader) {
        loader.style.pointerEvents = "none";
        if (loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }
});
