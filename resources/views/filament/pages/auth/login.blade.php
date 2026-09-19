<x-filament-panels::page.simple>
    <style>
        /* Dark and Fantastic Background Styles */
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated particles background */
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
            opacity: 0.3;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.3;
            }
            25% {
                transform: translate(100px, -100px) scale(1.2);
                opacity: 0.5;
            }
            50% {
                transform: translate(-50px, -200px) scale(0.8);
                opacity: 0.2;
            }
            75% {
                transform: translate(150px, -150px) scale(1.1);
                opacity: 0.4;
            }
        }

        /* Glowing orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: pulse 8s infinite ease-in-out;
            opacity: 0.4;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.4;
            }
            50% {
                transform: scale(1.3);
                opacity: 0.6;
            }
        }

        /* Login card styling */
        .fi-simple-page {
            position: relative;
            z-index: 10;
        }

        .fi-simple-main {
            background: rgba(17, 24, 39, 0.8) !important;
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37),
                        0 0 60px rgba(79, 70, 229, 0.2),
                        inset 0 0 60px rgba(79, 70, 229, 0.05);
            border-radius: 24px !important;
            transition: all 0.3s ease;
        }

        .fi-simple-main:hover {
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.5),
                        0 0 80px rgba(79, 70, 229, 0.3),
                        inset 0 0 60px rgba(79, 70, 229, 0.08;
            transform: translateY(-2px);
        }

        /* Input field styling */
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            background: rgba(30, 41, 59, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            background: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(129, 140, 248, 0.5) !important;
            box-shadow: 0 0 20px rgba(129, 140, 248, 0.3);
        }

        input::placeholder {
            color: rgba(226, 232, 240, 0.5) !important;
        }

        /* Button styling */
        .fi-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .fi-btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
        }

        /* Label styling */
        label {
            color: #e2e8f0 !important;
            font-weight: 500;
        }

        /* Logo container */
        .fi-simple-header {
            position: relative;
            z-index: 10;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        .fi-fo-field-wrp-label span {
            color: #cbd5e1 !important;
        }

        /* Dark mode text */
        .fi-simple-page h1,
        .fi-simple-page h2,
        .fi-simple-page p,
        .fi-simple-page span {
            color: #e2e8f0;
        }

        /* Link styling */
        a {
            color: #818cf8 !important;
            transition: all 0.2s ease;
        }

        a:hover {
            color: #a5b4fc !important;
            text-shadow: 0 0 10px rgba(129, 140, 248, 0.5);
        }

        /* Responsive animations */
        @media (max-width: 768px) {
            .glow-orb {
                display: none;
            }
        }

        /* Additional glow effect on card */
        .fi-simple-main::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            border-radius: 24px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: gradientRotate 6s ease infinite;
        }

        @keyframes gradientRotate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .fi-simple-main:hover::before {
            opacity: 0.3;
        }

        /* Error messages styling */
        .fi-fo-field-wrp-error-message {
            color: #fca5a5 !important;
        }
    </style>

    <!-- Particles Background -->
    <div class="particles-container">
        @for ($i = 0; $i < 30; $i++)
            <div class="particle" style="
                width: {{ rand(2, 6) }}px;
                height: {{ rand(2, 6) }}px;
                background: rgba({{ rand(100, 255) }}, {{ rand(100, 255) }}, {{ rand(200, 255) }}, 0.6);
                left: {{ rand(0, 100) }}%;
                top: {{ rand(0, 100) }}%;
                animation-duration: {{ rand(15, 25) }}s;
                animation-delay: {{ rand(0, 5) }}s;
            "></div>
        @endfor

        <!-- Glowing Orbs -->
        <div class="glow-orb" style="
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.4) 0%, transparent 70%);
            top: -200px;
            left: -200px;
        "></div>

        <div class="glow-orb" style="
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(118, 75, 162, 0.3) 0%, transparent 70%);
            bottom: -250px;
            right: -250px;
            animation-delay: 2s;
        "></div>

        <div class="glow-orb" style="
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.3) 0%, transparent 70%);
            top: 50%;
            right: -150px;
            animation-delay: 4s;
        "></div>
    </div>

    <!-- Login Content -->
    @if (filament()->hasLogin())
        <x-slot name="heading">
            {{ __('filament-panels::pages/auth/login.heading') }}
        </x-slot>
    @else
        <div class="fi-simple-main-ctn">
            <x-filament-panels::header.simple
                :heading="__('filament-panels::pages/auth/login.heading')"
            />
        </div>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
</x-filament-panels::page.simple>
