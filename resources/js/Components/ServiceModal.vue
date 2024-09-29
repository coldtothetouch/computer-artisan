<script setup>
import Modal from "./Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {watch} from "vue";

const props = defineProps({
    services: Object,
    delete: {
        type: Boolean,
        default: false,
    },
    show: {
        type: Boolean,
        default: false,
    },
    service: {
        type: Object,
        default: null
    },
    categories: {
        type: Object,
        default: null
    }
})

const form = useForm({
    service_category_id: null,
    name: '',
    price: 0,
})

watch(() => props.service, (service) => {
    form.name = service?.name ?? '';
    form.price = service?.price ?? 0;
    form.service_category_id = service?.service_category_id ?? null;
}, {immediate: true});

const emit = defineEmits(['close', 'update:delete', 'update:services'])

function close() {
    emit('close')
}

function storeService() {
    axios.post(route('admin.services.store'), form)
        .then(({data}) => {
            emit('update:services', [...props.services, data.service])
            close()
        })
}

function updateService(service) {
    axios.put(route('admin.services.update', service), form)
        .then(({data}) => {
            emit('update:services', props.services.map((s) => {
                return s.id === data.service.id ? data.service : s
            }))
            form.reset()
            close()
        })
}

function deleteService(service) {
    axios.delete(route('admin.services.destroy', service))
        .then(() => {
            emit('update:services', props.services.filter((s) => s.id !== service.id))
            emit('update:delete', false)
            close()
        })

}

</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-5 w-[350px]">
            <div v-if="props.delete">
                <h1 v-if="service" class="text-2xl font-semibold mb-3">Удалить услугу</h1>
                <p class="mb-3">Вы действительно хотите удалить услугу "{{ service?.name }}"?</p>
                <div class="flex gap-3">
                    <button @click="$emit('update:delete', false); close()"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Отмена
                    </button>
                    <button @click="deleteService(service)"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Удалить
                    </button>
                </div>
            </div>
            <div v-else>
                <h1 v-if="service" class="text-2xl font-semibold mb-3">Изменить услугу</h1>
                <h1 v-else class="text-2xl font-semibold mb-3">Добавить услугу</h1>
                <div class="flex flex-col gap-5 max-w-sm">
                    <div class="flex flex-col gap-2">
                        <label for="category">Категория</label>
                        <select v-model="form.service_category_id" id="category"
                                class="rounded-lg border-gray-400">
                            <option :value="null">Без категории</option>
                            <option v-for="category in categories" :value="category.id">{{
                                    category.name
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="name">Название услуги</label>
                        <input v-model="form.name" id="name" class="border-gray-400 rounded-lg"
                               type="text"
                               placeholder="Например, замена видеокарты">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="price">Стоимость услуги (в руб.)</label>
                        <input v-model="form.price" id="price"
                               class="border-gray-400 rounded-lg"
                               type="number" placeholder="500">
                    </div>
                    <button v-if="service" @click="updateService(service)"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Изменить
                    </button>
                    <button v-else @click="storeService"
                            class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>
