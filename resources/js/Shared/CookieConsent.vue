<script setup>
import { ref, onMounted } from 'vue';
import { useCookieConsent } from '../Composables/useCookieConsent';
import { X, Check, Settings2, Info } from 'lucide-vue-next';

const { showBanner, checkConsent, acceptAll, rejectNonEssential, saveConsent } = useCookieConsent();
const showSettings = ref(false);

const localPreferences = ref({
  analytics: false,
  marketing: false,
});

onMounted(() => {
  checkConsent();
});

const openSettings = () => {
  showSettings.value = true;
};

const closeSettings = () => {
  showSettings.value = false;
};

const handleSavePreferences = () => {
  saveConsent(localPreferences.value);
  showSettings.value = false;
};
</script>

<template>
  <!-- Main Banner -->
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="transform translate-y-full opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="transform translate-y-0 opacity-100"
    leave-to-class="transform translate-y-full opacity-0"
  >
    <div v-if="showBanner && !showSettings" class="fixed bottom-0 left-0 right-0 z-[100] p-4 pointer-events-none">
      <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] border border-gray-100 p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-6 pointer-events-auto">
        
        <div class="flex-1">
          <h3 class="text-sm md:text-base font-bold text-gray-900 flex items-center gap-2 mb-1">
            <Info class="w-4 h-4 text-action" />
            Valoramos tu privacidad
          </h3>
          <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
            Utilizamos cookies para mejorar tu experiencia de navegación, ofrecer anuncios o contenido personalizado y analizar nuestro tráfico. Al hacer clic en "Aceptar todas", aceptas nuestro uso de cookies.
          </p>
        </div>

        <div class="flex flex-col md:flex-row w-full md:w-auto gap-2 shrink-0">
          <button @click="openSettings" class="w-full md:w-auto px-4 py-2 text-xs md:text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center justify-center gap-1.5">
            <Settings2 class="w-4 h-4" />
            Personalizar
          </button>
          <button @click="rejectNonEssential" class="w-full md:w-auto px-4 py-2 text-xs md:text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-center">
            Rechazar no esenciales
          </button>
          <button @click="acceptAll" class="w-full md:w-auto px-4 py-2 text-xs md:text-sm font-bold text-white bg-action hover:bg-red-700 rounded-lg transition-colors shadow-sm text-center">
            Aceptar todas
          </button>
        </div>

      </div>
    </div>
  </Transition>

  <!-- Settings Panel (Expanded Banner) -->
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="transform translate-y-full opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="transform translate-y-0 opacity-100"
    leave-to-class="transform translate-y-full opacity-0"
  >
    <div v-if="showBanner && showSettings" class="fixed bottom-0 left-0 right-0 z-[100] p-0 md:p-4 pointer-events-none">
      <div class="w-full max-w-3xl mx-auto bg-white md:rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.15)] border-t md:border border-gray-100 pointer-events-auto max-h-[90vh] flex flex-col">
        
        <div class="p-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white md:rounded-t-2xl z-10">
          <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <Settings2 class="w-5 h-5 text-action" />
            Preferencias de Privacidad
          </h3>
          <button @click="closeSettings" class="text-gray-400 hover:text-gray-600 transition-colors p-1 bg-gray-50 hover:bg-gray-100 rounded-full">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-5">
          <!-- Necessary -->
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-bold text-gray-900">Cookies estrictamente necesarias</h4>
              <p class="text-xs text-gray-500 mt-1">Estas cookies son necesarias para que el sitio web funcione y no se pueden desactivar en nuestros sistemas. Usualmente están configuradas para responder a acciones hechas por usted.</p>
            </div>
            <div class="relative inline-flex items-center cursor-not-allowed">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-action rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all opacity-70"></div>
            </div>
          </div>

          <!-- Analytics -->
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-bold text-gray-900">Cookies de rendimiento y análisis</h4>
              <p class="text-xs text-gray-500 mt-1">Estas cookies nos permiten contar las visitas y fuentes de tráfico para poder evaluar y mejorar el rendimiento de nuestro sitio. Nos ayudan a saber qué páginas son las más o menos populares.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0">
              <input type="checkbox" v-model="localPreferences.analytics" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
            </label>
          </div>

          <!-- Marketing -->
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-bold text-gray-900">Cookies de marketing dirigida</h4>
              <p class="text-xs text-gray-500 mt-1">Estas cookies pueden estar en todo el sitio web, colocadas por nuestros socios publicitarios. Estos negocios pueden utilizarlas para crear un perfil de sus intereses y mostrarle anuncios relevantes.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0">
              <input type="checkbox" v-model="localPreferences.marketing" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
            </label>
          </div>
        </div>

        <div class="p-5 border-t border-gray-100 bg-gray-50 md:rounded-b-2xl flex flex-col sm:flex-row gap-3 sm:justify-end sticky bottom-0">
          <button @click="rejectNonEssential" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors w-full sm:w-auto text-center">
            Rechazar todas
          </button>
          <button @click="handleSavePreferences" class="px-5 py-2.5 text-sm font-bold text-white bg-gray-900 hover:bg-black rounded-xl transition-colors shadow-sm w-full sm:w-auto text-center flex items-center justify-center gap-2">
            <Check class="w-4 h-4" />
            Guardar preferencias
          </button>
        </div>

      </div>
    </div>
  </Transition>
</template>
