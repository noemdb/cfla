<div>

    <x-card title="Mini App">
        
        <div class="grid grid-cols-12 gap-">

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                <livewire:app.enrollment.main-component />
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                <livewire:app.payment.index-component />
            </div>

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                @include('home.highlighted.point')
            </div>  

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl h-full">
                @include('home.highlighted.infoPayment')
            </div>   
    
        </div>

    </x-card>    

</div>