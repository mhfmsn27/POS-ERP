<template>
    <div class="page-header">
        <h1 class="page-title">E-Commerce</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Media Konten</router-link
                >
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $route.meta.title }}
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="card p-3">
                <div class="list-group list-group-transparent mb-0 mail-inbox">
                    <div class="mb-4 text-center border-bottom">
                        <h4>Menu Media Konten</h4>
                    </div>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'slider_ecommerce',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'slider_ecommerce' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-image"></i></span
                        >Slider web
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'banner_ecommerce',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'banner_ecommerce' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-image"></i></span
                        >Banner Web
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'featured_ecommerce',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'featured_ecommerce' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-layers"></i></span
                        >Featured Icon
                    </a>

                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'ecommerce_category',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'ecommerce_category' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-grid"></i></span
                        >E-Commerce Kategori
                    </a>
                </div>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
export default {};
</script>
<style scoped>
.tabheader {
    background-color: #fff;
    margin-top: 0;
    margin: 0 !important;
    padding: 0 !important;
}

.item-nav {
    margin: 0 !important;
    padding: 0 !important;
}

.item-tab {
    width: 100% !important;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    color: black;
}

.item_tab {
    text-align: center !important;
}
</style>
