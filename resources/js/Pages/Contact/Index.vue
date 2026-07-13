<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { Phone, Mail, MapPin, Clock, MessageCircle, Map as MapIcon } from 'lucide-vue-next';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Información de Contacto" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
      <div class="max-w-3xl mb-12">
        <h1 class="text-4xl font-black text-gray-900 mb-4">Información de Contacto</h1>
        <p class="text-lg text-gray-600">
          Estamos aquí para ayudarte. Contáctanos para cotizaciones, disponibilidad de neumáticos o asesoramiento técnico. Nuestro equipo especializado te atenderá con velocidad y precisión.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left: Contact Details -->
        <div class="space-y-8">
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              Atención Inmediata
            </h2>
            
            <div class="space-y-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 text-gray-600">
                  <Phone class="w-6 h-6" />
                </div>
                <div>
                  <div class="text-sm text-gray-500 font-medium">Llamada Directa</div>
                  <div class="text-xl text-gray-900">+1 (234) 567-890</div>
                </div>
              </div>

              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 text-gray-600">
                  <Mail class="w-6 h-6" />
                </div>
                <div>
                  <div class="text-sm text-gray-500 font-medium">Correo Electrónico</div>
                  <div class="text-xl text-gray-900">ventas@tersal.com</div>
                </div>
              </div>
            </div>

            <a :href="'https://wa.me/' + $page.props.global_whatsapp + '?text=' + encodeURIComponent('¡Hola! Estoy navegando en la web de Tersal y necesito ayuda o asesoría con un servicio/neumático.')" target="_blank" class="mt-8 w-full h-14 flex items-center justify-center gap-2 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-sm">
              <MessageCircle class="w-5 h-5" /> Escribir al WhatsApp
            </a>
          </div>

          <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <Clock class="w-5 h-5 text-gray-500" /> Horario de Servicio
            </h2>
            
            <div class="space-y-4 text-gray-600">
              <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span>Lunes - Viernes</span>
                <span class="font-bold text-gray-900">08:00 - 18:00</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-50">
                <span>Sábado</span>
                <span class="font-bold text-gray-900">09:00 - 14:00</span>
              </div>
              <div class="flex justify-between items-center py-2">
                <span>Domingo</span>
                <span class="bg-red-50 text-action text-xs font-bold px-2 py-1 rounded">Cerrado</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Map -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
          <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
            <MapPin class="w-5 h-5 text-gray-500" /> Ubicación
          </h2>
          <p class="text-gray-600 mb-6 text-sm">Jr. Tacna 1150, Magdalena del Mar 15086</p>
          
          <div class="w-full bg-gray-100 rounded-xl overflow-hidden min-h-[350px] lg:h-full lg:min-h-[400px] mb-4">
            <iframe 
              src="https://maps.google.com/maps?q=Jr.%20Tacna%201150,%20Magdalena%20del%20Mar%2015086&t=&z=16&ie=UTF8&iwloc=&output=embed" 
              class="w-full h-full min-h-[350px] lg:min-h-[400px]"
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
          <a href="https://maps.google.com/maps?q=Jr.%20Tacna%201150,%20Magdalena%20del%20Mar%2015086" target="_blank" class="w-full h-12 flex items-center justify-center gap-2 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
            <MapPin class="w-5 h-5" /> Abrir en Google Maps
          </a>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
