<script setup>
/**
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires computed - Vueコンポーネント内で算出プロパティを定義するために使用
 * @requires defineEmits - Vueコンポーネントのイベントを定義するために使用
 * @requires getCurrentInstance - Vueコンポーネントのインスタンスを取得するために使用
 * @requires onMounted - Vueコンポーネントがマウントされた後に実行する処理を定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires ScheduleBooker - 店舗予約フォームを表示するためのVueコンポーネント
 * @requires ReviewForm - 口コミ入力フォームを表示するためのVueコンポーネント
 * @requires Inertia - Inertia.jsを使用するために使用
 */
import { Head } from '@inertiajs/inertia-vue3';
import { computed, defineEmits, getCurrentInstance, onMounted, ref } from 'vue';
import ScheduleBooker from '@/Views/ScheduleBooker.vue';
import ReviewForm from '@/Views/ReviewForm.vue';
import { Inertia } from '@inertiajs/inertia';

/**
 * preline UIを使用するための初期化処理。
 */
const instance = getCurrentInstance();
onMounted(() => {
    setTimeout(() => {
        instance.appContext.config.globalProperties.$HSStaticMethods.autoInit();
    }, 100);
});

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - エラーメッセージを含むオブジェクト
 * @property {Object} restaurant - 選択された店舗の情報を含むオブジェクト
 */
const props = defineProps({
    errors: Object,
    restaurant: Object
});

/**
 * 選択された店舗の情報を含むオブジェクト内のreviewsから特定のフィールドを抽出する算出プロパティ。
 */
const extractedReviews = computed(() => {
    return props.restaurant.reviews.map(review => ({
        reviewId: review.id,
        rating: review.rating,
        title: review.title,
        comment: review.comment,
        imageUrl: review.review_images[0]?.image_path ? '/storage/images/review/' + review.review_images[0].image_path : null,
        userId: review.user.id,
        userName: review.user.name,
        createdAt: formatDate(review.created_at),
    }));
});

/**
 * 抽出した口コミの件数を算出するプロパティ。
 */
const reviewCount = computed(() => extractedReviews.value.length);

/**
 * 管理者ユーザのID。
 */
const adminId = 1;

/**
 * 日付をYYYY-MM-DD形式に変換する関数。
 *
 * @param {string} dateString - 変換する日付の文字列
 * @returns {string} 変換後の日付文字列
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

/**
 * イベントを発火させるためのemit関数を定義。
 *
 * @property {Function} back - 'back'イベントを発火させる関数
 */
const emit = defineEmits(['back', 'toggleLike']);

/**
 * モーダルウィンドウの表示状態を管理するリアクティブなプロパティ。
 * モーダルウィンドウが表示されている場合はtrue、そうでない場合はfalse。
 *
 * @type {ref<boolean>}
 */
const showContent = ref(false);

/**
 * モーダルウィンドウを開く関数。
 */
const openModal = () => {
    showContent.value = true;
};

/**
 * モーダルウィンドウを閉じる関数。
 */
const closeModal = () => {
    showContent.value = false;
};

/**
 * 'back'イベントを発火させる関数。
 */
function emitBackEvent() {
    emit('back');
}

/**
 * 'back'イベントを発火させる関数。
 */
function emitToggleLike(restaurant) {
    emit('toggleLike', restaurant);
}

/**
 * Googleマップの埋め込みURLを生成する算出プロパティ。
 * 店舗の住所を基に、Googleマップの埋め込み用URLを返す。
 * 環境変数からGoogle Maps APIキーを取得し、URLに組み込む。
 *
 * @returns {string} Googleマップの埋め込み用URL
 */
const googleMapUrl = computed(() => {
    // Googleマップの基本URL
    const baseMapUrl = 'https://www.google.com/maps/embed/v1/place';
    // 環境変数からAPIキーを取得
    const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;
    // レストランの住所をURLエンコード
    const address = encodeURIComponent(props.restaurant.address);
    // 完成したURLを返す
    return `${baseMapUrl}?key=${apiKey}&q=${address}`;
});

/**
 * 口コミ入力フォームの表示状態を管理するリアクティブなプロパティ。
 *
 * @type {boolean} inputReviewForm - 口コミ入力フォームの表示状態
 * @type {boolean} updateReviewForm - 口コミ更新フォームの表示状態
 */
const inputReviewForm = ref(false);
const updateReviewForm = ref(false);

/**
 * 選択されたレビューを管理するリアクティブなプロパティ。
 *
 * @type {ref<null|Object>}
 */
const selectedReview = ref(null);

/**
 * 選択されたレビューを更新する関数。
 *
 * @param {Object} review - 選択されたレビュー
 */
const selectReview = (review) => {
    selectedReview.value = review;
    openUpdateReviewForm();
};

/**
 * フラッシュメッセージをリロードする関数。
 */
const refreshFlashMessage = () => {
    Inertia.reload({ only: ['flash'] });
};

/**
 * 口コミ入力フォーム(新規・更新)を表示する関数。
 * なお一度フラッシュメッセージが表示されたあと、再度入力フォームから投稿・更新してもメッセージが表示されないため、
 * その対策として、入力フォームを表示する前にフラッシュメッセージをリロードする処理を実行している。
 *
 */
const openInputReviewForm = () => {
    refreshFlashMessage();
    inputReviewForm.value = true;
};
const openUpdateReviewForm = () => {
    refreshFlashMessage();
    updateReviewForm.value = true;
};
const closeInputReviewForm = () => inputReviewForm.value = false;
const closeUpdateReviewForm = () => updateReviewForm.value = false;

/**
 * 指定された口コミIDに基づいて口コミ情報を削除する関数。
 * Inertia.jsのdeleteメソッドを使用して、サーバーにHTTP DELETEリクエストを送信する。
 *
 * @param {number} id - 削除する口コミの一意の識別子（ID）
 */
const deleteReview = (id) => {
    Inertia.delete(route('reviews.destroy', { review: id }), {
        onBefore: () => confirm('口コミを削除しますか？'),
    });
};
</script>

<template>

    <Head title="詳細" />
    <ScheduleBooker v-if="showContent" v-bind="{ restaurantId: restaurant.id, schedule: null, errors }" @closeModal="closeModal" />
    <ReviewForm v-if="inputReviewForm" v-bind="{ restaurantId: restaurant.id, reviewInfo: null, errors }" @closeModal="closeInputReviewForm" />
    <ReviewForm v-if="updateReviewForm" v-bind="{ restaurantId: restaurant.id, reviewInfo: selectedReview, errors }" @closeModal="closeUpdateReviewForm" />
    <section class="text-gray-600 body-font overflow-hidden animate-scale-in-hor-left">

        <div class="container px-5 py-24 mx-auto">
            <div class="text-center md:text-left md:px-10 md:pb-5">
                <a href="#" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" @click="emitBackEvent">
                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                    戻る
                </a>
            </div>
            <div class="lg:w-4/5 mx-auto flex flex-wrap">
                <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ restaurant.genre.name }}</h2>
                    <h1 class="text-gray-900 text-3xl title-font font-medium mb-4">{{ restaurant.name }}
                    </h1>
                    <div class="flex mb-4">お店の概要</div>
                    <p class="leading-relaxed mb-4">{{ restaurant.description }}</p>
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
                        <button @click="openModal" class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">予約する</button>
                        <button @click="openInputReviewForm" class="flex ml-4 text-white bg-orange-500 border-0 py-2 px-6 focus:outline-none hover:bg-orange-600 rounded">評価する</button>
                        <button @click="emitToggleLike(restaurant)" class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4" :class="{ 'bg-red-100': restaurant.liked }">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24" :class="{ 'text-red-500': restaurant.liked }">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="lg:w-1/2 w-full lg:h-auto h-64 bg-gray-300 rounded-lg  relative">
                    <!-- <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map" marginheight="0" marginwidth="0" scrolling="no" src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=%C4%B0zmir+(My%20Business%20Name)&ie=UTF8&t=&z=14&iwloc=B&output=embed" style="filter: grayscale(1) contrast(1.2) opacity(0.4);"></iframe> -->
                    <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map" marginheight="0" marginwidth="0" scrolling="no" :src="googleMapUrl"></iframe>
                </div>
            </div>
        </div>

        <div class="container px-5 py-5 mx-auto">
            <div data-hs-carousel='{
                        "loadingClasses": "opacity-0"
                        }' class="relative">
                <div class="hs-carousel relative overflow-hidden w-full min-h-[350px] bg-white rounded-lg">
                    <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                        <div class="hs-carousel-slide">
                            <div class="flex justify-center h-full bg-gray-100 p-6">
                                <span class="self-center text-4xl transition duration-700">First slide</span>
                            </div>
                        </div>
                        <div class="hs-carousel-slide">
                            <div class="flex justify-center h-full bg-gray-200 p-6">
                                <span class="self-center text-4xl transition duration-700">Second slide</span>
                            </div>
                        </div>
                        <div class="hs-carousel-slide">
                            <div class="flex justify-center h-full bg-gray-300 p-6">
                                <span class="self-center text-4xl transition duration-700">Third slide</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="hs-carousel-prev hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/[.1]">
                    <span class="text-2xl" aria-hidden="true">
                        <svg class="flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </span>
                    <span class="sr-only">Previous</span>
                </button>
                <button type="button" class="hs-carousel-next hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/[.1]">
                    <span class="sr-only">Next</span>
                    <span class="text-2xl" aria-hidden="true">
                        <svg class="flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </span>
                </button>

                <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 space-x-2">
                    <span class="hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 w-3 h-3 border border-gray-400 rounded-full cursor-pointer"></span>
                    <span class="hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 w-3 h-3 border border-gray-400 rounded-full cursor-pointer"></span>
                    <span class="hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 w-3 h-3 border border-gray-400 rounded-full cursor-pointer"></span>
                </div>
            </div>
        </div>

        <div class="container px-5 py-24 mx-auto">
            <h1 class="text-3xl font-medium title-font text-gray-900 mb-12 text-center">口コミ<span>({{ reviewCount }}件)</span></h1>
            <div class="flex flex-wrap -m-4">
                <div class="p-4 md:w-1/2 w-full" v-for="review in extractedReviews" :key="review.reviewId">
                    <div class="h-full bg-gray-100 p-8 rounded">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="block w-5 h-5 text-gray-400 mb-4" viewBox="0 0 975.036 975.036">
                            <path d="M925.036 57.197h-304c-27.6 0-50 22.4-50 50v304c0 27.601 22.4 50 50 50h145.5c-1.9 79.601-20.4 143.3-55.4 191.2-27.6 37.8-69.399 69.1-125.3 93.8-25.7 11.3-36.8 41.7-24.8 67.101l36 76c11.6 24.399 40.3 35.1 65.1 24.399 66.2-28.6 122.101-64.8 167.7-108.8 55.601-53.7 93.7-114.3 114.3-181.9 20.601-67.6 30.9-159.8 30.9-276.8v-239c0-27.599-22.401-50-50-50zM106.036 913.497c65.4-28.5 121-64.699 166.9-108.6 56.1-53.7 94.4-114.1 115-181.2 20.6-67.1 30.899-159.6 30.899-277.5v-239c0-27.6-22.399-50-50-50h-304c-27.6 0-50 22.4-50 50v304c0 27.601 22.4 50 50 50h145.5c-1.9 79.601-20.4 143.3-55.4 191.2-27.6 37.8-69.4 69.1-125.3 93.8-25.7 11.3-36.8 41.7-24.8 67.101l35.9 75.8c11.601 24.399 40.501 35.2 65.301 24.399z"></path>
                        </svg>

                        <h2 class="text-lg text-gray-900 font-medium title-font mb-2">{{ review.title }}</h2>

                        <!-- Rating -->
                        <div class="flex items-center mb-2">
                            <!-- 星を動的に表示 -->
                            <template v-for="star in 5">
                                <svg v-if="star <= review.rating" class="flex-shrink-0 size-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                                <svg v-else class="flex-shrink-0 size-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </template>
                        </div>
                        <!-- End Rating -->

                        <p class="leading-relaxed mb-6 whitespace-pre-line">{{ review.comment }}</p>

                        <img alt="review image" v-if="review.imageUrl !== null" :src="review.imageUrl" class="rounded-md h-80 object-cover object-center">

                        <a class="inline-flex items-center mt-4 mb-4">
                            <img alt="testimonial" src="/images/avatar.svg" class="w-12 h-12 rounded-full flex-shrink-0 object-cover object-center">
                            <span class="flex-grow flex flex-col pl-4">
                                <span class="title-font font-medium text-gray-900">{{ review.userName }}</span>
                                <span class="text-gray-500 text-sm">{{ review.createdAt }}</span>
                            </span>
                        </a>

                        <div class="mt-1 text-right">
                            <button v-show="review.userId === $page.props.auth.user.id" @click="selectReview(review)" class="ml-auto mr-2 text-white text-sm bg-orange-500 border-0 p-2 focus:outline-none hover:bg-orange-600 rounded">編集</button>
                            <button v-show="(review.userId === $page.props.auth.user.id) || ($page.props.auth.user.id === adminId)" @click="deleteReview(review.reviewId)" class="text-white text-sm bg-red-500 border-0 p-2 focus:outline-none hover:bg-red-600 rounded">削除</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>
