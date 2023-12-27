<script setup>
// propsの定義
defineProps({
    restaurant: Object
});
</script>

<template>
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-24 mx-auto">
            <div class="lg:w-4/5 mx-auto flex flex-wrap">
                <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ restaurant.genre.name }}</h2>
                    <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ restaurant.name }}
                    </h1>
                    <div class="flex mb-4">お店の概要</div>
                    <p class="leading-relaxed mb-4">{{ restaurant.description }}</p>
                    <div class="flex border-t border-gray-200 py-2">
                        <span class="text-gray-500">住所</span>
                        <span class="ml-auto text-gray-900">{{ restaurant.address }}</span>
                    </div>
                    <div class="flex border-t border-gray-200 py-2">
                        <span class="text-gray-500">電話番号</span>
                        <span class="ml-auto text-gray-900">{{ restaurant.tel }}</span>
                    </div>
                    <div class="flex border-t border-b mb-6 border-gray-200 py-2">
                        <span class="text-gray-500">メールアドレス</span>
                        <span class="ml-auto text-gray-900">{{ restaurant.email }}</span>
                    </div>
                    <div class="flex">
                        <button
                            class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">予約する</button>
                    </div>
                </div>
                <img alt="ecommerce" class="lg:w-1/2 w-full lg:h-auto h-64 object-cover object-center rounded"
                    src="https://dummyimage.com/400x400">
            </div>
        </div>
    </section>
</template>
