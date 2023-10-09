<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenAI;

class ChatsController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('chats.index')
            ->with('chats', $user->chats);
    }

    /**
     * @param int|null $alertId
     */
    public function create(int $alertId = null)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($alertId) {
            // Search alert
            $alert = Alert::where('id', $alertId)
                ->where('user_id', $user->id)
                ->first();

            $chat = Chat::where('alert_id', $alert->id)
                ->first();

            if (!$chat) {
                $chat = Chat::create([
                    // TODO add text
                    'title' => 'Chat from alert #' . $alertId,
                    'user_id' => $user->id,
                    'alert_id' => $alert->id,
                ]);
            }
        } else {
            $chat = Chat::create([
                // TODO add text
                'title' => 'New chat #' . time(),
                'user_id' => $user->id,
            ]);
        }

        return redirect(route('chatShow', ['chatId' => $chat->id]));
    }

    public function show(int $chatId)
    {
        /** @var User $user */
        $user = Auth::user();

        $chat = Chat::where('id', $chatId)
            ->where('user_id', $user->id)
            ->with(['messages'])
            ->first();

        $messages = $this->getMessages($chat, ['updated_at', 'id']);
        return view('chats.show')
            ->with('chat', $chat)
            ->with('chats', $user->chats)
            ->with('messages', $messages);
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function messages(int $chatId): JsonResponse
    {
        $chat = Chat::where('id', $chatId)
            ->with(['messages'])
            ->first();

        return response()->json([
            'status' => true,
            'messages' => $this->getMessages($chat, ['updated_at', 'id'])
        ]);
    }

    /**
     * @param int $chatId
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(int $chatId, Request $request): JsonResponse
    {
        $validateSendMessage = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        if ($validateSendMessage->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateSendMessage->errors()
            ], 401);
        }

        $content = $request->get('content');

        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chatId;
        $chatMessage->role = 'user';
        $chatMessage->content = $content;
        $chatMessage->save();

        $chat = Chat::where('id', $chatId)
            ->with(['messages'])
            ->first();

        $messages = $this->getMessages($chat);

        // Add last message
        $messages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        $client = OpenAI::client(config('integrations.openai.apiKey'));
        try {
            $chatResponse = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => true,
                'errors' => ['The service is experiencing some problems']
            ]);
        }

        // Save new message
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chat->id;
        $chatMessage->open_ai_chat_id = $chatResponse->id;

        foreach ($chatResponse->choices as $choice) {
            $chatMessage->role = $choice->message->role;
            $chatMessage->content = $choice->message->content;
        }

        $chatMessage->save();

        return response()->json([
            'status' => true,
            'message' => [
                'role' => $chatMessage->role,
                'content' => $chatMessage->content
            ]
        ]);
    }

    /**
     * @param Chat $chat
     * @param array $additionalFields
     * @return array
     */
    protected function getMessages(Chat $chat, array $additionalFields = []): array
    {
        // Search previous message
        $chatMessages = $chat->messages->sortBy('created_at');
        $previousUpdatedAt = null;
        $messages = [];
        foreach ($chatMessages as $chatMessage) {
            // Ignore message after edit
            if (empty($previousUpdatedAt) && $chatMessage->updated_at >= $previousUpdatedAt) {
                $currentMessage = [
                    'role' => $chatMessage->role,
                    'content' => $chatMessage->content,
                ];

                foreach ($additionalFields as $additionalField) {
                    $currentMessage[$additionalField] = $chatMessage->{$additionalField};
                }

                $messages[] = $currentMessage;
            }
            $previousUpdatedAt = $chatMessage->updated_at;
        }

        return $messages;
    }
}
