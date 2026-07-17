<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { ChevronDown, MessageCircle } from 'lucide-vue-next';

const props = defineProps({
  faqs: Array
});

const activeFaq = ref(null);
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
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
          <h1 class="text-4xl font-black text-primary mb-4">Entiende tus Neumáticos</h1>
          <p class="text-gray-600">Descifrar los números en el flanco de su neumático es crucial para la seguridad y el rendimiento. Use nuestra guía visual interactiva para encontrar el ajuste perfecto.</p>
        </div>
      </section>

      <!-- Interactive Graphic -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col lg:flex-row items-center justify-center gap-12">
            <!-- Left explanations -->
            <div class="w-full lg:w-1/3 space-y-4">
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-primary font-bold mb-1"><span class="text-lg">195</span> ANCHO DEL NEUMÁTICO</div>
                <div class="text-sm text-gray-500">Medido en milímetros de flanco a flanco.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-primary font-bold mb-1"><span class="text-lg">65</span> PERFIL (RELACIÓN DE ASPECTO)</div>
                <div class="text-sm text-gray-500">Altura de la sección transversal del neumático como % del ancho.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-bold mb-1"><span class="text-lg">R</span> CONSTRUCCIÓN</div>
                <div class="text-sm text-gray-500">Construcción radial, el estándar de la industria.</div>
              </div>
            </div>

            <!-- Graphic -->
            <div class="w-full lg:w-1/3 flex justify-center relative">
              <div class="relative bg-gray-900 rounded-3xl shadow-2xl overflow-hidden aspect-square flex items-center justify-center w-full max-w-md">
                <img src="https://i.pinimg.com/736x/43/cc/bb/43ccbbb96280ef18d5ac0341f25b2e34.jpg" class="w-full h-full object-cover opacity-90" alt="Tire Graphic" referrerpolicy="no-referrer">
              </div>
            </div>

            <!-- Right explanations -->
            <div class="w-full lg:w-1/3 space-y-4">
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-primary font-bold mb-1"><span class="text-lg">15</span> DIÁMETRO DEL ARO</div>
                <div class="text-sm text-gray-500">Diámetro de la rueda (en pulgadas) en la que encaja el neumático.</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-gray-900 font-bold mb-1">ÍNDICE DE CARGA</div>
                <div class="text-lg font-black text-gray-900">98 (750 kg)</div>
              </div>
              <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="text-action font-bold mb-1">ÍNDICE DE VELOCIDAD</div>
                <div class="text-lg font-black text-gray-900">V (240 km/h)</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Section -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-primary mb-2">Preguntas Frecuentes</h2>
            <p class="text-gray-500">Respuestas técnicas para una compra informada.</p>
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
