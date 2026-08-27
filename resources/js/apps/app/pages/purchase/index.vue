<template>
    <!-- <div class="page-header">
        <h1 class="page-title">Faktur Pembelian</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Faktur Pembelian</router-link
                >
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $route.meta.title }}
            </li>
        </ol>
    </div> -->
    <div class="row mt-6">
        <!-- <router-view v-slot="{ Component, route }">
            <keep-alive v-if="route.meta.keepAlive">
                <component :is="Component" :key="route.fullPath" />
            </keep-alive>
            <component v-else :is="Component" :key="route.fullPath" />
        </router-view> -->
         <router-view ></router-view>
    </div>
</template>

<script></script>
