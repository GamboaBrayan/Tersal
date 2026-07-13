<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, PackageSearch, Settings, LogOut, X } from 'lucide-vue-next';

defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close']);

const navItems = [
  { name: 'Métricas', href: '/admin/dashboard', icon: LayoutDashboard },
  { name: 'Inventario de Llantas', href: '/admin/inventory', icon: PackageSearch },
  { name: 'Marcas', href: '/admin/brands', icon: Settings },
  { name: 'Configuración Web', href: '/admin/settings', icon: Settings },
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
    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
  ></div>

  <!-- Sidebar -->
  <aside 
    :class="[
      isOpen ? 'translate-x-0' : '-translate-x-full',
      'lg:translate-x-0 transition-transform duration-300 ease-in-out'
    ]"
    class="w-72 sm:w-64 bg-primary text-white h-screen flex flex-col flex-shrink-0 fixed lg:sticky top-0 z-50 shadow-2xl lg:shadow-none"
  >
    <div class="h-20 flex items-center justify-between px-6 border-b border-blue-800">
      <Link href="/admin/dashboard" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary font-bold text-sm">T</div>
        <span class="text-xl font-black tracking-tight">ADMIN</span>
      </Link>
      <button @click="emit('close')" class="lg:hidden text-blue-200 hover:text-white">
        <X class="w-6 h-6" />
      </button>
    </div>

    <div class="flex-grow py-6 px-4 overflow-y-auto">
      <nav class="space-y-2">
        <Link 
          v-for="item in navItems" 
          :key="item.name" 
          :href="item.href"
          @click="emit('close')"
          :class="[
            $page.url.startsWith(item.href) ? 'bg-blue-800 text-white font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white',
            'group flex items-center px-4 py-3 text-sm rounded-xl transition-colors'
          ]"
        >
          <component :is="item.icon" class="w-5 h-5 mr-3 flex-shrink-0" />
          {{ item.name }}
        </Link>
      </nav>
    </div>

    <div class="p-4 border-t border-blue-800">
      <button 
        @click="logout"
        class="w-full group flex items-center px-4 py-3 text-sm font-bold rounded-xl text-blue-200 hover:bg-action hover:text-white transition-colors"
      >
        <LogOut class="w-5 h-5 mr-3 flex-shrink-0" />
        Cerrar Sesión
      </button>
    </div>
  </aside>
</template>
