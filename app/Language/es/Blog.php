<?php

/**
 * Blog textos de idioma (es)
 *
 * Textos de UI + textos de los artículos. Los artículos se indexan por
 * slug; el slug, la fecha, la categoría y la portada viven en el registro
 * del controlador Blog para que las URL sean idénticas en todos los idiomas.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Noticias del sector, consejos de mantenimiento, artículos de tecnología y guías expertas del mundo del semirremolque y la cisterna.',

    // Hero de página
    'page_title' => 'Blog',
    'page_lead'  => 'Noticias del sector, consejos de mantenimiento y guías con enfoque de ingeniería.',

    // Barra lateral
    'search_title'       => 'Buscar',
    'search_placeholder' => 'Buscar artículos...',
    'search_submit'      => 'Buscar',
    'categories_title'   => 'Categorías',
    'all_posts'          => 'Todos los artículos',

    // Tarjetas y estados de lista
    'read_more'        => 'Leer más',
    'results_found'    => 'artículos encontrados',
    'no_results_title' => 'Sin resultados',
    'no_results_text'  => 'Ningún artículo coincide con tu búsqueda. Prueba otra palabra clave o explora todos los artículos.',
    'clear_filters'    => 'Mostrar todos los artículos',

    // Página de detalle
    'related_title'   => 'Artículos relacionados',
    'related_eyebrow' => 'Sigue leyendo',
    'back_to_blog'    => 'Volver a todos los artículos',

    // Categorías
    'cat_industry'    => 'Sector',
    'cat_maintenance' => 'Mantenimiento y servicio',
    'cat_safety'      => 'Seguridad y ADR',
    'cat_technology'  => 'Tecnología',

    // Formato de fecha
    'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],

    // Artículos (slug => textos)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Normas de seguridad en cisternas certificadas ADR',
            'excerpt' => 'Cómo la normativa ADR condiciona el diseño de cisternas para el transporte de mercancías peligrosas, y las prácticas de seguridad que todo operador debe seguir.',
            'content' => '
                <p>El transporte de mercancías peligrosas por carretera está estrictamente regulado por el acuerdo internacional ADR. En las cisternas que transportan combustible, GLP o químicos, cada etapa —del diseño y la fabricación a las pruebas y las inspecciones periódicas— se desarrolla dentro de este marco normativo.</p>
                <h2>Cómo el ADR condiciona el diseño de la cisterna</h2>
                <p>En una cisterna certificada ADR, el material de la cuba, el espesor de pared, el número de compartimentos y la disposición de los rompeolas se calculan según la clase del producto transportado. La resistencia al vuelco, la puesta a tierra estática, las válvulas de cierre de emergencia y los sistemas antirrebose forman parte integral del diseño. Tras la producción, organismos de inspección independientes verifican las pruebas de presión y estanqueidad.</p>
                <h2>Qué debe vigilar el operador</h2>
                <p>La certificación por sí sola no basta; la seguridad se sostiene con el uso correcto y la inspección regular. Las prioridades principales para el operador son:</p>
                <ul>
                    <li>Controlar los plazos de las inspecciones intermedias y periódicas</li>
                    <li>Revisar con regularidad válvulas, juntas y equipos de seguridad</li>
                    <li>Cumplir íntegramente los procedimientos de carga y descarga</li>
                    <li>Mantener al día los certificados de formación ADR de los conductores</li>
                </ul>
                <p>Cuando una cisterna bien diseñada se encuentra con un equipo de operaciones bien formado, el transporte de mercancías peligrosas puede realizarse con seguridad y eficiencia a la vez.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 consejos de mantenimiento que alargan la vida del silo',
            'excerpt' => 'Los pasos de mantenimiento esenciales para que los silos que transportan cargas en polvo y granuladas funcionen con eficiencia durante más tiempo.',
            'content' => '
                <p>Los semirremolques silo trabajan en condiciones exigentes transportando cemento, harina, PVC y productos similares en polvo o granulados. Sin un mantenimiento regular, el sistema de descarga a presión y las líneas de aire se convierten en la primera fuente de pérdida de eficiencia y averías.</p>
                <h2>Por qué importa el mantenimiento planificado</h2>
                <p>El mantenimiento planificado hace más que reducir el riesgo de avería: acorta los tiempos de descarga, baja el consumo y protege el valor de reventa. Los pasos siguientes evitan la mayoría de los problemas en campo:</p>
                <ul>
                    <li>Comprueba fugas en líneas de aire y fuelles antes de cada viaje</li>
                    <li>Revisa con regularidad el nivel de aceite y los filtros del compresor</li>
                    <li>Vigila el desgaste de las juntas de las válvulas de descarga</li>
                    <li>Prueba los cierres y sellos de las bocas de carga superiores</li>
                    <li>Limpia el interior de la cuba en cada cambio de producto</li>
                    <li>Haz probar la válvula de seguridad al menos una vez al año</li>
                    <li>Reaprieta periódicamente las uniones de chasis y ejes</li>
                </ul>
                <h2>Apoyo de servicio profesional</h2>
                <p>Las comprobaciones diarias puede hacerlas el operador; los sistemas a presión, las válvulas de seguridad y las inspecciones estructurales corresponden a equipos de servicio especializados. Usar repuestos aprobados por el fabricante es la forma más fiable de conservar el rendimiento original del sistema.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Elegir la góndola adecuada para el transporte pesado',
            'excerpt' => 'Los criterios a considerar al definir la configuración de góndola adecuada para transportar maquinaria de obra y equipos pesados.',
            'content' => '
                <p>Mover maquinaria de obra, generadores, transformadores y equipos industriales exige soluciones más allá de los límites de los semirremolques estándar. Con su baja altura de carga y su alta capacidad, las góndolas son la herramienta crítica de este segmento.</p>
                <h2>Definir la configuración correcta</h2>
                <p>La primera pregunta al elegir góndola es siempre la misma: ¿qué se va a transportar? El peso, las dimensiones y el centro de gravedad de la carga determinan cada decisión técnica: del número de ejes a la longitud de la plataforma, el tipo de rampas y la necesidad de ensanches. Junto a los modelos fijos de dos, tres y cuatro ejes existen también soluciones modulares y de plataforma extensible.</p>
                <h2>Criterios clave de selección</h2>
                <ul>
                    <li>Carga útil máxima y peso por eje</li>
                    <li>Altura de carga y longitud del pocillo</li>
                    <li>Opciones de rampas hidráulicas o asistidas por muelle</li>
                    <li>Necesidad de ejes autodireccionales</li>
                    <li>Restricciones de puentes y túneles en la ruta</li>
                </ul>
                <p>Una góndola bien configurada hace más que llevar la carga: mejora la seguridad operativa, simplifica los permisos de transporte y reduce los costes a largo plazo. Trabajar con el equipo de ingeniería del fabricante durante la selección da el mejor resultado.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Tecnología de chasis ligero: menos peso, más carga útil',
            'excerpt' => 'Las ventajas operativas de reducir la tara con aceros de alta resistencia y un diseño de chasis optimizado.',
            'content' => '
                <p>Con los límites legales de peso total fijados en el transporte por carretera, la ganancia está en la tara del semirremolque. Cada kilogramo ahorrado en el chasis se convierte directamente en carga útil — y en más ingresos por viaje.</p>
                <h2>El papel de los aceros de alta resistencia</h2>
                <p>En la fabricación moderna de semirremolques, los aceros de alta resistencia sustituyen cada vez más a los grados estructurales estándar. Estos materiales ofrecen igual o mejor resistencia con secciones más finas. Combinados con procedimientos de soldadura correctos y técnicas de producción precisas, permiten reducir notablemente el peso del chasis conservando la vida a fatiga.</p>
                <h2>Optimización del diseño</h2>
                <p>El material por sí solo no basta. El análisis por elementos finitos (FEA) modela la distribución de tensiones del chasis, de modo que el material se usa solo donde hace falta. La geometría de los largueros, la colocación de los travesaños y los detalles de unión se optimizan a partir de estos análisis.</p>
                <p>El resultado: menos consumo, más carga útil y menos emisiones de carbono. La reducción de peso es hoy —económica y ambientalmente— uno de los temas más importantes de la ingeniería del semirremolque.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Tecnología de semirremolques frigoríficos en la logística de frío',
            'excerpt' => 'Las soluciones frigoríficas modernas que mantienen intacta la cadena de frío en el transporte de alimentos y farmacéuticos.',
            'content' => '
                <p>Al transportar productos sensibles a la temperatura, como alimentos y medicamentos, la más mínima desviación puede provocar pérdida de producto y serios riesgos sanitarios. Los semirremolques frigoríficos —piedra angular de la logística de frío— mantienen esta operación delicada sin interrupciones.</p>
                <h2>La calidad del aislamiento es lo primero</h2>
                <p>El rendimiento de una caja frigorífica lo determina la calidad del aislamiento antes que el equipo de frío. Los paneles rellenos de poliuretano de alta densidad, combinados con detalles de junta que minimizan los puentes térmicos, hacen trabajar menos al equipo y reducen el consumo. Las juntas de puertas y la construcción del suelo también son decisivas en la fuga total de calor.</p>
                <h2>Monitorización y registro de datos</h2>
                <p>En las operaciones modernas de cadena de frío, el seguimiento de temperatura ya no se limita a la pantalla de la cabina. Los sistemas de telemetría en tiempo real permiten vigilar a distancia la temperatura de la caja, las aperturas de puertas y el rendimiento del frío. Estos datos son cada vez más importantes tanto para el cumplimiento normativo como como prueba de calidad ante los clientes.</p>
                <p>Cuando el aislamiento adecuado, un equipo de frío fiable y la monitorización continua se combinan, la cadena de frío queda protegida incluso en las rutas más largas.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Preparados para el invierno: lista de mantenimiento del semirremolque',
            'excerpt' => 'Una lista completa para preparar tu flota frente a bajas temperaturas, sal de carretera y condiciones invernales duras.',
            'content' => '
                <p>Los meses de invierno —con bajas temperaturas, sal y firmes complicados— son la época más dura del año para los semirremolques. Una preparación planificada antes de la temporada evita averías en carretera en pleno invierno.</p>
                <h2>Frenos y sistemas neumáticos</h2>
                <p>El secador de aire se vuelve crítico con el frío: si el cartucho no se sustituye a tiempo, la humedad acumulada en las líneas puede congelarse y bloquear el sistema de frenos. Pastillas, discos y reglajes automáticos deben revisarse antes de la temporada.</p>
                <h2>Lista previa a la temporada</h2>
                <ul>
                    <li>Sustituye el cartucho del secador y purga los calderines</li>
                    <li>Comprueba el espesor de pastillas y las superficies de los discos</li>
                    <li>Mide la profundidad del dibujo y las presiones de los neumáticos</li>
                    <li>Inspecciona la iluminación y las conexiones eléctricas</li>
                    <li>Lava el chasis y aplica protección contra la corrosión por sal</li>
                    <li>Engrasa bisagras y cierres de puertas</li>
                </ul>
                <p>La preparación invernal regular es la vía más barata de aumentar la disponibilidad de la flota y alargar la vida de los vehículos. Nuestra red de servicio autorizado está lista para apoyarte con revisiones de temporada completas.</p>
            ',
        ],
    ],
];
