<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Metric;
use App\Models\User;
use App\Services\Notifications;
use Carbon\Carbon;
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

    public function create(Request $request)
    {
        $chatArray = $this->getChat($request->get('alert'), $request->get('content'));
        return redirect(route('chatShow', [
            'chatId' => $chatArray['id']
        ]));
    }

    /**
     * @param int $chatId
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(int $chatId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $chat = Chat::where('id', $chatId)
            ->user($user->id)
            ->first();

        if ($chat) {
            ChatMessage::where('chat_id', $chat->id)->delete();
            $chat->delete();
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    /**
     * @param int $chatId
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(int $chatId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validatorSendMessage = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validatorSendMessage->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validatorSendMessage->errors()
            ]);
        }

        $chat = Chat::where('id', $chatId)
            ->user($user->id)
            ->first();

        if ($chat) {
            $title = $request->get('title');
            $chat->title = $title;
            $chat->save();
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $chats = [];
        foreach ($user->chats as $chat) {
            $chats[] = [
                'title' => $chat->title,
                'id' => $chat->id,
            ];
        }

        return response()->json([
            'status' => true,
            'chats' => $chats
        ]);
    }

    public function show(int $chatId, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->ajax()) {
            $chat = Chat::where('id', $chatId)
                ->user($user->id)
                ->with(['messages', 'alert'])
                ->first();

            $messages = $this->getMessages($chat, ['updated_at', 'id']);
            unset($chat->messages);

            return response()->json([
                'status' => true,
                'chat' => $chat,
                'messages' => $messages,
                'showMoreDetails' => count($messages) < 3
            ]);
        } else {
            return view('chats.index')
                ->with('chats', $user->chats);
        }
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function messages(int $chatId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $chat = Chat::where('id', $chatId)
            ->user($user->id)
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
        /** @var User $user */
        $user = Auth::user();

        $validatorSendMessage = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        if ($validatorSendMessage->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validatorSendMessage->errors()
            ]);
        }

        $content = $request->get('content');

        // Logic for update
        $messageId = $request->get('messageId');
        if ($messageId) {
            $chatMessage = ChatMessage::where('id', $messageId)->first();
        } // Logic for create
        else {
            $chatMessage = new ChatMessage();
            $chatMessage->chat_id = $chatId;
            $chatMessage->role = 'user';
        }

        $chatMessage->content = $content;
        $chatMessage->save();
        $chatMessageSendId = $chatMessage->id;

        $chat = Chat::where('id', $chatId)
            ->user($user->id)
            ->with(['messages'])
            ->first();

        $messages = $this->getMessages($chat, [], 'ai');

        // Add last message
        $messages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        // Send all messages and receive answer
        try {
            $chatMessage = $this->sendToAi($messages, $chat);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => true,
                'errors' => ['The service is experiencing some problems']
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => [
                'send_id' => $chatMessageSendId,
                'role' => $chatMessage->role,
                'content' => $chatMessage->content,
                'receive_id' => $chatMessage->id
            ]
        ]);
    }

    protected function sendToAi($messages, $chat): ChatMessage
    {
        $client = OpenAI::client(config('integrations.openai.apiKey'));

        $chatResponse = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        // Save new message
        $chatMessage = new ChatMessage();
        $chatMessage->chat_id = $chat->id;
        $chatMessage->open_ai_chat_id = $chatResponse->id;

        foreach ($chatResponse->choices as $choice) {
            $chatMessage->role = $choice->message->role;
            $chatMessage->content = $choice->message->content;
        }

        $chatMessage->save();

        return $chatMessage;
    }

    /**
     * @param int $chatId
     * @param int $messageId
     * @param Request $request
     * @return JsonResponse
     */
    public function editMessage(int $chatId, int $messageId, Request $request): JsonResponse
    {
        $request->merge(['messageId' => $messageId]);
        return $this->sendMessage($chatId, $request);
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function moreDetails(int $chatId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $chat = Chat::where('id', $chatId)
            ->user($user->id)
            ->with(['messages'])
            ->first();

        $messages = $this->getMessages($chat, [], 'ai');

        // Send all messages and receive answer
        try {
            $chatMessage = $this->sendToAi($messages, $chat);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => true,
                'errors' => ['The service is experiencing some problems']
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => [
                'role' => $chatMessage->role,
                'content' => $chatMessage->content,
                'receive_id' => $chatMessage->id
            ]
        ]);
    }

    /**
     * @param Chat $chat
     * @param array $additionalFields
     * @param string $type
     * @return array
     */
    protected function getMessages(Chat $chat, array $additionalFields = [], string $type = 'front'): array
    {
        // Search previous message
        $chatMessages = $chat->messages->sortBy('id');
        $previousUpdatedAt = '1970-01-01';
        $messages = [];
        foreach ($chatMessages as $chatMessage) {
            if (($type === 'front' && $chatMessage->show) || ($type === 'ai' && $chatMessage->role !== 'inner-system')) {
                // Ignore message after edit
                if (Carbon::parse($chatMessage->updated_at)->timestamp >= Carbon::parse($previousUpdatedAt)->timestamp) {
                    $currentMessage = [
                        'role' => $chatMessage->role,
                        'content' => $chatMessage->content,
                    ];

                    foreach ($additionalFields as $additionalField) {
                        $currentMessage[$additionalField] = $chatMessage->{$additionalField};
                    }

                    $messages[] = $currentMessage;
                    $previousUpdatedAt = $chatMessage->updated_at;
                }
            }
        }

        return $messages;
    }

    /**
     * @param null $alertId
     * @param null $content
     * @return array
     */
    protected function getChat($alertId = null, $content = null): array
    {
        /** @var User $user */
        $user = Auth::user();

        if ($alertId) {
            // Search alert
            $alert = Alert::where('id', $alertId)
                ->where('user_id', $user->id)
                ->first();

            $chat = Chat::where('alert_id', $alert->id)
                ->user($user->id)
                ->first();

            if (!$chat) {
                $dateTime = Carbon::parse($alert->end_period)->toDateTimeString('minute');
                $chatTitle = 'Chat for alert #' . $alertId . ' (' . $dateTime . ')';
                if ($alert->period === Metric::PERIOD_DAY) {
                    $chatTitle = 'Chat for recommendation #' . $alertId . ' (' . $dateTime . ')';;
                }

                $chat = Chat::create([
                    'title' => $chatTitle,
                    'user_id' => $user->id,
                    'alert_id' => $alert->id,
                ]);

                $chatMessage = new ChatMessage();
                $chatMessage->chat_id = $chat->id;
                $chatMessage->role = 'inner-system';
                $chatMessage->content = $this->getFirstAutoGeneratedUserMessage($chat, true);
                $chatMessage->save();

                $chatMessage = new ChatMessage();
                $chatMessage->chat_id = $chat->id;
                $chatMessage->role = 'system';
                $chatMessage->content = $this->getSystemMessage(in_array($alert->result, [Alert::INCREASED, Alert::DECREASED]));
                $chatMessage->save();

                $chatMessage = new ChatMessage();
                $chatMessage->chat_id = $chat->id;
                $chatMessage->role = 'user';
                $chatMessage->show = false;
                $chatMessage->content = $this->getFirstAutoGeneratedUserMessage($chat);
                $chatMessage->save();
            }
        } else {
            $chat = Chat::create([
                'title' => mb_substr($content, 0, 100),
                'user_id' => $user->id,
            ]);

            $chatMessage = new ChatMessage();
            $chatMessage->chat_id = $chat->id;
            $chatMessage->role = 'user';
            $chatMessage->content = $content;
            $chatMessage->save();


            // Add last message
            $messages[] = [
                'role' => 'user',
                'content' => $content,
            ];

            try {
                $this->sendToAi($messages, $chat);
            } catch (\Exception $exception) {

            }
        }

        return [
            'id' => $chat->id,
            'title' => $chat->title,
        ];
    }

    /**
     * @param bool $problem
     * @return string
     */
    protected function getSystemMessage(bool $problem = true): string
    {
        $template = [];
        if ($problem) {
            $template[] = 'This is a real situation in ecommerce store, so prepare your best Data Analytics and Data Science skillset to give the best advice possible to our merchant in order to increase revenue by solving the specific problem of abandoned carts. Our merchant received an alert from you. You run a metrics monitoring system which regularly fetches and calculates metrics of interest from sources like Shopify API, Google Analytics 4, Google Ads and Meta Ads. Metrics monitoring system utilizes 6 sigma algorithm to detect anomalies on hourly basis. You also understand metrics which evaluate factors which directly influence our metric of interest. Your job is to identify the most probable cause of a given anomaly and if this is a positive anomaly - your recommendation must point out the main reasons for this and the importance of keeping up this way. If the anomaly is negative - point out the main reasons for this and the importance of solving this problem. Keep your recommendation simple because the person who will read it is not an analytics expert. And don’t write a novel - simply one or more recommendations with clear actionable insights, I\'ll provide a structure for you.';
        } else {
            $template[] = 'This is a real situation in ecommerce store, so prepare your best Data Analytics and Data Science skillset to give the best advice possible to our merchant in order to increase revenue by solving the specific problem of abandoned carts. Our merchant received a recommendation from you. You run a metrics monitoring system which regularly fetches and calculates metrics of interest from sources like Shopify API, Google Analytics 4, Google Ads and Meta Ads. Metrics monitoring system utilizes 6 sigma algorithm to detect anomalies on hourly basis. For the current metric of interest in the given recommendation there is no anomaly - everything is just fine, so you recommend simply to continue improving this metric. You also understand metrics which evaluate factors which directly influence our metric of interest. Your job is to identify the most fruitful way to improve a metric of interest by examining metrics which evaluate factors which directly influence our metric of interest. Keep your recommendation simple because the person who will read it is not an analytics expert. And don’t write a novel - simply one or more recommendations with clear actionable insights, I\'ll provide a structure for you.';
        }
        $template[] = 'Structure you must follow in your response:';
        $template[] = '1. Main metric: what happened and what does it mean in terms of influence on Revenue.';
        $template[] = '2. For each submetric:';
        $template[] = 'a) What happened and what does it mean in terms of influence on the main metric.';
        $template[] = 'b) What actions to take to improve the situation.';
        $template[] = 'Like a thesis about the main metric and action to take, same for each metric of influence and don\'t forget that you must use metrics of influence as references for causes of some effect on metric of interest. If there is something complex in your recommendation - recommend to hire a specialist with expertise with concrete matter. Also, don\'t use email jargon, you are speaking with your best friend with respect and sincere will to help. Finish with something like "if you need more help, feel free to ask", don\'t finish your message as it is an email.';

        return implode("\n", $template);
    }

    /**
     * @param $chat
     * @param false $ignoreFirst
     * @return string|null
     */
    public function getFirstAutoGeneratedUserMessage($chat, bool $ignoreFirst = false): ?string
    {
        $chatAlert = $chat->alert;

        $template = [];
        if (!$ignoreFirst) {
            $template[] = "Hey, I got following message from metrics monitoring system:";
        }

        if ($chat->alert->period === Metric::PERIOD_HOUR) {
            $template[] = Notifications::getMessageFromAlert($chatAlert);
            $template[] = '';
        } elseif ($chat->alert->period === Metric::PERIOD_DAY) {
            $template[] = 'Your ' . Metric::DESCRIPTIONS[$chatAlert->metric]['symbol'] . '(' . Metric::DESCRIPTIONS[$chatAlert->metric]['description'] . ') was just fine during the last 24 hours. Learn how to improve further.';
            $template[] = '';
        }

        $template[] = 'Metric of interest performance during last hour:';
        $template[] = '';

        $metric = Metric::where('user_id', $chatAlert->user_id)
            ->where('start_period', $chatAlert->start_period)
            ->where('end_period', $chatAlert->end_period)
            ->first();

        $metricPrevious = Metric::where('user_id', $chatAlert->user_id)
            ->where('start_period', Carbon::parse($chatAlert->start_period)->subHour())
            ->where('end_period', Carbon::parse($chatAlert->end_period)->subHour())
            ->first();

        $template[] = Metric::DESCRIPTIONS[$chatAlert->metric]['description'] . '(' . Metric::DESCRIPTIONS[$chatAlert->metric]['name'] . ')';
        $template[] = 'Hourly change: ' . round(($metric->{$chatAlert->metric} / $metricPrevious->{$chatAlert->metric} - 1) * 100, 2) . '%';
        $template[] = 'Metric explanation: ' . Metric::DESCRIPTIONS[$chatAlert->metric]['definition'];

        $subMetrics = Metric::DESCRIPTIONS[$chatAlert->metric]['sub_metrics']['default'];
        foreach ($subMetrics as $subMetric) {
            if (is_array($metric->{$subMetric}) && !empty($metric->{$subMetric}) || !empty($metric->{$subMetric})) {
                $template[] = '';
                $template[] = Metric::DESCRIPTIONS[$subMetric]['description'] . '(' . Metric::DESCRIPTIONS[$subMetric]['name'] . ')';
                if (!is_array($metric->{$subMetric})) {
                    $template[] = 'Hourly change: ' . round(($metric->{$subMetric} / $metricPrevious->{$subMetric} - 1) * 100, 2) . '%';
                } elseif (in_array($subMetric, ['c_pmd', 'car_pmu'])) {
                    $sum = 0;
                    foreach ($metric->{$subMetric} as $paymentSystem => $count) {
                        $sum += $count;
                    }

                    $prepared = [];
                    foreach ($metric->{$subMetric} as $paymentSystem => $count) {
                        $prepared[] = $paymentSystem . ' is ' . round(($count / $sum * 100), 2) . '%';
                    }

                    $template[] = 'Popular payments systems: ' . implode(', ', $prepared);
                } elseif ($subMetric == 'l_map') {
                    $template[] = 'List of Most Abandoned Products: ' . implode(',', $metric->{$subMetric});
                }
                $template[] = 'Metric explanation: ' . Metric::DESCRIPTIONS[$subMetric]['definition'];
            }
        }

        return implode("\n", $template);
    }
}
