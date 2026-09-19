// Privacy policy per language. ":site" is replaced with the site name.
// Each section: [title, [paragraphs...]]

const en = [
    ['1. Who we are', [
        'This policy explains what personal data :site collects when you use this website, why we collect it, who we share it with and what you can ask us to do with it. It applies to the whole site, including the games and the cashier.',
    ]],
    ['2. What we collect', [
        '2.1. What you give us: the name, e-mail address, telephone number and date of birth you type when you register, the password you choose (stored only as a hash), and the wallet addresses you ask us to send withdrawals to.',
        '2.2. What we record while you play: your deposits, withdrawals, bets, wins, bonuses and the balance of your wallet.',
        '2.3. What your device tells us: the IP address you connect from, the browser and device you use, the language you pick and the times you log in.',
    ]],
    ['3. Why we use it', [
        '3.1. To open and run your account and to keep your balance correct.',
        '3.2. To credit deposits, to pay withdrawals and to answer questions about them.',
        '3.3. To keep the games fair and to detect fraud, bonus abuse, multiple accounts and play by anyone under 18.',
        '3.4. To meet duties the law places on us and to answer lawful requests from the authorities.',
    ]],
    ['4. Who we share it with', [
        '4.1. Our payment provider, NOWPayments, receives the amount and the coin of each deposit and withdrawal so it can move the funds. We never send it your password.',
        '4.2. The company that hosts the server stores the data on our behalf and may not use it for anything else.',
        '4.3. Authorities and courts, where the law requires it.',
        '4.4. We do not sell your personal data and we do not pass it to advertisers.',
    ]],
    ['5. Cookies and local storage', [
        '5.1. We use cookies and browser storage to keep you signed in, to remember the language you chose and to remember that you accepted this notice.',
        '5.2. You can delete them at any time in your browser settings. If you do, you will be signed out and some parts of the site will stop working.',
    ]],
    ['6. How long we keep it', [
        '6.1. We keep account and transaction data for as long as the account exists and afterwards for the period that the law and our payment provider require. After that it is deleted or made anonymous.',
    ]],
    ['7. Your rights', [
        '7.1. You can ask for a copy of the data we hold about you, ask us to correct anything that is wrong, or ask us to delete your account.',
        '7.2. Some data must be kept even after an account is closed, because the law requires it. We will tell you if that applies.',
        '7.3. To make any of these requests, contact support through the Support page on this site.',
    ]],
    ['8. How we protect it', [
        '8.1. The whole site is served over an encrypted connection. Passwords are stored as hashes and are never readable. Access to the administration area is limited to named accounts and protected against password guessing.',
        '8.2. No system is perfect. If a breach ever affects your data, we will tell you and the competent authority as the law requires.',
    ]],
    ['9. Children', [
        '9.1. This site is for adults. Nobody under 18 may open an account. If we find that an account belongs to a minor, we close it and delete the data.',
    ]],
    ['10. Changes to this policy', [
        '10.1. When this policy changes we publish the new version on this page. Continuing to use the site after that means you accept it.',
    ]],
];

const pt_BR = [
    ['1. Quem somos', [
        'Esta política explica quais dados pessoais o :site coleta quando você usa este site, por que os coletamos, com quem os compartilhamos e o que você pode pedir que façamos com eles. Vale para todo o site, incluindo os jogos e o caixa.',
    ]],
    ['2. O que coletamos', [
        '2.1. O que você nos dá: o nome, o e-mail, o telefone e a data de nascimento que você digita ao se cadastrar, a senha que você escolhe (guardada apenas como hash) e os endereços de carteira para os quais você pede saques.',
        '2.2. O que registramos enquanto você joga: seus depósitos, saques, apostas, ganhos, bônus e o saldo da sua carteira.',
        '2.3. O que seu aparelho informa: o endereço IP usado na conexão, o navegador e o aparelho, o idioma escolhido e os horários de acesso.',
    ]],
    ['3. Por que usamos', [
        '3.1. Para abrir e manter sua conta e manter seu saldo correto.',
        '3.2. Para creditar depósitos, pagar saques e responder dúvidas sobre eles.',
        '3.3. Para manter os jogos justos e detectar fraude, abuso de bônus, contas múltiplas e uso por menores de 18 anos.',
        '3.4. Para cumprir obrigações legais e responder a pedidos legítimos das autoridades.',
    ]],
    ['4. Com quem compartilhamos', [
        '4.1. Nosso processador de pagamentos, a NOWPayments, recebe o valor e a moeda de cada depósito e saque para movimentar os fundos. Nunca enviamos sua senha.',
        '4.2. A empresa que hospeda o servidor guarda os dados em nosso nome e não pode usá-los para outra finalidade.',
        '4.3. Autoridades e tribunais, quando a lei exigir.',
        '4.4. Não vendemos seus dados pessoais e não os repassamos a anunciantes.',
    ]],
    ['5. Cookies e armazenamento local', [
        '5.1. Usamos cookies e o armazenamento do navegador para manter você conectado, lembrar o idioma escolhido e lembrar que você aceitou este aviso.',
        '5.2. Você pode apagá-los quando quiser nas configurações do navegador. Se fizer isso, sua sessão será encerrada e partes do site deixarão de funcionar.',
    ]],
    ['6. Por quanto tempo guardamos', [
        '6.1. Guardamos dados de conta e de transações enquanto a conta existir e, depois disso, pelo prazo exigido pela lei e pelo nosso processador de pagamentos. Em seguida, os dados são apagados ou anonimizados.',
    ]],
    ['7. Seus direitos', [
        '7.1. Você pode pedir uma cópia dos dados que temos sobre você, pedir a correção do que estiver errado ou pedir a exclusão da sua conta.',
        '7.2. Alguns dados precisam ser mantidos mesmo depois do encerramento da conta, porque a lei exige. Avisaremos quando for o caso.',
        '7.3. Para qualquer um desses pedidos, fale com o suporte pela página de Suporte deste site.',
    ]],
    ['8. Como protegemos', [
        '8.1. Todo o site é servido por conexão criptografada. As senhas são guardadas como hash e nunca podem ser lidas. O acesso à área administrativa é limitado a contas nominais e protegido contra tentativas de adivinhação de senha.',
        '8.2. Nenhum sistema é perfeito. Se algum incidente afetar seus dados, avisaremos você e a autoridade competente conforme a lei exigir.',
    ]],
    ['9. Menores de idade', [
        '9.1. Este site é para adultos. Ninguém com menos de 18 anos pode abrir conta. Se identificarmos uma conta de menor, ela é encerrada e os dados são apagados.',
    ]],
    ['10. Mudanças nesta política', [
        '10.1. Quando esta política mudar, publicaremos a nova versão nesta página. Continuar usando o site depois disso significa que você aceita.',
    ]],
];

const es = [
    ['1. Quiénes somos', [
        'Esta política explica qué datos personales recoge :site cuando usas este sitio, por qué los recogemos, con quién los compartimos y qué puedes pedirnos que hagamos con ellos. Se aplica a todo el sitio, incluidos los juegos y la caja.',
    ]],
    ['2. Qué recogemos', [
        '2.1. Lo que nos das: el nombre, el correo electrónico, el teléfono y la fecha de nacimiento que escribes al registrarte, la contraseña que eliges (guardada solo como hash) y las direcciones de monedero a las que pides los retiros.',
        '2.2. Lo que registramos mientras juegas: tus depósitos, retiros, apuestas, premios, bonos y el saldo de tu monedero.',
        '2.3. Lo que dice tu dispositivo: la dirección IP desde la que te conectas, el navegador y el dispositivo, el idioma que eliges y las horas de acceso.',
    ]],
    ['3. Para qué los usamos', [
        '3.1. Para abrir y mantener tu cuenta y mantener tu saldo correcto.',
        '3.2. Para acreditar depósitos, pagar retiros y responder preguntas sobre ellos.',
        '3.3. Para mantener los juegos justos y detectar fraude, abuso de bonos, cuentas múltiples y el juego de menores de 18 años.',
        '3.4. Para cumplir con las obligaciones legales y responder a requerimientos lícitos de las autoridades.',
    ]],
    ['4. Con quién los compartimos', [
        '4.1. Nuestro proveedor de pagos, NOWPayments, recibe el importe y la moneda de cada depósito y retiro para mover los fondos. Nunca le enviamos tu contraseña.',
        '4.2. La empresa que aloja el servidor guarda los datos en nuestro nombre y no puede usarlos para otra cosa.',
        '4.3. Autoridades y tribunales, cuando la ley lo exija.',
        '4.4. No vendemos tus datos personales ni los cedemos a anunciantes.',
    ]],
    ['5. Cookies y almacenamiento local', [
        '5.1. Usamos cookies y el almacenamiento del navegador para mantener tu sesión, recordar el idioma que elegiste y recordar que aceptaste este aviso.',
        '5.2. Puedes borrarlos cuando quieras en los ajustes del navegador. Si lo haces, se cerrará tu sesión y algunas partes del sitio dejarán de funcionar.',
    ]],
    ['6. Cuánto tiempo los guardamos', [
        '6.1. Guardamos los datos de cuenta y de transacciones mientras la cuenta exista y después durante el plazo que exijan la ley y nuestro proveedor de pagos. Pasado ese plazo se borran o se anonimizan.',
    ]],
    ['7. Tus derechos', [
        '7.1. Puedes pedir una copia de los datos que tenemos sobre ti, pedir que corrijamos lo que esté mal o pedir que borremos tu cuenta.',
        '7.2. Algunos datos deben conservarse incluso después de cerrar la cuenta porque la ley lo exige. Te lo diremos cuando sea el caso.',
        '7.3. Para cualquiera de estas peticiones, escribe al soporte desde la página de Soporte de este sitio.',
    ]],
    ['8. Cómo los protegemos', [
        '8.1. Todo el sitio se sirve por conexión cifrada. Las contraseñas se guardan como hash y nunca son legibles. El acceso al área de administración está limitado a cuentas nominales y protegido frente a intentos de adivinar contraseñas.',
        '8.2. Ningún sistema es perfecto. Si alguna brecha afecta a tus datos, te lo comunicaremos a ti y a la autoridad competente según exija la ley.',
    ]],
    ['9. Menores', [
        '9.1. Este sitio es para adultos. Nadie menor de 18 años puede abrir una cuenta. Si detectamos la cuenta de un menor, la cerramos y borramos los datos.',
    ]],
    ['10. Cambios en esta política', [
        '10.1. Cuando esta política cambie publicaremos la nueva versión en esta página. Seguir usando el sitio después significa que la aceptas.',
    ]],
];

const fr = [
    ['1. Qui nous sommes', [
        'Cette politique explique quelles données personnelles :site collecte lorsque vous utilisez ce site, pourquoi nous les collectons, avec qui nous les partageons et ce que vous pouvez nous demander d\'en faire. Elle vaut pour tout le site, y compris les jeux et la caisse.',
    ]],
    ['2. Ce que nous collectons', [
        '2.1. Ce que vous nous donnez : le nom, l\'adresse e-mail, le numéro de téléphone et la date de naissance saisis à l\'inscription, le mot de passe que vous choisissez (conservé seulement sous forme de hachage) et les adresses de portefeuille vers lesquelles vous demandez vos retraits.',
        '2.2. Ce que nous enregistrons pendant que vous jouez : vos dépôts, retraits, mises, gains, bonus et le solde de votre portefeuille.',
        '2.3. Ce que votre appareil indique : l\'adresse IP utilisée, le navigateur et l\'appareil, la langue choisie et les heures de connexion.',
    ]],
    ['3. Pourquoi nous les utilisons', [
        '3.1. Pour ouvrir et gérer votre compte et tenir votre solde à jour.',
        '3.2. Pour créditer les dépôts, payer les retraits et répondre aux questions à leur sujet.',
        '3.3. Pour garder les jeux équitables et détecter la fraude, l\'abus de bonus, les comptes multiples et le jeu des moins de 18 ans.',
        '3.4. Pour respecter nos obligations légales et répondre aux demandes légitimes des autorités.',
    ]],
    ['4. Avec qui nous les partageons', [
        '4.1. Notre prestataire de paiement, NOWPayments, reçoit le montant et la monnaie de chaque dépôt et retrait afin de déplacer les fonds. Nous ne lui transmettons jamais votre mot de passe.',
        '4.2. L\'hébergeur du serveur conserve les données pour notre compte et ne peut pas les utiliser autrement.',
        '4.3. Les autorités et les tribunaux, lorsque la loi l\'exige.',
        '4.4. Nous ne vendons pas vos données personnelles et ne les transmettons pas à des annonceurs.',
    ]],
    ['5. Cookies et stockage local', [
        '5.1. Nous utilisons des cookies et le stockage du navigateur pour vous garder connecté, retenir la langue choisie et retenir que vous avez accepté cet avis.',
        '5.2. Vous pouvez les supprimer à tout moment dans les réglages du navigateur. Vous serez alors déconnecté et certaines parties du site cesseront de fonctionner.',
    ]],
    ['6. Combien de temps nous les gardons', [
        '6.1. Nous conservons les données de compte et de transactions tant que le compte existe, puis pendant la durée exigée par la loi et par notre prestataire de paiement. Ensuite elles sont supprimées ou anonymisées.',
    ]],
    ['7. Vos droits', [
        '7.1. Vous pouvez demander une copie des données que nous détenons sur vous, demander la correction de ce qui est inexact ou demander la suppression de votre compte.',
        '7.2. Certaines données doivent être conservées même après la fermeture du compte parce que la loi l\'impose. Nous vous le dirons le cas échéant.',
        '7.3. Pour toute demande de ce type, contactez le support depuis la page Support de ce site.',
    ]],
    ['8. Comment nous les protégeons', [
        '8.1. Tout le site est servi via une connexion chiffrée. Les mots de passe sont stockés sous forme de hachage et ne sont jamais lisibles. L\'accès à l\'administration est limité à des comptes nominatifs et protégé contre les tentatives de devinette de mot de passe.',
        '8.2. Aucun système n\'est parfait. Si une faille touchait vos données, nous vous en informerions ainsi que l\'autorité compétente, comme la loi l\'exige.',
    ]],
    ['9. Mineurs', [
        '9.1. Ce site est réservé aux adultes. Personne de moins de 18 ans ne peut ouvrir un compte. Si nous constatons qu\'un compte appartient à un mineur, nous le fermons et supprimons les données.',
    ]],
    ['10. Modifications de cette politique', [
        '10.1. Lorsque cette politique change, nous publions la nouvelle version sur cette page. Continuer à utiliser le site vaut acceptation.',
    ]],
];

const de = [
    ['1. Wer wir sind', [
        'Diese Erklärung beschreibt, welche personenbezogenen Daten :site erhebt, wenn Sie diese Website nutzen, warum wir sie erheben, an wen wir sie weitergeben und was Sie von uns verlangen können. Sie gilt für die gesamte Website einschließlich der Spiele und der Kasse.',
    ]],
    ['2. Was wir erheben', [
        '2.1. Was Sie uns geben: Name, E-Mail-Adresse, Telefonnummer und Geburtsdatum aus der Registrierung, das von Ihnen gewählte Passwort (nur als Hash gespeichert) und die Wallet-Adressen, an die Sie Auszahlungen wünschen.',
        '2.2. Was wir während des Spiels festhalten: Ihre Einzahlungen, Auszahlungen, Einsätze, Gewinne, Boni und den Stand Ihrer Wallet.',
        '2.3. Was Ihr Gerät mitteilt: die IP-Adresse der Verbindung, Browser und Gerät, die gewählte Sprache und die Anmeldezeiten.',
    ]],
    ['3. Wozu wir sie nutzen', [
        '3.1. Um Ihr Konto zu führen und Ihren Kontostand korrekt zu halten.',
        '3.2. Um Einzahlungen gutzuschreiben, Auszahlungen zu leisten und Fragen dazu zu beantworten.',
        '3.3. Um die Spiele fair zu halten und Betrug, Bonusmissbrauch, Mehrfachkonten und Spiel von Personen unter 18 zu erkennen.',
        '3.4. Um gesetzliche Pflichten zu erfüllen und rechtmäßige Auskunftsersuchen von Behörden zu beantworten.',
    ]],
    ['4. An wen wir sie weitergeben', [
        '4.1. Unser Zahlungsdienstleister NOWPayments erhält Betrag und Währung jeder Ein- und Auszahlung, um die Mittel zu bewegen. Ihr Passwort geben wir niemals weiter.',
        '4.2. Das Unternehmen, das den Server betreibt, speichert die Daten in unserem Auftrag und darf sie zu nichts anderem verwenden.',
        '4.3. Behörden und Gerichte, soweit das Gesetz es verlangt.',
        '4.4. Wir verkaufen Ihre personenbezogenen Daten nicht und geben sie nicht an Werbetreibende weiter.',
    ]],
    ['5. Cookies und lokaler Speicher', [
        '5.1. Wir verwenden Cookies und den Browserspeicher, um Sie angemeldet zu halten, Ihre Sprachwahl zu merken und zu merken, dass Sie diesen Hinweis akzeptiert haben.',
        '5.2. Sie können sie jederzeit in den Browsereinstellungen löschen. Dann werden Sie abgemeldet und Teile der Website funktionieren nicht mehr.',
    ]],
    ['6. Wie lange wir sie speichern', [
        '6.1. Konto- und Transaktionsdaten speichern wir, solange das Konto besteht, und danach für den Zeitraum, den das Gesetz und unser Zahlungsdienstleister verlangen. Danach werden sie gelöscht oder anonymisiert.',
    ]],
    ['7. Ihre Rechte', [
        '7.1. Sie können eine Kopie der über Sie gespeicherten Daten verlangen, die Berichtigung falscher Angaben verlangen oder die Löschung Ihres Kontos verlangen.',
        '7.2. Manche Daten müssen auch nach der Schließung eines Kontos aufbewahrt werden, weil das Gesetz es verlangt. Wir sagen Ihnen, wenn das zutrifft.',
        '7.3. Für solche Anfragen wenden Sie sich über die Support-Seite dieser Website an den Support.',
    ]],
    ['8. Wie wir sie schützen', [
        '8.1. Die gesamte Website wird über eine verschlüsselte Verbindung ausgeliefert. Passwörter werden als Hash gespeichert und sind nie lesbar. Der Zugang zur Verwaltung ist auf benannte Konten beschränkt und gegen das Erraten von Passwörtern geschützt.',
        '8.2. Kein System ist perfekt. Sollte eine Verletzung Ihre Daten betreffen, informieren wir Sie und die zuständige Behörde, wie es das Gesetz verlangt.',
    ]],
    ['9. Minderjährige', [
        '9.1. Diese Website ist für Erwachsene. Niemand unter 18 Jahren darf ein Konto eröffnen. Stellen wir fest, dass ein Konto einem Minderjährigen gehört, schließen wir es und löschen die Daten.',
    ]],
    ['10. Änderungen dieser Erklärung', [
        '10.1. Ändert sich diese Erklärung, veröffentlichen wir die neue Fassung auf dieser Seite. Die weitere Nutzung der Website gilt als Zustimmung.',
    ]],
];

export default { en, pt_BR, es, fr, de };
