<script setup>
import { ref } from 'vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 text-xl flex items-center">
                                <Link :href="route('admin.appointments.index')">
                                    Admin Panel
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('admin.appointments.index')" :active="route().current('admin.appointments.index')">
                                    Заявки
                                </NavLink>
                                <NavLink :href="route('admin.services.index')" :active="route().current('admin.services.index')">
                                    Услуги
                                </NavLink>
                                <NavLink :href="route('admin.times.index')" :active="route().current('admin.times.index')">
                                    Время записи
                                </NavLink>
                                <NavLink :href="route('admin.reviews.index')" :active="route().current('admin.reviews.index')">
                                    Отзывы
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <DropdownLink href="/" as="button">
                                Вернуться
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Выйти
                            </DropdownLink>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2">
                        <ResponsiveNavLink :href="route('admin.appointments.index')" :active="route().current('admin.appointments.index')">
                            Заявки
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.services.index')" :active="route().current('admin.services.index')">
                            Услуги
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.times.index')" :active="route().current('admin.times.index')">
                            Время записи
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.reviews.index')" :active="route().current('admin.reviews.index')">
                            Отзывы
                        </ResponsiveNavLink>
                    </div>

                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Выйти
                    </ResponsiveNavLink>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
