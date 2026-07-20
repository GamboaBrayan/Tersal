<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search, MapPin, Phone, Menu, X, Mail, Crown, MessageCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const isMobileMenuOpen = ref(false);

// Cerrar el menú automáticamente cuando cambia la ruta
const page = usePage();
watch(() => page.url, () => {
  isMobileMenuOpen.value = false;
});
</script>

<template>
  <div class="w-full">
    <!-- Top Contact Bar -->
    <div class="bg-red-600 text-white py-2 text-xs md:text-sm hidden md:block relative z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <!-- Left Side: Contact Info -->
        <div class="flex items-center gap-4 lg:gap-6">
          <div class="flex items-center gap-2 font-medium hover:text-red-100 transition-colors cursor-pointer">
            <Phone class="w-4 h-4" /> 
            <span>(555) 123-4567</span>
          </div>
          <div class="flex items-center gap-2 font-medium hover:text-red-100 transition-colors cursor-pointer">
            <MessageCircle class="w-4 h-4" /> 
            <span>555 987 654 / 555 123 456</span>
          </div>
          <div class="flex items-center gap-2 font-medium hover:text-red-100 transition-colors cursor-pointer">
            <Mail class="w-4 h-4" /> 
            <span>contacto@tersal.com</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Header Wrapper -->
    <div class="w-full">
      <header class="bg-black sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
          
          <!-- Mobile Hamburger Button (Left) -->
          <div class="flex md:hidden flex-1 justify-start">
            <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-white hover:text-gray-300 p-1">
              <Menu v-if="!isMobileMenuOpen" class="w-8 h-8" />
              <X v-else class="w-8 h-8" />
            </button>
          </div>

          <!-- Logo (Centered on Mobile, Left on Desktop) -->
          <div class="flex items-center justify-center flex-[2] md:flex-1 md:justify-start">
            <Link href="/" class="flex items-center">
              <img src="/images/logo2.png" alt="Tersal Logo" class="h-16 w-auto object-contain" />
            </Link>
          </div>

          <!-- Center Nav (Desktop) -->
          <nav class="text-white hidden md:flex justify-center space-x-8 text-sm font-semibold uppercase tracking-wider">
            <Link href="/" :class="{ 'text-action border-b-4 border-action': $page.url === '/' }" class="hover:text-action py-7 transition-colors">Inicio</Link>
            <Link href="/catalog" :class="{ 'text-action border-b-4 border-action': $page.url.startsWith('/catalog') }" class="hover:text-action py-7 transition-colors">Catálogo</Link>
            <Link href="/guide" :class="{ 'text-action border-b-4 border-action': $page.url.startsWith('/guide') }" class="hover:text-action py-7 transition-colors">Guía de Neumáticos</Link>
            <Link href="/contacto" :class="{ 'text-action border-b-4 border-action': $page.url.startsWith('/contacto') }" class="hover:text-action py-7 transition-colors">Contacto</Link>
          </nav>

          <!-- Right Spacer (For perfect centering) -->
          <div class="flex flex-1 justify-end md:hidden"></div>
          <div class="hidden md:flex flex-1 justify-end">
            <!-- Right Side: WhatsApp Advice -->
            <div class="flex items-center gap-3 font-semibold text-white hover:text-gray-300 transition-colors cursor-pointer text-sm tracking-wide justify-end translate-x-4 lg:translate-x-8">
              <!-- WhatsApp SVG Logo -->
              <svg class="w-6 h-6 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12.031 0C5.383 0 .004 5.378.004 12.026c0 2.13.55 4.208 1.597 6.035L0 24l6.115-1.602c1.761.968 3.754 1.479 5.811 1.479h.005c6.648 0 12.027-5.379 12.027-12.027S18.679 0 12.031 0zM12.031 21.895h-.005c-1.8 0-3.565-.483-5.111-1.398l-.367-.217-3.8.995 1.014-3.705-.239-.381a10.02 10.02 0 0 1-1.536-5.36c0-5.541 4.509-10.05 10.047-10.05 2.686 0 5.207 1.047 7.104 2.946s2.946 4.417 2.946 7.103c-.001 5.54-4.511 10.049-10.053 10.067zm5.512-7.535c-.302-.151-1.787-.881-2.064-.982-.278-.101-.481-.151-.682.151-.202.302-.782.982-.958 1.183-.177.202-.353.226-.656.075-1.314-.66-2.527-1.531-3.487-2.73-.243-.3-.026-.464.125-.615.136-.136.302-.353.453-.529.151-.176.202-.302.302-.503.1-.202.05-.378-.026-.529-.076-.151-.682-1.644-.933-2.253-.245-.595-.494-.515-.682-.524-.176-.009-.378-.009-.58-.009s-.53.076-.807.378c-.278.302-1.06 1.036-1.06 2.525s1.085 2.923 1.236 3.125c.151.202 2.134 3.258 5.168 4.568.721.312 1.284.498 1.724.638.725.23 1.386.197 1.905.12.583-.087 1.787-.732 2.04-1.442.251-.71.251-1.319.176-1.442-.075-.123-.277-.198-.579-.349z"/>
              </svg>
              <div class="flex flex-col text-left leading-tight">
                <span>Asesoría inmediata</span>
                <span class="text-[11px] text-gray-300 font-medium">por WhatsApp</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div v-if="isMobileMenuOpen" class="md:hidden bg-[#111111] border-t border-gray-800 absolute w-full shadow-2xl z-40">
          <nav class="flex flex-col text-white text-sm font-semibold uppercase tracking-wider px-6 py-4 space-y-6">
            <Link href="/" class="hover:text-red-500 transition-colors">Inicio</Link>
            <Link href="/catalog" class="hover:text-red-500 transition-colors">Catálogo</Link>
            <Link href="/guide" class="hover:text-red-500 transition-colors">Guía de Neumáticos</Link>
            <Link href="/contacto" class="hover:text-red-500 transition-colors">Contacto</Link>
          </nav>
        </div>
      </header>
    </div>
  </div>
</template>
