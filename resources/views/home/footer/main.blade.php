<footer
    class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-emerald-500/30">
    <div
        class="flex items-center justify-center border-b border-gray-200 dark:border-emerald-500/30 p-6 lg:justify-between">
        <div class="mr-12 hidden lg:block text-emerald-800 dark:text-emerald-100 font-semibold">
            <span>Conéctate con nosotros en nuestras redes sociales:</span>
        </div>
        @include('home.footer.icons')
    </div>

    <div class="mx-6 py-10 text-center md:text-left text-xs text-gray-500 dark:text-gray-400">
        <div class="grid-1 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            @include('home.footer.elements')
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-900 p-6 text-center border-t border-gray-200 dark:border-emerald-500/10">
        <span class="text-gray-500">© 2024 Copyright:</span>
        <a class="font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors"
            href="https://github.com/noemdb">@noemdb</a>
    </div>

    @include('home.footer.buttonTop')

</footer>
