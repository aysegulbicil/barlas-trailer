<?php

/**
 * Legal language strings (en)
 *
 * Seeded by content agents; review wording (and have legal copy
 * approved by counsel) before release.
 */
return [
    'kvkk' => [
        'title' => 'KVKK Disclosure Notice',
        'intro' => 'This notice explains, in line with Turkey\'s Personal Data Protection Law No. 6698 (KVKK), how your personal data is processed when you visit the Barlas Trailer website and get in touch with us. We have kept the legal language to a minimum — our goal is simply to tell you what we do and why.',
        'sections' => [
            [
                'h' => 'Who Is the Data Controller?',
                'p' => [
                    'Barlas Trailer is the data controller for the personal data processed on this website. We are a manufacturer of trailers and tankers; this website exists to present our company and to communicate with you.',
                    'For any question or request about this notice, you can use the contact form on the site or the e-mail address on our contact page.',
                ],
            ],
            [
                'h' => 'What Personal Data Do We Process?',
                'p' => [
                    'When you fill in the contact form, your name, e-mail, phone number, company name, subject and message are recorded, together with your IP address and browser information for security purposes.',
                    'When you start a quote request, the category/product you are interested in and your IP address are recorded. Questions asked to the AI assistant are stored anonymously: only the question text and a truncated, irreversible digest (hash) of your IP address are kept — these records cannot identify you.',
                    'Like every website, our server also keeps standard access logs. There is no membership system on the site, no payments are taken, and no advertising or analytics tracking is used.',
                ],
            ],
            [
                'h' => 'Why Do We Process Your Data?',
                'p' => [
                    'We process your data only to: reply to your message, prepare the quote you requested, keep the site secure and prevent abuse, and improve the site and its content.',
                    'Your data is never added to any marketing list; we do not send you commercial electronic messages unless you ask for them.',
                ],
            ],
            [
                'h' => 'Legal Bases',
                'p' => [
                    'For your contact and quote requests we rely on Article 5/2-c of the KVKK: processing directly related to establishing or performing a contract (contacting you and preparing a quote at your request).',
                    'Data processed for site security, access logs and service improvement relies on our legitimate interest under Article 5/2-f of the KVKK. If any processing ever falls outside these grounds, we will ask for your explicit consent first.',
                ],
            ],
            [
                'h' => 'Transfers, Hosting and Retention',
                'p' => [
                    'Your data is stored in Turkey, on the server of our hosting provider. Your personal data is never sold or shared with third parties; messages from the contact form are forwarded by e-mail only to our sales team.',
                    'Our pages load fonts from Google Fonts and some script files from the jsDelivr CDN; while your browser downloads these files, those servers technically see your IP address. These services set no cookies through our site.',
                    'We keep your data only for as long as the purpose of processing requires, and then delete or anonymise it.',
                ],
            ],
            [
                'h' => 'Your Rights under Article 11 of the KVKK',
                'p' => [
                    'Under Article 11 of the KVKK you have the right to: learn whether your personal data is processed; request information about it if so; learn the purpose of processing and whether the data is used accordingly; know the third parties it is transferred to; request correction of incomplete or inaccurate data; request its erasure or destruction; request that these actions be notified to the third parties involved; object to a result produced against you exclusively by automated analysis; and claim compensation if you suffer damage.',
                    'You can submit your request through the contact form or the e-mail address on our contact page. We resolve requests free of charge, as soon as possible and within 30 days at the latest.',
                ],
            ],
        ],
    ],
    'privacy' => [
        'title' => 'Privacy Policy',
        'intro' => 'Privacy policies usually go unread; we wrote this one to be read. Below we explain, as plainly as we can, what data the Barlas Trailer website collects and why.',
        'sections' => [
            [
                'h' => 'In Short',
                'p' => [
                    'This is a company presentation website. No accounts, no payments, no ad tracking, no analytics tools. We only receive data from you when you send us something: a contact form message, a quote request, or a question to the AI assistant.',
                    'We do not sell, rent or share the data we collect with third parties.',
                ],
            ],
            [
                'h' => 'The Data We Collect',
                'p' => [
                    'Contact form: your name, e-mail, phone, company, subject and message. For security, your IP address and browser information are recorded with the form; the records are stored in our system and your message reaches our sales team by e-mail.',
                    'Quote interest: when you start a quote for a product, the category/product you are interested in and your IP address are recorded.',
                    'AI assistant: your questions are stored anonymously — only the question text and a truncated digest (hash) of your IP address. Who you are cannot be determined from these records. Our server also keeps standard access logs.',
                ],
            ],
            [
                'h' => 'How We Use Your Data',
                'p' => [
                    'Our goals are simple: to get back to you and to keep the site secure and working. We answer your message, prepare your quote, prevent abuse, and improve the site by understanding which products attract interest.',
                    'We do not send marketing e-mails without your permission — we do not even have a newsletter.',
                ],
            ],
            [
                'h' => 'Hosting and Third Parties',
                'p' => [
                    'Your data is kept on our hosting provider\'s server in Turkey. Only the relevant team sees your messages.',
                    'While pages load, fonts come from Google Fonts and some libraries from the jsDelivr CDN; these servers technically see your IP address while delivering the files to you. Beyond that, we transfer your data to no third party.',
                ],
            ],
            [
                'h' => 'Retention and Security',
                'p' => [
                    'We keep your data for as long as it is needed: once the correspondence is complete and no reason to retain it remains, we delete or anonymise it.',
                    'Our forms run with CSRF protection, access to the records is restricted, and the AI assistant\'s records are designed to be anonymous from the start.',
                ],
            ],
            [
                'h' => 'Your Rights and How to Reach Us',
                'p' => [
                    'All your rights under the Turkish Personal Data Protection Law (KVKK) — information, correction, erasure, objection and more — are reserved; the details are on our KVKK Disclosure Notice page.',
                    'For any question, use the contact form or the e-mail address on our contact page; we reply as soon as we can.',
                ],
            ],
        ],
    ],
    'cookies' => [
        'title' => 'Cookie Policy',
        'intro' => 'The short version: this site uses only two cookies, and both exist so the site can work. No tracking cookies, no advertising cookies, no third-party cookies. The details are below.',
        'sections' => [
            [
                'h' => 'What Is a Cookie?',
                'p' => [
                    'Cookies are small text files that websites place in your browser. They serve functions such as remembering your preferences or keeping things secure; they are not harmful in themselves.',
                ],
            ],
            [
                'h' => 'The Cookies We Use',
                'p' => [
                    'locale — the language preference cookie. It remembers which language you view the site in, so you do not have to choose again on every visit. It is stored for 1 year.',
                    'CSRF security cookie — it protects our forms against forged requests. It is a session cookie; it expires after about 2 hours or when you close your browser.',
                    'Both are functional cookies required for the site to work properly; they do not track you across websites and do not build a profile of you.',
                ],
            ],
            [
                'h' => 'Not a Cookie: Your Theme Preference',
                'p' => [
                    'Your light/dark theme choice is stored not in a cookie but in your browser\'s localStorage. That information never leaves your device and is not sent to our server.',
                ],
            ],
            [
                'h' => 'Third Parties',
                'p' => [
                    'There are no third-party cookies on our site — we use no advertising, analytics or social media cookies.',
                    'Fonts are loaded from Google Fonts and some JavaScript libraries from the jsDelivr CDN. While your browser downloads these files, those servers technically see your IP address; however, they set no cookies through our site.',
                ],
            ],
            [
                'h' => 'How to Manage Cookies',
                'p' => [
                    'You can view, delete or block cookies in your browser settings. If you delete the locale cookie, your language preference resets; if you block the CSRF cookie, forms may stop working for security reasons.',
                ],
            ],
            [
                'h' => 'Changes',
                'p' => [
                    'If our use of cookies changes — for example, if we ever add an analytics tool — we will update this page. For questions, you can reach us via our contact page.',
                ],
            ],
        ],
    ],
];
