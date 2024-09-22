<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";

const tab = ref('index')

const props = defineProps({
    services: Array,
    categories: Array,
})

const editCategoryId = ref(0)
const editServiceId = ref(0)

const categories = ref([...props.categories])
const services = ref([...props.services])

const updateCategoryForm = useForm({
    name: '',
})

const storeCategoryForm = useForm({
    name: '',
})

const storeServiceForm = useForm({
    service_category_id: null,
    name: '',
    price: 0,
})

const updateServiceForm = useForm({
    service_category_id: null,
    name: '',
    price: 0,
})

function storeCategory() {
    axios.post(route('admin.categories.store'), storeCategoryForm)
        .then(({data}) => {
            categories.value.push(data.category)
            storeCategoryForm.reset()
        })
}

function updateCategory(category) {
    axios.put(route('admin.categories.update', category), updateCategoryForm)
        .then(({data}) => {
            categories.value = categories.value.map((c) => {
                return c.id === data.category.id ? data.category : c
            })
            updateCategoryForm.reset()
        })
}

function deleteCategory(category) {
    axios.delete(route('admin.categories.destroy', category))
        .then(() => categories.value = categories.value.filter((c) => {
            return c.id !== category
        }))
}

function storeService() {
    axios.post(route('admin.services.store'), storeServiceForm)
        .then(({data}) => {
            services.value.push(data.service)
            storeServiceForm.reset()
        })
}

function deleteService(service) {
    axios.delete(route('admin.services.destroy', service))
        .then(() => services.value = services.value.filter((s) => {
            return s.id !== service
        }))
}

</script>

<template>
    <Head title="Услуги"/>

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden flex flex-col gap-5">
                    <div class="bg-white shadow-sm flex gap-5 h-[50px] px-10 text-sm font-medium">
                        <div @click="tab = 'index'"
                             :class="tab === 'index' ? 'border-indigo-400 text-gray-900 border-opacity-100' : 'hover:border-opacity-100 hover:border-gray-300 border-transparent'"
                             class="border-b-2 transition duration-150 ease-in-out flex items-center justify-center hover:text-gray-900 hover:border-b-2 border-gray-300 text-gray-500 cursor-pointer">
                            Мои услуги
                        </div>
                        <div @click="tab = 'categories'"
                             :class="tab === 'categories' ? 'border-indigo-400 text-gray-900 border-opacity-100' : 'hover:border-opacity-100 hover:border-gray-300 border-transparent'"
                             class="border-b-2 transition duration-150 ease-in-out flex items-center justify-center hover:text-gray-900 hover:border-b-2 border-gray-300 text-gray-500 cursor-pointer">
                            Категории услуг
                        </div>
                    </div>

                    <div v-if="tab === 'index'" class="flex flex-col gap-5">
                        <div class="shadow-sm bg-white p-6 text-gray-900">
                            <h1 class="text-2xl font-semibold mb-3">Добавить услугу</h1>
                            <div class="flex flex-col gap-5 max-w-sm">
                                <div class="flex flex-col gap-2">
                                    <label for="category">Категория</label>
                                    <select v-model="storeServiceForm.service_category_id" id="category"
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
                                    <input v-model="storeServiceForm.name" id="name" class="border-gray-400 rounded-lg"
                                           type="text"
                                           placeholder="Например, замена видеокарты">
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="price">Стоимость услуги (в руб.)</label>
                                    <input v-model="storeServiceForm.price" id="price"
                                           class="border-gray-400 rounded-lg"
                                           type="number" placeholder="500">
                                </div>
                                <button @click="storeService"
                                        class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                    Добавить
                                </button>
                            </div>
                        </div>
                        <div class="shadow-sm bg-white p-6 text-gray-900">
                            <h1 class="text-2xl font-semibold mb-3">Мои услуги</h1>
                            <div v-if="!services || services.length === 0">
                                Вы еще не добавили услуги
                            </div>
                            <table v-else class="table-auto w-full border rounded-lg ">
                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="text-left p-3">Название</th>
                                    <th class="text-center p-3">Цена</th>
                                    <th class="text-center p-3">Категория</th>
                                    <th class="text-center p-3">Изменить</th>
                                    <th class="text-center p-3">Удалить</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="border" v-for="service in services" :key="service.id">
                                    <td class="text-xl p-3">
                                        <div v-if="editServiceId !== service.id">
                                            {{ service.name }}
                                        </div>
                                        <div v-else>
                                            <input v-model="updateServiceForm.name" type="text" class="w-full">
                                        </div>
                                    </td>
                                    <td class="text-center">{{service.price}}</td>
                                    <td class="text-center">{{service.category?.name ?? 'Без категории'}}</td>
                                    <td class="text-center">
                                        <div class="flex justify-center">
                                            <div v-if="editServiceId === service.id" class="flex gap-5 items-center">
                                                <svg @click="editServiceId = 0" xmlns="http://www.w3.org/2000/svg"
                                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                     stroke="currentColor" class="size-7 cursor-pointer text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18 18 6M6 6l12 12"/>
                                                </svg>
                                                <svg @click="updateCategory(service.id); editServiceId = 0"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor"
                                                     class="size-7 cursor-pointer text-blue-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="m4.5 12.75 6 6 9-13.5"/>
                                                </svg>
                                            </div>
                                            <svg v-else
                                                 @click="
                                                     editServiceId = service.id;
                                                     updateCategoryForm.name = service.name;
                                                     updateCategoryForm.category_id = service.category_id
                                                     updateCategoryForm.price = service.price
                                                "
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor"
                                                 class="size-7 cursor-pointer text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div @click="deleteService(service.id)" class="flex justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor"
                                                 class="size-7 cursor-pointer text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="tab === 'categories'" class="flex flex-col gap-5">
                        <div class="shadow-sm bg-white p-6 text-gray-900">
                            <h1 class="text-2xl font-semibold mb-3">Добавить категорию</h1>
                            <div class="flex flex-col gap-2 max-w-sm">
                                <label for="category_name">Название категории</label>
                                <input v-model="storeCategoryForm.name" id="category_name"
                                       class="border-gray-400 rounded-lg" type="text"
                                       placeholder="Ремонт ноутбуков">
                                <button @click="storeCategory"
                                        class="py-2 flex-row px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                    Добавить
                                </button>
                            </div>
                        </div>

                        <div class="shadow-sm bg-white p-6 text-gray-900">
                            <h1 class="text-2xl font-semibold mb-3">Мои категории</h1>
                            <div v-if="!categories || categories.length === 0">
                                Вы еще не добавили категории услуг
                            </div>
                            <table v-else class="table-auto w-full border rounded-lg ">
                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="text-left p-3">Название</th>
                                    <th class="text-center p-3">Редактировать</th>
                                    <th class="text-center p-3">Удалить</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="border" v-for="category in categories" :key="category.id">
                                    <td class="text-xl p-3">
                                        <div v-if="editCategoryId !== category.id">
                                            {{ category.name }}
                                        </div>
                                        <div v-else>
                                            <input v-model="updateCategoryForm.name" type="text" class="w-full">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center">
                                            <div v-if="editCategoryId === category.id" class="flex gap-5 items-center">
                                                <svg @click="editCategoryId = 0" xmlns="http://www.w3.org/2000/svg"
                                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                     stroke="currentColor" class="size-7 cursor-pointer text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18 18 6M6 6l12 12"/>
                                                </svg>
                                                <svg @click="updateCategory(category.id); editCategoryId = 0"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor"
                                                     class="size-7 cursor-pointer text-blue-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="m4.5 12.75 6 6 9-13.5"/>
                                                </svg>
                                            </div>
                                            <svg v-else
                                                 @click="editCategoryId = category.id; updateCategoryForm.name = category.name"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor"
                                                 class="size-7 cursor-pointer text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div @click="deleteCategory(category.id)" class="flex justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor"
                                                 class="size-7 cursor-pointer text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
