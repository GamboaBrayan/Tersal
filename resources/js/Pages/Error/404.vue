<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
</script>

<template>
  <Head title="404 Página no encontrada" />
  
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
    <Header />

    <main class="flex-grow flex flex-col items-center justify-center p-6 py-20 sm:py-32 text-center">
      <div class="mb-6 relative">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-action/20">
          <circle cx="12" cy="12" r="10"/>
          <circle cx="12" cy="12" r="4"/>
          <path d="M12 2a10 10 0 0 1 10 10"/>
          <path d="M12 22a10 10 0 0 1-10-10"/>
          <path d="M2 12h20"/>
          <path d="M12 2v20"/>
        </svg>
        <h1 class="text-7xl sm:text-9xl font-black text-gray-900 leading-none absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 tracking-tighter">404</h1>
      </div>
      <h2 class="text-3xl sm:text-4xl font-black text-gray-800 mb-4 mt-4">¡Te saliste de la pista!</h2>
      <p class="text-gray-500 mb-10 leading-relaxed max-w-lg text-base sm:text-lg">Parece que el neumático que buscas rodó demasiado lejos. La página no existe o ha sido movida.</p>
      
      <Link href="/" class="inline-flex items-center gap-2 bg-action text-white font-bold py-4 px-10 rounded-xl hover:bg-red-700 transition-colors shadow-lg hover:shadow-xl hover:-translate-y-1 duration-200">
        Volver al Inicio
      </Link>
    </main>

    <Footer />
    <WhatsAppFloatingBtn />
  </div>
</template>
