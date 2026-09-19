// Support answers per language. ":site", ":bonus", ":rollover" and ":min" are
// replaced with the live values from the admin settings.
// Each section: [question, [paragraphs...]]

const en = [
    ['How do I deposit?', [
        'Open the cashier, choose a coin (Bitcoin, Litecoin, USDT, TRX or Dogecoin) and type the amount. The site shows you an address and the exact amount to send. Send it in one transaction from your own wallet. The balance is credited by itself once the network confirms the payment, usually within a few minutes.',
        'The smallest deposit is :min. Each coin also has a minimum set by the payment provider, and the cashier shows it before you pay.',
    ]],
    ['My deposit has not arrived yet', [
        'Check the transaction in your wallet first. As long as it is still unconfirmed on the blockchain, the balance cannot move. Busy networks can take longer than usual.',
        'If the transaction is confirmed and the balance is still not there after an hour, write to us with the coin, the amount and the transaction id.',
    ]],
    ['How do I withdraw?', [
        'Go to Wallet, then Withdraw. Choose the coin, paste an address from your own wallet and enter the amount. Check the address twice: a payment sent to a wrong address cannot be recovered by anyone.',
        'Requests are reviewed and paid out by us. You can follow the status under Transactions.',
    ]],
    ['I sent the wrong coin or the wrong amount', [
        'Write to us straight away with the transaction id. Send the exact amount shown at the cashier: an amount that does not match can be delayed until we check it by hand.',
    ]],
    ['How does the welcome bonus work?', [
        'Your first deposit is topped up with :bonus%. The bonus has to be wagered :rollover times before it, and anything won with it, can be withdrawn. Your own deposit is never locked.',
        'The Welcome Bonus page has the full rules.',
    ]],
    ['I forgot my password', [
        'Use "Forgot password" on the login screen and follow the link in the e-mail. If the e-mail does not arrive, check the spam folder, then write to us and we will reset it for you.',
    ]],
    ['Playing responsibly', [
        'The games are entertainment, not a way to earn. Only play with money you can afford to lose, and stop when it stops being fun. You must be 18 or older to hold an account.',
        'If you want your account closed, ask us and we will close it.',
    ]],
];

const pt_BR = [
    ['Como faço um depósito?', [
        'Abra o caixa, escolha a moeda (Bitcoin, Litecoin, USDT, TRX ou Dogecoin) e digite o valor. O site mostra um endereço e o valor exato a enviar. Envie em uma única transação a partir da sua carteira. O saldo é creditado sozinho assim que a rede confirmar o pagamento, normalmente em poucos minutos.',
        'O depósito mínimo é :min. Cada moeda também tem um mínimo definido pelo processador de pagamentos, e o caixa mostra esse valor antes de você pagar.',
    ]],
    ['Meu depósito ainda não chegou', [
        'Confira primeiro a transação na sua carteira. Enquanto estiver sem confirmação na blockchain, o saldo não pode ser movimentado. Redes congestionadas podem demorar mais do que o normal.',
        'Se a transação estiver confirmada e o saldo não aparecer depois de uma hora, escreva para nós com a moeda, o valor e o id da transação.',
    ]],
    ['Como faço um saque?', [
        'Vá em Carteira e depois em Saque. Escolha a moeda, cole um endereço da sua própria carteira e informe o valor. Confira o endereço duas vezes: um pagamento enviado para o endereço errado não pode ser recuperado por ninguém.',
        'Os pedidos são conferidos e pagos por nós. Você acompanha o status em Transações.',
    ]],
    ['Enviei a moeda errada ou o valor errado', [
        'Escreva para nós imediatamente com o id da transação. Envie exatamente o valor mostrado no caixa: um valor diferente pode ficar parado até conferirmos manualmente.',
    ]],
    ['Como funciona o bônus de boas-vindas?', [
        'Seu primeiro depósito recebe :bonus% a mais. O bônus precisa ser apostado :rollover vezes antes que ele, e o que for ganho com ele, possa ser sacado. Seu próprio depósito nunca fica preso.',
        'A página do Bônus de Boas-vindas tem as regras completas.',
    ]],
    ['Esqueci minha senha', [
        'Use "Esqueci a senha" na tela de login e siga o link do e-mail. Se o e-mail não chegar, verifique a caixa de spam e depois escreva para nós que redefinimos para você.',
    ]],
    ['Jogo responsável', [
        'Os jogos são entretenimento, não uma forma de ganhar dinheiro. Jogue apenas com um valor que você pode perder e pare quando deixar de ser divertido. É preciso ter 18 anos ou mais para ter conta.',
        'Se quiser encerrar sua conta, peça para nós que encerramos.',
    ]],
];

const es = [
    ['¿Cómo hago un depósito?', [
        'Abre la caja, elige una moneda (Bitcoin, Litecoin, USDT, TRX o Dogecoin) y escribe el importe. El sitio te muestra una dirección y el importe exacto a enviar. Envíalo en una sola transacción desde tu propio monedero. El saldo se acredita solo en cuanto la red confirma el pago, normalmente en unos minutos.',
        'El depósito mínimo es :min. Cada moneda tiene además un mínimo fijado por el proveedor de pagos, y la caja lo muestra antes de pagar.',
    ]],
    ['Mi depósito no ha llegado', [
        'Comprueba primero la transacción en tu monedero. Mientras siga sin confirmar en la blockchain, el saldo no se puede mover. Las redes congestionadas pueden tardar más de lo normal.',
        'Si la transacción está confirmada y el saldo sigue sin aparecer después de una hora, escríbenos con la moneda, el importe y el id de la transacción.',
    ]],
    ['¿Cómo retiro?', [
        'Entra en Monedero y luego en Retirar. Elige la moneda, pega una dirección de tu propio monedero e indica el importe. Comprueba la dirección dos veces: un pago enviado a una dirección equivocada no lo puede recuperar nadie.',
        'Nosotros revisamos y pagamos las solicitudes. Puedes seguir el estado en Transacciones.',
    ]],
    ['He enviado la moneda o el importe equivocado', [
        'Escríbenos enseguida con el id de la transacción. Envía el importe exacto que muestra la caja: un importe distinto puede quedar retenido hasta que lo revisemos a mano.',
    ]],
    ['¿Cómo funciona el bono de bienvenida?', [
        'Tu primer depósito se completa con un :bonus% extra. El bono debe apostarse :rollover veces antes de que él, y lo ganado con él, se puedan retirar. Tu propio depósito nunca queda bloqueado.',
        'La página del Bono de Bienvenida tiene las reglas completas.',
    ]],
    ['He olvidado mi contraseña', [
        'Usa "Olvidé mi contraseña" en la pantalla de acceso y sigue el enlace del correo. Si el correo no llega, mira en la carpeta de spam y después escríbenos y te la restablecemos.',
    ]],
    ['Juego responsable', [
        'Los juegos son entretenimiento, no una forma de ganar dinero. Juega solo con dinero que puedas permitirte perder y para cuando deje de ser divertido. Debes tener 18 años o más para tener una cuenta.',
        'Si quieres cerrar tu cuenta, pídenoslo y la cerramos.',
    ]],
];

const fr = [
    ['Comment déposer ?', [
        'Ouvrez la caisse, choisissez une monnaie (Bitcoin, Litecoin, USDT, TRX ou Dogecoin) et saisissez le montant. Le site affiche une adresse et le montant exact à envoyer. Envoyez-le en une seule transaction depuis votre propre portefeuille. Le solde est crédité tout seul dès que le réseau confirme le paiement, en général en quelques minutes.',
        'Le dépôt minimum est de :min. Chaque monnaie a aussi un minimum fixé par le prestataire de paiement, que la caisse affiche avant le paiement.',
    ]],
    ['Mon dépôt n\'est pas arrivé', [
        'Vérifiez d\'abord la transaction dans votre portefeuille. Tant qu\'elle n\'est pas confirmée sur la blockchain, le solde ne peut pas bouger. Les réseaux chargés peuvent prendre plus de temps que d\'habitude.',
        'Si la transaction est confirmée et que le solde n\'apparaît toujours pas après une heure, écrivez-nous avec la monnaie, le montant et l\'identifiant de la transaction.',
    ]],
    ['Comment retirer ?', [
        'Allez dans Portefeuille puis Retrait. Choisissez la monnaie, collez une adresse de votre propre portefeuille et indiquez le montant. Vérifiez l\'adresse deux fois : un paiement envoyé à une mauvaise adresse ne peut être récupéré par personne.',
        'Les demandes sont vérifiées et payées par nous. Vous suivez le statut dans Transactions.',
    ]],
    ['J\'ai envoyé la mauvaise monnaie ou le mauvais montant', [
        'Écrivez-nous tout de suite avec l\'identifiant de la transaction. Envoyez le montant exact affiché à la caisse : un montant différent peut être mis en attente le temps d\'une vérification manuelle.',
    ]],
    ['Comment fonctionne le bonus de bienvenue ?', [
        'Votre premier dépôt est majoré de :bonus%. Le bonus doit être misé :rollover fois avant que lui, et ce qu\'il a rapporté, puissent être retirés. Votre propre dépôt n\'est jamais bloqué.',
        'La page Bonus de bienvenue contient les règles complètes.',
    ]],
    ['J\'ai oublié mon mot de passe', [
        'Utilisez « Mot de passe oublié » sur l\'écran de connexion et suivez le lien reçu par e-mail. Si l\'e-mail n\'arrive pas, regardez dans les indésirables, puis écrivez-nous et nous le réinitialiserons.',
    ]],
    ['Jouer raisonnablement', [
        'Les jeux sont un divertissement, pas un moyen de gagner de l\'argent. Ne jouez qu\'avec une somme que vous pouvez perdre et arrêtez quand ce n\'est plus un plaisir. Il faut avoir 18 ans ou plus pour avoir un compte.',
        'Si vous souhaitez fermer votre compte, demandez-le-nous et nous le fermerons.',
    ]],
];

const de = [
    ['Wie zahle ich ein?', [
        'Öffnen Sie die Kasse, wählen Sie eine Währung (Bitcoin, Litecoin, USDT, TRX oder Dogecoin) und geben Sie den Betrag ein. Die Website zeigt eine Adresse und den genauen Betrag an. Senden Sie ihn in einer einzigen Transaktion aus Ihrer eigenen Wallet. Das Guthaben wird von selbst gutgeschrieben, sobald das Netzwerk die Zahlung bestätigt, meist innerhalb weniger Minuten.',
        'Die kleinste Einzahlung ist :min. Jede Währung hat zusätzlich einen Mindestbetrag des Zahlungsdienstleisters, den die Kasse vor der Zahlung anzeigt.',
    ]],
    ['Meine Einzahlung ist nicht angekommen', [
        'Prüfen Sie zuerst die Transaktion in Ihrer Wallet. Solange sie auf der Blockchain unbestätigt ist, kann das Guthaben nicht bewegt werden. Ausgelastete Netzwerke brauchen länger als sonst.',
        'Ist die Transaktion bestätigt und das Guthaben nach einer Stunde noch nicht da, schreiben Sie uns mit Währung, Betrag und Transaktions-ID.',
    ]],
    ['Wie zahle ich aus?', [
        'Gehen Sie zu Wallet und dann zu Auszahlung. Wählen Sie die Währung, fügen Sie eine Adresse aus Ihrer eigenen Wallet ein und geben Sie den Betrag an. Prüfen Sie die Adresse zweimal: Eine Zahlung an eine falsche Adresse kann niemand zurückholen.',
        'Anfragen werden von uns geprüft und ausgezahlt. Den Status sehen Sie unter Transaktionen.',
    ]],
    ['Ich habe die falsche Währung oder den falschen Betrag gesendet', [
        'Schreiben Sie uns sofort mit der Transaktions-ID. Senden Sie genau den Betrag, den die Kasse anzeigt: Ein abweichender Betrag kann liegen bleiben, bis wir ihn von Hand prüfen.',
    ]],
    ['Wie funktioniert der Willkommensbonus?', [
        'Ihre erste Einzahlung wird um :bonus% aufgestockt. Der Bonus muss :rollover Mal umgesetzt werden, bevor er und die damit erzielten Gewinne ausgezahlt werden können. Ihre eigene Einzahlung ist nie gesperrt.',
        'Die vollständigen Regeln stehen auf der Seite Willkommensbonus.',
    ]],
    ['Ich habe mein Passwort vergessen', [
        'Nutzen Sie „Passwort vergessen" auf der Anmeldeseite und folgen Sie dem Link in der E-Mail. Kommt die E-Mail nicht an, sehen Sie im Spam-Ordner nach und schreiben Sie uns dann, wir setzen es für Sie zurück.',
    ]],
    ['Verantwortungsvoll spielen', [
        'Die Spiele sind Unterhaltung und kein Weg, Geld zu verdienen. Spielen Sie nur mit Geld, dessen Verlust Sie verkraften, und hören Sie auf, wenn es keinen Spaß mehr macht. Für ein Konto müssen Sie 18 Jahre oder älter sein.',
        'Möchten Sie Ihr Konto schließen lassen, sagen Sie uns Bescheid und wir schließen es.',
    ]],
];

export default { en, pt_BR, es, fr, de };
