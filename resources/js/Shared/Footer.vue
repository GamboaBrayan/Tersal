<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Facebook, Instagram } from 'lucide-vue-next';
</script>

<template>
  <footer class="bg-[#1B3BE3] text-white pt-16 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Col 1 -->
        <div>
          <div class="flex items-center gap-2 mb-6">
            <img src="/images/logo2.png" alt="Tersal Logo" class="h-16 w-auto object-contain px-4 py-2 rounded-xl" />
          </div>
          <p class="text-white-200 text-sm mb-4">Encuentra el ajuste perfecto para tu vehículo con nuestra búsqueda técnica avanzada. Marcas premium, instalación experta, sin complicaciones.</p>
          <p class="text-xs text-white-300">&copy; {{ new Date().getFullYear() }} TERSAL. Todos los derechos reservados.</p>
        </div>
        
        <!-- Col 2 -->
        <div>
          <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Empresa</h3>
          <ul class="space-y-3 text-white-200 text-sm">
            <li><Link href="/contacto" class="hover:text-white transition-colors">Sobre Nosotros</Link></li>
            <li>
              <div class="flex items-center gap-4 mt-2">
                <a href="https://www.facebook.com/LlantasTersal/" target="_blank" class="hover:text-white transition-colors"><Facebook class="w-5 h-5" /></a>
                <a href="https://www.instagram.com/llantas.tersal/" target="_blank" class="hover:text-white transition-colors"><Instagram class="w-5 h-5" /></a>
              </div>
            </li>
          </ul>
        </div>
        
        <!-- Col 3 -->
        <div>
          <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Soporte</h3>
          <ul class="space-y-3 text-sm text-white-400">
            <li><a :href="'https://wa.me/' + usePage().props.global_whatsapp + '?text=' + encodeURIComponent('¡Hola! Estoy navegando en la web de Tersal y necesito ayuda o asesoría con un servicio/neumático.')" target="_blank" class="hover:text-white transition-colors flex items-center gap-2">Soporte por WhatsApp</a></li>
            <li><a href="mailto:ventas@tersal.pe" class="hover:text-white transition-colors flex items-center gap-2">ventas@tersal.pe</a></li>
          </ul>
        </div>
        
        <!-- Col 4 -->
        <div>
          <h3 class="font-bold uppercase tracking-wider mb-4 text-sm">Legal</h3>
          <ul class="space-y-3 text-gray-200 text-sm">
            <li><a href="/legales" target="_blank" class="hover:text-white transition-colors">Enlaces Legales</a></li>
            <li><a href="/privacidad" target="_blank" class="hover:text-white transition-colors">Política de Privacidad</a></li>
          </ul>
        </div>
      </div>
      
    </div>
  </footer>
</template>
