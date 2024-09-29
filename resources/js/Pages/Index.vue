<script setup>
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import Modal from "@/Components/Modal.vue";
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle'
import {onMounted, ref} from "vue";
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import ruLocale from '@fullcalendar/core/locales/ru';
import Notification from "@/Components/Notification.vue";

const user = usePage().props.auth.user

const showModal = ref(false)
const scrollTop = ref(0)

window.addEventListener('scroll', () => {
    scrollTop.value = document.documentElement.scrollTop;
});

onMounted(() => {
    new Swiper('.swiper', {
        loop: true,
        speed: 700,
        slidesPerView: document.body.clientWidth < 1000 ? 1 : 3,
        spaceBetween: 30,
        autoplay: {
            delay: 2000,
        }
    });
})

const props = defineProps({
    reviews: Array,
    categories: Array,
    times: Array,
    appointments: Array,
    services: Array,
})

const today = new Date()
const year = today.getFullYear()
const month = String(today.getMonth() + 1).padStart(2, '0')
const day = String(today.getDate()).padStart(2, '0')
const formattedDate = `${year}-${month}-${day}`;

onMounted(() => {
    document.querySelectorAll('.fc-daygrid-day-number').forEach(function (day) {
        const selectedDay = today.toLocaleDateString('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        if (day.ariaLabel === selectedDay) {
            day.style.backgroundColor = 'white'
            day.style.color = 'black'
        }
    })
})

const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: ruLocale,
    dayHeaders: false,
    aspectRatio: 1,
    dateClick: function (arg) {
        form.date = arg.dateStr
        form.time = ''

        document.querySelectorAll('.fc-daygrid-day-number').forEach(function (day) {
            day.style.backgroundColor = 'transparent';
            day.style.color = 'white'
        });

        arg.dayEl.querySelectorAll('a')[0].style.backgroundColor = 'white'
        arg.dayEl.querySelectorAll('a')[0].style.color = 'black'
    },
}
const isMenuOpen = ref(false)

const selectedCategory = ref(props.categories[0])

const form = useForm({
    phone_number: '',
    client_name: '',
    date: formattedDate,
    time: '',
    service: null,
})

const showNotification = ref(false);

function storeAppointment() {
    form.post(route('appointments.store'), {
        onSuccess: () => {
            form.phone_number = ''
            form.client_name = ''
            form.time = ''
            form.service = ''
        },
        onError: (error) => {
            console.log(error);
        },
        preserveScroll: true,
    })
}

const isBooked = function (time) {
    const dayAppointments = props.appointments.filter((a) => {
        return a.date === form.date
    })

    for (let i = 0; i < dayAppointments.length; i++) {
        if (dayAppointments[i].time === time) {
            return true
        }
    }

    return false
}

</script>

<template>
    <Head title="Главная"/>

    <Notification v-model:show="form.hasErrors" :message="Object.values(form.errors)[0]"/>

    <header
        :class="scrollTop > 15 ? 'opacity-100 visible' : 'opacity-0 invisible'"
        class="transition-[opacity] duration-1000 ease-in-out z-40 fixed top-3 left-6 right-6 text-[#3e3e3e] bg-white bg-opacity-90 rounded-[20px]">
        <div class="flex justify-between items-center px-6 md:px-10 py-3">
            <div class="flex gap-5 text-sm">
                <div>
                    максим фомин
                </div>
                <Link v-if="user" :href="route('admin.appointments.index')">Панель Администратора</Link>
            </div>

            <!-- Бургер-меню для мобильных -->
            <div class="min-[1110px]:hidden flex items-center">
                <button @click="isMenuOpen = !isMenuOpen" class="text-3xl focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <div
                class="hidden min-[1110px]:flex flex-row gap-10 items-center">
                <div class="flex gap-10 py-1 px-8 border border-gray-500 border-opacity-50 rounded-full text-sm">
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#about">обо мне</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#services">цены</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#reviews">отзывы</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#appointment">контакты</a>
                    </div>
                </div>
                <div class="text-2xl text-indigo-500">8 (495) 121 26 80</div>
                <a @click="isMenuOpen = false" href="#appointment"
                   class="border border-gray-500 border-opacity-50 text-indigo-500 rounded-full py-3 px-8 cursor-pointer hover:bg-gray-100">
                    оставить заявку
                </a>
            </div>
        </div>

        <div v-if="isMenuOpen" class="flex flex-col items-center px-6 py-3">
            <div class="flex flex-col gap-5 py-2 px-4 text-xl text-center">
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#about">обо мне</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#services">цены</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#reviews">отзывы</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#appointment">контакты</a>
                </div>
            </div>
            <div class="mt-5 text-xl text-indigo-500 text-center">
                8 (495) 121 26 80
            </div>
            <a @click="isMenuOpen = false" href="#appointment"
               class="mt-5 text-center border border-gray-500 border-opacity-50 text-indigo-500 rounded-full py-3 px-8 cursor-pointer hover:bg-gray-100">
                оставить заявку
            </a>
        </div>
    </header>

    <section id="about"
             class="bg-[url('/img.jpg')] bg-cover bg-opacity-95 my-3 mx-6 rounded-[20px] text-center text-white">
        <div class="flex justify-between bg-white bg-opacity-30 rounded-t-[20px] items-center px-6 md:px-10 py-3">
            <div class="flex items-center gap-5 text-sm font-semibold">
                <div>
                    максим фомин
                </div>
                <div class="flex items-center">
                    <Link v-if="user" :href="route('admin.appointments.index')">Панель Администратора</Link>
                </div>
            </div>

            <!-- Бургер-меню для мобильных -->
            <div class="min-[1110px]:hidden flex items-center">
                <button @click="isMenuOpen = !isMenuOpen" class="text-3xl focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <div
                class="hidden min-[1110px]:flex flex-row gap-10 items-center">
                <div class="flex gap-10 py-1 px-8 border border-gray-100 border-opacity-50 rounded-full text-sm">
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#about">обо мне</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#services">цены</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#reviews">отзывы</a>
                    </div>
                    <div class="font-semibold hover:text-indigo-400 flex items-center">
                        <a @click="isMenuOpen = false" href="#appointment">контакты</a>
                    </div>
                </div>
                <div class="text-2xl text-white">
                    8 (495) 121 26 80
                </div>
                <a @click="isMenuOpen = false" href="#appointment"
                   class="border border-gray-500 border-opacity-50 text-white rounded-full py-3 px-8 cursor-pointer bg-gray-400 hover:bg-gray-100 hover:text-indigo-500">
                    оставить заявку
                </a>
            </div>
        </div>

        <div v-if="isMenuOpen" class="px-6 py-3 flex bg-white bg-opacity-30 rounded-b-[20px] flex-col items-center">
            <div class="flex flex-col gap-5 py-2 px-4 text-xl text-center">
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#about">обо мне</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#services">цены</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#reviews">отзывы</a>
                </div>
                <div class="font-semibold hover:text-indigo-400">
                    <a @click="isMenuOpen = false" href="#appointment">контакты</a>
                </div>
            </div>
            <div class="mt-5 text-3xl font-semibold text-white text-center">
                8 (495) 121 26 80
            </div>
            <div @click="isMenuOpen = false"
                 class="mt-5 text-center max-w-[400px] border border-gray-500 border-opacity-50 text-white rounded-full py-3 px-8 cursor-pointer bg-gray-400 hover:bg-gray-100 hover:text-indigo-500">
                оставить заявку
            </div>
        </div>
        <div class="flex flex-col lg:flex-row p-10 lg:py-[150px] h-full items-center justify-center gap-10">
            <div class="flex flex-col gap-5">
                <img class="max-h-[400px] max-w-[450px] rounded-[30px]" src="master.png" alt="master">
                <a href="#appointment"
                   style="background-image: linear-gradient(0.903turn, rgba(102, 51, 204, 1) 0%, rgba(99, 196, 253, 1) 100%);"
                   class="order-4 py-5 px-10 rounded-full font-semibold text-xl text-nowrap">
                    Оставить заявку
                </a>
            </div>
            <div class="flex flex-col text-4xl lg:text-[80px] items-center lg:items-start">
                <div class="text-left lg:leading-[90px]">Максим Фомин</div>
                <div class="lg:leading-[90px] text-gray-100 text-center lg:text-left">Частный компьютерный мастер в
                    <span class="text-amber-400 border-b-4 border-amber-400">Москве</span>
                </div>
                <div class="text-lg grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 mt-10 max-w-5xl">
                    <div class="px-5 py-3 rounded-[30px] bg-blue-500 flex items-center justify-center">
                        Я - Максим Фомин, компьютерный мастер
                    </div>
                    <div class="px-5 py-3 rounded-[30px] bg-blue-500 flex gap-3 items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-8 lg:size-12">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/>
                        </svg>

                        Даю гарантию 1 год на мои услуги
                    </div>
                    <div class="px-5 py-3 rounded-[30px] bg-blue-500 flex gap-3 items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-8 lg:size-12">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        Использую оригинальные запчасти
                    </div>
                    <div class="px-5 py-3 rounded-[30px] bg-blue-500 flex gap-3 items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-8 lg:size-12">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>

                        Приеду в любой район города
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section v-if="categories.length !== 0 && !categories" id="services"
             class="bg-[#fafafa] py-[100px] px-10 text-center text-white flex flex-1 justify-center items-center">
        <div class="flex flex-col">
            <h1 class="text-3xl text-gray-400 mb-6">Цены на услуги</h1>
            <p class="text-4xl lg:text-[80px] text-black mb-20 lg:leading-[75px]">Всегда <span class="text-indigo-500">прозрачный</span>
                расчет стоимости</p>
            <div class="rounded-full flex self-center border border-[#272727] mb-16">
                <div @click="selectedCategory = categories[0]"
                     :class="selectedCategory === categories[0] ? 'bg-[#272727] text-white' : ''"
                     class="text-black px-7 py-4 flex-1 cursor-pointer rounded-l-full text-nowrap">{{
                        categories[0].name
                    }}
                </div>
                <div @click="selectedCategory = categories[1]"
                     :class="selectedCategory === categories[1] ? 'bg-[#272727] text-white' : ''"
                     class="text-black px-7 py-4 flex-1 cursor-pointer rounded-r-full text-nowrap">{{
                        categories[1].name
                    }}
                </div>
            </div>
            <div v-for="service in selectedCategory?.services" class="flex justify-center">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-3/4 lg:max-w-[1000px]">
                    <div class="border-b border-black border-opacity-10 flex justify-between">
                        <div class="text-[#363636]">{{ service.name }}</div>
                        <div class="text-black font-semibold">от {{ service.price }} руб.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        style="background-image: linear-gradient(0.16turn, rgba(47, 57, 72, 1) 0%, rgba(34, 34, 34, 1) 100%);"
        class="rounded-[30px] px-[100px] py-[60px] m-5 px-[10px] text-white flex flex-1 justify-between items-center">
        <div
            class="flex min-[1260px]:px-[80px] flex-col min-[1260px]:flex-row flex-1 justify-between items-center gap-10">
            <svg class="order-2 min-[1260px]:h-[125px] w-[322px] rotate-[90deg] min-[1260px]:rotate-0 h-[322px]"
                 height="125" width="322" xmlns="http://www.w3.org/2000/svg"
                 viewBox="5827 5119 94 42">
                <path fill="#4990f6" fill-opacity="1" stroke="" stroke-opacity="1" stroke-width="1" id="tSvg4223b067d6"
                      d="M 5900.0011 5120 C 5900.0011 5160 5900.0011 5160 5900.0011 5160 C 5900.0011 5160 5920 5140 5920 5140 C 5920 5140 5900.0011 5120 5900.0011 5120 M 5828.00523 5126 C 5827.99053 5132.9907 5828.01223 5137.9912 5828.00523 5154 C 5828.00523 5154 5829.9951 5153.99 5829.97387 5154 C 5829.95077 5140.0022 5829.99037 5133.9904 5830.00507 5126 C 5828.00523 5126 5828.00523 5126 5828.00523 5126 M 5836.0048 5126 C 5835.9901 5132.9907 5836.0118 5137.9912 5836.0048 5154 C 5836.0048 5154 5837.9946 5153.99 5837.9734 5154 C 5837.9503 5140.0022 5837.99 5133.9904 5838.0046 5126 C 5836.0048 5126 5836.0048 5126 5836.0048 5126 M 5844.0043 5126 C 5843.9896 5132.9907 5844.0113 5137.9912 5844.0043 5154 C 5844.0043 5154 5845.9942 5153.99 5845.973 5154 C 5845.9499 5140.0022 5845.9896 5133.9904 5846.0042 5126 C 5844.0043 5126 5844.0043 5126 5844.0043 5126 M 5852.0039 5126 C 5851.9892 5132.9907 5852.0109 5137.9912 5852.0039 5154 C 5852.0039 5154 5853.9937 5153.99 5853.9725 5154 C 5853.9494 5140.0022 5853.9891 5133.9904 5854.0037 5126 C 5852.0039 5126 5852.0039 5126 5852.0039 5126 M 5860.0034 5126 C 5859.9887 5132.9907 5860.0104 5137.9912 5860.0034 5154 C 5860.0034 5154 5861.9933 5153.99 5861.972 5154 C 5861.9489 5140.0022 5861.9887 5133.9904 5862.0033 5126 C 5860.0034 5126 5860.0034 5126 5860.0034 5126 M 5868.003 5126 C 5867.9883 5132.9907 5868.01 5137.9912 5868.003 5154 C 5868.003 5154 5869.9928 5153.99 5869.9716 5154 C 5869.9485 5140.0022 5869.9881 5133.9904 5870.0028 5126 C 5868.003 5126 5868.003 5126 5868.003 5126 M 5876.0025 5126 C 5875.9878 5132.9907 5876.0095 5137.9912 5876.0025 5154 C 5876.0025 5154 5877.9924 5153.99 5877.9711 5154 C 5877.948 5140.0022 5877.9876 5133.9904 5878.0023 5126 C 5876.0025 5126 5876.0025 5126 5876.0025 5126 M 5884.002 5126 C 5883.9873 5132.9907 5884.009 5137.9912 5884.002 5154 C 5884.002 5154 5885.9919 5153.99 5885.9707 5154 C 5885.9476 5140.0022 5885.9872 5133.9904 5886.0019 5126 C 5884.002 5126 5884.002 5126 5884.002 5126 M 5892.0016 5126 C 5891.9869 5132.9907 5892.0086 5137.9912 5892.0016 5154 C 5892.0016 5154 5893.9915 5153.99 5893.9702 5154 C 5893.9471 5140.0022 5893.9867 5133.9904 5894.0014 5126 C 5892.0016 5126 5892.0016 5126 5892.0016 5126"></path>
            </svg>
            <div class="order-3 font-semibold text-[30px]">
                Оставьте заявку,<br> и я подарю скидку
            </div>
            <div class="flex flex-col order-1">
                <div
                    style="background-image: linear-gradient(0.903turn, rgba(102, 51, 204, 1) 0%, rgba(99, 196, 253, 1) 100%);"
                    class="text-[120px] bg-clip-text text-transparent leading-[120px]">35%
                </div>
                <div class="text-lg text-gray-400 text-center">для новых клиентов</div>
            </div>
            <a href="#appointment"
               style="background-image: linear-gradient(0.903turn, rgba(102, 51, 204, 1) 0%, rgba(99, 196, 253, 1) 100%);"
               class="order-4 py-5 px-10 rounded-full font-semibold text-xl text-nowrap">
                Оставить заявку
            </a>
        </div>
    </section>

    <section v-if="reviews.length !== 0 && reviews" id="reviews"
             class="py-[100px] lg:px-10 text-center text-white flex flex-col flex-1 justify-center items-center">
        <h1 class="text-3xl text-gray-400 mb-3">Отзывы</h1>
        <p class="text-4xl lg:text-[80px] text-black mb-20 lg:leading-[75px]"><span class="text-indigo-500">>5000</span>
            довольных клиентов</p>
        <div class="swiper h-[200px] w-full">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <div v-for="review in props.reviews" class="swiper-slide cursor-pointer">
                    <div
                        class="p-5 text-[#363636] flex flex-1 justify-center items-center">
                        <div class="flex flex-col gap-3">
                            <div class="flex gap-3 items-center">
                                <img class="size-[50px] object-cover rounded-full"
                                     :src="'/storage/' + review.avatar_path" alt="avatar">
                                <div class="text-xl">{{ review.author_name }}</div>
                            </div>
                            <p class="text-left">{{
                                    review.body
                                }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section v-if="times.length !== 0" id="appointment"
             class="py-[100px] px-10 bg-black text-center text-white flex justify-center gap-10">
        <FullCalendar :options="calendarOptions"/>
        <div class="flex flex-col gap-5">

            <div class="flex flex-col">
                <label class="self-start" for="client_name">Как я могу к вам обращаться?</label>
                <input placeholder="Иван" v-model="form.client_name" id="client_name" type="text" class="bg-transparent rounded-lg">
            </div>

            <div class="flex flex-col">
                <label class="self-start" for="client_name">Поделитесь своим номером</label>
                <input v-model="form.phone_number" placeholder="+7 ( . . . )  . . .  . . - . ." class="bg-transparent text-white rounded-lg">
            </div>

            <div class="flex flex-col gap-2">
                <label class="self-start" for="service">Какую услугу я могу Вам оказать?</label>
                <select v-model="form.service" id="service"
                        class="bg-transparent rounded-lg border-gray-400">
                    <option class="bg-transparent" :value="null">Другое (скажу по телефону)</option>
                    <option class="bg-transparent" v-for="service in services" :value="service.name">{{
                            service.name
                        }}
                    </option>
                </select>
            </div>

            <div class="flex flex-wrap gap-3">
                <div v-for="time in props.times"
                     @click="isBooked(time.time) ? form.time = '' : form.time = time.time"
                     class="rounded-full text-white pl-4 pr-3 py-2 flex items-center gap-3"
                     :class="[
                         isBooked(time.time) ? 'bg-red-500' : 'cursor-pointer bg-green-500',
                         form.time === time.time ? 'outline outline-offset-4 outline-2 outline-green-500' : ''
                     ]">
                    {{ time.time }}
                </div>
            </div>
            <div @click="storeAppointment"
                 class="cursor-pointer hover:bg-gray-100 hover:bg-opacity-20 py-2 px-5 border rounded-full">Записаться
            </div>
        </div>
    </section>


    <footer class="flex justify-center">
        <div class="flex flex-col text-[#3e3e3e] text-center py-5">
            <div class="flex gap-3 py-1 px-4 border border-[#222222] border-opacity-20 rounded-full mb-8 text-sm">
                <div class="font-semibold hover:text-indigo-400"><a href="#about">обо мне</a></div>
                <div class="font-semibold hover:text-indigo-400"><a href="#services">цены</a></div>
                <div class="font-semibold hover:text-indigo-400"><a href="#reviews">отзывы</a></div>
                <div class="font-semibold hover:text-indigo-400"><a href="#appointment">оставить заявку</a></div>
            </div>
            <div class="text-xs">
                <div @click="showModal = true" class="text-[#222222] underline mb-2 cursor-pointer">Политика
                    конфиденциальности
                </div>
                <div>ИП Фомин Максим Алексеевич</div>
                <div>ИНН 710704691060</div>
                <div>ОГРНИП 304710721200027</div>
            </div>
        </div>
    </footer>
    <Modal :show="showModal" @close="showModal=false">
        <div class="p-10">
            <p class="font-bold text-xl">Политика конфиденциальности</p><br>
            <div class="text-xl">
                <p>
                    Политика конфиденциальности,
                    включающая обработку персональных данных (далее – Политика) действует в отношении всей информации,
                    которую Администрация Сервиса https://computerartisan.ru/ (далее по тексту "Сервисный центр"),
                    получает
                    о Пользователе во время использования сервисов на Сайте (далее – Сервисы).
                </p><br>
                <p>
                    Согласие Пользователя на предоставление персональной информации, данное им
                    в соответствии с настоящей Политикой конфиденциальности в рамках использования одного из Сервисов
                    распространяется на все Сервисы Сайта.
                </p><br>
                <p>
                    Само использование Сервисов Сайта означает безоговорочное согласие
                    Пользователя с настоящей Политикой конфиденциальности и указанными в ней условиями обработки его
                    персональной информации, в случае несогласия с этими условиями Пользователь должен воздержаться от
                    использования Сервисов Сайта.
                </p><br>
                <p>Термины и определения</p><br>
                <p>
                    "Персональные данные" - любая информация, относящаяся к физическому лицу
                    (субъекту персональных данных).
                </p>
                <p>
                    "Обработка персональных данных" - любое действие (операция) или
                    совокупность действий (операций), совершаемых с использованием средств автоматизации или без
                    использования таких средств с персональными данными, включая сбор, запись, систематизацию,
                    накопление, хранение, уточнение (обновление, изменение), извлечение, использование, передачу
                    (распространение, предоставление, доступ), обезличивание, блокирование, удаление, уничтожение
                    персональных данных.
                </p><br>
                <p>
                    "Конфиденциальность персональных данных" - обязательное для соблюдения
                    Сервисным центром или иным получившим доступ к персональным данным лицом требование не допускать их
                    распространения без согласия субъекта персональных данных или наличия иного законного основания.
                </p><br>
                <p>
                    "Пользователь" – любое дееспособное физическое лицо, либо любое юридическое
                    лицо или индивидуальный предприниматель, которые заключили с Сервисным центром Пользовательское
                    Соглашение об использовании Сервиса, путем использования Сервисов Сайта.
                </p><br>
                <p>
                    "Пользовательское соглашение" – детализированные правила и условия
                    пользования Сервисом, размещенные по адресу: https://computerartisan.ru/, распространяющиеся на всех
                    Пользователей.
                </p><br>
                <p>
                    "Cookies" — небольшой фрагмент данных, отправленный веб-сервером и хранимый
                    на компьютере пользователя, который веб-клиент или веб-браузер каждый раз пересылает веб-серверу в
                    HTTP-запросе при попытке открыть страницу соответствующего сайта.
                </p><br>
                <p>
                    "IP-адрес" — уникальный сетевой адрес узла в компьютерной сети, построенной
                    по протоколу IP.
                </p><br>
                <p>
                    "Сервисы Сайта" – все услуги, оказываемые Сервисным центром, заказанные на
                    полях Сайта, а также путем заключения письменных договоров по заказу в пункте приема и по
                    телефону.
                </p><br>
                <p>Общие положения</p><br>
                <p>
                    В рамках настоящей Политики под "персональной информацией Пользователя",
                    разрешённой к сбору и обработке понимаются:
                </p><br>
                <p>
                    · Персональная информация, которую Пользователь предоставляет о себе
                    самостоятельно при регистрации или в процессе использования Сайта, включая персональные данные
                    Пользователя. А именно: Имя; Номер контактного телефона. Фамилия; Отчество; Адрес электронной почты.
                    · Данные, которые автоматически передаются Сервисам Сайта в процессе их использования с помощью
                    установленного на устройстве Пользователя программного обеспечения, в том числе IP-адрес, информация
                    из cookie, информация о браузере Пользователя (или иной программе, с помощью которой осуществляется
                    доступ к Сервисам), время доступа, адрес запрашиваемой страницы. Отключение cookies может повлечь
                    невозможность доступа к частям Сайта, требующим авторизации. · Иная информация о Пользователе, сбор
                    и/или предоставление которой необходимо для использования Сервисов.
                </p><br>
                <p>
                    Настоящая Политика применяется только к Сервисам Сайта. Сервисный центр не
                    контролирует и не несет ответственность за сайты третьих лиц, на которые Пользователь может перейти
                    по ссылкам, доступным на Сайте.
                </p><br>
                <p>
                    Сервисный центр не проверяет достоверность персональной информации,
                    предоставляемой Пользователями, и не осуществляет контроль за их дееспособностью. Однако Сервисный
                    центр исходит из того, что Пользователь предоставляет достоверную и достаточную персональную
                    информацию по вопросам, предлагаемым в форме регистрации, и поддерживает эту информацию в актуальном
                    состоянии.
                </p><br>
                <p>Цели сбора и обработки персональной информации Пользователей</p><br>
                <p>
                    Сервисный центр собирает и хранит только те персональные данные, которые
                    необходимы для оказания услуг Пользователю.
                </p><br>
                <p>
                    Персональная информация Пользователя может использоваться в следующих
                    целях:
                </p><br>
                <p>
                    · Регистрации/авторизации Пользователя на Сайте; · Обработки Заказов
                    Пользователя и для выполнения своих обязательств перед Пользователем; · Для осуществления
                    деятельности по продвижению товаров и услуг; · Оценки и анализа работы Сервисного центра; ·
                    Определения победителя в акциях, проводимых Сервисным центром; · Анализа покупательских особенностей
                    Пользователя и предоставления персональных рекомендаций;
                </p><br>
                <p>
                    Предоставление Пользователю анонсов новостей, новостей Сервисов, а также
                    иной информации рекламного характера, путем рассылки сообщений на адрес электронной почты также
                    относится к правам Сервисного центра. При этом Пользователь в любой момент может отказаться от
                    предоставления этой информации, путем направления письменного уведомления по адресу: г. Москва,
                    Весковский переулок, 3
                </p><br>
                <p>Условия обработки персональной информации Пользователя</p><br>
                <p>
                    Обработка персональных данных Пользователя осуществляется без ограничения
                    срока, любым законным способом, в том числе в информационных системах персональных данных с
                    использованием средств автоматизации или без использования таких средств.
                </p><br>
                <p>
                    В отношении персональной информации Пользователя сохраняется ее
                    конфиденциальность, кроме случаев добровольного предоставления Пользователем информации о себе для
                    общего доступа всем пользователям Сайта. При использовании отдельных Сервисов Пользователь
                    соглашается с тем, что определённая часть его персональной информации становится общедоступной.
                </p><br>
                <p>
                    Сервисный центр вправе передать персональную информацию пользователя
                    третьим лицам в следующих случаях:
                </p><br>
                <p>
                    · Пользователь явно выразил свое согласие на такие действия; · Передача
                    необходима в рамках использования Пользователем определенного Сервиса либо для оказания услуги
                    Пользователю. При этом обеспечивается конфиденциальность персональной информации, а Пользователь
                    будет явным образом уведомлен о такой передаче;
                </p><br>
                <p>
                    · Передача предусмотрена российским или иным применимым законодательством в
                    рамках установленной законодательством процедуры; · Такая передача происходит в рамках продажи или
                    иной передачи бизнеса (полностью или в части), при этом к приобретателю переходят все обязательства
                    по соблюдению условий настоящей Политики конфиденциальности применительно к полученной им
                    персональной информации; · В целях обеспечения возможности защиты прав и законных интересов
                    Сервисного центра или третьих лиц в случаях, когда пользователь нарушает Пользовательское
                    соглашение. При обработке персональных данных Пользователей Сервисный центр руководствуется
                    Федеральным законом РФ «О персональных данных».
                </p><br>
                <p>
                    Сервисный центр принимает необходимые организационные и технические меры
                    для защиты персональных данных Пользователя от неправомерного или случайного доступа, уничтожения,
                    изменения, блокирования, копирования, распространения, а также от иных неправомерных действий
                    третьих лиц. Доступ к персональным данным имеют только уполномоченные сотрудники Сервисного центра,
                    уполномоченные сотрудники сторонних компаний (т. е. поставщиков услуг) или наших деловых партнеров,
                    подписавшие договор о конфиденциальности и защите персональных данных. Все сотрудники Сервисного
                    центра, имеющие доступ к персональным данным, должны придерживаться политики по обеспечению
                    конфиденциальности и защиты персональных данных. В целях обеспечения конфиденциальности информации и
                    защиты персональных данных Сервисный центр поддерживает соответствующую ИТ-среду и принимает все
                    меры, необходимые для предотвращения несанкционированного доступа.
                </p><br>
                <p>
                    При утрате или разглашении персональных данных Сервисный центр информирует
                    Пользователя об утрате или разглашении персональных данных.
                </p><br>
                <p>
                    Сервисный центр совместно с Пользователем принимает все необходимые меры по
                    предотвращению убытков или иных отрицательных последствий, вызванных утратой или разглашением
                    персональных данных Пользователя.
                </p><br>
                <p>
                    В случае утраты или разглашения Конфиденциальной информации Сервисный центр
                    не несёт ответственность, если данная конфиденциальная информация: · Стала публичным достоянием до
                    её утраты или разглашения. · Была получена от третьей стороны до момента её получения Сервисным
                    центром. · Была разглашена с согласия Пользователя.
                </p><br>
                <p>
                    Сервисный центр осуществляет блокирование персональных данных, относящихся
                    к соответствующему Пользователю, с момента обращения или запроса Пользователя или его законного
                    представителя либо уполномоченного органа по защите прав субъектов персональных данных на период
                    проверки, в случае выявления недостоверных персональных данных или неправомерных действий.
                </p><br>
                <p>Согласие Пользователя</p><br>
                <p>
                    Пользуясь сервисами Сайта и/или регистрируясь на Сайте
                    https://computerartisan.ru/ Пользователь дает свое согласие Сервисного центра на сбор,
                    систематизацию,
                    накопление, хранение, использование, обезличивание, удаление, изменение, передачу третьим лицам
                    своих персональных данных: фамилию, имя, отчество, номер мобильного/домашнего телефона, адрес
                    электронной почты, в целях заключения договора оказания услуг, а также дает свое согласие на
                    обработку вышеперечисленных персональных данных и направление на указанный Пользователем адрес
                    электронной почты и/или на номер мобильного телефона информации о работах, услугах.
                </p><br>
                <p>
                    Пользователь подтверждает, что персональные данные и иные сведения,
                    относящиеся к нему (в том числе, но не ограничиваясь: фамилия, имя, отчество, номер мобильного
                    телефона, адрес электронной почты) предоставлены Пользователем путем внесения их при
                    регистрации/заказе услуг/подписке на новости на Сайт https://computerartisan.ru/ добровольно и
                    являются
                    достоверными.
                </p><br>
                <p>
                    Согласие может быть отозвано Пользователем в любой момент путем направления
                    письменного уведомления по адресу: г. Москва, Весковский переулок, 3
                </p><br>
                <p>Заключительные положения</p><br>
                <p>
                    Сервисный центр имеет право вносить изменения в настоящую Политику
                    конфиденциальности. Актуальная редакции содержит дату последнего обновления. Новая редакция Политики
                    конфиденциальности вступает в силу с момента ее размещения, если иное не предусмотрено новой
                    редакцией Политики конфиденциальности. Новая редакция Политики конфиденциальности действует для
                    новых пользователей, а также для Пользователей заключивших пользовательское соглашение до внесения
                    изменений.
                </p><br>
                <p>
                    К настоящей Политике конфиденциальности и отношениям между Пользователем и
                    Сервисным центром, возникающим в связи с применением Политики, подлежит применению действующее
                    законодательство Российской Федерации.
                </p><br>
                <p>
                    Все споры между сторонами решаются путем переговоров. Претензионный порядок
                    решения споров является обязательным, срок ответа на претензию составляет 10 (десять) рабочих
                    дней.
                </p><br>
                <p>
                    Если по тем или иным причинам одно или несколько положений настоящей
                    Политики конфиденциальности будут признаны недействительными или не имеющими юридической силы, это
                    не оказывает влияния на действительность или применимость остальных положений Политики.
                </p><br>
                <p>
                    Сервисный центр не принимает предложения от Пользователей относительно
                    изменений настоящей Политики конфиденциальности.
                </p><br>
                <p>
                    Используя сервисы и материалы Сайта Пользователь подтверждает, что
                    ознакомлен со всеми пунктами настоящей Политики конфиденциальности и принимает их без исключений и
                    оговорок
                </p>
            </div>
        </div>
    </Modal>
</template>
