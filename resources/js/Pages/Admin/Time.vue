<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    times: Array,
})

const tab = ref('one');

const editTime = ref(false)

const time = ref('')

const from = ref('');
const to = ref('');
const interval = ref(60);

const times = ref([...props.times || []])
const addedTime = ref([])
const mergeTime = ref(false)

function getValueOfTime(time) {
    time = time.split(':')
    let hours = Number(time[0])
    let minutes = Number(time[1])

    return hours * 60 + minutes
}

function getTimeFromValue(value) {
    let hours = Math.floor(value / 60)
    let minutes = value - hours * 60

    return `${hours < 10 ? `0${hours}` : hours}:${minutes === 0 ? '00' : minutes}`
}

function sortTime(array) {
    return array.sort((a, b) => {
        const [hoursA, minutesA] = a.time ? a.time.split(":").map(Number) : a.split(":").map(Number);
        const [hoursB, minutesB] = b.time ? b.time.split(":").map(Number) : b.split(":").map(Number);

        const totalMinutesA = hoursA * 60 + minutesA;
        const totalMinutesB = hoursB * 60 + minutesB;

        return totalMinutesA - totalMinutesB;
    })
}

function timeNotExist(time) {
    return !addedTime.value.some((t) => time === t)
}

function addTime() {
    if (timeNotExist(time.value)) {
        addedTime.value.push(time.value);
        sortTime(addedTime.value)
    }
}

function addManyTime() {
    let fromValue = getValueOfTime(from.value)
    let toValue = getValueOfTime(to.value)

    for (let i = fromValue; i < toValue; i += interval.value) {
        const time = getTimeFromValue(i)
        if (timeNotExist(time)) {
            addedTime.value.push(time)
        }
    }
    sortTime(addedTime.value)
}

function removeTime(index) {
    addedTime.value.splice(index, 1)
}

function storeTime() {
    axios.post(route('admin.times.store'), {times: addedTime.value})
        .then(({data}) => {
            data.times.forEach(t => times.value.push({id: t.id, time: t.time }))
            addedTime.value = []
            mergeTime.value = false
        })
}

function updateTime() {
    let array = mergeTime.value ? [...addedTime.value, ...times.value.map(o => o.time)] : addedTime.value
    axios.post(route('admin.times.update'), {times: array})
        .then(({data}) => {
            times.value = data.times
            addedTime.value = []
            mergeTime.value = false
        })
}

function deleteTime(time) {
    axios.delete(route('admin.times.destroy', time))
        .then(() => {
            times.value = times.value.filter(t => t.id !== time)
        })
}

</script>

<template>
    <Head title="Время записи"/>

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col gap-5">
                    <div class="bg-white overflow-hidden shadow-sm p-6">
                        <h1 class="text-2xl font-semibold mb-3">Добавить время записи</h1>
                        <div class="border-b flex gap-5 mb-5">
                            <div @click="tab = 'one'"
                                 :class="tab === 'one' ? 'border-b border-indigo-500' : ''"
                                 class="py-3 cursor-pointer -m-[1px]">Добавить одно
                            </div>
                            <div @click="tab = 'many'"
                                 :class="tab === 'many' ? 'border-b border-indigo-500' : ''"
                                 class="py-3 cursor-pointer -m-[1px]">Добавить несколько
                            </div>
                        </div>

                        <div v-if="tab === 'many'" class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <label for="from">С</label>
                                <input class="rounded-lg h-[40px]" v-model="from" id="from" type="time">
                                <label for="to">До</label>
                                <input class="rounded-lg h-[40px]" v-model="to" id="to" type="time">
                            </div>

                            <div class="flex gap-3 items-center">
                                <label for="interval">Каждые</label>
                                <select class="rounded-lg h-[40px]" id="interval" v-model="interval">
                                    <option :value="60">1 час</option>
                                    <option :value="120">2 часа</option>
                                    <option :value="180">3 часа</option>
                                    <option :value="240">4 часа</option>
                                    <option :value="300">5 часов</option>
                                </select>
                            </div>
                            <button @click="addManyTime"
                                    class="py-2 self-start px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                Добавить
                            </button>
                        </div>

                        <div v-if="tab === 'one'" class="flex items-center gap-2">
                            <label for="time">Время</label>
                            <input class="rounded-lg h-[40px]" v-model="time" id="time" type="time">
                            <button @click="time !== '' ? addTime() : ''"
                                    class="py-2 px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                Добавить
                            </button>
                        </div>

                        <div v-if="addedTime.length !== 0" class="mt-5 flex flex-col gap-5">
                            <h2 class="font-semibold text-2xl">Предварительный просмотр</h2>
                            <div @click="mergeTime = !mergeTime" v-if="times.length !== 0" class="flex gap-2 items-center">
                                <Checkbox :checked="mergeTime"/>
                                <label>Объеденить с существующим временем</label>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div
                                    v-for="time in mergeTime ? sortTime([...addedTime, ...props.times.map(obj => obj.time)]) : addedTime"
                                    class="bg-indigo-500 rounded-full text-white pl-4 pr-3 py-2 flex items-center gap-3"
                                >
                                    {{ time }}
                                    <svg @click="removeTime(time)" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                         class="hover:bg-gray-100 hover:bg-opacity-10 cursor-pointer size-6 border rounded-full p-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button v-if="times.length !== 0" @click="updateTime"
                                        class="py-2 self-start px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                    Заменить время на выбранное
                                </button>
                                <button v-if="!mergeTime" @click="storeTime"
                                        class="py-2 self-start px-5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                    Добавить
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="bg-white overflow-hidden shadow-sm p-6">
                        <div class="flex gap-3 items-center mb-3">
                            <h1 class="text-2xl font-semibold">Доступное время записи</h1>
                            <div v-if="times.length !== 0" @click="editTime = !editTime"
                                 class="cursor-pointer py-2 px-4 flex items-center gap-2 rounded-full text-white"
                                 :class="editTime ? 'bg-amber-500' : 'bg-amber-400'"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-5 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                                Изменить
                            </div>
                        </div>
                        <div v-if="!times || times.length === 0" class="text-gray-900">Вы еще не добавили время записи
                        </div>
                        <div v-else class="flex flex-wrap gap-3">
                            <div v-for="time in sortTime(times.map(obj => ({id: obj.id,time: obj.time})))"
                                 class="bg-indigo-500 rounded-full text-white pl-4 pr-3 py-2 flex items-center gap-3"
                            >
                                {{ time.time || time }}
                                <svg v-if="editTime" @click="deleteTime(time.id)" xmlns="http://www.w3.org/2000/svg"
                                     fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="hover:bg-gray-100 hover:bg-opacity-10 cursor-pointer size-6 border rounded-full p-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
