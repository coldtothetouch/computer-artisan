<script setup>
import {Head, useForm, Link, usePage} from '@inertiajs/vue3';
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle'
import {onMounted, ref} from "vue";
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import ruLocale from '@fullcalendar/core/locales/ru';
import Notification from "@/Components/Notification.vue";
import {initIIFE} from '../main.js';
import MapModal from "@/Components/MapModal.vue";

const user = usePage().props.auth.user

onMounted(() => {
    new Swiper('.swiper', {
        loop: true,
        speed: 700,
        pagination: {
            el: '.swiper-pagination',
        },
        autoplay: {
            delay: 2000,
        }
    });

    document.querySelectorAll('.fc-daygrid-day-number').forEach(function (day) {
        const selectedDay = today.toLocaleDateString('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        if (day.ariaLabel === selectedDay) {
            day.style.backgroundColor = 'black'
            day.style.color = 'white'
        }
    })

    initIIFE()
})

const props = defineProps({
    reviews: Array,
    categories: Array,
    times: Array,
    appointments: Array,
    services: Array,
})

const showMapModal = ref(false)

const today = new Date()
const year = today.getFullYear()
const month = String(today.getMonth() + 1).padStart(2, '0')
const day = String(today.getDate()).padStart(2, '0')
const formattedDate = `${year}-${month}-${day}`

const monthMap = {
    'января': 'January',
    'февраля': 'February',
    'марта': 'March',
    'апреля': 'April',
    'мая': 'May',
    'июня': 'June',
    'июля': 'July',
    'августа': 'August',
    'сентября': 'September',
    'октября': 'October',
    'ноября': 'November',
    'декабря': 'December'
};

const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: ruLocale,
    dayHeaders: false,
    titleFormat: {
        month: 'long'
    },
    headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next'
    },
    aspectRatio: 1,
    datesSet: function () {
        if (!form.date) {
            return
        }

        document.querySelectorAll('.fc-daygrid-day-number').forEach(function (day) {
            let dateParts = day.getAttribute('aria-label').replace(' г.', '').split(' ');

            const dayNumber = dateParts[0];
            const monthInRussian = dateParts[1];
            const year = dateParts[2];

            const monthInEnglish = monthMap[monthInRussian]

            if (!monthInEnglish) {
                return;
            }

            const date = `${year}-${String((Object.keys(monthMap).indexOf(monthInRussian) + 1)).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;

            if (date === form.date) {
                day.style.backgroundColor = 'black';
                day.style.color = 'white'
            }
        });
    },
    dateClick: function (arg) {
        form.date = arg.dateStr
        form.time = ''

        document.querySelectorAll('.fc-daygrid-day-number').forEach(function (day) {
            day.style.backgroundColor = 'transparent';
            day.style.color = 'black'
        });

        arg.dayEl.querySelectorAll('a')[0].style.backgroundColor = 'black'
        arg.dayEl.querySelectorAll('a')[0].style.color = 'white'
    },
}

const form = useForm({
    phone_number: '',
    client_name: '',
    date: formattedDate,
    time: '',
    service: null,
})

const showNotification = ref(false);
const notificationMessage = ref('')
const notificationType = ref('error')

const selectedCategory = ref(props.categories[0] ?? null)

function storeAppointment() {
    form.post(route('appointments.store'), {
        onSuccess: (res) => {
            form.phone_number = ''
            form.client_name = ''
            form.time = ''
            form.service = null
            console.log(res);

            alert('success', usePage().props.flash.message)
        },
        onError: (res) => {
            alert('error', res[Object.keys(res)[0]])
        },
        preserveScroll: true,
    })
}

function alert(type, message) {
    showNotification.value = true
    notificationType.value = type
    notificationMessage.value = message
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

function sortTime(array) {
    return array.sort((a, b) => {
        const [hoursA, minutesA] = a.time ? a.time.split(":").map(Number) : a.split(":").map(Number);
        const [hoursB, minutesB] = b.time ? b.time.split(":").map(Number) : b.split(":").map(Number);

        const totalMinutesA = hoursA * 60 + minutesA;
        const totalMinutesB = hoursB * 60 + minutesB;

        return totalMinutesA - totalMinutesB;
    })
}

function showTime(time) {
    return time.time > `${new Date().getHours()}:${new Date().getMinutes()}`
        && form.date === `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`
        || form.date > `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`
}

function filteredTimes() {
    return sortTime(Array.from(props.times).filter(t => showTime(t)))
}

</script>

<template>
    <Head title="Главная"/>

    <Notification :type="notificationType" v-model:show="showNotification" :message="notificationMessage"/>
    <MapModal v-model:show="showMapModal" @close="showMapModal = false"/>

    <header id="header" class="header d-flex flex-column justify-content-center">
        <i class="header-toggle d-xl-none bi bi-list"></i>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li>
                    <a href="#hero" class="active"><i class="bi bi-house navicon"></i><span>Главная</span></a>
                </li>
                <li>
                    <a href="#about"><i class="bi bi-person navicon"></i><span class="text-nowrap">Обо мне</span></a>
                </li>
                <li>
                    <a href="#resume"><i class="bi bi-file-earmark-text navicon"></i><span
                        class="text-nowrap">Почему я?</span></a>
                </li>
                <li>
                    <a href="#services"><i class="bi bi-hdd-stack navicon"></i><span>Услуги</span></a>
                </li>
                <li>
                    <a href="#contact"><i class="bi bi-envelope navicon"></i><span>Связь</span></a>
                </li>
                <li v-if="usePage().props.auth.user">
                    <Link :href="route('admin.appointments.index')"><i
                        class="bi bi-gear navicon"></i><span>Админка</span></Link>
                </li>
            </ul>
        </nav>
    </header>

    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section light-background">
            <img src="hero-bg.jpg" alt=""/>

            <div class="container" data-aos="zoom-out">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <h2>Максим Фомин</h2>
                        <p>
                            Я компьютерный <span class="typed"
                                                 data-typed-items="мастер, гений, волшебник, любитель"></span
                        ><span class="typed-cursor typed-cursor--blink" aria-hidden="true"></span>
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="bi bi-twitter-x"></i></a>
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Hero Section -->

        <!-- About Section -->
        <section id="about" class="about section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Обо мне</h2>
                <p>
                    Я сертифицированный компьютерный мастер с более чем 10-летним опытом в ремонте и обслуживании
                    компьютерной техники. Помогаю как частным
                    клиентам, так и малому бизнесу решать технические проблемы быстро и эффективно.
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4 justify-content-center">
                    <div class="col-lg-4">
                        <img src="profile-img.jpg" class="img-fluid" alt=""/>
                    </div>
                    <div class="col-lg-8 content">
                        <h2>Профессиональный компьютерный мастер.</h2>
                        <p class="fst-italic py-3">
                            Моя цель — сделать вашу технику надежной и стабильной. Предлагаю полный спектр услуг по
                            ремонту и обслуживанию компьютеров и
                            ноутбуков любой сложности.
                        </p>
                        <div class="row">
                            <div class="col-lg-6">
                                <ul>
                                    <li><i class="bi bi-chevron-right"></i> <strong>Телефон:</strong> <span>8 (495) 126 33-92</span>
                                    </li>
                                    <li><i class="bi bi-chevron-right"></i> <strong>Эл. почта:</strong> <span>maxfomin@yandex.com</span>
                                    </li>
                                    <li><i class="bi bi-chevron-right"></i> <strong>Город:</strong>
                                        <span>Москва, Россия</span></li>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <ul>
                                    <li><i class="bi bi-chevron-right"></i> <strong>Возраст:</strong> <span>30</span>
                                    </li>
                                    <li><i class="bi bi-chevron-right"></i> <strong>Учёная степень:</strong> <span>Магистр</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="py-3">
                            Специализируюсь на диагностике, ремонте и настройке компьютерной техники, включая настольные
                            ПК, ноутбуки и периферийные
                            устройства.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- /About Section -->

        <!-- Stats Section -->
        <section id="stats" class="stats section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-emoji-smile"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1"
                                  class="purecounter"></span>
                            <p>Довольных клиентов</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-journal-richtext"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1"
                                  class="purecounter"></span>
                            <p>Выездов по записи</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-headset"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="1463" data-purecounter-duration="1"
                                  class="purecounter"></span>
                            <p>Часов работы</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-people"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="1"
                                  class="purecounter"></span>
                            <p>лет стажа</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                </div>
            </div>
        </section>
        <!-- /Stats Section -->

        <!-- Skills Section -->
        <section id="skills" class="skills section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Мои навыки</h2>
                <p>Как опытный компьютерный мастер, я обладаю широким спектром профессиональных навыков, которые
                    позволяют решать задачи любой сложности</p>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row skills-content skills-animation">
                    <div class="col-lg-6">
                        <div class="progress">
                            <span class="skill"><span>Ремонт и обслуживание компьютеров и ноутбуков</span> <i
                                class="val">100%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->

                        <div class="progress">
                            <span class="skill"><span>Установка и настройка операционных систем</span> <i class="val">90%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->

                        <div class="progress">
                            <span class="skill"><span>Восстановление данных</span> <i class="val">75%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->
                    </div>

                    <div class="col-lg-6">
                        <div class="progress">
                            <span class="skill"><span> Настройка и оптимизация программного обеспечения</span> <i
                                class="val">80%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->

                        <div class="progress">
                            <span class="skill"><span>Защита от вирусов и вредоносного ПО</span> <i class="val">90%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->

                        <div class="progress">
                            <span class="skill"><span>Модернизация и сборка ПК</span> <i class="val">55%</i></span>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar" role="progressbar" aria-valuenow="55" aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!-- End Skills Item -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /Skills Section -->

        <!-- Resume Section -->
        <section id="resume" class="resume section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Почему я?</h2>
                <p>Выбор компьютерного мастера — это важный шаг, особенно когда речь идет о вашей технике. Вот несколько
                    причин, почему стоит выбрать именно меня:
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row">
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="resume-item">
                            <h4>Многолетний опыт</h4>
                            <p>
                                <em
                                >Я работаю в сфере ремонта и обслуживания компьютерной техники более 8 лет. За это время
                                    я помог сотням клиентов решить проблемы с их техникой — от простых настроек до
                                    сложных ремонтов.</em
                                >
                            </p>
                        </div>
                        <!-- Edn Resume Item -->

                        <div class="resume-item">
                            <h4>Быстрая диагностика и ремонт</h4>
                            <p>
                                Я понимаю, насколько важен для вас ваш компьютер, поэтому гарантирую оперативную
                                диагностику и максимально быстрое устранение неисправностей, чтобы вы могли вернуться к
                                своим делам.
                            </p>
                        </div>
                        <!-- Edn Resume Item -->

                        <div class="resume-item">
                            <h4>Гарантия на все работы</h4>
                            <p>
                                Вся выполненная мной работа сопровождается гарантией качества. Я использую только
                                проверенные инструменты и комплектующие, что позволяет мне давать уверенность в
                                надежности ремонта.
                            </p>
                        </div>
                        <!-- Edn Resume Item -->
                    </div>

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="resume-item">
                            <h4>Индивидуальный подход</h4>
                            <p>Каждый клиент — уникален. Я внимательно выслушаю ваши пожелания, проведу подробную
                                консультацию и предложу оптимальное решение для ваших задач, будь то ремонт, настройка
                                или модернизация.</p>
                        </div>
                        <!-- Edn Resume Item -->

                        <div class="resume-item">
                            <h4>Прозрачные цены</h4>
                            <p>Никаких скрытых расходов. Вы заранее знаете стоимость работ и запчастей. Честность и
                                открытость — мои приоритеты в работе с клиентами.</p>
                        </div>
                        <!-- Edn Resume Item -->

                        <div class="resume-item">
                            <h4>Работа на результат</h4>
                            <p>Моя цель — не просто отремонтировать ваш компьютер, а сделать так, чтобы он работал
                                стабильно и надежно на протяжении долгого времени. Я стремлюсь к тому, чтобы после
                                каждого визита ваши устройства работали лучше, чем прежде.</p>
                        </div>
                        <!-- Edn Resume Item -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /Resume Section -->

        <!-- Services Section -->
        <section id="services" class="services section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Услуги</h2>
                <p>
                    Я предлагаю широкий спектр услуг по ремонту и обслуживанию компьютерной техники.<br>Моя цель —
                    обеспечить бесперебойную и стабильную работу ваших устройств, независимо от их сложности и возраста.
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row gy-4 mb-5 justify-center">
                    <div v-for="category in props.categories"
                         class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div @click="selectedCategory = category"
                             :class="selectedCategory === category ? 'bg-primary text-white' : 'bg-white'"
                             class="cursor-pointer service-item p-2 item-pink position-relative">
                            {{ category.name }}
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-6 w-3/4 lg:max-w-[1000px]" data-aos="fade-up"
                         data-aos-delay="600">
                        <div v-for="service in selectedCategory.services"
                             class="border-b border-black border-opacity-10 flex justify-between">
                            <div class="text-[#363636]">{{ service.name }}</div>
                            <div class="text-black font-semibold">от {{ service.price }} руб.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Services Section -->

        <!-- Testimonials Section -->
        <section v-if="reviews.length !== 0" id="testimonials" class="testimonials section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Отзывы моих клиентов</h2>
                <p>Если вы все еще не решились обратиться ко мне для решения вашей задачи</p>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper init-swiper">
                    <div class="swiper-wrapper">
                        <div v-for="review in reviews" class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="row gy-4 justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="testimonial-content">
                                            <p>
                                                <i class="bi bi-quote quote-icon-left"></i>
                                                <span
                                                >{{ review.body }}</span
                                                >
                                                <i class="bi bi-quote quote-icon-right"></i>
                                            </p>
                                            <h3>{{ review.body }}</h3>
                                            <h4>г. Одинцово</h4>
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                class="bi bi-star-fill"></i
                                            ><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-center">
                                        <img :src="`/storage/${review.avatar_path}`"
                                             class="size-[150px] object-cover testimonial-img"
                                             alt=""/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Testimonials Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Свяжитесь со мной</h2>
                <p>И вы не пожалеете о том, что выбрали именно меня</p>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade" data-aos-delay="100">
                <div class="flex flex-col gap-10">
                    <div class="flex flex-col lg:flex-row justify-center gap-10">
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Где я нахожусь?</h3>
                                <p>г. Москва, Весковский переулок, 3</p>
                            </div>
                        </div>
                        <!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Позвоните мне</h3>
                                <p>8 (495) 126 33 92</p>
                            </div>
                        </div>
                        <!-- End Info Item -->

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Напишите мне</h3>
                                <p>maxfomin@yandex.com</p>
                            </div>
                        </div>
                        <!-- End Info Item -->
                    </div>

                    <div class="flex flex-col lg:flex-row justify-center items-center gap-10" data-aos="fade-up"
                         data-aos-delay="100">
                        <FullCalendar class="min-w-[350px]" :options="calendarOptions"/>
                        <div class="flex flex-col gap-3 max-w-[380px]">

                            <a class="cursor-pointer hover:underline" @click="showMapModal = true">Показать радиус
                                выезда</a>

                            <div class="flex flex-col">
                                <label class="self-start" for="client_name">Как я могу к Вам обращаться?</label>
                                <input placeholder="Иван" v-model="form.client_name" id="client_name" type="text"
                                       class="bg-transparent rounded-lg">
                            </div>

                            <div class="flex flex-col">
                                <label class="self-start" for="client_name">Поделитесь своим номером</label>
                                <input type="tel" v-model="form.phone_number"
                                       placeholder="+7 ( . . . )  . . .  . . - . ."
                                       class="bg-transparent rounded-lg">
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

                            <div class="flex flex-wrap gap-2">
                                <div v-if="filteredTimes().length !== 0"
                                     v-for="time in filteredTimes()"
                                     @click="isBooked(time.time) ? form.time = '' : form.time = time.time"
                                     class="rounded-full text-white pl-4 pr-3 py-2 flex items-center gap-3"
                                     :class="[
                                     isBooked(time.time) ? 'bg-red-500' : 'cursor-pointer bg-green-500',
                                     form.time === time.time ? 'outline outline-offset-4 outline-2 outline-green-500' : ''
                                 ]">
                                    {{ time.time }}
                                </div>
                                <div v-else class="text-red-400">
                                    Нет доступного времени для записи
                                </div>
                            </div>
                            <div @click="storeAppointment"
                                 class="self-center cursor-pointer hover:bg-gray-100 hover:bg-opacity-20 py-2 px-5 border rounded-full">
                                Записаться
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Contact Form -->
            </div>
        </section>
        <!-- /Contact Section -->
    </main>

    <footer id="footer" class="footer position-relative light-background">
        <div class="container">
            <h3 class="sitename">Максим Фомин</h3>
            <p>Моя цель — сделать вашу технику надежной и стабильной.</p>
            <div class="social-links d-flex justify-content-center">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-skype"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
            <div class="container">
                <div class="copyright"><strong class="px-1 sitename">Максим Фомин.</strong>
                    <span>Все права защищены.</span>
                </div>
                <div class="text-xs text-gray-400 pt-2">ИП Фомин Максим Алексеевич</div>
                <div class="text-xs text-gray-400">ИНН 710704691060</div>
                <div class="text-xs text-gray-400 pb-2">ОГРНИП 304710721200027</div>
                <div class="credits">Made by <a href="#">Untitled Team</a>.</div>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <!--    <div id="preloader"></div>-->
</template>
