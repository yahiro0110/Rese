<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Core as YubinBangoCore } from 'yubinbango-core2';

const props = defineProps({
    restaurant: Object,
    genres: Array,
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

const file = ref(null);

const fetchAddress = () => {
    new YubinBangoCore(String(form.postal), (value) => {
        form.address = value.region + value.locality + value.street;
    })
}

// 郵便番号の検証メソッドを追加
const validatePostal = (event) => {
    // 7桁の数字のみを許可する正規表現
    const regex = /^\d{0,7}$/;
    if (!regex.test(event.target.value)) {
        // 正規表現にマッチしない場合、最後の文字を削除
        form.postal = form.postal.slice(0, -1);
    }
};

const updateRestaurant = (id) => {
    form.post(route('restaurants.formUpdate', { restaurant: id }));
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
                                <div
                                    class="lg:w-3/4 md:w-1/2 bg-gray-300 rounded-lg overflow-hidden sm:mr-10 p-10 flex items-end justify-start relative">
                                    <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map"
                                        marginheight="0" marginwidth="0" scrolling="no"
                                        src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=%C4%B0zmir+(My%20Business%20Name)&ie=UTF8&t=&z=14&iwloc=B&output=embed"
                                        style="filter: grayscale(1) contrast(1.2) opacity(0.4);"></iframe>
                                    <div class="bg-white relative flex flex-wrap py-6 mr-4 rounded shadow-md w-full">
                                        <div class="lg:w-1/2 px-6">
                                            <h2 class="title-font font-semibold text-gray-900 tracking-widest text-xs">
                                                住所</h2>
                                            <p class="mt-1">{{ restaurant.address }}</p>
                                        </div>
                                        <div class="lg:w-1/2 px-6 mt-4 lg:mt-0">
                                            <h2 class="title-font font-semibold text-gray-900 tracking-widest text-xs">
                                                メールアドレス
                                            </h2>
                                            <a class="text-indigo-500 leading-relaxed">{{ restaurant.email }}</a>
                                            <h2 class="title-font font-semibold text-gray-900 tracking-widest text-xs mt-4">
                                                電話番号</h2>
                                            <p class="leading-relaxed">{{ restaurant.tel }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="lg:w-1/2 md:w-1/2 bg-white flex flex-col md:ml-auto w-full md:py-8 mt-8 md:mt-0">
                                    <form @submit.prevent="updateRestaurant(restaurant.id)">
                                        <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">店舗の情報</h2>
                                        <p class="leading-relaxed mb-5 text-sm text-gray-600">店舗の情報を編集することができます。</p>
                                        <div class="relative mb-4">
                                            <label for="genre" class="leading-7 text-sm text-gray-600">ジャンル</label>
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
                                            <label for="name" class="leading-7 text-sm text-gray-600">店舗名</label>
                                            <input type="text" id="name" name="name" v-model="form.name"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="tel" class="leading-7 text-sm text-gray-600">電話番号</label>
                                            <input type="text" id="tel" name="tel" v-model="form.tel"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                            <input type="email" id="email" name="email" v-model="form.email"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="postal" class="leading-7 text-sm text-gray-600">郵便番号</label>
                                            <input type="text" id="postal" name="postal" @input="validatePostal"
                                                @change="fetchAddress" v-model="form.postal"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                maxlength="7">
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="address" class="leading-7 text-sm text-gray-600">住所</label>
                                            <input type="text" id="address" name="address" v-model="form.address"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="description" class="leading-7 text-sm text-gray-600">店舗の説明</label>
                                            <textarea id="description" name="description" v-model="form.description"
                                                class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        </div>
                                        <div class="relative mb-4">
                                            <label for="file" class="leading-7 text-sm text-gray-600">店舗の画像</label>
                                            <input type="file" id="file" name="file" ref="file"
                                                @input="form.file = $event.target.files[0]"
                                                class="w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
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
