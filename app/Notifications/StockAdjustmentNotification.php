<?php

namespace App\Notifications;

use App\Models\Admin\Inventory\StockAdjustment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAdjustmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public StockAdjustment $stockAdjustment
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $item = $this->stockAdjustment->item;
        $location = $this->stockAdjustment->location;
        $adjustedBy = $this->stockAdjustment->adjustedBy;

        return (new MailMessage)
            ->subject('Penyesuaian Stok Memerlukan Persetujuan')
            ->line('Sebuah penyesuaian stok telah diajukan dan memerlukan persetujuan Anda.')
            ->line("**Item:** {$item->item_code} - {$item->item_name}")
            ->line("**Lokasi:** {$location->name}")
            ->line("**Stok Sistem:** {$this->stockAdjustment->system_quantity}")
            ->line("**Stok Fisik:** {$this->stockAdjustment->physical_quantity}")
            ->line("**Selisih:** {$this->stockAdjustment->difference}")
            ->line("**Disesuaikan Oleh:** {$adjustedBy->name}")
            ->line("**Alasan:** {$this->stockAdjustment->reason}")
            ->action('Tinjau Penyesuaian', route('admin.inventory.stock-adjustment'))
            ->line('Silakan tinjau dan setujui atau tolak penyesuaian ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stock_adjustment',
            'stock_adjustment_id' => $this->stockAdjustment->id,
            'item_code' => $this->stockAdjustment->item->item_code,
            'item_name' => $this->stockAdjustment->item->item_name,
            'location' => $this->stockAdjustment->location->name,
            'difference' => $this->stockAdjustment->difference,
            'adjusted_by' => $this->stockAdjustment->adjustedBy->name,
            'message' => "Penyesuaian stok untuk {$this->stockAdjustment->item->item_code} memerlukan persetujuan",
        ];
    }
}
