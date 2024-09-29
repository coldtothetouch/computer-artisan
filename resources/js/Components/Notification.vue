<script setup>
import {watch} from "vue";

const props = defineProps({
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
        <div v-show="props.show" class="fixed bottom-5 right-5 py-3 px-6 text-white bg-red-500 rounded-full">
            {{ message }}
        </div>
    </teleport>
</template>
