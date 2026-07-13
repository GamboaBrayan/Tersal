<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search, MapPin, Phone, Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

</script>

<template>
  <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <!-- Logo -->
      <div class="flex-shrink-0 flex items-center">
        <Link href="/" class="flex items-center">
          <img src="/images/logo.png" alt="Tersal Logo" class="h-14 w-auto object-contain" />
        </Link>
      </div>

      <!-- Center Nav -->
      <nav class="hidden md:flex space-x-8 text-sm font-semibold uppercase tracking-wider text-gray-600">
        <Link href="/" :class="{ 'text-primary border-b-2 border-primary': $page.url === '/' }" class="hover:text-primary py-7 transition-colors">Inicio</Link>
        <Link href="/catalog" :class="{ 'text-primary border-b-2 border-primary': $page.url.startsWith('/catalog') }" class="hover:text-primary py-7 transition-colors">Catálogo</Link>
        <Link href="/guide" :class="{ 'text-primary border-b-2 border-primary': $page.url.startsWith('/guide') }" class="hover:text-primary py-7 transition-colors">Guía de Neumáticos</Link>
      </nav>

      <!-- Right Actions -->
      <div class="flex items-center gap-6">
        <!-- Contact Icons -->
        <div class="hidden lg:flex items-center gap-4 text-gray-500">
          <Link href="/contacto" :class="['transition-colors hover:text-primary', { 'text-primary': $page.url.startsWith('/contacto') }]">
            <MapPin class="w-6 h-6" />
          </Link>
        </div>
        

        
        <!-- WhatsApp CTA (Desktop) -->
        <a :href="'https://wa.me/' + usePage().props.global_whatsapp + '?text=' + encodeURIComponent('¡Hola! Estoy navegando en la web de Tersal y necesito ayuda o asesoría con un servicio/neumático.')" target="_blank" class="hidden lg:inline-flex h-12 items-center px-6 rounded-lg bg-action text-white font-bold hover:bg-red-700 transition-colors shadow-sm">
          Contáctanos
        </a>

        <!-- Contact Page Link (Mobile) -->
        <Link href="/contacto" class="inline-flex lg:hidden h-12 items-center px-6 rounded-lg bg-action text-white font-bold hover:bg-red-700 transition-colors shadow-sm">
          Contáctanos
        </Link>
      </div>
    </div>
  </header>
</template>
