<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Tools\Crypt;
use Illuminate\Support\Facades\Lang;

class TelegramNotification extends Notification
{
    use Queueable;
    // private $user;

    // /**
    //  * Create a new notification instance.
    //  *
    //  * @return void
    //  */
    // public function __construct(User $user)
    // {
    //     $this->user = $user;
    // }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }
    public function toTelegram($notifiable)
    {
        try {

            $concat = Crypt::encrypt($notifiable->routes['telegram']->chat->id.'@@@'.$notifiable->routes['telegram']->text.'@@@'.now());
            if(config('app.env') == 'local'){
                $link = 'https://google.com?token='.$concat;
            }else if(config('app.env') == 'sandbox'){
                $link = 'https://ba-support.builderall.io/login/telegram?token='.$concat;
            }else if(config('app')['is_helpdesk']){
                $link = 'https://hs.builderall.com/login/telegram?token='.$concat;
            }else{
                $link = 'https://ba-support.builderall.com/login/telegram?token='.$concat;
            }

            return TelegramMessage::create()
            ->to($notifiable->routes['telegram']->chat->id)
            ->content(Lang::get("app.bs-to-confirm-the-registration-need-to-log", [], 'pt_BR').":")
            ->button(Lang::get("app.bs-open", [], 'pt_BR'), $link);
        }catch (\Exception $e) {
             echo $e->getMessage();
        }

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
