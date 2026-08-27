<template>
    <div class="page-header">
        <h1 class="page-title">E-Commerce</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Blog dan Page</router-link
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
                        <h4>Menu Konten</h4>
                    </div>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'category_blog_ecommerce',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'category_blog_ecommerce'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-grid"></i></span
                        >Kategori Blog
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'blog_ecommerce',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="$route.name == 'blog_ecommerce' ? 'active' : ''"
                    >
                        <span class="icon me-3"><i class="fe fe-file"></i></span
                        >Blog Article
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_about',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_about' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-file-text"></i></span
                        >About Us
                    </a>

                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_sosmed',
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_sosmed' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fa fa-globe"></i></span
                        >Sosial Media
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
