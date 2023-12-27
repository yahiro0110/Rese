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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} users - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 * @property {Array} roles - 利用可能なユーザロールの配列。各ロールは一意の名前と説明を持つ。
 * @property {Object} flash - フラッシュメッセージに関するデータ。メッセージの内容やステータス（成功、警告など）を含む。
 */
defineProps({
    restaurant: Object,
});

const sushiImageUrl = '/storage/images/sushi.jpg';
</script>

<template>
    <Head title="店舗詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">店舗詳細</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <section class="text-gray-600 body-font overflow-hidden">
                            <div class="container px-5 py-24 mx-auto">
                                <div class="lg:w-4/5 mx-auto flex flex-wrap">
                                    <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                                        <h2 class="text-sm title-font text-gray-500 tracking-widest">寿司</h2>
                                        <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ restaurant.name }}
                                        </h1>
                                        <div class="flex mb-4">お店の概要</div>
                                        <p class="leading-relaxed mb-4">
                                            {{ restaurant.description }}
                                        </p>
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
                                    <img alt="ecommerce"
                                        class="lg:w-1/2 w-full lg:h-auto h-64 object-cover object-center rounded"
                                        src="https://dummyimage.com/400x400">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
