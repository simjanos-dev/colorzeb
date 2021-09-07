@component('mail::layout')
    @slot ('header')
        @component('mail::header')
        @endcomponent
    @endslot
    <div class="mail-text-box">
        <h3>Kedves {{ $user->name }},</h3>
        Azért küldtük ezt az üzenetet, mert valaki jelszóvisszaállítást kért az email címedre.
        <br><br>
        <a class="black-link" href="{{ url('/password/reset/' . $token) }}">Jelszó visszaállításához kattints ide</a>
        <br><br>
        Amennyiben nem te kérted a jelszó visszaállítását, kérlek hagyd figyelmen kívül ezt az emailt.
    </div>
    @slot ('footer')
        @component('mail::footer')
        @endcomponent
    @endslot
@endcomponent