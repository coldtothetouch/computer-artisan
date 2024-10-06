<script setup>
import Modal from "./Modal.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
})

const emit = defineEmits(['close'])

function close() {
    emit('close')
}

</script>

<template>
    <Modal :show="show" @close="close">
        <img src="/map.png" alt="">
    </Modal>
</template>
