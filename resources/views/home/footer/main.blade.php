<!-- TW Elements is free under AGPL, with commercial license required for specific uses. See more details: https://tw-elements.com/license/ and contact us for queries at tailwind@mdbootstrap.com -->
<!-- Footer container -->
<footer class="bg-neutral-100 text-center text-neutral-600 dark:bg-neutral-600 dark:text-neutral-200 lg:text-left">
    <div
        class="flex items-center justify-center border-b-2 border-neutral-200 p-6 dark:border-neutral-500 lg:justify-between">
        <div class="mr-12 hidden lg:block">
            <span>Get connected with us on social networks:</span>
        </div>
        <!-- Social network icons container -->        
        @include('home.footer.icons')
    </div>

    <!-- Main container div: holds the entire content of the footer, including four sections (TW elements, Products, Useful links, and Contact), with responsive styling and appropriate padding/margins. -->
    <div class="mx-6 py-10 text-center md:text-left">
        <div class="grid-1 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <!-- TW elements section -->
            @include('home.footer.elements')
        </div>
    </div>

    <!--Copyright section-->
    <div class="bg-neutral-200 p-6 text-center dark:bg-neutral-700">
        <span>© 2023 Copyright:</span>
        <a class="font-semibold text-neutral-600 dark:text-neutral-400" href="https://tw-elements.com/">TW elements</a>
    </div>

    @include('home.footer.buttonTop')

</footer>

