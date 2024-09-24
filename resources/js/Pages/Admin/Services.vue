<script setup>
import {Head} from "@inertiajs/vue3";
import {ref} from "vue";
import CategoryModal from "@/Components/CategoryModal.vue";
import ServiceModal from "@/Components/ServiceModal.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    services: Array,
    categories: Array,
})

const categories = ref([...props.categories])
const services = ref([...props.services])

const showCategoryModal = ref(false)
const showServiceModal = ref(false)

const editCategory = ref(null)
const editService = ref(null)

const deleteCategory = ref(false)
const deleteService = ref(false)

</script>

<template>
    <Head title="Услуги"/>

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 sm:text-sm lg:px-8">
                <div class="flex flex-col gap-5">
                    <!--                    <div class="bg-white shadow-sm flex gap-5 h-[50px] px-10 text-sm font-medium">
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
                                        </div>-->
                    <CategoryModal v-model:categories="categories"
                                   v-model:delete="deleteCategory"
                                   :show="showCategoryModal"
                                   :category="editCategory"
                                   @close="showCategoryModal = false"/>

                    <ServiceModal v-model:services="services"
                                  v-model:delete="deleteService"
                                  :show="showServiceModal"
                                  :service="editService"
                                  :categories="categories"
                                  @close="showServiceModal = false"/>

                    <div class="bg-white p-6 text-gray-900">
                        <div class="flex gap-3 items-center mb-3">
                            <h1 class="text-2xl font-semibold">Категории услуг</h1>
                            <div @click="editCategory = null; showCategoryModal = true;"
                                 class="cursor-pointer py-2 px-4 bg-indigo-500 flex items-center gap-2 rounded-full text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Добавить
                            </div>
                        </div>
                        <div v-if="!categories || categories.length === 0">
                            Вы еще не добавили категории услуги
                        </div>
                        <table v-else class="table-auto w-full border rounded-lg">
                            <thead>
                            <tr class="bg-gray-100">
                                <th class="text-left p-3">Название</th>
                                <th class="text-center p-3">Изменить</th>
                                <th class="text-center p-3">Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="border" v-for="category in categories" :key="category.id">
                                <td class="p-3">{{ category.name }}</td>
                                <td class="text-center">
                                    <div class="flex justify-center">
                                        <svg
                                            @click="
                                                    editCategory = category;
                                                    showCategoryModal = true;
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
                                    <div
                                        @click="
                                                editCategory = category;
                                                deleteCategory = true;
                                                showCategoryModal = true;
                                            "
                                        class="flex justify-center">
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

                    <div class="shadow-sm bg-white p-6 text-gray-900">
                        <div class="flex gap-3 items-center mb-3">
                            <h1 class="text-2xl font-semibold">Услуги</h1>
                            <div @click="editService = null; showServiceModal = true;"
                                 class="cursor-pointer py-2 px-4 bg-indigo-500 flex items-center gap-2 rounded-full text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Добавить
                            </div>
                        </div>
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
                                <td class="p-3">{{ service.name }}</td>
                                <td class="text-center">{{ service.price }}</td>
                                <td class="text-center">{{ service.category?.name ?? 'Без категории' }}</td>
                                <td class="text-center">
                                    <div class="flex justify-center">
                                        <svg @click="editService = service; showServiceModal = true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor"
                                             class="size-7 cursor-pointer text-blue-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div @click="
                                        editService = service;
                                        deleteService = true;
                                        showServiceModal = true;
                                    " class="flex justify-center">
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
    </AuthenticatedLayout>
</template>
