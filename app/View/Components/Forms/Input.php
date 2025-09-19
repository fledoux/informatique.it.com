<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public bool $required = false,
        public ?string $placeholder = null,
        public ?string $value = null,
        public ?int $rows = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.forms.input');
    }
}
