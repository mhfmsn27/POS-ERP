<template>
    <div class="app-header header sticky">
        <div class="container-fluid main-container">
            <div
                class="d-flex align-items-center"
                :class="get_tabs_menus.length > 0 ? 'border-bottom' : ''"
            >
                <a
                    aria-label="Hide Sidebar"
                    class="app-sidebar__toggle"
                    data-bs-toggle="sidebar"
                    href="javascript:void(0);"
                ></a>
                <!-- sidebar-toggle-->
                <router-link class="logo-horizontal" :to="{ name: 'home' }">
                    <img
                        :src="asset.logo"
                        class="header-brand-img desktop-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img light-logo1"
                        alt="logo"
                    />
                </router-link>
                <!-- LOGO -->

                <div class="d-flex order-lg-2">
                    <div class="mt-2">
                        <h4>{{ store.name }}</h4>
                    </div>
                </div>

                <div class="d-flex order-lg-2 ms-auto header-right-icons">
                    <div class="navbar navbar-collapse responsive-navbar p-0">
                        <div
                            class="collapse navbar-collapse navbarSupportedContent-4"
                            id="navbarSupportedContent-4"
                        >
                            <div class="d-flex order-lg-2">
                                <div class="d-flex">
                                    <a class="nav-link icon" href="/pos">
                                        <span
                                            ><i class="fa fa-desktop"></i
                                        ></span>
                                    </a>
                                </div>

                                <!-- Layout -->
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
                                <!-- FULL-SCREEN -->

                                <div class="dropdown d-flex notifications">
                                    <a
                                        class="nav-link icon"
                                        data-bs-toggle="dropdown"
                                        ><i class="fe fe-bell"></i
                                        ><span class="pulse"></span>
                                    </a>
                                    <div
                                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow"
                                    >
                                        <div class="drop-heading border-bottom">
                                            <div class="d-flex">
                                                <h6
                                                    class="mt-1 mb-0 fs-16 fw-semibold text-dark"
                                                >
                                                    Notifications
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="notifications-menu">
                                            <a
                                                class="dropdown-item d-flex"
                                                href=""
                                            >
                                                <div
                                                    class="me-3 notifyimg bg-primary brround box-shadow-primary my-auto"
                                                >
                                                    <i class="fe fe-mail"></i>
                                                </div>
                                                <div class="mt-1 wd-80p">
                                                    <h5
                                                        class="notification-label mb-1"
                                                    >
                                                        New Application received
                                                    </h5>
                                                    <span
                                                        class="notification-subtext"
                                                        >3 days ago</span
                                                    >
                                                </div>
                                            </a>
                                            <a
                                                class="dropdown-item d-flex"
                                                href=""
                                            >
                                                <div
                                                    class="me-3 notifyimg bg-secondary brround box-shadow-secondary my-auto"
                                                >
                                                    <i
                                                        class="fe fe-check-circle"
                                                    ></i>
                                                </div>
                                                <div class="mt-1 wd-80p">
                                                    <h5
                                                        class="notification-label mb-1"
                                                    >
                                                        Project has been
                                                        approved
                                                    </h5>
                                                    <span
                                                        class="notification-subtext"
                                                        >2 hours ago</span
                                                    >
                                                </div>
                                            </a>
                                            <a
                                                class="dropdown-item d-flex"
                                                href=""
                                            >
                                                <div
                                                    class="me-3 notifyimg bg-success brround my-auto"
                                                >
                                                    <i
                                                        class="fe fe-shopping-cart"
                                                    ></i>
                                                </div>
                                                <div class="mt-1 wd-80p">
                                                    <h5
                                                        class="notification-label mb-1"
                                                    >
                                                        Your Product Delivered
                                                    </h5>
                                                    <span
                                                        class="notification-subtext"
                                                        >30 min ago</span
                                                    >
                                                </div>
                                            </a>
                                            <a
                                                class="dropdown-item d-flex"
                                                href=""
                                            >
                                                <div
                                                    class="me-3 notifyimg bg-pink brround box-shadow-pink my-auto"
                                                >
                                                    <i
                                                        class="fe fe-user-plus"
                                                    ></i>
                                                </div>
                                                <div class="mt-1 wd-80p">
                                                    <h5
                                                        class="notification-label mb-1"
                                                    >
                                                        Friend Requests
                                                    </h5>
                                                    <span
                                                        class="notification-subtext"
                                                        >1 day ago</span
                                                    >
                                                </div>
                                            </a>
                                        </div>
                                        <div class="dropdown-divider m-0"></div>
                                        <div class="dropdown-footer p-4">
                                            <a
                                                class="btn btn-primary btn-pill w-sm btn-sm py-2 btn-block fs-14"
                                                href=""
                                                >View All</a
                                            >
                                        </div>
                                    </div>
                                </div>
                                <!-- NOTIFICATIONS -->

                                <!-- SIDE-MENU -->
                                <div class="dropdown d-flex profile-1">
                                    <a
                                        href="javascript:void(0);"
                                        data-bs-toggle="dropdown"
                                        class="nav-link leading-none d-flex"
                                    >
                                        <img
                                            :src="user.photo"
                                            :alt="user.name"
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
                                        <router-link
                                            class="dropdown-item"
                                            :to="{
                                                name: 'profile',
                                                query: { tab: true },
                                            }"
                                        >
                                            <i
                                                class="dropdown-icon fe fe-user"
                                            ></i>
                                            Profile
                                        </router-link>
                                        <a
                                            class="dropdown-item"
                                            href="javascript:void(0);"
                                            @click="starterPage"
                                        >
                                            <i
                                                class="dropdown-icon fe fe-grid"
                                            ></i>
                                            Halaman Starter
                                        </a>
                                        <a
                                            class="dropdown-item"
                                            href="javascript:void(0);"
                                            @click="logoutAccount"
                                        >
                                            <i
                                                class="dropdown-icon fe fe-log-out"
                                            ></i>
                                            Log Out
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="demo-icon nav-link icon">
                        <i class="fe fe-settings fa-spin text_primary"></i>
                    </div>
                </div>
            </div>
            <div
                class="panel panel-primary"
                :class="get_tabs_menus.length > 0 ? 'mt-2' : ''"
            >
                <div class="tab-menu-heading">
                    <div class="tabs-menu">
                        <ul class="nav panel-tabs panel-info" role="tablist">
                            <li
                                class="d-flex justify-content-between"
                                v-for="(parents, index) in get_tabs_menus"
                                :key="index"
                            >
                                <a
                                    style="border-radius: 0 !important"
                                    @click="clickParent(parents)"
                                    :href="`#tab${parents.name}`"
                                    :class="
                                        openDetailActiveMenu() == parents.name
                                            ? 'active'
                                            : ''
                                    "
                                    data-bs-toggle="tab"
                                    aria-selected="true"
                                    role="tab"
                                >
                                    <span>
                                        <i
                                            :class="parents.icon + ' me-1'"
                                        ></i></span
                                    >{{ parents.title }}
                                </a>
                                <a
                                    href="javascript:void(0);"
                                    @click="closeParent(parents, index)"
                                    class="nav-link"
                                    style="border-radius: 0 !important"
                                    :class="
                                        openDetailActiveMenu() == parents.name
                                            ? 'active'
                                            : ''
                                    "
                                >
                                    <span
                                        ><i class="fe fe-x-circle fs-14"></i
                                    ></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tabs-menu-body">
                    <div class="tab-content">
                        <div
                            v-for="(parents, index) in get_tabs_menus"
                            :key="index"
                            class="tab-pane tab-header"
                            :class="
                                openDetailActiveMenu() == parents.name
                                    ? 'active'
                                    : ''
                            "
                            :id="`tab${parents.name}`"
                            role="tabpanel"
                        >
                            <ul
                                class="nav nav-tabs tab-style-1 d-sm-flex d-block"
                                role="tablist"
                            >
                                <li
                                    class="nav-item d-flex justify-content-between"
                                    role="presentation"
                                    v-for="(menu, m) in parents.links"
                                    :key="m"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        @click="openMenu(menu.id)"
                                        class="nav-link"
                                        :class="
                                            menu.id == get_active_menu
                                                ? 'active'
                                                : ''
                                        "
                                    >
                                        <span
                                            ><i :class="menu.icon + ' me-1'"></i
                                        ></span>
                                        {{ menu.title }}
                                    </a>
                                    <a
                                        href="javascript:void(0);"
                                        @click="closeMenu(menu, parents, m)"
                                        class="nav-link"
                                        :class="
                                            menu.id == get_active_menu
                                                ? 'active'
                                                : ''
                                        "
                                    >
                                        <span
                                            ><i class="fe fe-x-circle fs-14"></i
                                        ></span>
                                    </a>
                                </li>
                            </ul>
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
            store: {
                name: "",
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
        ...mapGetters("general", [
            "get_tabs_menus",
            "get_histories",
            "get_active_menu",
        ]),
    },
    created() {
        this.userData();
    },
    mounted() {
        var stickyElement = $(".sticky"),
            stickyClass = "sticky-pin",
            stickyPos = stickyElement.offset().top, //Distance from the top of the window.
            stickyHeight;

        //Create a negative margin to prevent content 'jumps':
        stickyElement.after('<div class="jumps-prevent"></div>');

        this.jumpsPrevent(); //Run.

        //Function trigger:
        $(document).on("click", "#resetAll", () => {
            this.resetData();
        });

        $(window).resize(this.jumpsPrevent());
    },
    methods: {
        ...mapActions([
            "general/remove_menu",
            "general/remove_parent",
            "general/set_active_menu",
        ]),

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

            if (TokenService.getStore() != null) {
                this.store.name = TokenService.getStore().name;
            } else {
                this.starterPage();
            }
        },

        logoutAccount() {
            NProgress.start();
            NProgress.set(0.1);
            this.$store.dispatch("auth/signOut");
        },

        starterPage() {
            var url = "/starter/choose-store";
            return (window.location = url);
        },

        jumpsPrevent() {
            var stickyElement = $(".sticky"),
                stickyClass = "sticky-pin",
                stickyPos = stickyElement.offset().top, //Distance from the top of the window.
                stickyHeight;

            stickyHeight = stickyElement.innerHeight();
            stickyHeight =
                stickyHeight < 100
                    ? stickyHeight + (this.get_tabs_menus.length > 0 ? 96 : 0)
                    : stickyHeight;
            stickyHeight =
                stickyHeight > 100
                    ? stickyHeight - (this.get_tabs_menus.length == 0 ? 96 : 0)
                    : stickyHeight;

            stickyElement.css({ "margin-bottom": "-" + stickyHeight + "px" });
            stickyElement.next().css({ "padding-top": +stickyHeight + "px" });
        },

        closeMenu(menu, parents, index) {
            const totalParents = parents.links.length;

            if (totalParents > 1) {
                // Tentukan menu sebelum atau setelah menu yang ditutup
                let beforeMenu =
                    index > 0
                        ? parents.links[index - 1]
                        : parents.links[index + 1];

                if (beforeMenu) {
                    // Navigasi ke menu sebelum atau setelahnya
                    this.openMenu(beforeMenu.id);
                } else {
                    this.openHomeTab()
                }
            } else {
                this.openHomeTab()
            }

            // Hapus menu dari daftar
            this["general/remove_menu"](menu);
        },

        openMenu(id) {
            this["general/set_active_menu"](id);
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
                        this.openMenu(beforemenu.links[0].id);
                    } else {
                        this.openHomeTab();
                    }
                } else {
                    this.openHomeTab();
                }
            } else {
                this.openHomeTab();
            }
            this["general/remove_parent"](menu);
        },

        openHomeTab() {
            const routeDetails = this.$router.resolve({
                name: "home",
            });

            this.$store.dispatch("general/set_open_menu", {
                name: routeDetails.meta.parent.name,
                title: routeDetails.meta.title,
                icon: routeDetails.meta.parent.icon,
                links: {
                    name: routeDetails.name,
                    url: routeDetails.meta.url ?? "",
                    parent: routeDetails.meta.parent.name,
                    title: routeDetails.meta.title,
                    icon: routeDetails.meta.icon,
                    params: {},
                },
            });
        },

        clickParent(parents) {
            if (parents.links && parents.links.length > 0) {
                const firstSubTab = parents.links[0];
                this.openMenu(firstSubTab.id);
            }
        },

        openDetailActiveMenu() {
            var findIndex = this.get_histories.findIndex(
                (el) => el.id === this.get_active_menu
            );

            if (findIndex != -1) {
                var detail = this.get_histories[findIndex];
                return detail.parent;
            }

            return "";
        },
    },
    watch: {
        get_tabs_menus: {
            handler: function (newVal, oldVal) {
                var jumpSticky = $(".jumps-prevent");

                if (jumpSticky.length > 0) {
                    this.jumpsPrevent();
                }
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
