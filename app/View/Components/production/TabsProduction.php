<?php

namespace App\View\Components\production;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TabsProduction extends Component
{
  public $mrId;
  public $wip; // <-- PASTI ADA properti ini

  public function __construct($mrId, $wip) // <-- Konstruktor menerima $wip
  {
    $this->mrId = $mrId;
    $this->wip = $wip;
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render(): View|Closure|string
  {
    return view('components.production.tabs-production');
  }
}
