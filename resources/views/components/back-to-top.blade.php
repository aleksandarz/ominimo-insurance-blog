<button
    type="button"
    x-data="{ show: false }"
    @scroll.window="show = window.scrollY > 300"
    x-show="show"
    x-transition
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    aria-label="{{ __('Back to top') }}"
    style="display: none"
    class="fixed bottom-6 right-6 z-40 inline-flex h-11 w-11 items-center justify-center rounded-full bg-gray-800 text-white shadow-lg hover:bg-gray-700 focus:outline-none"
>
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
</button>
