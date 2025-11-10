<?php

namespace App\View\Components\outbound;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TabsOutbound extends Component
{
  public $packingId;
  public $status;
  
  public function __construct($packingId, $status)
  {
    $this->packingId = $packingId;
    $this->status = $status;
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render(): View|Closure|string
  {
    return view('components.outbound.tabs-outbound');
  }
}
