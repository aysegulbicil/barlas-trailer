<?php

/**
 * Blog language strings (en)
 *
 * UI strings + post texts. Post texts are keyed by slug; the slug, date,
 * category and cover image live in the Blog controller registry so URLs
 * stay identical across locales.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Industry news, maintenance tips, technology articles and expert guides from the trailer and tanker world.',

    // Page hero
    'page_title' => 'Blog',
    'page_lead'  => 'Industry news, maintenance tips and engineering-driven guides.',

    // Sidebar
    'search_title'       => 'Search',
    'search_placeholder' => 'Search blog posts...',
    'search_submit'      => 'Search',
    'categories_title'   => 'Categories',
    'all_posts'          => 'All Posts',

    // Cards and list states
    'read_more'        => 'Read More',
    'results_found'    => 'posts found',
    'no_results_title' => 'No results found',
    'no_results_text'  => 'No posts match your search criteria. Try a different keyword or browse all posts.',
    'clear_filters'    => 'Show all posts',

    // Detail page
    'related_title'   => 'Related Posts',
    'related_eyebrow' => 'Keep Reading',
    'back_to_blog'    => 'Back to All Posts',

    // Categories
    'cat_industry'    => 'Industry',
    'cat_maintenance' => 'Maintenance & Service',
    'cat_safety'      => 'Safety & ADR',
    'cat_technology'  => 'Technology',

    // Date formatting
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

    // Posts (slug => texts)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Safety Standards in ADR-Certified Tankers',
            'excerpt' => 'How ADR regulations shape tanker design for dangerous goods transport, and the key safety practices every operator should follow.',
            'content' => '
                <p>The transport of dangerous goods by road is strictly governed by the international ADR agreement. For tankers carrying fuel, LPG or chemicals, every stage — from design and manufacturing to testing and periodic inspection — is carried out within this regulatory framework.</p>
                <h2>How ADR Shapes Tanker Design</h2>
                <p>In an ADR-certified tanker, the shell material, wall thickness, number of compartments and baffle layout are all calculated according to the class of product being carried. Rollover resistance, static earthing, emergency shut-off valves and overfill prevention systems are integral parts of the design. After production, pressure and leak tests are verified by independent inspection bodies.</p>
                <h2>What Operators Need to Watch</h2>
                <p>Certification alone is not enough; safety is sustained through correct use and regular inspection. The main priorities for operators are:</p>
                <ul>
                    <li>Tracking intermediate and periodic inspection deadlines</li>
                    <li>Regular checks of valves, gaskets and safety equipment</li>
                    <li>Full compliance with loading and discharge procedures</li>
                    <li>Keeping drivers\' ADR training certificates up to date</li>
                </ul>
                <p>When a well-engineered tanker meets a well-trained operations team, dangerous goods transport can be carried out both safely and efficiently.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 Maintenance Tips That Extend Silo Trailer Life',
            'excerpt' => 'The essential maintenance steps that keep silo trailers carrying powder and granular cargo running efficiently for longer.',
            'content' => '
                <p>Silo trailers work under demanding conditions, carrying cement, flour, PVC and similar powder or granular products. Without regular maintenance, the pressurised discharge system and air lines become the first source of efficiency loss and breakdowns.</p>
                <h2>Why Planned Maintenance Matters</h2>
                <p>Planned maintenance does more than reduce the risk of failure: it shortens discharge times, lowers fuel consumption and protects resale value. The following steps prevent most of the problems encountered in the field:</p>
                <ul>
                    <li>Check air lines and bellows for leaks before every run</li>
                    <li>Inspect compressor oil level and filters regularly</li>
                    <li>Monitor discharge valve gaskets for wear</li>
                    <li>Test the locks and seals of top filling hatches</li>
                    <li>Clean the tank interior at every product change</li>
                    <li>Have the safety relief valve tested at least once a year</li>
                    <li>Re-torque chassis and axle connections periodically</li>
                </ul>
                <h2>Professional Service Support</h2>
                <p>While daily checks can be handled by the operator, pressure systems, relief valves and structural inspections should be carried out by specialist service teams. Using manufacturer-approved spare parts is the most reliable way to preserve the system\'s original performance.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Choosing the Right Lowbed for Heavy Transport',
            'excerpt' => 'The criteria to consider when specifying the right lowbed configuration for construction machinery and heavy equipment transport.',
            'content' => '
                <p>Moving construction machinery, generators, transformers and industrial equipment calls for solutions beyond the limits of standard trailers. With their low loading height and high payload capacity, lowbed trailers are the critical tool in this segment.</p>
                <h2>Defining the Right Configuration</h2>
                <p>The first question in lowbed selection is always the same: what will be carried? The cargo\'s weight, dimensions and centre of gravity determine every technical decision — from the number of axles to platform length, ramp type and the need for widening. Alongside fixed two-, three- and four-axle models, modular and extendable platform solutions are also available.</p>
                <h2>Key Selection Criteria</h2>
                <ul>
                    <li>Maximum payload and weight per axle</li>
                    <li>Loading height and well length</li>
                    <li>Hydraulic or spring-assisted ramp options</li>
                    <li>Need for self-steering axles</li>
                    <li>Bridge and tunnel restrictions along the route</li>
                </ul>
                <p>A correctly configured lowbed does more than carry the load: it improves operational safety, simplifies transport permit processes and reduces operating costs in the long run. Working with the manufacturer\'s engineering team during selection delivers the best result.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Lightweight Chassis Technology: Less Weight, More Payload',
            'excerpt' => 'The operational advantages of reducing tare weight with high-strength steels and optimised chassis design.',
            'content' => '
                <p>With legal gross weight limits fixed in road transport, the gain comes from the trailer\'s tare weight. Every kilogram saved on the chassis converts directly into usable payload — and higher revenue per trip.</p>
                <h2>The Role of High-Strength Steels</h2>
                <p>In modern trailer manufacturing, high-strength steels are increasingly replacing standard structural grades. These materials deliver equal or better strength with thinner sections. Combined with correct welding procedures and precise production techniques, they enable a significant reduction in chassis weight while preserving fatigue life.</p>
                <h2>Design Optimisation</h2>
                <p>Material alone is not enough. Finite element analysis (FEA) models the stress distribution across the chassis, so material is used only where it is needed. Main beam geometry, cross-member placement and connection details are all optimised based on these analyses.</p>
                <p>The result is lower fuel consumption, more payload and reduced carbon emissions. Weight reduction is one of the most important topics in today\'s trailer engineering — both economically and environmentally.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Refrigerated Trailer Technology in Cold Chain Logistics',
            'excerpt' => 'Modern refrigerated trailer solutions that keep the cold chain unbroken in food and pharmaceutical transport.',
            'content' => '
                <p>When transporting temperature-sensitive products such as food and pharmaceuticals, the smallest deviation can lead to product loss and serious health risks. Refrigerated trailers — the cornerstone of cold chain logistics — keep this sensitive operation running without interruption.</p>
                <h2>Insulation Quality Comes First</h2>
                <p>The performance of a refrigerated body is determined by insulation quality before the cooling unit. High-density polyurethane-filled panel systems, combined with joint details that minimise thermal bridging, allow the cooling unit to work less and cut fuel consumption. Door seals and floor construction also play a decisive role in total heat leakage.</p>
                <h2>Monitoring and Data Logging</h2>
                <p>In modern cold chain operations, temperature tracking is no longer limited to the cab display. Real-time telemetry systems make it possible to remotely monitor body temperature, door openings and refrigeration performance. This data is becoming ever more important both for regulatory compliance and as quality evidence for customers.</p>
                <p>When proper insulation, a reliable cooling unit and continuous monitoring come together, the cold chain stays protected even on the longest routes.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Preparing for Winter: A Trailer Maintenance Checklist',
            'excerpt' => 'A comprehensive checklist for preparing your trailer fleet against low temperatures, road salt and harsh winter conditions.',
            'content' => '
                <p>Winter months — with low temperatures, road salt and difficult surfaces — are the most punishing period of the year for trailers. A planned preparation before the season starts prevents mid-winter roadside breakdowns.</p>
                <h2>Brakes and Air Systems</h2>
                <p>The air dryer becomes critical in cold weather: if the cartridge is not replaced in time, moisture accumulating in the lines can freeze and lock the braking system. Brake pads, discs and automatic slack adjusters must be checked before the season.</p>
                <h2>Pre-Season Checklist</h2>
                <ul>
                    <li>Replace the air dryer cartridge and drain the tanks</li>
                    <li>Check brake pad thickness and disc surfaces</li>
                    <li>Measure tyre tread depth and pressures</li>
                    <li>Inspect lighting and electrical connections</li>
                    <li>Wash the chassis and apply protection against salt corrosion</li>
                    <li>Grease door hinges and locks</li>
                </ul>
                <p>Regular winter preparation is the lowest-cost way to increase fleet availability and extend vehicle service life. Our authorised service network is ready to support you with comprehensive seasonal inspections.</p>
            ',
        ],
    ],
];
