// The restricted-country list that section 14 of the Welcome Bonus terms says
// must be shown separately on the site. ":site" is replaced with the site name.
// Each section: [title, [paragraphs...]]

const en = [
    ['1. Why this list exists', [
        'Online gaming is not permitted everywhere, and some countries are covered by international sanctions. :site therefore cannot open accounts for, or accept deposits from, people who live in or connect from the places listed below.',
        'The list is part of our terms. Registering an account means you confirm that you are not in one of these places.',
    ]],
    ['2. Countries and territories we cannot accept', [
        'United States of America, including all its states and territories.',
        'United Kingdom of Great Britain and Northern Ireland.',
        'France, together with its overseas departments and territories.',
        'Netherlands, Curacao, Aruba, Bonaire, Sint Maarten, Sint Eustatius and Saba.',
        'Australia.',
        'Spain.',
        'Israel.',
        'Ontario, Canada.',
        'Hong Kong Special Administrative Region.',
        'Singapore.',
    ]],
    ['3. Sanctioned jurisdictions', [
        'We also cannot serve anyone in a country or region subject to international financial sanctions. At the date of this page that includes Iran, North Korea, Syria, Cuba, Belarus, the Russian Federation, the Crimea region and the non-government controlled areas of the Donetsk and Luhansk regions.',
        'Sanctions change. If a country is added while your account is open, we will apply the change from the day it takes effect.',
    ]],
    ['4. What happens if you register from a restricted place', [
        'The account will be closed as soon as we notice, and any bonus and any winnings made with it are void.',
        'Deposits that have not been played are returned to the wallet address they came from, less the network fee. We cannot return funds that have already been lost in play.',
        'Verification documents are checked against this list, so a restriction is usually found before a withdrawal is paid.',
    ]],
    ['5. VPNs, proxies and false information', [
        'Using a VPN, a proxy, a remote desktop or any other tool to hide where you are connecting from is a breach of our terms.',
        'The same applies to giving a false address, a false nationality or documents that belong to somebody else.',
        'In either case the account is closed and the balance is treated as set out in section 4.',
    ]],
    ['6. Changes to this list', [
        'We update this page when the law, our payment provider or an applicable sanctions programme requires it.',
        'The version shown here is always the one that applies. Please check it before you deposit if you travel.',
    ]],
    ['7. Contact', [
        'If you are not sure whether your country is affected, ask us before you deposit:',
        'E-mail: support@vpcasino.net',
        'Website: vpcasino.net',
    ]],
];

const pt_BR = [
    ['1. Por que esta lista existe', [
        'O jogo online não é permitido em todos os lugares e alguns países estão sujeitos a sanções internacionais. Por isso, o :site não pode abrir contas nem aceitar depósitos de pessoas que morem ou se conectem a partir dos locais listados abaixo.',
        'A lista faz parte dos nossos termos. Ao criar uma conta, você confirma que não está em nenhum desses locais.',
    ]],
    ['2. Países e territórios que não podemos aceitar', [
        'Estados Unidos da América, incluindo todos os seus estados e territórios.',
        'Reino Unido da Grã-Bretanha e Irlanda do Norte.',
        'França, junto com seus departamentos e territórios ultramarinos.',
        'Países Baixos, Curaçao, Aruba, Bonaire, Sint Maarten, Santo Eustáquio e Saba.',
        'Austrália.',
        'Espanha.',
        'Israel.',
        'Ontário, Canadá.',
        'Região Administrativa Especial de Hong Kong.',
        'Singapura.',
    ]],
    ['3. Jurisdições sancionadas', [
        'Também não podemos atender ninguém em país ou região sujeita a sanções financeiras internacionais. Na data desta página isso inclui Irã, Coreia do Norte, Síria, Cuba, Belarus, Federação Russa, a região da Crimeia e as áreas não controladas pelo governo nas regiões de Donetsk e Lugansk.',
        'As sanções mudam. Se um país for incluído enquanto sua conta estiver aberta, aplicaremos a mudança a partir do dia em que ela entrar em vigor.',
    ]],
    ['4. O que acontece se você se registrar de um local restrito', [
        'A conta será encerrada assim que percebermos, e qualquer bônus e qualquer ganho obtido com ela ficam sem efeito.',
        'Depósitos que ainda não foram jogados são devolvidos ao endereço de carteira de onde vieram, descontada a taxa de rede. Não podemos devolver valores já perdidos no jogo.',
        'Os documentos de verificação são conferidos com esta lista, portanto uma restrição costuma ser encontrada antes de um saque ser pago.',
    ]],
    ['5. VPNs, proxies e informações falsas', [
        'Usar VPN, proxy, área de trabalho remota ou qualquer outra ferramenta para esconder de onde você se conecta é uma violação dos nossos termos.',
        'O mesmo vale para informar endereço falso, nacionalidade falsa ou documentos que pertencem a outra pessoa.',
        'Em qualquer dos casos a conta é encerrada e o saldo é tratado conforme a seção 4.',
    ]],
    ['6. Alterações nesta lista', [
        'Atualizamos esta página quando a lei, nosso provedor de pagamento ou um programa de sanções aplicável exigir.',
        'A versão mostrada aqui é sempre a que vale. Se você viajar, confira antes de depositar.',
    ]],
    ['7. Contato', [
        'Se não tiver certeza de que seu país é afetado, pergunte antes de depositar:',
        'E-mail: support@vpcasino.net',
        'Site: vpcasino.net',
    ]],
];

const es = [
    ['1. Por qué existe esta lista', [
        'El juego en línea no está permitido en todas partes y algunos países están sujetos a sanciones internacionales. Por eso :site no puede abrir cuentas ni aceptar depósitos de personas que residan o se conecten desde los lugares que figuran abajo.',
        'La lista forma parte de nuestros términos. Al registrar una cuenta confirmas que no te encuentras en ninguno de esos lugares.',
    ]],
    ['2. Países y territorios que no podemos aceptar', [
        'Estados Unidos de América, incluidos todos sus estados y territorios.',
        'Reino Unido de Gran Bretaña e Irlanda del Norte.',
        'Francia, junto con sus departamentos y territorios de ultramar.',
        'Países Bajos, Curazao, Aruba, Bonaire, San Martín, San Eustaquio y Saba.',
        'Australia.',
        'España.',
        'Israel.',
        'Ontario, Canadá.',
        'Región Administrativa Especial de Hong Kong.',
        'Singapur.',
    ]],
    ['3. Jurisdicciones sancionadas', [
        'Tampoco podemos atender a nadie en un país o región sujeta a sanciones financieras internacionales. A la fecha de esta página eso incluye Irán, Corea del Norte, Siria, Cuba, Bielorrusia, la Federación Rusa, la región de Crimea y las zonas de Donetsk y Lugansk no controladas por el gobierno.',
        'Las sanciones cambian. Si se añade un país mientras tu cuenta está abierta, aplicaremos el cambio desde el día en que tenga efecto.',
    ]],
    ['4. Qué ocurre si te registras desde un lugar restringido', [
        'La cuenta se cerrará en cuanto lo detectemos, y cualquier bono y cualquier ganancia obtenida con ella quedan anulados.',
        'Los depósitos que no se hayan jugado se devuelven a la dirección de la que vinieron, menos la comisión de red. No podemos devolver fondos que ya se han perdido jugando.',
        'Los documentos de verificación se comparan con esta lista, así que una restricción suele detectarse antes de pagar un retiro.',
    ]],
    ['5. VPN, proxies e información falsa', [
        'Usar una VPN, un proxy, un escritorio remoto o cualquier otra herramienta para ocultar desde dónde te conectas incumple nuestros términos.',
        'Lo mismo vale para dar una dirección falsa, una nacionalidad falsa o documentos que pertenecen a otra persona.',
        'En cualquiera de los casos la cuenta se cierra y el saldo se trata según la sección 4.',
    ]],
    ['6. Cambios en esta lista', [
        'Actualizamos esta página cuando lo exigen la ley, nuestro proveedor de pagos o un programa de sanciones aplicable.',
        'La versión que se muestra aquí es siempre la que rige. Si viajas, revísala antes de depositar.',
    ]],
    ['7. Contacto', [
        'Si no estás seguro de si tu país está afectado, pregúntanos antes de depositar:',
        'Correo: support@vpcasino.net',
        'Sitio web: vpcasino.net',
    ]],
];

const fr = [
    ['1. Pourquoi cette liste existe', [
        "Le jeu en ligne n'est pas autorisé partout et certains pays font l'objet de sanctions internationales. :site ne peut donc pas ouvrir de compte ni accepter de dépôt de personnes résidant dans les lieux ci-dessous ou s'y connectant.",
        "Cette liste fait partie de nos conditions. En créant un compte, vous confirmez ne pas vous trouver dans l'un de ces lieux.",
    ]],
    ['2. Pays et territoires que nous ne pouvons pas accepter', [
        "États-Unis d'Amérique, y compris tous leurs États et territoires.",
        'Royaume-Uni de Grande-Bretagne et d’Irlande du Nord.',
        'France, ainsi que ses départements et territoires d’outre-mer.',
        'Pays-Bas, Curaçao, Aruba, Bonaire, Saint-Martin, Saint-Eustache et Saba.',
        'Australie.',
        'Espagne.',
        'Israël.',
        'Ontario, Canada.',
        'Région administrative spéciale de Hong Kong.',
        'Singapour.',
    ]],
    ['3. Juridictions sous sanctions', [
        "Nous ne pouvons pas non plus servir une personne se trouvant dans un pays ou une région visée par des sanctions financières internationales. À la date de cette page, cela comprend l'Iran, la Corée du Nord, la Syrie, Cuba, le Bélarus, la Fédération de Russie, la Crimée et les zones des régions de Donetsk et de Louhansk non contrôlées par le gouvernement.",
        "Les sanctions évoluent. Si un pays est ajouté alors que votre compte est ouvert, nous appliquons le changement à compter du jour où il prend effet.",
    ]],
    ['4. Ce qui se passe si vous vous inscrivez depuis un lieu restreint', [
        'Le compte est fermé dès que nous le constatons, et tout bonus ainsi que tout gain obtenu avec celui-ci sont annulés.',
        "Les dépôts qui n'ont pas été joués sont renvoyés à l'adresse de portefeuille d'origine, moins les frais de réseau. Nous ne pouvons pas restituer des fonds déjà perdus au jeu.",
        "Les documents de vérification sont comparés à cette liste, si bien qu'une restriction est en général détectée avant le paiement d'un retrait.",
    ]],
    ['5. VPN, proxys et fausses informations', [
        "Utiliser un VPN, un proxy, un bureau à distance ou tout autre outil pour masquer le lieu de connexion constitue une violation de nos conditions.",
        "Il en va de même pour une adresse fausse, une nationalité fausse ou des documents appartenant à autrui.",
        'Dans les deux cas, le compte est fermé et le solde est traité conformément à la section 4.',
    ]],
    ['6. Modifications de cette liste', [
        "Nous mettons cette page à jour lorsque la loi, notre prestataire de paiement ou un programme de sanctions applicable l'exige.",
        'La version affichée ici est toujours celle qui s’applique. Si vous voyagez, consultez-la avant de déposer.',
    ]],
    ['7. Contact', [
        'Si vous ne savez pas si votre pays est concerné, demandez-nous avant de déposer :',
        'E-mail : support@vpcasino.net',
        'Site : vpcasino.net',
    ]],
];

const de = [
    ['1. Warum es diese Liste gibt', [
        'Onlinespiel ist nicht überall erlaubt, und einige Länder unterliegen internationalen Sanktionen. :site kann daher keine Konten für Personen eröffnen und keine Einzahlungen annehmen, die in den unten genannten Ländern wohnen oder sich von dort verbinden.',
        'Die Liste ist Teil unserer Bedingungen. Mit der Registrierung bestätigen Sie, dass Sie sich nicht an einem dieser Orte befinden.',
    ]],
    ['2. Länder und Gebiete, die wir nicht annehmen können', [
        'Vereinigte Staaten von Amerika, einschließlich aller Bundesstaaten und Gebiete.',
        'Vereinigtes Königreich Großbritannien und Nordirland.',
        'Frankreich sowie seine überseeischen Departements und Gebiete.',
        'Niederlande, Curaçao, Aruba, Bonaire, Sint Maarten, Sint Eustatius und Saba.',
        'Australien.',
        'Spanien.',
        'Israel.',
        'Ontario, Kanada.',
        'Sonderverwaltungsregion Hongkong.',
        'Singapur.',
    ]],
    ['3. Sanktionierte Gebiete', [
        'Ebenso wenig können wir Personen bedienen, die sich in einem Land oder einer Region mit internationalen Finanzsanktionen aufhalten. Zum Stand dieser Seite gehören dazu Iran, Nordkorea, Syrien, Kuba, Belarus, die Russische Föderation, die Krim sowie die nicht von der Regierung kontrollierten Gebiete Donezk und Luhansk.',
        'Sanktionen ändern sich. Kommt ein Land hinzu, während Ihr Konto besteht, wenden wir die Änderung ab ihrem Inkrafttreten an.',
    ]],
    ['4. Was passiert, wenn Sie sich von einem gesperrten Ort registrieren', [
        'Das Konto wird geschlossen, sobald wir es bemerken; jeder Bonus und jeder damit erzielte Gewinn verfallen.',
        'Einzahlungen, die noch nicht verspielt wurden, gehen abzüglich der Netzwerkgebühr an die Wallet-Adresse zurück, von der sie kamen. Bereits im Spiel verlorene Beträge können wir nicht erstatten.',
        'Verifizierungsunterlagen werden mit dieser Liste abgeglichen, sodass eine Sperre in der Regel vor der Auszahlung auffällt.',
    ]],
    ['5. VPNs, Proxys und falsche Angaben', [
        'Ein VPN, ein Proxy, ein Remote-Desktop oder ein anderes Mittel, um den Verbindungsort zu verschleiern, verstößt gegen unsere Bedingungen.',
        'Dasselbe gilt für falsche Adressen, eine falsche Staatsangehörigkeit oder Unterlagen, die einer anderen Person gehören.',
        'In beiden Fällen wird das Konto geschlossen und das Guthaben nach Abschnitt 4 behandelt.',
    ]],
    ['6. Änderungen dieser Liste', [
        'Wir aktualisieren diese Seite, wenn das Gesetz, unser Zahlungsdienstleister oder ein geltendes Sanktionsprogramm es verlangt.',
        'Es gilt stets die hier gezeigte Fassung. Prüfen Sie sie vor einer Einzahlung, wenn Sie verreisen.',
    ]],
    ['7. Kontakt', [
        'Wenn Sie nicht sicher sind, ob Ihr Land betroffen ist, fragen Sie uns vor der Einzahlung:',
        'E-Mail: support@vpcasino.net',
        'Website: vpcasino.net',
    ]],
];

export default { en, pt_BR, es, fr, de };
