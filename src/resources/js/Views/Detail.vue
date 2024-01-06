<script setup>
/**
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires defineEmits - Vueコンポーネントのイベントを定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires ScheduleBooker - 店舗予約フォームを表示するためのVueコンポーネント
 */
import { Head } from '@inertiajs/inertia-vue3';
import { defineEmits, ref } from 'vue';
import ScheduleBooker from '@/Views/ScheduleBooker.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - エラーメッセージを含むオブジェクト
 * @property {Object} restaurant - 選択された店舗の情報を含むオブジェクト
 */
const props = defineProps({
    errors: Object,
    restaurant: Object
});

/**
 * イベントを発火させるためのemit関数を定義。
 * @property {Function} back - 'back'イベントを発火させる関数
 */
const emit = defineEmits(['back', 'toggleLike']);

/**
 * モーダルウィンドウの表示状態を管理するリアクティブな参照。
 * モーダルウィンドウが表示されている場合はtrue、そうでない場合はfalse。
 *
 * @type {ref<boolean>}
 */
const showContent = ref(false);

/**
 * モーダルウィンドウを開く関数。
 */
const openModal = () => {
    showContent.value = true;
};

/**
 * モーダルウィンドウを閉じる関数。
 */
const closeModal = () => {
    showContent.value = false;
};

/**
 * 'back'イベントを発火させる関数。
 */
function emitBackEvent() {
    emit('back');
}

/**
 * 'back'イベントを発火させる関数。
 */
function emitToggleLike(restaurant) {
    emit('toggleLike', restaurant);
}
</script>

<template>
    <Head title="詳細" />
    <section class="text-gray-600 body-font overflow-hidden">
        <ScheduleBooker v-if="showContent" v-bind="{ restaurantId: restaurant.id, schedule: null, errors }" @closeModal="closeModal" />
        <div class="container px-5 py-24 mx-auto">
            <div class="text-center md:text-left md:px-10 md:pb-5">
                <a href="#" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" @click="emitBackEvent">
                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                    戻る
                </a>
            </div>
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
                        <button @click="openModal" class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">予約する</button>
                        <button @click="emitToggleLike(restaurant)" class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4" :class="{ 'bg-red-100': restaurant.liked }">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24" :class="{ 'text-red-500': restaurant.liked }">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="lg:w-1/2 w-full lg:h-auto h-64 bg-gray-300 rounded-lg  relative">
                    <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map" marginheight="0" marginwidth="0" scrolling="no" src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=%C4%B0zmir+(My%20Business%20Name)&ie=UTF8&t=&z=14&iwloc=B&output=embed" style="filter: grayscale(1) contrast(1.2) opacity(0.4);"></iframe>
                </div>
            </div>
        </div>
        <div class="container px-5 py-24 mx-auto flex flex-wrap">
            <div class="flex flex-wrap md:-m-2 -m-1">
                <div class="flex flex-wrap w-1/2">
                    <div class="md:p-2 p-1 w-1/2">
                        <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://dummyimage.com/500x300">
                    </div>
                    <div class="md:p-2 p-1 w-1/2">
                        <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://dummyimage.com/501x301">
                    </div>
                    <div class="md:p-2 p-1 w-full">
                        <img alt="gallery" class="w-full h-full object-cover object-center block" src="https://dummyimage.com/600x360">
                    </div>
                </div>
                <div class="flex flex-wrap w-1/2">
                    <div class="md:p-2 p-1 w-full">
                        <img alt="gallery" class="w-full h-full object-cover object-center block" src="https://dummyimage.com/601x361">
                    </div>
                    <div class="md:p-2 p-1 w-1/2">
                        <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://dummyimage.com/502x302">
                    </div>
                    <div class="md:p-2 p-1 w-1/2">
                        <img alt="gallery" class="w-full object-cover h-full object-center block" src="https://dummyimage.com/503x303">
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
