<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { Search, Info, CheckCircle2, MessageCircle, Truck } from 'lucide-vue-next';

defineProps({
  brands: Array,
  promotions: Array,
  widths: Array,
  profiles: Array,
  rims: Array
});

const activeTab = ref('medida');

const searchFilters = ref({
  width: '',
  profile: '',
  rim: ''
});

const searchByMeasure = () => {
  const params = {};
  if (searchFilters.value.width) params.width = searchFilters.value.width;
  if (searchFilters.value.profile) params.profile = searchFilters.value.profile;
  if (searchFilters.value.rim) params.rim = searchFilters.value.rim;
  router.get('/catalog', params);
};

const getDiscountPercentage = (price, offerPrice) => {
  if (!price || !offerPrice) return 0;
  return Math.round(((price - offerPrice) / price) * 100);
};
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Inicio" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow">
      <!-- Hero Section -->
      <section class="relative pt-20 pb-32">
        <div class="absolute inset-0 z-0">
          <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&q=80&w=1920" class="w-full h-full object-cover" alt="Tire Background">
          <div class="absolute inset-0 bg-white/75 backdrop-blur-[2px]"></div>
          <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
          <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight mb-4">
            Rendimiento y Precisión <br class="hidden md:block"/>
            <span class="text-action">en Cada Kilómetro.</span>
          </h1>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-12">
            Encuentra el ajuste perfecto para tu vehículo con nuestra búsqueda técnica avanzada. Marcas premium, instalación experta, sin complicaciones.
          </p>

          <!-- Finder Container -->
          <div class="max-w-4xl mx-auto p-2 sm:p-4 text-left">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
              <button @click="activeTab = 'medida'" :class="{'border-gray-900 text-gray-900': activeTab === 'medida', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'medida'}" class="flex-1 pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors text-center">
                Por Medida
              </button>
              <button @click="activeTab = 'vehiculo'" :class="{'border-gray-900 text-gray-900': activeTab === 'vehiculo', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'vehiculo'}" class="flex-1 pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors text-center">
                Por Vehículo
              </button>
            </div>

            <!-- Tab Content: Medida -->
            <div v-if="activeTab === 'medida'">
              <div class="flex justify-end mb-2">
                <Link href="/guide" class="text-action text-sm font-semibold flex items-center gap-1 hover:underline">
                  <Info class="w-4 h-4" /> ¿Dónde veo mi medida?
                </Link>
              </div>
              <form @submit.prevent="searchByMeasure">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Ancho</label>
                    <select v-model="searchFilters.width" class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-action focus:border-transparent text-gray-900">
                      <option value="">Todos</option>
                      <option v-for="w in widths" :key="w" :value="w">{{ w }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Perfil</label>
                    <select v-model="searchFilters.profile" class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-action focus:border-transparent text-gray-900">
                      <option value="">Todos</option>
                      <option v-for="p in profiles" :key="p" :value="p">{{ p }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Aro</label>
                    <select v-model="searchFilters.rim" class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-action focus:border-transparent text-gray-900">
                      <option value="">Todos</option>
                      <option v-for="r in rims" :key="r" :value="r">{{ r }}</option>
                    </select>
                  </div>
                </div>
                <button type="submit" class="w-full h-12 flex items-center justify-center gap-2 bg-action text-white font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md cursor-pointer">
                  <Search class="w-5 h-5" /> BUSCAR NEUMÁTICOS
                </button>
              </form>
            </div>

            <!-- Tab Content: Vehiculo -->
            <div v-if="activeTab === 'vehiculo'" class="text-center py-10">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <Search class="w-8 h-8 text-gray-400" />
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Búsqueda por vehículo</h3>
              <p class="text-gray-500 mb-6">Esta función estará disponible próximamente en nuestra nueva actualización, donde podrás vincular la marca y versión de tu auto a la medida exacta.</p>
              <button @click="activeTab = 'medida'" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded hover:bg-gray-200 transition-colors">
                Buscar por Medida
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Brands Marquee -->
      <section v-if="brands && brands.length > 0" class="py-10 bg-white border-b border-gray-100 overflow-hidden">
        <div class="marquee-container group flex w-full">
          <!-- We repeat the block enough times to fill any screen even with 1 or 2 brands -->
          <div v-for="n in 10" :key="n" class="marquee-content flex shrink-0 gap-12 pr-12 items-center justify-start min-w-max" :style="{ animationDuration: Math.max(brands.length * 4, 10) + 's' }">
            <Link :href="`/catalog?brand_id=${brand.id}`" v-for="brand in brands" :key="`${n}-${brand.id}`" class="shrink-0 flex items-center justify-center w-32 h-20 transition-all duration-300 opacity-70 hover:opacity-100 cursor-pointer">
              <img v-if="brand.logo_url" :src="'/storage/' + brand.logo_url" :alt="brand.name" class="max-w-full max-h-full object-contain grayscale hover:grayscale-0 transition-all duration-300" />
              <span v-else class="text-xl font-black text-gray-400 hover:text-gray-900 transition-colors">{{ brand.name }}</span>
            </Link>
          </div>
        </div>
      </section>

      <!-- Promociones -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black text-gray-900">Ofertas Especiales</h2>
            <Link href="/catalog" class="text-primary font-bold hover:underline">Ver todo el catálogo &rarr;</Link>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="tire in promotions" :key="tire.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col max-w-sm mx-auto w-full">
              <div class="relative pt-[85%] bg-gray-100">
                <img :src="tire.images_json && tire.images_json.length ? '/storage/'+tire.images_json[0] : 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=400&h=400'" class="absolute inset-0 w-full h-full object-cover" alt="Tire" />
                <div class="absolute top-2 left-2 bg-action text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                  OFERTA
                  <span class="bg-white text-action px-1 rounded text-[10px]">{{ getDiscountPercentage(tire.price, tire.offer_price) }}%</span>
                </div>
              </div>
              <div class="p-4 flex-grow flex flex-col">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ tire.brand?.name || 'Marca' }}</div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 leading-tight">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }} {{ tire.model }}</h3>
                <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                  <div>
                    <div class="text-xs text-gray-400 line-through">S/. {{ tire.price }}</div>
                    <div class="text-lg font-black text-gray-900 leading-none mt-1">S/. {{ tire.offer_price }}</div>
                  </div>
                  <a :href="'https://wa.me/' + $page.props.global_whatsapp + '?text=' + encodeURIComponent(`¡Hola! Estoy interesado en el neumático ${tire.brand?.name || ''} ${tire.model} en la medida ${tire.width}/${tire.profile} R${tire.rim} que vi en su web. Deseo confirmar el stock.`)" target="_blank" class="h-8 px-3 flex items-center justify-center bg-action text-white font-bold rounded hover:bg-red-700 transition-colors text-xs">
                    Ver más
                  </a>
                </div>
              </div>
            </div>
            <div v-if="!promotions || promotions.length === 0" class="col-span-full py-12 text-center text-gray-500">
              No hay promociones activas en este momento.
            </div>
          </div>
        </div>
      </section>

      <!-- Infographic -->
      <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-3xl font-black text-gray-900 mb-12">Cómo comprar por WhatsApp en 3 pasos</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <Search class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">1. Encuentra tu medida</h3>
              <p class="text-gray-600 text-sm">Usa nuestro buscador para ubicar el neumático exacto que necesita tu vehículo.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <MessageCircle class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">2. Consulta stock</h3>
              <p class="text-gray-600 text-sm">Haz clic en el botón rojo de WhatsApp. Te atenderemos en minutos.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <Truck class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">3. Coordina la entrega</h3>
              <p class="text-gray-600 text-sm">Paga de forma segura y recibe o instala tus neumáticos el mismo día.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.marquee-container:hover .marquee-content {
  animation-play-state: paused;
}
.marquee-content {
  animation: marquee 30s linear infinite;
}
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>
