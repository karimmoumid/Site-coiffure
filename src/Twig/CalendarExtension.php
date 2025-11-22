<?php
// src/Twig/CalendarExtension.php
namespace App\Twig;

use App\Entity\Appointement;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalendarExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('google_calendar_link', [$this, 'googleCalendarLink']),
            new TwigFunction('outlook_calendar_link', [$this, 'outlookCalendarLink']),
        ];
    }

    public function googleCalendarLink(Appointement $appointment): string
    {
        $start = $appointment->getDateHour()->format('Ymd\THis\Z');
        $end = $appointment->getEndDateHour()->format('Ymd\THis\Z');
        $title = urlencode('Rendez-vous Salon Luxe');
        $details = urlencode('Votre rendez-vous a été confirmé.');
        $location = urlencode('Salon Luxe, Paris');

        return "https://calendar.google.com/calendar/r/eventedit?text={$title}&dates={$start}/{$end}&details={$details}&location={$location}";
    }

    public function outlookCalendarLink(Appointement $appointment): string
    {
        $start = $appointment->getDateHour()->format('Y-m-d\TH:i:s');
        $end = $appointment->getEndDateHour()->format('Y-m-d\TH:i:s');
        $subject = urlencode('Rendez-vous Salon Luxe');
        $body = urlencode('Votre rendez-vous a été confirmé.');
        $location = urlencode('Salon Luxe, Paris');

        return "https://outlook.office.com/calendar/0/deeplink/compose?subject={$subject}&body={$body}&startdt={$start}&enddt={$end}&location={$location}";
    }
}
