<template>
    <div class="sticky">
        <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
        <div class="app-sidebar">
            <div class="side-header">
                <router-link
                    class="header-brand1"
                    :to="{ name: 'choose_store' }"
                >
                    <img
                        :src="asset.logo"
                        class="header-brand-img desktop-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img toggle-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img light-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img light-logo1"
                        alt="logo"
                    />
                </router-link>
                <!-- LOGO -->
            </div>
            <div class="main-sidemenu">
                <div class="slide-left disabled" id="slide-left">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="#7b8191"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"
                        />
                    </svg>
                </div>
                <ul class="side-menu">
                    <li class="sub-category">
                        <h3>Main</h3>
                    </li>
                    <li class="slide">
                        <router-link
                            :to="{ name: 'choose_store' }"
                            class="side-menu__item"
                            :class="
                                $route.name == 'choose_store' ? 'active' : ''
                            "
                            ><i class="side-menu__icon fa fa-bank"></i
                            ><span class="side-menu__label"
                                >Pilih Toko / Cabang</span
                            >
                        </router-link>
                    </li>

                    <li>
                        <router-link
                            :to="{ name: 'packages' }"
                            class="side-menu__item has-link"
                            :class="$route.name == 'packages' ? 'active' : ''"
                            ><i class="side-menu__icon fe fe-package"></i
                            ><span class="side-menu__label"
                                >Pilihan Paket Langganan</span
                            ></router-link
                        >
                    </li>
                    <li>
                        <router-link
                            class="side-menu__item has-link"
                            :to="{ name: 'transactions' }"
                            ><i class="side-menu__icon fe fe-list"></i
                            ><span class="side-menu__label"
                                >Daftar Transaksi Langganan</span
                            ></router-link
                        >
                    </li>
                    <li>
                        <router-link
                            class="side-menu__item has-link"
                            :to="{ name: 'create_store' }"
                            ><i class="side-menu__icon fe fe-plus-circle"></i
                            ><span class="side-menu__label"
                                >Tambah Toko / Cabang</span
                            ></router-link
                        >
                    </li>
                </ul>
                <div class="slide-right" id="slide-right">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="#7b8191"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import logoApp from "@/assets/images/logo.webp";
export default {
    components: {},
    data() {
        return {
            asset: {
                logo: logoApp,
            },
        };
    },
    computed: {
        ...mapGetters("general", ["get_tabs_menus"]),
    },
    methods: {
        ...mapActions(["general/set_open_menu"]),

        openMenu(data) {
            this.$router.push({ name: data.links.name });
            this["general/set_open_menu"](data);
        },

        onloadSidebar() {
            let currentWidth;
            currentWidth = [window.innerWidth];
            $(document).on(
                "click",
                '[data-bs-toggle="sidebar"]',
                function (event) {
                    event.preventDefault();
                    // $('.app').toggleClass('sidenav-toggled');
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").removeClass("sidenav-toggled");
                        if (
                            (document.body.classList.contains("double-menu") ||
                                document.body.classList.contains(
                                    "double-menu-tabs"
                                )) &&
                            !document.body.classList.contains("horizontal")
                        ) {
                            if (
                                document.querySelector(".slide-menu") &&
                                window.innerWidth >= 992
                            ) {
                                let slidemenu =
                                    document.querySelectorAll(".slide-menu");
                                slidemenu.forEach((e) => {
                                    if (
                                        e.classList.contains(
                                            "double-menu-active"
                                        )
                                    ) {
                                        e.classList.remove(
                                            "double-menu-active"
                                        );
                                    }
                                });
                                let sidemenuActive = document.querySelector(
                                    ".side-menu__item.active"
                                );
                                if (sidemenuActive?.nextElementSibling) {
                                    let submenu =
                                        sidemenuActive.nextElementSibling;
                                    submenu.classList.add("double-menu-active");
                                    document.body.classList.remove(
                                        "sidenav-toggled"
                                    );
                                } else {
                                    document.body.classList.add(
                                        "sidenav-toggled"
                                    );
                                }
                            }
                        }
                    } else {
                        $(".app").addClass("sidenav-toggled");
                        if (innerWidth >= 992) {
                            if (
                                (document.body.classList.contains(
                                    "double-menu"
                                ) ||
                                    document.body.classList.contains(
                                        "double-menu-tabs"
                                    )) &&
                                !document.body.classList.contains("horizontal")
                            ) {
                                if (document.querySelector(".slide-menu")) {
                                    let slidemenu =
                                        document.querySelectorAll(
                                            ".slide-menu"
                                        );
                                    slidemenu.forEach((e) => {
                                        if (
                                            e.classList.contains(
                                                "double-menu-active"
                                            )
                                        ) {
                                            e.classList.remove(
                                                "double-menu-active"
                                            );
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            );

            this.responsive();
            this.toggleSidebar();
            $(window).resize(this.toggleSidebar());

            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".app-header").addClass("fixed-header");
                    $(".app-header").addClass("visible-title");
                } else {
                    $(".app-header").removeClass("fixed-header");
                    $(".app-header").removeClass("visible-title");
                }
            });

            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".horizontal-main").addClass("fixed-header");
                    $(".horizontal-main").addClass("visible-title");
                } else {
                    $(".horizontal-main").removeClass("fixed-header");
                    $(".horizontal-main").removeClass("visible-title");
                }
            });

            //sticky-header
            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".app-header").addClass("fixed-header");
                    $(".app-header").addClass("visible-title");
                } else {
                    $(".app-header").removeClass("fixed-header");
                    $(".app-header").removeClass("visible-title");
                }
            });

            this.HorizontalHovermenu();
            this.hovermenu();
            this.ActiveSubmenu();
        },

        toggleSidebar() {
            var w = $(window);
            if (w.outerWidth() <= 1024) {
                $("body").addClass("sidebar-gone");
                $(document)
                    .off("click", "body")
                    .on("click", "body", function (e) {
                        if (
                            $(e.target).hasClass("sidebar-show") ||
                            $(e.target).hasClass("search-show")
                        ) {
                            $("body").removeClass("sidebar-show");
                            $("body").addClass("sidebar-gone");
                            $("body").removeClass("search-show");
                        }
                    });
            } else {
                $("body").removeClass("sidebar-gone");
            }
        },

        responsive() {
            let currentWidth;
            currentWidth = [window.innerWidth];
            const mediaQuery = window.innerWidth;
            currentWidth.push(mediaQuery);
            if (currentWidth.length > 2) {
                currentWidth.shift();
            }
            if (currentWidth.length > 1) {
                if (
                    currentWidth[currentWidth.length - 1] < 992 &&
                    currentWidth[currentWidth.length - 2] >= 992
                ) {
                    // less than 992
                }

                if (
                    currentWidth[currentWidth.length - 1] >= 992 &&
                    currentWidth[currentWidth.length - 2] < 992
                ) {
                    // greater than 992

                    if (
                        document.body.classList.contains("double-menu") ||
                        document.body.classList.contains("double-menu-tabs")
                    ) {
                        document.body.classList.remove("sidenav-toggled");
                    }
                }
            }
        },

        HorizontalHovermenu() {
            let value = document
                .querySelector("body")
                .classList.contains("horizontal-hover");
            if (value && window.innerWidth >= 992) {
                $("[data-bs-toggle='slide']").off("click");
                $("[data-bs-toggle='sub-slide']").off("click");
                $("[data-bs-toggle='sub-slide2']").off("click");
                slideClick();
            } else {
                this.menuClick();
            }
        },

        hovermenu() {
            $(".app-sidebar").hover(
                function () {
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").addClass("sidenav-toggled-open");
                    }
                },
                function () {
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").removeClass("sidenav-toggled-open");
                    }
                }
            );
        },

        ActiveSubmenu() {
            var position = window.location.pathname.split("/");
            position = position[position.length - 1];
            $(".app-sidebar li a").each(function () {
                var $this = $(this);
                var pageUrl = window.location.href.split(/[?#]/)[0];
                let prevValue = [window.innerWidth];
                if (prevValue.length > 1) {
                    prevValue = prevWidth[prevWidth.length - 2];
                }

                if (this.href == pageUrl) {
                    setTimeout(() => {
                        if ($this.closest(".sub-slide-menu2")) {
                            $this.closest(".sub-slide-menu2").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".sub-slide-menu2").slideDown();
                            }
                            $this
                                .closest(".sub-slide-menu2")
                                .prev()
                                .addClass("active");
                            $this
                                .closest(".sub-slide-menu2")
                                .parent()
                                .addClass("is-expanded");
                        }
                        if ($this.closest(".sub-slide-menu")) {
                            $this.closest(".sub-slide-menu").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".sub-slide-menu").slideDown();
                            }
                            $this
                                .closest(".sub-slide-menu")
                                .parent()
                                .addClass("is-expanded");
                            $this
                                .closest(".sub-slide-menu")
                                .prev()
                                .addClass("active");
                        }
                        if ($this.closest(".slide-menu")) {
                            $this.closest(".slide-menu").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".slide-menu").slideDown();
                            }
                            $this
                                .closest(".slide-menu")
                                .parent()
                                .addClass("is-expanded");
                            $this
                                .closest(".slide-menu")
                                .prev()
                                .addClass("active");
                        }
                        $this.addClass("active");
                        $this.parent().addClass("active");

                        if (
                            document.body.classList.contains(
                                "double-menu-tabs"
                            ) ||
                            document.body.classList.contains("double-menu")
                        ) {
                            if ($this.closest(".slide-menu").length) {
                                $this
                                    .closest(".slide-menu")
                                    .addClass("double-menu-active");
                            } else {
                                let slideMenu =
                                        document.querySelectorAll(
                                            ".slide-menu"
                                        ),
                                    slideNavStatus = false;
                                slideMenu.forEach((e) => {
                                    if (
                                        e.classList.contains(
                                            "double-menu-active"
                                        )
                                    ) {
                                        slideNavStatus = true;
                                    }
                                });
                                if (!slideNavStatus) {
                                    document.body.classList.add(
                                        "sidenav-toggled"
                                    );
                                }
                            }
                        }
                    }, 200);
                }
            });
        },

        menuClick() {
            $("[data-bs-toggle='slide']").off("click");
            $("[data-bs-toggle='sub-slide']").off("click");
            $("[data-bs-toggle='sub-slide2']").off("click");
            $("[data-bs-toggle='slide']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".slide-menu";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }

                if (window.innerWidth >= 992) {
                    if (
                        !checkElement.hasClass("double-menu-active") &&
                        !document.body.classList.contains("horizontal") &&
                        (document.body.classList.contains("double-menu") ||
                            document.body.classList.contains(
                                "double-menu-tabs"
                            ))
                    ) {
                        if (document.querySelector(".slide-menu")) {
                            let slidemenu =
                                document.querySelectorAll(".slide-menu");
                            slidemenu.forEach((e) => {
                                if (
                                    e.classList.contains("double-menu-active")
                                ) {
                                    e.classList.remove("double-menu-active");
                                }
                            });
                        }

                        checkElement.addClass("double-menu-active");
                        document.body.classList.remove("sidenav-toggled");
                    }
                }
            });
            // Activate sidebar slide toggle
            $("[data-bs-toggle='sub-slide']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".sub-slide-menu";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="sub-slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }
            });
            // Activate sidebar slide toggle
            $("[data-bs-toggle='sub-slide2']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".sub-slide-menu2";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="sub-slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }
            });
        },

        checkOptions() {
            "use strict";
            // rtl
            if (document.querySelector("body").classList.contains("rtl")) {
                $("#myonoffswitch24").prop("checked", true);
            }
            // horizontal
            if (
                document.querySelector("body").classList.contains("horizontal")
            ) {
                $("#myonoffswitch35").prop("checked", true);
            }
            // horizontal-hover
            if (
                document
                    .querySelector("body")
                    .classList.contains("horizontal-hover")
            ) {
                $("#myonoffswitch111").prop("checked", true);
            }

            // light header
            if (
                document
                    .querySelector("body")
                    .classList.contains("header-light")
            ) {
                $("#myonoffswitch6").prop("checked", true);
            }
            // color header
            if (
                document
                    .querySelector("body")
                    .classList.contains("color-header")
            ) {
                $("#myonoffswitch7").prop("checked", true);
            }
            // dark header
            if (
                document.querySelector("body").classList.contains("dark-header")
            ) {
                $("#myonoffswitch8").prop("checked", true);
            }

            // light menu
            if (
                document.querySelector("body").classList.contains("light-menu")
            ) {
                $("#myonoffswitch3").prop("checked", true);
            }
            // color menu
            if (
                document.querySelector("body").classList.contains("color-menu")
            ) {
                $("#myonoffswitch4").prop("checked", true);
            }
            // dark menu
            if (
                document.querySelector("body").classList.contains("dark-menu")
            ) {
                $("#myonoffswitch5").prop("checked", true);
            }
            // dark-mode
            if (
                document.querySelector("body").classList.contains("dark-mode")
            ) {
                $("#myonoffswitch2").prop("checked", true);
            }
            // icontext-menu
            if (
                document
                    .querySelector("body")
                    .classList.contains("icontext-menu")
            ) {
                $("#myonoffswitch14").prop("checked", true);
            }
            // icon-overlay
            if (
                document
                    .querySelector("body")
                    .classList.contains("icon-overlay")
            ) {
                $("#myonoffswitch15").prop("checked", true);
            }
            // closed-leftmenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("closed-leftmenu")
            ) {
                $("#myonoffswitch16").prop("checked", true);
            }
            // closed-leftmenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("closed-leftmenu")
            ) {
                $("#myonoffswitch16").prop("checked", true);
            }
            // hover-submenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("hover-submenu")
            ) {
                $("#myonoffswitch17").prop("checked", true);
            }
            // hover-submenu1
            if (
                document
                    .querySelector("body")
                    .classList.contains("hover-submenu1")
            ) {
                $("#myonoffswitch18").prop("checked", true);
            }
            // hover-submenu1
            if (
                document.querySelector("body").classList.contains("double-menu")
            ) {
                $("#myonoffswitch19").prop("checked", true);
            }
            // hover-submenu1
            if (
                document
                    .querySelector("body")
                    .classList.contains("double-menu-tabs")
            ) {
                $("#myonoffswitch20").prop("checked", true);
            }
            // layout-boxed
            if (
                document
                    .querySelector("body")
                    .classList.contains("layout-boxed")
            ) {
                $("#myonoffswitch10").prop("checked", true);
            }
            // scrollable-layout
            if (
                document
                    .querySelector("body")
                    .classList.contains("scrollable-layout")
            ) {
                $("#myonoffswitch12").prop("checked", true);
            }
            // center-logo
            if (
                document.querySelector("body").classList.contains("center-logo")
            ) {
                $("#center-logo").prop("checked", true);
            }
            // default-logo
            if (
                document
                    .querySelector("body")
                    .classList.contains("default-logo")
            ) {
                $("#default-logo").prop("checked", true);
            }
        },

        checkHoriMenu() {
            setTimeout(() => {
                let menuWidth = document.querySelector(".horizontal-main");
                let menuItems = document.querySelector(".side-menu");
                let mainSidemenuWidth =
                    document.querySelector(".main-sidemenu");
                let menuContainerWidth =
                    menuWidth?.offsetWidth - mainSidemenuWidth?.offsetWidth;
                let marginLeftValue = Math.ceil(
                    window.getComputedStyle(menuItems).marginLeft.split("px")[0]
                );
                let marginRightValue = Math.ceil(
                    window
                        .getComputedStyle(menuItems)
                        .marginRight.split("px")[0]
                );
                let check =
                    menuItems.scrollWidth +
                    (0 - menuWidth?.offsetWidth) +
                    menuContainerWidth;

                if ($("body").hasClass("ltr")) {
                    menuItems.style.marginRight = 0;
                } else {
                    menuItems.style.marginLeft = 0;
                }

                if (
                    menuItems.scrollWidth - 2 <
                    menuWidth?.offsetWidth - menuContainerWidth
                ) {
                    $("#slide-right").addClass("d-none");
                    $("#slide-left").addClass("d-none");
                } else if (marginLeftValue != 0) {
                    $("#slide-left").removeClass("d-none");
                } else if (marginLeftValue != -check) {
                    $("#slide-right").removeClass("d-none");
                } else if (marginRightValue != 0) {
                    $("#slide-left").removeClass("d-none");
                } else if (marginRightValue != -check) {
                    $("#slide-right").removeClass("d-none");
                }
            }, 100);
        },

        setHorizontalMenu() {
            let bodyhorizontal = $("body").hasClass("horizontal");
            if (bodyhorizontal) {
                if (!document.querySelector(".login-img")) {
                    this.ActiveSubmenu();
                    this.checkHoriMenu();
                    this.responsive();
                }
                setTimeout(() => {
                    if (window.innerWidth >= 992) {
                        let li = document.querySelectorAll(".side-menu li");
                        li.forEach((e, i) => {
                            e.classList.remove("is-expanded");
                        });
                        var animationSpeed = 300;
                        // first level
                        var parent = $("[data-bs-toggle='sub-slide']").parents(
                            "ul"
                        );
                        var ul = parent
                            .find('ul[class^="slide-menu"]:visible')
                            .slideUp(animationSpeed);
                        ul.removeClass("open");
                        var parent1 = $(
                            "[data-bs-toggle='sub-slide2']"
                        ).parents("ul");
                        var ul1 = parent1
                            .find('ul[class^="sub-slide-menu"]:visible')
                            .slideUp(animationSpeed);
                        ul1.removeClass("open");
                    }
                }, 200);
                $("body").addClass("horizontal");
                $(".main-content").addClass("hor-content");
                $(".main-content").removeClass("app-content");
                $(".main-container").addClass("container");
                $(".main-container").removeClass("container-fluid");
                $(".app-header").addClass("hor-header");
                $(".hor-header").removeClass("app-header");
                $(".app-sidebar").addClass("horizontal-main");
                $(".main-sidemenu").addClass("container");
                $("body").removeClass("sidebar-mini");
                $("body").removeClass("sidenav-toggled");
                $("body").removeClass("horizontal-hover");
                $("body").removeClass("default-menu");
                $("body").removeClass("icontext-menu");
                $("body").removeClass("icon-overlay");
                $("body").removeClass("closed-leftmenu");
                $("body").removeClass("double-menu");
                $("body").removeClass("double-menu-tabs");
                $("body").removeClass("hover-submenu");
                $("body").removeClass("hover-submenu1");
                document
                    .querySelector(".horizontal .side-menu")
                    ?.classList.add("flex-nowrap");
            }
        },
    },

    mounted() {
        this.onloadSidebar();
        this.setHorizontalMenu();

        document.addEventListener("touchstart", function () {}, false);
        jQuery(function () {
            jQuery("body").wrapInner('<div class="horizontalMenucontainer" />');
        });

        $(document).on("click", ".horizontal-content", function () {
            $(".app-sidebar li a").each(function () {
                $(this)
                    .next()
                    .slideUp(300, function () {
                        $(this).next().removeClass("open");
                    });
                $(this).parent("li").removeClass("is-expanded");
            });
        });

        $(document).on("click", ".horizontal-content", function () {
            $(".app-sidebar li a").each(function () {
                $(this)
                    .next()
                    .slideUp(300, function () {
                        $(this).next().removeClass("open");
                    });
                $(this).parent("li").removeClass("is-expanded");
            });

            setTimeout(() => {
                if ($(".slide-item").hasClass("active")) {
                    $(".app-sidebar").animate(
                        {
                            scrollTop:
                                $("a.slide-item.active").offset().top - 600,
                        },
                        600
                    );
                }
                if ($(".sub-side-menu__item").hasClass("active")) {
                    $(".app-sidebar").animate(
                        {
                            scrollTop:
                                $("a.sub-side-menu__item.active").offset().top -
                                600,
                        },
                        600
                    );
                }
            }, 200);
        });

        document.querySelector(".main-content").addEventListener(
            "click",
            () => {
                if (
                    document
                        .querySelector("body")
                        .classList.contains("horizontal")
                ) {
                    let li = document.querySelectorAll(".side-menu li");
                    li.forEach((e, i) => {
                        e.classList.remove("is-expanded");
                    });
                    var animationSpeed = 300;
                    // first level
                    var parent = $("[data-bs-toggle='sub-slide']").parents(
                        "ul"
                    );
                    var ul = parent.find("ul:visible").slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent1 = $("[data-bs-toggle='sub-slide2']").parents(
                        "ul"
                    );
                    var ul1 = parent1
                        .find("ul:visible")
                        .slideUp(animationSpeed);
                    ul1.removeClass("open");
                }
            },
            true
        );
    },

    watch: {
        $route(to, from) {},
    },
};
</script>
