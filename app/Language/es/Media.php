<?php

/**
 * Media textos de idioma (es)
 *
 * Página de medios con pestañas (fotos + vídeos). El contenido se basa
 * en archivos (ver App\Controllers\Media): imágenes de
 * public/assets/media/photos y vídeos de public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Medios',
    'meta_description' => 'Fotos y vídeos de la fabricación de semirremolques y cisternas Barlas: nuestra planta, el proceso de producción y nuestros vehículos en carretera.',

    // Hero de página
    'page_title' => 'Medios',
    'page_lead'  => 'Fotos y vídeos de nuestra planta, del proceso de producción y de nuestros vehículos en carretera.',
    'breadcrumb' => 'Medios',

    // Pestañas
    'tab_photos' => 'Fotos',
    'tab_videos' => 'Vídeos',

    // Filtros de categoría (fotos)
    'filter_aria'              => 'Filtrar por categoría',
    'filter_all'               => 'Todas',
    'cat_before_after'         => 'Antes / después',
    'before_after_empty_title' => 'Antes / después muy pronto',
    'before_after_empty'       => 'Pronto se añadirán imágenes de antes y después a esta categoría.',

    // Fotos — estado vacío
    'photos_empty_title' => 'Fotos muy pronto',
    'photos_empty_text'  => 'Pronto se añadirán aquí fotos de producción y de la planta.',
    'sample_note'        => 'Imágenes de muestra: se muestran temporalmente hasta que añadas las tuyas.',

    // Vídeos — vacío / marcador
    'videos_empty_title' => 'Vídeos muy pronto',
    'videos_empty_text'  => 'Nuestros vídeos promocionales y de producción están en camino. Vuelve pronto.',
    'video_badge'        => 'Muy pronto',
    'video_placeholders' => [
        'Desde la línea de producción',
        'Visita a la planta',
        'En carretera — entrega',
    ],

    // Accesibilidad
    'play'          => 'Reproducir',
    'lightbox_close' => 'Cerrar',
    'lightbox_prev'  => 'Anterior',
    'lightbox_next'  => 'Siguiente',
];
