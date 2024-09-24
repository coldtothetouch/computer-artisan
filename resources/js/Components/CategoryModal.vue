<script setup>
import Modal from "./Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {watch} from "vue";

const props = defineProps({
    categories: Object,
    delete: {
        type: Boolean,
        default: false,
    },
    show: {
        type: Boolean,
        default: false,
    },
    category: {
        type: Object,
        default: null
    }
})

const form = useForm({
    name: '',
})

watch(() => props.category, (category) => {
    form.name = category?.name ?? '';
}, {immediate: true});

const emit = defineEmits(['close', 'update:delete', 'update:categories'])

function close() {
    emit('close')
}

function storeCategory() {
    axios.post(route('admin.categories.store'), form)
        .then(({data}) => {
            props.categories.push(data.category)
            form.reset()
            close()
        })
}

function updateCategory(category) {
    axios.put(route('admin.categories.update', category), form)
        .then(({data}) => {
            emit('update:categories', props.categories.map((c) => {
                return c.id === data.category.id ? data.category : c
            }))
            form.reset()
            close()
        })
}

function deleteCategory(category) {
    axios.delete(route('admin.categories.destroy', category))
        .then(() => {
            emit('update:categories', props.categories.filter((c) => c.id !== category.id))
            emit('update:delete', false)
            close()
        })

}

</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-5 max-w-2xl">
            <div v-if="props.delete">
                <h1 v-if="category" class="text-2xl font-semibold mb-3">Удалить категорию</h1>
                <p class="mb-3">Вы действительно хотите удалить категорию "{{ category?.name }}"?</p>
                <div class="flex gap-3">
                    <button @click="$emit('update:delete', false); close()"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Отмена
                    </button>
                    <button @click="deleteCategory(category)"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Удалить
                    </button>
                </div>
            </div>
            <div v-else>
                <h1 v-if="category" class="text-2xl font-semibold mb-3">Изменить категорию</h1>
                <h1 v-else class="text-2xl font-semibold mb-3">Добавить категорию</h1>
                <div class="flex flex-col gap-2">
                    <label for="category_name">Название категории</label>
                    <input v-model="form.name" id="category_name"
                           class="border-gray-400 rounded-lg" type="text"
                           placeholder="Введите текст...">
                    <button v-if="category" @click="updateCategory(category)"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Изменить
                    </button>
                    <button v-else @click="storeCategory"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>
