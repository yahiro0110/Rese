<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                     クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires computed - Vueコンポーネントの算出プロパティを定義するために使用
 * @requires getCurrentInstance - 現在アクティブなVueコンポーネントインスタンスを取得するために使用。setup関数内でのみ呼び出すことができる
 * @requires onMounted - Vueコンポーネントがマウントされた後に実行する処理を定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires Detail - 店舗詳細情報を表示するためのVueコンポーネント
 * @requires FlashMessage - フラッシュメッセージ表示コンポーネント
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { computed, getCurrentInstance, onMounted, ref } from 'vue';
import Detail from '@/Views/Detail.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

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
 * @property {Array} restaurants - 店舗の情報を含むオブジェクトの配列
 * @property {Array} genres - 店舗のジャンル情報を含むオブジェクトの配列
 * @property {Array} prefectures - 店舗の都道府県情報を含むオブジェクトの配列
 */
const props = defineProps({
    errors: Object,
    restaurants: Array,
    genres: Array,
    prefectures: Array,
});

/**
 * 都道府県とジャンルの各selectタグで選択された値を保持するリアクティブな参照。
 * ユーザーがフィルタ条件を選択すると、span.truncate要素の参照が更新される。
 *
 * @type {Ref<string>} prefectureElement - 都道府県の選択値を保持するリアクティブな参照
 * @type {Ref<string>} genreElement - ジャンルの選択値を保持するリアクティブな参照
 */
const prefectureElement = ref('');
const genreElement = ref('');

/**
 * 都道府県とジャンルの選択値を追跡するためのリアクティブな参照を設定し、
 * DOM要素の変更を監視してそれに応じてフィルタキーを更新する。
 * setTimeoutを使用して３秒後にDOM要素が利用可能になるのを待ち、MutationObserverをセットアップして
 * 都道府県とジャンルの選択値が変更された場合に反応する。
 *
 * @type {Ref<string>} prefectureElement - 都道府県の選択値を保持するリアクティブな参照。
 * @type {Ref<string>} genreElement - ジャンルの選択値を保持するリアクティブな参照。
 */
setTimeout(() => {
    // 都道府県とジャンルの選択値を保持するDOM要素への参照を設定
    prefectureElement.value = document.querySelector('div#preferenceId span.truncate');
    genreElement.value = document.querySelector('div#genreId span.truncate');

    // MutationObserverのセットアップ。DOM要素の変更を監視して、フィルタキーを更新
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'characterData' || mutation.type === 'childList') {
                // 変更があった際の処理
                prefectureFilterKey.value = prefectureElement.value.textContent;
                genreFilterKey.value = genreElement.value.textContent;
                // 'なし'を選択した場合はプレースホルダーを設定
                if (prefectureElement.value.textContent === 'なし') {
                    prefectureElement.value.textContent = 'エリア選択...';
                }
                if (genreElement.value.textContent === 'なし') {
                    genreElement.value.textContent = 'カテゴリ選択...';
                }
            }
        });
    });

    // DOM要素の監視を開始
    if (prefectureElement.value) {
        observer.observe(prefectureElement.value, { characterData: true, childList: true });
    }
    if (genreElement.value) {
        observer.observe(genreElement.value, { characterData: true, childList: true });
    }
}, 3000);

/**
 * 各フィルタ条件を保持するリアクティブな参照。
 * 初期値はfalseもしくは空文字列で、ユーザーがフィルタ条件を選択すると更新される。
 *
 * @type {Ref<boolean>} favoriteOnly - お気に入りのみを表示するかどうかを保持するリアクティブな参照
 * @type {Ref<string>} prefectureFilterKey - 都道府県のフィルタ条件を保持するリアクティブな参照
 * @type {Ref<string>} genreFilterKey - ジャンルのフィルタ条件を保持するリアクティブな参照
 * @type {Ref<string>} restaurantNameFilterKey - 店舗名のフィルタ条件を保持するリアクティブな参照
 */
const favoriteOnly = ref(false);
const prefectureFilterKey = ref('');
const genreFilterKey = ref('');
const restaurantNameFilterKey = ref('');

/**
 * フィルタ関数。
 * フィルタ条件に基づいて、店舗情報をフィルタリングする。
 *
 * @param {Array} restaurants - フィルタリングする店舗情報の配列
 * @returns {Array} - フィルタリングされた店舗情報の配列
 */
const filterByFavorite = (restaurants) => favoriteOnly.value ? restaurants.filter(restaurant => restaurant.liked) : restaurants;
const filterByPrefecture = (restaurants) => prefectureFilterKey.value && prefectureFilterKey.value !== 'エリア選択...' ? restaurants.filter(restaurant => restaurant.prefecture.name.includes(prefectureFilterKey.value)) : restaurants;
const filterByGenre = (restaurants) => genreFilterKey.value && genreFilterKey.value !== 'カテゴリ選択...' ? restaurants.filter(restaurant => restaurant.genre.name.includes(genreFilterKey.value)) : restaurants;
const filterByName = (restaurants) => restaurantNameFilterKey.value ? restaurants.filter(restaurant => restaurant.name.toLowerCase().includes(restaurantNameFilterKey.value.toLowerCase())) : restaurants;

/**
 * フィルタされた店舗情報を保持するリアクティブな参照。
 * フィルタ条件に基づいて、filteredRestaurantsを更新する。
 *
 * @type {Ref<Array>} - フィルタされた店舗情報の配列
 */
const filteredRestaurants = computed(() => {
    let filtered = reactiveRestaurants.value;
    // フィルタ条件（お気に入り、都道府県、カテゴリ）に基づいて、店舗情報をフィルタリング
    // もしフィルタ結果が空の場合は、早期リターンする
    if (!(filtered = filterByFavorite(filtered)).length) return filtered;
    if (!(filtered = filterByPrefecture(filtered)).length) return filtered;
    if (!(filtered = filterByGenre(filtered)).length) return filtered;
    // 店舗名のフィルタ条件に基づいて、店舗情報をフィルタリングし、フィルタ結果を返す
    // もしフィルタ結果が空の場合は、フィルタ条件（お気に入り、都道府県、カテゴリ）でフィルタリングした結果を返す
    return (!filterByName(filtered).length) ? filtered : filterByName(filtered);
});

/**
 * 画像が見つからない場合に表示されるデフォルトの画像パス。
 * この変数は、指定された画像リソースが存在しない、またはアクセスできない場合に使用される。
 * '/storage/images/notfound.png' への相対パスを指定している。
 *
 * @type {string} - 画像が見つからない場合のデフォルト画像のファイルパス
 */
const notfoundImage = '/images/notfound.png';

/**
 * 選択された店舗の情報を保持するリアクティブな参照。
 * ユーザーが店舗を選択すると、その情報がこの参照に格納される。
 *
 * @type {ref|null} - 選択された店舗のオブジェクト、または選択されていない場合はnull
 */
const selectedRestaurant = ref(null);

/**
 * 店舗が選択された時の処理。
 * 引数として受け取った店舗オブジェクトを、selectedRestaurantに設定する。
 *
 * @param {Object} restaurant - 選択された店舗のオブジェクト
 */
const selectRestaurant = (restaurant) => {
    selectedRestaurant.value = restaurant;
}

/**
 * コンテナのアニメーション状態を管理するリアクティブな参照。
 * 初期値はfalseで、アニメーションが非アクティブを意味する。
 *
 * @type {Ref<boolean>} - アニメーションの有効/無効状態を保持するリアクティブな参照
 */
const containerAnimation = ref(false);

/**
 * 店舗選択をクリアする関数。
 * 選択された店舗情報をリセットし、関連するアニメーションをトリガーする。
 * selectedRestaurantをnullに設定して選択状態をクリアし、コンテナのアニメーション状態をtrueに設定してアニメーションを開始する。
 * 1000ミリ秒（1秒）後に、アニメーション状態をfalseに戻してアニメーションを終了させる。
 */
const clearSelectedRestaurant = () => {
    selectedRestaurant.value = null;
    // アニメーションをトリガー
    containerAnimation.value = true;
    // 1000ミリ秒後にアニメーションをリセット
    setTimeout(() => containerAnimation.value = false, 1000);
}

/**
 * 画像URLが有効かどうかをチェックする関数。
 * URLが非空かつ正しい形式であることを確認する。
 *
 * @param {string} url - チェックする画像のURL
 * @returns {boolean} - URLが有効であればtrue、そうでなければfalse
 */
const isValidImageUrl = (url) => {
    return url && url.length > 0;
}

/**
 * リアクティブな店舗情報の配列。
 * propsから取得した店舗情報に基づいて初期化され、ユーザーの操作に応じて更新される。
 *
 * @type {ref<Array>} - リアクティブな店舗情報の配列
 */
const reactiveRestaurants = ref([]);

/**
 * コンポーネントがマウントされた際に、propsから取得した店舗情報でreactiveRestaurantsを初期化する。
 * 各店舗オブジェクトに 'liked' プロパティを追加し、ユーザーが店舗をお気に入りにしているかどうかを表す。
 * 'liked' プロパティは、ユーザーが店舗をお気に入りにしている場合はtrue、そうでない場合はfalseとなる。
 */
onMounted(() => {
    reactiveRestaurants.value = props.restaurants.map(restaurant => ({
        ...restaurant,
        liked: restaurant.user_attached === 1 ? true : false,
    }));
});

/**
 * 選択された店舗のお気に入り状態を切り替える関数。
 * 選択された店舗のlikedプロパティを切り替え、状態に応じてattachRestaurantまたはdetachRestaurantを呼び出す。
 * restaurant.likedがtrue - attachRestaurantを呼び出し、お気に入り登録
 * restaurant.likedがfalse - detachRestaurantを呼び出し、お気に入り解除
 *
 * @param {Object} restaurant - お気に入り状態を切り替える店舗のオブジェクト
 */
const toggleLike = (restaurant) => {
    restaurant.liked = !restaurant.liked;
    if (restaurant.liked) {
        attachRestaurant(restaurant);
    } else {
        detachRestaurant(restaurant);
    }
}

/**
 * 特定の店舗をユーザーのお気に入りに登録するための関数。
 * Inertia.jsを使用して、サーバーの `restaurants.attach` ルートにPOSTリクエストを送信する。
 *
 * @param {Object} restaurant - お気に入りに追加する店舗のオブジェクト
 *                             必要なプロパティ: `id` - 店舗の一意の識別子
 */
const attachRestaurant = (restaurant) => {
    Inertia.post(route('restaurants.attach', { restaurant: restaurant.id }), {},
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => console.log('attach success'),
        }
    );
}

/**
 * 特定の店舗をユーザーのお気に入りから削除するための関数。
 * Inertia.jsを使用して、サーバーの `restaurants.detach` ルートにDELETEリクエストを送信する。
 *
 * @param {Object} restaurant - お気に入りから削除する店舗のオブジェクト
 *                            必要なプロパティ: `id` - 店舗の一意の識別子
 */
const detachRestaurant = (restaurant) => {
    Inertia.delete(route('restaurants.detach', { restaurant: restaurant.id }),
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => console.log('detach success'),
        }
    );
}
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <template v-if="selectedRestaurant">店舗詳細</template>
                <template v-else>店舗一覧</template>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <section class="text-gray-600 body-font" v-show="!selectedRestaurant" :class="{ 'animate-flash': containerAnimation }">
                            <div class="container px-5 py-4 mx-auto">
                                <!-- search form -->
                                <div class="flex">
                                    <input type="checkbox" v-model="favoriteOnly" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" id="hs-default-checkbox">
                                    <label for="hs-default-checkbox" class="text-sm text-gray-500 ms-3">お気に入り店舗のみを表示する</label>
                                </div>
                                <div class="pb-6 sm:flex justify-end rounded-lg">
                                    <!-- Select -->
                                    <div class="relative" id="preferenceId">
                                        <select id="testSelect" data-hs-select='{
                                                "placeholder": "エリア選択...",
                                                "toggleTag": "<button type=\"button\"></button>",
                                                "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 first:rounded-t-lg last:rounded-b-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-[1] shadow-sm",
                                                "dropdownClasses": "mt-2 z-50 w-full max-h-[300px] p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto",
                                                "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100",
                                                "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500\" xmlns=\"http:.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>"
                                            }' @change="logSelection">
                                            <option value="">choose</option>
                                            <option>なし</option>
                                            <option v-for="prefecture in prefectures" :key="prefecture.id" :value="prefecture.id">{{ prefecture.name }}</option>
                                        </select>
                                        <div class="absolute top-1/2 end-3 -translate-y-1/2">
                                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-gray-500 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m7 15 5 5 5-5" />
                                                <path d="m7 9 5-5 5 5" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative" id="genreId">
                                        <select data-hs-select='{
                                                "placeholder": "カテゴリ選択...",
                                                "toggleTag": "<button type=\"button\"></button>",
                                                "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 first:rounded-t-lg last:rounded-b-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-[1] focus:z-30 shadow-sm id-test",
                                                "dropdownClasses": "mt-2 z-50 w-full max-h-[300px] p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto",
                                                "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100",
                                                "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"flex-shrink-0 w-3.5 h-3.5 text-blue-600 dark:text-blue-500\" xmlns=\"http:.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>"
                                            }' class="hidden">
                                            <option value="">choose</option>
                                            <option>なし</option>
                                            <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                                        </select>
                                        <div class="absolute top-1/2 end-3 -translate-y-1/2">
                                            <svg class="flex-shrink-0 w-3.5 h-3.5 text-gray-500 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m7 15 5 5 5-5" />
                                                <path d="m7 9 5-5 5 5" />
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- input -->
                                    <div class="relative max-w-xs w-full">
                                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
                                            <svg class="flex-shrink-0 w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="11" cy="11" r="8" />
                                                <path d="m21 21-4.3-4.3" />
                                            </svg>
                                        </div>
                                        <input type="text" class="py-3 px-4 ps-11 block w-full border-gray-200 shadow-sm -mt-px -ms-px first:rounded-t-lg last:rounded-b-lg sm:first:rounded-s-lg sm:mt-0 sm:first:ms-0 sm:first:rounded-se-none sm:last:rounded-es-none sm:last:rounded-e-lg text-sm relative focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none before:z-[1]" placeholder="店舗名を入力してください" data-hs-overlay="#hs-pro-dnsm" v-model="restaurantNameFilterKey">
                                    </div>
                                </div>
                                <!-- End search form -->
                                <!-- restaurant notfound area -->
                                <div v-show="filteredRestaurants.length === 0" class="p-8">
                                    <div class="bg-yellow-50 border border-yellow-200 text-sm text-yellow-800 rounded-lg p-4" role="alert">
                                        <div class="flex w-full h-96">
                                            <div class="flex-shrink-0">
                                                <svg class="flex-shrink-0 h-6 w-6 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                                                    <path d="M12 9v4" />
                                                    <path d="M12 17h.01" />
                                                </svg>
                                            </div>
                                            <div class="ms-3">
                                                <h3 class="text-lg font-semibold">
                                                    お気に入り、選択したエリアおよびカテゴリに該当する店舗は見つかりませんでした。
                                                </h3>
                                                <div class="mt-1 text-base text-yellow-700">
                                                    選択条件を変更して再度検索してください。
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- restaurant index area -->
                                <div class="flex flex-wrap -m-4">
                                    <div class="p-4 md:w-1/3" v-for="restaurant in filteredRestaurants" :key="restaurant.id">
                                        <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                            <!-- 条件付きレンダリングで画像を表示 -->
                                            <img v-if="isValidImageUrl(restaurant.restaurant_image)" class="lg:h-48 md:h-36 w-full object-cover object-center" :src="'/storage/images/' + restaurant.restaurant_image" alt="restaurant image">
                                            <!-- 画像が無効の場合は代替画像を表示 -->
                                            <img v-else class="lg:h-48 md:h-36 w-full object-cover object-center" :src="notfoundImage" alt="not found">
                                            <div class="p-6">
                                                <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">CATEGORY</h2>
                                                <h2 class="title-font text-base font-medium text-gray-900 mb-3">{{ restaurant.genre.name }}</h2>
                                                <h1 class="title-font text-2xl font-mono text-gray-900 mb-3">{{ restaurant.name }}</h1>
                                                <p class="leading-relaxed mb-3">{{ restaurant.description }}</p>
                                                <div class="flex items-center flex-wrap">
                                                    <a href="#" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" @click="selectRestaurant(restaurant)">
                                                        詳細
                                                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M5 12h14"></path>
                                                            <path d="M12 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                    <span class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1">
                                                        <button class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4" @click="toggleLike(restaurant)" :class="{ 'bg-red-100 animate-bounce': restaurant.liked }">
                                                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24" :class="{ 'text-red-500 animate-bounce': restaurant.liked }">
                                                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End restaurant index area -->
                            </div>
                        </section>
                        <Detail v-if="selectedRestaurant" v-bind="{ restaurant: selectedRestaurant, errors }" @back="clearSelectedRestaurant" @toggleLike="toggleLike" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
