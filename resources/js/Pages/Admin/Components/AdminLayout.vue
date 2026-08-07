<script setup>
import { ref, watch } from 'vue';
import { Menu, CheckCircle2, AlertCircle, X } from 'lucide-vue-next';
import AdminSidebar from './AdminSidebar.vue';
import { usePage } from '@inertiajs/vue3';

const isSidebarOpen = ref(false);
const page = usePage();

const toast = ref(null);

watch(() => page.props.flash, (flash) => {
  if (flash?.success) {
    toast.value = { type: 'success', message: flash.success };
    setTimeout(() => { toast.value = null; }, 5000);
  } else if (flash?.error) {
    toast.value = { type: 'error', message: flash.error };
    setTimeout(() => { toast.value = null; }, 8000);
  }
}, { deep: true, immediate: true });

</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <AdminSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

    <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden">
      <!-- Mobile Header -->
      <div class="lg:hidden h-16 bg-white border-b border-gray-100 flex items-center px-4 shrink-0 shadow-sm z-30">
        <button @click="isSidebarOpen = true" class="text-gray-500 hover:text-gray-800 transition-colors p-2">
          <Menu class="w-6 h-6" />
        </button>
        <span class="ml-4 font-medium tracking-tight text-gray-800">Dashboard Tersal</span>
      </div>

      <!-- Main Content Area with its own scroll -->
      <main class="flex-1 overflow-y-auto w-full relative">
        <slot />
      </main>
    </div>

    <!-- Global Toast -->
    <div v-if="toast" class="fixed top-4 right-4 z-[9999] animate-bounce-in max-w-sm w-full shadow-2xl rounded-xl overflow-hidden">
      <div :class="['flex items-start p-4', toast.type === 'success' ? 'bg-green-50' : 'bg-red-50']">
        <div class="flex-shrink-0">
          <CheckCircle2 v-if="toast.type === 'success'" class="h-5 w-5 text-green-400" />
          <AlertCircle v-else class="h-5 w-5 text-red-400" />
        </div>
        <div class="ml-3 w-0 flex-1 pt-0.5">
          <p :class="['text-sm font-medium', toast.type === 'success' ? 'text-green-800' : 'text-red-800']">
            {{ toast.type === 'success' ? '¡Éxito!' : 'Error' }}
          </p>
          <p :class="['mt-1 text-sm', toast.type === 'success' ? 'text-green-700' : 'text-red-700']">
            {{ toast.message }}
          </p>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
          <button @click="toast = null" :class="['rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2', toast.type === 'success' ? 'focus:ring-green-500 bg-green-50' : 'focus:ring-red-500 bg-red-50']">
            <span class="sr-only">Close</span>
            <X class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
