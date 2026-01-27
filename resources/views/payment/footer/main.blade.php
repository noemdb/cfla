<footer class="text-center bg-gray-900 text-gray-300 lg:text-left border-t border-emerald-500/10 mt-8 pb-20 sm:pb-8">
    <div class="flex flex-col items-center justify-center border-b border-emerald-500/10 p-6 space-y-4">
        <div class="text-emerald-200 font-semibold text-sm">
            <span>Conéctate con nosotros:</span>
        </div>
        @include('payment.footer.icons')
    </div>

    <div class="mx-6 py-8 text-left text-xs">
        <div class="space-y-8">
            @include('payment.footer.elements')
        </div>
    </div>

    @include('payment.footer.buttonTop')

    <div class="bg-gray-900 p-6 text-center border-t border-emerald-500/10">
        <div class="text-xl font-bold text-emerald-400 mb-2">SAEFL</div>
        <span class="text-gray-400">© 2024 Copyright:</span>
        <a class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors"
            href="https://github.com/noemdb">@noemdb</a>
    </div>
</footer>
