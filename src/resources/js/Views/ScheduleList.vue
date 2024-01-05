<script setup>
/**
 * @requires useForm - Inertia.jsのフォームハンドリング機能を提供し、フォームの状態管理や送信時の処理を容易にする
 * @requires computed - Vueコンポーネントの算出プロパティを定義するために使用
 * @requires defineEmits - Vueコンポーネントのイベントを定義するために使用
 * @requires onMounted - Vueコンポーネントがマウントされた後に実行する処理を定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires InputError - フォーム入力エラーを表示するためのコンポーネント
 */
import { defineEmits } from 'vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Array} schedules - 予約情報を含むオブジェクトのコレクション
 */
defineProps({
    restaurantName: String,
    schedules: Array
});

/**
 * 時間のフォーマットを変換する関数。
 * 例えば、引数に'09:00:00'を渡すと、'09:00'が返される。
 *
 * @param {string} timeStr - 時間の文字列
 * @returns {string} - フォーマット変換後の時間の文字列
 */
const formatTime = (timeStr) => {
    const [hours, minutes] = timeStr.split(':');
    return `${hours}:${minutes}`;
};

/**
 * イベントを発火させるためのemit関数を定義。
 * @property {Function} closeModal - モーダルウィンドウを閉じる関数
 */
const emit = defineEmits(['closeModal']);

/**
 * モーダルウィンドウを閉じるイベントを発火させる関数。
 */
const closeModal = () => {
    emit('closeModal');
};
</script>

<template>
    <section id="overlay" class="text-gray-600 body-font relative">
        <div id="content" class="container px-5 py-24">
            <p class="text-right"><button @click='closeModal'>close</button></p>
            <div class="container px-5 py-8 mx-auto">
                <!-- 予約一覧 -->
                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                    <h2 class="text-gray-900 text-lg mb-5 font-medium title-font">{{ restaurantName }}の予約状況</h2>
                    <table class="table-auto w-full text-left whitespace-no-wrap">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">お名前</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">日付</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">時間</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">人数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="schedule in schedules" :key="schedule.id">
                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.user.name }}</td>
                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.date }}</td>
                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ formatTime(schedule.time) }}</td>
                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.members }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
    height: 50%;
    width: 50%;
    padding: 1em;
    background: #fff;
}
</style>
