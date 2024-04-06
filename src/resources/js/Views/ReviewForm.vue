<script setup>
/**
 * @requires useForm - Inertia.jsのフォームハンドリング機能を提供し、フォームの状態管理や送信時の処理を容易にする
 * @requires computed - Vueコンポーネントの算出プロパティを定義するために使用
 * @requires defineEmits - Vueコンポーネントのイベントを定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires InputError - フォーム入力エラーを表示するためのコンポーネント
 * @requires InputLabel - フォーム入力ラベルを表示するためのコンポーネント
 * @requires TextInput - テキスト入力フォームを表示するためのコンポーネント
 */
import { useForm } from '@inertiajs/inertia-vue3';
import { computed, defineEmits, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - エラーメッセージを含むオブジェクト
 * @property {Object} restaurantId - 選択された店舗ID
 * @property {Object} schedule - 予約情報を含むオブジェクト
 */
const props = defineProps({
    errors: Object,
    reviewInfo: Object,
    restaurantId: Number,
});

/**
 * イベントを発火させるためのemit関数を定義。
 *
 * @property {Function} closeModal モーダルウィンドウを閉じる関数
 */
const emit = defineEmits(['closeModal']);

/**
 * モーダルウィンドウを閉じるイベントを発火させる関数。
 */
const closeModal = () => {
    emit('closeModal');
};

/**
 * 口コミ入力フォームのデータモデル。
 *
 * @type {Object} form - 口コミの登録に必要なデータを保持するフォームオブジェクト
 * @property {number} review_id - 口コミID
 * @property {number} restaurant_id - 店舗ID
 * @property {number} rating - 評価数
 * @property {String} title - タイトル
 * @property {String} comment - 内容
 * @property {String} imageUrl - レビュー画像URL
 * @property {File} file - レビュー画像ファイル
 */
const form = ref(useForm({
    review_id: props.reviewInfo?.reviewId ?? null,
    restaurant_id: props.restaurantId,
    rating: props.reviewInfo?.rating ?? 0,
    title: props.reviewInfo?.title ?? null,
    comment: props.reviewInfo?.comment ?? null,
    imageUrl: props.reviewInfo?.imageUrl ?? null,
    file: null,
}));

/**
 * 口コミ入力フォーム（タイトル、内容）の最大文字数。
 *
 * @type {number} - 最大文字数
 */
const maxTitleLength = 50;
const maxCommentLength = 400;

/**
 * 口コミ画像のプレビュー用のURLを提供するリアクティブなプロパティ。
 *
 *  @type {string} - 店舗画像のファイルパス
 */
let imagePreview = form.value.imageUrl ? ref(form.value.imageUrl) : ref(null);

/**
 * ファイル選択時に呼び出され、画像プレビューを更新する。
 *
 * @param {Event} event - ファイル入力イベント
 */
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

/**
 * submitボタンのラベルを算出する算出プロパティ。
 * reviewInfoがnullであれば「投稿する」、そうでなければ「更新する」を返す。
 */
const buttonName = computed(() => {
    return props.reviewInfo === null ? '投稿する' : '更新する';
});

/**
 * 口コミの「新規投稿」または「既存内容の更新」を判断してリクエスト処理を実行する関数。
 * reviewInfoがnullであれば「新規投稿」、そうでなければ「既存を更新」のリクエスト処理を返す。
 */
const handleSubmit = () => {
    if (props.reviewInfo === null) {
        storeReview();
    } else {
        updateReview(props.reviewInfo.reviewId);
    }
};

/**
 * 口コミを登録する関数。
 * フォームデータを指定したエンドポイントにPOSTリクエストとして送信し、
 * 成功した場合にモーダルウィンドウを閉じる。
 */
const storeReview = () => {
    form.value.post(route('reviews.store'), {
        onSuccess: () => closeModal(),
    });
};

/**
 * 既存の口コミ情報を更新する関数。
 * フォームデータを指定したエンドポイントにPOSTリクエストとして送信し、
 * 成功した場合にモーダルウィンドウを閉じる。
 */
const updateReview = (id) => {
    form.value.post(route('reviews.refresh', { review: id }), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <section id="overlay" class="text-gray-600 body-font relative">
        <div id="content" class="container px-5 py-24 animate-flip-in-diag-1-bl">
            <p class="text-right"><button @click='closeModal'>close</button></p>
            <form @submit.prevent="handleSubmit">
                <div class="md:w-3/4 bg-white rounded-lg md:p-0 flex flex-col w-full mt-0 md:mt-5 relative z-10 mx-auto">
                    <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">口コミを投稿</h2>
                    <p class="leading-relaxed mb-5 text-gray-600">体験を評価してください。</p>

                    <div>
                        <InputLabel for="rating" value="評価数" />

                        <!-- Rating -->
                        <div class="flex flex-row-reverse justify-end items-center mt-1 mb-5">
                            <input id="rating-5" type="radio" class="peer -ms-5 size-5 bg-transparent border-0 text-transparent cursor-pointer appearance-none checked:bg-none focus:bg-none focus:ring-0 focus:ring-offset-0" name="rating" value="5" v-model="form.rating">
                            <label for="rating-5" class="peer-checked:text-yellow-400 text-gray-300 pointer-events-none">
                                <svg class="flex-shrink-0 size-5 mx-0.5" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </label>
                            <input id="rating-4" type="radio" class="peer -ms-5 size-5 bg-transparent border-0 text-transparent cursor-pointer appearance-none checked:bg-none focus:bg-none focus:ring-0 focus:ring-offset-0" name="rating" value="4" v-model="form.rating">
                            <label for="rating-4" class="peer-checked:text-yellow-400 text-gray-300 pointer-events-none">
                                <svg class="flex-shrink-0 size-5 mx-0.5" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </label>
                            <input id="rating-3" type="radio" class="peer -ms-5 size-5 bg-transparent border-0 text-transparent cursor-pointer appearance-none checked:bg-none focus:bg-none focus:ring-0 focus:ring-offset-0" name="rating" value="3" v-model="form.rating">
                            <label for="rating-3" class="peer-checked:text-yellow-400 text-gray-300 pointer-events-none">
                                <svg class="flex-shrink-0 size-5 mx-0.5" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </label>
                            <input id="rating-2" type="radio" class="peer -ms-5 size-5 bg-transparent border-0 text-transparent cursor-pointer appearance-none checked:bg-none focus:bg-none focus:ring-0 focus:ring-offset-0" name="rating" value="2" v-model="form.rating">
                            <label for="rating-2" class="peer-checked:text-yellow-400 text-gray-300 pointer-events-none">
                                <svg class="flex-shrink-0 size-5 mx-0.5" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </label>
                            <input id="rating-1" type="radio" class="peer -ms-5 size-5 bg-transparent border-0 text-transparent cursor-pointer appearance-none checked:bg-none focus:bg-none focus:ring-0 focus:ring-offset-0" name="rating" value="1" v-model="form.rating">
                            <label for="rating-1" class="peer-checked:text-yellow-400 text-gray-300 pointer-events-none">
                                <svg class="flex-shrink-0 size-5 mx-0.5" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </label>
                        </div>
                        <!-- End Rating -->

                        <InputError class="-mt-3 mb-1.5" :message="form.errors.rating" />
                    </div>

                    <div>
                        <InputLabel for="title" value="タイトル" />

                        <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" :class="{ 'focus:border-red-500': form.title?.length > maxTitleLength, 'focus:ring-red-500': form.title?.length > maxTitleLength }" />

                        <p class="mt-1 text-sm text-right" :class="{ 'text-gray-500': form.title?.length <= maxTitleLength, 'text-red-500': form.title?.length > maxTitleLength }">{{ form.title?.length ?? 0 }} / {{ maxTitleLength }}</p>

                        <InputError class="-mt-4 mb-1" :message="form.errors.title" />

                        <InputError class="mb-1" v-show="form.title?.length > maxTitleLength" :message="'タイトルは' + maxTitleLength + '文字までです'" :class="{ '-mt-4': !(form.errors.title), 'mt-2': form.errors.title }" />
                    </div>

                    <div>
                        <InputLabel for="comment" value="内容" />

                        <textarea class="mt-1 py-3 px-4 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="6" placeholder="カジュアルな夜のお手かけにおすすめのスポット" v-model="form.comment" :class="{ 'focus:border-red-500': form.comment?.length > maxCommentLength, 'focus:ring-red-500': form.comment?.length > maxCommentLength }"></textarea>

                        <p class="mt-1 text-sm text-right" :class="{ 'text-gray-500': form.comment?.length <= maxCommentLength, 'text-red-500': form.comment?.length > maxCommentLength }">{{ form.comment?.length ?? 0 }} / {{ maxCommentLength }}</p>

                        <InputError class="-mt-4 mb-1" :message="form.errors.comment" />

                        <InputError class="mb-1" v-show="form.comment?.length > maxCommentLength" :message="'投稿できる内容は' + maxCommentLength + '文字までです'" :class="{ '-mt-4': !(form.errors.comment), 'mt-2': form.errors.comment }" />
                    </div>

                    <div>
                        <InputLabel for="comment" value="レビュー画像" />

                        <input type="file" id="file" name="file" ref="file" @change="handleFileChange" @input="form.file = $event.target.files[0]" class="w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none text-gray-700 py-1 px-0 leading-8 transition-colors duration-200 ease-in-out mt-1">

                        <p class="mt-1 text-sm text-red-500" id="file_input_help">PNG, JPG or JPEG (MAX. 5MB)</p>

                        <img v-if="imagePreview" :src="imagePreview" class="h-80 w-full object-cover object-center" alt="Preview image">

                        <InputError class="-mt-4" :message="form.errors.file" />
                    </div>

                    <button class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg mt-10">{{ buttonName }}</button>
                </div>
            </form>
        </div>
    </section>
</template>

<style scoped>
#overlay {
    /*　要素を重ねた時の順番　*/
    z-index: 1;

    /*　画面全体を覆う設定　*/
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);

    /*　画面の中央に要素を表示させる設定　*/
    display: flex;
    align-items: center;
    justify-content: center;
}

#content {
    z-index: 2;
    height: 100%;
    width: 100%;
    padding: 1em;
    background: #fff;
    overflow: auto;
}

/* mdサイズ以上（768px以上）の場合に適用されるスタイル */
@media (min-width: 768px) {
    #content {
        height: 80%;
        width: 50%;
    }
}
</style>
