// Welcome bonus page per language. ":site" becomes the site name, ":bonus" the
// welcome bonus percentage, ":rollover" the rollover multiplier and ":min" the
// minimum deposit, all read from the admin settings so the page follows them.
// Each section: [title, [paragraphs...]]

const en = [
    ['What you get', [
        'Your first deposit at :site is topped up with an extra :bonus%. Deposit 100 and you play with 150. The bonus lands in your wallet by itself as soon as the payment is confirmed on the blockchain.',
    ]],
    ['How to claim it', [
        '1. Create an account. It takes a minute and needs nothing but an e-mail address.',
        '2. Open the cashier and choose a coin: Bitcoin, Litecoin, USDT, TRX or Dogecoin.',
        '3. Send at least :min to the address shown. There is no code to type in.',
        'The bonus is added to your first confirmed deposit only.',
    ]],
    ['Before you can withdraw it', [
        'The bonus must be wagered :rollover times before the bonus, and anything won with it, can be withdrawn. Your own deposit is never locked: you can withdraw that at any time, but doing so before the rollover is finished removes the bonus.',
    ]],
    ['Which games count', [
        'Every slot on the site counts towards the rollover at full value.',
    ]],
    ['The rules in short', [
        'One welcome bonus per person, household, device and IP address. You must be 18 or older. :site may change or end this offer at any time; a change never affects a bonus already in your wallet. The full Bonus Terms and the Terms of Service apply.',
    ]],
];

const pt_BR = [
    ['O que você recebe', [
        'Seu primeiro depósito no :site ganha :bonus% a mais. Deposite 100 e jogue com 150. O bônus cai na sua carteira sozinho assim que o pagamento é confirmado na blockchain.',
    ]],
    ['Como resgatar', [
        '1. Crie uma conta. Leva um minuto e só precisa de um e-mail.',
        '2. Abra o caixa e escolha a moeda: Bitcoin, Litecoin, USDT, TRX ou Dogecoin.',
        '3. Envie pelo menos :min para o endereço mostrado. Não há código para digitar.',
        'O bônus é somado apenas ao seu primeiro depósito confirmado.',
    ]],
    ['Antes de poder sacar', [
        'O bônus precisa ser apostado :rollover vezes antes que ele, e o que for ganho com ele, possa ser sacado. Seu próprio depósito nunca fica preso: você pode sacá-lo quando quiser, mas fazer isso antes de concluir o rollover remove o bônus.',
    ]],
    ['Quais jogos contam', [
        'Todos os slots do site contam para o rollover com valor integral.',
    ]],
    ['As regras em resumo', [
        'Um bônus de boas-vindas por pessoa, residência, aparelho e endereço IP. É preciso ter 18 anos ou mais. O :site pode mudar ou encerrar esta oferta a qualquer momento; a mudança nunca afeta um bônus que já está na sua carteira. Valem os Termos de Bônus e os Termos de Serviço.',
    ]],
];

const es = [
    ['Qué recibes', [
        'Tu primer depósito en :site se completa con un :bonus% extra. Deposita 100 y juegas con 150. El bono llega a tu monedero solo, en cuanto el pago se confirma en la blockchain.',
    ]],
    ['Cómo conseguirlo', [
        '1. Crea una cuenta. Lleva un minuto y solo hace falta un correo electrónico.',
        '2. Abre la caja y elige la moneda: Bitcoin, Litecoin, USDT, TRX o Dogecoin.',
        '3. Envía al menos :min a la dirección que aparece. No hay ningún código que escribir.',
        'El bono se suma únicamente a tu primer depósito confirmado.',
    ]],
    ['Antes de poder retirarlo', [
        'El bono debe apostarse :rollover veces antes de que el bono, y lo ganado con él, se puedan retirar. Tu propio depósito nunca queda bloqueado: puedes retirarlo cuando quieras, pero hacerlo antes de terminar el rollover elimina el bono.',
    ]],
    ['Qué juegos cuentan', [
        'Todas las tragaperras del sitio cuentan para el rollover por su valor completo.',
    ]],
    ['Las reglas en corto', [
        'Un bono de bienvenida por persona, domicilio, dispositivo y dirección IP. Debes tener 18 años o más. :site puede cambiar o terminar esta oferta en cualquier momento; el cambio nunca afecta a un bono que ya esté en tu monedero. Se aplican los Términos de Bonos y los Términos del Servicio.',
    ]],
];

const fr = [
    ['Ce que vous recevez', [
        'Votre premier dépôt sur :site est majoré de :bonus%. Déposez 100 et jouez avec 150. Le bonus arrive tout seul dans votre portefeuille dès que le paiement est confirmé sur la blockchain.',
    ]],
    ['Comment l\'obtenir', [
        '1. Créez un compte. Cela prend une minute et ne demande qu\'une adresse e-mail.',
        '2. Ouvrez la caisse et choisissez une monnaie : Bitcoin, Litecoin, USDT, TRX ou Dogecoin.',
        '3. Envoyez au moins :min à l\'adresse affichée. Aucun code à saisir.',
        'Le bonus s\'ajoute uniquement à votre premier dépôt confirmé.',
    ]],
    ['Avant de pouvoir le retirer', [
        'Le bonus doit être misé :rollover fois avant que le bonus, et ce qu\'il a rapporté, puissent être retirés. Votre propre dépôt n\'est jamais bloqué : vous pouvez le retirer quand vous voulez, mais le faire avant la fin du rollover supprime le bonus.',
    ]],
    ['Quels jeux comptent', [
        'Toutes les machines à sous du site comptent pour le rollover à leur pleine valeur.',
    ]],
    ['Les règles en bref', [
        'Un bonus de bienvenue par personne, foyer, appareil et adresse IP. Vous devez avoir 18 ans ou plus. :site peut modifier ou arrêter cette offre à tout moment ; une modification ne touche jamais un bonus déjà présent dans votre portefeuille. Les Conditions de bonus et les Conditions de service s\'appliquent.',
    ]],
];

const de = [
    ['Was Sie bekommen', [
        'Ihre erste Einzahlung bei :site wird um :bonus% aufgestockt. Zahlen Sie 100 ein und spielen Sie mit 150. Der Bonus erscheint von selbst in Ihrer Wallet, sobald die Zahlung auf der Blockchain bestätigt ist.',
    ]],
    ['So erhalten Sie ihn', [
        '1. Konto anlegen. Das dauert eine Minute und braucht nur eine E-Mail-Adresse.',
        '2. Kasse öffnen und eine Währung wählen: Bitcoin, Litecoin, USDT, TRX oder Dogecoin.',
        '3. Mindestens :min an die angezeigte Adresse senden. Es gibt keinen Code einzugeben.',
        'Der Bonus wird nur auf Ihre erste bestätigte Einzahlung gewährt.',
    ]],
    ['Bevor Sie ihn auszahlen können', [
        'Der Bonus muss :rollover Mal umgesetzt werden, bevor der Bonus und die damit erzielten Gewinne ausgezahlt werden können. Ihre eigene Einzahlung ist nie gesperrt: Sie können sie jederzeit auszahlen, doch vor Erfüllung der Umsatzbedingung entfällt dadurch der Bonus.',
    ]],
    ['Welche Spiele zählen', [
        'Alle Slots der Website zählen mit vollem Wert für die Umsatzbedingung.',
    ]],
    ['Die Regeln in Kürze', [
        'Ein Willkommensbonus pro Person, Haushalt, Gerät und IP-Adresse. Sie müssen 18 Jahre oder älter sein. :site kann dieses Angebot jederzeit ändern oder beenden; eine Änderung betrifft nie einen Bonus, der schon in Ihrer Wallet liegt. Es gelten die Bonusbedingungen und die Nutzungsbedingungen.',
    ]],
];

export default { en, pt_BR, es, fr, de };
