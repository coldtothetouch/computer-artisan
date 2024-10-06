<script setup>
import Modal from "./Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, watch} from "vue";

const props = defineProps({
    reviews: Object,
    delete: {
        type: Boolean,
        default: false,
    },
    show: {
        type: Boolean,
        default: false,
    },
    review: {
        type: Object,
        default: null
    }
})

const form = useForm({
    author_name: props.review?.author_name,
    avatar_path: props.review?.avatar_path,
    body: props.review?.body,
    avatar: null,
})

watch(() => props.review, (review) => {
    form.author_name = review?.author_name ?? '';
    form.avatar_path = review?.avatar_path ?? '';
    form.body = review?.body ?? '';
}, {immediate: true});

const emit = defineEmits(['close', 'update:delete', 'update:reviews'])

function close() {
    emit('update:delete', false);
    emit('close')
}

function storeReview() {
    form.post(route('admin.reviews.store'), {
        onSuccess: () => {
            form.reset()
            close();
        }
    })
}

function updateReview() {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.reviews.update', props.review), {
        onSuccess: () => {
            close();
        }
    })
}

function deleteReview() {
    axios.delete(route('admin.reviews.destroy', props.review))
        .then(() => {
            emit('update:reviews', props.reviews.filter((r) => r.id !== props.review.id))
            emit('update:delete', false)
            close()
        })
}

function deleteImage() {
    form.avatar_path = null;
}

function addPicture(event) {
    const pic = event.target.files[0]
    const reader = new FileReader()
    reader.onload = () => {
        form.avatar_path = reader.result
        form.avatar = pic
    }
    reader.readAsDataURL(pic)
}

const imagePath = computed(() => {
    if (form.avatar_path) {
        return form.avatar_path.startsWith('data:') ? form.avatar_path : `/storage/${form.avatar_path}`
    }
})

</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-4 w-[500px]">
            <div v-if="props.delete">
                <h1 class="text-2xl font-semibold mb-3">Удалить отзыв</h1>
                <p class="mb-3">Вы действительно хотите удалить этот отзыв?</p>
                <div class="flex justify-center gap-3">
                    <button @click="close()"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Отмена
                    </button>
                    <button @click="deleteReview()"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Удалить
                    </button>
                </div>
            </div>
            <div v-else>
                <h1 v-if="review" class="text-2xl font-semibold mb-3">Изменить отзыв</h1>
                <h1 v-else class="text-2xl font-semibold mb-3">Добавить отзыв</h1>
                <div class="flex flex-col gap-2">
                    <label for="author_avatar">Картинка автора</label>

                    <div v-if="form.avatar_path" class="self-start">
                        <div class="relative">
                            <img :src="imagePath" alt="image"
                                 class="object-cover size-[50px] rounded-lg">
                            <svg @click="deleteImage()" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                 class="border border-gray-500 size-[20px] rounded-full absolute -top-[5px] -right-[5px] size-[10px] bg-white cursor-pointer p-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>

                    <div v-else class="border border-gray-500 p-3 rounded-lg">
                        <input @input="addPicture" class="cursor-pointer" id="author_avatar" type="file" alt="image">
                    </div>

                    <label for="author_name">Имя автора</label>
                    <input v-model="form.author_name" id="author_name"
                           class="border-gray-400 rounded-lg" type="text"
                           placeholder="Иван Иванов...">
                    <label for="body">Текст отзыва</label>
                    <textarea v-model="form.body" id="body"
                              class="border-gray-400 rounded-lg" type="text"
                              placeholder="Мастер очень понравился..."/>
                   <div v-if="review" class="flex justify-center gap-2">
                       <button @click="close()"
                                class=" flex-1 py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                           Отменить
                       </button>
                       <button @click="updateReview"
                               class=" flex-1 py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                           Изменить
                       </button>
                   </div>
                    <button v-else @click="storeReview"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>
