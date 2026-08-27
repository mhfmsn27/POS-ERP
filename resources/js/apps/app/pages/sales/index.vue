<template>
    
    <div class="row mt-6">
        <!-- <router-view v-slot="{ Component, route }">
            <keep-alive v-if="route.meta.keepAlive">
                <component :is="Component" :key="route.fullPath" />
            </keep-alive>
            <component v-else :is="Component" :key="route.fullPath" />
        </router-view> -->
         <router-view v-slot="{ Component, route }">
            <component
                v-if="route.query?.close == 'true' || route.query?.tab == 'true'"
                :is="Component"
                :key="route.fullPath"
            />
            <keep-alive v-else>
                <component :is="Component" :key="route.fullPath" />
            </keep-alive>
        </router-view>
    </div>
</template>

<script></script>
