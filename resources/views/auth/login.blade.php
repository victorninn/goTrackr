<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – {{ config('brand.name') }}</title>
    <link rel="icon" type="image/png" href="{{ Storage::url('logos/goTrackr.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-blue-900">

    {{-- Background glow --}}
    <div class="fixed inset-0 pointer-events-none"
         style="background: radial-gradient(circle at 15% 60%, rgba(1,126,131,0.3) 0%, transparent 55%),
                            radial-gradient(circle at 85% 20%, rgba(1,91,150,0.25) 0%, transparent 50%);"></div>

    <div class="relative z-10 w-full max-w-4xl">

        {{-- Beta pill --}}
        <div class="flex justify-center mb-5">
            <span class="inline-flex items-center gap-1.5 bg-white bg-opacity-10 border border-white border-opacity-20 text-amber-600 text-xs font-regular px-3 py-1 rounded-full tracking-wide">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse inline-block"></span>
                Beta — Free during early access
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 rounded-2xl shadow-2xl overflow-hidden">

            {{-- ============================================================ --}}
            {{-- LEFT PANEL — Demo Request                                     --}}
            {{-- ============================================================ --}}
            <div class="bg-blue-800 bg-opacity-50 p-8 flex flex-col">

                {{-- Brand --}}
                <div class="flex items-center gap-3 mb-7">
                    <img src="{{ Storage::url('logos/goTrackr.png') }}"
                         alt="{{ config('brand.name') }}"
                         class="w-8 h-8 rounded-full object-cover border border-white border-opacity-20"
                         onerror="this.style.display='none'">
                    <div>
                        <p class="text-white font-bold text-base leading-none">{{ config('brand.name') }}</p>
                        <p class="text-blue-300 text-xs mt-0.5">{{ config('brand.tagline') }}</p>
                    </div>
                </div>

                <h2 class="text-white text-xl font-bold mb-1">Request a Free Demo</h2>
                <p class="text-blue-300 text-sm mb-6">See how {{ config('brand.name') }} simplifies time tracking for your team — no commitment needed.</p>

                {{-- Alerts --}}
                @if(session('demo_success'))
                <div class="mb-5 flex items-center gap-2 bg-green-100 border border-green-400 border-opacity-40 text-green-700 px-4 py-3 rounded-lg text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('demo_success') }}
                </div>
                @endif

                @if($errors->has('demo_name') || $errors->has('demo_business') || $errors->has('demo_phone') || $errors->has('demo_email') || $errors->has('demo_terms'))
                <div class="mb-5 bg-red-500 bg-opacity-20 border border-red-400 border-opacity-40 text-red-300 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first('demo_name') ?: ($errors->first('demo_business') ?: ($errors->first('demo_phone') ?: ($errors->first('demo_email') ?: $errors->first('demo_terms')))) }}
                </div>
                @endif

                <form method="POST" action="{{ route('demo.request') }}" class="space-y-4 flex-1">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-blue-200 mb-1.5">Full Name <span class="text-blue-400">*</span></label>
                        <input type="text" name="demo_name" value="{{ old('demo_name') }}" required placeholder="Juan dela Cruz"
                            class="w-full bg-blue-900 bg-opacity-40 border border-blue-600 border-opacity-60 text-white placeholder-blue-500 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-blue-200 mb-1.5">Business / Company <span class="text-blue-400">*</span></label>
                        <input type="text" name="demo_business" value="{{ old('demo_business') }}" required placeholder="Acme Corporation"
                            class="w-full bg-blue-900 bg-opacity-40 border border-blue-600 border-opacity-60 text-white placeholder-blue-500 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-blue-200 mb-1.5">Contact Number <span class="text-blue-400">*</span></label>
                        <input type="tel" name="demo_phone" value="{{ old('demo_phone') }}" required placeholder="+63 917 000 0000"
                            class="w-full bg-blue-900 bg-opacity-40 border border-blue-600 border-opacity-60 text-white placeholder-blue-500 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-blue-200 mb-1.5">Email Address <span class="text-blue-400">*</span></label>
                        <input type="email" name="demo_email" value="{{ old('demo_email') }}" required placeholder="you@company.com"
                            class="w-full bg-blue-900 bg-opacity-40 border border-blue-600 border-opacity-60 text-white placeholder-blue-500 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>

                    <div class="flex items-start gap-2.5 pt-1">
                        <input type="checkbox" name="demo_terms" id="demo_terms" required
                            class="mt-0.5 w-4 h-4 rounded border-blue-500 text-blue-600 focus:ring-blue-500 flex-shrink-0">
                        <label for="demo_terms" class="text-xs text-blue-300 leading-relaxed">
                            I agree to the
                            <button type="button" onclick="document.getElementById('terms-modal').classList.remove('hidden')"
                                class="text-blue-300 underline hover:text-white transition-colors">Terms &amp; Conditions</button>
                            and understand that goTrackr is in <span class="text-white font-semibold">beta</span> — features may change without notice.
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                        Request Demo
                    </button>
                </form>

                <p class="text-white text-xs mt-6 text-center">&copy; {{ date('Y') }} {{ config('brand.name') }}. All rights reserved.</p>
            </div>

            {{-- ============================================================ --}}
            {{-- RIGHT PANEL — Login                                           --}}
            {{-- ============================================================ --}}
            <div class="bg-white p-8 flex flex-col justify-center">

                <div class="mb-7">
                    <h2 class="text-gray-800 text-xl font-bold mb-1">Welcome back</h2>
                    <p class="text-gray-400 text-sm">Sign in to your {{ config('brand.name') }} account</p>
                </div>

                @if($errors->has('email'))
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first('email') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <label for="remember" class="text-sm text-gray-500">Remember me</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                        Sign In
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">No account yet?
                        <button type="button" onclick="document.getElementById('terms-modal').classList.remove('hidden')"
                            class="text-blue-600 hover:underline">Learn about our beta program</button>
                    </p>
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-xs text-amber-700">
                        🚧 goTrackr is in <strong>beta</strong>. Some features are still being refined. Thank you for your patience!
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TERMS & CONDITIONS MODAL                                      --}}
    {{-- ============================================================ --}}
    <div id="terms-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-60">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800">Terms &amp; Conditions</h3>
                <button onclick="document.getElementById('terms-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 overflow-y-auto text-sm text-gray-600 space-y-4 leading-relaxed">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Last updated: {{ date('F Y') }}</p>

                <p><strong class="text-gray-800">1. Beta Software</strong><br>
                goTrackr is currently in beta. Features, pricing, and availability may change at any time without prior notice. By using this software, you acknowledge that it is provided "as is" during the beta period.</p>

                <p><strong class="text-gray-800">2. Demo Requests</strong><br>
                By submitting a demo request, you consent to being contacted by the goTrackr team via the email address and phone number you provide. Your information will only be used to schedule and facilitate your product demonstration.</p>

                <p><strong class="text-gray-800">3. Data Privacy</strong><br>
                We collect only the information necessary to provide and improve our services. We do not sell or share your personal data with third parties. All data is handled in accordance with applicable privacy laws.</p>

                <p><strong class="text-gray-800">4. Acceptable Use</strong><br>
                You agree to use goTrackr only for lawful purposes. You may not use the platform to store false information, impersonate others, or engage in any activity that violates applicable law.</p>

                <p><strong class="text-gray-800">5. Limitation of Liability</strong><br>
                During the beta period, goTrackr shall not be liable for any loss of data, business interruption, or indirect damages arising from your use of the platform.</p>

                <p><strong class="text-gray-800">6. Changes to Terms</strong><br>
                We reserve the right to update these terms at any time. Continued use of the platform after changes are posted constitutes acceptance of the revised terms.</p>

                <p><strong class="text-gray-800">7. Contact</strong><br>
                For questions about these terms, please contact us at <a href="mailto:information@victorninn.com" class="text-blue-600 hover:underline">information@victorninn.com</a>.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                <button onclick="document.getElementById('terms-modal').classList.add('hidden')"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    I Understand
                </button>
            </div>
        </div>
    </div>

</body>
</html>
