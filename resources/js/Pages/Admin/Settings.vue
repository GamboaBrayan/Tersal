<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { Save, Plus, Trash2, MessageCircle, HelpCircle, ChevronUp, ChevronDown } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const props = defineProps({
  settings: Object
});

const activeTab = ref('contact');

let initialFaqs = [];
try {
  if (props.settings?.faqs) {
    initialFaqs = JSON.parse(props.settings.faqs);
  }
} catch (e) {
  initialFaqs = [];
}

const form = useForm({
  whatsapp_number: props.settings?.whatsapp_number || '',
  faqs: initialFaqs.length > 0 ? initialFaqs : [{ question: '', answer: '' }]
});

const addFaq = () => {
  form.faqs.push({ question: '', answer: '' });
};

const removeFaq = (index) => {
  form.faqs.splice(index, 1);
};

const moveUp = (index) => {
  if (index > 0) {
    const item = form.faqs[index];
    form.faqs.splice(index, 1);
    form.faqs.splice(index - 1, 0, item);
  }
};

const moveDown = (index) => {
  if (index < form.faqs.length - 1) {
    const item = form.faqs[index];
    form.faqs.splice(index, 1);
    form.faqs.splice(index + 1, 0, item);
  }
};

const submit = () => {
  form.post('/admin/settings', {
    preserveScroll: true
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Configuración Web" />
    
    <form @submit.prevent="submit" class="flex flex-col h-full">
      <div class="px-4 sm:px-8 py-6 sm:py-10 flex-grow">
        <div class="mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Configuración Web</h1>
          <p class="text-sm sm:text-base text-gray-500 mt-2">Gestiona la información pública de contacto y las preguntas frecuentes.</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 border-b border-gray-200 mb-6 sm:mb-8 overflow-x-auto whitespace-nowrap">
          <button 
            type="button"
            @click="activeTab = 'contact'"
            :class="[
              'pb-3 sm:pb-4 px-2 text-sm font-bold transition-colors border-b-2 -mb-[2px]',
              activeTab === 'contact' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2">
              <MessageCircle class="w-4 h-4" />
              Contacto y WhatsApp
            </div>
          </button>
          <button 
            type="button"
            @click="activeTab = 'faqs'"
            :class="[
              'pb-3 sm:pb-4 px-2 text-sm font-bold transition-colors border-b-2 -mb-[2px]',
              activeTab === 'faqs' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2">
              <HelpCircle class="w-4 h-4" />
              Preguntas Frecuentes
            </div>
          </button>
        </div>

        <!-- Tab Content 1: Contact -->
        <div v-show="activeTab === 'contact'" class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
          <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-1">Número Central de WhatsApp</label>
            <p class="text-[10px] sm:text-xs text-gray-500 mb-4">Este número se utilizará en todos los botones de "Chatear" y enlaces dinámicos de la web pública. Ingrese el código de país sin el '+' (ej. 51999999999).</p>
            
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <MessageCircle class="h-5 w-5 text-gray-400" />
              </div>
              <input type="text" v-model="form.whatsapp_number" class="w-full h-12 pl-12 pr-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent font-bold text-sm sm:text-base">
            </div>
            <div v-if="form.errors.whatsapp_number" class="text-red-500 text-xs mt-1">{{ form.errors.whatsapp_number }}</div>
          </div>
        </div>

        <!-- Tab Content 2: FAQs -->
        <div v-show="activeTab === 'faqs'" class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
            <div>
              <h2 class="text-lg font-bold text-gray-900">Guía de Neumáticos (FAQs)</h2>
              <p class="text-xs text-gray-500 mt-1">Configura las preguntas y respuestas que aparecerán en la sección pública de la guía.</p>
            </div>
            <button type="button" @click="addFaq" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 text-primary font-bold rounded-xl hover:bg-blue-100 transition-colors text-sm w-full sm:w-auto">
              <Plus class="w-4 h-4" /> Añadir Fila
            </button>
          </div>

          <div class="space-y-4 sm:space-y-6">
            <div v-for="(faq, index) in form.faqs" :key="index" class="p-4 sm:p-6 border border-gray-200 rounded-xl bg-gray-50 relative group transition-all hover:border-primary/30">
              <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="flex-grow space-y-4 w-full">
                  <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Pregunta</label>
                    <input type="text" v-model="faq.question" placeholder="Ej: ¿Cuándo debo cambiar mis llantas?" class="w-full h-10 px-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-primary focus:border-transparent font-bold text-gray-900 text-sm">
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Respuesta</label>
                    <textarea v-model="faq.answer" rows="3" placeholder="Ej: Se recomienda cambiarlas cuando la banda de rodadura esté por debajo de los 2mm..." class="w-full p-4 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm text-gray-700"></textarea>
                  </div>
                </div>
                <div class="flex flex-col gap-2 mt-2 sm:mt-6 w-full sm:w-10 flex-shrink-0">
                  <div class="flex gap-1 sm:flex-col">
                    <button type="button" @click="moveUp(index)" :disabled="index === 0" class="flex-1 h-8 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                      <ChevronUp class="w-4 h-4" />
                    </button>
                    <button type="button" @click="moveDown(index)" :disabled="index === form.faqs.length - 1" class="flex-1 h-8 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                      <ChevronDown class="w-4 h-4" />
                    </button>
                  </div>
                  <button type="button" @click="removeFaq(index)" class="h-10 bg-red-50 text-action rounded-lg flex items-center justify-center hover:bg-red-100 transition-colors">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
            <div v-if="form.faqs.length === 0" class="text-center p-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-xl text-sm sm:text-base">
              No hay preguntas configuradas. Haz clic en "Añadir Fila".
            </div>
          </div>
          
        </div>
      </div>

      <!-- Sticky Bottom Bar -->
      <div class="bg-white border-t border-gray-200 px-4 sm:px-8 py-4 sticky bottom-0 z-10 flex justify-end gap-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 h-12 px-6 sm:px-8 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto text-sm sm:text-base">
          <Save class="w-5 h-5" /> Guardar Configuración
        </button>
      </div>
    </form>
  </AdminLayout>
</template>
