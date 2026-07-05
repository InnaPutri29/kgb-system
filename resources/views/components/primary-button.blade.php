<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center py-2.5 px-6 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors']) }}>
    {{ $slot }}
</button>
