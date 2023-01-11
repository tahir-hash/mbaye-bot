<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;

class Chat extends Component
{
    public array $chats = [];

    public string $input = '';

    public function render(): View
    {
        return view('livewire.chat');
    }

    public function submit()
    {
        $this->chats[] = ['user' => 'human', 'request' => $this->input];

        $result = OpenAI::completions()->create([
            'max_tokens' => 100,
            'model' => 'text-davinci-003',
            'prompt' => $this->input
        ]);
        
        $this->chats[] = [
            'user' => 'ai',
            'response' => array_reduce(
                $result->toArray()['choices'],
                fn(string $result, array $choice) => $result . $choice['text'],
                ""
            )
        ];
        $this->input = "";
    }
}