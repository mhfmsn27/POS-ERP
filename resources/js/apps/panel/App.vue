<template>
    <Toast />
    <Toast position="top-left" group="tl" />

    <div class="page">
        <div class="page-main">
            <!-- App-Header -->
            <HeaderComponent />
            <!-- End App-Header -->

            <!--App-Sidebar-->
            <SidebarComponent />
            <!-- End App-Sidebar-->

            <!--app-content open-->
            <div class="app-content main-content">
                <div class="side-app">
                    <div
                        class="main-container"
                        v-for="(parents, index) in get_tabs_menus"
                        :key="index"
                    >
                        <div
                            v-for="(menu, m) in parents.links"
                            :key="m"
                            :class="menu.id == get_active_menu ? '' : 'd-none'"
                        >
                            <iframe
                                :src="
                                    menu.params
                                        ? addParamsToUrl(
                                              menu.url,
                                              menu.params,
                                              menu.query
                                          )
                                        : menu.url
                                "
                                frameborder="0"
                                style="width: 100%; height: 100vh"
                            ></iframe>
                        </div>
                    </div>
                </div>
                <!-- Container closed -->
            </div>
            <!-- main-content closed -->
        </div>

        <!-- Sidebar-right-->
        <RightBarComponent />
        <!-- End Sidebar-right-->

        <SwitcherComponent />
        <!-- Siwtcher -->

        <!-- Footer opened -->
        <FooterComponent />
        <!-- End Footer -->
    </div>
</template>

<script>
import HeaderComponent from "./Components/HeaderComponent.vue";
import SidebarComponent from "./Components/SidebarComponent.vue";
import RightBarComponent from "./Components/RightBarComponent.vue";
import SwitcherComponent from "./Components/SwitcherComponent.vue";
import FooterComponent from "./Components/FooterComponent.vue";
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        HeaderComponent,
        SidebarComponent,
        RightBarComponent,
        SwitcherComponent,
        FooterComponent,
    },
    data() {
        return {};
    },

    computed: {
        ...mapGetters("general", [
            "get_tabs_menus",
            "get_histories",
            "get_active_menu",
        ]),
    },
    methods: {
        ...mapActions([
            "general/remove_menu",
            "general/remove_parent",
            "general/set_open_menu",
        ]),

        // Fungsi untuk menambahkan parameter ke URL
        addParamsToUrl(url, params, query) {
            let processedUrl = url;

            // Ganti setiap parameter di URL dengan nilai yang sesuai
            Object.keys(params).forEach((key) => {
                const placeholder = `:${key}`;
                if (processedUrl.includes(placeholder)) {
                    processedUrl = processedUrl.replace(
                        placeholder,
                        params[key]
                    );
                }
            });

            if (query && typeof query === "object") {
                const queryString = Object.keys(query)
                    .map(
                        (key) =>
                            `${encodeURIComponent(key)}=${encodeURIComponent(
                                query[key]
                            )}`
                    )
                    .join("&");

                if (queryString) {
                    processedUrl +=
                        (processedUrl.includes("?") ? "&" : "?") + queryString;
                }
            }

            return processedUrl;
        },

        SideMenuConfiguration() {
            //Active Class
            $(".app-sidebar #sidemenu-Tab a").each(function () {
                var pageUrl = window.location.href.split(/[?#]/)[0];
                if (this.href == pageUrl) {
                    $(this).addClass("active");
                    $(this).parent().parent().parent().addClass("active"); // add active to li of the current link
                    $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                    $(this).parent().parent().prev().click(); // click the item to make it drop
                }
            });
        },

        pScrollConfiguration() {
            const ps = new PerfectScrollbar(".app-sidebar", {
                useBothWheelAxes: true,
                suppressScrollX: true,
                suppressScrollY: false,
            });

            const ps2 = new PerfectScrollbar(".notifications-menu", {
                useBothWheelAxes: true,
                suppressScrollX: true,
                suppressScrollY: false,
            });

            const ps4 = new PerfectScrollbar(".tabs-menu-body", {
                useBothWheelAxes: true,
                suppressScrollX: true,
                suppressScrollY: false,
            });

            // For RightBar
            const ps11 = new PerfectScrollbar(".sidebar-right", {
                useBothWheelAxes: true,
                suppressScrollX: true,
            });

            // For RightBar 1
            const ps5 = new PerfectScrollbar(".sidebar-right1", {
                useBothWheelAxes: true,
                suppressScrollX: true,
            });
        },

        layoutDarkLight() {
            $(".layout-setting").on("click", function (e) {
                if (
                    !document
                        .querySelector("body")
                        .classList.contains("dark-mode")
                ) {
                    $("body").addClass("dark-mode");
                    $("body").removeClass("light-mode");

                    $("body")?.removeClass("color-menu");
                    $("body")?.removeClass("light-menu");
                    $("body")?.removeClass("color-header");
                    $("body")?.removeClass("header-light");

                    $("#myonoffswitch5").prop("checked", true);
                    $("#myonoffswitch8").prop("checked", true);

                    localStorage.setItem("volghdarkMode", true);
                    localStorage.removeItem("volghlightMode");
                    localStorage.removeItem("volghcolorheader");
                    localStorage.removeItem("volghlightheader");
                    localStorage.removeItem("volghdarkheader");
                    localStorage.removeItem("volghdarkmenu");
                    localStorage.removeItem("volghlightmenu");
                    localStorage.removeItem("volghcolormenu");
                    // localStorage.removeItem("volghdarkBody");
                    // localStorage.removeItem("volghdarkTheme");
                    $("#myonoffswitch2").prop("checked", true);
                } else {
                    $("body").addClass("light-mode");
                    $("body").removeClass("dark-mode");
                    localStorage.removeItem("volghdarkMode");
                    localStorage.setItem("volghlightMode", true);
                    localStorage.removeItem("volghdarkBody");
                    localStorage.removeItem("volghcolorheader");
                    localStorage.removeItem("volghdarkheader");
                    localStorage.removeItem("volghlightheader");
                    localStorage.removeItem("volghdarkmenu");
                    localStorage.removeItem("volghlightmenu");
                    localStorage.removeItem("volghcolormenu");
                    localStorage.removeItem("volghdarkTheme");
                    $("#myonoffswitch3").prop("checked", true);
                    $("#myonoffswitch6").prop("checked", true);
                    $("#myonoffswitch1").prop("checked", true);
                }
            });
        },
        swictherOption() {
            jQuery(".demo-icon").click(function () {
                if ($(".demo_changer").hasClass("active")) {
                    $(".demo_changer").animate(
                        { right: "-270px" },
                        function () {
                            $(".demo_changer").toggleClass("active");
                        }
                    );
                } else {
                    $(".demo_changer").animate({ right: "0px" }, function () {
                        $(".demo_changer").toggleClass("active");
                    });
                }
            });

            $(document).on("click", "#closeSwitcher", function () {
                if ($(".demo_changer").hasClass("active")) {
                    $(".demo_changer").animate(
                        { right: "-270px" },
                        function () {
                            $(".demo_changer").toggleClass("active");
                        }
                    );
                }
            });
        },
        handleIframeMessage(event) {
            const { action, data } = event.data;

            // Tangani pesan sesuai kebutuhan
            if (action === "newTab") {
                const routeDetails = this.$router.resolve({
                    name: data.name,
                    params: data.params,
                    query: data.query,
                });
 
                var tabName = routeDetails.params?.name
                    ? routeDetails.meta.title +
                      " - " +
                      routeDetails.params?.name
                    : routeDetails.meta.title;
                this.$store.dispatch("general/set_open_menu", {
                    name: routeDetails.meta.parent.name,
                    title: routeDetails.meta.parent.title,
                    icon: routeDetails.meta.parent.icon,
                    links: {
                        name: routeDetails.name,
                        url: routeDetails.meta.url ?? "",
                        parent: routeDetails.meta.parent.name,
                        title: tabName,
                        icon: routeDetails.meta.icon,
                        params: routeDetails.params ?? {},
                        query: routeDetails.query ?? {},
                    },
                });
            }

            if (action === "closeActiveMenu") { 
                this.$store.dispatch(
                    "general/remove_menu_id",
                    this.get_active_menu
                );
            }
        },
    },

    mounted() {
        window.addEventListener("message", this.handleIframeMessage);
        this.SideMenuConfiguration();
        this.pScrollConfiguration();
        this.layoutDarkLight();
        this.swictherOption();
    },

    watch: {
        $route(to, from) {},
    },
};
</script>
