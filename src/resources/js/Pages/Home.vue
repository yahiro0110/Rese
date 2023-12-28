<script setup>
/**
 * @file index.vue
 * @description ユーザ一覧ページのVueコンポーネント。認証済みユーザのみアクセス可能で、ユーザ情報の一覧表示と検索機能を提供します。
 *
 * @component
 * @requires AuthenticatedLayout: 認証済みユーザ用のレイアウトコンポーネント
 * @requires Pagination: ページネーションコンポーネント
 * @requires FlashMessage: フラッシュメッセージ表示コンポーネント
 * @requires @inertiajs/inertia-vue3: Inertia.jsのVue 3バインディング
 * @requires vue: Vue.jsフレームワーク
 * @requires @inertiajs/inertia: Inertia.jsライブラリ
 *
 * @prop {Object} users - 表示するユーザデータ
 * @prop {Array} roles - 利用可能なユーザロール
 * @prop {Object} flash - フラッシュメッセージ情報
 *
 * @data {String} search - 検索クエリ文字列
 * @data {Array} selectedRoles - 選択されたロール
 *
 * @method searchUser 検索クエリと選択されたロールに基づいてユーザを検索
 * @method searchClear 検索フィールドと選択されたロールをクリア
 *
 * @watchEffect セッションストレージとの状態同期を管理
 *
 * @template ユーザ一覧の表示、検索バー、ロール選択チェックボックス、フラッシュメッセージ、ページネーション
 */
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Detail from '@/Views/Detail.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} users - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 * @property {Array} roles - 利用可能なユーザロールの配列。各ロールは一意の名前と説明を持つ。
 * @property {Object} flash - フラッシュメッセージに関するデータ。メッセージの内容やステータス（成功、警告など）を含む。
 */
defineProps({
    restaurants: Array,
});

const notfoundImage = '/storage/images/notfound.jpeg';

const selectedRestaurant = ref(null);

// レストランが選択された時の処理
function selectRestaurant(restaurant) {
    selectedRestaurant.value = restaurant;
}

// 画像URLが有効かどうかをチェックするメソッド
const isValidImageUrl = (url) => {
    return url && url.length > 0;
};
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
                        <section class="text-gray-600 body-font" v-if="!selectedRestaurant">
                            <div class="container px-5 py-24 mx-auto">
                                <div class="flex flex-wrap -m-4">
                                    <div class="p-4 md:w-1/3" v-for="restaurant in restaurants" :key="restaurant.id">
                                        <div
                                            class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                            <!-- 条件付きレンダリングで画像を表示 -->
                                            <img v-if="isValidImageUrl(restaurant.restaurant_image)"
                                                class="lg:h-48 md:h-36 w-full object-cover object-center"
                                                :src="'/storage/images/' + restaurant.restaurant_image"
                                                alt="restaurant image">
                                            <!-- 画像が無効の場合は代替画像を表示 -->
                                            <img v-else class="lg:h-48 md:h-36 w-full object-cover object-center"
                                                :src="notfoundImage" alt="not found">
                                            <div class="p-6">
                                                <h2
                                                    class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                    CATEGORY</h2>
                                                <h2 class="title-font text-base font-medium text-gray-900 mb-3">{{
                                                    restaurant.genre.name }}
                                                </h2>
                                                <h1 class="title-font text-2xl font-mono text-gray-900 mb-3">{{
                                                    restaurant.name }}
                                                </h1>
                                                <p class="leading-relaxed mb-3">{{ restaurant.description }}</p>
                                                <div class="flex items-center flex-wrap"
                                                    @click="selectRestaurant(restaurant)">
                                                    <a href="#"
                                                        class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">
                                                        詳細
                                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M5 12h14"></path>
                                                            <path d="M12 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                    <span
                                                        class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                        <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2"
                                                            fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                            viewBox="0 0 24 24">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z">
                                                            </path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>1.2K
                                                    </span>
                                                    <span
                                                        class="text-gray-400 inline-flex items-center leading-none text-sm">
                                                        <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2"
                                                            fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z">
                                                            </path>
                                                        </svg>6
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <Detail v-if="selectedRestaurant" :restaurant="selectedRestaurant" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
