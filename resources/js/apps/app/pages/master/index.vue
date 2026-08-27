<template>
    <div class="row mt-6">
        <!-- <div class="col-12 mb-3">
            <nav aria-label="breadcrumb mt-4">
                <ol class="breadcrumb iq-bg-primary">
                    <li class="breadcrumb-item">
                        <a href="#"
                            ><i class="ri-home-4-line mr-1 float-left"></i
                            >Perusahaan</a
                        >
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $route.meta.title }}
                    </li>
                </ol>
            </nav>
        </div> -->
        <router-view ></router-view>

        <!-- <router-view></router-view> -->
    </div>
</template>

<script>
export default {
    computed: {
        currentRouteName() {
            return this.$route.name;
        },
    },
};
</script>
<style scoped>
.tabheader {
    background-color: #fff;
    margin-top: 0;
    margin: 0 !important;
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
