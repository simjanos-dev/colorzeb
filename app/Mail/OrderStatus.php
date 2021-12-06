<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $orderProducts, $status, $customText)
    {
        $this->order = $order;
        $this->orderProducts = $orderProducts;
        $this->status = $status;
        $this->customText = $customText;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subjects = [
            'Feldolgozásra vár' => 'Megrendelés ' . $this->order->id,
            'Megerősítve' => 'Rendelésed megerősítve',
            'Elutasítva' => 'Rendelésed elutasítva',
            'Fizetésre vár' => 'Rendelésed fizetésre vár',
            'Feladva' => 'Rendelésed feladásra került',
            'Teljesítve' => 'Rendelésed teljesítve',
            'Törölve' => 'Rendelésed törölve',
        ];

        return $this->markdown('emails.order_status')
                    ->subject($subjects[$this->status])
                    ->with([
                        'order' => $this->order,
                        'orderProducts' => $this->orderProducts,
                        'customText' => $this->customText,
                        'status' => $this->status,
                    ]);
    }
}
