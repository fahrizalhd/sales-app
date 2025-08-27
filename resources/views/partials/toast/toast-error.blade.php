<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
     class="fixed top-5 right-5 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-sm z-50"
     role="alert">
    <div class="inline-flex items-center justify-center w-8 h-8 text-red-500 bg-red-100 rounded-lg">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-9-4a1 1 0 1 0 2 0 1 1 0 0 0-2 0ZM9 9a1 1 0 0 0 0 2v3a1 1 0 0 0 2 0v-3a1 1 0 0 0-2-2Z"/>
        </svg>
    </div>
    <div class="ml-3 text-sm font-medium">{{ $message }}</div>
    <button @click="show = false" type="button"
        class="ml-auto -mx-1.5 -my-1.5 text-gray-400 hover:text-gray-900 p-1.5 hover:bg-gray-100 rounded-lg focus:ring-2 focus:ring-gray-300 inline-flex items-center justify-center h-8 w-8"
        aria-label="Close">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13"/>
        </svg>
    </button>
</div>
