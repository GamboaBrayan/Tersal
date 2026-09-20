<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Facebook, Instagram } from 'lucide-vue-next';
</script>

<template>
  <footer class="bg-[#1B3BE3] text-white mt-auto relative flex flex-col overflow-hidden">
    <!-- Imagen de la llanta de fondo -->
    <img src="/images/llantafooter.webp" alt="Tire Background" class="absolute right-[-5%] md:right-0 -bottom-4 h-[95%] md:h-[100%] object-contain object-bottom pointer-events-none z-30 opacity-77" />

    <!-- Top Section -->
    <div class="pt-16 pb-12 relative z-10">
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8">
          <!-- Col 1 -->
          <div class="lg:col-span-4">
            <div class="flex items-center gap-2 mb-6">
              <img src="/images/logo.png" alt="Tersal Logo" class="h-16 w-auto object-contain" />
            </div>
            <p class="text-white-200 text-sm leading-relaxed">
              Especialistas en neumáticos, comprometidos<br>
              con recomendar la opción adecuada para<br>
              cada vehículo y propósito.
            </p>
            <div class="mt-6">
              <span class="text-sm font-semibold block mb-3">Síguenos en:</span>
              <div class="flex items-center gap-3">
                <a href="https://www.instagram.com/llantas.tersal/" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-full border border-white hover:bg-white hover:text-[#1B3BE3] transition-colors duration-300">
                  <Instagram class="w-5 h-5" />
                </a>
                <a href="https://www.facebook.com/LlantasTersal/" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-full border border-white hover:bg-white hover:text-[#1B3BE3] transition-colors duration-300">
                  <Facebook class="w-5 h-5" />
                </a>
                <a href="https://www.tiktok.com/@llantas_tersal" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-full border border-white hover:bg-white hover:text-[#1B3BE3] transition-colors duration-300">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.23-1.11 4.46-2.9 5.81-1.78 1.34-4.22 1.83-6.4 1.4-2.18-.43-4.14-1.8-5.32-3.69-1.18-1.89-1.46-4.32-.77-6.39.69-2.07 2.37-3.76 4.38-4.57 2.02-.81 4.38-.72 6.32.26v4.11c-1.61-.71-3.63-.49-4.99.71-1.36 1.2-1.76 3.29-.98 4.9.78 1.61 2.7 2.54 4.54 2.18 1.84-.36 3.19-2.03 3.23-3.92V.02h-1.17z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
          
          <!-- Col 2 -->
          <div class="lg:col-span-2">
            <h3 class="font-bold uppercase tracking-wider mb-2 text-sm">Empresa</h3>
            <div class="w-8 h-0.5 bg-white mb-4"></div>
            <ul class="space-y-3 text-white-200 text-sm">
              <li><Link href="/" class="hover:text-white transition-colors">Inicio</Link></li>
              <li><Link href="/catalog" class="hover:text-white transition-colors">Catálogo</Link></li>
              <li><Link href="/sobre-nosotros" class="hover:text-white transition-colors">Nosotros</Link></li>
              <li><Link href="/guide" class="hover:text-white transition-colors">Guía de Neumáticos</Link></li>
            </ul>
          </div>
          
          <!-- Col 3 -->
          <div class="lg:col-span-3 lg:border-l border-white/20 lg:pl-8">
            <h3 class="font-bold uppercase tracking-wider mb-2 text-sm">Atención al cliente</h3>
            <div class="w-8 h-0.5 bg-white mb-4"></div>
            <ul class="space-y-3 text-sm text-white-400">
              <li><Link href="/contacto" class="hover:text-white transition-colors inline-block w-fit">Contacto</Link></li>
              <li><a :href="'https://wa.me/' + usePage().props.global_whatsapp + '?text=' + encodeURIComponent('¡Hola! Estoy navegando en la web de Tersal y necesito ayuda o asesoría con un servicio/neumático.')" target="_blank" class="hover:text-white transition-colors inline-block w-fit">Soporte por WhatsApp</a></li>
              <li><a href="mailto:ventas@tersal.pe" class="hover:text-white transition-colors inline-block w-fit">ventas@llantastersal.com.pe</a></li>
            </ul>
          </div>
          
          <!-- Col 4 -->
          <div class="lg:col-span-3 lg:border-l border-white/20 lg:pl-8">
            <h3 class="font-bold uppercase tracking-wider mb-2 text-sm">Legal</h3>
            <div class="w-8 h-0.5 bg-white mb-4"></div>
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
    <div class="w-full z-20 py-4 relative">
      <!-- Línea divisoria que se corta antes de tocar la llanta -->
      <div class="absolute top-0 left-0 h-px bg-white/20 right-[35%] sm:right-[30%] md:right-[25%] lg:right-[18%] xl:right-[17.4%]"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
          <div class="flex justify-center md:justify-start order-2 md:order-1">
            <p class="text-xs text-white-400 font-medium tracking-wide">&copy; {{ new Date().getFullYear() }} TERSAL. Todos los derechos reservados.</p>
          </div>
          <div class="flex justify-center items-center gap-8 order-1 md:order-2">
            <div class="flex items-center justify-center">
              <img src="/images/VISA.png" alt="Visa" class="h-6 w-auto object-contain transition-transform hover:scale-110 brightness-0 invert" />
            </div>
            <div class="flex items-center justify-center">
              <img src="/images/mastercard_full.svg" alt="Mastercard" class="h-8 w-auto object-contain transition-transform hover:scale-110" />
            </div>
            <div class="flex items-center justify-center">
              <img src="/images/amex_text.svg" alt="American Express" class="h-6 w-auto object-contain transition-transform hover:scale-110" />
            </div>
            <div class="flex items-center justify-center">
              <img src="/images/diners_full.svg" alt="Diners Club" class="h-6 w-auto object-contain transition-transform hover:scale-110" />
            </div>
          </div>
          <div class="hidden md:block order-3"></div>
        </div>
      </div>
    </div>
  </footer>
</template>
