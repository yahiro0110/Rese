<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Core as YubinBangoCore } from 'yubinbango-core2';

const props = defineProps({
    errors: Object,
    restaurant: Object,
    genres: Array,
    prefectures: Array,
});

const form = useForm({
    id: props.restaurant.id,
    name: props.restaurant.name,
    tel: props.restaurant.tel,
    email: props.restaurant.email,
    postal: props.restaurant.postal,
    address: props.restaurant.address,
    description: props.restaurant.description,
    restaurant_image: props.restaurant.restaurant_image,
    genre_id: props.restaurant.genre_id,
    prefecture_id: props.restaurant.prefecture_id,
    file: null,
});

const fetchAddress = () => {
    new YubinBangoCore(String(form.postal), (value) => {
        form.address = value.region + value.locality + value.street;
    })
};

// 郵便番号の検証メソッドを追加
const validatePostal = (event) => {
    // 7桁の数字のみを許可する正規表現
    const regex = /^\d{0,7}$/;
    if (!regex.test(event.target.value)) {
        // 正規表現にマッチしない場合、最後の文字を削除
        form.postal = form.postal.slice(0, -1);
    }
};

const validatePhoneNumber = (event) => {
    // 入力された電話番号
    let phoneNumber = event.target.value;

    // 全角文字を削除
    phoneNumber = phoneNumber.replace(/[^\x20-\x7E]/g, '');

    // 半角数字とハイフン以外を全て削除
    phoneNumber = phoneNumber.replace(/[^0-9\-]/g, '');

    // フォームの値を更新
    form.tel = phoneNumber;
};

// 現在選択されているジャンルの名前を返す算出プロパティ
const selectedGenreName = computed(() => {
    const selectedGenre = props.genres.find(genre => genre.id === form.genre_id);
    return selectedGenre ? selectedGenre.name : '未選択';
});

// 画像のDataURLを格納するためのリアクティブなプロパティ
const imagePreview = ref('/storage/images/' + props.restaurant.restaurant_image);

// ファイル選択時に呼ばれるメソッド
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// 住所から都道府県IDを厳密に検出する関数
const updatePrefectureId = () => {
    for (const prefecture of props.prefectures) {
        if (form.address.startsWith(prefecture.name)) {
            form.prefecture_id = prefecture.id;
            return;
        }
    }
    // 該当する都道府県が見つからない場合はIDをnullまたは初期値に設定
    form.prefecture_id = null;
};

// form.address または form.postal が変更されるたびに都道府県IDを更新
watch([() => form.address], updatePrefectureId);

// 最大文字数を定義
const maxCharacters = 125;

// 残り文字数を計算するリアクティブなプロパティ
const remainingCharacters = computed(() => {
    return maxCharacters - form.description.length;
});

const updateRestaurant = (id) => {
    form
        .transform((data) => ({
            ...data,
            // 郵便番号で数字以外の文字をハイフンに置換する
            tel: data.tel.replace(/[^\d]/g, '-'),
        }))
        .post(route('restaurants.formUpdate', { restaurant: id }));
};
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
                        <section class="text-gray-600 body-font relative">
                            <div class="container px-5 py-24 mx-auto flex sm:flex-nowrap flex-wrap">
                                <div class="lg:w-3/4 md:w-1/2 bg-gray-100 rounded-lg sm:mr-10 p-4 md:p-10">
                                    <div
                                        class="w-full bg-gray-300 rounded-lg overflow-hidden p-10 flex items-end justify-start relative">
                                        <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0"
                                            title="map" marginheight="0" marginwidth="0" scrolling="no"
                                            src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=%C4%B0zmir+(My%20Business%20Name)&ie=UTF8&t=&z=14&iwloc=B&output=embed"
                                            style="filter: grayscale(1) contrast(1.2) opacity(0.4);"></iframe>
                                        <div class="h-72"></div>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-lg sm:mr-10 p-4 md:p-10">
                                        <p class="text-sm md:text-base text-center md:text-left">表示プレビュー</p>
                                        <section class="text-gray-600 body-font">
                                            <div class="container md:px-5 md:py-5 mx-auto">
                                                <div class="flex flex-wrap -m-4">
                                                    <div class="py-4 md:px-4 w-full">
                                                        <div
                                                            class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden bg-white">
                                                            <!-- 条件付きレンダリングで画像を表示 -->
                                                            <!-- <img class="lg:h-48 md:h-36 w-full object-cover object-center"
                                                            src="https://dummyimage.com/1200x500" alt="restaurant image"> -->
                                                            <img v-if="imagePreview" :src="imagePreview"
                                                                class="lg:h-48 md:h-36 w-full object-cover object-center"
                                                                alt="Preview image">
                                                            <div class="p-6">
                                                                <h2
                                                                    class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                                                                    CATEGORY</h2>
                                                                <h2
                                                                    class="title-font text-base font-medium text-gray-900 mb-3">
                                                                    {{ selectedGenreName }}
                                                                </h2>
                                                                <h1
                                                                    class="title-font text-2xl font-mono text-gray-900 mb-3">
                                                                    {{ form.name }}
                                                                </h1>
                                                                <p class="leading-relaxed mb-3 auto-break-text">{{
                                                                    form.description }}</p>
                                                                <div class="flex items-center flex-wrap">
                                                                    <a href="#"
                                                                        class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">
                                                                        詳細
                                                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24"
                                                                            stroke="currentColor" stroke-width="2"
                                                                            fill="none" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path d="M5 12h14"></path>
                                                                            <path d="M12 5l7 7-7 7"></path>
                                                                        </svg>
                                                                    </a>
                                                                    <span
                                                                        class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                                                        <button
                                                                            class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                                                                            <svg fill="currentColor" stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                class="w-5 h-5" viewBox="0 0 24 24">
                                                                                <path
                                                                                    d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z">
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
                                    </div>
                                </div>
                                <div
                                    class="lg:w-1/2 md:w-1/2 bg-white flex flex-col md:ml-auto w-full md:py-8 mt-8 md:mt-0">
                                    <form @submit.prevent="updateRestaurant(restaurant.id)">
                                        <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">店舗の情報</h2>
                                        <p class="leading-relaxed mb-5 text-sm text-gray-600">店舗の情報を編集することができます。</p>
                                        <p class="leading-relaxed mb-2 text-sm text-gray-600"><span
                                                class="text-red-500 text-lg">*</span>は入力必須です。</p>
                                        <div class="relative mb-4">
                                            <label for="genre" class="leading-7 text-sm text-gray-600">ジャンル
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <div class="relative">
                                                <select v-model="form.genre_id"
                                                    class="w-1/3 rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10">
                                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                                                        {{ genre.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="name" class="leading-7 text-sm text-gray-600">店舗名
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="name" name="name" v-model="form.name"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.name" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="tel" class="leading-7 text-sm text-gray-600">電話番号
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="tel" name="tel" v-model="form.tel"
                                                @input="validatePhoneNumber"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.tel" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="email" id="email" name="email" v-model="form.email"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.email" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="postal" class="leading-7 text-sm text-gray-600">郵便番号
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="postal" name="postal" @input="validatePostal"
                                                @change="fetchAddress" v-model="form.postal"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                maxlength="7">
                                            <InputError class="p-1" :message="errors.postal" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="address" class="leading-7 text-sm text-gray-600">住所
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <input type="text" id="address" name="address" v-model="form.address"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <InputError class="p-1" :message="errors.address" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="description" class="leading-7 text-sm text-gray-600">店舗の説明
                                                <span class="text-red-500 text-lg">*</span>
                                            </label>
                                            <textarea id="description" name="description" maxlength="125"
                                                v-model="form.description"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                            <!-- 残りの入力可能文字数を表示 -->
                                            <p class="text-sm text-red-500">
                                                残りの入力可能文字数: {{ remainingCharacters }}
                                            </p>
                                            <InputError class="p-1" :message="errors.description" />
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="file" class="leading-7 text-sm text-gray-600">店舗の画像</label>
                                            <input type="file" id="file" name="file" ref="file" @change="handleFileChange"
                                                @input="form.file = $event.target.files[0]"
                                                class="w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <p class="mt-1 text-sm text-red-500" id="file_input_help">
                                                PNG, JPG or JPEG (MAX. 5MB)</p>
                                            <InputError class="p-1" :message="errors.file" />
                                        </div>
                                        <button
                                            class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">更新</button>
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
/* 表示プレビューの説明文が改行されるように設定 */
.auto-break-text {
    word-break: break-all;
    white-space: pre-wrap;
    overflow-wrap: break-word;
}
</style>
