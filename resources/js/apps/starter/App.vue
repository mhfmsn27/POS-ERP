<template>
    <Toast />
    <Toast position="top-left" group="tl" />

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
                <div class="main-container">
                    <router-view> </router-view>
                    <nprogress-container></nprogress-container>
                </div>
            </div>
            <!-- Container closed -->
        </div>
        <!-- main-content closed -->
    </div>

    <!-- Footer opened -->
    <FooterComponent />
    <!-- End Footer -->
</template>

<script>
import HeaderComponent from "./Components/HeaderComponent.vue";
import SidebarComponent from "./Components/MenuComponent.vue";
import FooterComponent from "./Components/FooterComponent.vue";
export default {
    components: {
        HeaderComponent,
        SidebarComponent,
        FooterComponent,
    },
    data() {
        return {};
    },

    methods: {
        
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
    },

    mounted() {
        this.SideMenuConfiguration();
        this.layoutDarkLight();
        this.swictherOption(); 
    },

    watch: {
        $route(to, from) {},
    },
};
</script>
