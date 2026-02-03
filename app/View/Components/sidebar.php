<?php

namespace App\View\Components;

use Closure;
use App\Models\Staff;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class sidebar extends Component
{
    /**
     * Create a new component instance.
     */

    public $currentStaff;

    public function __construct()
    {
        $this->currentStaff = Staff::find(session('staff_id'));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
