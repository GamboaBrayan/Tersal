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

  </div>
</template>
