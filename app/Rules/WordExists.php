<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\WordService;

class WordExists implements Rule
{
    public function __construct()
    {
        $this->service = app(WordService::class);
        $this->word = null;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->word = $this->service->findByWordRaw($value);
        return ! ((bool) $this->service->findByWordRaw($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $wordString = sprintf('<b>%s</b>', $this->word->word);

        $str = $wordString . ' already exists in our database.';

        if (!$this->word->pending && !$this->word->rejected) {
            return sprintf('%s Click <a class="underline" href="/word/%s">here</a> to view it', $str, $this->word->slug);
        }

        if ($this->word->pending) {
            return $wordString . ' has already been suggested and is currently awaiting review!';
        }

        if ($this->word->rejected) {
            return $wordString . ' has already been suggested and has been rejected. Please try another!';
        }

        return sprintf('%s Click <a href="/word/%s">here</a> to view it', $str, $this->word->slug);
    }
}
