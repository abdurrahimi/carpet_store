<template>
  <div class="h-1 w-8"
       :class="active ? 'bg-red-600' : 'bg-gray-300'">
  </div>
</template>

<script setup>
defineProps({
  active: {
    type: Boolean,
    default: true
  }
})
</script>