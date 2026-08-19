<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ShieldCheck, Info, ChevronLeft, Send, CheckCircle2 } from 'lucide-vue-next';
import axios from 'axios';

const form = ref({
  es_menor: false,
  nombre_apoderado: '',
  tipo_documento: 'DNI',
  numero_documento: '',
  nombres: '',
  apellidos: '',
  telefono: '',
  email: '',
  direccion: '',
  distrito: '',
  tipo_bien: 'producto',
  monto_reclamado: '',
  descripcion_bien: '',
  tipo_reclamo: 'reclamo',
  detalle: '',
  pedido: '',
  acepta_privacidad: false
});

const isSubmitted = ref(false);
const isSubmitting = ref(false);
const generatedId = ref('');
const errorMessage = ref('');

const submitForm = async () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  errorMessage.value = '';
  
  try {
    const response = await axios.post('/libro-reclamaciones', form.value);
    if (response.data.success) {
      generatedId.value = response.data.generated_id;
      isSubmitted.value = true;
    }
  } catch (error) {
    console.error(error);
    errorMessage.value = error.response?.data?.error || 'Ocurrió un error al enviar el formulario. Por favor, intenta nuevamente.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Head title="Libro de Reclamaciones - TERSAL" />

    <!-- Banner Principal -->
    <div class="bg-[#1B3BE3] text-white py-16 px-4 relative overflow-hidden shrink-0">
      <div class="absolute inset-0 bg-[url('/images/pattern.png')] opacity-10"></div>
      <div class="max-w-4xl mx-auto relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-black mb-4 uppercase tracking-tight">Libro de Reclamaciones</h1>
        <p class="text-white-200 text-sm md:text-base max-w-2xl mx-auto">
          Conforme a lo establecido en el Código de Protección y Defensa del Consumidor, esta institución cuenta con un Libro de Reclamaciones virtual a tu disposición para reportar cualquier inconveniente.
        </p>
      </div>
    </div>

    <!-- Contenido -->
    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 -mt-8 relative z-20 pb-20">
      <div class="max-w-4xl mx-auto">
        
        <!-- Estado de Éxito -->
        <div v-if="isSubmitted" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-10 text-center flex flex-col items-center justify-center min-h-[400px]">
          <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
            <CheckCircle2 class="w-10 h-10" />
          </div>
          <h2 class="text-3xl font-black text-gray-900 mb-2">¡Hoja de Reclamación Enviada!</h2>
          <div class="bg-gray-50 border border-gray-200 px-6 py-4 rounded-xl mb-6">
            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Código de Seguimiento</p>
            <p class="text-2xl font-black text-primary tracking-widest">{{ generatedId }}</p>
          </div>
          <p class="text-gray-500 mb-8 max-w-md">Hemos recibido tu solicitud. Se ha enviado una copia del reclamo a tu correo electrónico. Nos pondremos en contacto contigo en un plazo máximo de 15 días hábiles.</p>
          <button @click="isSubmitted = false; form.nombres = ''; form.apellidos = ''; form.numero_documento = ''; form.detalle = ''; form.monto_reclamado = ''; form.descripcion_bien = ''; form.acepta_privacidad = false;" class="text-primary font-bold hover:underline">
            Enviar otro reclamo
          </button>
        </div>

        <!-- Formulario -->
        <div v-else class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          
          <div class="p-6 md:p-10 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-xl font-bold text-gray-800">Hoja de Reclamación</h2>
            <p class="text-sm text-gray-500 mt-2">Por favor, completa el siguiente formulario con información veraz y detallada. Todos los campos con asterisco (<span class="text-red-500">*</span>) son obligatorios.</p>
          </div>

          <form @submit.prevent="submitForm" class="p-6 md:p-10">
            
            <!-- SECCIÓN 1: Consumidor -->
            <div class="mb-12">
              <div class="flex items-center gap-3 mb-6 pb-2 border-b-2 border-gray-100">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold text-sm">1</span>
                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Identificación del Consumidor</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Check menor de edad -->
                <div class="md:col-span-2 mb-2">
                  <label class="flex items-start gap-3 cursor-pointer p-4 border border-blue-100 bg-blue-50/50 rounded-xl hover:bg-blue-50 transition-colors">
                    <input type="checkbox" v-model="form.es_menor" class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                    <div>
                      <span class="block text-sm font-bold text-gray-800">Soy menor de edad</span>
                      <span class="block text-xs text-gray-500 mt-1">Si marcas esta casilla, deberás ingresar los datos de tu padre, madre o apoderado legal.</span>
                    </div>
                  </label>
                </div>

                <div v-if="form.es_menor" class="md:col-span-2 p-4 bg-gray-50 border border-gray-200 rounded-xl mb-4">
                  <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Nombre Completo del Apoderado <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.nombre_apoderado" :required="form.es_menor" placeholder="Ej: Juan Pérez Díaz" class="w-full h-12 px-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary text-gray-800">
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nombres <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.nombres" required placeholder="Tus nombres" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Apellidos <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.apellidos" required placeholder="Tus apellidos" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tipo de Documento <span class="text-red-500">*</span></label>
                  <select v-model="form.tipo_documento" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                    <option value="DNI">DNI (Documento Nacional de Identidad)</option>
                    <option value="CE">CE (Carné de Extranjería)</option>
                    <option value="Pasaporte">Pasaporte</option>
                    <option value="RUC">RUC</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">N° Documento <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.numero_documento" required placeholder="Ej: 71234567" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dirección de Domicilio <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.direccion" required placeholder="Ej: Av. Javier Prado Este 123, Dpto 401" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Distrito / Provincia / Región <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.distrito" required placeholder="Ej: San Borja, Lima, Lima" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Teléfono / Celular <span class="text-red-500">*</span></label>
                  <input type="tel" v-model="form.telefono" required placeholder="Ej: 987654321" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                </div>
                
                <div class="md:col-span-2">
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Correo Electrónico <span class="text-red-500">*</span></label>
                  <input type="email" v-model="form.email" required placeholder="ejemplo@correo.com" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                  <p class="text-xs text-gray-400 mt-1 flex items-center gap-1"><Info class="w-3 h-3" /> A este correo enviaremos la copia y respuesta de tu reclamo.</p>
                </div>
              </div>
            </div>

            <!-- SECCIÓN 2: Bien Contratado -->
            <div class="mb-12">
              <div class="flex items-center gap-3 mb-6 pb-2 border-b-2 border-gray-100">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold text-sm">2</span>
                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Identificación del Bien Contratado</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjetas Tipo de Bien -->
                <div class="md:col-span-2">
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">¿Sobre qué trata el reclamo? <span class="text-red-500">*</span></label>
                  <div class="grid grid-cols-2 gap-4">
                    <label :class="['flex items-center justify-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all', form.tipo_bien === 'producto' ? 'border-primary bg-blue-50/30' : 'border-gray-200 hover:border-gray-300 bg-white']">
                      <input type="radio" v-model="form.tipo_bien" value="producto" class="hidden">
                      <div :class="['w-4 h-4 rounded-full border flex items-center justify-center', form.tipo_bien === 'producto' ? 'border-primary' : 'border-gray-300']">
                        <div v-if="form.tipo_bien === 'producto'" class="w-2 h-2 rounded-full bg-primary"></div>
                      </div>
                      <span class="font-bold text-gray-800">Producto (Llantas, etc.)</span>
                    </label>
                    <label :class="['flex items-center justify-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all', form.tipo_bien === 'servicio' ? 'border-primary bg-blue-50/30' : 'border-gray-200 hover:border-gray-300 bg-white']">
                      <input type="radio" v-model="form.tipo_bien" value="servicio" class="hidden">
                      <div :class="['w-4 h-4 rounded-full border flex items-center justify-center', form.tipo_bien === 'servicio' ? 'border-primary' : 'border-gray-300']">
                        <div v-if="form.tipo_bien === 'servicio'" class="w-2 h-2 rounded-full bg-primary"></div>
                      </div>
                      <span class="font-bold text-gray-800">Servicio (Instalación, etc.)</span>
                    </label>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Monto Reclamado (S/) <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">S/</span>
                    <input type="number" step="0.01" v-model="form.monto_reclamado" required placeholder="0.00" class="w-full h-12 pl-10 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 transition-colors">
                  </div>
                </div>
                
                <div class="md:col-span-2">
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Descripción del Producto o Servicio <span class="text-red-500">*</span></label>
                  <textarea v-model="form.descripcion_bien" rows="2" required placeholder="Ej: Compra de 4 llantas Michelin Aro 15 con orden de compra #12345" class="w-full p-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 resize-none transition-colors"></textarea>
                </div>
              </div>
            </div>

            <!-- SECCIÓN 3: Reclamo -->
            <div class="mb-12">
              <div class="flex items-center gap-3 mb-6 pb-2 border-b-2 border-gray-100">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold text-sm">3</span>
                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Detalle de la Reclamación</h3>
              </div>
              
              <div class="grid grid-cols-1 gap-6">
                <!-- Tarjetas Tipo Reclamo -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tipo de Reclamación <span class="text-red-500">*</span></label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label :class="['relative p-5 rounded-xl border-2 cursor-pointer transition-all flex items-start gap-3', form.tipo_reclamo === 'reclamo' ? 'border-action bg-red-50/30' : 'border-gray-200 hover:border-gray-300 bg-white']">
                      <input type="radio" v-model="form.tipo_reclamo" value="reclamo" class="hidden">
                      <div :class="['w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center shrink-0', form.tipo_reclamo === 'reclamo' ? 'border-action' : 'border-gray-300']">
                        <div v-if="form.tipo_reclamo === 'reclamo'" class="w-2.5 h-2.5 rounded-full bg-action"></div>
                      </div>
                      <div>
                        <span class="block font-black text-gray-900 mb-1">RECLAMO</span>
                        <span class="text-xs text-gray-500 leading-snug">Disconformidad relacionada directamente a los productos adquiridos o servicios prestados.</span>
                      </div>
                    </label>

                    <label :class="['relative p-5 rounded-xl border-2 cursor-pointer transition-all flex items-start gap-3', form.tipo_reclamo === 'queja' ? 'border-action bg-red-50/30' : 'border-gray-200 hover:border-gray-300 bg-white']">
                      <input type="radio" v-model="form.tipo_reclamo" value="queja" class="hidden">
                      <div :class="['w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center shrink-0', form.tipo_reclamo === 'queja' ? 'border-action' : 'border-gray-300']">
                        <div v-if="form.tipo_reclamo === 'queja'" class="w-2.5 h-2.5 rounded-full bg-action"></div>
                      </div>
                      <div>
                        <span class="block font-black text-gray-900 mb-1">QUEJA</span>
                        <span class="text-xs text-gray-500 leading-snug">Malestar o descontento respecto a la atención al público (trato, demoras, etc). No ligado al producto en sí.</span>
                      </div>
                    </label>
                  </div>
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Detalle de lo sucedido <span class="text-red-500">*</span></label>
                  <textarea v-model="form.detalle" rows="5" required placeholder="Explica detalladamente qué sucedió, fechas, lugares, personal involucrado..." class="w-full p-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 resize-none transition-colors"></textarea>
                  <p class="text-xs text-gray-400 mt-1 flex items-center gap-1"><Info class="w-3 h-3" /> Sé lo más específico posible para poder ayudarte mejor.</p>
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pedido o solución esperada (Opcional)</label>
                  <textarea v-model="form.pedido" rows="3" placeholder="¿Qué acción esperas que tomemos al respecto?" class="w-full p-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary text-gray-800 resize-none transition-colors"></textarea>
                </div>
              </div>
            </div>

            <!-- Políticas -->
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 mb-8">
              <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" v-model="form.acepta_privacidad" required class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-sm text-gray-600 leading-relaxed">
                  Declaro que la información proporcionada es veraz y exacta. Acepto el <a href="/privacidad" target="_blank" class="text-primary hover:underline font-bold">tratamiento de mis datos personales</a> para la gestión y respuesta de este reclamo/queja conforme a la Ley N° 29733.<br>
                  <span class="block mt-2 text-xs text-gray-500">
                    * La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.<br>
                    * El proveedor deberá dar respuesta al reclamo en un plazo no mayor a quince (15) días hábiles improrrogables.
                  </span>
                </span>
              </label>
            </div>

            <!-- Botón Submit -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100">
              <Link href="/" class="flex items-center gap-2 text-gray-500 hover:text-gray-800 font-bold transition-colors">
                <ChevronLeft class="w-5 h-5" /> Volver al Inicio
              </Link>
              
              <div class="flex flex-col items-end gap-2 w-full sm:w-auto">
                <div v-if="errorMessage" class="text-sm font-bold text-red-500 bg-red-50 px-4 py-2 rounded-lg w-full text-center sm:text-right">
                  {{ errorMessage }}
                </div>
                <button type="submit" :disabled="!form.acepta_privacidad || isSubmitting" class="w-full sm:w-auto px-10 py-4 bg-action text-white font-black uppercase tracking-wider rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 hover:bg-red-700 transition-all focus:outline-none focus:ring-4 focus:ring-red-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                  <span v-if="isSubmitting">Enviando...</span>
                  <span v-else>Enviar Formulario</span>
                  <Send v-if="!isSubmitting" class="w-5 h-5" />
                </button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </main>
  </div>
</template>
