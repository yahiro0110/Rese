<script setup>
/**
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires onMounted - Vueコンポーネントがマウントされた後に実行する処理を登録するために使用
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                    クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires getRoleDisplayName - ユーザの権限を表す文字列を日本語に変換する関数
 */
import { computed, ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { getRoleDisplayName } from '@/Commons/disprayRoleName.js';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {string} roles - ユーザの権限を表す文字列
 */
const props = defineProps({
    roles: String,
});

/**
 * カウントダウンの残り秒数を管理するリアクティブな参照。
 *
 * @type {ref<number>}
 */
const countdown = ref(5);

/**
 * カウントダウンの残り秒数を1秒ごとに減らす処理を登録する。
 * カウントダウンが0になったらホーム画面へ遷移する。
 *
 * @returns {string} - カウントダウンの残り秒数
 */
onMounted(() => {
    const interval = setInterval(() => {
        if (countdown.value === 0) {
            clearInterval(interval);
            Inertia.visit(route('home'));
        } else {
            countdown.value--;
        }
    }, 1000);
});

/**
 * スペース文字を100個連結した文字列を返す関数。
 * 表示コンテンツ下部の文字にスペースを追加し、表示コンテンツの幅を調整するために使用。
 *
 * @returns {string} - スペース文字を100個連結した文字列
 */
const setSpaceChar = () => {
    return new Array(100).fill('&nbsp;').join('');
};

/**
 * ユーザの権限を表す文字列を日本語に変換した結果を返す算出プロパティ。
 */
const displayRoles = computed(() => {
    return props.roles.split(',').map(role => getRoleDisplayName(role)).join(', ');
});
</script>

<template>
    <div class="bg-gray-950 h-screen">
        <div class="flex justify-center py-10">
            <div class="h-48 md:h-auto w-48 flex-none bg-cover rounded-t md:rounded-t-none md:rounded-l text-center overflow-hidden" style="background-image: url('/images/caution.jpeg')" title="Woman holding a mug">
            </div>
            <div class="border-r border-b border-l border-gray-400 md:border-l-0 md:border-t lg:border-gray-400 bg-white rounded-b md:rounded-b-none md:rounded-r p-4 flex flex-col justify-between leading-normal">
                <div class="mb-8">
                    <p class="text-sm text-gray-600 flex items-center pb-3">
                        <svg class="fill-current text-gray-500 w-3 h-3 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z" />
                        </svg>
                        {{ displayRoles }}のみアクセス可能
                    </p>
                    <div class="text-gray-900 font-bold text-xl mb-2">当該ページへのアクセス権限はありません</div>
                    <p class="text-gray-700 text-base">{{ countdown }}秒後にホーム画面へ戻ります<span v-html="setSpaceChar()"></span></p>
                </div>
            </div>
        </div>
    </div>
</template>
