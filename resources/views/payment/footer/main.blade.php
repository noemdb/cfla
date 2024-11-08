<footer class="text-center bg-green-900 text-green-100 dark:bg-gray-900 dark:text-green-200 lg:text-left">
    <div
        class="flex items-center justify-center border-b-2 border-green-200 p-6 dark:border-green-500 lg:justify-between">
        <div class="mr-12 hidden lg:block">
            <span>Conéctate con nosotros en nuestras redes sociales:</span>
        </div>
        @include('payment.footer.icons')
    </div>

    <div class="hidden md:block">

        <div class="mx-6 py-10 text-center md:text-left text-xs">
            <div class="grid-1 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @include('payment.footer.elements')
            </div>
        </div>
    
        @include('payment.footer.buttonTop')

    </div>

    <div class="bg-green-200 p-6 text-center dark:bg-green-700">
        <div class="text-xl text-green-950 font-bold">SAEFL</div>
        <span class="text-green-900">© 2024 Copyright:</span>
        <a class="font-semibold text-green-900 dark:text-green-400" href="https://github.com/noemdb">@noemdb</a>
    </div>
    

</footer>

