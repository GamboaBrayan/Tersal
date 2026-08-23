<script setup>
import { Head} from '@inertiajs/vue3';
import { ref } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';

const props = defineProps({
  faqs: Array
});

const activeFaq = ref(null);
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
};

const imageContainer = ref(null);
const zoomStyle = ref({ transformOrigin: 'center center', transform: 'scale(1)' });
const isZooming = ref(false);

const handleMouseMove = (e) => {
  if (!imageContainer.value) return;
  const { left, top, width, height } = imageContainer.value.getBoundingClientRect();
  const x = ((e.clientX - left) / width) * 100;
  const y = ((e.clientY - top) / height) * 100;
  zoomStyle.value = {
    transformOrigin: `${x}% ${y}%`,
    transform: 'scale(2.5)' // Ajusta el nivel de zoom aquí
  };
};

const handleMouseEnter = () => {
  isZooming.value = true;
};

const handleMouseLeave = () => {
  isZooming.value = false;
  zoomStyle.value = {
    transformOrigin: 'center center',
    transform: 'scale(1)'
  };
};
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Guía de Neumáticos" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow">
      <!-- Header -->
      <section class="bg-gray-50 py-16 text-center">
        <div class="max-w-3xl mx-auto px-4">
          <h1 class="text-4xl font-black text-black mb-4">¿No sabes qué medida usan tus neumáticos?</h1>
          <br>
          <p class="text-black font-medium ">Primero, revisa el costado de tu neumático.<br>Encontrarás una serie de números y letras de la siguiente manera:  </p>
        </div>
      </section>

      <!-- Interactive Graphic -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col lg:flex-row items-center justify-center gap-12">
            <!-- Left explanations -->
            <div class="w-full lg:w-1/3 space-y-4">
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">195</span> (ANCHO DEL NEUMÁTICO)</div>
                <div class="text-sm font-medium text-gray-800">Medido en milímetros de flanco a flanco.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">65</span> (PERFIL)</div>
                <div class="text-sm font-medium text-gray-800">Altura de la sección transversal del neumático como % del ancho.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">R</span> (CONSTRUCCIÓN)</div>
                <div class="text-sm font-medium text-gray-800">Construcción radial, el estándar de la industria.</div>
              </div>
            </div>

            <!-- Graphic -->
            <div class="w-full lg:w-1/3 flex justify-center relative">
              <div 
                ref="imageContainer"
                class="relative bg-gray-900 rounded-3xl shadow-2xl overflow-hidden aspect-square flex items-center justify-center w-full max-w-md"
                @mousemove="handleMouseMove"
                @mouseenter="handleMouseEnter"
                @mouseleave="handleMouseLeave"
              >
                <img 
                  src="images/guide-image.webp" 
                  class="w-full h-full object-cover opacity-90 transition-transform" 
                  :class="[isZooming ? 'duration-0' : 'duration-300 ease-out']"
                  :style="zoomStyle"
                  alt="Tire Graphic" 
                  referrerpolicy="no-referrer"
                >
              </div>
            </div>

            <!-- Right explanations -->
            <div class="w-full lg:w-1/3 space-y-4">
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">15</span> (DIÁMETRO DEL ARO)</div>
                <div class="text-sm font-medium text-gray-800">Diámetro de la rueda (en pulgadas) en la que encaja el neumático.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">98</span> (ÍNDICE DE CARGA)</div>
                <div class="text-sm font-medium text-gray-800">Indica el peso máximo que puede soportar el neumático en kg.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-medium mb-1"><span class="text-lg font-bold">V</span> (ÍNDICE DE VELOCIDAD)</div>
                <div class="text-sm font-medium text-gray-800">Indica la velocidad máxima a la que puede operar el neumático.</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Section -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-action mb-2">Preguntas Frecuentes</h2>
            <p class="text-black font-medium ">Respuestas técnicas para una compra informada.</p>
          </div>
          
          <div class="max-w-3xl mx-auto border border-gray-200 rounded-xl bg-white shadow-sm">
            <div v-for="(faq, index) in faqs" :key="index" class="border-b border-gray-200 last:border-b-0">
              <button @click="toggleFaq(index)" class="w-full flex items-center justify-between p-5 sm:p-6 text-left hover:bg-gray-50 transition-colors focus:outline-none cursor-pointer">
                <span class="font-bold text-gray-900 pr-4 text-sm sm:text-base">{{ faq.question || faq.q }}</span>
                <span class="text-gray-400 font-bold text-2xl leading-none flex-shrink-0 transition-transform" :class="{'rotate-45 text-action': activeFaq === index}">
                  +
                </span>
              </button>
              <div v-show="activeFaq === index" class="px-5 sm:px-6 pb-5 sm:pb-6 text-gray-600 text-sm leading-relaxed">
                {{ faq.answer || faq.a }}
              </div>
            </div>
          </div>
        </div>
      </section>


    </main>

    <Footer />
  </div>
</template>
