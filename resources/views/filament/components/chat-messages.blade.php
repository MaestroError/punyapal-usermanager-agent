<div class="space-y-4">
    <!-- System Messages Panel -->
    <details class="mb-4">
        <summary class="text-sm font-medium text-gray-500 cursor-pointer hover:text-gray-700">
            System Messages
        </summary>
        <div class="mt-2 p-4 bg-gray-50 rounded-lg">
            @foreach($this->getSystemMessages() as $message)
                <div class="text-xs text-gray-500">
                    <strong class="uppercase">{{ $message['role'] }}:</strong>
                    <p class="mt-1">{{ $message['content'] }}</p>
                </div>
            @endforeach
        </div>
    </details>

    <!-- Chat Messages -->
    <div class="space-y-4">
        @foreach($this->getChatHistory() as $index => $message)
            <div class="flex {{ $message['role'] === 'assistant' ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-3/4 rounded-lg px-4 py-2 {!! $message['role'] === 'assistant' ? 'bg-gray-100 text-gray-800' : 'bg-blue-500 text-white' !!}">
                    <div class="text-sm">
                        @if($message['role'] === 'assistant')
                            <span class="font-medium">Assistant</span>
                        @else
                            <span class="font-medium">You</span>
                        @endif
                        @if(isset($message['timestamp']))
                            <span class="text-xs opacity-50 ml-2">
                                {{ \Carbon\Carbon::parse($message['timestamp'])->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-1">{{ $message['content'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
