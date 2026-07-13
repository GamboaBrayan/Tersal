<script setup>
import { ref } from 'vue';
import { Menu } from 'lucide-vue-next';
import AdminSidebar from './AdminSidebar.vue';

const isSidebarOpen = ref(false);
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <AdminSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

    <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden">
      <!-- Mobile Header -->
      <div class="lg:hidden h-16 bg-white border-b border-gray-200 flex items-center px-4 shrink-0 shadow-sm z-30">
        <button @click="isSidebarOpen = true" class="text-gray-600 hover:text-primary transition-colors p-2">
          <Menu class="w-6 h-6" />
        </button>
        <span class="ml-4 font-black tracking-tight text-gray-900">ADMIN</span>
      </div>

      <!-- Main Content Area with its own scroll -->
      <main class="flex-1 overflow-y-auto w-full relative">
        <slot />
      </main>
    </div>
  </div>
</template>
