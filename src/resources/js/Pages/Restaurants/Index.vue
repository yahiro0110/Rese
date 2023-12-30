<script setup>
/**
 * @requires AuthenticatedLayout: 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires Link - @inertiajs/inertia-vue3からインポート
 *                  Inertia.jsを用いたアプリケーション内のページ間ナビゲーションを提供するVueコンポーネント
 * @requires FlashMessage: フラッシュメッセージ表示コンポーネント
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import FlashMessage from '@/Components/FlashMessage.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Array} - 店舗の情報を含むオブジェクトの配列
 */
defineProps({
    restaurants: Array,
});

/**
 * 画像が見つからない場合に表示されるデフォルトの画像パス。
 * この変数は、指定された画像リソースが存在しない、またはアクセスできない場合に使用される。
 * '/storage/images/notfound.jpeg' への相対パスを指定している。
 *
 * @type {string} - 画像が見つからない場合のデフォルト画像のファイルパス
 */
const notfoundImage = '/storage/images/notfound.jpeg';

/**
 * 与えられたURLが有効な画像URLであるかどうかを判定する関数。
 * URLが非空であることを確認することで、有効なURLかどうかをチェックします。
 *
 * @param {string} url - 検証する画像のURL
 * @returns {boolean} URLが有効である場合はtrue、そうでなければfalseを返す
 */
const isValidImageUrl = (url) => {
    return url && url.length > 0;
};
</script>

<template>
    <Head title="店舗一覧" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">店舗一覧</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-24 mx-auto">
                                <div class="flex flex-wrap w-full mb-20">
                                    <div class="lg:w-1/2 w-full mb-6 lg:mb-0">
                                        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">{{ $page.props.auth.user.name }}さんの店舗情報</h1>
                                        <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                                    </div>
                                    <p class="lg:w-1/2 w-full leading-relaxed text-gray-500">
                                        この度は、私たちのサイトにご登録いただき、誠にありがとうございます。こちらのページでは、あなたが登録されたお店の情報を表示しております。お店の情報に関しましては、必要に応じて随時編集が可能です。お客様にとってより魅力的で正確な情報を提供するため、ご活用いただけますと幸いです。
                                        何かご不明点やご質問がございましたら、お気軽にご連絡ください。
                                    </p>
                                </div>
                                <div class="flex flex-wrap -m-4">
                                    <div class="xl:w-1/4 md:w-1/2 p-4" v-for="restaurant in restaurants" :key="restaurant.id">
                                        <div class="bg-gray-100 p-6 rounded-lg">
                                            <!-- 条件付きレンダリングで画像を表示 -->
                                            <img v-if="isValidImageUrl(restaurant.restaurant_image)" class="lg:h-48 md:h-36 w-full object-cover object-center" :src="'/storage/images/' + restaurant.restaurant_image" alt="restaurant image">
                                            <!-- 画像が無効の場合は代替画像を表示 -->
                                            <img v-else class="lg:h-48 md:h-36 w-full object-cover object-center" :src="notfoundImage" alt="not found">
                                            <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">店舗名</h3>
                                            <h2 class="text-lg text-gray-900 font-medium title-font mb-4">{{ restaurant.name }}</h2>
                                            <Link as="button" :href="route('restaurants.show', { restaurant: restaurant.id })" class="flex ml-auto text-white bg-indigo-500 border-0 p-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">
                                            詳細
                                            </Link>
                                        </div>
                                    </div>
                                    <div class="xl:w-1/4 md:w-1/2 p-4">
                                        <div class="bg-gray-100 p-6 rounded-lg text-center">
                                            <Link as="button" :href="route('restaurants.create')" class="bg-gray-100 p-6 rounded-lg text-5xl">
                                            +
                                            </Link>
                                            <p>店舗を登録する</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
