# Plan de Arquitectura, Especificaciones Técnicas y UX/UI: Tersal

Este documento sirve como la **Guía Única de Verdad (Single Source of Truth)** para el desarrollo, mantenimiento y escalabilidad de la plataforma web de **Tersal**. Cualquier cambio en el diseño, flujo o código debe respetar los lineamientos aquí descritos.

---

## 1. Stack Tecnológico Especificado

La plataforma se desarrollará bajo una **arquitectura monolítica moderna**, lo que significa que el backend y el frontend conviven en el mismo repositorio, se despliegan bajo una misma ruta y comparten el mismo servidor.

*   **Backend (Core, Base de Datos y API):** **Laravel 11+ (PHP 8.2+)**
    *   Maneja el enrutamiento global, el almacenamiento de datos en MySQL, la autenticación segura del administrador y el procesamiento de la información.
*   **Frontend (Interactividad Fluida):** **Vue.js 3** (Composition API)
    *   Maneja la reactividad de la interfaz (buscador dual, filtros del catálogo, acordeones e infografías interactivas).
*   **Comunicación de Arquitectura (El Puente):** **Inertia.js**
    *   Elimina la necesidad de crear una API REST independiente. Permite a los controladores de Laravel renderizar componentes de Vue directamente, logrando una experiencia de *Single Page Application* (SPA) sin recargas de página.
*   **Estilos y Maquetación:** **Tailwind CSS v4**
    *   Permite un control estricto del responsive mediante clases utilitarias y una carga de estilos optimizada. Se configura de forma nativa a través de directivas `@theme` en `resources/css/app.css` (sin necesidad de `tailwind.config.js`).
*   **Package Manager (Exclusivo):** **PNPM**
    *   Se prohíbe estrictamente el uso de `npm` o `yarn`. Toda gestión de dependencias y scripts de ciclo de vida (`pnpm dev`, `pnpm build`) debe ejecutarse mediante PNPM para asegurar un único `pnpm-lock.yaml` unificado en la raíz del proyecto.

---

## 2. Sistema de Diseño & Paleta de Colores (Soft UI)

El diseño visual mantiene una estética "soft" y limpia, caracterizada por bordes redondeados suaves, sombras sutiles, tipografía moderna y un uso generoso del espacio en blanco para mitigar la densidad visual de los datos técnicos de los neumáticos.

*   **Fondo y Claridad:** Blanco Puro (`#FFFFFF`) para los lienzos principales y Gris Soft (`#F8F9FA`) para diferenciar secciones o tarjetas de productos.
*   **Estructura y Confianza:** **Azul Eléctrico (`#0A1BCC`)**
    *   Utilizado en la barra de navegación, el pie de página (Footer), títulos de secciones, estados seleccionados y la barra lateral del Dashboard. Transmite solidez y seguridad técnica.
*   **Acción y Conversión:** **Rojo Vibrante (`#D61A1A`)**
    *   **Regla estricta:** Reservado *únicamente* para elementos interactivos que disparen una acción crítica (Botón de búsqueda, "Consultar por WhatsApp", etiquetas de ofertas o modales de confirmación/eliminación).

---

## 3. Filosofía de Desarrollo: Mobile-First

Dado que la mayor parte de las consultas de compra rápida ocurren desde dispositivos móviles, la plataforma se maqueta pensando primero en pantallas pequeñas y escalando gradualmente a escritorio.

*   **Layouts Responsivos:** Uso de grids de una sola columna por defecto (`grid-cols-1` en Tailwind) que se expanden a tres o más en pantallas grandes (`md:grid-cols-3`).
*   **Ergonomía del Pulgar:** Todos los botones principales, campos de texto y dropdowns deben tener una altura mínima de **48px** (`h-12` en Tailwind) para facilitar el toque interactivo.
*   **Adaptabilidad del Catálogo:** El panel lateral de filtros en escritorio se transforma automáticamente en un menú inferior deslizable (*Drawer / Bottom Sheet*) a pantalla completa en dispositivos móviles.
*   **Paginación:** Queda **prohibida** la paginación numérica tradicional. Se debe utilizar un botón explícito de **"Cargar más resultados"** para mantener una navegación fluida y ligera.

---

## 4. Core Feature: Motor de Redirección a WhatsApp

La plataforma no utiliza pasarelas de pago ni carrito de compras tradicional. Toda la conversión se centraliza a través de la API de WhatsApp (`https://wa.me/{teléfono}`).

### Regla de Construcción del Enlace Dinámico
Cada botón de acción rojo debe capturar las propiedades del neumático en su estado actual (pasados por la función `encodeURIComponent()`) para armar el mensaje de destino.

*   **Plantilla del String:**
    ```text
    ¡Hola! Estoy interesado en el neumático {Marca} {Modelo} en la medida {Medida} que vi en su web. Deseo confirmar el stock para {Cantidad} unidades y coordinar la atención.
    ```
*   **Comportamiento del Inventario:** El selector de cantidad (`Counter`) en la ficha de producto debe venir configurado por defecto en **4 unidades** (fomentando la compra del juego completo). Si el stock de un producto llega a `0` en el Dashboard, el botón rojo se desactiva y se reemplaza por un tag gris de "Agotado".

---

## 5. Estructura de Archivos y Carpetas (Por Feature)

Para asegurar la escalabilidad del código, la estructura del proyecto organiza los componentes front-end de Vue y la lógica de Laravel por **funcionalidades/módulos independientes** en lugar de agruparlos por tipo de archivo.

```text
TERSAL/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/                        # Autenticación segura del Administrador
│   │       ├── CatalogController.php        # Consultas a la BD y filtrado del catálogo
│   │       ├── DashboardController.php      # Lógica de negocio y CRUD del panel
│   │       └── HomeController.php           # Render de la vista de inicio pública
│   └── Models/
│       ├── Tire.php                         # Modelo del Neumático (medidas, precio, stock)
│       └── Brand.php                        # Modelo de Marcas (asociación relacional)
├── resources/
│   └── js/
│       ├── Pages/                           # Páginas principales (Componentes de Inertia)
│       │   ├── Home/                        # FEATURE: Página de Inicio
│       │   │   ├── Components/
│       │   │   │   ├── HeroBanner.vue       # Banner principal
│       │   │   │   └── DualSearchFinder.vue # Buscador por medida o por vehículo
│       │   │   └── Index.vue
│       │   ├── Catalog/                     # FEATURE: Catálogo de Productos (PLP)
│       │   │   ├── Components/
│       │   │   │   ├── FilterSidebar.vue    # Filtros acumulativos (Uso, Marca, Precio)
│       │   │   │   └── ProductCard.vue      # Tarjeta de neumático con CTA Rojo
│       │   │   └── Index.vue
│       │   ├── ProductDetail/               # FEATURE: Detalle del Producto (PDP)
│       │   │   ├── Components/
│       │   │   │   ├── ImageGallery.vue     # Visor principal + miniaturas (tread/perfil)
│       │   │   │   └── TechAccordion.vue    # Ficha técnica colapsable en acordeón
│       │   │   └── Index.vue
│       │   ├── Guide/                       # FEATURE: Guía Interactiva y FAQs
│       │   │   ├── Components/
│       │   │   │   ├── InteractiveTire.vue  # Gráfico interactivo SVG de medidas
│       │   │   │   └── FaqAccordion.vue     # Preguntas frecuentes logísticas
│       │   │   └── Index.vue
│       │   ├── Contact/                     # FEATURE: Información de Contacto
│       │   │   └── Index.vue                # Página de contacto con mapa y medios
│       │   └── Admin/                       # FEATURE: Dashboard de Administración
│       │       ├── Components/
│       │       │   ├── AdminSidebar.vue     # Navegación del panel (Azul Eléctrico)
│       │       │   └── DragDropUpload.vue   # Gestor multimedia "Drag & Drop" con reordenamiento
│       │       ├── InventoryList.vue        # Tabla de control de stock, precios y edición
│       │       ├── ProductForm.vue          # Formulario unificado (Agregar/Editar)
│       │       └── Settings.vue             # Configuración de número de WhatsApp y FAQs
│       ├── Shared/                          # Componentes globales transversales
│       │   ├── Header.vue                   # Cabecera con menú e input predictivo libre
│       │   ├── Footer.vue                   # Pie de página estructurado en 4 columnas
│       │   └── WhatsAppFloatingBtn.vue      # Botón flotante responsive e independiente
│       └── app.js                           # Inicialización e inyección de Vue 3 + Inertia
├── resources/css/
│   └── app.css                              # Inyección de Tailwind v4 y variables CSS (@theme)
└── routes/
    └── web.php                              # Enrutador web de Laravel