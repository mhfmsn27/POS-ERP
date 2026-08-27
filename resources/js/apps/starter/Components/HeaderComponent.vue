<template>
    <div class="app-header header sticky">
        <div class="container-fluid main-container">
            <div class="d-flex align-items-center">
                <a
                    aria-label="Hide Sidebar"
                    class="app-sidebar__toggle"
                    data-bs-toggle="sidebar"
                    href="javascript:void(0);"
                ></a>
                <!-- sidebar-toggle-->
                <router-link
                    :to="{ name: 'choose_store' }"
                    class="logo-horizontal"
                    href="#"
                >
                    <img
                        :src="asset.logo"
                        class="header-brand-img desktop-logo"
                        alt="logo"
                        style="width: 200px"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img light-logo1"
                        alt="logo"
                        style="width: 200px"
                    />
                </router-link>
                <!-- LOGO -->

                <div class="d-flex order-lg-2 ms-auto header-right-icons">
                    <!-- SEARCH -->
                    <button
                        class="navbar-toggler navresponsive-toggler d-lg-none ms-auto"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent-4"
                        aria-controls="navbarSupportedContent-4"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span
                            class="navbar-toggler-icon fe fe-more-vertical"
                        ></span>
                    </button>
                    <div class="navbar navbar-collapse responsive-navbar p-0">
                        <div
                            class="collapse navbar-collapse navbarSupportedContent-4"
                            id="navbarSupportedContent-4"
                        >
                            <div class="d-flex order-lg-2">
                                <div class="d-flex">
                                    <a
                                        class="nav-link icon theme-layout nav-link-bg layout-setting"
                                    >
                                        <span class="dark-layout"
                                            ><i class="fe fe-moon"></i
                                        ></span>
                                        <span class="light-layout"
                                            ><i class="fe fe-sun"></i
                                        ></span>
                                    </a>
                                </div>
                                <!-- Theme-Layout -->

                                <div class="dropdown d-flex">
                                    <a
                                        class="nav-link icon full-screen-link nav-link-bg"
                                    >
                                        <i
                                            class="fe fe-minimize fullscreen-button"
                                        ></i>
                                    </a>
                                </div>
                                <!-- FULL-SCREEN -->

                                <div class="dropdown d-flex profile-1">
                                    <a
                                        href="javascript:void(0);"
                                        data-bs-toggle="dropdown"
                                        class="nav-link leading-none d-flex"
                                    >
                                        <img
                                            :src="user.photo"
                                            alt="profile-user"
                                            class="avatar profile-user brround cover-image"
                                        />
                                    </a>
                                    <div
                                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow"
                                    >
                                        <div class="drop-heading">
                                            <div class="text-center">
                                                <h5
                                                    class="text-dark mb-0 fs-14 fw-semibold"
                                                >
                                                    {{ user.name }}
                                                </h5>
                                                <small class="text-muted">{{
                                                    user.email
                                                }}</small>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider m-0"></div>

                                        <a
                                            class="dropdown-item"
                                            href="javascript:void(0);"
                                            @click="logoutAccount"
                                        >
                                            <i
                                                class="dropdown-icon fe fe-alert-circle"
                                            ></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import logoApp from "@/assets/images/logo.webp";
import { TokenService } from "@/services";
import NProgress from "nprogress";
export default {
    name: "HeaderComponent",
    data: function () {
        return {
            asset: {
                logo: logoApp,
            },
            user: {
                name: "",
                email: "",
                photo: "",
            },
            menus: [],
            links: [],
        };
    },
    computed: {
        ...mapGetters("general", ["get_tabs_menus", "get_histories"]),
    },
    created() {
        this.userData();
    },
    mounted() {
        this.stickerFn();

        $(document).on("click", "#resetAll", () => {
            this.resetData();
        });

        $(window).on("scroll", () => {
            this.stickerFn();
        });
    },
    methods: {
        ...mapActions(["general/remove_menu", "general/remove_parent"]),

        stickerFn() {
            let stickyElement = $(".sticky");
            let stickyPos = 66;
            let winTop = $(window).scrollTop();
            //Check element position:
            winTop >= stickyPos
                ? stickyElement.addClass("stickyClass")
                : stickyElement.removeClass("stickyClass"); //Boolean class switcher.
        },

        userData() {
            if (
                TokenService.getToken() != null &&
                TokenService.getProfile() != null
            ) {
                this.user = {
                    name: TokenService.getProfile().name,
                    email: TokenService.getProfile().email,
                    phone: TokenService.getProfile().phone,
                    photo: TokenService.getProfile().photo,
                };
            } else {
                this.logoutAccount();
            }
        },

        logoutAccount() {
            NProgress.start();
            NProgress.set(0.1);
            this.$store.dispatch("auth/signOut");
        },

        closeMenu(menu, parents, index) {
            if (parents.links.length > 1) {
                var totalparents = parents.links.length;

                var beforemenu = parents.links[totalparents - 2];
                if (index == totalparents - 2) {
                    beforemenu = parents.links[index + 1];
                }

                if (beforemenu) {
                    this.$router.push({ name: beforemenu.name });
                }
            }

            this["general/remove_menu"](menu);
        },

        closeParent(menu, index) {
            var totalmenus = this.get_tabs_menus.length;
            if (totalmenus > 0) {
                var beforemenu = this.get_tabs_menus[totalmenus - 2];
                if (index == totalmenus - 2) {
                    beforemenu = this.get_tabs_menus[index + 1];
                }

                if (beforemenu) {
                    if (beforemenu.links[0]) {
                        this.$router.push({ name: beforemenu.links[0].name });
                    }
                }
            }

            this["general/remove_parent"](menu);
        },
    },
};
</script>
