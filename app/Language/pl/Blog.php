<?php

/**
 * Blog teksty językowe (pl)
 *
 * Teksty UI + treści wpisów. Wpisy indeksowane slugiem; slug, data,
 * kategoria i okładka są w rejestrze kontrolera Blog, dzięki czemu adresy
 * URL pozostają identyczne we wszystkich językach.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Wiadomości branżowe, porady serwisowe, artykuły technologiczne i eksperckie przewodniki ze świata naczep i cystern.',

    // Hero strony
    'page_title' => 'Blog',
    'page_lead'  => 'Wiadomości branżowe, porady serwisowe i przewodniki o inżynierskim rodowodzie.',

    // Panel boczny
    'search_title'       => 'Szukaj',
    'search_placeholder' => 'Szukaj wpisów...',
    'search_submit'      => 'Szukaj',
    'categories_title'   => 'Kategorie',
    'all_posts'          => 'Wszystkie wpisy',

    // Karty i stany listy
    'read_more'        => 'Czytaj dalej',
    'results_found'    => 'znalezionych wpisów',
    'no_results_title' => 'Brak wyników',
    'no_results_text'  => 'Żaden wpis nie pasuje do kryteriów wyszukiwania. Spróbuj innego słowa kluczowego albo przejrzyj wszystkie wpisy.',
    'clear_filters'    => 'Pokaż wszystkie wpisy',

    // Strona szczegółów
    'related_title'   => 'Powiązane wpisy',
    'related_eyebrow' => 'Czytaj dalej',
    'back_to_blog'    => 'Wróć do wszystkich wpisów',

    // Kategorie
    'cat_industry'    => 'Branża',
    'cat_maintenance' => 'Konserwacja i serwis',
    'cat_safety'      => 'Bezpieczeństwo i ADR',
    'cat_technology'  => 'Technologia',

    // Formatowanie daty
    'months' => ['stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'],

    // Wpisy (slug => teksty)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Normy bezpieczeństwa w cysternach z certyfikatem ADR',
            'excerpt' => 'Jak przepisy ADR kształtują konstrukcję cystern do przewozu towarów niebezpiecznych i jakie praktyki bezpieczeństwa powinien znać każdy operator.',
            'content' => '
                <p>Drogowy przewóz towarów niebezpiecznych jest ściśle regulowany międzynarodową umową ADR. W cysternach przewożących paliwo, LPG czy chemikalia każdy etap — od projektu i produkcji po badania i przeglądy okresowe — odbywa się w tych ramach prawnych.</p>
                <h2>Jak ADR kształtuje konstrukcję cysterny</h2>
                <p>W cysternie z certyfikatem ADR materiał zbiornika, grubość ścianek, liczba komór i układ falochronów są obliczane według klasy przewożonego produktu. Odporność na przewrócenie, uziemienie statyczne, awaryjne zawory odcinające i zabezpieczenia przed przepełnieniem to integralne elementy projektu. Po produkcji próby ciśnieniowe i szczelności weryfikują niezależne jednostki inspekcyjne.</p>
                <h2>Na co musi uważać operator</h2>
                <p>Sama certyfikacja nie wystarczy; bezpieczeństwo utrzymuje się dzięki właściwej eksploatacji i regularnym przeglądom. Główne priorytety operatorów to:</p>
                <ul>
                    <li>Pilnowanie terminów badań pośrednich i okresowych</li>
                    <li>Regularna kontrola zaworów, uszczelek i wyposażenia bezpieczeństwa</li>
                    <li>Pełne przestrzeganie procedur załadunku i rozładunku</li>
                    <li>Aktualne zaświadczenia ADR kierowców</li>
                </ul>
                <p>Gdy dobrze zaprojektowana cysterna spotyka dobrze wyszkolony zespół operacyjny, przewóz towarów niebezpiecznych może być zarazem bezpieczny i efektywny.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 porad serwisowych wydłużających życie naczepy silosu',
            'excerpt' => 'Kluczowe czynności konserwacyjne, dzięki którym silosy do ładunków sypkich i granulatów dłużej pracują wydajnie.',
            'content' => '
                <p>Naczepy silosy pracują w wymagających warunkach, przewożąc cement, mąkę, PVC i podobne produkty sypkie lub granulaty. Bez regularnej konserwacji układ rozładunku ciśnieniowego i przewody powietrzne stają się pierwszym źródłem spadku wydajności i awarii.</p>
                <h2>Dlaczego planowa konserwacja się opłaca</h2>
                <p>Planowa konserwacja nie tylko zmniejsza ryzyko awarii: skraca czas rozładunku, obniża zużycie paliwa i chroni wartość odsprzedaży. Poniższe kroki zapobiegają większości problemów w praktyce:</p>
                <ul>
                    <li>Przed każdym kursem sprawdzaj szczelność przewodów powietrznych i mieszków</li>
                    <li>Regularnie kontroluj poziom oleju i filtry sprężarki</li>
                    <li>Obserwuj zużycie uszczelek zaworów rozładunkowych</li>
                    <li>Testuj zamknięcia i uszczelnienia górnych włazów załadunkowych</li>
                    <li>Czyść wnętrze zbiornika przy każdej zmianie produktu</li>
                    <li>Przynajmniej raz w roku zlecaj badanie zaworu bezpieczeństwa</li>
                    <li>Okresowo dokręcaj połączenia ramy i osi</li>
                </ul>
                <h2>Profesjonalne wsparcie serwisowe</h2>
                <p>Codzienne kontrole może wykonywać operator, ale układy ciśnieniowe, zawory bezpieczeństwa i przeglądy konstrukcyjne należą do wyspecjalizowanych zespołów serwisowych. Stosowanie części zatwierdzonych przez producenta to najpewniejszy sposób zachowania oryginalnych osiągów układu.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Jak wybrać właściwą naczepę niskopodwoziową do transportu ciężkiego',
            'excerpt' => 'Kryteria doboru właściwej konfiguracji naczepy niskopodwoziowej do przewozu maszyn budowlanych i ciężkiego sprzętu.',
            'content' => '
                <p>Przewóz maszyn budowlanych, generatorów, transformatorów i urządzeń przemysłowych wymaga rozwiązań wykraczających poza możliwości standardowych naczep. Dzięki niskiej wysokości załadunku i dużej ładowności naczepy niskopodwoziowe są kluczowym narzędziem tego segmentu.</p>
                <h2>Określenie właściwej konfiguracji</h2>
                <p>Pierwsze pytanie przy wyborze jest zawsze to samo: co będzie przewożone? Masa, wymiary i środek ciężkości ładunku przesądzają o każdej decyzji technicznej — od liczby osi po długość platformy, typ najazdów i potrzebę poszerzeń. Obok stałych modeli dwu-, trzy- i czteroosiowych dostępne są też rozwiązania modułowe i z rozsuwaną platformą.</p>
                <h2>Kluczowe kryteria wyboru</h2>
                <ul>
                    <li>Maksymalna ładowność i nacisk na oś</li>
                    <li>Wysokość załadunku i długość stołu</li>
                    <li>Najazdy hydrauliczne lub wspomagane sprężynami</li>
                    <li>Potrzeba osi skrętnych</li>
                    <li>Ograniczenia mostów i tuneli na trasie</li>
                </ul>
                <p>Właściwie skonfigurowana naczepa niskopodwoziowa nie tylko wiezie ładunek: poprawia bezpieczeństwo operacji, upraszcza procedury zezwoleń i obniża koszty eksploatacji w dłuższej perspektywie. Najlepszy efekt daje współpraca z zespołem inżynierskim producenta na etapie doboru.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Technologia lekkich ram: mniej masy, więcej ładunku',
            'excerpt' => 'Operacyjne korzyści z obniżenia masy własnej dzięki stalom o wysokiej wytrzymałości i zoptymalizowanej konstrukcji ramy.',
            'content' => '
                <p>Przy ustawowo stałych limitach masy całkowitej w transporcie drogowym zysk tkwi w masie własnej naczepy. Każdy kilogram zaoszczędzony na ramie zamienia się bezpośrednio w użyteczną ładowność — i wyższy przychód z kursu.</p>
                <h2>Rola stali o wysokiej wytrzymałości</h2>
                <p>We współczesnej produkcji naczep stale o wysokiej wytrzymałości coraz częściej zastępują standardowe gatunki konstrukcyjne. Materiały te zapewniają równą lub lepszą wytrzymałość przy cieńszych przekrojach. W połączeniu z właściwymi procedurami spawania i precyzyjną produkcją pozwalają znacząco obniżyć masę ramy przy zachowaniu trwałości zmęczeniowej.</p>
                <h2>Optymalizacja konstrukcji</h2>
                <p>Sam materiał nie wystarczy. Analiza elementów skończonych (MES) modeluje rozkład naprężeń w ramie, dzięki czemu materiał trafia tylko tam, gdzie jest potrzebny. Geometria podłużnic, rozmieszczenie poprzeczek i detale połączeń są optymalizowane na podstawie tych analiz.</p>
                <p>Efekt to niższe zużycie paliwa, większa ładowność i mniejsza emisja CO₂. Redukcja masy jest dziś — ekonomicznie i środowiskowo — jednym z najważniejszych tematów inżynierii naczep.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Technologia naczep chłodni w logistyce łańcucha chłodniczego',
            'excerpt' => 'Nowoczesne rozwiązania chłodnicze, które utrzymują nieprzerwany łańcuch chłodniczy w transporcie żywności i farmaceutyków.',
            'content' => '
                <p>Przy przewozie produktów wrażliwych na temperaturę, takich jak żywność i farmaceutyki, najmniejsze odchylenie może oznaczać stratę towaru i poważne ryzyko zdrowotne. Naczepy chłodnie — filar logistyki łańcucha chłodniczego — utrzymują tę wrażliwą operację bez przerw.</p>
                <h2>Najpierw jakość izolacji</h2>
                <p>O osiągach zabudowy chłodniczej decyduje jakość izolacji jeszcze przed agregatem. Panele wypełnione poliuretanem o wysokiej gęstości, w połączeniu z detalami złączy minimalizującymi mostki termiczne, pozwalają agregatowi pracować mniej i obniżają zużycie paliwa. Uszczelki drzwi i konstrukcja podłogi również mają decydujący udział w całkowitych stratach ciepła.</p>
                <h2>Monitoring i rejestracja danych</h2>
                <p>W nowoczesnych operacjach chłodniczych śledzenie temperatury nie ogranicza się już do wyświetlacza w kabinie. Systemy telemetrii czasu rzeczywistego umożliwiają zdalny nadzór nad temperaturą zabudowy, otwarciami drzwi i pracą agregatu. Dane te zyskują na znaczeniu zarówno dla zgodności z przepisami, jak i jako dowód jakości dla klientów.</p>
                <p>Gdy dobra izolacja, niezawodny agregat i ciągły monitoring działają razem, łańcuch chłodniczy pozostaje chroniony nawet na najdłuższych trasach.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Przygotowanie do zimy: lista kontrolna serwisu naczepy',
            'excerpt' => 'Kompleksowa lista kontrolna przygotowania floty naczep na niskie temperatury, sól drogową i trudne warunki zimowe.',
            'content' => '
                <p>Miesiące zimowe — z niskimi temperaturami, solą drogową i trudną nawierzchnią — to najcięższy okres roku dla naczep. Zaplanowane przygotowanie przed sezonem zapobiega awariom na poboczu w środku zimy.</p>
                <h2>Hamulce i układy pneumatyczne</h2>
                <p>Osuszacz powietrza staje się na mrozie krytyczny: jeśli wkład nie zostanie wymieniony na czas, wilgoć gromadząca się w przewodach może zamarznąć i zablokować układ hamulcowy. Klocki, tarcze i automatyczne korektory luzu trzeba sprawdzić przed sezonem.</p>
                <h2>Lista kontrolna przed sezonem</h2>
                <ul>
                    <li>Wymień wkład osuszacza i odwodnij zbiorniki</li>
                    <li>Sprawdź grubość klocków i powierzchnie tarcz</li>
                    <li>Zmierz głębokość bieżnika i ciśnienie w oponach</li>
                    <li>Skontroluj oświetlenie i połączenia elektryczne</li>
                    <li>Umyj ramę i nałóż zabezpieczenie przed korozją od soli</li>
                    <li>Nasmaruj zawiasy drzwi i zamki</li>
                </ul>
                <p>Regularne przygotowanie do zimy to najtańszy sposób na wyższą dostępność floty i dłuższe życie pojazdów. Nasza autoryzowana sieć serwisowa jest gotowa wesprzeć Cię kompleksowymi przeglądami sezonowymi.</p>
            ',
        ],
    ],
];
