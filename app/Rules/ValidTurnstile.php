<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if no secret key is set (development without CAPTCHA)
        if (!config('services.turnstile.secret_key')) {
            return;
        }

        if (!$value) {
            $fail(__('global.messages.captcha_required'));
            return;
        }

        $result = TurnstileService::verify($value, request()->ip());

        if (!$result['success']) {
            \Illuminate\Support\Facades\Log::warning('Cloudflare Turnstile validation failed', [
                'error_codes' => $result['error_codes'] ?? [],
                'ip' => request()->ip(),
            ]);

            $fail(__('global.messages.captcha_failed'));
        }
    }
}
