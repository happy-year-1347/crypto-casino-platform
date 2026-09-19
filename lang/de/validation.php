<?php

/*
| German validation messages for the rules the player site uses. Anything not
| listed here falls back to lang/en/validation.php.
*/
return [
    'accepted'       => ':attribute muss akzeptiert werden.',
    'array'          => ':attribute muss eine Liste sein.',
    'boolean'        => ':attribute muss wahr oder falsch sein.',
    'confirmed'      => 'Die Bestätigung von :attribute stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date'           => ':attribute ist kein gültiges Datum.',
    'different'      => ':attribute und :other müssen sich unterscheiden.',
    'digits'         => ':attribute muss :digits Ziffern haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Ziffern haben.',
    'email'          => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'         => 'Die Auswahl für :attribute ist ungültig.',
    'file'           => ':attribute muss eine Datei sein.',
    'image'          => ':attribute muss ein Bild sein.',
    'in'             => 'Die Auswahl für :attribute ist ungültig.',
    'integer'        => ':attribute muss eine ganze Zahl sein.',
    'max'            => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file'    => ':attribute darf nicht größer als :max Kilobyte sein.',
        'string'  => ':attribute darf nicht länger als :max Zeichen sein.',
        'array'   => ':attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes'          => ':attribute muss eine Datei vom Typ :values sein.',
    'min'            => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute muss mindestens :min Kilobyte groß sein.',
        'string'  => ':attribute muss mindestens :min Zeichen lang sein.',
        'array'   => ':attribute muss mindestens :min Elemente haben.',
    ],
    'numeric'        => ':attribute muss eine Zahl sein.',
    'password'       => [
        'letters'       => ':attribute muss mindestens einen Buchstaben enthalten.',
        'mixed'         => ':attribute muss Groß- und Kleinbuchstaben enthalten.',
        'numbers'       => ':attribute muss mindestens eine Zahl enthalten.',
        'symbols'       => ':attribute muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => 'Dieses :attribute ist in einem Datenleck aufgetaucht. Bitte wählen Sie ein anderes.',
    ],
    'regex'          => 'Das Format von :attribute ist ungültig.',
    'required'       => ':attribute ist erforderlich.',
    'required_if'    => ':attribute ist erforderlich, wenn :other :value ist.',
    'required_with'  => ':attribute ist erforderlich, wenn :values angegeben ist.',
    'same'           => ':attribute und :other müssen übereinstimmen.',
    'size'           => [
        'numeric' => ':attribute muss :size sein.',
        'string'  => ':attribute muss :size Zeichen lang sein.',
    ],
    'string'         => ':attribute muss ein Text sein.',
    'unique'         => 'Diese(s) :attribute wird bereits verwendet.',
    'url'            => ':attribute muss eine gültige URL sein.',

    'custom' => [],

    'attributes' => [
        'name'                  => 'Name',
        'email'                 => 'E-Mail',
        'password'              => 'Passwort',
        'password_confirmation' => 'Passwortbestätigung',
        'phone'                 => 'Telefon',
        'amount'                => 'Betrag',
        'term_a'                => 'Bedingungen',
        'agreement'             => 'Nutzervereinbarung',
        'currency'              => 'Währung',
        'crypto_address'        => 'Adresse',
        'pix_key'               => 'Adresse',
        'pix_type'              => 'Coin',
        'accept_terms'          => 'Bedingungen',
    ],
];
