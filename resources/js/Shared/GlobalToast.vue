<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';

const page = usePage();
const showToast = ref(false);
const toastMessage = ref('');
let timeout = null;

// Watch for flash messages in Inertia page props
watch(() => page.props.flash?.success, (newSuccess) => {
  if (newSuccess) {
    toastMessage.value = newSuccess;
    showToast.value = true;
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
      showToast.value = false;
    }, 2000);
  }
}, { immediate: true });
</script>

<template>
  <div 
    class="fixed top-4 right-4 z-[9999] transition-all duration-300 transform"
    :class="showToast ? 'translate-y-0 opacity-100' : '-translate-y-4 opacity-0 pointer-events-none'"
  >
    <div class="bg-white border border-gray-100 text-gray-900 px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 min-w-[250px]">
      <CheckCircle2 class="w-5 h-5 text-green-500 shrink-0" />
      <span class="font-bold text-sm">{{ toastMessage }}</span>
    </div>
  </div>
</template>
