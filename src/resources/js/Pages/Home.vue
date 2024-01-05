<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires Detail - 店舗詳細情報を表示するためのVueコンポーネント
 * @requires FlashMessage - フラッシュメッセージ表示コンポーネント
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import Detail from '@/Views/Detail.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - エラーメッセージを含むオブジェクト
 * @property {Array} restaurants - 店舗の情報を含むオブジェクトの配列
 */
defineProps({
    errors: Object,
    restaurants: Array,
});

/**
 * 画像が見つからない場合に表示されるデフォルトの画像パス。
 * この変数は、指定された画像リソースが存在しない、またはアクセスできない場合に使用される。
 * '/storage/images/notfound.jpeg' への相対パスを指定している。
 *
 * @type {string} - 画像が見つからない場合のデフォルト画像のファイルパス
 */
const notfoundImage = '/images/notfound.jpeg';

/**
 * 選択された店舗の情報を保持するリアクティブな参照。
 * ユーザーが店舗を選択すると、その情報がこの参照に格納される。
 *
 * @type {ref|null} - 選択された店舗のオブジェクト、または選択されていない場合はnull
 */
const selectedRestaurant = ref(null);

/**
 * 店舗が選択された時の処理。
 * 引数として受け取った店舗オブジェクトを、selectedRestaurantに設定する。
 *
 * @param {Object} restaurant - 選択された店舗のオブジェクト
 */
const selectRestaurant = (restaurant) => {
    selectedRestaurant.value = restaurant;
}

/**
 * 店舗選択をクリアする関数。
 * selectedRestaurantをnullに設定し、選択された店舗情報をリセットする。
 */
const clearSelectedRestaurant = () => {
    selectedRestaurant.value = null;
}

/**
 * 画像URLが有効かどうかをチェックする関数。
 * URLが非空かつ正しい形式であることを確認する。
 *
 * @param {string} url - チェックする画像のURL
 * @returns {boolean} - URLが有効であればtrue、そうでなければfalse
 */
const isValidImageUrl = (url) => {
    return url && url.length > 0;
}
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <template v-if="selectedRestaurant">店舗詳細</template>
                <template v-else>店舗一覧</template>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <section class="text-gray-600 body-font" v-if="!selectedRestaurant">
                            <div class="container px-5 py-24 mx-auto">
                                <div class="flex flex-wrap -m-4">
                                    <div class="p-4 md:w-1/3" v-for="restaurant in restaurants" :key="restaurant.id">
                                        <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                            <!-- 条件付きレンダリングで画像を表示 -->
                                            <img v-if="isValidImageUrl(restaurant.restaurant_image)" class="lg:h-48 md:h-36 w-full object-cover object-center" :src="'/storage/images/' + restaurant.restaurant_image" alt="restaurant image">
                                            <!-- 画像が無効の場合は代替画像を表示 -->
                                            <img v-else class="lg:h-48 md:h-36 w-full object-cover object-center" :src="notfoundImage" alt="not found">
                                            <div class="p-6">
                                                <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                    CATEGORY</h2>
                                                <h2 class="title-font text-base font-medium text-gray-900 mb-3">{{
                                                    restaurant.genre.name }}
                                                </h2>
                                                <h1 class="title-font text-2xl font-mono text-gray-900 mb-3">{{
                                                    restaurant.name }}
                                                </h1>
                                                <p class="leading-relaxed mb-3">{{ restaurant.description }}</p>
                                                <div class="flex items-center flex-wrap">
                                                    <a href="#" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" @click="selectRestaurant(restaurant)">
                                                        詳細
                                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M5 12h14"></path>
                                                            <path d="M12 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                    <span class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                                        <button class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                                                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <Detail v-if="selectedRestaurant" v-bind="{ restaurant: selectedRestaurant, errors }" @back="clearSelectedRestaurant" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
