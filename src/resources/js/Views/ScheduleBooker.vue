<script setup>
/**
 * @requires useForm - Inertia.jsのフォームハンドリング機能を提供し、フォームの状態管理や送信時の処理を容易にする
 * @requires computed - Vueコンポーネントの算出プロパティを定義するために使用
 * @requires defineEmits - Vueコンポーネントのイベントを定義するために使用
 * @requires onMounted - Vueコンポーネントがマウントされた後に実行する処理を定義するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires InputError - フォーム入力エラーを表示するためのコンポーネント
 */
import { useForm } from '@inertiajs/inertia-vue3';
import { computed, defineEmits, onMounted, ref } from 'vue';
import InputError from '@/Components/InputError.vue';

/**
 * コンポーネントのプロパティ定義。
 *
 * @property {Object} errors - エラーメッセージを含むオブジェクト
 * @property {Object} restaurantId - 選択された店舗ID
 * @property {Object} schedule - 予約情報を含むオブジェクト
 */
const props = defineProps({
    errors: Object,
    restaurantId: Number,
    schedule: Object,
});

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

/**
 * 店舗予約フォームのデータモデル。
 *
 * @type {Object} form - 店舗の予約に必要なデータを保持するフォームオブジェクト
 * @property {string} date - 予約日
 * @property {string} time - 予約時間（09:00~20:45）
 * @property {string} members - 予約人数(1~20)
 * @property {number} restaurant_id - 予約店舗ID
 */
const form = ref(useForm({
    date: '',
    time: '',
    members: '',
    restaurant_id: props.restaurantId,
}));

/**
 * コンポーネントがマウントされた後に実行される処理。
 */
onMounted(() => {
    initializeForm();
});

/**
 * 現在の日付を取得する変数。
 * Dateオブジェクトを使用して現在の日付と時刻の情報を取得する。
 *
 * @type {Date}
 */
const today = new Date();

/**
 * 現在の日付をISO 8601形式の文字列に変換し、日付部分のみをYYYY-MM-DD形式で取得する変数。
 * toISOStringメソッドは日付をISO形式の文字列 「 YYY-MM-DDTHH:mm:ss.sssZ 」 に変換し、
 * splitメソッドで日付部分YYYY-MM-DDのみを切り出す。
 *
 * @type {string}
 */
const formattedDate = today.toISOString().split('T')[0];

/**
 * コンポーネントのマウント時にフォームを初期化する関数。
 * 15分単位の時間オプションを生成し、propsから渡されるscheduleオブジェクト（存在する場合）に基づいて
 * フォームのdate、time、membersの各フィールドを初期設定する。
 */
const initializeForm = () => {
    generateTimeOptions();

    const defaultTime = timeOptions.value.length > 0 ? timeOptions.value[0] : '';

    // props.schedule.timeをHH:mm形式に変換
    const scheduleTimeFormatted = props.schedule?.time
        ? props.schedule.time.substring(0, 5)
        : null;

    const timeValue = scheduleTimeFormatted && timeOptions.value.includes(scheduleTimeFormatted)
        ? scheduleTimeFormatted
        : defaultTime;

    // formの値を更新
    form.value = {
        ...form.value,
        date: props.schedule?.date || formattedDate,
        time: timeValue,
        members: props.schedule?.members || '',
    };
};

/**
 * 15分単位で時間オプションを生成するためのリアクティブな参照。
 */
const timeOptions = ref([]);

/**
 * 15分単位の時間オプションを生成する関数。
 * 9時から21時までの間で、15分単位で時間オプションを生成する。
 */
const generateTimeOptions = () => {
    for (let hour = 9; hour < 21; hour++) {
        for (let minute = 0; minute < 60; minute += 15) {
            const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            timeOptions.value.push(time);
        }
    }
};

/**
 * 予約ボタンのラベルを算出する算出プロパティ。
 * restaurantIdがnullであれば「予約を変更する」、そうでなければ「予約する」を返す。
 */
const buttonName = computed(() => {
    return props.restaurantId === null ? '予約を変更する' : '予約する';
});

/**
 * 新規予約または既存予約の更新を判断して実行する関数。
 * restaurantIdがnullの場合は「既存予約の更新」を、そうでない場合は「新規予約」を実行する。
 */
const handleSubmit = () => {
    if (props.restaurantId === null) {
        updateSchedule(props.schedule.id);
    } else {
        storeSchedule();
    }
};

/**
 * 新規予約を登録する関数。
 * フォームデータを指定したエンドポイントにPOSTリクエストとして送信し、
 * 成功した場合にモーダルウィンドウを閉じる。
 */
const storeSchedule = () => {
    form.value.post(route('schedules.store'), {
        onSuccess: () => closeModal(),
    });
};

/**
 * 既存の予約情報を更新する関数。
 * フォームデータを指定したエンドポイントにPUTリクエストとして送信し、
 * 成功した場合にモーダルウィンドウを閉じる。
 */
const updateSchedule = (id) => {
    form.value.put(route('schedules.update', { schedule: id }), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <section id="overlay" class="text-gray-600 body-font relative">
        <div id="content" class="container px-5 py-24">
            <p class="text-right"><button @click='closeModal'>close</button></p>
            <form @submit.prevent="handleSubmit">
                <div class="md:w-2/3 bg-white rounded-lg md:p-8 flex flex-col w-full mt-0 md:mt-5 relative z-10 mx-auto">
                    <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">店舗予約</h2>
                    <p class="leading-relaxed mb-5 text-gray-600">ご希望の日時を選択してください。</p>
                    <div class="relative mb-4">
                        <label for="date" class="leading-7 text-sm text-gray-600">日付</label>
                        <input type="date" id="date" name="date" v-model="form.date" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        <InputError class="p-1" :message="errors.date" />
                    </div>
                    <div class="relative mb-4">
                        <label for="time" class="leading-7 text-sm text-gray-600">時間</label>
                        <select id="time" name="time" v-model="form.time" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                            <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                        </select>
                    </div>
                    <div class="relative mb-4">
                        <label for="members" class="leading-7 text-sm text-gray-600">人数</label>
                        <input type="number" id="members" name="members" v-model="form.members" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" min="1" max="20">
                        <InputError class="p-1" :message="errors.members" />
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
    height: 50%;
    width: 50%;
    padding: 1em;
    background: #fff;
}
</style>
