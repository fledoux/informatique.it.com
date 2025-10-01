<?php

namespace App\Helpers;
use App\Services\PushoverService;

class Helper
{
	/**
	 * Génère les initiales à partir du prénom et nom
	 */
	public static function generateInitials(string $firstname, string $lastname): string
	{
		$firstInitial = !empty($firstname) ? strtoupper(mb_substr(trim($firstname), 0, 1)) : '';
		
		$lastInitials = '';
		if (!empty($lastname)) {
			$cleanLastname = trim($lastname);
			$lastInitials = strtoupper(mb_substr($cleanLastname, 0, 1)); // Première lettre
			if (mb_strlen($cleanLastname) > 1) {
				$lastInitials .= strtoupper(mb_substr($cleanLastname, -1, 1)); // Dernière lettre
			}
		}

		return $firstInitial . $lastInitials;
	}

	/**
     * Envoie une notification lors de la création d'un message
     */
    public static function sendPushoverNotification(string $title, string $message): void
    {        
        PushoverService::send($title, $message);
    }

	/**
	 * Formate le nom complet d'une personne
	 */
	public static function getFullName(?string $firstname, ?string $lastname, ?string $name = null): string
	{
		$fullName = trim(($firstname ?? '') . ' ' . ($lastname ?? ''));

		if (!empty($fullName)) {
			return $fullName;
		}

		return $name ?? '';
	}

	/**
	 * Formate un numéro de téléphone
	 */
	public static function formatPhone(?string $phone): ?string
	{
		if (empty($phone)) {
			return null;
		}

		// Supprime tous les espaces, points, tirets
		$cleaned = preg_replace('/[\s\-\.]/', '', $phone);

		// Formate si c'est un numéro français (10 chiffres commençant par 0)
		if (preg_match('/^0[1-9]\d{8}$/', $cleaned)) {
			return substr($cleaned, 0, 2) . ' ' . substr($cleaned, 2, 2) . ' ' .
				substr($cleaned, 4, 2) . ' ' . substr($cleaned, 6, 2) . ' ' .
				substr($cleaned, 8, 2);
		}

		return $phone; // Retourne le format original si pas reconnu
	}

	/**
	 * Génère une couleur de badge selon le statut
	 */
	public static function getStatusBadgeColor(string $status, array $mapping = []): string
	{
		$defaultMapping = [
			'active' => 'success',
			'inactive' => 'secondary',
			'new' => 'info',
			'in_progress' => 'primary',
			'waiting' => 'warning',
			'resolved' => 'success',
			'closed' => 'secondary',
			'canceled' => 'danger',
			'low' => 'info',
			'normal' => 'primary',
			'high' => 'warning',
			'urgent' => 'danger'
		];

		$colors = array_merge($defaultMapping, $mapping);

		return $colors[$status] ?? 'secondary';
	}

	/**
	 * Génère un lien mailto (équivalent Html::mail_to de FuelPHP)
	 */
	public static function mailTo(string $email, ?string $name = null, array $attributes = ['class' => 'text-orange text-decoration-none']): string
	{
		$name = $name ?: $email;
		$href = 'mailto:' . $email;
		
		// Ajouter des attributs HTML si fournis
		$attrString = '';
		foreach ($attributes as $key => $value) {
			$attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
		}
		
		return '<a href="' . $href . '"' . $attrString . '>' . htmlspecialchars($name) . '</a>';
	}

	public static function asLetters($number = null, $masculin = 'm')
	{
		if (!is_null($number)) {
			switch ($masculin) {
				case 'f':
					$adde = 'e';
					break;

				default:
					$adde = '';
					break;
			}

			$convert = explode('.', $number);

			$num[17] = array(
				'zéro',
				'un' . $adde,
				'deux',
				'trois',
				'quatre',
				'cinq',
				'six',
				'sept',
				'huit',
				'neuf',
				'dix',
				'onze',
				'douze',
				'treize',
				'quatorze',
				'quinze',
				'seize'
			);

			$num[100] = array(
				20 => 'vingt',
				30 => 'trente',
				40 => 'quarante',
				50 => 'cinquante',
				60 => 'soixante',
				70 => 'soixante-dix',
				80 => 'quatre-vingt',
				90 => 'quatre-vingt-dix'
			);

			if (isset($convert[1]) && $convert[1] != '') {
				return Self::asLetters($convert[0]) . ' et ' . Self::asLetters($convert[1]);
			}
			if ($number < 0) return 'moins ' . Self::asLetters(-$number);
			if ($number < 17) {
				return $num[17][$number];
			} elseif ($number < 20) {
				return 'dix-' . Self::asLetters($number - 10);
			} elseif ($number < 100) {
				if ($number % 10 == 0) {
					return $num[100][$number];
				} elseif (substr($number, -1) == 1) {
					if (((int) ($number / 10) * 10) < 70) {
						return Self::asLetters((int) ($number / 10) * 10) . '-et-un' . $adde;
					} elseif ($number == 71) {
						return 'soixante-et-onze';
					} elseif ($number == 81) {
						return 'quatre-vingt-un' . $adde;
					} elseif ($number == 91) {
						return 'quatre-vingt-onze';
					}
				} elseif ($number < 70) {
					return Self::asLetters($number - $number % 10) . '-' . Self::asLetters($number % 10);
				} elseif ($number < 80) {
					return Self::asLetters(60) . '-' . Self::asLetters($number % 20);
				} else {
					return Self::asLetters(80) . '-' . Self::asLetters($number % 20);
				}
			} elseif ($number == 100) {
				return 'cent';
			} elseif ($number < 200) {
				return Self::asLetters(100) . ' ' . Self::asLetters($number % 100);
			} elseif ($number < 1000) {
				return Self::asLetters((int) ($number / 100)) . ' ' . Self::asLetters(100) . ($number % 100 > 0 ? ' ' . Self::asLetters($number % 100) : '');
			} elseif ($number == 1000) {
				return 'mille';
			} elseif ($number < 2000) {
				return Self::asLetters(1000) . ' ' . Self::asLetters($number % 1000) . ' ';
			} elseif ($number < 1000000) {
				return Self::asLetters((int) ($number / 1000)) . ' ' . Self::asLetters(1000) . ($number % 1000 > 0 ? ' ' . Self::asLetters($number % 1000) : '');
			} elseif ($number == 1000000) {
				return 'millions';
			} elseif ($number < 2000000) {
				return Self::asLetters(1000000) . ' ' . Self::asLetters($number % 1000000);
			} elseif ($number < 1000000000) {
				return Self::asLetters((int) ($number / 1000000)) . ' ' . Self::asLetters(1000000) . ($number % 1000000 > 0 ? ' ' . Self::asLetters($number % 1000000) : '');
			}
		} else {
			return false;
		}
	}

	/**
	 * Retourne une salutation contextuelle selon l'heure (traduite)
	 */
	public static function getGreeting(): string
	{
		$hour = now()->hour;

		if ($hour >= 5 && $hour < 12) {
			return __('global.Good morning');
		} elseif ($hour >= 12 && $hour < 19) {
			return __('global.Good afternoon');
		} else {
			return __('global.Good evening');
		}
	}

	/**
	 * Valide la force d'un mot de passe selon nos critères
	 * 
	 * @param string $password
	 * @return array ['valid' => bool, 'errors' => array]
	 */
	public static function validatePasswordStrength(string $password): array
	{
		$errors = [];
		
		// Au moins 8 caractères
		if (strlen($password) < 8) {
			$errors[] = __('validation.password_min_length', ['min' => 8]);
		}
		
		// Au moins une minuscule
		if (!preg_match('/[a-z]/', $password)) {
			$errors[] = __('validation.password_lowercase');
		}
		
		// Au moins une majuscule
		if (!preg_match('/[A-Z]/', $password)) {
			$errors[] = __('validation.password_uppercase');
		}
		
		// Au moins un chiffre
		if (!preg_match('/[0-9]/', $password)) {
			$errors[] = __('validation.password_number');
		}
		
		// Au moins un caractère spécial
		if (!preg_match('/[!@#$%^&*]/', $password)) {
			$errors[] = __('validation.password_special', ['chars' => '!@#$%^&*']);
		}
		
		return [
			'valid' => empty($errors),
			'errors' => $errors
		];
	}

	/**
	 * Retourne les règles de validation pour un mot de passe fort
	 * 
	 * @return array
	 */
	public static function getPasswordRules(): array
	{
		return [
			__('validation.password_min_length', ['min' => 8]),
			__('validation.password_lowercase'),
			__('validation.password_uppercase'),
			__('validation.password_number'),
			__('validation.password_special', ['chars' => '!@#$%^&*']),
		];
	}
}
