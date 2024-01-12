<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires Link - Inertia.jsのリンクコンポーネント
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires watchEffect - Vueコンポーネント内でリアクティブなデータを監視するために使用
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                   クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires Pagination - ページネーションコンポーネント
 * @requires FlashMessage - フラッシュメッセージ表示コンポーネント
 * @requires HeaderMessage - ヘッダーメッセージ表示コンポーネント
 * @requires getRoleDisplayName - ユーザの権限を表す文字列を日本語に変換する関数
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import Pagination from '@/Components/Pagination.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import HeaderMessage from '@/Components/HeaderMessage.vue';
import { getRoleDisplayName } from '@/Commons/disprayRoleName.js';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} users - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 * @property {Array} roles - 利用可能なユーザロールの配列。各ロールは一意の名前と説明を持つ。
 * @property {Object} searchInfo - ユーザ検索用のクエリ文字列と選択されたロールおよび取得件数を保持するオブジェクト。
 */
const props = defineProps({
    users: Object,
    roles: Array,
    searchInfo: Object,
});

/**
 * ユーザ検索用のクエリ文字列。
 * セッションストレージから「searchKey」を取得して初期化をおこない、値が存在しない場合は、空文字列で初期化される。
 * @type {Ref<String>}
 */
const search = ref(props.searchInfo.searchKey);

/**
 * 選択されたユーザロールを追跡するリアクティブリファレンス。
 * セッションストレージから「selectedRoles」をJSONとして解析し、初期化をおこない。値が存在しない場合は、空の配列で初期化される。
 * @type {Ref<Array>}
 */
const selectedRoles = ref(props.searchInfo.selectedRoles);

/**
 * Vueのリアクティブシステムを使用して、`search` と `selectedRoles` の変更を監視し、
 * これらの変更があるたびにセッションストレージを更新する。
 * - `search` は `searchKey` として文字列としてセッションストレージに保存される。
 * - `selectedRoles` はJSON文字列に変換されてから `selectedRoles` としてセッションストレージに保存される。
 * これにより、ページ再読み込み時にユーザの検索状態とロール選択状態を維持することができる。
 */
watchEffect(() => {
    localStorage.setItem('searchKey', search.value);
    localStorage.setItem('selectedRoles', JSON.stringify(selectedRoles.value));
});

/**
 * 現在の検索クエリ（`search`）と選択されたロール（`selectedRoles`）に基づいてユーザを検索する関数。
 * 検索クエリとロールが両方とも空の場合は、`searchClear`関数を呼び出して検索条件をクリアする。
 * それ以外の場合は、Inertia.jsのgetメソッドを使用してサーバーに検索クエリを送信し、
 * 応答に基づいてユーザ一覧を更新する。
 *
 * @function searchUser
 */
const searchUser = () => {
    if (!search.value && !selectedRoles.value.length) {
        // 検索キーワードと選択されたロールが両方とも空の場合、検索条件をクリアする
        return searchClear();
    } else {
        // それ以外の場合、通常の検索処理を行う
        Inertia.get(route('users.index', { search: search.value, roles: selectedRoles.value }), {
            preserveState: true
        });
    }
};

/**
 * 検索条件をクリアし、全ユーザを表示するための関数。
 * `search`と`selectedRoles`のリアクティブリファレンスをリセットし、Inertia.jsを使用してサーバーにリクエストを送信する。
 * これにより、フィルタリングなしでユーザ一覧が再ロードされる。
 *
 * @function searchClear
 */
const searchClear = () => {
    search.value = '';
    selectedRoles.value = [];
    Inertia.get(route('users.index', { clear: true }), {
        preserveState: true
    });
};
</script>

<template>
    <Head title="ユーザ一覧" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ユーザ一覧</h2>
        </template>

        <div :class="{ 'animate-shake-horizontal': $page.props.flash.status === 'warning' }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <HeaderMessage :message="searchInfo" />
                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-8 mx-auto">
                                <!-- ロール選択のチェックボックス -->
                                <div class="flex pl-5 my-4 lg:w-2/3 w-full mx-auto">
                                    <label v-for="role in roles" :key="role" class="inline-flex items-center">
                                        <input type="checkbox" v-model="selectedRoles" :value="role.name">
                                        <span class="mx-2">{{ getRoleDisplayName(role.name) }}</span>
                                    </label>
                                </div>
                                <!-- 検索バーとボタン -->
                                <div class="flex pb-5 pl-5 my-4 lg:w-2/3 w-full mx-auto">
                                    <input type="text" class="w-2/3" v-model="search" placeholder="検索ワードを入力してください">
                                    <button class="bg-blue-300 text-white py-2 px-2 mx-2 w-20 rounded" @click="searchUser">検索</button>
                                    <button class="bg-red-300 text-white py-2 px-2 mx-2 w-20 rounded" @click="searchClear">クリア</button>
                                </div>
                                <!-- ユーザ一覧 -->
                                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                                    <table class="table-auto w-full text-left whitespace-no-wrap">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                                                    ID</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                    名前</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                    メールアドレス</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="user in users.data" :key="user.id">
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.id }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.name }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.email }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">
                                                    <Link as="button" :href="route('users.show', { user: user.id })" class="flex ml-auto text-white bg-indigo-500 border-0 p-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">
                                                    詳細
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <Pagination class="flex justify-center mt-6" :links="users.links"></Pagination>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
