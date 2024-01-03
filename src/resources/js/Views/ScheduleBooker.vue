<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { defineEmits, onMounted, ref } from 'vue';

// emit関数の定義
const emit = defineEmits(['closeModal']);

// 現在の日付を取得してフォーマット
const today = new Date();
const formattedDate = today.toISOString().split('T')[0]; // YYYY-MM-DD

const form = useForm({
    date: formattedDate,
    time: '',
    members: '',
});

const timeOptions = ref([]);

// 15分単位の時間オプションを生成する関数
const generateTimeOptions = () => {
    for (let hour = 9; hour < 21; hour++) {
        for (let minute = 0; minute < 60; minute += 15) {
            const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            timeOptions.value.push(time);
        }
    }
};

onMounted(() => {
    generateTimeOptions();
});

const closeModal = () => {
    emit('closeModal');
};
</script>

<template>
    <section id="overlay" class="text-gray-600 body-font relative">
        <div id="content" class="container px-5 py-24">
            <p class="text-right"><button @click='closeModal'>close</button></p>
            <form @submit.prevent="">
                <div class="md:w-2/3 bg-white rounded-lg md:p-8 flex flex-col w-full mt-0 md:mt-5 relative z-10 mx-auto">
                    <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">店舗予約</h2>
                    <p class="leading-relaxed mb-5 text-gray-600">ご希望の日時を選択してください。</p>
                    <div class="relative mb-4">
                        <label for="date" class="leading-7 text-sm text-gray-600">日付</label>
                        <input type="date" id="date" name="date" v-model="form.date" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    </div>
                    <div class="relative mb-4">
                        <label for="time" class="leading-7 text-sm text-gray-600">時間</label>
                        <select id="time" name="time" v-model="form.time" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                            <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                        </select>
                    </div>
                    <div class="relative mb-4">
                        <label for="members" class="leading-7 text-sm text-gray-600">人数</label>
                        <input type="number" id="members" name="members" v-model="form.members" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" min="0" max="20">
                    </div>
                    <button class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg mt-10">予約する</button>
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
    height: 50%;
    width: 50%;
    padding: 1em;
    background: #fff;
}
</style>
