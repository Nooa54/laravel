@php($r = (int) round($rating ?? 0))
<span class="stars" aria-label="{{ $r }} / 5 tähteä">{{ str_repeat('★', $r) }}{{ str_repeat('☆', 5 - $r) }}</span>
