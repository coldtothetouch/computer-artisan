<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {ref} from "vue";

const props = defineProps({
    appointments: Array,
    appointmentStatuses: Array,
})

const appointments = ref([...props.appointments])
const selectedStatus = ref('any')

function whereStatus() {
    appointments.value = [...props.appointments]

    if (selectedStatus.value === 'any') {
        return
    }

    appointments.value = appointments.value.filter(a => a.status === selectedStatus.value);
}

const form = useForm({
    status: '',
})

function updateAppointmentStatus(appointment, status) {
    axios.put(route('admin.appointments.update', appointment.id), {status: status})
        .then(() => {
            const record = appointments.value.find(a => a.id === appointment.id);
            if (record) {
                record.status = status;
            }
            whereStatus()
        })
}

</script>

<template>
    <Head title="Заявки"/>

    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white overflow-hidden shadow-sm">
                    <div class="flex gap-3 items-center mb-3">
                        <h1 class="text-2xl font-semibold">Мои заявки</h1>
                        <div @click="selectedStatus = 'any'; whereStatus()"
                             :class="selectedStatus === 'any' ? 'outline outline-offset-2 outline-2 outline-indigo-500' : ''"
                             class="cursor-pointer py-1 px-4 bg-indigo-500 flex items-center gap-2 rounded-full text-white">
                            Все
                        </div>
                        <div v-for="status in appointmentStatuses" @click="selectedStatus = status; whereStatus()"
                             :class="[
                                        status === 'В обработке' ? 'bg-amber-500' : '',
                                        status === 'Завершена' ? 'bg-green-500' : '',
                                        status === 'Отменена' ? 'bg-red-500' : '',
                                        selectedStatus === status ? 'outline outline-offset-2 outline-2 outline-indigo-500' : ''
                                    ]"
                             class="cursor-pointer py-1 px-4 bg-amber-500 flex items-center gap-2 rounded-full text-white">
                            {{ status }}
                        </div>
                    </div>
                    <table v-if="appointments.length !== 0" class="table-auto w-full border rounded-lg ">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left p-3">Дата</th>
                            <th class="text-left p-3">Время</th>
                            <th class="text-left p-3">Статус</th>
                            <th class="text-center p-3">Телефон</th>
                            <th class="text-center p-3">Услуга</th>
                            <th class="text-center p-3">Имя</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="border" v-for="appointment in appointments">
                            <td class="p-3">{{ appointment.date }}</td>
                            <td class="p-3">{{ appointment.time }}</td>
                            <td class="p-3">
                                <select v-model="appointment.status"
                                        @change="updateAppointmentStatus(appointment, $event.target.value)" :class="[
                                        appointment.status === 'В обработке' ? 'bg-amber-500' : '',
                                        appointment.status === 'Завершена' ? 'bg-green-500' : '',
                                        appointment.status === 'Отменена' ? 'bg-red-500' : '',
                                    ]" class="rounded-full text-white border-none cursor-pointer">
                                    <option class="p-1 text-white rounded-full cursor-pointer"
                                    > {{ appointment.status }}
                                    </option>
                                    <option :key="status" :value="status"
                                            v-for="status in appointmentStatuses.filter(s => s !== appointment.status)"
                                            :class="[
                                                status === 'В обработке' ? 'bg-amber-500' : '',
                                                status === 'Завершена' ? 'bg-green-500' : '',
                                                status === 'Отменена' ? 'bg-red-500' : '',
                                            ]"
                                            class="p-1 text-white rounded-full cursor-pointer">
                                        {{ status }}
                                    </option>
                                </select>
                            </td>
                            <td class="text-center">{{ appointment.phone_number }}</td>
                            <td class="text-center">{{ appointment.service }}</td>
                            <td class="text-center">{{ appointment.client_name }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-else class="text-gray-900">У вас еще нет заявок</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
