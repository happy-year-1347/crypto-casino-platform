<?php

/*
| Spanish validation messages for the rules the player site uses. Anything not
| listed here falls back to lang/en/validation.php.
*/
return [
    'accepted'       => 'Debe aceptar :attribute.',
    'array'          => 'El campo :attribute debe ser una lista.',
    'boolean'        => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'      => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date'           => 'El campo :attribute no es una fecha válida.',
    'different'      => 'Los campos :attribute y :other deben ser diferentes.',
    'digits'         => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'email'          => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists'         => 'El :attribute seleccionado no es válido.',
    'file'           => 'El campo :attribute debe ser un archivo.',
    'image'          => 'El campo :attribute debe ser una imagen.',
    'in'             => 'El :attribute seleccionado no es válido.',
    'integer'        => 'El campo :attribute debe ser un número entero.',
    'max'            => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'file'    => 'El archivo :attribute no puede superar :max kilobytes.',
        'string'  => 'El campo :attribute no puede tener más de :max caracteres.',
        'array'   => 'El campo :attribute no puede tener más de :max elementos.',
    ],
    'mimes'          => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min'            => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file'    => 'El archivo :attribute debe tener al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'numeric'        => 'El campo :attribute debe ser un número.',
    'password'       => [
        'letters'       => 'El campo :attribute debe contener al menos una letra.',
        'mixed'         => 'El campo :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers'       => 'El campo :attribute debe contener al menos un número.',
        'symbols'       => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El :attribute proporcionado apareció en una filtración de datos. Elija otro.',
    ],
    'regex'          => 'El formato de :attribute no es válido.',
    'required'       => 'El campo :attribute es obligatorio.',
    'required_if'    => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_with'  => 'El campo :attribute es obligatorio cuando :values está presente.',
    'same'           => 'Los campos :attribute y :other deben coincidir.',
    'size'           => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'string'  => 'El campo :attribute debe tener :size caracteres.',
    ],
    'string'         => 'El campo :attribute debe ser un texto.',
    'unique'         => 'Este :attribute ya está en uso.',
    'url'            => 'El campo :attribute debe ser una URL válida.',

    'custom' => [],

    'attributes' => [
        'name'                  => 'nombre',
        'email'                 => 'correo electrónico',
        'password'              => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'phone'                 => 'teléfono',
        'amount'                => 'monto',
        'term_a'                => 'términos',
        'agreement'             => 'acuerdo de usuario',
        'currency'              => 'moneda',
        'crypto_address'        => 'dirección',
        'pix_key'               => 'dirección',
        'pix_type'              => 'moneda',
        'accept_terms'          => 'términos',
    ],
];
