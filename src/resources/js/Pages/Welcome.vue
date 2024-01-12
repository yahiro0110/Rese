<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const windowWidth = ref(window.innerWidth);

const onResize = () => {
    windowWidth.value = window.innerWidth;
};

onMounted(() => {
    window.addEventListener('resize', onResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', onResize);
});

const isMdOrLarger = computed(() => {
    return windowWidth.value >= 768; // md breakpoint
});
</script>

<template>
    <Head title="Welcome" />

    <div class="animate-fade-in relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white" :class="{ 'bg-image': isMdOrLarger, 'bg-image-sm': !isMdOrLarger }">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            <Link v-if="$page.props.auth.user" :href="route('home')" class="font-semibold text-gray-900 hover:text-white dark:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-gray-500">
            Home</Link>

            <template v-else>
                <Link :href="route('login')" class="font-semibold text-gray-900 hover:text-white dark:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-gray-500">
                Login</Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 font-semibold text-gray-900 hover:text-white dark:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-gray-500">
                Register</Link>
            </template>
        </div>
    </div>
</template>

<style>
.bg-image {
    background-image: url('/images/welcome.png');
    background-size: cover;
}

.bg-image-sm {
    background-image: url('/images/welcome-sm.png');
    background-size: cover;
}
</style>
