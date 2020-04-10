<?php

namespace Corals\User\Communication\Classes;


use Corals\User\Communication\Models\NotificationTemplate;
use Fleshgrinder\Core\Formatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

abstract class CoralsBaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var
     */
    protected $templateName;
    /**
     * @var
     */
    protected $data;

    /**
     * @var NotificationTemplate
     */
    protected $notificationTemplate;

    /**
     * @param $templateName
     * @param $eventName
     * @param $data
     * @throws \Exception
     */
    public function initNotification($templateName, $eventName, $data)
    {
        $this->setTemplateName($templateName);

        $notificationTemplate = NotificationTemplate::query()->where('name', $templateName)->first();

        if (!$notificationTemplate) {
            $notificationTemplate = NotificationTemplate::query()->where('event_name', $eventName)->first();
        }

        if (!$notificationTemplate) {
            throw new \Exception("Notification template for: $templateName is missing");
        }

        $this->setNotificationTemplate($notificationTemplate);

        $this->setData($data);
    }

    /**
     * @return mixed
     */
    public function getNotificationTemplate()
    {
        return $this->notificationTemplate;
    }

    /**
     * @param mixed $notificationTemplate
     */
    public function setNotificationTemplate($notificationTemplate)
    {
        $this->notificationTemplate = $notificationTemplate;
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @param mixed $templateName
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    protected function getMessageBodyByChannel($channel)
    {
        return $this->notificationTemplate->body[$channel] ?? '';
    }

    /*
     * to get array of parameters for message text
     * ex: the name of the user, the status, etc
     */
    abstract public function getNotificationMessageParameters($notifiable, $channel);

    /**
     * @return mixed
     */
    abstract public function getNotifiables();

    /**
     * @return array
     */
    public function getOnDemandNotificationNotifiables()
    {
        return [];
    }

    /*
     * @return array
     */
    public static abstract function getNotificationMessageParametersDescriptions();

    /**
     * @param null $subject
     * @param null $body
     * @return null
     */
    protected function mailable($subject = null, $body = null)
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getAttachments()
    {
        return [];
    }

    /**
     *  Get the notification's delivery channels.
     *
     * @param $notifiable
     * @return mixed
     */
    public function via($notifiable)
    {
        if (!$this->notificationTemplate) {
            return [];
        }
        $channels = $this->notificationTemplate->forced_channels;

        //if template has "user_preferences", the user options will be taken along with "force channels"
        if ($this->notificationTemplate->via && in_array('user_preferences', $this->notificationTemplate->via)) {
            $channels = array_unique(array_merge($notifiable->notification_preferences[$this->notificationTemplate->id] ?? [], $channels));
        }

        if ($notifiable instanceof AnonymousNotifiable) {
            if (in_array('database', $channels)) {
                unset($channels[array_search('database', $channels)]);
            }
        }

        return $channels;
    }


    /*
     * @return array
     * return title and body of notification
     */
    public function getNotificationAttributes($notifiable, $channel)
    {
        $notificationParameters = $this->getNotificationMessageParameters($notifiable, $channel);

        try {
            $notificationTitle = Formatter::format($this->notificationTemplate->title ?? '', $notificationParameters);
            $notificationBody = Formatter::format($this->getMessageBodyByChannel($channel) ?? '', $notificationParameters);
        } catch (\Exception $exception) {
            log_exception($exception, CoralsBaseNotification::class, 'getNotificationAttributes');
        }

        $attributes = [];

        $attributes['title'] = $notificationTitle;
        $attributes['body'] = $notificationBody;

        return $attributes;
    }


    /**
     * Get the mail representation of the notification.
     * @param $notifiable
     * @return mixed
     */
    public function toMail($notifiable)
    {
        $channel = 'mail';

        $notificationAttributes = $this->getNotificationAttributes($notifiable, $channel);

        $body = $notificationAttributes['body'];
        $subject = $notificationAttributes['title'];

        if (is_null($this->mailable())) {
            $mail = (new MailMessage)
                ->subject($subject)
                ->view('Notification::mail.general_email_template', ['body' => $body]);

            if (!empty($attachments = $this->getAttachments())) {
                if (!is_array($attachments)) {
                    $attachments = [$attachments];
                }

                foreach ($attachments as $attachment) {
                    if (!file_exists($attachment)) {
                        continue;
                    }

                    $mail->attach($attachment);
                }
            }

            return $mail;
        } else {
            if ($notifiable instanceof AnonymousNotifiable) {
                $notifiable = $notifiable->routes['mail'];
            }
            return $this->mailable($subject, $body)->to($notifiable);
        }
    }


    /**
     * Get the array representation of the notification.
     * (for Database)
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $channel = 'database';

        $notificationAttributes = $this->getNotificationAttributes($notifiable, $channel);

        return [
            'title' => $notificationAttributes['title'],
            'body' => $notificationAttributes['body']
        ];
    }

    public function toNexmo($notifiable)
    {

        $channel = 'nexmo';

        $notificationAttributes = $this->getNotificationAttributes($notifiable, $channel);


        return (new NexmoMessage())
            ->content($notificationAttributes['body']);
    }
}
