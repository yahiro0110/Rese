<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import Pagination from '@/Components/Pagination.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import HeaderMessage from '@/Components/HeaderMessage.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} users - ユーザ情報を含むオブジェクト。各ユーザは一意のID、名前、メールアドレスなどの情報を持つ。
 * @property {Array} roles - 利用可能なユーザロールの配列。各ロールは一意の名前と説明を持つ。
 * @property {Object} flash - フラッシュメッセージに関するデータ。メッセージの内容やステータス（成功、警告など）を含む。
 */
defineProps({
    schedules: Object,
    message: Object,
});

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
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.time }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.members }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ schedule.restaurant.name }}</td>
                                                <td class="border-b-2 border-gray-200 py-3">
                                                    <Link as="button" :href="route('schedules.show', { schedule: schedule.id })" class="flex text-white bg-indigo-500 border-0 p-2 -mx-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">変更</Link>
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
