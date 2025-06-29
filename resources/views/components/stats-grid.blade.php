@props(['title' => '', 'items' => []])

<div class="bg-white overflow-hidden shadow-lg rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ $title }}
        </h3>
        <div class="space-y-3">
            @foreach($items as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        @if(isset($item['avatar']))
                            <div class="flex-shrink-0 h-8 w-8 rounded-full {{ $item['avatar']['bg'] ?? 'bg-gray-300' }} flex items-center justify-center">
                                <span class="text-xs font-medium {{ $item['avatar']['text'] ?? 'text-gray-600' }}">
                                    {{ $item['avatar']['letter'] ?? '?' }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $item['name'] ?? '' }}</p>
                            @if(isset($item['subtitle']))
                                <p class="text-xs text-gray-500">{{ $item['subtitle'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-gray-900">{{ $item['value'] ?? '' }}</span>
                        @if(isset($item['badge']))
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item['badge']['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $item['badge']['text'] ?? '' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>