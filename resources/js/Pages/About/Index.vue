<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { 
  ShieldCheck, 
  Compass, 
  Award, 
  Headphones, 
  CheckCircle, 
  Sparkles,
  ArrowRight
} from 'lucide-vue-next';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <Head title="Sobre Nosotros - Tersal" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow">
      <!-- 1. HERO SECTION (Fondo oscuro con técnico / taller y texto lateral) -->
      <section class="relative bg-black text-white min-h-[420px] md:min-h-[500px] flex items-center overflow-hidden">
        <!-- Imagen de fondo con opacidad y overlay degradado -->
        <img 
          src="/images/about-workshop.jpg" 
          alt="Tersal Taller y Neumáticos" 
          class="absolute inset-0 w-full h-full object-cover object-center opacity-30 md:opacity-40 filter saturate-120"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/85 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10 w-full">
          <div class="max-w-2xl space-y-5">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
              TERSAL
            </h1>
            <p class="text-gray-100 text-sm sm:text-base leading-relaxed font-semibold">
              Centro especializado en la distribución, venta y asesoría técnica de neumáticos para todo tipo de vehículos: ligeros, SUVs, comerciales y camionetas 4x4.
            </p>
            <p class="text-gray-200 text-sm sm:text-base leading-relaxed font-semibold">
              Trabajamos con las marcas más prestigiosas a nivel internacional, garantizando productos 100% originales con altos estándares de seguridad, durabilidad y rendimiento en pista y trocha.
            </p>
            <p class="text-gray-200 text-sm sm:text-base leading-relaxed font-semibold">
              Nuestro objetivo es brindarte una experiencia ágil y transparente, ayudándote a tomar la mejor decisión de compra con asesoría experta y atención personalizada en cada consulta.
            </p>
          </div>
        </div>
      </section>

      <!-- 2. QUIÉNES SOMOS SECTION (Texto a la izquierda + imagen redondeada a la derecha + patrón de puntos) -->
      <section class="py-16 md:py-24 bg-white relative overflow-hidden">
        <!-- Fondo de trama sutil con puntos -->
        <div class="absolute inset-0 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:20px_20px] opacity-40 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Columna Izquierda: Contenido -->
            <div class="lg:col-span-6 space-y-6">
              <span class="text-action font-extrabold uppercase tracking-widest text-xs">
                Quiénes Somos
              </span>
              <h2 class="text-3xl sm:text-4xl font-extrabold text-black leading-tight">
                En Tersal creemos que el servicio y asesoramiento automotriz puede y debe ser diferente.
              </h2>
              <p class="text-black text-sm sm:text-base leading-relaxed font-normal">
                Somos un equipo especializado en neumáticos y asesoría automotriz, comprometidos en brindar atención transparente, puntual y de calidad. Para nosotros, cada cliente merece sentirse seguro, informado y bien atendido en todo momento.
              </p>
              <p class="text-black text-sm sm:text-base leading-relaxed font-normal">
                Por eso unimos tecnología, profesionalismo y un trato humano cercano para que cuidar y equipar tu auto sea simple, rápido y sin preocupaciones.
              </p>
              
              <div class="pt-2">
                <Link 
                  href="/contacto" 
                  class="inline-flex items-center gap-2 px-6 py-3 bg-action hover:bg-red-700 text-white font-bold text-sm rounded-lg shadow-sm hover:shadow transition-all duration-200"
                >
                  Contáctanos
                </Link>
              </div>
            </div>

            <!-- Columna Derecha: Imagen del taller con esquinas redondeadas -->
            <div class="lg:col-span-6">
              <div class="relative mx-auto max-w-lg lg:max-w-none rounded-2xl overflow-hidden shadow-xl border border-gray-200 group">
                <img 
                  src="/images/about-workshop.jpg" 
                  alt="Servicio Automotriz Tersal" 
                  class="w-full h-[320px] sm:h-[400px] object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                />
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- 3. PILARES: MISIÓN, VISIÓN Y VALORES (Cards limpias sin iconos) -->
      <section class="pb-16 md:pb-24 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Card Misión -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col text-center justify-start">
              <h3 class="text-xl font-bold text-black mb-4">Misión</h3>
              <p class="text-black text-sm leading-relaxed font-normal">
                Ofrecer un servicio confiable, transparente y eficiente que priorice la seguridad del vehículo y la tranquilidad del cliente, entregando soluciones precisas y una experiencia de atención superior.
              </p>
            </div>

            <!-- Card Visión -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col text-center justify-start">
              <h3 class="text-xl font-bold text-black mb-4">Visión</h3>
              <p class="text-black text-sm leading-relaxed font-normal">
                Ser la distribuidora y tienda especializada en neumáticos más recomendada del país por transformar la manera en que las personas compran e instalan sus llantas: con confianza, comodidad y cero estrés.
              </p>
            </div>

            <!-- Card Valores -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col text-center justify-start">
              <h3 class="text-xl font-bold text-black mb-4">Valores</h3>
              <ul class="text-black text-sm space-y-2 text-center font-normal">
                <li>• Honestidad y transparencia</li>
                <li>• Excelencia en productos</li>
                <li>• Puntualidad y rapidez</li>
                <li>• Empatía con el conductor</li>
                <li>• Innovación continua</li>
              </ul>
            </div>

          </div>
        </div>
      </section>

      <!-- 4. NUESTRA PROMESA (Auto con punteros / viñetas a la derecha) -->
      <section class="py-16 md:py-24 bg-gray-50 border-t border-gray-200/80 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <!-- Encabezado de sección -->
          <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-black tracking-tight mb-3">
              Nuestra Promesa
            </h2>
            <p class="text-black text-sm sm:text-base font-normal max-w-2xl mx-auto">
              Explicarte todo lo que ocurre con tu auto, mostrarte los avances y entregarte soluciones claras, para que siempre tomes decisiones con confianza.
            </p>
          </div>

          <!-- Contenedor Ilustración + Items de promesa -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <!-- Lado Izquierdo: Auto -->
            <div class="lg:col-span-7 flex justify-center items-center">
              <div class="relative w-full max-w-lg lg:max-w-xl">
                <img 
                  src="/images/about-car-promise.jpg" 
                  alt="Vehículo de Nuestra Promesa" 
                  class="w-full h-auto object-contain drop-shadow-md rounded-2xl"
                />
              </div>
            </div>

            <!-- Lado Derecho: 3 Puntos con guías y badges -->
            <div class="lg:col-span-5 space-y-8">
              
              <!-- Item 1 -->
              <div class="flex items-center gap-4 group">
                <div class="hidden sm:block w-12 h-0.5 bg-gray-300 relative">
                  <div class="absolute -top-1 right-0 w-2.5 h-2.5 rounded-full bg-action"></div>
                </div>
                <div class="flex items-center gap-3 bg-white px-5 py-4 rounded-xl border border-gray-200 shadow-sm flex-1 group-hover:border-action/40 group-hover:shadow transition-all">
                  <div class="w-10 h-10 rounded-lg bg-red-50 text-action flex items-center justify-center flex-shrink-0">
                    <Headphones class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="text-base font-bold text-black">Servicio personalizado</h4>
                    <p class="text-xs text-black font-normal">Atención directa para responder tus dudas con rapidez.</p>
                  </div>
                </div>
              </div>

              <!-- Item 2 -->
              <div class="flex items-center gap-4 group">
                <div class="hidden sm:block w-12 h-0.5 bg-gray-300 relative">
                  <div class="absolute -top-1 right-0 w-2.5 h-2.5 rounded-full bg-action"></div>
                </div>
                <div class="flex items-center gap-3 bg-white px-5 py-4 rounded-xl border border-gray-200 shadow-sm flex-1 group-hover:border-action/40 group-hover:shadow transition-all">
                  <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <CheckCircle class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="text-base font-bold text-black">Calidad y confiabilidad</h4>
                    <p class="text-xs text-black font-normal">Marcas originales y garantía en todos nuestros productos.</p>
                  </div>
                </div>
              </div>

              <!-- Item 3 -->
              <div class="flex items-center gap-4 group">
                <div class="hidden sm:block w-12 h-0.5 bg-gray-300 relative">
                  <div class="absolute -top-1 right-0 w-2.5 h-2.5 rounded-full bg-action"></div>
                </div>
                <div class="flex items-center gap-3 bg-white px-5 py-4 rounded-xl border border-gray-200 shadow-sm flex-1 group-hover:border-action/40 group-hover:shadow transition-all">
                  <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <Sparkles class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="text-base font-bold text-black">Asesoría especializada</h4>
                    <p class="text-xs text-black font-normal">Orientación técnica para la medida y propósito de tu vehículo.</p>
                  </div>
                </div>
              </div>

            </div>

          </div>

        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>
