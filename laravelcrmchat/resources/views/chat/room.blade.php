<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат - {{ $room->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('chat') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800">{{ $room->name }}</h1>
                <span class="text-sm text-gray-500">
                    @if($room->type === 'private')
                        Диалог
                    @else
                        Группа ({{ $room->users->count() }} участников)
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Выйти</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Messages Area -->
    <main id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
        @foreach($messages as $message)
            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-lg {{ $message->user_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white text-gray-800' }} rounded-lg px-4 py-3 shadow-sm">
                    @if($message->user_id !== auth()->id())
                        <p class="text-xs font-medium text-blue-600 mb-1">{{ $message->user->name }}</p>
                    @endif
                    <p class="text-sm">{{ $message->message }}</p>
                    <p class="text-xs opacity-70 mt-1">{{ $message->created_at->format('H:i') }}</p>
                </div>
            </div>
        @endforeach
    </main>

    <!-- Message Input -->
    <footer class="bg-white border-t px-6 py-4">
        <form action="{{ route('chat.message.send', $room) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="message" id="message-input" 
                placeholder="Введите сообщение..." 
                required
                class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" 
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Отправить
            </button>
        </form>
    </footer>

    <script>
        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Echo setup for real-time messages
        @if(auth()->check())
        window.Echo = window.Echo || {};
        
        import('{{ config('app.url') }}/build/assets/echo-*.js').then(({ default: Echo }) => {
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ config("broadcasting.reverb.app_key") }}',
                wsHost: '{{ config("broadcasting.reverb.host") }}',
                wsPort: '{{ config("broadcasting.reverb.port") }}',
                wssPort: '{{ config("broadcasting.reverb.port") }}',
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            });

            window.Echo.private(`chat.{{ $room->id }}`)
                .listen('.message.sent', (e) => {
                    if (e.user_id !== {{ auth()->id() }}) {
                        addMessage(e);
                    }
                });
        });
        @endif

        function addMessage(data) {
            const container = document.getElementById('messages-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-start';
            messageDiv.innerHTML = `
                <div class="max-w-lg bg-white text-gray-800 rounded-lg px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium text-blue-600 mb-1">${data.user_name}</p>
                    <p class="text-sm">${data.message}</p>
                    <p class="text-xs opacity-70 mt-1">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                </div>
            `;
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
        }
    </script>
</body>
</html>
