<?php

namespace App\Http\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Contracts\View\View;
use OpenAI\Exceptions\ErrorException;

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
        try {
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
                    fn (string $result, array $choice) => $result . $choice['text'],
                    ""
                )
            ];
            $this->input = "";
        } catch (ErrorException $th) {
            $this->input = "";
            session()->flash("message","Votre compte a été bloqué, veuillez contacter l'administreur.");
        }
    }
}
