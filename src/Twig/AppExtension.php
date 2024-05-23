<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('masked_email', [$this, 'maskEmail']),
        ];
    }

    public function maskEmail(string $email): string
    {
        $username = substr($email, 0, 2); // Les deux premiers caractères
        $domain = substr($email, strpos($email, '@')); // Récupérer le domaine complet
        $maskedUsername = $username . str_repeat('*', strlen($email) - strlen($domain) - 2); // Masquer les caractères intermédiaires
        return $maskedUsername . $domain;
    }
}