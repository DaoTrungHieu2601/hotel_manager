<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-violet-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md shadow-indigo-500/25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
