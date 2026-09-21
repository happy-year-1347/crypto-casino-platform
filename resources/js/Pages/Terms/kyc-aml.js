// The KYC/AML Policy referred to in section 21 of the Welcome Bonus terms.
// ":site" is the site name. Each section: [title, [paragraphs...]]

const en = [
    ['1. Purpose', [
        'This policy explains how :site verifies who its customers are and how it works against money laundering and the financing of crime.',
        'It applies to every account. Accepting our terms means accepting the checks described here.',
    ]],
    ['2. Know Your Customer', [
        'Know Your Customer, or KYC, means making sure an account belongs to a real adult who is who they say they are.',
        'An account may be opened with an e-mail address, but verification must be completed before a withdrawal is paid, and we may ask for it earlier.',
    ]],
    ['3. When we ask you to verify', [
        'Before the first withdrawal from an account.',
        'When total deposits or withdrawals pass the threshold set by our payment provider or by applicable law.',
        'When the details on the account do not match, for example a name, a country or a date of birth that changes.',
        'When several accounts appear to be used by one person, or one payment source funds several accounts.',
        'When play or payment behaviour looks unusual for the amounts involved.',
        'At any time, where we have a legal duty to do so.',
    ]],
    ['4. What we ask for', [
        'Proof of identity: a passport, national identity card or driving licence, in colour, showing all four corners and still valid.',
        'Proof of address: a utility bill, bank statement or official letter issued in the last three months showing your name and address.',
        'Proof that you control the account: a selfie holding your document, where the identity document alone is not enough.',
        'Proof of the wallet you withdraw to, where the withdrawal address is new or has not been used before.',
        'Source of funds, for larger amounts: a payslip, a tax return, a sale contract or an exchange statement showing where the money came from.',
    ]],
    ['5. How long it takes', [
        'We aim to review documents within 24 hours of receiving them, and within 72 hours at the busiest times.',
        'If a document is unreadable, expired or incomplete we will tell you what is missing rather than simply refusing it.',
        'Withdrawals requested during verification are held, not cancelled, and are released once the check passes.',
    ]],
    ['6. Cryptocurrency and the blockchain', [
        'Deposits and withdrawals are handled by our payment provider, which screens wallet addresses against sanctions lists and known criminal sources.',
        'We do not accept funds from mixers, tumblers, darknet markets or addresses flagged as stolen, and we may be required to freeze such funds rather than return them.',
        'Withdrawals are normally paid to a wallet you control. We do not pay a withdrawal to a third party.',
    ]],
    ['7. Monitoring and reporting', [
        'Transactions are monitored for patterns associated with laundering, such as depositing and withdrawing with little or no play, structuring amounts to stay under a threshold, or repeated use of different payment sources.',
        'Where we have reasonable grounds for suspicion, we are required to report it to the competent authority. The law forbids us from telling you that a report has been made.',
    ]],
    ['8. If a check fails', [
        'We may suspend the account, hold a withdrawal, ask for further documents or close the account.',
        'Where an account is closed because the customer cannot be verified, or is underage, or is in a restricted country, unplayed deposits are returned to their source and any bonus is void.',
        'Funds we are legally required to hold or hand over cannot be returned, whatever the reason the account was closed.',
    ]],
    ['9. Records and your data', [
        'Verification documents are stored securely, are seen only by staff who need them, and are kept for the period the law and our payment provider require, normally five years after the account closes.',
        'What we do with personal data, and the rights you have over it, is set out in the Privacy Policy.',
    ]],
    ['10. Staff and updates', [
        'Anyone handling verification at :site is instructed in these rules before they start and cannot approve their own account.',
        'This policy is reviewed when the law, our payment provider or the risks we see change.',
    ]],
    ['11. Contact', [
        'Send verification documents, or ask what is still needed, through the support channel on the site or by e-mail:',
        'E-mail: support@vpcasino.net',
        'Website: vpcasino.net',
        'Never send documents to anyone who contacts you from another address claiming to be :site.',
    ]],
];

const pt_BR = [
    ['1. Objetivo', [
        'Esta política explica como o :site verifica quem são seus clientes e como atua contra a lavagem de dinheiro e o financiamento de crimes.',
        'Ela vale para todas as contas. Aceitar nossos termos é aceitar as verificações aqui descritas.',
    ]],
    ['2. Conheça Seu Cliente', [
        'Conheça Seu Cliente, ou KYC, é garantir que a conta pertence a um adulto real e que ele é quem diz ser.',
        'A conta pode ser aberta com um e-mail, mas a verificação precisa estar concluída antes do pagamento de um saque, e podemos pedi-la antes disso.',
    ]],
    ['3. Quando pedimos a verificação', [
        'Antes do primeiro saque da conta.',
        'Quando o total de depósitos ou saques passa do limite definido pelo nosso provedor de pagamento ou pela lei aplicável.',
        'Quando os dados da conta não batem, por exemplo um nome, um país ou uma data de nascimento que muda.',
        'Quando várias contas parecem ser usadas por uma só pessoa, ou uma origem de pagamento financia várias contas.',
        'Quando o comportamento de jogo ou de pagamento parece incomum para os valores envolvidos.',
        'A qualquer momento, quando tivermos dever legal de fazê-lo.',
    ]],
    ['4. O que pedimos', [
        'Documento de identidade: passaporte, documento nacional ou carteira de habilitação, colorido, com os quatro cantos visíveis e dentro da validade.',
        'Comprovante de endereço: conta de consumo, extrato bancário ou carta oficial emitida nos últimos três meses com seu nome e endereço.',
        'Prova de que você controla a conta: uma selfie segurando o documento, quando o documento sozinho não bastar.',
        'Prova da carteira de saque, quando o endereço for novo ou nunca tiver sido usado.',
        'Origem dos recursos, para valores maiores: holerite, declaração de imposto, contrato de venda ou extrato de corretora mostrando de onde veio o dinheiro.',
    ]],
    ['5. Quanto tempo leva', [
        'Buscamos analisar os documentos em até 24 horas do recebimento, e em até 72 horas nos períodos de maior movimento.',
        'Se um documento estiver ilegível, vencido ou incompleto, dizemos o que falta em vez de simplesmente recusá-lo.',
        'Saques pedidos durante a verificação ficam retidos, não cancelados, e são liberados quando a conferência passa.',
    ]],
    ['6. Criptomoedas e a blockchain', [
        'Depósitos e saques são processados pelo nosso provedor de pagamento, que compara os endereços de carteira com listas de sanções e com origens criminosas conhecidas.',
        'Não aceitamos recursos de mixers, tumblers, mercados da darknet ou endereços marcados como roubados, e podemos ser obrigados a bloquear esses valores em vez de devolvê-los.',
        'Os saques são pagos a uma carteira que você controla. Não pagamos saque a terceiros.',
    ]],
    ['7. Monitoramento e comunicação', [
        'As transações são monitoradas em busca de padrões ligados à lavagem, como depositar e sacar com pouco ou nenhum jogo, fracionar valores para ficar abaixo de um limite ou usar repetidamente origens de pagamento diferentes.',
        'Havendo fundada suspeita, somos obrigados a comunicar a autoridade competente. A lei nos proíbe de avisar que uma comunicação foi feita.',
    ]],
    ['8. Se uma verificação falha', [
        'Podemos suspender a conta, reter um saque, pedir documentos adicionais ou encerrar a conta.',
        'Quando a conta é encerrada porque o cliente não pode ser verificado, é menor de idade ou está em país restrito, os depósitos não jogados voltam à origem e qualquer bônus fica sem efeito.',
        'Valores que somos legalmente obrigados a reter ou entregar não podem ser devolvidos, qualquer que seja o motivo do encerramento.',
    ]],
    ['9. Registros e seus dados', [
        'Os documentos de verificação são guardados com segurança, vistos apenas por quem precisa deles e mantidos pelo prazo exigido pela lei e pelo provedor de pagamento, normalmente cinco anos após o encerramento da conta.',
        'O que fazemos com dados pessoais e os direitos que você tem sobre eles estão na Política de Privacidade.',
    ]],
    ['10. Equipe e atualizações', [
        'Quem cuida da verificação no :site é orientado sobre estas regras antes de começar e não pode aprovar a própria conta.',
        'Esta política é revista quando a lei, o provedor de pagamento ou os riscos observados mudam.',
    ]],
    ['11. Contato', [
        'Envie os documentos, ou pergunte o que ainda falta, pelo canal de suporte do site ou por e-mail:',
        'E-mail: support@vpcasino.net',
        'Site: vpcasino.net',
        'Nunca envie documentos a quem entrar em contato de outro endereço dizendo ser o :site.',
    ]],
];

const es = [
    ['1. Finalidad', [
        'Esta política explica cómo :site comprueba quiénes son sus clientes y cómo actúa contra el blanqueo de capitales y la financiación del delito.',
        'Se aplica a todas las cuentas. Aceptar nuestros términos supone aceptar las comprobaciones aquí descritas.',
    ]],
    ['2. Conozca a su Cliente', [
        'Conozca a su Cliente, o KYC, significa asegurarse de que una cuenta pertenece a un adulto real y de que es quien dice ser.',
        'Una cuenta puede abrirse con un correo electrónico, pero la verificación debe completarse antes de pagar un retiro, y podemos pedirla antes.',
    ]],
    ['3. Cuándo pedimos la verificación', [
        'Antes del primer retiro de la cuenta.',
        'Cuando el total de depósitos o retiros supera el umbral fijado por nuestro proveedor de pagos o por la ley aplicable.',
        'Cuando los datos de la cuenta no cuadran, por ejemplo un nombre, un país o una fecha de nacimiento que cambia.',
        'Cuando varias cuentas parecen usadas por una misma persona, o una fuente de pago financia varias cuentas.',
        'Cuando el juego o los pagos resultan inusuales para los importes en cuestión.',
        'En cualquier momento, cuando tengamos el deber legal de hacerlo.',
    ]],
    ['4. Qué pedimos', [
        'Documento de identidad: pasaporte, documento nacional o permiso de conducir, en color, con las cuatro esquinas visibles y en vigor.',
        'Justificante de domicilio: factura de suministro, extracto bancario o carta oficial emitida en los últimos tres meses con tu nombre y dirección.',
        'Prueba de que controlas la cuenta: un selfi sosteniendo el documento, cuando el documento por sí solo no baste.',
        'Prueba de la cartera de retiro, cuando la dirección sea nueva o no se haya usado antes.',
        'Origen de los fondos, para importes mayores: nómina, declaración de impuestos, contrato de venta o extracto de un exchange que muestre de dónde procede el dinero.',
    ]],
    ['5. Cuánto tarda', [
        'Procuramos revisar los documentos en 24 horas desde su recepción, y en 72 horas en los momentos de más trabajo.',
        'Si un documento es ilegible, está caducado o incompleto, te diremos qué falta en lugar de rechazarlo sin más.',
        'Los retiros pedidos durante la verificación quedan retenidos, no cancelados, y se liberan al superar la comprobación.',
    ]],
    ['6. Criptomonedas y la cadena de bloques', [
        'Los depósitos y retiros los gestiona nuestro proveedor de pagos, que coteja las direcciones con listas de sanciones y con orígenes delictivos conocidos.',
        'No aceptamos fondos de mixers, tumblers, mercados de la darknet ni direcciones señaladas como robadas, y podemos vernos obligados a bloquear esos fondos en lugar de devolverlos.',
        'Los retiros se pagan a una cartera que controlas tú. No pagamos un retiro a un tercero.',
    ]],
    ['7. Vigilancia y comunicación', [
        'Se vigilan las operaciones en busca de patrones propios del blanqueo, como depositar y retirar jugando poco o nada, fraccionar importes para quedar por debajo de un umbral o usar repetidamente fuentes de pago distintas.',
        'Cuando hay motivos razonables de sospecha, estamos obligados a comunicarlo a la autoridad competente. La ley nos prohíbe avisarte de que se ha hecho una comunicación.',
    ]],
    ['8. Si una comprobación falla', [
        'Podemos suspender la cuenta, retener un retiro, pedir más documentos o cerrar la cuenta.',
        'Cuando se cierra una cuenta porque el cliente no puede verificarse, es menor de edad o está en un país restringido, los depósitos no jugados vuelven a su origen y cualquier bono queda anulado.',
        'Los fondos que estamos legalmente obligados a retener o entregar no pueden devolverse, sea cual sea el motivo del cierre.',
    ]],
    ['9. Registros y tus datos', [
        'Los documentos se guardan de forma segura, solo los ve el personal que los necesita y se conservan durante el plazo que exigen la ley y nuestro proveedor de pagos, normalmente cinco años tras el cierre de la cuenta.',
        'Qué hacemos con los datos personales y qué derechos tienes sobre ellos figura en la Política de Privacidad.',
    ]],
    ['10. Personal y actualizaciones', [
        'Quien se ocupa de la verificación en :site recibe instrucciones sobre estas reglas antes de empezar y no puede aprobar su propia cuenta.',
        'Esta política se revisa cuando cambian la ley, nuestro proveedor de pagos o los riesgos observados.',
    ]],
    ['11. Contacto', [
        'Envía los documentos, o pregunta qué falta todavía, por el canal de soporte del sitio o por correo:',
        'Correo: support@vpcasino.net',
        'Sitio web: vpcasino.net',
        'No envíes nunca documentos a quien te escriba desde otra dirección diciendo ser :site.',
    ]],
];

const fr = [
    ['1. Objet', [
        "Cette politique explique comment :site vérifie l'identité de ses clients et comment il lutte contre le blanchiment et le financement de la criminalité.",
        "Elle s'applique à tous les comptes. Accepter nos conditions, c'est accepter les contrôles décrits ici.",
    ]],
    ['2. Connaissance du client', [
        "La connaissance du client, ou KYC, consiste à s'assurer qu'un compte appartient à un adulte réel et qu'il est bien celui qu'il prétend être.",
        "Un compte peut être ouvert avec une adresse e-mail, mais la vérification doit être terminée avant le paiement d'un retrait, et nous pouvons la demander plus tôt.",
    ]],
    ['3. Quand nous demandons la vérification', [
        'Avant le premier retrait du compte.',
        "Lorsque le total des dépôts ou des retraits dépasse le seuil fixé par notre prestataire de paiement ou par la loi applicable.",
        "Lorsque les informations du compte ne concordent pas, par exemple un nom, un pays ou une date de naissance qui change.",
        "Lorsque plusieurs comptes semblent utilisés par une même personne, ou qu'une même source de paiement alimente plusieurs comptes.",
        "Lorsque le jeu ou les paiements paraissent inhabituels au regard des montants.",
        "À tout moment, lorsque la loi nous y oblige.",
    ]],
    ['4. Ce que nous demandons', [
        "Pièce d'identité : passeport, carte nationale d'identité ou permis de conduire, en couleur, les quatre coins visibles et en cours de validité.",
        "Justificatif de domicile : facture, relevé bancaire ou courrier officiel émis dans les trois derniers mois, à votre nom et adresse.",
        "Preuve que vous contrôlez le compte : un selfie avec votre pièce d'identité, lorsque la pièce seule ne suffit pas.",
        "Preuve du portefeuille de retrait, lorsque l'adresse est nouvelle ou n'a jamais servi.",
        "Origine des fonds, pour les montants importants : bulletin de salaire, avis d'imposition, acte de vente ou relevé de plateforme montrant d'où vient l'argent.",
    ]],
    ['5. Délais', [
        "Nous visons un examen des documents dans les 24 heures suivant leur réception, et dans les 72 heures aux périodes chargées.",
        "Si un document est illisible, périmé ou incomplet, nous vous disons ce qui manque plutôt que de le refuser sans explication.",
        "Les retraits demandés pendant la vérification sont retenus, non annulés, et libérés dès que le contrôle est passé.",
    ]],
    ['6. Cryptomonnaies et blockchain', [
        "Les dépôts et retraits sont traités par notre prestataire de paiement, qui compare les adresses aux listes de sanctions et aux sources criminelles connues.",
        "Nous n'acceptons pas de fonds provenant de mixeurs, de marchés du darknet ou d'adresses signalées comme volées, et nous pouvons être tenus de geler ces fonds plutôt que de les restituer.",
        "Les retraits sont versés à un portefeuille que vous contrôlez. Nous ne payons pas un retrait à un tiers.",
    ]],
    ['7. Surveillance et déclaration', [
        "Les opérations sont surveillées pour repérer des schémas liés au blanchiment : déposer et retirer en jouant peu ou pas, fractionner des montants pour rester sous un seuil, ou multiplier les sources de paiement.",
        "En cas de soupçon raisonnable, nous devons le déclarer à l'autorité compétente. La loi nous interdit de vous informer qu'une déclaration a été faite.",
    ]],
    ['8. Si un contrôle échoue', [
        'Nous pouvons suspendre le compte, retenir un retrait, demander des documents supplémentaires ou fermer le compte.',
        "Lorsqu'un compte est fermé parce que le client ne peut pas être vérifié, est mineur ou se trouve dans un pays restreint, les dépôts non joués sont renvoyés à leur source et tout bonus est annulé.",
        "Les fonds que la loi nous oblige à conserver ou à remettre ne peuvent pas être restitués, quelle que soit la raison de la fermeture.",
    ]],
    ['9. Conservation et vos données', [
        "Les documents sont conservés de façon sécurisée, vus seulement par le personnel qui en a besoin, et gardés pendant la durée exigée par la loi et notre prestataire de paiement, en principe cinq ans après la clôture du compte.",
        "Ce que nous faisons des données personnelles et les droits dont vous disposez figurent dans la Politique de confidentialité.",
    ]],
    ['10. Personnel et mises à jour', [
        "Toute personne chargée de la vérification chez :site est formée à ces règles avant de commencer et ne peut pas valider son propre compte.",
        "Cette politique est revue lorsque la loi, notre prestataire de paiement ou les risques constatés évoluent.",
    ]],
    ['11. Contact', [
        "Envoyez vos documents, ou demandez ce qui manque encore, via le support du site ou par e-mail :",
        'E-mail : support@vpcasino.net',
        'Site : vpcasino.net',
        "N'envoyez jamais de documents à quelqu'un qui vous écrit depuis une autre adresse en se faisant passer pour :site.",
    ]],
];

const de = [
    ['1. Zweck', [
        'Diese Richtlinie erklärt, wie :site die Identität seiner Kunden prüft und wie es gegen Geldwäsche und die Finanzierung von Straftaten vorgeht.',
        'Sie gilt für jedes Konto. Wer unsere Bedingungen annimmt, nimmt auch die hier beschriebenen Prüfungen an.',
    ]],
    ['2. Kundenkenntnis', [
        'Kundenkenntnis, kurz KYC, bedeutet sicherzustellen, dass ein Konto einem echten Erwachsenen gehört und dieser ist, wer er zu sein angibt.',
        'Ein Konto kann mit einer E-Mail-Adresse eröffnet werden, die Verifizierung muss aber vor der Auszahlung abgeschlossen sein, und wir können sie früher verlangen.',
    ]],
    ['3. Wann wir die Verifizierung verlangen', [
        'Vor der ersten Auszahlung aus dem Konto.',
        'Wenn die Summe der Ein- oder Auszahlungen die von unserem Zahlungsdienstleister oder vom geltenden Recht gesetzte Schwelle überschreitet.',
        'Wenn die Kontodaten nicht zusammenpassen, etwa ein wechselnder Name, ein wechselndes Land oder Geburtsdatum.',
        'Wenn mehrere Konten von einer Person genutzt zu werden scheinen oder eine Zahlungsquelle mehrere Konten speist.',
        'Wenn Spiel- oder Zahlungsverhalten für die Beträge ungewöhnlich wirkt.',
        'Jederzeit, wenn wir gesetzlich dazu verpflichtet sind.',
    ]],
    ['4. Was wir verlangen', [
        'Identitätsnachweis: Reisepass, Personalausweis oder Führerschein, in Farbe, mit allen vier Ecken und gültig.',
        'Adressnachweis: Rechnung eines Versorgers, Kontoauszug oder amtliches Schreiben aus den letzten drei Monaten mit Name und Anschrift.',
        'Nachweis, dass Sie das Konto führen: ein Selfie mit dem Ausweis, wenn der Ausweis allein nicht genügt.',
        'Nachweis der Auszahlungs-Wallet, wenn die Adresse neu ist oder noch nie genutzt wurde.',
        'Mittelherkunft bei größeren Beträgen: Lohnabrechnung, Steuerbescheid, Kaufvertrag oder Börsenauszug, der zeigt, woher das Geld stammt.',
    ]],
    ['5. Wie lange es dauert', [
        'Wir prüfen Unterlagen möglichst innerhalb von 24 Stunden nach Eingang, in Spitzenzeiten innerhalb von 72 Stunden.',
        'Ist ein Dokument unlesbar, abgelaufen oder unvollständig, sagen wir Ihnen, was fehlt, statt es einfach abzulehnen.',
        'Während der Prüfung beantragte Auszahlungen werden zurückgehalten, nicht storniert, und nach bestandener Prüfung freigegeben.',
    ]],
    ['6. Kryptowährungen und die Blockchain', [
        'Ein- und Auszahlungen wickelt unser Zahlungsdienstleister ab, der Wallet-Adressen gegen Sanktionslisten und bekannte kriminelle Quellen prüft.',
        'Mittel aus Mixern, Tumblern, Darknet-Marktplätzen oder als gestohlen markierten Adressen nehmen wir nicht an; wir können verpflichtet sein, solche Mittel einzufrieren statt zurückzuzahlen.',
        'Auszahlungen gehen an eine Wallet, die Sie kontrollieren. An Dritte zahlen wir nicht aus.',
    ]],
    ['7. Überwachung und Meldung', [
        'Transaktionen werden auf Muster der Geldwäsche geprüft, etwa Ein- und Auszahlen mit wenig oder ohne Spiel, Stückelung von Beträgen unterhalb einer Schwelle oder wiederholt wechselnde Zahlungsquellen.',
        'Bei begründetem Verdacht müssen wir dies der zuständigen Behörde melden. Das Gesetz verbietet uns, Sie über eine Meldung zu informieren.',
    ]],
    ['8. Wenn eine Prüfung scheitert', [
        'Wir können das Konto sperren, eine Auszahlung zurückhalten, weitere Unterlagen verlangen oder das Konto schließen.',
        'Wird ein Konto geschlossen, weil der Kunde nicht verifiziert werden kann, minderjährig ist oder sich in einem gesperrten Land befindet, gehen nicht verspielte Einzahlungen an ihre Quelle zurück und jeder Bonus verfällt.',
        'Mittel, die wir gesetzlich zurückhalten oder herausgeben müssen, können nicht erstattet werden, gleich aus welchem Grund das Konto geschlossen wurde.',
    ]],
    ['9. Aufbewahrung und Ihre Daten', [
        'Verifizierungsunterlagen werden sicher gespeichert, nur von den dafür zuständigen Personen eingesehen und so lange aufbewahrt, wie Gesetz und Zahlungsdienstleister es verlangen, in der Regel fünf Jahre nach Schließung des Kontos.',
        'Was mit personenbezogenen Daten geschieht und welche Rechte Sie daran haben, steht in der Datenschutzerklärung.',
    ]],
    ['10. Mitarbeitende und Aktualisierung', [
        'Wer bei :site Verifizierungen bearbeitet, wird vorab in diese Regeln eingewiesen und darf das eigene Konto nicht freigeben.',
        'Diese Richtlinie wird überprüft, wenn sich Gesetz, Zahlungsdienstleister oder die erkannten Risiken ändern.',
    ]],
    ['11. Kontakt', [
        'Senden Sie Unterlagen oder fragen Sie nach, was noch fehlt, über den Support der Website oder per E-Mail:',
        'E-Mail: support@vpcasino.net',
        'Website: vpcasino.net',
        'Senden Sie nie Unterlagen an jemanden, der Sie von einer anderen Adresse aus anschreibt und sich als :site ausgibt.',
    ]],
];

export default { en, pt_BR, es, fr, de };
