<footer class="text-center bg-transparent text-gray-300 lg:text-left">
    {{-- Secciones secundarias ocultas en móvil para mayor armonía --}}
    <div class="hidden sm:flex flex-col items-center justify-center border-b border-emerald-500/10 p-6 space-y-4">
        <div class="text-emerald-200 font-semibold text-sm">
            <span>Conéctate con nosotros:</span>
        </div>
        @include('payment.footer.icons')
    </div>

    <div class="hidden sm:block mx-6 py-8 text-left text-xs">
        <div class="space-y-8">
            @include('payment.footer.elements')
        </div>
    </div>

    @include('payment.footer.buttonTop')

    <div class="py-4 px-6 text-center">
        <div class="text-lg font-bold text-emerald-400/80 mb-1">SAEFL</div>
        <div class="flex items-center justify-center gap-2 text-[10px] text-gray-500">
            <span>© 2024</span>
            <span class="text-emerald-500/30">•</span>
            <a class="hover:text-emerald-300 transition-colors" href="https://github.com/noemdb">@noemdb</a>
        </div>
    </div>
</footer>
