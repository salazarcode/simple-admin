@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue',
    'trend' => null,
    'trendValue' => null
])

<div class="bg-white overflow-hidden shadow-lg rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($icon)
                    <div class="w-8 h-8 bg-{{ $color }}-100 rounded-md flex items-center justify-center">
                        {!! $icon !!}
                    </div>
                @endif
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $value }}
                        </div>
                        @if($trend && $trendValue)
                            <p class="ml-2 flex items-baseline text-sm font-semibold 
                                {{ $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                                @if($trend === 'up')
                                    <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @elseif($trend === 'down')
                                    <svg class="self-center flex-shrink-0 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="sr-only">
                                    {{ $trend === 'up' ? 'Increased' : 'Decreased' }} by
                                </span>
                                {{ $trendValue }}
                            </p>
                        @endif
                    </dd>
                </dl>
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>