<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>
    <Head title="Время записи" />

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Вы еще не добавили время записи</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
