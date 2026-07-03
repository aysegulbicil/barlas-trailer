<?php

/**
 * Blog language strings (es)
 *
 * UI strings + post texts. Post texts match the slugs kept in the Blog
 * controller registry (slug, date and category live there so URLs are
 * identical across locales).
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Artículos expertos basados en fuentes académicas sobre aerodinámica de semirremolques, ahorro de combustible, aligeramiento y tecnologías de materiales.',

    // Hero de página
    'page_title' => 'Blog',
    'page_lead'  => 'Artículos sobre aerodinámica, ahorro de combustible y tecnología de materiales basados en estudios académicos revisados por pares.',

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
    'cat_aerodynamics'   => 'Aerodinámica & Ahorro de Combustible',
    'cat_materials'      => 'Aligeramiento & Tecnología de Materiales',
    'cat_electric'       => 'Cero Emisiones & Semirremolque Eléctrico',
    'cat_adr'            => 'Cisternas & ADR',
    'cat_regulations'    => 'Regulaciones Europeas & CO₂',
    'cat_fleet'          => 'Logística & Gestión de Flotas',
    'cat_autonomous'     => 'Conducción Autónoma & Platooning',
    'cat_sustainability' => 'Sostenibilidad & Economía Circular',
    'cat_maintenance'    => 'Mantenimiento, Seguridad & Nuevas Tecnologías',
    'cat_segments'       => 'Temas Sectoriales Específicos',

    // Formato de fecha
    'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],

    // Artículos (slug => textos)
    'posts' => [
        'tanker-aerodynamics-fuel-savings' => [
            'title'   => '¿Es posible ahorrar un 7% de combustible con mejoras aerodinámicas en los semirremolques cisterna?',
            'excerpt' => 'Estudios académicos basados en ensayos reales en carretera demuestran que, en semirremolques sin caja como las cisternas, el paquete aerodinámico adecuado puede reducir el consumo de combustible hasta un siete por ciento.',
            'content' => '
                <p>A velocidades de larga distancia, una parte importante de la energía que consume un conjunto tractora-semirremolque se destina a vencer la resistencia del aire. Los equipos aerodinámicos desarrollados para los semirremolques de caja (lona/furgón) se conocen desde hace años; la verdadera pregunta es qué parte de esa ganancia puede conservarse en las cisternas de cuba cilíndrica.</p>
                <h2>¿Qué dicen los hallazgos científicos?</h2>
                <p>Un equipo de investigación con base en Cambridge midió con datos reales de conducción el efecto de las modificaciones del semirremolque en el consumo de combustible de los vehículos pesados, y reportó que las combinaciones de faldones laterales, tratamiento trasero y cierre del hueco pueden alcanzar en conjunto la banda del siete por ciento. Otro estudio de origen norteamericano examinó cómo se utilizan en campo las cisternas y otros semirremolques sin caja, y puso de manifiesto la necesidad de un catálogo específico de dispositivos aerodinámicos para este segmento.</p>
                <h2>Las fuentes de la ganancia en la cisterna</h2>
                <ul>
                    <li>Faldones laterales que ordenan los bajos del chasis: la mayor partida individual de ganancia</li>
                    <li>Carenado integrado en la cuba del tubo de llenado, el armario de válvulas y el equipo de bombeo</li>
                    <li>Reducción al mínimo del hueco entre la tractora y la cisterna</li>
                    <li>Ordenación aerodinámica de la zona trasera de equipos y señalización</li>
                </ul>
                <p>Aunque la sección cilíndrica parte de una forma más favorable que la caja, el complejo equipamiento situado bajo y detrás de la cisterna genera turbulencias. Cuando estas zonas se carenan con la ingeniería adecuada, un ahorro de entre el cinco y el siete por ciento es un objetivo realista; en un vehículo que recorre cien mil kilómetros al año, eso significa miles de litros de combustible.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                    <li>Durbin, T. D., Johnson, K. C., Jung, H. &amp; Miller, J. W. (2021). Characterizing non-box trailer activity and aerodynamic devices for greenhouse gas emissions reductions. <em>Transportation Research Part D: Transport and Environment</em>, 98, 102763. DOI: 10.1016/j.trd.2021.102763</li>
                </ul>
            ',
        ],

        'composite-trailer-weight-reduction' => [
            'title'   => 'Con materiales compuestos es posible reducir el peso del semirremolque hasta en 2,5 toneladas',
            'excerpt' => 'Los paneles sándwich y los composites reforzados con fibra ofrecen toneladas de ventaja de tara frente a las carrocerías tradicionales de acero; cada kilogramo ganado se convierte directamente en carga útil.',
            'content' => '
                <p>Como el peso total máximo en carretera está fijado por ley, cada kilogramo ganado a la tara del semirremolque se suma directamente a la carga transportable. Los prototipos de semirremolques ligeros de la literatura académica muestran que, con diseños intensivos en composites, una reducción de la tara de hasta 2,5 toneladas es técnicamente posible.</p>
                <h2>¿Qué composites y dónde?</h2>
                <p>Los paneles de polímero reforzado con fibra de vidrio (GFRP) se emplean en las paredes de la caja; las estructuras sándwich con núcleo de espuma o balsa, en el piso; y la fibra de carbono, en las zonas de vigas sometidas a las mayores tensiones. El acero queda solo en las zonas donde se concentran las cargas puntuales, como el king-pin o la fijación de los ejes; el resultado es una estructura híbrida.</p>
                <h2>Ganancias y puntos de atención</h2>
                <ul>
                    <li>Más carga útil por viaje o menor consumo de combustible</li>
                    <li>Mayor vida útil gracias a la resistencia natural a la corrosión</li>
                    <li>El diseño a fatiga de los detalles de unión metal-composite es de importancia crítica</li>
                    <li>Los procedimientos de reparación y la detección de daños exigen una especialización distinta a la del acero</li>
                    <li>El coste de la inversión inicial debe calcularse junto con la ganancia en combustible y carga</li>
                </ul>
                <p>Cuando se elige el ámbito de aplicación correcto, el aligeramiento con composites es una estrategia de ingeniería probada que reduce el coste del ciclo de vida del vehículo y aumenta la eficiencia de la flota.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                </ul>
            ',
        ],

        'side-skirt-selection-guide' => [
            'title'   => 'Elección de faldones laterales (side skirts): ¿cuál conviene a cada tipo de semirremolque?',
            'excerpt' => 'Los faldones laterales son el equipo aerodinámico más extendido; sin embargo, de la lona al silo, la longitud, el material y el enfoque de montaje correctos son distintos para cada tipo de semirremolque.',
            'content' => '
                <p>El faldón lateral reduce la turbulencia alrededor del grupo de ejes y de los elementos del chasis al impedir que el flujo de aire entre lateralmente bajo el semirremolque. Las investigaciones muestran que un faldón bien aplicado puede aportar por sí solo entre un tres y un seis por ciento de ahorro de combustible; pero el enfoque de "talla única" erosiona rápidamente esa ganancia.</p>
                <h2>La elección correcta según el tipo de semirremolque</h2>
                <ul>
                    <li><strong>Lona y furgón:</strong> El faldón de longitud completa que llega hasta el grupo de ejes ofrece la mayor ganancia.</li>
                    <li><strong>Cisterna y silo:</strong> Como se necesita acceso al armario de válvulas y a la línea de descarga, se prefieren faldones parciales abisagrados o por módulos.</li>
                    <li><strong>Bañera:</strong> Por las condiciones de obra convienen faldones cortos, resistentes al impacto y con borde inferior flexible.</li>
                    <li><strong>Chasis portacontenedores:</strong> Se requieren sistemas con mecanismo plegable que puedan montarse a posteriori sobre la estructura del chasis.</li>
                </ul>
                <h2>Notas sobre material y montaje</h2>
                <p>Los faldones de composite destacan por su ligereza; los de aluminio, por su facilidad de reparación. Que el borde inferior sea flexible (caucho/termoplástico) evita daños en los contactos con rampas y badenes. Emplear en el montaje una unión con amortiguación de vibraciones entre el faldón y el chasis previene las grietas por fatiga. La lisura y la continuidad de la superficie del faldón pueden ser más importantes que su longitud.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Durbin, T. D., Johnson, K. C., Jung, H. &amp; Miller, J. W. (2021). Characterizing non-box trailer activity and aerodynamic devices for greenhouse gas emissions reductions. <em>Transportation Research Part D: Transport and Environment</em>, 98, 102763. DOI: 10.1016/j.trd.2021.102763</li>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'hardox-vs-aluminium-chassis' => [
            'title'   => 'Acero de alta resistencia (Hardox) vs. aluminio: la elección correcta de material para el chasis del semirremolque',
            'excerpt' => '¿Resistencia al desgaste o ligereza absoluta? Comparativa de ingeniería de los puntos fuertes y débiles de las dos familias de materiales en aplicaciones de chasis y carrocería.',
            'content' => '
                <p>La decisión de material en el chasis de un semirremolque condiciona toda la vida económica del vehículo. Los aceros de alta resistencia y antidesgaste y las aleaciones de aluminio son los dos grandes polos de esta decisión; la respuesta correcta varía según la carga transportada y el perfil de operación.</p>
                <h2>Dónde es fuerte el acero</h2>
                <p>Los aceros antidesgaste, gracias a su alta dureza, no tienen rival en aplicaciones con contacto abrasivo intenso, como escombros, movimiento de tierras o cargas de minería. Su alto límite elástico proporciona la misma resistencia con secciones más finas, con una notable ganancia de peso frente al acero estructural clásico. La soldabilidad y la amplitud de la infraestructura de reparación también dan confianza a las flotas.</p>
                <h2>Dónde es fuerte el aluminio</h2>
                <p>El aluminio, por su baja densidad, es el estándar en las cubas de cisterna y silo donde se busca la ligereza absoluta; gracias a su resistencia a la corrosión puede usarse sin pintar y su valor residual es alto. En contrapartida, su bajo módulo de elasticidad hace más crítico el control de la flecha, y su comportamiento a fatiga vuelve el diseño de los detalles de unión más delicado que en el acero.</p>
                <h2>Criterios de decisión</h2>
                <ul>
                    <li>Si hay contacto con cargas abrasivas: acero antidesgaste</li>
                    <li>Si la prioridad es la ligereza y la corrosión: aluminio o estructura híbrida</li>
                    <li>En las zonas donde se concentran cargas puntuales: refuerzo de acero de alta resistencia</li>
                    <li>Coste total de propiedad: combustible, mantenimiento y valor de reventa deben calcularse juntos</li>
                </ul>
                <p>La ingeniería moderna del semirremolque, en lugar de enfrentar a los dos materiales, converge la mayoría de las veces en soluciones híbridas que emplean cada uno en la zona donde es más fuerte.</p>
            ',
        ],

        'boat-tail-diffuser-guide' => [
            'title'   => 'Boat tail y difusor: guía para reducir la resistencia al viento en la zaga del semirremolque',
            'excerpt' => 'La zona de baja presión detrás del semirremolque es una de las mayores fuentes de resistencia aerodinámica. El boat tail y el difusor ordenan esa zona y aportan un ahorro de combustible medible.',
            'content' => '
                <p>Cuando un semirremolque circula, deja tras de sí una amplia zona de baja presión (la estela); el vehículo es literalmente succionado hacia atrás por ese vacío. Los paneles boat tail añadidos a los cantos traseros y el difusor bajo el chasis reducen esa pérdida recogiendo el flujo de forma gradual.</p>
                <h2>¿Cómo funciona el boat tail?</h2>
                <p>Tres o cuatro paneles ligeramente inclinados hacia dentro, dispuestos alrededor de la puerta trasera, hacen que el flujo que abandona la carrocería se estreche sin desprenderse. La normativa europea ha abierto el camino a esta tecnología al permitir, bajo determinadas condiciones, dispositivos aerodinámicos plegables que superan la longitud máxima del vehículo; los paneles se pliegan en el muelle de carga y se despliegan en ruta.</p>
                <h2>La aportación del difusor</h2>
                <p>La superficie del difusor, que asciende bajo el chasis después del grupo de ejes trasero, entrega de forma controlada a la estela el flujo que llega por debajo. Aunque su aportación aislada es limitada, usado junto con los faldones laterales y el boat tail incrementa de forma clara la ganancia total; el efecto de la combinación está validado con mediciones reales en carretera.</p>
                <ul>
                    <li>El mejor resultado: la combinación faldones laterales + boat tail + difusor</li>
                    <li>Los ángulos de los paneles deben optimizarse en la banda de 10-15 grados</li>
                    <li>Los mecanismos plegables no deben estorbar la operación en el muelle</li>
                    <li>La ganancia es directamente proporcional al tiempo pasado a velocidad de crucero</li>
                </ul>
                <h2>Referencias</h2>
                <ul>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                </ul>
            ',
        ],

        'fea-lightweight-trailer-optimisation' => [
            'title'   => 'Optimización estructural con análisis de elementos finitos en el diseño de semirremolques ligeros',
            'excerpt' => 'Usar el material solo en las zonas de alta tensión es el método de ingeniería del aligeramiento. El FEA y los algoritmos de optimización sistematizan este proceso.',
            'content' => '
                <p>Un semirremolque ligero no nace solo de un material ligero, sino del uso del material en el lugar correcto y con la sección correcta. El análisis por elementos finitos (FEA) hace visible en el entorno virtual la distribución de tensiones y deformaciones del chasis, de modo que estas decisiones se toman por cálculo y no por ensayo y error.</p>
                <h2>¿Cómo funciona el proceso de optimización?</h2>
                <ul>
                    <li>Se definen los escenarios de carga: carga estática, frenada, curva, torsión en rampa e impactos del firme</li>
                    <li>La geometría del chasis se discretiza en elementos y se obtiene el mapa de tensiones</li>
                    <li>Mediante la optimización topológica y de secciones se retira material de las zonas poco solicitadas</li>
                    <li>La vida a fatiga se verifica por separado sobre los detalles críticos de soldadura</li>
                    <li>El modelo se calibra con mediciones de deformación sobre el prototipo</li>
                </ul>
                <h2>El equilibrio entre aligeramiento y estabilidad</h2>
                <p>Al reducir el peso no puede ignorarse el comportamiento del vehículo frente al vuelco y al balanceo; especialmente en las cisternas que transportan carga líquida, el centro de gravedad y el diseño de los compartimentos afectan directamente a la estabilidad al vuelco. Los estudios de optimización de modelos de la literatura muestran que el aligeramiento estructural y la estabilidad de marcha deben tratarse en el mismo ciclo de cálculo. El resultado: un vehículo más predecible y más seguro con menos material.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Yu, D. &amp; Chu, J. (2019). Study on roll-stability model optimization for partially filled tanker trucks. <em>Advances in Mechanical Engineering</em>, 11(4). DOI: 10.1177/1687814019837805</li>
                </ul>
            ',
        ],

        'container-carrier-drag-reduction' => [
            'title'   => 'Métodos para reducir la resistencia aerodinámica en los portacontenedores',
            'excerpt' => 'La estructura abierta en celosía y el perfil de carga variable hacen de los chasis portacontenedores uno de los tipos de semirremolque más difíciles desde el punto de vista aerodinámico. Aun así, existen métodos de ganancia probados.',
            'content' => '
                <p>Un chasis portacontenedores sin carga queda expuesto al viento como una auténtica celosía; y las distintas disposiciones de los contenedores de 20 y 40 pies crean cada vez un perfil aerodinámico diferente. Esta variabilidad impide copiar directamente las soluciones estándar desarrolladas para los semirremolques de caja.</p>
                <h2>Métodos probados</h2>
                <ul>
                    <li><strong>Paneles laterales plegables:</strong> Sistemas que cierran la celosía del chasis y funcionan también sin contenedor</li>
                    <li><strong>Gestión del hueco:</strong> Puenteo con deflector de la abertura entre dos contenedores cortos</li>
                    <li><strong>Acoplamiento con la tractora:</strong> Alineación de la altura del spoiler de cabina con la altura del contenedor</li>
                    <li><strong>Tratamiento del canto trasero:</strong> Soluciones de paneles ligeros acoplables al canto trasero del último contenedor</li>
                </ul>
                <h2>La realidad de los viajes en vacío</h2>
                <p>Las investigaciones de campo muestran que los chasis portacontenedores pasan una parte importante de sus viajes vacíos o con carga parcial; esto aumenta el peso de las soluciones aerodinámicas que funcionan en configuración vacía dentro de la ganancia total. En operaciones de baja velocidad centradas en la rotación portuaria el plazo de retorno se alarga; en las líneas intermodales de larga distancia la inversión se amortiza con rapidez. La decisión debe basarse en el perfil real velocidad-distancia de la flota.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Durbin, T. D., Johnson, K. C., Jung, H. &amp; Miller, J. W. (2021). Characterizing non-box trailer activity and aerodynamic devices for greenhouse gas emissions reductions. <em>Transportation Research Part D: Transport and Environment</em>, 98, 102763. DOI: 10.1016/j.trd.2021.102763</li>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'silo-trailer-stainless-composite' => [
            'title'   => 'Acero inoxidable y composite en los semirremolques silo: ventajas y desventajas',
            'excerpt' => 'Del transporte de grado alimentario al cemento, la elección del material de la cuba del silo es un ejercicio de equilibrio entre higiene, peso y coste de inversión.',
            'content' => '
                <p>Durante muchos años el aluminio se ha considerado el estándar en la cuba del silo; pero a medida que se amplía la gama de productos transportados, las cubas de acero inoxidable y de composite reforzado con fibra (FRP) se han convertido también en alternativas sólidas.</p>
                <h2>Acero inoxidable: higiene y durabilidad</h2>
                <p>Para los productos de grado alimentario y los polvos expuestos a interacción química, el acero inoxidable es la elección más segura en términos de higiene superficial y limpiabilidad. Su resistencia al desgaste y al impacto es alta; su desventaja es el aumento de la tara debido a su densidad y el coste de la inversión inicial.</p>
                <h2>Composite: ligereza y aislamiento</h2>
                <p>Las cubas de FRP ofrecen una seria ventaja de peso incluso frente al aluminio; gracias a su baja conductividad térmica proporcionan aislamiento natural con productos sensibles a la temperatura y no sufren problemas de corrosión. En contrapartida, la detección y la reparación de los daños por impacto exigen especialización, y la red de fabricantes no es tan extensa como la de las cubas metálicas.</p>
                <h2>Tabla de decisión</h2>
                <ul>
                    <li>Prioridad de higiene alimentaria y química: acero inoxidable</li>
                    <li>Máxima carga útil y economía de combustible: composite</li>
                    <li>Coste equilibrado y red de servicio extendida: aluminio</li>
                    <li>Cartera de productos mixta: evaluación híbrida según los detalles de compartimentos y juntas</li>
                </ul>
                <p>La decisión correcta empieza por la lista de los productos a transportar; el material de la cuba debe elegirse en función del producto más agresivo de esa lista.</p>
            ',
        ],

        'cab-over-vs-conventional-aerodynamics' => [
            'title'   => 'Diferencias aerodinámicas entre los camiones de tipo europeo (cab-over) y americano',
            'excerpt' => 'La cabina europea de frontal plano y la cabina americana de capó largo son producto de dos mundos normativos distintos, y sus enfoques de la aerodinámica del semirremolque divergen en consecuencia.',
            'content' => '
                <p>Como la normativa europea de carretera limita la longitud total del vehículo, los fabricantes, para conservar el volumen de carga, situaron la cabina sobre el motor: así nació el diseño cab-over. En el mercado americano, en cambio, al definirse el límite de longitud sobre el semirremolque, las tractoras evolucionaron hacia la forma de capó largo (conventional).</p>
                <h2>Dos filosofías desde el punto de vista aerodinámico</h2>
                <p>La cabina de capó largo ofrece una forma natural que guía el flujo de manera gradual; el capó del motor y las aletas actúan como superficies de transición. La cabina cab-over, con su frontal vertical, genera una resistencia de presión mayor; compensa esa pérdida con el spoiler de techo, los carenados laterales y la reducción agresiva del hueco cabina-semirremolque. En la práctica, una combinación cab-over bien acoplada puede competir, como paquete total, con su rival de capó largo.</p>
                <h2>El nuevo rumbo de la normativa</h2>
                <p>Las actualizaciones de la legislación europea acercan los dos mundos al permitir formas de cabina alargadas con fines aerodinámicos y de seguridad; los frontales redondeados y los diseños de morro adaptados al flujo ya son posibles también en las carreteras europeas. En el lado del semirremolque la lección no cambia: sea cual sea la cabina, el acoplamiento tractora-semirremolque y la gestión del hueco siguen siendo los determinantes de la resistencia total.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'carbon-fiber-trailer-manufacturing' => [
            'title'   => 'La fibra de carbono en la fabricación de semirremolques: ¿coste o rendimiento?',
            'excerpt' => 'La fibra de carbono, llegada de la aeronáutica a la carretera, no tiene rival en relación resistencia/peso; pero en la economía del semirremolque solo encuentra su sitio en las aplicaciones adecuadas.',
            'content' => '
                <p>El polímero reforzado con fibra de carbono (CFRP), pese a ser varias veces más ligero que el acero, ofrece una resistencia específica superior. La cuota de este material —estandarizado en la aeronáutica y el automovilismo deportivo— en la fabricación de semirremolques sigue siendo limitada; la razón no es técnica, sino económica.</p>
                <h2>La ecuación del coste</h2>
                <p>El coste de la materia prima y de la fabricación del CFRP multiplica por decenas el del acero estructural. Esa diferencia solo se cierra en los escenarios donde cada kilogramo ganado genera ingresos elevados: operaciones de cisterna que trabajan al límite de peso, líneas donde la baja tara se traduce directamente en ingresos por viaje, o flotas que calculan el coste total de propiedad a largo plazo.</p>
                <h2>Uso racional: el enfoque híbrido</h2>
                <ul>
                    <li>En los largueros principales del chasis se mantiene el acero de alta resistencia</li>
                    <li>El CFRP se emplea como refuerzo local en las zonas críticas de alta tensión de flexión</li>
                    <li>En los paneles y puertas de la caja, la mezcla fibra de vidrio-carbono logra el equilibrio económico</li>
                    <li>En la producción en serie, la pultrusión y los métodos distintos del prepreg reducen el coste</li>
                </ul>
                <p>Respuesta corta: hoy por hoy un semirremolque íntegramente de carbono no es económico; pero los diseños híbridos, en los que el carbono se aplica de forma quirúrgica allí donde más aporta, responden afirmativamente a la vez a la pregunta del rendimiento y a la del coste.</p>
            ',
        ],

        'adjustable-roof-height-trailers' => [
            'title'   => 'Sistemas de techo de altura regulable en semirremolques: el equilibrio entre aerodinámica y volumen de carga',
            'excerpt' => 'Volumen cuando va cargado, perfil bajo cuando va vacío: los sistemas de techo regulable unen dos necesidades opuestas en un solo vehículo y aportan un ahorro de combustible medible.',
            'content' => '
                <p>El gran volumen de caja es un argumento de venta; pero buena parte de la vida del vehículo transcurre con carga parcial o en vacío, y esa altura sobrante se traduce en resistencia aerodinámica —y en factura— en cada kilómetro. Los sistemas de techo regulable resuelven esta contradicción con una respuesta mecánica: el techo desciende hasta la altura real de la carga.</p>
                <h2>La base científica</h2>
                <p>Las investigaciones que estudian las modificaciones del semirremolque con datos reales de conducción demostraron que reducir la altura de la caja —especialmente a velocidades de larga distancia— disminuye el consumo de combustible de forma medible. La reducción del área frontal baja directamente la fuerza de resistencia aerodinámica; la ganancia crece en proporción al cuadrado de la velocidad.</p>
                <h2>¿Qué vigilar en la operación?</h2>
                <ul>
                    <li>Cumplimiento del programa de mantenimiento del mecanismo de elevación hidráulico o neumático</li>
                    <li>Conservación de la estanqueidad de las juntas de puertas y lona con el techo bajado</li>
                    <li>Introducción correcta de la altura en el sistema durante la planificación de la carga</li>
                    <li>Seguimiento de la altura actual del vehículo frente a los gálibos de puentes y túneles</li>
                </ul>
                <p>En las flotas que trabajan con un perfil de carga variable, el techo regulable es una de las pocas soluciones que ofrecen a la vez mejora aerodinámica y flexibilidad de volumen.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                </ul>
            ',
        ],

        'trailer-material-selection-2026' => [
            'title'   => 'Criterios de selección de materiales en la fabricación de semirremolques en 2026: resistencia, peso y sostenibilidad',
            'excerpt' => 'Las decisiones de material ya no se toman solo con la tabla de resistencias; la huella de carbono, la reciclabilidad y la economía de la refabricación han entrado en la ecuación.',
            'content' => '
                <p>Los dos ejes clásicos de la selección de materiales en la ingeniería del semirremolque son bien conocidos: resistencia y peso. A partir de 2026, el tercer eje es ya permanente: la sostenibilidad. La normativa europea y los objetivos corporativos de carbono cuestionan el desempeño del material no solo sobre el vehículo, sino a lo largo de todo su ciclo de vida.</p>
                <h2>Evaluación en tres ejes</h2>
                <ul>
                    <li><strong>Resistencia:</strong> Límite elástico, vida a fatiga y tenacidad al impacto; se ponderan según el perfil de operación</li>
                    <li><strong>Peso:</strong> Cada kilogramo ganado a la tara vuelve como carga útil o como ahorro de combustible</li>
                    <li><strong>Sostenibilidad:</strong> Intensidad de carbono de la producción, tasa de reciclaje y facilidad de desmontaje</li>
                </ul>
                <h2>El efecto de la economía circular</h2>
                <p>Los estudios de campo del sector del vehículo pesado han demostrado que reincorporar los vehículos a la economía mediante el desmontaje, la refabricación (remanufacturing) y la recuperación de piezas es viable tanto técnica como comercialmente. Esta mirada repercute en la fase de diseño: las uniones atornilladas se prefieren al pegado y los módulos de un solo material a las estructuras mixtas, porque el vehículo desmontable es el vehículo que conserva su valor. La decisión de material ya no forma parte de la compra, sino de la estrategia de ciclo de vida.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Saidani, M., Yannou, B., Leroy, Y. &amp; Cluzel, F. (2020). Dismantling, remanufacturing and recovering heavy vehicles in a circular economy. <em>Resources, Conservation and Recycling</em>, 156, 104684. DOI: 10.1016/j.resconrec.2020.104684</li>
                </ul>
            ',
        ],

        'wind-tunnel-to-road-data' => [
            'title'   => 'Del túnel de viento a los datos reales de carretera: últimos hallazgos científicos en aerodinámica de semirremolques',
            'excerpt' => '¿Por qué la ganancia medida en el túnel se reduce en la carretera? La investigación moderna cierra la brecha entre laboratorio y campo con telemetría y datos reales de conducción.',
            'content' => '
                <p>La herramienta clásica del desarrollo aerodinámico era el túnel de viento; pero los modelos a escala, el ángulo de viento fijo y las condiciones idealizadas del suelo solo representan parcialmente el caos del tráfico real. La tendencia investigadora de los últimos años es clara: el juez definitivo son los datos reales de carretera.</p>
                <h2>La pirámide de validación</h2>
                <ul>
                    <li><strong>Simulación CFD:</strong> Cribado rápido y barato de las variantes de diseño</li>
                    <li><strong>Túnel de viento:</strong> Medición en entorno controlado de las configuraciones seleccionadas</li>
                    <li><strong>Ensayos en pista:</strong> Validación intermedia con protocolos de velocidad constante y deceleración libre (coast-down)</li>
                    <li><strong>Telemetría de flota:</strong> Prueba definitiva con datos de combustible y GPS durante meses de operación real</li>
                </ul>
                <h2>Lo aprendido en campo</h2>
                <p>Los estudios recientes basados en mediciones reales de conducción arrojaron dos hallazgos críticos. Primero: la ganancia en carretera suele quedar por debajo del valor del túnel, porque el tráfico, el ángulo del viento y el perfil de velocidad se apartan del ideal. Segundo: bajo viento cruzado algunos equipos rinden mejor de lo que predice el túnel; el faldón lateral es el ejemplo típico. Por eso las decisiones de compra no deben basarse en una única cifra de catálogo, sino en datos medidos en condiciones parecidas a su propio perfil de operación.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'tipper-wear-plate-hardox-400-450' => [
            'title'   => 'Elección de la chapa antidesgaste en la caja de la bañera: Hardox 400 vs. 450',
            'excerpt' => 'La diferencia entre las dos clases de dureza es de 50 Brinell sobre el papel; en campo es, ni más ni menos, el equilibrio entre la vida de la caja, la tara y el coste de procesado.',
            'content' => '
                <p>En la caja de una bañera, la chapa antidesgaste es la superficie en contacto permanente con el material transportado y la que determina la vida económica de la caja. Las clases de dureza 400 y 450 de los aceros antidesgaste son las dos elecciones más habituales en este campo; la decisión correcta depende del carácter de la carga transportada.</p>
                <h2>El carácter de las dos clases</h2>
                <p>La clase 400 Brinell ofrece un perfil equilibrado entre dureza y tenacidad: se puede plegar, es fácil de soldar y su riesgo de agrietamiento bajo cargas de impacto es bajo. La clase 450, con una dureza aproximadamente un diez por ciento mayor, proporciona una vida claramente más larga frente al desgaste por deslizamiento; a cambio, el conformado y el taladrado exigen más cuidado.</p>
                <h2>Elección según el tipo de carga</h2>
                <ul>
                    <li><strong>Movimiento de tierras, tierra, arena:</strong> La clase 400 suele bastar; destaca su ventaja de tenacidad</li>
                    <li><strong>Piedra machacada, escombro, fresado de asfalto:</strong> La clase 450 marca la diferencia de vida frente al desgaste por deslizamiento</li>
                    <li><strong>Roca gruesa y carga con impacto:</strong> Las soluciones mixtas —fondo en 450, laterales en 400— dan resultados equilibrados</li>
                </ul>
                <p>La ecuación que no debe olvidarse es esta: una chapa más dura puede usarse más fina; la sección reducida baja la tara y en cada viaje se transporta más carga. Por eso la elección de la chapa no es una cuestión de catálogo de materiales, sino un cálculo de economía operativa.</p>
            ',
        ],

        'silo-tipper-wind-resistance' => [
            'title'   => '¿Cómo reducir la resistencia al viento en los semirremolques silo y bañera?',
            'excerpt' => 'La cuba cilíndrica del silo parte con una ventaja aerodinámica; la bañera, con su caja abierta, con una desventaja. En ambos tipos, la ganancia llega con la disciplina de los detalles.',
            'content' => '
                <p>La aerodinámica de los semirremolques sin caja se descuidó durante mucho tiempo; sin embargo, las investigaciones de campo muestran que este segmento acumula una parte considerable del kilometraje total de los vehículos pesados y que su potencial de mejora sigue siendo grande.</p>
                <h2>Prioridades en el silo</h2>
                <p>La cuba cilíndrica es un buen punto de partida para el flujo; el problema está en los añadidos. Los tubos de descarga, las escaleras, la pasarela superior y el grupo de válvulas crean islas de turbulencia alrededor de la cuba limpia. Empotrar este equipamiento en la línea de la cuba o cubrirlo con tapas, combinado con los faldones laterales, aporta una ganancia significativa.</p>
                <h2>Prioridades en la bañera</h2>
                <p>La boca abierta de la caja actúa a alta velocidad casi como un freno de aire. Los sistemas de lona automáticos no son críticos solo para la seguridad de la carga, sino también para la aerodinámica: una lona cerrada y tensa corta en gran medida la turbulencia del interior de la caja. La alineación del frontal con la cabina de la tractora y la ordenación de los bajos con faldones cortos son las partidas de ganancia restantes.</p>
                <ul>
                    <li>Carene o empotre todo el equipamiento añadido en el sentido del flujo</li>
                    <li>Mantenga la lona cerrada en cada viaje, incluso en los retornos en vacío</li>
                    <li>Los faldones laterales cortos y resistentes se adaptan a las condiciones de obra</li>
                    <li>Actualice el reglaje del spoiler de la tractora según la altura de la caja</li>
                </ul>
                <h2>Referencias</h2>
                <ul>
                    <li>Durbin, T. D., Johnson, K. C., Jung, H. &amp; Miller, J. W. (2021). Characterizing non-box trailer activity and aerodynamic devices for greenhouse gas emissions reductions. <em>Transportation Research Part D: Transport and Environment</em>, 98, 102763. DOI: 10.1016/j.trd.2021.102763</li>
                </ul>
            ',
        ],

        'lowbed-high-strength-steel' => [
            'title'   => 'Aumentar la capacidad de carga de las góndolas con acero de alta resistencia',
            'excerpt' => 'Más carga con las mismas dimensiones exteriores, un pocillo más bajo y una vida a fatiga más larga: el acero de alta resistencia es la revolución silenciosa de la ingeniería de la góndola.',
            'content' => '
                <p>En una góndola compiten dos valores: la capacidad de carga y la altura de carga. Con el acero estructural clásico, mejorar uno de los dos solía significar renunciar al otro; los aceros de alta resistencia han eliminado en gran medida ese dilema.</p>
                <h2>¿De dónde viene la ganancia de ingeniería?</h2>
                <p>Un material cuyo límite elástico se duplica soporta la misma carga con una sección más fina. En el caso concreto de la góndola, la contrapartida es tangible: una geometría más esbelta pero más fuerte en la zona del cuello, un perfil más bajo en el pocillo y, en total, toneladas de ganancia de tara. El peso ganado se suma directamente a la capacidad de carga, mientras que el pocillo rebajado facilita que las cargas de gran gálibo se mantengan dentro de los límites autorizados.</p>
                <h2>Puntos que exigen atención</h2>
                <ul>
                    <li>El acero de alta resistencia exige el pleno cumplimiento de los procedimientos de soldadura; el control del aporte térmico es crítico</li>
                    <li>En las secciones adelgazadas deben verificarse además el pandeo y la estabilidad local</li>
                    <li>En las zonas de rampas y pocillo que reciben cargas puntuales debe planificarse un refuerzo local</li>
                    <li>La reparación debe realizarse con procedimientos aprobados por el fabricante y material equivalente</li>
                </ul>
                <p>Conclusión: una góndola de acero de alta resistencia bien diseñada es un vehículo que hace más trabajo dentro de los mismos límites legales — y para la flota eso significa ingresos directos.</p>
            ',
        ],

        'gap-fairing-systems' => [
            'title'   => 'Cerrar el hueco entre la tractora y el semirremolque: los sistemas de gap fairing',
            'excerpt' => 'El metro de hueco entre la cabina y el semirremolque es una gran fuente de resistencia, sobre todo con viento cruzado. Los sistemas de cierre del hueco apuntan a esa pérdida.',
            'content' => '
                <p>El hueco entre la cabina de la tractora y el frontal del semirremolque parece inocente con viento de frente; pero cuando un ligero viento cruzado entra en ese intervalo, el flujo golpea la esquina delantera del semirremolque y la resistencia se dispara. Las investigaciones señalan la zona del hueco como una de las áreas de mejora de mayor potencial en la aerodinámica del vehículo pesado.</p>
                <h2>Familias de soluciones</h2>
                <ul>
                    <li><strong>Extensiones de cabina:</strong> Los paneles laterales y superiores añadidos detrás de la cabina estrechan el hueco</li>
                    <li><strong>Deflector frontal del semirremolque:</strong> Superficies directrices montadas en la esquina superior delantera del semirremolque</li>
                    <li><strong>Gestión activa del hueco:</strong> Reducción de la distancia del king-pin al mínimo compatible con la operación</li>
                    <li><strong>Sistemas de cortina flexible:</strong> Revestimientos blandos que se deforman en el giro y se cierran en recta</li>
                </ul>
                <h2>El equilibrio: aerodinámica y maniobra</h2>
                <p>Anular el hueco por completo es imposible; en el giro el semirremolque no rota en el mismo eje que la cabina, y esa distancia es el margen de seguridad de ese movimiento. Un buen diseño de gap fairing es el que "elimina" aerodinámicamente el hueco en marcha recta e impide el contacto mecánico en el giro. Las mediciones reales en carretera confirman que la gestión del hueco, combinada con los faldones laterales y los tratamientos traseros, amplifica de forma notable el ahorro total.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                    <li>Madhusudhanan, A. K., Na, X., Boies, A. &amp; Cebon, D. (2021). Effects of semi-trailer modifications on HGV fuel consumption. <em>Transportation Research Part D: Transport and Environment</em>, 92, 102717. DOI: 10.1016/j.trd.2021.102717</li>
                </ul>
            ',
        ],

        'lean-material-management' => [
            'title'   => 'Gestión lean de materiales y estrategias de reducción de mermas en la fabricación de semirremolques',
            'excerpt' => 'Tanto como cada pieza cortada de la chapa, el retal que no puede cortarse es también una partida de coste. La gestión lean de materiales reduce la merma empezando en la mesa de diseño.',
            'content' => '
                <p>En la fabricación de semirremolques el material es la mayor partida del coste total; la merma es la fuga invisible de esa partida. Los principios de la producción lean enseñan a atrapar la merma no en el taller, sino en un punto mucho más temprano: en las fases de diseño y planificación.</p>
                <h2>Cinco prácticas que reducen la merma</h2>
                <ul>
                    <li><strong>Optimización del anidado (nesting):</strong> El encaje por software de los planes de corte CNC aumenta de forma notable la tasa de aprovechamiento de la chapa</li>
                    <li><strong>Banco de secciones estándar:</strong> Una biblioteca de secciones comunes, en lugar de perfiles especiales por proyecto, permite usar los sobrantes en el siguiente trabajo</li>
                    <li><strong>Stock basado en tirón (pull):</strong> El material se llama según el plan de producción; desaparecen las largas esperas expuestas a la corrosión y a los daños</li>
                    <li><strong>Sistema de seguimiento de retales:</strong> Los retales de corte etiquetados se priorizan como fuente primaria en los trabajos de piezas pequeñas</li>
                    <li><strong>Segregación de la chatarra:</strong> La recogida separada de los residuos de acero, aluminio y composite eleva el valor de recuperación</li>
                </ul>
                <h2>La conexión con la economía circular</h2>
                <p>Los pilotos industriales del sector del vehículo pesado han demostrado que la recuperación de materiales y la refabricación aportan un retorno no solo ambiental, sino directamente económico. Cuando la cadena que empieza con la disciplina de mermas en la línea de producción se prolonga hasta el desmontaje y la valorización del vehículo al final de su vida, la eficiencia de materiales deja de ser una partida de coste para convertirse en una ventaja competitiva.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Saidani, M., Yannou, B., Leroy, Y. &amp; Cluzel, F. (2020). Dismantling, remanufacturing and recovering heavy vehicles in a circular economy. <em>Resources, Conservation and Recycling</em>, 156, 104684. DOI: 10.1016/j.resconrec.2020.104684</li>
                </ul>
            ',
        ],

        'lowbed-heavy-haul-aerodynamics' => [
            'title'   => 'Soluciones aerodinámicas en góndolas y transporte pesado para ahorrar combustible',
            'excerpt' => 'En el transporte pesado la aerodinámica suele ser lo último en lo que se piensa; sin embargo, los kilómetros de retorno en vacío y el alto kilometraje anual crean también en este segmento un área real de ahorro.',
            'content' => '
                <p>En las operaciones de góndola lo primero que viene a la mente son los trámites de permisos, las cargas por eje y el plan de ruta; la aerodinámica se despacha con un "a estas velocidades no se nota". Los datos de campo dicen lo contrario: las investigaciones que estudian el segmento de semirremolques sin caja muestran que estos vehículos recorren a velocidad de crucero más kilómetros de lo que se cree, y una parte importante de ellos en vacío.</p>
                <h2>El retorno en vacío: la oportunidad oculta</h2>
                <p>La máquina de obra sobre una góndola cargada es un perfil aerodinámicamente impredecible y el margen de intervención es limitado. Pero en el retorno en vacío el panorama cambia: la plataforma baja y plana se presta muy bien a una ordenación adaptada al flujo. En las operaciones con alta proporción de kilómetros en vacío, la ganancia viene directamente de ahí.</p>
                <h2>Soluciones aplicables</h2>
                <ul>
                    <li>Paneles de faldón cortos y resistentes al impacto en los laterales del chasis</li>
                    <li>Deflector en la zona del cuello que dirige el flujo sobre la plataforma</li>
                    <li>Bloqueo horizontal de las rampas en ruta para formar una superficie plana</li>
                    <li>En el viaje cargado, posicionamiento de la carga lo más baja y adelantada posible</li>
                </ul>
                <p>Es importante mantener una expectativa realista: la tasa de ganancia no es tan alta como en los semirremolques de caja. Pero en una flota de transporte pesado que recorre cientos de miles de kilómetros al año, incluso un ahorro en la banda del uno al dos por ciento equivale a una cifra que ninguna empresa dejaría sobre la mesa.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Durbin, T. D., Johnson, K. C., Jung, H. &amp; Miller, J. W. (2021). Characterizing non-box trailer activity and aerodynamic devices for greenhouse gas emissions reductions. <em>Transportation Research Part D: Transport and Environment</em>, 98, 102763. DOI: 10.1016/j.trd.2021.102763</li>
                </ul>
            ',
        ],

        'tanker-corrosion-material-coating' => [
            'title'   => 'Elección correcta de materiales y recubrimientos contra la corrosión en los semirremolques cisterna',
            'excerpt' => 'En la cisterna la corrosión no es solo un problema estético: es un riesgo estructural que devora el espesor de pared y acorta la vida de inspección. La defensa empieza con la elección del material.',
            'content' => '
                <p>La cuba de una cisterna sufre el ataque de la corrosión en dos frentes: desde el interior, el efecto químico del producto transportado; desde el exterior, la sal de la carretera y la humedad atmosférica. Un material equivocado o un recubrimiento descuidado adelgaza el espesor de pared año tras año, lo que afecta directamente tanto a la seguridad del transporte como a los resultados de las inspecciones periódicas.</p>
                <h2>Material de la cuba según el producto</h2>
                <ul>
                    <li><strong>Combustibles y aceites minerales:</strong> El aluminio es el estándar; es ligero y estable frente a los derivados del petróleo</li>
                    <li><strong>Ácidos y químicos agresivos:</strong> Se requiere acero inoxidable o acero al carbono con revestimiento interior de caucho/ebonita</li>
                    <li><strong>Productos de grado alimentario:</strong> El acero inoxidable electropulido resuelve a la vez la higiene y la corrosión</li>
                    <li><strong>Transporte químico multiproducto:</strong> No debe decidirse el material sin una tabla de compatibilidad de productos</li>
                </ul>
                <h2>Recubrimiento y disciplina operativa</h2>
                <p>En la superficie exterior, una imprimación rica en zinc con acabado de poliuretano es un sistema probado contra la sal de carretera; en los puntos de contacto chasis-cuba son críticos los detalles de aislamiento que evitan la formación de pares galvánicos. En la superficie interior, la integridad del recubrimiento debe auditarse periódicamente y el procedimiento de lavado en los cambios de producto debe cumplirse sin concesiones. La gestión de la corrosión no es una elección puntual, sino una línea de defensa sostenida conjuntamente por el material, el recubrimiento y los hábitos de operación.</p>
            ',
        ],

        'what-is-e-trailer' => [
            'title'   => '¿Qué es el e-Trailer? El principio de funcionamiento de los semirremolques de eje eléctrico',
            'excerpt' => 'Equipado con un eje de motor eléctrico y un paquete de baterías, el semirremolque se convierte en una fuente de tracción auxiliar para la tractora; el concepto de e-trailer constituye el brazo del semirremolque en la electrificación del vehículo pesado.',
            'content' => '
                <p>El semirremolque clásico es una carga pasiva detrás de la tractora: no consume energía, pero tampoco la genera. El e-trailer cambia esa definición. Al menos uno de los ejes se equipa con un motor eléctrico, y en el chasis se instalan un paquete de baterías y electrónica de potencia; el semirremolque se convierte así en un componente activo que recupera energía al frenar y, cuando se necesita, apoya a la tractora.</p>
                <h2>¿Cómo funciona el sistema?</h2>
                <p>El eje eléctrico, en los tramos de deceleración y bajada, funciona como un generador y escribe la energía cinética del vehículo en la batería. En el arranque, en rampa o al adelantar, ese mismo motor entra en acción en sentido de tracción y reduce la potencia exigida al motor de la tractora. El software de control trabaja de forma sincronizada con las señales de freno y acelerador de la tractora, logrando una transición tan suave que el conductor apenas la percibe.</p>
                <h2>¿Qué aporta a la flota?</h2>
                <ul>
                    <li>Reducción del consumo de combustible y de las emisiones en la tractora diésel</li>
                    <li>Mayor autonomía en la tractora eléctrica; reparto de la carga de la batería</li>
                    <li>Alimentación sin diésel de consumidores auxiliares como la unidad frigorífica</li>
                    <li>Mayor vida de las pastillas de freno al reducirse la carga del sistema de frenado</li>
                </ul>
                <p>El punto más fuerte del e-trailer es su compatibilidad con la flota existente: el mismo semirremolque aporta ahorro hoy detrás de una tractora diésel y mañana será un socio de autonomía detrás de una tractora eléctrica. Para las empresas que quieren iniciar el camino de la electrificación sin cambiar la tractora, el e-trailer es una puerta de entrada gradual y de bajo riesgo.</p>
            ',
        ],

        'e-axle-energy-recovery' => [
            'title'   => 'Tecnología de eje eléctrico (e-Axle): recuperación de energía desde el semirremolque',
            'excerpt' => 'El eje eléctrico, que devuelve a la batería la energía que se pierde como calor al frenar, transforma el semirremolque de una carga pasiva en un componente que recolecta energía.',
            'content' => '
                <p>Cuando un vehículo pesado desacelera, una enorme cantidad de energía cinética se convierte en calor en las pastillas de freno y se disipa en el aire. El punto de partida de la tecnología de eje eléctrico (e-axle) es precisamente ese desperdicio: el motor pasa a modo generador, convierte la energía de deceleración en electricidad y la almacena en la batería instalada en el semirremolque.</p>
                <h2>Las fuentes de la recuperación</h2>
                <p>Los momentos de mayor recolección son las bajadas largas y el tráfico repetitivo de parada y arranque. En la bajada ya se requiere frenar para mantener la velocidad constante; el e-axle convierte esa resistencia en generación eléctrica y además reduce el calentamiento de los frenos de servicio. En el reparto urbano, cada parada añade contribuciones pequeñas pero constantes a la batería.</p>
                <h2>¿A dónde va la energía recolectada?</h2>
                <ul>
                    <li>Se devuelve como apoyo de tracción en el arranque y en la subida de rampas</li>
                    <li>Se emplea en la alimentación eléctrica de la unidad de refrigeración frigorífica</li>
                    <li>Alimenta sistemas auxiliares como el eje elevador, el elevador de la puerta trasera y el alumbrado</li>
                    <li>Aporta a la autonomía total cuando se combina con una tractora eléctrica</li>
                </ul>
                <p>En el lado de la ingeniería el asunto crítico es la integración: el e-axle debe comunicarse sin fisuras con el sistema de frenos y la unidad de control del vehículo; el par de recuperación nunca debe poner en riesgo el agarre ni la estabilidad. En un sistema bien ajustado el conductor no percibe ninguna diferencia; la única diferencia visible es la factura de combustible y las partidas de mantenimiento de frenos. La recuperación de energía es una partida de ingresos silenciosa que se escribe directamente en el coste total de propiedad del semirremolque.</p>
            ',
        ],

        'electric-reefer-units' => [
            'title'   => 'Alternativas eléctricas al enfriador diésel en semirremolques frigoríficos',
            'excerpt' => 'El motor diésel propio de la unidad frigorífica es la principal fuente de ruido y de emisión local; la refrigeración eléctrica apoyada en batería y e-axle cambia este panorama.',
            'content' => '
                <p>La unidad de refrigeración de los semirremolques frigoríficos funciona tradicionalmente con su propio pequeño motor diésel. Este motor consume combustible durante horas incluso con el vehículo parado, genera ruido y se convierte en fuente de emisión local en la ciudad. Las alternativas eléctricas apuntan exactamente a estos tres problemas.</p>
                <h2>Los pilares de la refrigeración eléctrica</h2>
                <ul>
                    <li><strong>Paquete de baterías:</strong> El paquete montado en el chasis alimenta el compresor de refrigeración sin diésel</li>
                    <li><strong>Recuperación por e-axle:</strong> La energía de frenado renueva continuamente la batería durante la marcha</li>
                    <li><strong>Conexión a red:</strong> En el depósito y en el muelle de carga la unidad funciona enchufada a la red</li>
                    <li><strong>Modo híbrido:</strong> En los vehículos de la etapa de transición se conserva el diésel como potencia de reserva</li>
                </ul>
                <h2>Ganancias operativas</h2>
                <p>El beneficio más visible de la refrigeración eléctrica es el silencio: en las zonas que aplican restricciones de ruido al reparto nocturno, la unidad eléctrica amplía de hecho la ventana de entrega. La eliminación de la emisión de escape puede ser determinante para entrar en zonas de bajas emisiones. En el lado del mantenimiento, el motor eléctrico, con menos piezas móviles, genera menos partidas de servicio que la unidad diésel.</p>
                <p>La pregunta clave en la planificación es el presupuesto energético: el rango de temperatura del producto transportado, la frecuencia de apertura de puertas y la duración de la ruta determinan la capacidad de la batería. Un sistema de refrigeración eléctrico correctamente dimensionado libera la cadena de frío del olor y el ruido del diésel, a la vez que reduce el coste operativo.</p>
            ',
        ],

        'solar-panels-on-trailers' => [
            'title'   => 'Paneles solares en el techo del semirremolque: ¿qué producen y para qué alcanzan?',
            'excerpt' => 'La amplia superficie plana del techo del semirremolque es una superficie natural para los paneles fotovoltaicos flexibles; pero hay que fijar bien la expectativa: es una fuente de apoyo, no de tracción.',
            'content' => '
                <p>El techo de un semirremolque estándar es una de las mayores superficies sin aprovechar de la carretera. Con el desarrollo de los paneles fotovoltaicos delgados y flexibles, esta superficie ya puede producir energía; la pregunta es: ¿para qué alcanza realmente la electricidad producida?</p>
                <h2>Usos realistas</h2>
                <ul>
                    <li>Alimentación ininterrumpida de la telemática, el seguimiento GPS y los sensores de puerta</li>
                    <li>Renovación durante todo el día de la batería del elevador de la puerta trasera</li>
                    <li>Eliminación del problema de descarga de la batería del vehículo estacionado</li>
                    <li>Apoyo al paquete de baterías en la unidad frigorífica; reducción del tiempo de funcionamiento del diésel</li>
                    <li>Funcionamiento del alumbrado interior y los enchufes independiente de la red</li>
                </ul>
                <h2>Fijar bien la expectativa</h2>
                <p>El panel solar no es una fuente de tracción; la potencia necesaria para mover un vehículo pesado supera con creces la energía que puede recolectarse en la superficie del techo. El papel correcto del panel es de apoyo: al asumir los consumidores auxiliares, reduce la carga del alternador y de la unidad diésel, y alarga la vida de las baterías. La producción fluctúa según la estación, la latitud y la disposición del estacionamiento, por lo que el sistema siempre debe diseñarse junto con el depósito de baterías.</p>
                <p>Los puntos a cuidar en el montaje están claros: los paneles deben fijarse mediante adhesivo o perfiles sin perforar la estructura del techo, los pasos de cable deben quedar estancos y hay que tener en cuenta el peso total y la carga de viento. Un sistema de techo bien concebido es una inversión práctica de eficiencia que se amortiza en silencio.</p>
            ',
        ],

        'trailer-design-for-electric-trucks' => [
            'title'   => 'Diseño de semirremolque compatible con tractoras eléctricas: ¿en qué hay que fijarse?',
            'excerpt' => 'El semirremolque acoplado a una tractora eléctrica ya no solo transporta carga; se convierte en socio directo de la autonomía, la eficiencia y el plan de carga.',
            'content' => '
                <p>En la tractora eléctrica cada kilovatio-hora es valioso; en un vehículo cuyo depósito no puede llenarse en minutos, cada resistencia adicional que genera el semirremolque resta directamente autonomía. Por eso el pliego de especificaciones del semirremolque también debe cambiar en las empresas que se preparan para una flota eléctrica.</p>
                <h2>Los dos enemigos de la autonomía: resistencia y peso</h2>
                <p>La resistencia aerodinámica es la principal partida de consumo de energía a velocidad de crucero; el equipo aerodinámico como el faldón lateral, la gestión del hueco y el tratamiento trasero se vuelve más crítico en la combinación eléctrica que en la diésel. Los neumáticos de baja resistencia a la rodadura y la correcta alineación de los ejes forman parte del mismo cálculo. Y cada kilogramo ganado a la tara se escribe en la autonomía o en la carga útil; la estrategia de aligeramiento con material ligero recupera valor en la era eléctrica.</p>
                <h2>Lista de comprobación de compatibilidad</h2>
                <ul>
                    <li>Interfaz eléctrica: compatibilidad de la línea de alta tensión y del protocolo de comunicación para el e-axle o la unidad frigorífica eléctrica</li>
                    <li>Intercambio de datos: que la telemetría del semirremolque aporte información de peso y resistencia al cálculo de autonomía de la tractora</li>
                    <li>Zona del king-pin: control de la holgura de giro frente a la disposición de chasis y batería, que difiere en algunas tractoras eléctricas</li>
                    <li>Coordinación de frenado: funcionamiento equilibrado del sistema de frenos del semirremolque junto con el frenado regenerativo</li>
                </ul>
                <p>Resumen: la compatibilidad con la tractora eléctrica no es una simple cuestión de equipamiento, sino una filosofía de diseño. Si el semirremolque que se encarga hoy va a pasar una parte importante de su vida detrás de una tractora eléctrica, el pliego debe redactarse en consecuencia.</p>
            ',
        ],

        'battery-trailer-weight-range' => [
            'title'   => 'Equilibrio entre peso y autonomía en los semirremolques con apoyo de batería',
            'excerpt' => 'Una batería más grande significa más tiempo de apoyo eléctrico; pero cada celda añadida resta carga útil. El dimensionamiento correcto es el equilibrio entre estos dos extremos.',
            'content' => '
                <p>El dilema fundamental de los semirremolques con apoyo de batería es simple: almacenar energía requiere peso, y el peso resta carga útil o eficiencia. En el transporte por carretera, donde el peso total máximo está limitado por ley, cuanto mayor es el paquete de baterías, menor es la carga útil; y cuanto menor es el paquete, más corta es la duración del apoyo eléctrico.</p>
                <h2>Las preguntas que fijan el equilibrio</h2>
                <ul>
                    <li>Perfil de operación: ¿larga distancia o reparto? En rutas con mucho arranque y parada, la recuperación renueva continuamente la batería y basta un paquete más pequeño</li>
                    <li>Carácter de la carga: en cargas que se llenan por volumen, el margen de peso ya está libre; la batería se transporta sin penalización</li>
                    <li>Topografía: las rutas con subidas y bajadas aumentan el potencial de recuperación</li>
                    <li>Posibilidad de carga: si hay carga regular en el depósito, el paquete puede reducirse</li>
                </ul>
                <h2>El lado de la ingeniería</h2>
                <p>La ubicación de la batería no es solo una cuestión de volumen; la posición del paquete en el chasis afecta al centro de gravedad y a las cargas por eje, y con ello al equilibrio del vehículo. Un paquete colocado bajo y entre los ejes puede incluso contribuir al agarre. La refrigeración, la protección contra impactos y el acceso de servicio deben planificarse desde el inicio del diseño.</p>
                <p>En conclusión, no existe una única longitud de batería correcta; la correcta se obtiene del cálculo del presupuesto energético hecho con los datos reales de ruta de la flota. El error más caro no es comprar el paquete más grande, sino comprar el paquete que no se ajusta a la operación.</p>
            ',
        ],

        'charging-infrastructure-fleet' => [
            'title'   => 'Infraestructura de carga y planificación del depósito: guía de transición a la flota eléctrica',
            'excerpt' => 'Pedir vehículos eléctricos es la parte fácil; la verdadera transformación ocurre en el depósito. La capacidad de red, el plan de carga y la disposición del terreno son los determinantes reales de la transición.',
            'content' => '
                <p>El error más frecuente en la transición a la flota eléctrica es empezar la planificación por el vehículo. La hoja de ruta de los operadores experimentados funciona al revés: primero el depósito, luego el vehículo. Porque si la infraestructura de carga es insuficiente, hasta el mejor vehículo eléctrico se convierte en capital parado en el patio.</p>
                <h2>Los pasos de la planificación del depósito</h2>
                <ul>
                    <li><strong>Análisis de red:</strong> Medición de la potencia de conexión eléctrica actual de la instalación; contacto temprano con la compañía distribuidora si es necesario aumentar la capacidad del transformador y de la línea</li>
                    <li><strong>Perfil de carga:</strong> Determinación de las horas que los vehículos pasan en el depósito; la carga lenta durante toda la noche es en la mayoría de las operaciones tanto más barata como suficiente frente a la carga rápida</li>
                    <li><strong>Gestión inteligente de carga:</strong> Un software que carga todos los vehículos no a la vez, sino por orden de prioridad; reduce la demanda de potencia pico y la factura eléctrica</li>
                    <li><strong>Disposición del terreno:</strong> Ubicación de los puntos de carga de forma que no interrumpan el flujo de estacionamiento y maniobra; posibilidad de carga con el semirremolque enganchado</li>
                    <li><strong>Margen de futuro:</strong> Dimensionar los canales de cable y la capacidad del cuadro no para la flota de hoy, sino para el objetivo futuro</li>
                </ul>
                <h2>Transición gradual</h2>
                <p>El patrón común de los casos de éxito es el piloto: primero se electrifican unas pocas rutas previsibles que vuelven al depósito cada noche; se recopilan datos reales de consumo y la infraestructura se escala con esos datos. Añadidos como los paneles solares y el depósito fijo de baterías son los siguientes pasos para gestionar el coste eléctrico. La flota eléctrica no es un proyecto de compra, sino un proyecto de infraestructura; el presupuesto y el calendario deben fijarse en consecuencia.</p>
            ',
        ],

        'hydrogen-freight-trailers' => [
            'title'   => 'El hidrógeno en el transporte de mercancías: ¿qué cambiará en el lado del semirremolque?',
            'excerpt' => 'Las tractoras de pila de combustible prometen superar los límites de la batería en larga distancia; para los fabricantes de semirremolques, el hidrógeno significa a la vez cliente y carga.',
            'content' => '
                <p>El hidrógeno es el candidato de la larga distancia en la electrificación del vehículo pesado: la pila de combustible genera la electricidad a bordo, el tiempo de repostaje se acerca al del diésel y no hay que transportar el peso de la batería. ¿Qué cambia entonces en el lado del semirremolque en esta transformación? La respuesta se agrupa en dos apartados.</p>
                <h2>Primer cambio: el semirremolque remolcado</h2>
                <p>Para el semirremolque detrás de una tractora de pila de combustible, las expectativas son en gran medida las mismas que con la tractora eléctrica: baja resistencia aerodinámica, baja resistencia a la rodadura y ligereza. Como el hidrógeno es un vector energético caro, la presión de eficiencia no disminuye, sino que aumenta. Los semirremolques con apoyo de e-axle aportan la misma contribución al sistema de pila de combustible: se recupera la energía de frenado y baja la demanda de potencia pico.</p>
                <h2>Segundo cambio: el propio hidrógeno será carga</h2>
                <p>A medida que crezca la economía del hidrógeno, crecerá también la necesidad de transportarlo desde los puntos de producción hasta las estaciones de repostaje; esto significa un nuevo mercado para los fabricantes de cisternas y de haces de tubos (tube trailer). Estos vehículos no son cisternas corrientes:</p>
                <ul>
                    <li>Diseño de recipiente y equipo certificado apto para el transporte de gas a presión</li>
                    <li>Aumento de la carga útil mediante tecnologías de tubos ligeros con bobinado de composite</li>
                    <li>Estanqueidad y selección de materiales adaptadas a la estructura molecular pequeña del hidrógeno</li>
                    <li>Pleno cumplimiento de los requisitos de la clase de gas peligroso de la normativa ADR</li>
                </ul>
                <p>En resumen, el hidrógeno llegará al sector del semirremolque por dos vías: como fuente de energía de los vehículos que remolcamos y como carga de la nueva generación de cisternas que diseñaremos. El fabricante preparado en ambos frentes saldrá ganando.</p>
            ',
        ],

        'zero-emission-zones-europe' => [
            'title'   => 'Las zonas de cero emisiones se extienden por las ciudades europeas: ¿cómo deben prepararse las flotas?',
            'excerpt' => 'Las zonas de bajas y cero emisiones en las ciudades europeas se amplían de forma gradual; para las flotas que transportan carga al centro urbano, la pregunta ya no es si entrar, sino cómo hacerlo.',
            'content' => '
                <p>La calidad del aire y los objetivos climáticos en las ciudades europeas han dado lugar a zonas que escalonan el acceso de vehículos al centro urbano según su clase de emisión. El proceso, que comenzó con las zonas de bajas emisiones, evoluciona en muchas ciudades hacia zonas con objetivo de cero emisiones, y con el tiempo el alcance incluye también a los vehículos pesados. Toda flota que reparte en el centro urbano debe leer ya este mapa como una capa más de su plan de rutas.</p>
                <h2>Hoja de ruta para la preparación</h2>
                <ul>
                    <li><strong>Inventario de rutas:</strong> ¿Qué recorridos pasan hoy, o pasarán en un futuro próximo, por zonas restringidas? Primero hay que medir la exposición</li>
                    <li><strong>Asignación de vehículos:</strong> Asignar los vehículos más limpios de la flota a las rutas urbanas y los demás a las líneas interurbanas</li>
                    <li><strong>Modelo de transbordo:</strong> Realizar la última milla con vehículo eléctrico desde un centro de consolidación fuera de la ciudad</li>
                    <li><strong>Contribución del semirremolque:</strong> La unidad frigorífica eléctrica y el e-axle pueden facilitar el cumplimiento de las normas de la zona sin cambiar la tractora</li>
                    <li><strong>Seguimiento documental:</strong> El sistema de registro, etiqueta y exención de cada ciudad es distinto; el equipo de operaciones debe mantenerse actualizado</li>
                </ul>
                <h2>Convertir la restricción en ventaja</h2>
                <p>Estas zonas no son una eliminación, sino una oportunidad de diferenciación para las flotas que cumplen pronto las normas: poder entregar en horas y calles donde los competidores no pueden entrar es una ventaja comercial. La refrigeración eléctrica silenciosa abre la puerta al reparto nocturno; el certificado de vehículo limpio suma puntos en los pliegos de compra de los clientes corporativos. Prepararse tiene coste; no prepararse tiene más coste.</p>
            ',
        ],

        'electric-trailer-market-2030' => [
            'title'   => 'El mercado del semirremolque eléctrico camino a 2030: expectativas y realidades',
            'excerpt' => 'La tecnología del semirremolque eléctrico baja del expositor a la carretera; pero la velocidad de maduración del mercado no la determinará la tecnología, sino los estándares, la infraestructura y el cálculo del coste total.',
            'content' => '
                <p>Los semirremolques de eje eléctrico han dejado los stands de las ferias y han empezado a acumular kilómetros en flotas reales. Para responder con honestidad a la pregunta de hacia dónde irá el mercado en el camino hacia 2030, hay que separar las expectativas de las realidades.</p>
                <h2>Expectativas: las fuerzas que impulsan el crecimiento</h2>
                <p>Los objetivos europeos de carbono para el vehículo pesado empujan en la misma dirección a fabricantes y grandes flotas; los compromisos de emisiones de la cadena de suministro de los clientes corporativos se traducen en presión sobre los transportistas para usar vehículos limpios. Las zonas de bajas y cero emisiones en las ciudades amplían el campo de uso de las soluciones eléctricas. La tendencia de descenso a largo plazo del coste de las baterías y la maduración de la tecnología e-axle preparan el terreno técnico.</p>
                <h2>Realidades: quienes tienen el freno en la mano</h2>
                <ul>
                    <li>Estandarización: la interfaz eléctrica tractora-semirremolque aún no ha alcanzado un lenguaje común único; el inversor quiere estar seguro de que el semirremolque que compra se comunicará con las futuras tractoras</li>
                    <li>Infraestructura: la capacidad de carga del depósito y los plazos de conexión a la red pueden avanzar más despacio que la entrega de vehículos</li>
                    <li>Coste total: el plazo en que la diferencia de inversión inicial se compensa con el ahorro de combustible y mantenimiento varía mucho según el perfil de operación</li>
                    <li>Incertidumbre del mercado de segunda mano: el valor residual de una tecnología joven aún no está probado con datos de mercado</li>
                </ul>
                <p>El escenario realista no es una revolución sino una expansión gradual: primero grandes flotas que operan en rutas previsibles y de alto kilometraje; después, a medida que la tecnología se abarata y los estándares se asientan, una base más amplia. En este período la estrategia más valiosa es hacer cada nueva inversión en semirremolques con un pliego preparado para la electrificación.</p>
            ',
        ],

        'what-is-adr-basics' => [
            'title'   => '¿Qué es el ADR? Las reglas básicas del transporte de mercancías peligrosas',
            'excerpt' => 'Desde lo inflamable hasta lo corrosivo, el marco internacional ADR que regula el transporte seguro de mercancías peligrosas por carretera se sostiene sobre cuatro pilares: vehículo, equipo, documentación y persona.',
            'content' => '
                <p>Combustible, GLP, productos químicos industriales y cientos de productos más se transportan cada día por carretera; el nombre del marco internacional que garantiza la seguridad de ese transporte es ADR. Este acuerdo sobre el transporte internacional de mercancías peligrosas por carretera, aunque nació en Europa, constituye hoy la base de la normativa nacional en una amplia geografía, incluida Turquía.</p>
                <h2>La lógica del sistema: clasificar el riesgo, emparejar la medida</h2>
                <p>En el corazón de la normativa ADR está la clasificación: cada mercancía peligrosa se asigna a una clase según el peligro principal que representa (líquido inflamable, gas a presión, corrosivo, tóxico, etc.) y se identifica con un número de materia. Esa clase determina en cadena el embalaje, el tanque, el equipamiento del vehículo, el etiquetado y las condiciones de transporte.</p>
                <h2>Cuatro pilares fundamentales</h2>
                <ul>
                    <li><strong>Vehículo y tanque:</strong> Las cisternas de mercancías peligrosas se fabrican con un diseño homologado y demuestran su conformidad mediante inspecciones periódicas</li>
                    <li><strong>Equipo:</strong> Desde el extintor hasta las señales de advertencia, el vehículo lleva el equipo de seguridad adecuado a la clase transportada</li>
                    <li><strong>Documentación:</strong> El documento de transporte, las instrucciones escritas y los certificados de aprobación del vehículo son la identidad del viaje</li>
                    <li><strong>Persona:</strong> Los conductores se certifican con formación especial; las empresas trabajan con un consejero de seguridad para mercancías peligrosas</li>
                </ul>
                <p>Desde la óptica del fabricante, el ADR no es burocracia sino un pliego de ingeniería: desde el material del tanque hasta el tipo de válvula, desde la instalación eléctrica hasta el sistema de frenos, cada detalle se define según el riesgo de la materia a transportar. Una cisterna fabricada conforme a las normas no es solo la que pasa la inspección; es la que retiene su carga incluso en el peor de los días.</p>
            ',
        ],

        'partial-fill-tanker-rollover' => [
            'title'   => 'Riesgo de vuelco en cisternas parcialmente llenas: la física del oleaje del líquido',
            'excerpt' => 'Una cisterna llena hasta la mitad puede ser más peligrosa que una completamente llena: el líquido de superficie libre se desplaza junto con el vehículo en la curva y reduce el umbral de vuelco.',
            'content' => '
                <p>Un hecho contraintuitivo pero bien conocido en el terreno: una cisterna parcialmente llena puede ser más inestable que una completamente llena. La razón es la superficie libre del líquido. En un tanque totalmente lleno el líquido no puede moverse y se comporta como una carga sólida; en el llenado parcial, en cambio, cientos o miles de kilogramos de masa se desplazan libremente dentro del tanque.</p>
                <h2>La mecánica del oleaje</h2>
                <p>Cuando el vehículo entra en una curva, el líquido se acumula hacia el exterior por inercia; el centro de gravedad se desplaza tanto lateralmente como hacia arriba. El ancho efectivo que determina la resistencia al vuelco se estrecha así. Lo más insidioso es el efecto de retardo: la ola de líquido golpea el tanque un instante después del movimiento del volante. En maniobras sucesivas como un cambio de carril, si la ola se sincroniza con la oscilación del vehículo, cada oscilación crece respecto a la anterior y la estabilidad que siente el conductor se pierde de repente.</p>
                <h2>Factores que gobiernan el riesgo</h2>
                <ul>
                    <li>Tasa de llenado: la banda más crítica son los niveles de llenado medio, donde el líquido alcanza su mayor superficie libre</li>
                    <li>Sección del tanque: las secciones bajas y anchas reducen el centro de gravedad</li>
                    <li>Rompeolas y compartimentos: fragmentan el movimiento del líquido y rompen la energía de la ola</li>
                    <li>Disciplina de velocidad y maniobra: las entradas suaves de dirección no amplifican la ola</li>
                </ul>
                <p>La física de la cisterna parcialmente llena concierne tanto al diseñador como al conductor: el fabricante eleva el umbral con el diseño de rompeolas y sección; el conductor, con su elección de velocidad y maniobra, nunca se acerca a ese umbral. Los estudios de modelado académico confirman también que ambos frentes deben abordarse conjuntamente.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Yu, D. &amp; Chu, J. (2019). Study on roll-stability model optimization for partially filled tanker trucks. <em>Advances in Mechanical Engineering</em>, 11(4). DOI: 10.1177/1687814019837805</li>
                </ul>
            ',
        ],

        'tanker-baffle-compartment-design' => [
            'title'   => 'Diseño de compartimentos y rompeolas en cisternas: la arquitectura invisible de la seguridad',
            'excerpt' => 'Vista desde fuera, la cisterna es un cilindro liso; en su interior funciona una arquitectura compuesta por paredes de compartimentación y placas rompeolas que doman la fuerza del líquido.',
            'content' => '
                <p>La seguridad de una cisterna reside en gran medida en elementos que no se ven desde fuera: las paredes que dividen el interior del tanque y las placas rompeolas que cortan el flujo. Esta arquitectura interior gobierna el efecto de la carga líquida sobre la dinámica del vehículo y reduce directamente el riesgo de vuelco en llenado parcial.</p>
                <h2>La diferencia entre compartimento y rompeolas</h2>
                <p>Los dos se confunden a menudo, pero sus funciones son distintas. La <strong>pared de compartimento</strong> divide el tanque en cámaras estancas: se pueden transportar distintos productos en el mismo viaje, el llenado se gestiona por cámara y se limita el desplazamiento longitudinal del líquido. El <strong>rompeolas</strong>, en cambio, no es estanco; con sus aberturas de paso no retiene el líquido, sino que frena su movimiento. Impide que la ola corra libremente a lo largo del tanque y golpee los extremos, y fragmenta la energía del oleaje en partes pequeñas.</p>
                <h2>Características de un buen diseño interior</h2>
                <ul>
                    <li>Los espaciados de los rompeolas se eligen con la frecuencia adecuada para romper las longitudes de onda generadas por el frenado y la maniobra</li>
                    <li>Los orificios de paso de las placas se ubican de modo que no impidan el lavado ni el acceso para la inspección interior</li>
                    <li>El número de compartimentos se planifica junto con los escenarios de llenado: en qué orden se llenan las cámaras para que el centro de gravedad se mantenga más favorable</li>
                    <li>Las soldaduras de unión se verifican con un cálculo de vida a fatiga; un rompeolas desprendido se convierte en una masa libre dentro del tanque</li>
                </ul>
                <p>Los estudios de modelado sobre la estabilidad al vuelco muestran que el diseño de la estructura interior debe optimizarse junto con el cálculo de la dinámica del vehículo: el tanque no es solo un recipiente, sino un sistema de ingeniería que da forma al comportamiento de una carga en movimiento.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Yu, D. &amp; Chu, J. (2019). Study on roll-stability model optimization for partially filled tanker trucks. <em>Advances in Mechanical Engineering</em>, 11(4). DOI: 10.1177/1687814019837805</li>
                </ul>
            ',
        ],

        'adr-tanker-inspections' => [
            'title'   => 'Inspecciones ADR de cisternas: guía de los controles intermedios, periódicos y excepcionales',
            'excerpt' => 'La cisterna ADR es segura no el día que se fabrica, sino cada día que pasa la inspección. Resumimos qué son los controles intermedios, periódicos y excepcionales y cómo prepararse para ellos.',
            'content' => '
                <p>Cuando una cisterna de mercancías peligrosas se matricula y sale a la carretera, la historia no termina; la normativa ADR exige que el tanque vuelva a demostrar su conformidad a intervalos regulares. Un vehículo que se salta el calendario de inspección no puede transportar carga legalmente, aunque esté técnicamente en perfecto estado.</p>
                <h2>Tres tipos de inspección</h2>
                <ul>
                    <li><strong>Inspección periódica:</strong> Es el control más completo; incluye el examen interior y exterior del tanque, la prueba de presión y el control funcional del equipo. Se verifica de principio a fin la integridad estructural del tanque</li>
                    <li><strong>Inspección intermedia:</strong> Es un control reducido realizado entre dos inspecciones periódicas; predominan la prueba de estanqueidad y la verificación de que el equipo de servicio funciona correctamente</li>
                    <li><strong>Inspección excepcional:</strong> No depende del calendario; se vuelve obligatoria tras un accidente, un incendio, una reparación o cualquier suceso que pueda afectar a la seguridad del tanque</li>
                </ul>
                <h2>Claves para preparar la inspección</h2>
                <p>Las flotas experimentadas viven la inspección no como un examen, sino como el resultado natural del ciclo de mantenimiento. El primer requisito es limpiar y desgasificar debidamente la superficie interior del tanque antes de la inspección; el organismo inspector no entra en un tanque con residuos. Las carencias conocidas en válvulas, juntas de tapa y equipo de seguridad deben resolverse antes de la cita; cada defecto descubierto el día de la inspección significa tiempo adicional que retiene al vehículo fuera de servicio. La placa del tanque, los registros de pruebas anteriores y los certificados de aprobación deben presentarse completos.</p>
                <p>La responsabilidad también continúa del lado del fabricante: una cisterna bien diseñada se dibuja pensando en los puntos de acceso para la inspección. Un tanque fácil de inspeccionar es, a lo largo de su vida, un tanque que espera menos.</p>
            ',
        ],

        'static-electricity-grounding' => [
            'title'   => 'Electricidad estática y seguridad de puesta a tierra en cisternas de combustible',
            'excerpt' => 'El combustible que fluye acumula, por fricción, una carga eléctrica invisible; esta carga, suficiente para una sola chispa, se descarga sin peligro a tierra mediante la disciplina de puesta a tierra.',
            'content' => '
                <p>El peligro más insidioso en el llenado de combustible es invisible a simple vista: al fluir, el líquido acumula carga eléctrica por fricción con las superficies de tubos y mangueras. Esta carga acumulada dentro del tanque puede descargarse, en condiciones adecuadas, con una sola chispa; y la energía de esa chispa es más que suficiente para inflamar el vapor de combustible.</p>
                <h2>¿Dónde y cómo se acumula la carga?</h2>
                <p>La generación de carga aumenta con la velocidad del flujo; los filtros y los codos de tubería aceleran la generación al ampliar la superficie de fricción. Que el líquido llene el tanque por caída libre desde arriba (llenado por salpicadura) aumenta tanto la producción de vapor como la separación de carga; esta es una de las razones por las que se prefiere el llenado por el fondo. La carga dentro del tanque conserva su presencia durante un tiempo incluso después de terminado el llenado; por eso los tiempos de espera tras el llenado figuran en los procedimientos.</p>
                <h2>Línea de defensa: equipotencialidad y puesta a tierra</h2>
                <ul>
                    <li>Primer paso antes del llenado: conectar la pinza de puesta a tierra a una superficie metálica limpia; una superficie pintada o sucia falsea la conexión</li>
                    <li>Conexión equipotencial entre la cisterna, la isla de llenado y la tubería: si no hay diferencia de potencial, tampoco hay chispa</li>
                    <li>En sistemas con monitorización de puesta a tierra, que el llenado no comience sin verificar la conexión</li>
                    <li>Mantener la velocidad de flujo, especialmente al inicio del llenado, dentro de los límites definidos en el procedimiento</li>
                    <li>Control diario de cables trenzados rotos, pinzas sueltas y puntos de conexión corroídos</li>
                </ul>
                <p>En el lado del diseño, el enfoque de Barlas es claro: los puntos de puesta a tierra deben ser accesibles, estar señalizados y tener una conexión conductora continua con la carrocería. La electricidad estática no es un destino ingobernable, sino un riesgo que se anula con disciplina.</p>
            ',
        ],

        'eu-co2-standards-trailers' => [
            'title'   => 'Los estándares europeos de CO₂ llegan al vehículo pesado: qué significan para los fabricantes de semirremolques',
            'excerpt' => 'El alcance de los estándares de emisión de CO₂ de la UE para el vehículo pesado se amplía; el semirremolque ya no es una parte pasiva de la ecuación del combustible, sino un componente medido y documentado.',
            'content' => '
                <p>La Unión Europea endurece de forma gradual los estándares de emisión de CO₂ del vehículo pesado para reducir las emisiones procedentes del transporte por carretera. Este marco, que durante mucho tiempo solo concernía a los fabricantes de tractoras y camiones, se amplía ahora para abarcar también a los semirremolques: la contribución de los remolques y semirremolques al consumo de combustible se mide, se clasifica y se documenta.</p>
                <h2>¿Por qué ha entrado el semirremolque en la ecuación?</h2>
                <p>Buena parte de la resistencia aerodinámica de una combinación tractora-semirremolque procede de la carrocería del semirremolque; el peso en vacío y la resistencia a la rodadura de los neumáticos también afectan directamente al consumo. Los reguladores han asumido, a partir de este hecho, que los objetivos de emisiones no pueden alcanzarse solo con mejoras en el motor: un semirremolque eficiente significa un vehículo eficiente.</p>
                <h2>Consecuencias concretas para el fabricante</h2>
                <ul>
                    <li>El equipo aerodinámico, el aligeramiento y los neumáticos de baja resistencia se convierten en entradas estándar del diseño</li>
                    <li>La documentación del rendimiento de eficiencia de los productos mediante simulación estándar se incorpora a los procesos de producción</li>
                    <li>Los compradores de flotas exigen cada vez con más frecuencia datos de rendimiento de CO₂ en sus decisiones de compra</li>
                    <li>Para los fabricantes que exportan al mercado europeo, el cumplimiento se convierte en condición previa para el acceso al mercado</li>
                </ul>
                <p>Esta transformación no es una carga para el fabricante de semirremolques, sino un espacio de diferenciación. Los paquetes aerodinámicos, la ingeniería de materiales ligeros y el diseño orientado a la eficiencia se convierten, junto con la regulación, en un argumento de venta medible; el fabricante preparado se diferenciará de sus competidores a medida que los estándares se endurezcan.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'what-is-vecto-trailer' => [
            'title'   => '¿Qué es VECTO? ¿Cómo se calcula la emisión de carbono en los semirremolques?',
            'excerpt' => 'Europa calcula el consumo de combustible de los vehículos pesados mediante una simulación estándar en lugar de pruebas reales en carretera. Esta herramienta, llamada VECTO, procesa ahora también datos de semirremolques.',
            'content' => '
                <p>VECTO es la herramienta de software oficial desarrollada por la Comisión Europea que calcula el consumo de combustible y la emisión de CO₂ de los vehículos pesados mediante una simulación estándar. En lugar de someter cada vehículo por separado a una prueba en carretera, utiliza datos certificados de los componentes para generar un resultado comparable para todas las combinaciones.</p>
                <h2>¿Cómo funciona la simulación?</h2>
                <p>En el lado de la tractora entran como datos el motor, la transmisión y los ejes; en el lado del semirremolque, la resistencia aerodinámica, el peso en vacío y la resistencia a la rodadura de los neumáticos. El software ejecuta un viaje virtual sobre perfiles de misión estándar, como el de larga distancia y el de reparto regional, y reporta el valor de consumo y emisión por kilómetro. Así, los productos de distintos fabricantes quedan medidos con la misma vara.</p>
                <h2>Su significado para el semirremolque</h2>
                <ul>
                    <li>La contribución de los dispositivos aerodinámicos se refleja oficialmente en el cálculo a través de datos certificados</li>
                    <li>El aligeramiento se traduce directamente en la simulación en un menor consumo</li>
                    <li>La elección de neumáticos de baja resistencia a la rodadura se convierte en un parámetro medible</li>
                    <li>El comprador puede exigir datos producidos con el método estándar en lugar de una afirmación de catálogo</li>
                </ul>
                <p>Desde el punto de vista del fabricante de semirremolques, VECTO es un escaparate que hace visible la ingeniería de eficiencia: un paquete aerodinámico bien diseñado o un trabajo de aligeramiento exitoso ya marca la diferencia no solo en el terreno, sino también en el documento oficial de la mesa de compra. <strong>La medición estándar es el aliado más fuerte de la buena ingeniería.</strong></p>
            ',
        ],

        'eu-weights-dimensions-directive' => [
            'title'   => 'Cambian las normas europeas de peso y dimensiones: se abre la puerta a los dispositivos aerodinámicos y a los vehículos largos',
            'excerpt' => 'El marco de peso y dimensiones del transporte por carretera europeo se actualiza; comienza una nueva era para los dispositivos aerodinámicos plegables y las combinaciones más largas.',
            'content' => '
                <p>En las carreteras europeas, el peso y las dimensiones máximas de los vehículos pesados se determinan por normas comunes. Este marco se mantuvo estable durante muchos años; pero los objetivos de emisiones y la presión de eficiencia han puesto sobre la mesa su actualización. El cambio tiene dos ejes principales: la flexibilidad concedida al equipo aerodinámico y la apertura del camino a combinaciones de vehículos más largas.</p>
                <h2>Se abre espacio para los dispositivos aerodinámicos</h2>
                <p>En las normas clásicas, la longitud máxima prohibía de hecho cada centímetro añadido en la parte trasera del semirremolque; esto bloqueaba en la práctica soluciones probadas como el boat tail. El enfoque actualizado permite que, en determinadas condiciones, los dispositivos aerodinámicos plegables no se contabilicen en la longitud del vehículo. De forma similar, se concede espacio a formas de cabina alargadas adaptadas al flujo; así, la eficiencia deja de estar en conflicto con el límite legal de dimensiones.</p>
                <h2>Repercusiones en el diseño</h2>
                <ul>
                    <li>La zona de la puerta trasera y de las bisagras debe diseñarse junto con los mecanismos de panel plegable</li>
                    <li>El alumbrado y la señalización deben permanecer visibles tanto con el dispositivo abierto como cerrado</li>
                    <li>En las combinaciones largas deben verificarse de nuevo las cargas por eje y la capacidad de maniobra</li>
                    <li>Las normas de paso entre países deben incorporarse al plan de operación en las líneas internacionales</li>
                </ul>
                <p>Algunos estados miembros ya están probando las combinaciones de vehículos largos en sus propias redes; el debate avanza ahora hacia su uso transfronterizo. El mensaje para el fabricante de semirremolques es claro: quien diseñe plataformas modulares, aerodinámicas y adaptables a los nuevos escenarios de longitud, se estará preparando hoy para el mercado de mañana.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Van Raemdonck, G. M. R. &amp; Urila, I. (2019). A Study in Options to Improve Aerodynamic Profile of Heavy-Duty Vehicles in Europe. <em>Sustainability</em>, 11(19), 5519. DOI: 10.3390/su11195519</li>
                </ul>
            ',
        ],

        'eurovignette-co2-tolls' => [
            'title'   => 'Clases de CO₂ en el peaje: ¿cómo beneficia un semirremolque eficiente a la flota?',
            'excerpt' => 'Los sistemas europeos de peaje diferencian los vehículos según su rendimiento de CO₂; una combinación eficiente ya no ahorra solo en combustible, sino también en el peaje.',
            'content' => '
                <p>El peaje de autopistas y vías en Europa atraviesa una transformación de raíz: las tarifas se diferencian según la clase de rendimiento de CO₂ del vehículo. Mientras las combinaciones de baja clase de emisión pagan menos por kilómetro, para los vehículos ineficientes el coste de la vía se convierte en una partida de gasto permanente.</p>
                <h2>El lugar del semirremolque en la ecuación</h2>
                <p>Aunque la clase de tarifa se define a través del vehículo motor, el consumo real de la combinación lo determina en gran medida el semirremolque: la resistencia aerodinámica, el peso en vacío y la elección de neumáticos se reflejan directamente en la factura de combustible. Un semirremolque eficiente aporta ahorro de combustible en cada kilómetro y, además, mejora el rendimiento de emisiones general de la flota, apoyando los objetivos corporativos de carbono.</p>
                <h2>El retorno del semirremolque eficiente</h2>
                <ul>
                    <li>Menor consumo de combustible: la partida de ganancia más grande y de efecto más rápido</li>
                    <li>Menor coste de peaje en las rutas donde los sistemas de tarificación premian la eficiencia</li>
                    <li>Datos de emisión por viaje reportables al cliente y ventaja en licitaciones</li>
                    <li>Valor conservado en el mercado de segunda mano de un vehículo preparado para la regulación</li>
                </ul>
                <p>Para el gestor de flota el cálculo ha cambiado: la inversión en semirremolques ya debe evaluarse no solo por el precio de compra, sino por la suma del peaje y los costes de carbono a lo largo de los años. <strong>El paquete aerodinámico y el aligeramiento son, en esta nueva ecuación, inversiones que se pagan a sí mismas.</strong> A medida que los sistemas de tarificación premian la eficiencia, el semirremolque eficiente deja de ser un recipiente de carga pasivo para convertirse en un reductor de costes activo.</p>
            ',
        ],

        'extended-producer-responsibility' => [
            'title'   => 'Responsabilidad ampliada del productor: regulaciones de fin de vida en el semirremolque',
            'excerpt' => 'La responsabilidad del fabricante ya no termina en la entrega; las fases de desmontaje, reciclaje y refabricación del vehículo vuelven también a la mesa de diseño.',
            'content' => '
                <p>La responsabilidad ampliada del productor es el enfoque que vincula el coste ambiental de un producto al fabricante hasta el final de su vida. Este marco, una práctica consolidada en el automóvil de turismo, se amplía en Europa hacia el segmento del vehículo pesado y el semirremolque: cómo se desmontará el vehículo, qué materiales se recuperarán y cómo se gestionará el residuo son ya preguntas del fabricante.</p>
                <h2>¿Qué significa para el semirremolque?</h2>
                <p>El semirremolque, por su larga vida útil y su gran masa metálica, es en realidad un producto ventajoso en cuanto a recuperación; pero ese potencial solo se hace realidad con un diseño desmontable. Las uniones atornilladas en lugar de pegadas, los módulos separables en lugar de materiales mixtos, y el marcado trazable del tipo de material son las decisiones que determinan el valor de fin de vida.</p>
                <h2>Principios que vuelven a la mesa de diseño</h2>
                <ul>
                    <li>Arquitectura de uniones y módulos planificada pensando en el orden de desmontaje</li>
                    <li>Estructuración separable de las zonas de acero, aluminio y composite</li>
                    <li>Puntos de desconexión segura para el aceite hidráulico, la línea de frenos y los componentes electrónicos</li>
                    <li>Aptitud para la refabricación de grupos valiosos como el eje, la suspensión y el king-pin</li>
                </ul>
                <p>Los estudios de campo del sector del vehículo pesado muestran que el desmontaje y la refabricación son viables no solo ambiental sino también comercialmente. El fabricante que diseña el fin de vida se prepara tanto para la regulación que se aproxima como para dar a su producto una segunda vida económica.</p>
                <h2>Referencias</h2>
                <ul>
                    <li>Saidani, M., Yannou, B., Leroy, Y. &amp; Cluzel, F. (2020). Dismantling, remanufacturing and recovering heavy vehicles in a circular economy. <em>Resources, Conservation and Recycling</em>, 156, 104684. DOI: 10.1016/j.resconrec.2020.104684</li>
                </ul>
            ',
        ],

        'eu-roadworthiness-rules' => [
            'title'   => 'Normativa de inspección técnica periódica: condiciones para operar un semirremolque en el mercado europeo',
            'excerpt' => 'El semirremolque, como vehículo independiente de la tractora, está sujeto a inspección periódica; el camino para operar en las líneas europeas no pasa por aprobar la inspección, sino por estar preparado para ella cada día.',
            'content' => '
                <p>El semirremolque, aunque no sea un vehículo de motor, está sujeto por sí mismo a inspección en la normativa europea. La inspección técnica periódica es la verificación, por un ojo independiente, de los frenos, la suspensión, el alumbrado y la integridad estructural del vehículo; para una flota que opera en líneas internacionales, un certificado de inspección válido es la condición previa para no quedarse en la carretera y anular el riesgo de sanción.</p>
                <h2>Los principales apartados que se revisan en la inspección</h2>
                <ul>
                    <li><strong>Sistema de frenos:</strong> Reparto de la fuerza de frenado, fugas de aire y funciones del freno electrónico</li>
                    <li><strong>Alumbrado y señalización:</strong> Todas las luces, reflectores y el alumbrado de la matrícula</li>
                    <li><strong>Chasis y carrocería:</strong> Corrosión, grietas y deformación; especialmente en las zonas de soldadura</li>
                    <li><strong>Elementos de enganche:</strong> Desgaste del king-pin, patas de apoyo y argollas de enganche</li>
                    <li><strong>Neumáticos y grupo de ejes:</strong> Profundidad del dibujo, desgaste irregular y holguras de rodamientos</li>
                </ul>
                <h2>No aprobar, sino estar preparado</h2>
                <p>Las carencias que se cierran apresuradamente al acercarse la inspección son, en realidad, la fotografía del déficit de mantenimiento acumulado durante el año. Los controles intermedios regulares, los registros de servicio digitales y el seguimiento sistemático de las averías reportadas convierten la inspección de un examen en una verificación rutinaria. En el lado del fabricante, un diseño que facilita el acceso a los puntos de inspección —tapas abatibles, zonas de chasis visibles, conexiones de prueba de frenos accesibles— reduce el coste de servicio durante toda la vida del vehículo. Un semirremolque bien diseñado también hace ganar tiempo en la estación de inspección.</p>
            ',
        ],

        'smart-tachograph-trailer-data' => [
            'title'   => 'Los datos del semirremolque en la era del tacógrafo inteligente: ¿qué queda registrado?',
            'excerpt' => 'El tacógrafo inteligente procesa automáticamente, más allá del tiempo de conducción, datos de posición y operación; combinado con la telemática del semirremolque, produce el registro digital completo del viaje.',
            'content' => '
                <p>Durante muchos años el tacógrafo fue un dispositivo que solo registraba los tiempos de conducción y descanso. Con la generación del tacógrafo inteligente el panorama ha cambiado: el dispositivo procesa ahora automáticamente los puntos de posición, los pasos de frontera y las operaciones de carga y descarga, y los organismos de control pueden acceder a estos datos de forma remota. Aunque el dispositivo reside en la tractora, la mitad de la operación registrada ocurre en el semirremolque.</p>
                <h2>¿Qué queda registrado?</h2>
                <ul>
                    <li>El desglose de los tiempos de conducción, pausa y descanso emparejado con la tarjeta del conductor</li>
                    <li>Los puntos de posición de inicio, fin e intermedios del viaje, y los pasos de frontera</li>
                    <li>La información de hora y posición de las operaciones de carga y descarga</li>
                    <li>El cruce de los registros relativos a la combinación del vehículo con los sistemas de telemática</li>
                </ul>
                <h2>Al combinarse con la telemática del semirremolque</h2>
                <p>El dato del tacógrafo por sí solo narra al conductor; la telemática del semirremolque añade la historia del vehículo: aperturas de puerta, registros de temperatura, cargas por eje y datos del sistema de frenos. Cuando se combinan ambas fuentes surge el gemelo digital completo del viaje, lo que significa un expediente de defensa en una inspección, una herramienta de prueba frente al cliente y materia prima para el análisis de operaciones. La tarea del gestor de flota es establecer de forma consciente los permisos de acceso y el régimen de conservación de este flujo de datos. <strong>La flota que recopila los datos no para la inspección, sino para su propia eficiencia, sale ganando de esta transformación.</strong></p>
            ',
        ],

        'quiet-trailer-noise-rules' => [
            'title'   => 'Entregas nocturnas y normas de ruido: diseño de semirremolque silencioso',
            'excerpt' => 'Las ciudades fomentan la entrega nocturna; pero los vehículos cuyo ruido de puerta, rampa y unidad de refrigeración supera los límites quedan fuera de esta operación.',
            'content' => '
                <p>Las ciudades europeas fomentan trasladar las entregas a tiendas y almacenes a las horas nocturnas para aliviar el tráfico diurno. Pero la operación nocturna tiene un precio: los límites de ruido vigentes en las zonas residenciales. El chirrido de puerta, el contacto de rampa y el zumbido de la unidad de refrigeración de un semirremolque estándar superan fácilmente esos límites; el diseño de semirremolque silencioso es la llave de la ventana nocturna.</p>
                <h2>¿De dónde viene el ruido?</h2>
                <p>En la entrega nocturna, los sonidos más molestos no proceden de la marcha, sino del momento de la parada: el choque metal contra metal de las puertas traseras, los sonidos hidráulicos y de contacto de la plataforma elevadora, el eco de las ruedas de la transpaleta en el suelo y el funcionamiento continuo de la unidad de refrigeración. Cada una de estas fuentes puede silenciarse con el diseño.</p>
                <h2>Las herramientas del diseño silencioso</h2>
                <ul>
                    <li>Herrajes de puerta con junta amortiguadora, de cierre lento, y superficies de contacto de caucho</li>
                    <li>Suelo con revestimiento absorbente de eco y textura de superficie antideslizante pero silenciosa</li>
                    <li>Unidades de refrigeración con modo de bajo ruido y opciones de refrigeración eléctrica</li>
                    <li>Suspensión neumática y conexiones de equipo con aislamiento de vibraciones</li>
                </ul>
                <p>En Europa, los programas de certificación de entrega silenciosa vinculan estas medidas a un estándar medible; los vehículos certificados ganan prioridad en los permisos de operación nocturna. La operación importa tanto como el equipamiento: la formación del conductor en trabajo silencioso conserva lo que aporta el diseño. La ventana nocturna significa, para las flotas preparadas, tanto un tiempo de entrega más corto como un uso más eficiente del vehículo.</p>
            ',
        ],

        'eu-type-approval-trailers' => [
            'title'   => 'El proceso de homologación de tipo de la UE: paso a paso para el fabricante de semirremolques',
            'excerpt' => 'Que un semirremolque pueda circular por las carreteras europeas exige que el diseño y la producción se verifiquen oficialmente; la homologación de tipo es el camino sistemático de esa garantía.',
            'content' => '
                <p>La homologación de tipo es la certificación por un organismo competente de que un tipo de vehículo cumple los requisitos de seguridad y medio ambiente. Este proceso, condición previa para vender semirremolques en el mercado europeo, no aprueba un único vehículo, sino el tipo de producto: cada vehículo fabricado conforme al tipo aprobado puede circular sin necesidad de pruebas separadas.</p>
                <h2>Los pasos principales del proceso</h2>
                <ul>
                    <li><strong>Expediente técnico:</strong> Recopilación sistemática de los datos de diseño, informes de cálculo y planos</li>
                    <li><strong>Pruebas del servicio técnico:</strong> Verificación por un organismo independiente de los frenos, el alumbrado, los elementos de enganche y los requisitos estructurales</li>
                    <li><strong>Revisión del organismo de homologación:</strong> Evaluación de los resultados de las pruebas y el expediente, y emisión del certificado de homologación de tipo</li>
                    <li><strong>Certificado de conformidad:</strong> Emisión, para cada vehículo fabricado, del documento que acredita su conformidad con el tipo</li>
                    <li><strong>Conformidad de la producción:</strong> Auditoría periódica de que la producción en serie no se desvía del tipo homologado</li>
                </ul>
                <h2>Notas prácticas para el fabricante</h2>
                <p>La parte del proceso más frecuentemente subestimada es la gestión de variantes: estructurar correctamente en la homologación las combinaciones de número de ejes, longitud y equipamiento evita repetir el proceso en cada nuevo pedido. En la producción multietapa —como una superestructura sobre un chasis— es crítico documentar los límites de responsabilidad. La homologación de tipo no es burocracia, sino la prueba externa de la disciplina de ingeniería; el fabricante con el expediente en orden acelera el proceso de aprobación y abre a su producto las puertas de los mercados internacionales.</p>
            ',
        ],

        'cbam-steel-trailer-costs' => [
            'title'   => '¿Cómo afecta el mecanismo de ajuste en frontera de carbono al precio del acero? Repercusiones en el coste del semirremolque',
            'excerpt' => 'El mecanismo de ajuste en frontera de carbono de la UE impone al acero importado un coste según sus emisiones de producción; el sector del semirremolque, cuya principal materia prima es el acero, debe seguir de cerca esta transformación.',
            'content' => '
                <p>La Unión Europea pone en marcha el mecanismo de ajuste en frontera de carbono para evitar que el coste de carbono que aplica a su propia industria se vea eludido mediante las importaciones. La esencia del mecanismo es simple: los productos intensivos en carbono que entran en la UE —empezando por el acero— soportan un coste adicional según la emisión generada durante su producción. Como la principal materia prima del semirremolque es el acero, esta regulación concierne directamente al cuadro de costes del sector.</p>
                <h2>El efecto en cadena</h2>
                <p>El coste del carbono repercute primero en el fabricante de acero, de ahí a los precios, de los precios al coste del semirremolque y, finalmente, a las tarifas de transporte. Mientras el acero procedente de rutas de producción de alta emisión se encarece, aumenta la competitividad del acero bajo en carbono y con contenido reciclado. El fabricante que reestructura su cadena de suministro en este eje puede convertir la fluctuación de costes en ventaja.</p>
                <h2>Hoja de ruta para el fabricante</h2>
                <ul>
                    <li>Empezar ya a exigir a los proveedores datos de emisión por producto</li>
                    <li>Cualificar alternativas de acero bajo en carbono y procedente de horno de arco eléctrico</li>
                    <li>Reducir el consumo de acero por vehículo mediante ingeniería de aligeramiento</li>
                    <li>Aumentar la eficiencia del material mediante la planificación de corte y la gestión de mermas</li>
                </ul>
                <p>El dato de carbono entra ya en el pliego técnico de la compra: en las licitaciones de mañana se preguntará tanto por la emisión incorporada del producto como por su precio. El fabricante que conoce su material, documenta su cadena de suministro y transporta más con menos, será el ganador de esta transformación, no su perdedor.</p>
            ',
        ],

        'trailer-telematics-guide' => [
            'title'   => 'Telemática del semirremolque: gestione con datos la mitad invisible de su flota',
            'excerpt' => 'Las tractoras llevan años generando datos; sin embargo, la otra mitad de la flota, el semirremolque, sigue siendo un punto ciego en la mayoría de las empresas. La telemática cierra esta brecha.',
            'content' => '
                <p>Una tractora moderna reporta todo, desde su posición hasta su consumo de combustible; el semirremolque, en cambio, sigue siendo silencioso en la mayoría de las flotas. Sin embargo, la carga está en el semirremolque, la temperatura se mide en el semirremolque, la puerta se abre en el semirremolque. La telemática del semirremolque convierte esta mitad invisible de la flota en un activo generador de datos.</p>
                <h2>¿Qué datos se recogen?</h2>
                <ul>
                    <li><strong>Posición y movimiento:</strong> Dónde está el vehículo, cuánto tiempo lleva esperando, en qué ruta trabaja</li>
                    <li><strong>Datos del sistema de frenos:</strong> Información de carga, velocidad y avería procedente del sistema de frenos electrónico</li>
                    <li><strong>Presión de neumáticos:</strong> Detección temprana del riesgo de reventón y desgaste mediante monitorización continua</li>
                    <li><strong>Puerta y temperatura:</strong> Registro ininterrumpido en la cadena de frío, eventos de puerta para la seguridad de la carga</li>
                    <li><strong>Estado de acoplamiento:</strong> Registro automático de qué tractora está emparejada con qué semirremolque</li>
                </ul>
                <h2>Del dato a la decisión</h2>
                <p>El valor de la telemática no está en el sensor, sino en que el dato se convierta en decisión. El análisis de los tiempos de espera, la detección de vehículos poco utilizados, la planificación del mantenimiento con datos de uso real en lugar de kilometraje, y la localización inmediata en caso de pérdida o robo son las áreas donde la inversión se amortiza a sí misma. En la implantación, la pregunta crítica no es el hardware, sino la integración: el dato debe fluir hacia el software de gestión de flota y hacia los informes al cliente. Empiece pequeño: pilote, mida y luego extienda en el segmento donde el valor es más visible, como la cadena de frío o la línea más intensa.</p>
            ',
        ],

        'reduce-empty-mileage' => [
            'title'   => 'Formas de reducir el kilometraje en vacío: bolsas de carga y optimización de rutas',
            'excerpt' => 'Cada kilómetro recorrido en vacío es un kilómetro que quema combustible pero no genera ingresos. Las bolsas de carga digitales y la planificación de red reducen sistemáticamente esta pérdida.',
            'content' => '
                <p>La ineficiencia más antigua del transporte por carretera es el retorno en vacío: cada kilómetro que lleva vacío al vehículo desde el punto de entrega hasta la base o hasta la siguiente carga consume combustible y tiempo pero no genera ingresos. Reducir la tasa de kilometraje en vacío es una de las formas más directas de aumentar la rentabilidad de la flota, y hoy existen más herramientas que nunca para lograrlo.</p>
                <h2>Bolsas de carga digitales</h2>
                <p>Las bolsas de carga son mercados que ponen en contacto al vehículo que busca carga de retorno con la carga que busca transportista. La clave del uso correcto es la planificación: la carga de retorno debe buscarse al planificar el viaje, no después de que el vehículo quede vacío. Las flotas que evalúan los puntos de carga alternativos a lo largo de la ruta y las ventanas de entrega flexibles obtienen ingresos regulares de las bolsas.</p>
                <h2>Los pasos de un enfoque sistemático</h2>
                <ul>
                    <li>Medir la tasa de kilometraje en vacío por línea: lo que no se mejora, no se puede gestionar</li>
                    <li>Establecer acuerdos de línea bidireccional con clientes regulares</li>
                    <li>Combinar viajes multiparada con software de optimización de rutas</li>
                    <li>Elegir el tipo de semirremolque apto para carga bidireccional: una carrocería multiuso trabaja en dos mercados</li>
                    <li>Realizar reparto de carga mediante colaboraciones regionales y redes de transportistas</li>
                </ul>
                <p>El kilometraje en vacío nunca puede reducirse a cero; pero en las flotas que miden, planifican y sitúan las herramientas digitales en el centro de la operación, puede reducirse de forma permanente. La ganancia no es solo financiera: la flota que reduce su kilometraje en vacío también reduce su emisión por transporte y ofrece un precio más competitivo a su cliente.</p>
            ',
        ],

        'trailer-pooling-model' => [
            'title'   => 'El modelo de pool de semirremolques: la economía colaborativa en la logística',
            'excerpt' => 'No es imprescindible que cada carga espere a su propio semirremolque; las flotas de semirremolques gestionadas desde un pool común elevan la tasa de utilización y liberan capital.',
            'content' => '
                <p>Un semirremolque medio pasa una parte importante de su vida esperando carga o estacionado. El modelo de pool de semirremolques apunta precisamente a esa capacidad ociosa: en lugar de ser el activo fijo de una única empresa, los vehículos se gestionan desde un pool al que acceden en común varios usuarios. El transportista usa los semirremolques que necesita durante el tiempo que los necesita.</p>
                <h2>¿Cómo funciona el modelo?</h2>
                <p>El operador del pool —puede ser una empresa de alquiler, una red logística o un gran cargador— mantiene disponibles semirremolques de características estándar en puntos determinados. La tractora llega, recoge el semirremolque cargado, lo deja en destino; allí el semirremolque se empareja con una nueva carga y una nueva tractora. El vehículo trabaja de forma continua, el capital rota de forma continua.</p>
                <h2>Ganancias y condiciones</h2>
                <ul>
                    <li>Aumenta la tasa de utilización: el mismo trabajo de transporte se realiza con menos vehículos</li>
                    <li>El capital queda libre: la inversión en flota se convierte en gasto operativo</li>
                    <li>La fluctuación de la demanda se equilibra en todo el pool, se gestiona la estacionalidad</li>
                    <li>Condición: características de vehículo estandarizadas; el pool trabaja con semirremolques que comparten un denominador común</li>
                    <li>Condición: seguimiento por telemática y protocolos claros de daños y responsabilidad</li>
                </ul>
                <p>Desde el punto de vista del fabricante, el modelo de pool pone en primer plano la durabilidad y la estandarización: un vehículo que pasará por las manos de distintos conductores y operaciones debe diseñarse con equipamiento robusto y facilidad de servicio. La economía colaborativa ha llegado para quedarse en la logística; un pool bien concebido es uno de los pocos modelos que benefician a todas las partes a la vez.</p>
            ',
        ],

        'fleet-replacement-age' => [
            'title'   => 'Decisión de renovación de flota: cálculo de la edad óptima de sustitución del semirremolque',
            'excerpt' => 'Vender los semirremolques demasiado pronto es un desperdicio de capital; venderlos demasiado tarde es un pantano de mantenimiento. La edad óptima de sustitución se esconde en el punto donde se cruzan dos curvas de coste.',
            'content' => '
                <p>¿Cuándo debe sustituirse un semirremolque? La flota que responde a esta pregunta con "cuando se estropee" ha aplazado la decisión hasta su momento más caro. La edad óptima de sustitución no es una sensación, sino un cálculo: encontrar el punto donde el coste anual de propiedad del vehículo es más bajo y no dejarse arrastrar más allá de ese punto.</p>
                <h2>La intersección de dos curvas</h2>
                <p>La depreciación del vehículo es alta en los primeros años y se ralentiza con el tiempo; el coste de mantenimiento y reparación, en cambio, aumenta de forma acelerada con la edad. La suma de estas dos curvas primero baja, alcanza un mínimo y luego vuelve a subir. La edad óptima de sustitución es precisamente la zona alrededor de ese mínimo, que se desplaza según el tipo de vehículo, el kilometraje anual y la severidad de la operación.</p>
                <h2>Partidas que deben entrar en el cálculo</h2>
                <ul>
                    <li>Evolución del gasto anual de mantenimiento y reparación según la edad, tomada de sus propios registros de servicio</li>
                    <li>El coste oculto de las paradas no planificadas: viaje perdido, vehículo sustituto, pérdida de cliente</li>
                    <li>La relación del valor de segunda mano con la edad y el estado; el retorno del momento de venta</li>
                    <li>La ganancia de combustible y eficiencia de la nueva tecnología del vehículo; la brecha que se abre con el vehículo antiguo</li>
                    <li>El calendario regulatorio: cambios en los requisitos de inspección, emisión y equipamiento</li>
                </ul>
                <p>En las operaciones pesadas y abrasivas la ventana se adelanta; en las líneas ligeras y regulares se amplía. Lo crítico es tomar la decisión por vehículo y con datos: la flota que mantiene registros de servicio ordenados no adivina el punto óptimo, lo ve.</p>
            ',
        ],

        'trailer-tco-calculation' => [
            'title'   => 'Coste total de propiedad: el cálculo real de la compra de un semirremolque',
            'excerpt' => 'El precio de compra es solo la puerta de entrada al coste real que le supondrá el semirremolque; el cálculo real es la suma de todas las facturas que el vehículo escribe a lo largo de toda su vida.',
            'content' => '
                <p>Elegir entre dos ofertas de semirremolque mirando solo la etiqueta de precio se parece a trazar una ruta viendo solo la punta del iceberg. El coste total de propiedad (TCO) suma en una sola ecuación todos los costes que genera el vehículo desde su compra hasta su venta, y a menudo revela que la oferta que parecía barata es en realidad la más cara.</p>
                <h2>Las partidas de la ecuación</h2>
                <ul>
                    <li><strong>Adquisición:</strong> Precio de compra, gasto de financiación y costes de puesta en servicio</li>
                    <li><strong>Efecto del combustible:</strong> El combustible que consumen el peso en vacío y la aerodinámica; una de las partidas más grandes durante toda la vida</li>
                    <li><strong>Mantenimiento y reparación:</strong> Mantenimiento programado, acceso a repuestos y mano de obra</li>
                    <li><strong>Coste de parada:</strong> El ingreso perdido de cada día que el vehículo no trabaja</li>
                    <li><strong>Valor de segunda mano:</strong> El capital que retorna en el momento de la venta</li>
                </ul>
                <h2>Los detalles clave del cálculo</h2>
                <p>Un semirremolque ligero transporta más carga en cada viaje o consume menos; esta diferencia, multiplicada por el kilometraje anual, suele cerrar con creces la diferencia del precio de compra. Una red de servicio amplia y un suministro rápido de piezas reducen la partida de parada; la protección anticorrosión y un chasis robusto conservan el valor de segunda mano como un cheque que se cobra años después. El enfoque correcto es comparar las ofertas no por el precio, sino con un modelo de TCO calculado con su propio perfil de operación: kilometraje anual, tipo de carga, estructura de la línea. <strong>No compra un semirremolque barato, compra un kilómetro de bajo coste.</strong></p>
            ',
        ],

        'tpms-fuel-savings' => [
            'title'   => 'Sistemas de monitorización de presión de neumáticos: sensor pequeño, gran ahorro',
            'excerpt' => 'Un neumático con baja presión quema combustible en silencio, se desgasta rápido y revienta en el peor momento; la monitorización continua ataca estos tres costes de una sola vez.',
            'content' => '
                <p>La única parte del semirremolque que toca la carretera es el neumático, y el parámetro más importante del neumático es la presión. La baja presión aumenta la resistencia a la rodadura y eleva en silencio el consumo de combustible, acorta la vida del neumático con desgaste en los hombros y aumenta el riesgo de reventón en carretera al elevar el calentamiento. El problema es que no se ve a simple vista: un neumático que parece visiblemente deshinchado ya lleva tiempo por debajo del umbral crítico.</p>
                <h2>¿Cómo funciona el sistema?</h2>
                <p>El sistema de monitorización de presión de neumáticos (TPMS) lee de forma continua el dato de presión y temperatura del sensor de cada rueda; cuando los valores caen por debajo del umbral, avisa al conductor y, a través de la telemática, al centro de la flota. En instalaciones avanzadas entran en acción sistemas de inflado automático que corrigen la presión durante la marcha, sin que el conductor tenga siquiera que percatarse.</p>
                <h2>Partidas de ganancia</h2>
                <ul>
                    <li>Menor resistencia a la rodadura y consumo de combustible con la presión correcta</li>
                    <li>Mayor vida del neumático y valor de la carcasa gracias a un desgaste uniforme</li>
                    <li>Reducción de los retrasos causados por averías en carretera y reventones</li>
                    <li>Gestión del suministro de neumáticos basada en datos gracias al historial de presión</li>
                </ul>
                <p>Si se considera la gran cantidad de neumáticos que giran en un semirremolque, el control manual uno por uno resulta en la práctica siempre incompleto. El TPMS automatiza esta tarea y su coste se amortiza a sí mismo incluso con una sola avería en carretera evitada. Rara vez, en la eficiencia de una flota, un equipo tan pequeño toca un área tan amplia.</p>
            ',
        ],

        'drop-and-hook-strategy' => [
            'title'   => 'Operaciones drop &amp; hook: la estrategia de semirremolque que acorta los tiempos de espera',
            'excerpt' => 'Que la tractora espere en la rampa la carga es la espera más cara de la flota; dejar los semirremolques cargados y recoger los que ya están listos elimina esa pérdida.',
            'content' => '
                <p>En la operación clásica la tractora se acerca a la rampa y espera hasta que termina la carga: la hora del conductor, el capital de la tractora y la ventana de entrega se consumen juntos. El enfoque drop &amp; hook elimina de raíz esta espera: el conductor deja el semirremolque cargado en el patio, recoge un semirremolque previamente cargado y se pone en marcha en minutos. La carga se realiza a su propio ritmo, independientemente de la tractora.</p>
                <h2>Los requisitos del modelo</h2>
                <ul>
                    <li><strong>Excedente de semirremolques:</strong> Más de un semirremolque por tractora; el pool trabaja tanto en la rampa como en la carretera</li>
                    <li><strong>Gestión del patio:</strong> Un orden y un seguimiento telemático que sepan qué semirremolque está dónde y en qué estado</li>
                    <li><strong>Equipamiento estándar:</strong> Que cada tractora pueda emparejarse con cada semirremolque; compatibilidad en los sistemas de enganche y frenos</li>
                    <li><strong>Equipo robusto:</strong> En un vehículo que se engancha y desengancha con frecuencia, el king-pin, las patas de apoyo y las conexiones trabajan intensamente</li>
                </ul>
                <h2>¿Dónde se acumula la ganancia?</h2>
                <p>La tractora y el conductor son los recursos más caros de la flota; drop &amp; hook dirige estos recursos hacia el kilómetro en lugar de hacia la espera. El horario del conductor se vuelve previsible, la densidad de la rampa se reparte a lo largo del día, la instalación se libra de la cola de vehículos en la puerta. A cambio se requiere una inversión adicional en semirremolques y una operación de patio disciplinada; el cálculo se vuelve rápidamente positivo en operaciones con alta densidad de línea y tiempos de carga largos. Para las flotas que trabajan en líneas regulares, drop &amp; hook es la forma probada de generar más viajes con el mismo número de vehículos.</p>
            ',
        ],

        'cold-chain-data-logging' => [
            'title'   => 'Registro de datos en la cadena de frío y carga de la prueba: evidencia de calidad para el cliente',
            'excerpt' => 'En la cadena de frío no hablan las palabras, habla el registro; el dato de temperatura ininterrumpido es tanto la prueba de calidad del transportista como su expediente de defensa en caso de reclamación.',
            'content' => '
                <p>En el transporte de cadena de frío no basta con decir que el producto no se ha estropeado; hay que demostrarlo. En los envíos de alimentos y medicamentos, el receptor exige el documento que acredite que la carga se mantuvo en la banda de temperatura correcta durante todo el viaje. Esta carga de la prueba recae sobre el transportista, y su única respuesta fiable es el registro de datos ininterrumpido.</p>
                <h2>Los componentes del sistema de registro</h2>
                <ul>
                    <li>Sensores de temperatura calibrados colocados en distintos puntos del compartimento de carga</li>
                    <li>Registro con sello de tiempo de las aperturas de puerta, la fuente más frecuente de desviaciones</li>
                    <li>Registro del modo de funcionamiento de la unidad de refrigeración y de los avisos de avería</li>
                    <li>Monitorización en vivo por telemática y alarma inmediata al superarse el umbral</li>
                    <li>Informe de temperatura generado automáticamente al final del viaje, listo para compartir</li>
                </ul>
                <h2>Del registro al valor</h2>
                <p>Una infraestructura de datos bien construida no es solo una herramienta de defensa. La alarma en vivo detecta la desviación antes de que el producto se estropee y permite intervenir en ruta: se cierra la puerta, se corrige el ajuste de la unidad, si es necesario se traslada la carga al almacén más cercano. La acumulación de los informes de viaje, por su parte, hace visibles los problemas sistemáticos: la desviación de puerta que siempre ocurre en la misma rampa, la unidad que se ve forzada en cierta línea. El efecto en el cliente es claro: el transportista que presenta el dato de calidad sin que se lo pidan pasa de la discusión de precio a la relación de confianza. En la cadena de frío, el dato es el embalaje invisible del producto; el que está completo marca la diferencia.</p>
            ',
        ],

        'intermodal-trailer-selection' => [
            'title'   => 'Selección de semirremolque en el transporte intermodal: compatibilidad con carretera, ferrocarril y ro-ro',
            'excerpt' => 'Un viaje que empieza por carretera puede seguir en tren y terminar en barco; que el semirremolque funcione sin problemas en los tres mundos depende de la elección correcta en el momento de la compra.',
            'content' => '
                <p>El transporte intermodal es el traslado de la carga entre carretera, ferrocarril y vía marítima dentro del mismo contenedor de transporte. Este modelo, que ofrece ventaja de coste y emisión en larga distancia, impone tareas adicionales al semirremolque: el vehículo no solo será remolcado, sino izado con grúa, asentado sobre un vagón, amarrado en la cubierta de un barco. Un semirremolque de carretera estándar no está diseñado para estas cargas.</p>
                <h2>Compatibilidad ferroviaria</h2>
                <p>El chasis de los semirremolques que se cargarán en vagón mediante grúa debe tener los bordes de izado reforzados, y la carrocería debe soportar sin daños las cargas de torsión durante el izado. La compatibilidad de las dimensiones y el equipamiento del vehículo con el gálibo ferroviario se documenta mediante un sistema de codificación; un semirremolque que no cuente con esta codificación no puede subirse al tren en la terminal intermodal.</p>
                <h2>El lado ro-ro y de carretera</h2>
                <p>En el trayecto marítimo el semirremolque se fija a la cubierta y se asegura en las condiciones del mar: son imprescindibles suficientes puntos de amarre de resistencia adecuada, patas de apoyo robustas y una infraestructura inferior resistente a la corrosión. En el lado de carretera, en cambio, el vehículo debe conservar todos los requisitos de la operación estándar; el equipamiento intermodal no debe restar capacidad de carga ni usabilidad.</p>
                <ul>
                    <li>Chasis reforzado para izado y codificación de compatibilidad ferroviaria documentada</li>
                    <li>Puntos de amarre marítimo y patas de apoyo reforzadas</li>
                    <li>Revestimiento y selección de materiales adecuados para el entorno salino</li>
                    <li>Compatibilidad verificada de antemano con el equipo de terminal de las líneas de destino</li>
                </ul>
                <p>El semirremolque intermodal bien elegido abre la puerta de los tres modos de transporte con un solo vehículo; una elección equivocada, en cambio, hace esperar al vehículo justo donde resulta más caro: en la terminal.</p>
            ',
        ],

        'winter-fleet-readiness' => [
            'title'   => 'Preparación de la flota para operaciones de invierno: lista de comprobación y planificación',
            'excerpt' => 'El invierno es implacable para encontrar los puntos débiles de la flota; desde la línea de frenos congelada hasta el neumático sin agarre, cada carencia debe cerrarse antes de la temporada.',
            'content' => '
                <p>Las condiciones invernales son un régimen operativo aparte para el semirremolque: la baja temperatura convierte en hielo la humedad del sistema de aire, la sal de carretera acelera la corrosión, la nieve y el hielo alargan la distancia de frenado. La preparación para el invierno no es un mantenimiento de un día, sino un programa planificado que se completa antes de la temporada, y la avería más barata es la que nunca ocurre.</p>
                <h2>Lista de comprobación previa a la temporada</h2>
                <ul>
                    <li><strong>Sistema de aire:</strong> Sustitución del cartucho secador de aire, purga del tanque y control de las conexiones de línea; la humedad que se congela es la principal fuente de avería en invierno</li>
                    <li><strong>Sistema de frenos:</strong> Estado de las pastillas y discos, ajuste de frenos y prueba de las funciones del freno electrónico</li>
                    <li><strong>Neumáticos:</strong> Profundidad de dibujo adecuada para condiciones invernales, control de daños y equipo de cadenas en las líneas necesarias</li>
                    <li><strong>Alumbrado y electricidad:</strong> Revisión de todas las luces, tomas y aislamiento de cables</li>
                    <li><strong>Protección anticorrosión:</strong> Lavado de los bajos del chasis y renovación del revestimiento protector, antes de que empiece la temporada de sal</li>
                    <li><strong>Juntas y mecanismos:</strong> Engrase adecuado para baja temperatura de las juntas de puerta, bisagras y patas de apoyo</li>
                </ul>
                <h2>El lado de la planificación</h2>
                <p>La operación debe adaptarse al invierno tanto como el equipamiento: los planes de viaje deben incluir un margen por condiciones meteorológicas, los conductores deben recibir formación sobre colocación de cadenas y rampas heladas, y deben definirse de antemano rutas alternativas para las líneas críticas. El ritmo se mantiene también a mitad de temporada: los lavados intermedios en zonas saladas y las purgas del tanque de aire se fijan como rutina. <strong>La flota que pasa el invierno sin contratiempos es el resultado no de la suerte, sino de una preparación calendarizada.</strong></p>
            ',
        ],
    ],
];
