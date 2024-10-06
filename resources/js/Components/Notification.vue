<script setup>
import {watch} from "vue";

const props = defineProps({
    type: String,
    show: {
        type: Boolean,
        default: false
    },
    message: String,
})

const emit = defineEmits(['update:show']);

watch(() => props.show, () => {
    if (props.show === true) {
        setTimeout(() => {
            emit('update:show', false);
        }, 5000)
    }
})

</script>

<template>
    <teleport to="body">
        <div v-show="props.show" :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'" class="fixed bottom-5 right-5 py-3 px-6 text-white rounded-full">
            {{ message }}
        </div>
    </teleport>
</template>
