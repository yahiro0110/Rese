<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const fileInput = ref(null);

const submit = () => {
    const formData = new FormData();
    if (fileInput.value && fileInput.value.files.length > 0) {
        formData.append('file', fileInput.value.files[0]);
        Inertia.post('/upload', formData);
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <input type="file" id="file" name="file" class="form-control" ref="fileInput">
        <button type="submit">アップロード</button>
    </form>
</template>
