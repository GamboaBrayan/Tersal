<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { Search, MapPin, Phone, Menu, X, Mail, Crown, MessageCircle } from 'lucide-vue-next';
import { ref } from 'vue';

</script>

<template>
  <div class="w-full">
    <!-- Top Contact Bar -->
    <div class="bg-red-600 text-white py-2 text-xs md:text-sm hidden md:block">
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
        
        <!-- Right Side: Links -->
        <div class="flex items-center gap-4 lg:gap-6 font-semibold">
          <Link href="/contacto" class="flex items-center gap-2 hover:text-red-100 transition-colors">
            <MapPin class="w-4 h-4" /> 
            <span>Ubícanos</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center flex-1">
          <Link href="/" class="flex items-center">
            <img src="/images/logo.png" alt="Tersal Logo" class="h-14 w-auto object-contain" />
          </Link>
        </div>

        <!-- Center Nav -->
        <nav class="hidden md:flex justify-center space-x-8 text-sm font-semibold uppercase tracking-wider text-gray-600">
          <Link href="/" :class="{ 'text-primary border-b-2 border-primary': $page.url === '/' }" class="hover:text-primary py-7 transition-colors">Inicio</Link>
          <Link href="/catalog" :class="{ 'text-primary border-b-2 border-primary': $page.url.startsWith('/catalog') }" class="hover:text-primary py-7 transition-colors">Catálogo</Link>
          <Link href="/guide" :class="{ 'text-primary border-b-2 border-primary': $page.url.startsWith('/guide') }" class="hover:text-primary py-7 transition-colors">Guía de Neumáticos</Link>
        </nav>

        <!-- Right Spacer -->
        <div class="hidden md:flex flex-1"></div>
      </div>
    </header>
  </div>
</template>
