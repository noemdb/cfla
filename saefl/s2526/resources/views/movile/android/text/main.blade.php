<div class="continer-fluid small">

    <div class="pb-2" x-data="{ open: false }">
        <div class="text-secondary text-center" @click="open = ! open" role="button">
            Terminos y condiciones
        </div>

        <div x-show="open" @click.outside="open = false">
            <div class="card card-body small">
                @include('movile.android.text.terms')
            </div>
        </div>
    </div>

    <div class="pb-2" x-data="{ open: false }">
        <div class="text-secondary text-center" @click="open = ! open" role="button">
            P. Privacidad
        </div>

        <div x-show="open" @click.outside="open = false">
            <div class="card card-body small">
                @include('movile.android.text.privacity')
            </div>
        </div>
    </div>

    <div class="pb-2" x-data="{ open: false }">
        <div class="text-secondary text-center" @click="open = ! open" role="button">
            Contacto
        </div>

        <div x-show="open" @click.outside="open = false">
            <div class="card card-body small">
                @include('movile.android.text.contact')
            </div>
        </div>
    </div>

    <div class="pb-2" x-data="{ open: false }">
        <div class="text-secondary text-center" @click="open = ! open" role="button">
            Acerca de ...
        </div>

        <div x-show="open" @click.outside="open = false">
            <div class="card card-body small">
                @include('movile.android.text.about')
            </div>
        </div>
    </div>

</div>
