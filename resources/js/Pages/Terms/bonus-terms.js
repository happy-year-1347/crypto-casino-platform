// Bonus terms per language. ":site" becomes the site name and ":rollover" the
// rollover multiplier from the admin settings, so the text follows the setting.
// Each section: [title, [paragraphs...]]

const en = [
    ['1. General', [
        '1.1. A bonus is a promotion offered by :site. It is not cash and it has no value until the conditions on this page are met.',
        '1.2. :site may add, change, suspend or end any promotion at any time. A change never affects a bonus you have already been granted.',
        '1.3. Where a promotion has its own page, the rules on that page apply on top of these ones.',
    ]],
    ['2. Rollover', [
        '2.1. A bonus must be wagered :rollover times before the bonus and anything won with it can be withdrawn.',
        '2.2. Your progress is shown in your wallet. Until the rollover is complete, the bonus part of your balance cannot be withdrawn.',
        '2.3. Only real bets on the games of this site count towards the rollover. Cancelled or voided rounds do not count.',
    ]],
    ['3. One per person', [
        '3.1. A promotion may be claimed once per person, household, device, IP address and payment method, unless the promotion says otherwise.',
        '3.2. Accounts that belong to the same person are treated as one account. Extra accounts are closed and their bonuses removed.',
    ]],
    ['4. How the balance is used', [
        '4.1. Bets are taken from your real balance first. The bonus balance is used once the real balance is spent, unless the promotion says otherwise.',
        '4.2. A withdrawal made before the rollover is complete removes the bonus balance and anything won from it.',
    ]],
    ['5. Abuse', [
        '5.1. Bets placed only to clear the rollover with no real risk, betting patterns that cover most outcomes, moving funds between accounts, and the use of several accounts are all bonus abuse.',
        '5.2. Where abuse is found, :site may cancel the bonus and any winnings from it, and may close the account. Real money that you deposited yourself is always returned.',
    ]],
    ['6. Decisions', [
        '6.1. In a dispute about a bonus, the transaction records of :site are the final record.',
        '6.2. If any rule here conflicts with the Terms of Service, the Terms of Service apply.',
    ]],
];

const pt_BR = [
    ['1. Geral', [
        '1.1. O bônus é uma promoção oferecida pelo :site. Não é dinheiro e não tem valor até que as condições desta página sejam cumpridas.',
        '1.2. O :site pode criar, alterar, suspender ou encerrar qualquer promoção a qualquer momento. A mudança nunca afeta um bônus já concedido.',
        '1.3. Quando a promoção tiver página própria, as regras daquela página valem junto com estas.',
    ]],
    ['2. Rollover', [
        '2.1. O bônus precisa ser apostado :rollover vezes antes que ele e o que for ganho com ele possam ser sacados.',
        '2.2. Seu progresso aparece na carteira. Até o rollover terminar, a parte de bônus do saldo não pode ser sacada.',
        '2.3. Só apostas reais nos jogos deste site contam para o rollover. Rodadas canceladas ou anuladas não contam.',
    ]],
    ['3. Uma por pessoa', [
        '3.1. Cada promoção pode ser usada uma vez por pessoa, residência, aparelho, endereço IP e meio de pagamento, salvo se a promoção disser o contrário.',
        '3.2. Contas da mesma pessoa são tratadas como uma só. Contas extras são encerradas e seus bônus removidos.',
    ]],
    ['4. Como o saldo é usado', [
        '4.1. As apostas saem primeiro do saldo real. O saldo de bônus é usado depois que o saldo real acaba, salvo se a promoção disser o contrário.',
        '4.2. Um saque feito antes de concluir o rollover elimina o saldo de bônus e o que foi ganho com ele.',
    ]],
    ['5. Abuso', [
        '5.1. Apostas feitas só para cumprir o rollover sem risco real, padrões de aposta que cobrem quase todos os resultados, transferência de fundos entre contas e uso de várias contas são abuso de bônus.',
        '5.2. Havendo abuso, o :site pode cancelar o bônus e os ganhos vindos dele e pode encerrar a conta. O dinheiro real depositado por você é sempre devolvido.',
    ]],
    ['6. Decisões', [
        '6.1. Em caso de divergência sobre um bônus, os registros de transação do :site são o registro final.',
        '6.2. Se alguma regra daqui conflitar com os Termos de Serviço, valem os Termos de Serviço.',
    ]],
];

const es = [
    ['1. General', [
        '1.1. Un bono es una promoción que ofrece :site. No es dinero y no tiene valor hasta que se cumplan las condiciones de esta página.',
        '1.2. :site puede crear, cambiar, suspender o terminar cualquier promoción en cualquier momento. Un cambio nunca afecta a un bono ya concedido.',
        '1.3. Cuando una promoción tenga su propia página, las reglas de esa página se aplican junto con estas.',
    ]],
    ['2. Rollover', [
        '2.1. Un bono debe apostarse :rollover veces antes de que el bono y lo ganado con él puedan retirarse.',
        '2.2. Tu progreso aparece en el monedero. Hasta completar el rollover, la parte de bono del saldo no se puede retirar.',
        '2.3. Solo las apuestas reales en los juegos de este sitio cuentan para el rollover. Las rondas canceladas o anuladas no cuentan.',
    ]],
    ['3. Una por persona', [
        '3.1. Cada promoción puede usarse una vez por persona, domicilio, dispositivo, dirección IP y método de pago, salvo que la promoción diga otra cosa.',
        '3.2. Las cuentas de una misma persona se tratan como una sola. Las cuentas de más se cierran y sus bonos se retiran.',
    ]],
    ['4. Cómo se usa el saldo', [
        '4.1. Las apuestas salen primero del saldo real. El saldo de bono se usa cuando el saldo real se agota, salvo que la promoción diga otra cosa.',
        '4.2. Un retiro hecho antes de completar el rollover elimina el saldo de bono y lo ganado con él.',
    ]],
    ['5. Abuso', [
        '5.1. Las apuestas hechas solo para cumplir el rollover sin riesgo real, los patrones de apuesta que cubren casi todos los resultados, mover fondos entre cuentas y el uso de varias cuentas son abuso de bonos.',
        '5.2. Si se detecta abuso, :site puede cancelar el bono y las ganancias derivadas y puede cerrar la cuenta. El dinero real que depositaste se devuelve siempre.',
    ]],
    ['6. Decisiones', [
        '6.1. En una disputa sobre un bono, los registros de transacciones de :site son el registro final.',
        '6.2. Si alguna regla de aquí choca con los Términos del Servicio, prevalecen los Términos del Servicio.',
    ]],
];

const fr = [
    ['1. Généralités', [
        '1.1. Un bonus est une promotion proposée par :site. Ce n\'est pas de l\'argent et il n\'a aucune valeur tant que les conditions de cette page ne sont pas remplies.',
        '1.2. :site peut créer, modifier, suspendre ou arrêter une promotion à tout moment. Une modification ne touche jamais un bonus déjà accordé.',
        '1.3. Lorsqu\'une promotion a sa propre page, les règles de cette page s\'ajoutent à celles-ci.',
    ]],
    ['2. Rollover', [
        '2.1. Un bonus doit être misé :rollover fois avant que le bonus et ce qu\'il a rapporté puissent être retirés.',
        '2.2. Votre progression s\'affiche dans votre portefeuille. Tant que le rollover n\'est pas terminé, la partie bonus du solde ne peut pas être retirée.',
        '2.3. Seules les mises réelles sur les jeux de ce site comptent pour le rollover. Les tours annulés ne comptent pas.',
    ]],
    ['3. Un par personne', [
        '3.1. Une promotion peut être utilisée une fois par personne, foyer, appareil, adresse IP et moyen de paiement, sauf mention contraire.',
        '3.2. Les comptes appartenant à la même personne sont traités comme un seul. Les comptes en trop sont fermés et leurs bonus retirés.',
    ]],
    ['4. Utilisation du solde', [
        '4.1. Les mises sont prises d\'abord sur le solde réel. Le solde bonus sert une fois le solde réel épuisé, sauf mention contraire.',
        '4.2. Un retrait effectué avant la fin du rollover supprime le solde bonus et ce qu\'il a rapporté.',
    ]],
    ['5. Abus', [
        '5.1. Les mises placées uniquement pour libérer le rollover sans risque réel, les schémas de mise qui couvrent presque tous les résultats, les transferts de fonds entre comptes et l\'usage de plusieurs comptes sont des abus de bonus.',
        '5.2. En cas d\'abus, :site peut annuler le bonus et les gains qui en découlent et peut fermer le compte. L\'argent réel que vous avez déposé est toujours restitué.',
    ]],
    ['6. Décisions', [
        '6.1. En cas de litige sur un bonus, les enregistrements de transactions de :site font foi.',
        '6.2. Si une règle de cette page contredit les Conditions de service, ce sont les Conditions de service qui s\'appliquent.',
    ]],
];

const de = [
    ['1. Allgemeines', [
        '1.1. Ein Bonus ist eine Aktion von :site. Er ist kein Bargeld und hat keinen Wert, solange die Bedingungen auf dieser Seite nicht erfüllt sind.',
        '1.2. :site kann eine Aktion jederzeit einführen, ändern, aussetzen oder beenden. Eine Änderung betrifft nie einen bereits gewährten Bonus.',
        '1.3. Hat eine Aktion eine eigene Seite, gelten deren Regeln zusätzlich zu diesen.',
    ]],
    ['2. Umsatzbedingung', [
        '2.1. Ein Bonus muss :rollover Mal umgesetzt werden, bevor der Bonus und die damit erzielten Gewinne ausgezahlt werden können.',
        '2.2. Ihren Fortschritt sehen Sie in Ihrer Wallet. Bis die Umsatzbedingung erfüllt ist, kann der Bonusanteil des Guthabens nicht ausgezahlt werden.',
        '2.3. Nur echte Einsätze in den Spielen dieser Website zählen. Stornierte oder ungültige Runden zählen nicht.',
    ]],
    ['3. Einmal pro Person', [
        '3.1. Eine Aktion kann einmal pro Person, Haushalt, Gerät, IP-Adresse und Zahlungsmittel genutzt werden, sofern die Aktion nichts anderes sagt.',
        '3.2. Konten derselben Person gelten als ein Konto. Zusätzliche Konten werden geschlossen und ihre Boni entfernt.',
    ]],
    ['4. Verwendung des Guthabens', [
        '4.1. Einsätze werden zuerst vom echten Guthaben abgezogen. Das Bonusguthaben wird genutzt, wenn das echte Guthaben aufgebraucht ist, sofern die Aktion nichts anderes sagt.',
        '4.2. Eine Auszahlung vor Erfüllung der Umsatzbedingung entfernt das Bonusguthaben und die daraus erzielten Gewinne.',
    ]],
    ['5. Missbrauch', [
        '5.1. Einsätze, die nur die Umsatzbedingung ohne echtes Risiko erfüllen sollen, Einsatzmuster, die fast alle Ergebnisse abdecken, das Verschieben von Guthaben zwischen Konten und die Nutzung mehrerer Konten sind Bonusmissbrauch.',
        '5.2. Bei Missbrauch kann :site den Bonus und die daraus erzielten Gewinne streichen und das Konto schließen. Selbst eingezahltes echtes Geld wird immer zurückgegeben.',
    ]],
    ['6. Entscheidungen', [
        '6.1. Bei Streit über einen Bonus sind die Transaktionsaufzeichnungen von :site maßgeblich.',
        '6.2. Widerspricht eine Regel dieser Seite den Nutzungsbedingungen, gelten die Nutzungsbedingungen.',
    ]],
];

export default { en, pt_BR, es, fr, de };
