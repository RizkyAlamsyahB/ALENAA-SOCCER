<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<div style="display: flex; align-items: center; justify-content: center;">
    <span style="font-weight: bold; font-size: 24px; color: #000;">
        ALENA<span style="color: #2A2A2A;">SOCCER</span>
    </span>

</div>
</x-mail::header>
</x-slot:header>

{{-- Top Color Bar --}}
<div class="message-container"></div>

{{-- Body --}}
<div class="message-body">
    {{ $slot }}
</div>

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} Alena Soccer. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
