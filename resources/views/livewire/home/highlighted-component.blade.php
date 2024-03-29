<div>

    <x-card title="Aplicaciones">
        
        <div class="grid grid-cols-12 gap-4">

            <!-- Column -->
            <div class="col-span-12 md:col-span-12 xl:col-span-4 border rounded-xl shadow-xl">
                <livewire:app.enrollment.index-component />
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-6 xl:col-span-4 border rounded-xl shadow-xl">
                <livewire:app.payment.index-component />
            </div>
    
            <!-- Column -->
            <div class="col-span-12 md:col-span-6 xl:col-span-4 border rounded-xl shadow-xl">
                @include('home.highlighted.point')
            </div>
    
        </div>

    </x-card>    

</div>