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

    <main class="flex-grow flex items-center justify-center p-6">
      <div class="text-center bg-white p-12 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100/50 max-w-lg w-full">
        <h1 class="text-7xl font-black text-action leading-none mb-4">404</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Oops! Página no encontrada</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">Parece que la página que estás buscando no existe, ha sido movida o la ruta es incorrecta.</p>
        
        <Link href="/" class="inline-block bg-action text-white font-bold py-3 px-8 rounded-xl hover:bg-red-700 transition-colors shadow-sm hover:shadow">
          Volver al Inicio
        </Link>
      </div>
    </main>

    <Footer />
    <WhatsAppFloatingBtn />
  </div>
</template>
