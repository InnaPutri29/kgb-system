@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white/40 bg-white/20 focus:bg-white/50 focus:border-white/60 focus:ring-white/30 rounded-md shadow-sm transition-all text-gray-800 placeholder-gray-400']) }}>
