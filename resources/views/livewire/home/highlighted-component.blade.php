<div>

    <x-card title="Mini App">
        
        <div class="grid grid-cols-12 gap-">

            <!-- Column -->
            <div class="col-span-12 md:col-span-4 lg:col-span-4 xl:col-span-4 border rounded-xl shadow-xl">
                {{-- <livewire:app.catchment.index-component /> --}}
                <livewire:app.enrollment.main-component />
                {{-- livewire/app/enrollment/index-component.blade.php --}}
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-4 lg:col-span-4 xl:col-span-4 border rounded-xl shadow-xl">
                <livewire:app.payment.index-component />
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-4 lg:col-span-4 xl:col-span-4 border rounded-xl shadow-xl">
                @include('home.highlighted.point')
            </div>
    
        </div>

    </x-card>    

</div>