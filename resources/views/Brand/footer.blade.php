<?php
use Orchid\Attachment\Models\Attachment;
?>

<p class="small m-n">
    © Copyright {{date('Y')}}
    <a href="{{ config('app.url') }}" target="_blank">
        {{ config('app.name') }}
    </a>
</p>

@vite(['resources/js/platform.js'])