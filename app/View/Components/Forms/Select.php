<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public array $options,
        public bool $required = false,
        public ?string $value = null,
        public string $placeholder = '-- Choisissez --'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
