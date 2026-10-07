<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }} — Clinical Practice & Records</title>
        
        <!-- Google Fonts: Fraunces + Plus Jakarta Sans + JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            serif: ['Fraunces', 'Georgia', 'serif'],
                            sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                            mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                        },
                        colors: {
                            sanctuary: {
                                50: '#f0fdf9',
                                100: '#ccfbef',
                                200: '#9af5df',
                                300: '#5ee7cb',
                                400: '#2cd0b2',
                                500: '#13b499',
                                600: '#0b907c',
                                700: '#0c7365',
                                800: '#0e5c52',
                                900: '#104c44',
                                950: '#052b27',
                            },
                            spruce: {
                                700: '#1d323a',
                                800: '#15252c',
                                850: '#0f1c22',
                                900: '#0b161b',
                                950: '#070f13',
                            },
                        },
                    },
                },
            }
        </script>
        <style>
            [x-cloak] { display: none !important; }
            .sanctuary-bg {
                background-color: #080f14;
                background-image: 
                    radial-gradient(circle 800px at 5% 0%, rgba(19, 180, 153, 0.09), transparent 60%),
                    radial-gradient(circle 600px at 95% 20%, rgba(45, 212, 191, 0.05), transparent 50%),
                    radial-gradient(circle 700px at 80% 95%, rgba(217, 119, 6, 0.04), transparent 55%),
                    linear-gradient(to bottom, rgba(8, 15, 20, 0.8), rgba(8, 15, 20, 0.98));
                background-attachment: fixed;
            }
            .sanctuary-glass {
                background: rgba(15, 28, 34, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(45, 212, 191, 0.12);
            }
            .sanctuary-glass-card {
                background: linear-gradient(135deg, rgba(21, 37, 44, 0.75) 0%, rgba(11, 22, 27, 0.85) 100%);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }
        </style>
        @livewireStyles
    </head>
    <body class="sanctuary-bg font-sans text-slate-100 antialiased min-h-screen selection:bg-teal-500 selection:text-slate-950">
        {{ $slot }}
        @livewireScripts
    </body>
</html>
