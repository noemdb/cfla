<div class="p-2">
    <x-card title="Your title here">

        <x-input label="Name" placeholder="your name" corner-hint="Ej: John">
            <x-slot name="append">
                <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                    <x-button class="h-full rounded-r-md" icon="search" primary flat squared />
                </div>
            </x-slot>
        </x-input>
        
        
        <x-slot name="footer">
            <div class="flex justify-between items-center">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit. Similique velit impedit molestias voluptas iusto earum eius, consequatur corrupti rem accusamus, explicabo repellendus eveniet blanditiis architecto numquam dignissimos! Sit, obcaecati quia?
            </div>
        </x-slot>
    
    </x-card>
</div>