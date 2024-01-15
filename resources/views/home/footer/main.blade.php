<footer class="text-center bg-green-900 text-green-100 dark:bg-gray-900 dark:text-green-200 lg:text-left">
    <div
        class="flex items-center justify-center border-b-2 border-green-200 p-6 dark:border-green-500 lg:justify-between">
        <div class="mr-12 hidden lg:block">
            <span>Get connected with us on social networks:</span>
        </div>
        @include('home.footer.icons')
    </div>

    <div class="mx-6 py-10 text-center md:text-left">
        <div class="grid-1 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            @include('home.footer.elements')
        </div>
    </div>

    <div class="bg-green-200 p-6 text-center dark:bg-green-700">
        <span>© 2023 Copyright:</span>
        <a class="font-semibold text-green-100 dark:text-green-400" href="https://tw-elements.com/">TW elements</a>
    </div>

    @include('home.footer.buttonTop')

</footer>

