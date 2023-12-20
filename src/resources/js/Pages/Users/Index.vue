<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    users: Object,
    roles: Array,
});


const searchText = sessionStorage.getItem('searchKey');
const search = ref(searchText);

const searchUser = () => {
    sessionStorage.setItem('searchKey', search.value);
    Inertia.get(route('users.index', { search: search.value }));
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
                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-8 mx-auto">
                                <!-- 検索バーとボタン -->
                                <div class="flex pb-5 pl-5 my-4 lg:w-2/3 w-full mx-auto">
                                    <input type="text" class="w-2/3" v-model="search" placeholder="検索ワードを入力してください">
                                    <button class="bg-blue-300 text-white py-2 px-2 mx-2" @click="searchUser">検索</button>
                                </div>
                                <!-- ユーザ一覧 -->
                                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                                    <table class="table-auto w-full text-left whitespace-no-wrap">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                                                    ID</th>
                                                <th
                                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                    名前</th>
                                                <th
                                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                    メールアドレス</th>
                                                <th
                                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="user in users.data" :key="user.id">
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.id }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.name }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">{{ user.email }}</td>
                                                <td class="px-4 border-b-2 border-gray-200 py-3">
                                                    <Link as="button" :href="route('users.show', { user: user.id })"
                                                        class="flex ml-auto text-white bg-indigo-500 border-0 p-2 focus:outline-none hover:bg-indigo-600 rounded text-xs">
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
