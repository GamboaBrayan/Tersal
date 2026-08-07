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
    :class="showToast ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0 pointer-events-none'"
  >
    <div class="bg-white border border-gray-100 text-gray-800 px-5 py-3.5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] flex items-center gap-3 min-w-[280px]">
      <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center shrink-0">
        <CheckCircle2 class="w-4 h-4 text-green-500" stroke-width="2.5" />
      </div>
      <span class="font-bold text-sm">{{ toastMessage }}</span>
    </div>
  </div>
</template>
