<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Facebook, Instagram } from 'lucide-vue-next';
</script>

<template>
  <footer class="bg-[#1B3BE3] text-white mt-auto relative flex flex-col">
    <!-- Top Section -->
    <div class="pt-16 pb-12 relative overflow-hidden">
      <!-- Imagen de la llanta de fondo -->
      <img src="/images/llantafooter.webp" alt="Tire Background" class="absolute right-[-5%] md:right-0 bottom-[-2rem] h-[95%] md:h-[100%] object-contain object-bottom pointer-events-none z-10 opacity-70" />
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Col 1 -->
          <div>
            <div class="flex items-center gap-2 mb-6">
              <img src="/images/logo.png" alt="Tersal Logo" class="h-16 w-auto object-contain" />
            </div>
            <p class="text-white-200 text-sm">Especialistas en neumáticos, comprometidos con recomendar la opción adecuada para cada vehículo y propósito.</p>
            <div class="flex items-center gap-4 mt-6">
              <span class="text-sm font-semibold">Síguenos en:</span>
              <a href="https://www.facebook.com/LlantasTersal/" target="_blank" class="hover:text-gray-300 transition-colors"><Facebook class="w-5 h-5" /></a>
              <a href="https://www.instagram.com/llantas.tersal/" target="_blank" class="hover:text-gray-300 transition-colors"><Instagram class="w-5 h-5" /></a>
            </div>
          </div>
          
          <!-- Col 2 -->
          <div>
            <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Empresa</h3>
            <ul class="space-y-3 text-white-200 text-sm">
              <li><Link href="/" class="hover:text-white transition-colors">Inicio</Link></li>
              <li><Link href="/catalog" class="hover:text-white transition-colors">Catálogo</Link></li>
              <li><Link href="/sobre-nosotros" class="hover:text-white transition-colors">Nosotros</Link></li>
              <li><Link href="/guide" class="hover:text-white transition-colors">Guía de Neumáticos</Link></li>
              <li><Link href="/contacto" class="hover:text-white transition-colors">Contacto</Link></li>
            </ul>
          </div>
          
          <!-- Col 3 -->
          <div>
            <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Soporte</h3>
            <ul class="space-y-3 text-sm text-white-400">
              <li><a :href="'https://wa.me/' + usePage().props.global_whatsapp + '?text=' + encodeURIComponent('¡Hola! Estoy navegando en la web de Tersal y necesito ayuda o asesoría con un servicio/neumático.')" target="_blank" class="hover:text-white transition-colors inline-block w-fit">Soporte por WhatsApp</a></li>
              <li><a href="mailto:ventas@tersal.pe" class="hover:text-white transition-colors inline-block w-fit">ventas@llantastersal.com.pe</a></li>
            </ul>
          </div>
          
          <!-- Col 4 -->
          <div>
            <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Legal</h3>
            <ul class="space-y-3 text-gray-200 text-sm">
              <li class="hidden"><span class="text-white-200 cursor-not-allowed opacity-75">Términos y condiciones</span></li>
              <li class="hidden"><span class="text-white-200 cursor-not-allowed opacity-75">Política de Privacidad</span></li>
              <li>
                <a href="/libro-reclamaciones" target="_blank" class="inline-block transition-transform hover:scale-105">
                  <img src="/images/libroreclamaciones.jpg" alt="Libro de Reclamaciones" class="h-12 w-auto object-contain bg-white rounded p-1" />
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section: Medios de pago & Copyright (z-20) -->
    <div class="bg-black w-full z-20 py-4 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
          <div class="flex justify-center md:justify-start order-2 md:order-1">
            <p class="text-xs text-white-400 font-medium tracking-wide">&copy; {{ new Date().getFullYear() }} TERSAL. Todos los derechos reservados.</p>
          </div>
          <div class="flex justify-center items-center gap-4 order-1 md:order-2">
            <div class="flex items-center justify-center bg-white/5 rounded-md px-3 py-2 hover:bg-white/10 transition-colors duration-300">
              <img src="/images/VISA.png" alt="Visa" class="h-6 w-auto object-contain transition-transform hover:scale-110 brightness-0 invert" />
            </div>
            <div class="flex items-center justify-center bg-white/5 rounded-md px-3 py-2 hover:bg-white/10 transition-colors duration-300">
              <img src="/images/mastercard_full.svg" alt="Mastercard" class="h-6 w-auto object-contain transition-transform hover:scale-110" />
            </div>
            <div class="flex items-center justify-center bg-white/5 rounded-md px-3 py-2 hover:bg-white/10 transition-colors duration-300">
              <img src="/images/amex_text.svg" alt="American Express" class="h-6 w-auto object-contain transition-transform hover:scale-110" />
            </div>
            <div class="flex items-center justify-center bg-white/5 rounded-md px-3 py-2 hover:bg-white/10 transition-colors duration-300">
              <img src="/images/diners_full.svg" alt="Diners Club" class="h-6 w-auto object-contain transition-transform hover:scale-110" />
            </div>
          </div>
          <div class="hidden md:block order-3"></div>
        </div>
      </div>
    </div>
  </footer>
</template>
