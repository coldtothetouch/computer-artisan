<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import ReviewModal from "@/Components/ReviewModal.vue";

const props = defineProps({
    reviews: Array,
})
const reviews = ref([...props.reviews])
const showReviewModal = ref(false);
const deleteReview = ref(false);
const editReview = ref(null);

watch(() => props.reviews, (newReviews) => {
    reviews.value = newReviews
})
</script>

<template>
    <Head title="Отзывы" />

    <AuthenticatedLayout>
        <ReviewModal
            v-model:reviews="reviews"
            v-model:delete="deleteReview"
            :show="showReviewModal"
            :review="editReview"
            @close="showReviewModal = false"
        />
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm p-6">
                    <div class="flex gap-3 items-center mb-3">
                        <h1 class="text-2xl font-semibold">Отзывы</h1>
                        <div @click="editReview = null; showReviewModal = true"
                             class="cursor-pointer py-2 px-4 bg-indigo-500 flex items-center gap-2 rounded-full text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Добавить
                        </div>
                    </div>
                    <div v-if="reviews?.length === 0 || !reviews" class="text-gray-900">Вы еще не добавили отзывы</div>
                    <div v-else class="flex flex-col gap-3">
                        <div v-for="review in reviews" class="border rounded-lg p-4">
                            <div class="flex gap-4">
                                <img class="size-[80px] object-cover rounded-full" :src="'/storage/' + review.avatar_path" alt="avatar">
                                <div class="flex flex-1 gap-5 justify-between items-center">
                                    <div class="flex flex-col gap-1">
                                        <div class="font-semibold">{{review.author_name}}</div>
                                        <div>{{review.body}}</div>
                                    </div>
                                    <div class="flex gap-4">
                                        <svg @click="editReview = { ...review }; showReviewModal = true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cursor-pointer text-blue-500 size-7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <svg @click="deleteReview = true; editReview = review; showReviewModal = true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="cursor-pointer text-red-500 size-7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
