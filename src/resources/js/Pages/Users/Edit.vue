<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires reactive - Vueコンポーネント内でリアクティブなデータを管理するために使用
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                     クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires InputError - フォーム入力エラーを表示するためのコンポーネント
 * @requires getRoleDisplayName - ユーザの権限を表す文字列を日本語に変換する関数
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import InputError from '@/Components/InputError.vue';
import { getRoleDisplayName } from '@/Commons/disprayRoleName.js';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - バリデーションエラー情報を含むオブジェクト。各フィールドに対するエラーが配列で格納される。
 * @property {Object} users - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 * @property {Array} roles - 利用可能なユーザロールの配列。各ロールは一意の名前と説明を持つ。
 */
const props = defineProps({
    errors: Object,
    user: Object,
    roles: Array,
});

/**
 * ユーザ編集フォームのデータモデル。
 * このオブジェクトはVueのreactive機能を用いてリアクティブな状態を管理する。
 *
 * @type {Object} form - ユーザの編集に必要なデータを保持するフォームオブジェクト。
 * @property {number} id - ユーザの一意の識別子
 * @property {string} name - ユーザの名前
 * @property {string} email - ユーザのメールアドレス
 * @property {Object} roles - ユーザの権限情報を保持するオブジェクト。各権限はIDとその権限がユーザに割り当てられているかの真偽値で構成される。
 */
const form = reactive({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
    roles: props.roles.reduce((obj, role) => {
        // 現在のロール（role）のIDをキーとして、objオブジェクトにプロパティを追加する。
        // このプロパティの値は、ユーザーがそのロールを持っているかどうかの真偽値。
        // `props.user.roles.some(...)` は、ユーザーのロールの中に現在のロールのIDが存在するかをチェックし、
        // 存在する場合はtrue、存在しない場合はfalseを返す。
        obj[role.id] = props.user.roles.some(userRole => userRole.id === role.id);
        return obj;
    }, {}),
});

/**
 * 指定されたユーザーIDに基づいてユーザー情報を更新する関数。
 * Inertia.jsのputメソッドを使用して、サーバーにHTTP PUTリクエストを送信する。
 * この関数は、主にユーザーの編集フォームから呼び出される。
 *
 * @param {number} id - 更新するユーザーの一意の識別子（ID）
 */
const updateUser = (id) => {
    Inertia.put(route('users.update', { user: id }), form);
};
</script>

<template>
    <Head title="ユーザ編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ user.name }}さんの情報を編集します</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <section class="text-gray-600 body-font relative">
                            <form @submit.prevent="updateUser(form.id)">
                                <div class="container px-5 py-8 mx-auto">
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                        <div class="flex flex-wrap -m-2">
                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="name" class="leading-7 text-sm text-gray-600">名前</label>
                                                    <input type="text" id="name" name="name" v-model="form.name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <InputError class="p-1" :message="errors.name" />
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                                    <input type="email" id="email" name="email" v-model="form.email" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <InputError class="p-1" :message="errors.email" />
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <label for="email" class="leading-7 text-sm text-gray-600">役割</label>
                                                <div class="relative flex items-center" v-for="role in roles" :key="role.id">
                                                    <input type="checkbox" :id="`role-${role.id}`" v-model="form.roles[role.id]">
                                                    <label :for="`role-${role.id}`" class="ml-2">{{ getRoleDisplayName(role.name) }}</label>
                                                </div>
                                                <InputError class="p-1" :message="errors.roles" />
                                            </div>
                                            <div class="p-2 w-full">
                                                <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">更新する</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
