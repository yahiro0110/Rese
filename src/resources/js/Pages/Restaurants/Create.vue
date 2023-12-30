<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires useForm - Inertia.jsのフォームハンドリング機能を提供し、フォームの状態管理や送信時の処理を容易にする
 * @requires watch - 指定されたリアクティブなデータソースの変更を監視し、
 *                   変更があった場合に指定されたコールバック関数を実行するために使用される
 * @requires InputError - フォーム入力エラーを表示するためのコンポーネント
 * @requires useRestaurantForm - 店舗フォームのカスタムフック機能を提供。フォームの検証やデータハンドリングを行う
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import { useRestaurantForm } from '@/commons/useRestaurantForm'

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - フォーム入力時のエラーメッセージを含むオブジェクト
 * @property {Array} genres - 利用可能なジャンルの配列。各ジャンルは一意のID、名前の情報を持つ
 * @property {Array} prefectures - 利用可能な都道府県の配列。各都道府県は一意のID、名前の情報を持つ
 */
const props = defineProps({
    errors: Object,
    genres: Array,
    prefectures: Array,
});

/**
 * 店舗登録フォームのデータモデル。
 *
 * @type {Object} form - 店舗の登録に必要なデータを保持するフォームオブジェクト
 * @property {string|null} name - 店舗の名前、初期値はnull
 * @property {string|null} tel - 店舗の電話番号、初期値はnull
 * @property {string|null} email - 店舗のメールアドレス、初期値はnull
 * @property {string|null} postal - 店舗の郵便番号、初期値はnull
 * @property {string|null} address - 店舗の住所、初期値はnull
 * @property {string|null} description - 店舗の説明、初期値はnull
 * @property {string|null} restaurant_image - 店舗の画像、初期値はnull
 * @property {number|null} genre_id - 店舗のジャンルID、初期値はnull
 * @property {number|null} prefecture_id - 店舗の都道府県ID、初期値はnull
 * @property {File|null} file - 店舗の画像ファイル、初期値はnull
 */
const form = useForm({
    name: null,
    tel: null,
    email: null,
    postal: null,
    address: null,
    description: null,
    restaurant_image: null,
    genre_id: null,
    prefecture_id: null,
    file: null,
});

/**
 * useRestaurantFormカスタムフックからのデータとメソッド。
 *
 * @type {Object} formUtils - フォーム関連のユーティリティ関数とデータ
 * @property {Function} selectedGenreName - 選択されたジャンルの名前を返す関数
 * @property {Function} validatePhoneNumber - 電話番号のバリデーションを行う関数
 * @property {Function} validatePostal - 郵便番号のバリデーションを行う関数
 * @property {Function} fetchAddress - 郵便番号に基づいて住所を取得する関数
 * @property {Function} updatePrefectureId - 住所に基づいて都道府県IDを更新する関数
 * @property {Function} remainingCharacters - 店舗説明エリアの残り文字数を計算する関数
 * @property {ref<string|null>} imagePreview - 店舗画像のプレビュー用のURLを提供するリアクティブなプロパティ
 *                                             フォームに店舗画像がある場合はそのパスを、そうでなければnullを保持する
 * @property {Function} handleFileChange - ファイル選択をハンドルする関数
 */
const {
    selectedGenreName,
    validatePhoneNumber,
    validatePostal,
    fetchAddress,
    updatePrefectureId,
    remainingCharacters,
    imagePreview,
    handleFileChange,
} = useRestaurantForm(props, form);

/**
 * form.addressの変更を監視し、都道府県IDを更新するウォッチャー。
 * form.addressが変更されるたびに、updatePrefectureId関数が呼び出される。
 * これにより、ユーザーが入力した住所に基づいて、適切な都道府県IDがフォームに設定される。
 *
 * @watch form.address - フォームの住所フィールド
 * @action updatePrefectureId - 住所の変更に応じて都道府県IDを更新するアクション
 */
watch([() => form.address], updatePrefectureId);

/**
 * 新規にレストランを登録するための関数。
 * フォームデータを変換し、Inertia.jsのpostメソッドを使用してサーバーに送信する。
 * 特に電話番号フィールドでは、数字以外の文字をハイフンに置換する処理をおこなう。
 */
const createRestaurant = () => {
    form
        .transform((data) => ({
            ...data,
            // 郵便番号で数字以外の文字をハイフンに置換する
            tel: data.tel.replace(/[^\d]/g, '-'),
        }))
        .post(route('restaurants.store'));
};
</script>

<template>
    <Head title="店舗登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">店舗登録</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <section class="text-gray-600 body-font relative">
                            <div class="container px-5 py-24 mx-auto flex sm:flex-nowrap flex-wrap">
                                <div class="lg:w-3/4 md:w-1/2 bg-gray-100 rounded-lg sm:mr-10 p-4 md:p-10 grid-background">
                                    <p>表示プレビュー</p>
                                    <section class="text-gray-600 body-font">
                                        <div class="container px-5 py-5 mx-auto">
                                            <div class="flex flex-wrap -m-4">
                                                <div class="p-4 md:w-full">
                                                    <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden bg-white">
                                                        <img v-if="imagePreview" :src="imagePreview" class="lg:h-48 md:h-36 w-full object-cover object-center" alt="Preview image">
                                                        <div class="p-6">
                                                            <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">CATEGORY</h2>
                                                            <h2 class="title-font text-base font-medium text-gray-900 mb-3">{{ selectedGenreName }}</h2>
                                                            <h1 class="title-font text-2xl font-mono text-gray-900 mb-3">{{ form.name }}</h1>
                                                            <p class="leading-relaxed mb-3 auto-break-text">{{ form.description }}</p>
                                                            <div class="flex items-center flex-wrap">
                                                                <a href="#" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">
                                                                    詳細
                                                                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M5 12h14"></path>
                                                                        <path d="M12 5l7 7-7 7"></path>
                                                                    </svg>
                                                                </a>
                                                                <span class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                                                    <button class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                                                                        <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                                                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
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
                                </div>
                                <div class="lg:w-1/2 md:w-1/2 bg-white flex flex-col md:ml-auto w-full md:py-8 mt-8 md:mt-0">
                                    <form @submit.prevent="createRestaurant">
                                        <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">店舗の情報</h2>
                                        <p class="leading-relaxed mb-5 text-sm text-gray-600">店舗の情報を登録することができます。</p>
                                        <p class="leading-relaxed mb-2 text-sm text-gray-600"><span class="text-red-500 text-lg">*</span>は入力必須です。</p>
                                        <div class="relative mb-4">
                                            <label for="genre" class="leading-7 text-sm text-gray-600">ジャンル
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <div class="relative">
                                                <select v-model="form.genre_id" class="w-1/3 rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10">
                                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                                                </select>
                                                <InputError class="p-1" :message="errors.genre_id" />
                                            </div>
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="name" class="leading-7 text-sm text-gray-600">店舗名
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="name" name="name" v-model="form.name" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.name" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="tel" class="leading-7 text-sm text-gray-600">電話番号
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="tel" name="tel" v-model="form.tel" @input="validatePhoneNumber" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.tel" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="email" id="email" name="email" v-model="form.email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.email" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="postal" class="leading-7 text-sm text-gray-600">郵便番号
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="postal" name="postal" @input="validatePostal" @change="fetchAddress" v-model="form.postal" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" maxlength="7">
                                            <InputError class="p-1" :message="errors.postal" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="address" class="leading-7 text-sm text-gray-600">住所
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="address" name="address" v-model="form.address" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.address" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="description" class="leading-7 text-sm text-gray-600">店舗の説明
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <textarea id="description" name="description" maxlength="125" v-model="form.description" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                            <p class="text-sm text-red-500">残りの入力可能文字数: {{ remainingCharacters }}</p>
                                            <InputError class="p-1" :message="errors.description" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="file" class="leading-7 text-sm text-gray-600">店舗の画像</label>
                                            <input type="file" id="file" name="file" ref="file" @change="handleFileChange" @input="form.file = $event.target.files[0]" class="w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <p class="mt-1 text-sm text-red-500" id="file_input_help">PNG, JPG or JPEG (MAX. 5MB)</p>
                                            <InputError class="p-1" :message="errors.file" />
                                        </div>
                                        <button class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">登録</button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* 方眼背景のCSS */
.grid-background {
    background-image: linear-gradient(0deg, transparent 24px, rgba(0, 0, 0, 0.1) 25px), linear-gradient(90deg, transparent 24px, rgba(0, 0, 0, 0.1) 25px);
    background-size: 25px 25px;
}

/* 表示プレビューの説明文が改行されるように設定 */
.auto-break-text {
    word-break: break-all;
    white-space: pre-wrap;
    overflow-wrap: break-word;
}
</style>
