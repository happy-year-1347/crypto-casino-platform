// The Responsible Gambling Policy referred to in section 16 of the Welcome Bonus
// terms. ":site" is the site name, ":min" the qualifying deposit.
// Each section: [title, [paragraphs...]]

const en = [
    ['1. Our commitment', [
        'Playing at :site should stay entertainment. Most people play for fun and stop when they mean to; for a few it stops being a choice. This policy explains how we try to keep play safe and what you can ask us to do.',
        'We would rather lose a customer than keep one who is being harmed.',
    ]],
    ['2. Adults only', [
        'You must be at least 18 years old, or the legal age in your country if that is higher, to open an account or to play.',
        'We check age during verification. An account opened by anyone underage is closed, any winnings are void and deposits are returned.',
    ]],
    ['3. Keeping it under control', [
        'Decide before you start how much you are willing to lose, and treat that money as the price of the entertainment.',
        'Play with money you can spare, never with money meant for rent, food, bills or debts.',
        'Set yourself a time as well as an amount, and take breaks.',
        'Losses are part of the game; chasing them with bigger bets is the fastest way to lose more.',
        'Never play to make money, to escape stress or while drunk or upset.',
    ]],
    ['4. Questions worth asking yourself', [
        'Do you play longer, or for more, than you meant to?',
        'Have you gone back to win losses back?',
        'Have you borrowed money, sold something or left a bill unpaid to play?',
        'Do you hide how much you play from people close to you?',
        'Do you feel restless or bad tempered when you try to cut down?',
        'Has playing cost you sleep, work or a relationship?',
        'If you answer yes to any of these, please use the tools below or talk to one of the organisations in section 9.',
    ]],
    ['5. Deposit limits', [
        'You can ask us to cap how much you are able to deposit per day, per week or per month.',
        'A lower limit takes effect at once. A higher limit, or removing one, takes effect 24 hours after you ask, so the decision is never made in the heat of the moment.',
        'Write to support with the limit you want and we will set it on your account.',
    ]],
    ['6. Taking a break', [
        'You can ask for a cooling-off period of 24 hours, 7 days or 30 days. During it you can sign in to see your account but you cannot deposit or play.',
        'The break starts as soon as we receive the request and cannot be shortened once it has started.',
    ]],
    ['7. Self-exclusion', [
        'You can ask to be excluded for 6 months, 1 year, 5 years or permanently.',
        'While excluded, your account is locked, you cannot deposit or play, and we stop sending you promotional messages. Any real-money balance is paid out to you, and open bonuses are forfeited.',
        'A self-exclusion cannot be lifted before it ends. When it ends, the account stays closed until you ask us to reopen it, and a permanent exclusion is never reopened.',
        'Do not open a second account during an exclusion. If you do, it is closed and winnings from it are void.',
    ]],
    ['8. Bonuses and promotions', [
        'A welcome bonus is optional. You may decline it, and you may ask us not to send you promotional offers at all.',
        'While a bonus is running there is a maximum bet, so a bonus can never push you into stakes far larger than your normal ones.',
        'We do not send promotions to anyone who is self-excluded or on a cooling-off break.',
    ]],
    ['9. Where to get help', [
        'These organisations are independent of :site, free and confidential.',
        'GamCare (English, worldwide): gamcare.org.uk',
        'BeGambleAware: begambleaware.org',
        'Gamblers Anonymous (worldwide meetings): gamblersanonymous.org',
        'Gambling Therapy (online support in many languages): gamblingtherapy.org',
        'Jogadores Anonimos (Brazil): jogadoresanonimos.com.br',
    ]],
    ['10. Protecting children', [
        'If you share a device, sign out when you have finished and never save the password where a child can use it.',
        'Filtering software such as Gamban, Net Nanny or Qustodio can block gambling sites on a shared computer or phone.',
    ]],
    ['11. Contact', [
        'To set a limit, take a break or self-exclude, write to us and say clearly what you want and for how long:',
        'E-mail: support@vpcasino.net',
        'Website: vpcasino.net',
        'We act on these requests before anything else.',
    ]],
];

const pt_BR = [
    ['1. Nosso compromisso', [
        'Jogar no :site deve continuar sendo diversão. A maioria das pessoas joga por prazer e para quando pretende; para algumas, deixa de ser uma escolha. Esta política explica como tentamos manter o jogo seguro e o que você pode nos pedir.',
        'Preferimos perder um cliente a manter alguém que está sendo prejudicado.',
    ]],
    ['2. Somente maiores de idade', [
        'Você precisa ter pelo menos 18 anos, ou a idade legal do seu país se for maior, para abrir conta ou jogar.',
        'Conferimos a idade na verificação. Conta aberta por menor de idade é encerrada, os ganhos ficam sem efeito e os depósitos são devolvidos.',
    ]],
    ['3. Mantendo sob controle', [
        'Decida antes de começar quanto está disposto a perder e trate esse dinheiro como o preço da diversão.',
        'Jogue com dinheiro que sobra, nunca com dinheiro do aluguel, da comida, das contas ou de dívidas.',
        'Defina também um tempo, além do valor, e faça pausas.',
        'Perdas fazem parte do jogo; tentar recuperá-las com apostas maiores é a forma mais rápida de perder mais.',
        'Nunca jogue para ganhar dinheiro, para fugir do estresse ou depois de beber ou quando estiver abalado.',
    ]],
    ['4. Perguntas que vale a pena se fazer', [
        'Você joga por mais tempo, ou com mais dinheiro, do que pretendia?',
        'Já voltou para tentar recuperar o que perdeu?',
        'Já pegou dinheiro emprestado, vendeu algo ou deixou uma conta sem pagar para jogar?',
        'Esconde de quem é próximo o quanto joga?',
        'Fica inquieto ou irritado quando tenta diminuir?',
        'O jogo já lhe custou sono, trabalho ou um relacionamento?',
        'Se respondeu sim a qualquer uma delas, use as ferramentas abaixo ou fale com uma das organizações da seção 9.',
    ]],
    ['5. Limites de depósito', [
        'Você pode nos pedir para limitar quanto consegue depositar por dia, por semana ou por mês.',
        'Um limite menor vale imediatamente. Um limite maior, ou a remoção de um limite, só vale 24 horas depois do pedido, para que a decisão nunca seja tomada no calor do momento.',
        'Escreva ao suporte com o limite desejado e nós o aplicamos na sua conta.',
    ]],
    ['6. Fazendo uma pausa', [
        'Você pode pedir uma pausa de 24 horas, 7 dias ou 30 dias. Durante ela você entra na conta para vê-la, mas não pode depositar nem jogar.',
        'A pausa começa assim que recebemos o pedido e não pode ser encurtada depois de iniciada.',
    ]],
    ['7. Autoexclusão', [
        'Você pode pedir exclusão por 6 meses, 1 ano, 5 anos ou definitiva.',
        'Durante a exclusão a conta fica bloqueada, você não pode depositar nem jogar, e paramos de enviar mensagens promocionais. O saldo em dinheiro real é pago a você e os bônus em aberto são perdidos.',
        'Uma autoexclusão não pode ser suspensa antes do prazo. Quando termina, a conta segue fechada até você pedir a reabertura, e a exclusão definitiva nunca é reaberta.',
        'Não abra uma segunda conta durante a exclusão. Se abrir, ela é encerrada e os ganhos ficam sem efeito.',
    ]],
    ['8. Bônus e promoções', [
        'O bônus de boas-vindas é opcional. Você pode recusá-lo e pode pedir para não receber ofertas promocionais.',
        'Enquanto um bônus está ativo existe uma aposta máxima, de modo que um bônus nunca empurra você para apostas muito maiores que as suas normais.',
        'Não enviamos promoções a quem está autoexcluído ou em pausa.',
    ]],
    ['9. Onde buscar ajuda', [
        'Estas organizações são independentes do :site, gratuitas e confidenciais.',
        'Jogadores Anônimos (Brasil): jogadoresanonimos.com.br',
        'Gambling Therapy (apoio online em vários idiomas): gamblingtherapy.org',
        'Gamblers Anonymous (reuniões no mundo todo): gamblersanonymous.org',
        'GamCare (inglês): gamcare.org.uk',
        'BeGambleAware: begambleaware.org',
    ]],
    ['10. Protegendo crianças', [
        'Se compartilha o aparelho, saia da conta ao terminar e nunca salve a senha onde uma criança possa usá-la.',
        'Programas de filtro como Gamban, Net Nanny ou Qustodio bloqueiam sites de jogo em um computador ou celular compartilhado.',
    ]],
    ['11. Contato', [
        'Para definir um limite, fazer uma pausa ou se autoexcluir, escreva para nós dizendo claramente o que quer e por quanto tempo:',
        'E-mail: support@vpcasino.net',
        'Site: vpcasino.net',
        'Tratamos esses pedidos antes de qualquer outro.',
    ]],
];

const es = [
    ['1. Nuestro compromiso', [
        'Jugar en :site debe seguir siendo entretenimiento. La mayoría juega por diversión y para cuando quiere; para unos pocos deja de ser una elección. Esta política explica cómo intentamos que el juego sea seguro y qué puedes pedirnos.',
        'Preferimos perder un cliente a conservar a alguien a quien el juego está dañando.',
    ]],
    ['2. Solo mayores de edad', [
        'Debes tener al menos 18 años, o la edad legal de tu país si es mayor, para abrir una cuenta o jugar.',
        'Comprobamos la edad en la verificación. Una cuenta abierta por un menor se cierra, las ganancias quedan anuladas y los depósitos se devuelven.',
    ]],
    ['3. Mantenerlo bajo control', [
        'Decide antes de empezar cuánto estás dispuesto a perder y trata ese dinero como el precio del entretenimiento.',
        'Juega con dinero que te sobre, nunca con el del alquiler, la comida, las facturas o las deudas.',
        'Ponte también un tiempo, además de una cantidad, y haz pausas.',
        'Las pérdidas son parte del juego; perseguirlas con apuestas mayores es la forma más rápida de perder más.',
        'No juegues nunca para ganar dinero, para escapar del estrés ni tras beber o estando alterado.',
    ]],
    ['4. Preguntas que conviene hacerse', [
        '¿Juegas más tiempo, o más dinero, del que pensabas?',
        '¿Has vuelto para recuperar lo perdido?',
        '¿Has pedido prestado, vendido algo o dejado una factura sin pagar para jugar?',
        '¿Ocultas cuánto juegas a las personas cercanas?',
        '¿Te sientes inquieto o irritable cuando intentas reducir?',
        '¿El juego te ha costado sueño, trabajo o una relación?',
        'Si respondes que sí a alguna, usa las herramientas de abajo o habla con una de las organizaciones de la sección 9.',
    ]],
    ['5. Límites de depósito', [
        'Puedes pedirnos que limitemos cuánto puedes depositar por día, por semana o por mes.',
        'Un límite menor se aplica de inmediato. Uno mayor, o retirarlo, se aplica 24 horas después de pedirlo, para que la decisión no se tome en caliente.',
        'Escribe a soporte con el límite que quieras y lo fijamos en tu cuenta.',
    ]],
    ['6. Tomarse un descanso', [
        'Puedes pedir un descanso de 24 horas, 7 días o 30 días. Durante ese tiempo puedes entrar para ver la cuenta, pero no depositar ni jugar.',
        'El descanso empieza en cuanto recibimos la petición y no puede acortarse una vez iniciado.',
    ]],
    ['7. Autoexclusión', [
        'Puedes pedir la exclusión por 6 meses, 1 año, 5 años o de forma permanente.',
        'Durante la exclusión la cuenta queda bloqueada, no puedes depositar ni jugar y dejamos de enviarte mensajes promocionales. El saldo en dinero real se te paga y los bonos abiertos se pierden.',
        'Una autoexclusión no puede levantarse antes de tiempo. Al terminar, la cuenta sigue cerrada hasta que pidas reabrirla, y una exclusión permanente no se reabre nunca.',
        'No abras una segunda cuenta durante la exclusión. Si lo haces, se cierra y sus ganancias quedan anuladas.',
    ]],
    ['8. Bonos y promociones', [
        'El bono de bienvenida es opcional. Puedes rechazarlo y puedes pedirnos que no te enviemos ofertas.',
        'Mientras un bono está activo hay una apuesta máxima, de modo que un bono nunca te empuja a apuestas mucho mayores que las habituales.',
        'No enviamos promociones a quien está autoexcluido o en pausa.',
    ]],
    ['9. Dónde pedir ayuda', [
        'Estas organizaciones son independientes de :site, gratuitas y confidenciales.',
        'Gambling Therapy (apoyo en línea en varios idiomas): gamblingtherapy.org',
        'Jugadores Anónimos (España): jugadoresanonimos.org',
        'Gamblers Anonymous (reuniones en todo el mundo): gamblersanonymous.org',
        'GamCare (inglés): gamcare.org.uk',
        'BeGambleAware: begambleaware.org',
    ]],
    ['10. Proteger a los menores', [
        'Si compartes el dispositivo, cierra la sesión al terminar y no guardes la contraseña donde un menor pueda usarla.',
        'Programas de filtrado como Gamban, Net Nanny o Qustodio bloquean sitios de juego en un ordenador o teléfono compartido.',
    ]],
    ['11. Contacto', [
        'Para fijar un límite, tomarte un descanso o autoexcluirte, escríbenos diciendo claramente qué quieres y por cuánto tiempo:',
        'Correo: support@vpcasino.net',
        'Sitio web: vpcasino.net',
        'Atendemos estas peticiones antes que cualquier otra.',
    ]],
];

const fr = [
    ['1. Notre engagement', [
        "Jouer sur :site doit rester un divertissement. La plupart des gens jouent pour le plaisir et s'arrêtent quand ils le décident ; pour quelques-uns, ce n'est plus un choix. Cette politique explique comment nous essayons de garder le jeu sûr et ce que vous pouvez nous demander.",
        'Nous préférons perdre un client plutôt que garder une personne que le jeu met en difficulté.',
    ]],
    ['2. Réservé aux adultes', [
        "Vous devez avoir au moins 18 ans, ou l'âge légal de votre pays s'il est supérieur, pour ouvrir un compte ou jouer.",
        "Nous vérifions l'âge lors de la vérification. Un compte ouvert par un mineur est fermé, les gains sont annulés et les dépôts sont restitués.",
    ]],
    ['3. Garder la maîtrise', [
        'Décidez avant de commencer combien vous acceptez de perdre et considérez cette somme comme le prix du divertissement.',
        "Jouez avec de l'argent dont vous pouvez vous passer, jamais avec celui du loyer, des courses, des factures ou des dettes.",
        'Fixez-vous aussi une durée, pas seulement un montant, et faites des pauses.',
        'Les pertes font partie du jeu ; les poursuivre avec des mises plus grosses est le moyen le plus rapide de perdre davantage.',
        "Ne jouez jamais pour gagner de l'argent, pour fuir le stress, ni après avoir bu ou en étant contrarié.",
    ]],
    ['4. Questions à se poser', [
        'Jouez-vous plus longtemps, ou plus gros, que prévu ?',
        'Êtes-vous revenu pour récupérer vos pertes ?',
        'Avez-vous emprunté, vendu quelque chose ou laissé une facture impayée pour jouer ?',
        'Cachez-vous à vos proches combien vous jouez ?',
        'Vous sentez-vous agité ou irritable quand vous essayez de réduire ?',
        'Le jeu vous a-t-il coûté du sommeil, du travail ou une relation ?',
        'Si vous répondez oui à l’une de ces questions, utilisez les outils ci-dessous ou contactez un organisme de la section 9.',
    ]],
    ['5. Limites de dépôt', [
        'Vous pouvez nous demander de plafonner ce que vous pouvez déposer par jour, par semaine ou par mois.',
        "Une limite plus basse s'applique immédiatement. Une limite plus haute, ou sa suppression, ne s'applique que 24 heures après la demande, afin que la décision ne soit jamais prise sur le coup.",
        'Écrivez au support avec la limite souhaitée et nous la posons sur votre compte.',
    ]],
    ['6. Faire une pause', [
        "Vous pouvez demander une pause de 24 heures, 7 jours ou 30 jours. Pendant ce temps, vous pouvez consulter votre compte mais ni déposer ni jouer.",
        "La pause démarre dès réception de la demande et ne peut pas être raccourcie une fois commencée.",
    ]],
    ['7. Auto-exclusion', [
        'Vous pouvez demander une exclusion de 6 mois, 1 an, 5 ans ou définitive.',
        "Pendant l'exclusion, le compte est bloqué, vous ne pouvez ni déposer ni jouer, et nous cessons tout message promotionnel. Le solde en argent réel vous est versé et les bonus en cours sont perdus.",
        "Une auto-exclusion ne peut pas être levée avant son terme. À la fin, le compte reste fermé jusqu'à ce que vous demandiez sa réouverture ; une exclusion définitive n'est jamais rouverte.",
        "N'ouvrez pas de second compte pendant une exclusion. Le cas échéant, il est fermé et ses gains sont annulés.",
    ]],
    ['8. Bonus et promotions', [
        'Le bonus de bienvenue est facultatif. Vous pouvez le refuser et demander à ne recevoir aucune offre.',
        "Pendant un bonus, une mise maximale s'applique, si bien qu'un bonus ne vous pousse jamais à des mises très supérieures aux vôtres.",
        "Nous n'envoyons pas de promotions aux personnes auto-exclues ou en pause.",
    ]],
    ['9. Où trouver de l’aide', [
        'Ces organismes sont indépendants de :site, gratuits et confidentiels.',
        'Gambling Therapy (soutien en ligne en plusieurs langues) : gamblingtherapy.org',
        'Joueurs Anonymes (France) : joueurs-anonymes.org',
        'Gamblers Anonymous (réunions dans le monde entier) : gamblersanonymous.org',
        'GamCare (anglais) : gamcare.org.uk',
        'BeGambleAware : begambleaware.org',
    ]],
    ['10. Protéger les enfants', [
        "Si vous partagez un appareil, déconnectez-vous en partant et n'enregistrez jamais le mot de passe là où un enfant pourrait s'en servir.",
        'Des logiciels de filtrage comme Gamban, Net Nanny ou Qustodio bloquent les sites de jeu sur un ordinateur ou un téléphone partagé.',
    ]],
    ['11. Contact', [
        'Pour poser une limite, faire une pause ou vous auto-exclure, écrivez-nous en indiquant clairement ce que vous voulez et pour combien de temps :',
        'E-mail : support@vpcasino.net',
        'Site : vpcasino.net',
        'Nous traitons ces demandes en priorité.',
    ]],
];

const de = [
    ['1. Unser Anspruch', [
        'Spielen bei :site soll Unterhaltung bleiben. Die meisten spielen zum Vergnügen und hören auf, wenn sie es wollen; für wenige ist es keine Wahl mehr. Diese Richtlinie erklärt, wie wir das Spiel sicher halten und worum Sie uns bitten können.',
        'Wir verlieren lieber einen Kunden, als jemanden zu behalten, dem das Spiel schadet.',
    ]],
    ['2. Nur für Erwachsene', [
        'Sie müssen mindestens 18 Jahre alt sein, oder das gesetzliche Alter Ihres Landes, wenn es höher liegt, um ein Konto zu eröffnen oder zu spielen.',
        'Das Alter prüfen wir bei der Verifizierung. Ein von Minderjährigen eröffnetes Konto wird geschlossen, Gewinne verfallen und Einzahlungen werden zurückgezahlt.',
    ]],
    ['3. Die Kontrolle behalten', [
        'Legen Sie vor dem Start fest, wie viel Sie verlieren wollen, und betrachten Sie dieses Geld als Preis der Unterhaltung.',
        'Spielen Sie mit Geld, das Sie übrig haben, nie mit Geld für Miete, Essen, Rechnungen oder Schulden.',
        'Setzen Sie sich neben dem Betrag auch eine Zeit und machen Sie Pausen.',
        'Verluste gehören dazu; sie mit höheren Einsätzen zurückholen zu wollen, ist der schnellste Weg, mehr zu verlieren.',
        'Spielen Sie nie, um Geld zu verdienen, um Stress zu entkommen oder wenn Sie getrunken haben oder aufgewühlt sind.',
    ]],
    ['4. Fragen, die Sie sich stellen sollten', [
        'Spielen Sie länger oder höher, als Sie wollten?',
        'Sind Sie zurückgekehrt, um Verluste auszugleichen?',
        'Haben Sie Geld geliehen, etwas verkauft oder eine Rechnung offen gelassen, um zu spielen?',
        'Verheimlichen Sie nahestehenden Menschen, wie viel Sie spielen?',
        'Sind Sie unruhig oder gereizt, wenn Sie einschränken wollen?',
        'Hat das Spiel Sie Schlaf, Arbeit oder eine Beziehung gekostet?',
        'Wenn Sie eine Frage mit Ja beantworten, nutzen Sie bitte die Mittel unten oder wenden Sie sich an eine Stelle aus Abschnitt 9.',
    ]],
    ['5. Einzahlungslimits', [
        'Sie können uns bitten, Ihre Einzahlungen pro Tag, Woche oder Monat zu begrenzen.',
        'Ein niedrigeres Limit gilt sofort. Ein höheres Limit oder dessen Aufhebung gilt erst 24 Stunden nach Ihrer Bitte, damit die Entscheidung nie im Moment fällt.',
        'Schreiben Sie dem Support das gewünschte Limit, wir setzen es auf Ihrem Konto.',
    ]],
    ['6. Eine Pause einlegen', [
        'Sie können eine Pause von 24 Stunden, 7 Tagen oder 30 Tagen verlangen. In dieser Zeit können Sie Ihr Konto ansehen, aber nicht einzahlen oder spielen.',
        'Die Pause beginnt mit Eingang der Bitte und kann danach nicht verkürzt werden.',
    ]],
    ['7. Selbstsperre', [
        'Sie können eine Sperre für 6 Monate, 1 Jahr, 5 Jahre oder dauerhaft verlangen.',
        'Während der Sperre ist das Konto blockiert, Sie können weder einzahlen noch spielen, und wir senden keine Werbung mehr. Echtgeldguthaben wird ausgezahlt, laufende Boni verfallen.',
        'Eine Selbstsperre kann nicht vorzeitig aufgehoben werden. Danach bleibt das Konto geschlossen, bis Sie die Wiedereröffnung verlangen; eine dauerhafte Sperre wird nie aufgehoben.',
        'Eröffnen Sie während einer Sperre kein zweites Konto. Andernfalls wird es geschlossen und die Gewinne verfallen.',
    ]],
    ['8. Boni und Werbung', [
        'Der Willkommensbonus ist freiwillig. Sie können ihn ablehnen und ganz auf Werbeangebote verzichten.',
        'Solange ein Bonus läuft, gilt ein Höchsteinsatz, damit ein Bonus Sie nie zu Einsätzen weit über Ihren gewohnten drängt.',
        'An gesperrte oder pausierende Konten senden wir keine Werbung.',
    ]],
    ['9. Wo Sie Hilfe finden', [
        'Diese Stellen sind von :site unabhängig, kostenlos und vertraulich.',
        'Bundeszentrale für gesundheitliche Aufklärung (Deutschland): check-dein-spiel.de',
        'Gambling Therapy (Onlinehilfe in vielen Sprachen): gamblingtherapy.org',
        'Anonyme Spieler (Treffen weltweit): gamblersanonymous.org',
        'GamCare (englisch): gamcare.org.uk',
        'BeGambleAware: begambleaware.org',
    ]],
    ['10. Kinder schützen', [
        'Wenn Sie ein Gerät teilen, melden Sie sich nach dem Spielen ab und speichern Sie das Passwort nie dort, wo ein Kind es nutzen kann.',
        'Filterprogramme wie Gamban, Net Nanny oder Qustodio sperren Glücksspielseiten auf einem gemeinsam genutzten Rechner oder Telefon.',
    ]],
    ['11. Kontakt', [
        'Für ein Limit, eine Pause oder eine Selbstsperre schreiben Sie uns und sagen Sie klar, was Sie möchten und für wie lange:',
        'E-Mail: support@vpcasino.net',
        'Website: vpcasino.net',
        'Solche Bitten bearbeiten wir vor allem anderen.',
    ]],
];

export default { en, pt_BR, es, fr, de };
