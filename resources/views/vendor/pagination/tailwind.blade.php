@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 cursor-not-allowed rounded-lg shadow-sm">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 shadow-sm transition">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 shadow-sm transition">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 cursor-not-allowed rounded-lg shadow-sm">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-4 sm:items-center sm:justify-between">
            <div class="flex items-center text-sm text-gray-600">
                <p class="mb-0">
                    Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> data
                </p>
                
                <form method="GET" class="inline-flex items-center gap-2 border-l border-gray-300 pl-4 ml-4">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <span class="text-gray-500">Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 pl-3 pr-8 shadow-sm cursor-pointer hover:border-gray-300 transition text-gray-700 font-medium">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            @if ($paginator->hasPages())
                <div>
                    <span class="inline-flex gap-1.5 shadow-sm rounded-lg">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg shadow-sm" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </span>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 hover:text-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-500 bg-white border border-gray-200 cursor-default rounded-lg shadow-sm">{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-blue-400/90 border border-blue-400/90 cursor-default rounded-lg shadow-sm shadow-blue-500/20">{{ $page }}</span>
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 hover:text-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 hover:text-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg shadow-sm" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </span>
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </nav>
@endif
