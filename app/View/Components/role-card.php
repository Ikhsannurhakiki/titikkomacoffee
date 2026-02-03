<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RoleCard extends Component
{
    public string $role;
    public bool $active;

    public function __construct(string $role, bool $active = false)
    {
        $this->role = $role;
        $this->active = $active;
    }

    public function render()
    {
        return view('components.role-card');
    }
}
