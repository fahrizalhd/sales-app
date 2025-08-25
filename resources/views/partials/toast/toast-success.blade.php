<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
     class="fixed top-5 right-5 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-sm z-50"
     role="alert">
    <div class="inline-flex items-center justify-center w-8 h-8 text-green-500 bg-green-100 rounded-lg">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
    </div>
    <div class="ml-3 text-md font-semibold">{{ $message }}</div>
    <button @click="show = false" type="button"
        class="ml-auto -mx-1.5 -my-1.5 text-gray-400 hover:text-gray-900 p-1.5 hover:bg-gray-100 rounded-lg focus:ring-2 focus:ring-gray-300 inline-flex items-center justify-center h-8 w-8"
        aria-label="Close">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13"/>
        </svg>
    </button>
</div>