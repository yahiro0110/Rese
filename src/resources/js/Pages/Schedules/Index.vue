<script setup>
/**
 * @requires AuthenticatedLayout - 認証済みユーザ用のレイアウトコンポーネント
 * @requires Head - Vueコンポーネント内でページのタイトルやメタデータを管理するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires Inertia - @inertiajs/inertiaパッケージからインポート。SPA(Single Page Application)のような体験を提供するための
 *                     クライアントサイドのページ遷移やサーバーとのデータ送受信を行うライブラリの主要オブジェクト
 * @requires Pagination - ページネーションを表示するためのVueコンポーネント
 * @requires FlashMessage - フラッシュメッセージを表示するためのVueコンポーネント
 * @requires HeaderMessage - ページヘッダーにメッセージを表示するためのVueコンポーネント
 * @requires ScheduleBooker - 店舗予約フォームを表示するためのVueコンポーネント
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import Pagination from '@/Components/Pagination.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import HeaderMessage from '@/Components/HeaderMessage.vue';
import ScheduleBooker from '@/Views/ScheduleBooker.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - フォーム入力時のエラーメッセージを含むオブジェクト
 * @property {Object} schedules - 店舗の予約情報を含むオブジェクトの配列
 * @property {Object} message - DB処理に関するメッセージの内容やステータス（成功、警告など）を含むオブジェクト
 */
defineProps({
    errors: Object,
    schedules: Object,
    message: Object,
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
 * モーダルウィンドウの表示状態を管理するリアクティブな参照。
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
 * 選択された店舗の情報を保持するリアクティブな参照。
 * ユーザーが店舗を選択すると、その情報がこの参照に格納される。
 *
 * @type {ref|null} - 選択された店舗のオブジェクト、または選択されていない場合はnull
 */
const selectedSchedule = ref(null);

/**
 * 特定の予約情報が選択された時の処理。
 * 引数として受け取った予約情報のオブジェクトをselectedScheduleに設定し、モーダルウィンドウを開く。
 *
 * @param {Object} Schedule - 選択された予約情報のオブジェクト
 */
const selectSchedule = (Schedule) => {
    selectedSchedule.value = Schedule;
    openModal();
}

/**
 * 指定されたIDの予約情報を削除するための関数。
 * Inertia.jsのdeleteメソッドを使用してサーバーに削除リクエストを送信する。
 * 削除の確認ダイアログが表示され、ユーザーが確認した場合のみ削除処理が実行される。
 *
 * @param {number} id - 削除する予約情報の一意識別子
 */
const deleteSchedule = (id) => {
    Inertia.delete(route('schedules.destroy', { schedule: id }), {
        onBefore: () => confirm('予約情報を削除しますか？'),
    });
};
</script>

<template>
    <Head title="予約一覧" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">予約一覧</h2>
        </template>

        <div :class="{ 'animate-shake-horizontal': $page.props.flash.status === 'warning' }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        <HeaderMessage :message="message" />
                        <section class="text-gray-600 body-font">
                            <ScheduleBooker v-if="showContent" v-bind="{ restaurantId: null, schedule: selectedSchedule, errors }" @closeModal="closeModal" />
                            <div class="container px-5 py-8 mx-auto">
                                <!-- 予約一覧 -->
                                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                                    <table class="table-auto w-full text-left whitespace-no-wrap">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">日付</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">時間</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">人数</th>
                                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">店舗名</th>
                                                <th class="py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"></th>
                                                <th class="py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="schedule in schedules.data" :key="schedule.id">
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.date }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ formatTime(schedule.time) }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.members }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.restaurant.name }}</td>
                                                <td class="border-b-2 border-gray-200 py-3">
                                                    <button type="button" @click="selectSchedule(schedule)" class="flex text-white bg-indigo-500 border-0 p-2 -mx-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">変更</button>
                                                </td>
                                                <td class="border-b-2 border-gray-200 py-3">
                                                    <button type="button" @click="deleteSchedule(schedule.id)" class="flex text-white bg-red-500 border-0 p-2 focus:outline-none hover:bg-red-600 rounded text-xs">削除</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <Pagination class="flex justify-center mt-6" :links="schedules.links"></Pagination>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
