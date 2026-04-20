<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Обработка входа
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

class AdminController extends Controller
{
    /**
     * Список всех сотрудников (только для администраторов)
     */
    public function employees()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут управлять сотрудниками.');
        }

        $users = User::paginate(15);
        return view('admin.employees', compact('users'));
    }

    /**
     * Показать форму создания сотрудника
     */
    public function createEmployee()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут создавать сотрудников.');
        }

        return view('admin.create-employee');
    }

    /**
     * Создание нового сотрудника
     */
    public function storeEmployee(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут создавать сотрудников.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'full_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,vacation'],
            'work_schedule' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['boolean'],
        ]);

        User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'] ?? null,
            'position' => $validated['position'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'work_schedule' => $validated['work_schedule'] ?? null,
            'is_admin' => $validated['is_admin'] ?? false,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Сотрудник успешно создан.');
    }

    /**
     * Редактирование сотрудника
     */
    public function editEmployee(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут редактировать сотрудников.');
        }

        return view('admin.edit-employee', compact('user'));
    }

    /**
     * Обновление сотрудника
     */
    public function updateEmployee(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут обновлять сотрудников.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Password::min(8)],
            'full_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,vacation'],
            'work_schedule' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['boolean'],
        ]);

        $updateData = [
            'email' => $validated['email'],
            'full_name' => $validated['full_name'] ?? null,
            'position' => $validated['position'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'work_schedule' => $validated['work_schedule'] ?? null,
            'is_admin' => $validated['is_admin'] ?? false,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.employees')->with('success', 'Данные сотрудника обновлены.');
    }

    /**
     * Удаление сотрудника
     */
    public function destroyEmployee(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администраторы могут удалять сотрудников.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя удалить самого себя.');
        }

        $user->delete();

        return redirect()->route('admin.employees')->with('success', 'Сотрудник удален.');
    }
}

class ProfileController extends Controller
{
    /**
     * Показать профиль текущего пользователя
     */
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    /**
     * Показать форму редактирования профиля
     */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Обновление профиля
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,vacation'],
            'work_schedule' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Профиль обновлен.');
    }
}

class ChatController extends Controller
{
    /**
     * Список чатов пользователя
     */
    public function index()
    {
        $chats = Auth::user()->chats()
            ->with(['users', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->latest('updated_at')
            ->paginate(20);

        return view('chats.index', compact('chats'));
    }

    /**
     * Показать чат
     */
    public function show(Chat $chat)
    {
        if (!$chat->isParticipant(Auth::user())) {
            abort(403, 'Вы не участвуете в этом чате.');
        }

        $chat->load(['users', 'messages.user']);

        return view('chats.show', compact('chat'));
    }

    /**
     * Создать личный чат
     */
    public function createPersonal(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя создать чат с самим собой.');
        }

        // Проверка, существует ли уже чат между этими пользователями
        $existingChat = Chat::where('is_group', false)
            ->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereHas('users', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->first();

        if ($existingChat) {
            return redirect()->route('chats.show', $existingChat);
        }

        $chat = Chat::create([
            'is_group' => false,
            'created_by' => Auth::id(),
        ]);

        $chat->users()->attach([Auth::id(), $user->id]);

        return redirect()->route('chats.show', $chat);
    }

    /**
     * Показать форму создания группового чата
     */
    public function createGroup()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chats.create-group', compact('users'));
    }

    /**
     * Создание группового чата
     */
    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'users' => ['required', 'array', 'min:1'],
            'users.*' => ['exists:users,id'],
        ]);

        $chat = Chat::create([
            'name' => $validated['name'],
            'is_group' => true,
            'created_by' => Auth::id(),
        ]);

        $participantIds = array_merge([Auth::id()], $validated['users']);
        $chat->users()->attach($participantIds);

        return redirect()->route('chats.show', $chat)->with('success', 'Групповой чат создан.');
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        if (!$chat->isParticipant(Auth::user())) {
            abort(403, 'Вы не участвуете в этом чате.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $message->load('user');

        broadcast(new MessageSent($message))->toOthers();

        return back()->with('success', 'Сообщение отправлено.');
    }

    /**
     * Обновить название группового чата
     */
    public function updateName(Request $request, Chat $chat)
    {
        if (!$chat->is_group) {
            return back()->with('error', 'Нельзя изменить название личного чата.');
        }

        if (!$chat->isParticipant(Auth::user())) {
            abort(403, 'Вы не участвуете в этом чате.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $chat->update(['name' => $validated['name']]);

        return back()->with('success', 'Название чата обновлено.');
    }
}

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chatsCount = $user->chats()->count();
        $messagesCount = $user->messages()->count();

        return view('dashboard', compact('user', 'chatsCount', 'messagesCount'));
    }
}
