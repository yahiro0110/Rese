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
import { Head, Link } from '@inertiajs/inertia-vue3';

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

const sushiImageUrl = '/storage/images/sushi.jpg';
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
                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-24 mx-auto">
                                <div class="flex flex-wrap w-full mb-20">
                                    <div class="lg:w-1/2 w-full mb-6 lg:mb-0">
                                        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900">
                                            {{ $page.props.auth.user.name }}さんの店舗情報</h1>
                                        <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                                    </div>
                                    <p class="lg:w-1/2 w-full leading-relaxed text-gray-500">
                                        この度は、私たちのサイトにご登録いただき、誠にありがとうございます。こちらのページでは、あなたが登録されたお店の情報を表示しております。お店の情報に関しましては、必要に応じて随時編集が可能です。お客様にとってより魅力的で正確な情報を提供するため、ご活用いただけますと幸いです。
                                        何かご不明点やご質問がございましたら、お気軽にご連絡ください。</p>
                                </div>
                                <div class="flex flex-wrap -m-4">
                                    <div class="xl:w-1/4 md:w-1/2 p-4" v-for="restaurant in restaurants"
                                        :key="restaurant.id">
                                        <div class="bg-gray-100 p-6 rounded-lg">
                                            <img class="h-40 rounded w-full object-cover object-center mb-6"
                                                src="https://dummyimage.com/720x400" alt="content">
                                            <h3 class="tracking-widest text-indigo-500 text-xs font-medium title-font">
                                                店舗名</h3>
                                            <h2 class="text-lg text-gray-900 font-medium title-font mb-4">{{ restaurant.name
                                            }}</h2>
                                            <Link as="button"
                                                class="flex ml-auto text-white bg-indigo-500 border-0 p-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">
                                            詳細
                                            </Link>
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
