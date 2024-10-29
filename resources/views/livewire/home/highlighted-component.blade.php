<div>

    <x-card title="Mini App">
        
        <div class="grid grid-cols-12 gap-">

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl">
                <livewire:app.enrollment.main-component />
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl">
                <livewire:app.payment.index-component />
            </div>

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl">
                @include('home.highlighted.infoPayment')
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-3 border rounded-xl shadow-xl">
                @include('home.highlighted.point')
            </div>            
    
        </div>

    </x-card>    

</div>