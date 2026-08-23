<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, PackageSearch, Settings, LogOut, X, ChevronLeft, ChevronRight, Tags, Megaphone } from 'lucide-vue-next';

defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close']);

const isCollapsed = ref(false);

const navItems = [
  { name: 'Métricas', href: '/admin/dashboard', icon: LayoutDashboard },
  { name: 'Inventario', href: '/admin/inventory', icon: PackageSearch },
  { name: 'Promociones', href: '/admin/promotions', icon: Megaphone },
  { name: 'Marcas', href: '/admin/brands', icon: Settings },
  { name: 'Categorías', href: '/admin/categories', icon: Tags },
  { name: 'Configuración', href: '/admin/settings', icon: Settings },
];

const logout = () => {
  import('@inertiajs/vue3').then(({ router }) => {
    router.post('/admin/logout');
  });
};
</script>

<template>
  <!-- Mobile Backdrop -->
  <div 
    v-if="isOpen" 
    @click="emit('close')"
    class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40 lg:hidden"
  ></div>

  <!-- Sidebar -->
  <aside 
    :class="[
      isOpen ? 'translate-x-0' : '-translate-x-full',
      isCollapsed ? 'lg:w-20' : 'lg:w-60',
      'lg:translate-x-0 w-64 bg-white border-r border-gray-100 h-screen flex flex-col flex-shrink-0 fixed lg:sticky top-0 z-50 transition-all duration-300 ease-in-out shadow-sm lg:shadow-none'
    ]"
  >
    <div class="h-16 flex items-center justify-between px-5 border-b border-gray-100">
      <Link href="/admin/dashboard" class="flex items-center gap-3 overflow-hidden">
        <div class="w-8 h-8 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-sm shrink-0 shadow-sm">
          T
        </div>
        <span v-if="!isCollapsed" class="text-base font-medium tracking-tight text-gray-800 whitespace-nowrap">Dashboard Tersal</span>
      </Link>
      <button @click="emit('close')" class="lg:hidden text-gray-400 hover:text-primary transition-colors">
        <X class="w-5 h-5" />
      </button>
    </div>

    <div class="flex-grow py-5 px-3 overflow-y-auto overflow-x-hidden scrollbar-hide">
      <nav class="space-y-1.5">
        <Link 
          v-for="item in navItems" 
          :key="item.name" 
          :href="item.href"
          @click="emit('close')"
          :class="[
            $page.url.startsWith(item.href) ? 'bg-primary/5 text-primary shadow-sm border border-primary/10' : 'text-gray-500 hover:bg-gray-50 hover:text-primary border border-transparent',
            'group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all relative'
          ]"
          :title="isCollapsed ? item.name : ''"
        >
          <component :is="item.icon" :class="[$page.url.startsWith(item.href) ? 'text-primary' : 'text-gray-400 group-hover:text-primary', 'w-5 h-5 shrink-0 transition-colors']" />
          <span :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100 ml-3 font-medium', 'transition-opacity duration-200 whitespace-nowrap']">
            {{ item.name }}
          </span>
        </Link>
      </nav>
    </div>

    <div class="p-3 border-t border-gray-100 space-y-1">
      <button 
        @click="logout"
        class="w-full group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors border border-transparent"
        :title="isCollapsed ? 'Cerrar Sesión' : ''"
      >
        <LogOut class="w-5 h-5 shrink-0 text-gray-400 group-hover:text-red-500 transition-colors" />
        <span :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100 ml-3', 'transition-opacity duration-200 whitespace-nowrap']">
          Cerrar Sesión
        </span>
      </button>

      <button 
        @click="isCollapsed = !isCollapsed"
        class="hidden lg:flex w-full group items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-400 hover:bg-primary/5 hover:text-primary transition-colors border border-transparent"
      >
        <component :is="isCollapsed ? ChevronRight : ChevronLeft" class="w-5 h-5 shrink-0 transition-transform group-hover:text-primary" />
        <span :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100 ml-3', 'transition-opacity duration-200 whitespace-nowrap']">
          Colapsar
        </span>
      </button>
    </div>
  </aside>
</template>
