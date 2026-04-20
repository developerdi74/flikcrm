<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корпоративный чат</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800">Корпоративный чат</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Выйти</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Create Room Form -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Создать чат</h2>
                    
                    <form action="{{ route('chat.room.create') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Название</label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Тип</label>
                            <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="private">Диалог</option>
                                <option value="group">Групповой чат</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="user_ids" class="block text-sm font-medium text-gray-700 mb-2">Участники</label>
                            <select id="user_ids" name="user_ids[]" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 h-32">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Зажмите Ctrl/Cmd для выбора нескольких</p>
                        </div>
                        
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                            Создать
                        </button>
                    </form>
                </div>
            </div>

            <!-- Rooms List -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Ваши чаты</h2>
                    
                    @if($rooms->count() > 0)
                        <div class="space-y-3">
                            @foreach($rooms as $room)
                                <a href="{{ route('chat.room', $room) }}" 
                                    class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $room->name }}</h3>
                                            <p class="text-sm text-gray-500">
                                                @if($room->type === 'private')
                                                    Диалог
                                                @else
                                                    Группа • {{ $room->users->count() }} участников
                                                @endif
                                            </p>
                                            @if($room->lastMessage)
                                                <p class="text-sm text-gray-600 mt-1 truncate">
                                                    {{ $room->lastMessage->message }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $unreadCount = $room->unreadCount(auth()->user());
                                            @endphp
                                            @if($unreadCount > 0)
                                                <span class="inline-block bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $room->updated_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">У вас пока нет чатов. Создайте первый!</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
