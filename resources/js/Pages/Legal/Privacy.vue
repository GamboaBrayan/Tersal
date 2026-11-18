<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';

const page = usePage();
const company = computed(() => page.props.company || {
  legal_name: 'Tersal Performance S.A.C.',
  ruc: '20600000000',
  address: 'Jr. Tacna 1150, Magdalena del Mar, Lima - Perú',
  email: 'ventas@llantastersal.com.pe',
  phone: '+51 904830422',
});

const updatedAt = new Date().toLocaleDateString('es-PE', { year: 'numeric', month: 'long' });
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <Head title="Términos, Condiciones y Política de Privacidad" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 text-gray-700 leading-relaxed">
      <div class="border-b border-gray-200 pb-6 mb-10">
        <p class="text-xs font-bold uppercase tracking-widest text-action mb-2">Información Legal</p>
        <h1 class="text-3xl sm:text-4xl font-black text-gray-900">Términos y Condiciones &amp; Política de Privacidad</h1>
        <p class="text-sm text-gray-500 mt-2">Última actualización: {{ updatedAt }}</p>
        <div class="flex flex-wrap gap-4 mt-4 text-sm font-semibold">
          <a href="#terminos" class="text-primary hover:underline">1. Términos y Condiciones</a>
          <span class="text-gray-300">|</span>
          <a href="#privacidad" class="text-primary hover:underline">2. Política de Privacidad</a>
        </div>
      </div>

      <!-- SECCIÓN 1: TÉRMINOS Y CONDICIONES -->
      <article id="terminos" class="space-y-6 scroll-mt-24">
        <h2 class="text-2xl font-black text-gray-900">1. Términos y Condiciones de Uso</h2>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.1 Datos Generales del Proveedor</h3>
          <p>
            El presente sitio web es operado por <strong>{{ company.legal_name }}</strong> (en adelante, <strong>TERSAL</strong>), identificada con RUC N° <strong>{{ company.ruc }}</strong>, con domicilio en <strong>{{ company.address }}</strong>, teléfono <strong>{{ company.phone }}</strong> y correo de contacto <strong>{{ company.email }}</strong>. El acceso y uso de este portal implica la aceptación de los presentes Términos y Condiciones.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.2 Naturaleza del Catálogo y Proceso de Compra vía WhatsApp</h3>
          <p>
            Este sitio web funciona como un catálogo informativo y herramienta de asesoría técnica para la búsqueda de neumáticos por medida o por vehículo. Actualmente, <strong>las cotizaciones, confirmación de stock, coordinación de pago y programación de entrega o instalación se realizan de forma directa y personalizada a través de nuestra línea oficial de WhatsApp</strong>.
          </p>
          <ul class="list-disc pl-6 space-y-1.5">
            <li>La visualización de un producto en el catálogo no constituye una reserva automática ni obligación de venta hasta que un asesor confirme la disponibilidad real en almacén.</li>
            <li>La compra se formaliza únicamente tras la confirmación de la cotización y la emisión del comprobante de pago correspondiente (boleta o factura electrónica).</li>
          </ul>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.3 Precios, Moneda e Impuestos</h3>
          <p>
            Los precios promocionales mostrados en el sitio web se expresan en <strong>Soles (S/)</strong> o <strong>Dólares Americanos (USD)</strong> según se indique en cada producto, e incluyen el Impuesto General a las Ventas (IGV). TERSAL se reserva el derecho de actualizar precios, promociones y condiciones comerciales sin previo aviso; sin embargo, se respetará el precio confirmado en una cotización vigente aceptada por el cliente.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.4 Instalación, Despacho y Medios de Pago</h3>
          <ul class="list-disc pl-6 space-y-1.5">
            <li><strong>Instalación en talleres autorizados:</strong> los beneficios de instalación gratuita aplican exclusivamente en los talleres autorizados y horarios coordinados previamente con nuestro equipo comercial.</li>
            <li><strong>Envíos:</strong> los despachos a Lima Metropolitana y provincias se coordinan vía WhatsApp según cobertura, peso y volumen del pedido.</li>
            <li><strong>Medios de pago:</strong> aceptamos transferencias bancarias y tarjetas de crédito o débito (Visa, Mastercard, American Express y Diners Club) mediante los canales habilitados por el asesor.</li>
          </ul>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.5 Garantía de Fábrica y Cambios</h3>
          <p>
            Todos los neumáticos comercializados cuentan con garantía por defectos de fabricación conforme a las políticas de cada marca y fabricante (mínimo 3 meses o según especificación del producto). La garantía no cubre daños ocasionados por golpes, cortes, pinchazos, presión de inflado inadecuada, desalineamiento del vehículo o uso indebido.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.6 Propiedad Intelectual y Buscador por Vehículo</h3>
          <p>
            Las marcas, logotipos, textos e imágenes exhibidos pertenecen a TERSAL o a sus respectivos fabricantes. La información de compatibilidad vehicular es referencial; recomendamos verificar siempre la medida inscrita en el flanco del neumático actual o en el manual del fabricante de tu vehículo antes de confirmar el pedido.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">1.7 Libro de Reclamaciones y Legislación Aplicable</h3>
          <p>
            De conformidad con el Código de Protección y Defensa del Consumidor (Ley N° 29571), ponemos a tu disposición nuestro <Link href="/libro-reclamaciones" class="text-action font-semibold hover:underline">Libro de Reclamaciones Virtual</Link>. Estos Términos y Condiciones se rigen por las leyes de la República del Perú.
          </p>
        </section>
      </article>

      <!-- SECCIÓN 2: POLÍTICA DE PRIVACIDAD -->
      <article id="privacidad" class="space-y-6 border-t border-gray-200 pt-12 mt-12 scroll-mt-24">
        <h2 class="text-2xl font-black text-gray-900">2. Política de Privacidad</h2>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">2.1 Identidad del Responsable</h3>
          <p>
            En cumplimiento de la <strong>Ley N° 29733 — Ley de Protección de Datos Personales</strong> y su Reglamento aprobado mediante <strong>D.S. N° 003-2013-JUS</strong> (República del Perú), informamos que el titular y responsable del tratamiento de los datos personales recabados a través de este sitio web es <strong>{{ company.legal_name }}</strong>, con RUC N° <strong>{{ company.ruc }}</strong>, domicilio en <strong>{{ company.address }}</strong>, teléfono <strong>{{ company.phone }}</strong> y correo electrónico <strong>{{ company.email }}</strong>.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">2.2 Datos Personales que Recopilamos</h3>
          <ul class="list-disc pl-6 space-y-1.5">
            <li><strong>Atención y cotizaciones vía WhatsApp:</strong> nombre, número telefónico, marca/modelo/año de vehículo o medida de neumático consultada.</li>
            <li><strong>Libro de Reclamaciones Virtual:</strong> nombres, apellidos, tipo y número de documento de identidad (DNI, CE, RUC o Pasaporte), teléfono, correo electrónico, domicilio y detalle del reclamo o queja (y datos del apoderado en caso de menores de edad).</li>
            <li><strong>Datos de navegación y cookies:</strong> dirección IP, tipo de navegador, páginas visitadas y métricas de uso mediante Google Analytics/Tag Manager, únicamente cuando el usuario otorga su consentimiento.</li>
          </ul>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">2.3 Finalidad del Tratamiento</h3>
          <ul class="list-disc pl-6 space-y-1.5">
            <li>Atender solicitudes de cotización, verificar disponibilidad de stock y brindar asesoría técnica personalizada por WhatsApp o correo electrónico.</li>
            <li>Coordinar la instalación, despacho o recojo de neumáticos en nuestros talleres autorizados.</li>
            <li>Gestionar y dar respuesta a reclamos o quejas registrados en el Libro de Reclamaciones Virtual conforme a la normativa de INDECOPI.</li>
            <li>Mejorar la experiencia de navegación y el rendimiento del sitio web.</li>
          </ul>
          <p>TERSAL no vende, alquila ni cede tus datos personales a terceros con fines comerciales ajenos a la atención solicitada.</p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">2.4 Plazo de Conservación y Seguridad</h3>
          <p>
            Los datos personales se conservarán durante el tiempo necesario para cumplir con las finalidades descritas y, en el caso del Libro de Reclamaciones, por el plazo mínimo exigido por la normativa peruana. TERSAL adopta medidas técnicas, organizativas y legales razonables para evitar su alteración, pérdida o acceso no autorizado.
          </p>
        </section>

        <section class="space-y-2">
          <h3 class="text-lg font-bold text-gray-900">2.5 Derechos ARCO y Uso de Cookies</h3>
          <p>
            Puedes ejercer en cualquier momento tus derechos de Acceso, Rectificación, Cancelación y Oposición (derechos ARCO), así como revocar tu consentimiento, escribiendo gratuitamente a <a :href="`mailto:${company.email}`" class="text-primary font-semibold hover:underline">{{ company.email }}</a> indicando tu nombre completo, número de documento y el detalle de tu requerimiento. Asimismo, puedes aceptar o rechazar las cookies analíticas desde el banner de consentimiento o configurar tu navegador.
          </p>
        </section>
      </article>
    </main>

    <Footer />
  </div>
</template>
