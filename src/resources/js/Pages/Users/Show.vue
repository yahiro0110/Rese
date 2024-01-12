<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires Link - Inertia.jsのリンクコンポーネント
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                   クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires FlashMessage - フラッシュメッセージ表示コンポーネント
 * @requires getRoleDisplayName - ユーザの権限を表す文字列を日本語に変換する関数
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import FlashMessage from '@/Components/FlashMessage.vue';
import { getRoleDisplayName } from '@/Commons/disprayRoleName.js';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 */
const props = defineProps({
    user: Object,
});

/**
 * 指定されたユーザーIDに基づいてユーザー情報を削除する関数。
 * Inertia.jsのdeleteメソッドを使用して、サーバーにHTTP DELETEリクエストを送信する。
 *
 * @param {number} id - 削除するユーザーの一意の識別子（ID）
 */
const deleteUser = (id) => {
    Inertia.delete(route('users.destroy', { user: id }), {
        onBefore: () => confirm('削除しますか？'),
    });
};
</script>

<template>
    <Head title="ユーザ詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ user.name }}さんの情報</h2>
        </template>

        <div :class="{ 'animate-shake-horizontal': $page.props.flash.status === 'warning' }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <section class="text-gray-600 body-font relative">
                            <div class="container px-5 py-8 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                                <div id="email" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    {{ user.email }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="role" class="leading-7 text-sm text-gray-600">役割</label>
                                                <ul class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <li v-for=" role in user.roles" :key="role.id">{{ getRoleDisplayName(role.name) }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="p-2 w-full">
                                            <div class="mt-4 flex justify-center">
                                                <Link as="button" :href="route('users.edit', { user: user.id })" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg mx-2">
                                                編集する
                                                </Link>
                                                <button @click="deleteUser(user.id)" class="text-white bg-red-500 border-0 py-2 px-8 focus:outline-none hover:bg-red-600 rounded text-lg mx-2">
                                                    削除する
                                                </button>
                                            </div>
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
