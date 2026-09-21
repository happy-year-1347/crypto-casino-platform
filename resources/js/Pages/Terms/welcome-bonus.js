// Welcome bonus terms, supplied by the operator on 2026-09-19 and published as
// written. The numbers are placeholders so the page always shows what is
// actually configured in the admin:
//   :site      site name            :bonus     welcome bonus percentage
//   :bonusmax  the cap on the bonus :rollover  wagering multiplier
//   :min       qualifying deposit   :maxbet    largest bet while the bonus runs
//   :days      days before it expires
// Each section: [title, [paragraphs...]]

const en = [
    ['1. Welcome Bonus', [
        'New eligible :site customers may receive a :bonus% deposit match bonus up to a maximum bonus of :bonusmax.',
        'Examples:',
        'Deposit :min and receive a bonus of the same amount.',
        'Deposit :bonusmax and receive a bonus of :bonusmax.',
        'Deposit more than :bonusmax and the bonus is still :bonusmax.',
        'The maximum Welcome Bonus available to one customer is :bonusmax.',
        'The minimum qualifying deposit is :min.',
    ]],
    ['2. Eligible Currencies', [
        'The Welcome Bonus may be claimed using the following supported cryptocurrencies: Bitcoin (BTC), Ethereum (ETH), USD Coin (USDC) and Dogecoin (DOGE).',
        'Where a cryptocurrency deposit is converted into the casino\'s bonus accounting currency, :site will use the exchange rate displayed by the casino or payment system at the time the deposit is credited.',
        'Blockchain transaction fees, network fees or third-party payment fees are not included in the bonus amount.',
    ]],
    ['3. Wagering Requirement', [
        'The Welcome Bonus is subject to a :rollover times wagering requirement on the bonus amount.',
        'Only wagers placed on the designated eligible :site slots listed in Section 4 count toward the wagering requirement.',
        'For example, a bonus of :min carries a wagering requirement of :min multiplied by :rollover.',
        'The wagering requirement applies to the bonus amount, not the total deposit plus bonus amount.',
    ]],
    ['4. Eligible Games', [
        'For the initial :site pilot, wagering toward the Welcome Bonus is available only on the following 12 designated games:',
        'Fortune Tiger, Queen of Bounty, Song Kran Party, Bikini Paradise, Phoenix Rises, Fortune Panda, Hood vs Wolf, Treasures of Aztec, Jack Frost\'s Winter, Fortune OX, Fortune Mouse and Fortune Rabbit.',
        'Games not listed above do not contribute toward completion of the Welcome Bonus wagering requirement.',
        ':site may change its available games from time to time, subject to applicable law and the applicable game and promotional terms.',
    ]],
    ['5. Bonus Validity', [
        'The Welcome Bonus must be fully wagered within :days calendar days from the time the bonus is credited to the customer\'s account.',
        'If the wagering requirement has not been completed when that period expires, the remaining promotional bonus balance will expire.',
        'Any real-money balance remaining in the customer\'s account will remain subject to the General Terms and Conditions and applicable withdrawal requirements.',
    ]],
    ['6. Maximum Bet', [
        'While the Welcome Bonus is active, the maximum qualifying wager is :maxbet per spin or equivalent cryptocurrency value.',
        'A customer who exceeds the maximum permitted wager may have the bonus and associated promotional winnings cancelled where permitted by applicable law and the General Terms and Conditions.',
        'The maximum-bet rule does not apply where the customer is playing exclusively with unrestricted real-money funds after the bonus has been forfeited or completed, subject to applicable game rules.',
    ]],
    ['7. One Welcome Bonus Per Customer', [
        'The Welcome Bonus is limited to one bonus per person.',
        ':site may also consider accounts connected through factors including, but not limited to: household; residential address; device; IP address; payment method; cryptocurrency wallet; telephone number; email address; or other account or identity information.',
        'Opening multiple accounts for the purpose of obtaining additional Welcome Bonuses is prohibited.',
    ]],
    ['8. No Bonus Abuse', [
        'Customers must not attempt to manipulate or abuse the Welcome Bonus.',
        'Examples of prohibited activity may include: creating multiple accounts; using another person\'s identity or account; coordinating accounts to obtain multiple bonuses; using fraudulent payment information; exploiting software, technical or pricing errors; using automated systems or bots; attempting to circumvent wagering or promotional restrictions; or any other conduct intended to obtain a promotional advantage through deception or circumvention of these terms.',
        'Where :site reasonably identifies suspected bonus abuse, it may suspend the relevant account while conducting an investigation, subject always to applicable law and the customer\'s contractual and regulatory rights.',
    ]],
    ['9. Verification and KYC', [
        ':site may require identity, age, address, source-of-funds or other verification before allowing a customer to withdraw funds.',
        'A customer may be required to complete applicable Know Your Customer ("KYC"), anti-money-laundering and responsible-gambling checks.',
        'Completion of the wagering requirement does not guarantee immediate withdrawal approval where legally required verification remains outstanding.',
    ]],
    ['10. Withdrawals', [
        'Once the applicable wagering requirement has been completed, promotional winnings may become eligible for withdrawal subject to: completion of applicable wagering requirements; successful account and identity verification; compliance with applicable anti-money-laundering and responsible-gambling requirements; compliance with the General Terms and Conditions; any applicable minimum withdrawal amount; and any applicable cryptocurrency or network requirements.',
        ':site will not require a customer to wager deposited real-money funds solely because those funds were deposited alongside a promotional bonus, except where a specific and lawful promotional condition expressly applies.',
    ]],
    ['11. Cryptocurrency Withdrawals', [
        'Withdrawals will normally be processed using a supported cryptocurrency and network selected by :site.',
        'Customers are responsible for providing a correct receiving wallet address and selecting the correct supported network.',
        ':site is not responsible for cryptocurrency sent to an incorrect or unsupported address or network where the transaction cannot be recovered.',
        'Blockchain transactions may be irreversible.',
        'Applicable network or processing fees may be deducted from withdrawals where disclosed before the transaction is processed.',
    ]],
    ['12. Cryptocurrency Value', [
        'Because cryptocurrency prices fluctuate, the value of a cryptocurrency transaction may change between deposit, wagering and withdrawal.',
        'The casino may determine the equivalent value of a cryptocurrency transaction using the exchange rate displayed by its payment processor or casino system at the relevant transaction time.',
    ]],
    ['13. Deposits and Withdrawals', [
        ':site reserves the right to establish minimum and maximum deposit and withdrawal limits for each supported cryptocurrency.',
        'Payment processing may be subject to additional verification, network confirmations, blockchain conditions and applicable compliance procedures.',
    ]],
    ['14. Eligibility and Restricted Jurisdictions', [
        'The Welcome Bonus is available only to customers who are legally permitted to participate in online gambling in their jurisdiction and who satisfy :site\'s applicable eligibility requirements.',
        ':site does not authorise customers to access the service from jurisdictions where online gambling or the provision of remote gambling services is prohibited or requires a licence or permit that :site does not possess.',
        'The current restricted-country list must be displayed separately on the :site website and may be updated as legal and regulatory requirements change.',
        'Customers are responsible for determining whether their participation is lawful in their location.',
        ':site must not use this clause as a substitute for determining where it is legally permitted to accept customers.',
    ]],
    ['15. Age Requirement', [
        'Customers must satisfy the minimum legal gambling age applicable in their jurisdiction and must provide accurate information during registration.',
        ':site will not knowingly provide gambling services to persons who are below the applicable legal gambling age.',
    ]],
    ['16. Responsible Gambling', [
        'The Welcome Bonus is a promotional offer and is not intended to encourage customers to gamble beyond their means.',
        'Customers should only gamble with funds they can afford to lose.',
        'Information concerning deposit limits, wagering limits, self-exclusion, account restrictions and other responsible-gambling tools is available through the :site Responsible Gambling Policy.',
    ]],
    ['17. Promotional Changes', [
        ':site may withdraw, modify or discontinue the Welcome Bonus where reasonably necessary, including because of legal, regulatory, technical, security or operational requirements.',
        'Where legally required, customers will be given appropriate notice of material changes.',
        'Changes will not retrospectively remove rights that have already accrued except where permitted or required by applicable law.',
    ]],
    ['18. Errors and Technical Problems', [
        'If the Welcome Bonus is credited incorrectly because of a technical, software or administrative error, :site may correct the error.',
        'Where an error has resulted in a customer receiving an amount to which they were not entitled, :site may take reasonable steps to correct the account balance, subject to applicable law.',
    ]],
    ['19. No Guarantee of Winnings', [
        'The Welcome Bonus does not guarantee winnings.',
        'Casino games are games of chance and outcomes are not guaranteed.',
    ]],
    ['20. Relationship With Other Terms', [
        'These Bonus Terms must be read together with the :site General Terms and Conditions, Privacy Policy, Responsible Gambling Policy, KYC/AML Policy and individual game rules.',
        'If a provision of these Bonus Terms conflicts with mandatory applicable law or a binding regulatory requirement, the mandatory law or regulatory requirement will prevail.',
    ]],
    ['21. Contact', [
        'Questions regarding the Welcome Bonus may be directed to:',
        'Viper Casino. Legal entity: Viper Casino LTD. Registered address: Hong Kong, Central District, str. AIE Bldg.',
        'Email: support@vpcasino.net',
        'Website: vpcasino.net',
    ]],
];

const pt_BR = [
    ['1. Bônus de Boas-vindas', [
        'Novos clientes elegíveis do :site podem receber um bônus de :bonus% sobre o depósito, até um bônus máximo de :bonusmax.',
        'Exemplos:',
        'Deposite :min e receba um bônus do mesmo valor.',
        'Deposite :bonusmax e receba um bônus de :bonusmax.',
        'Deposite mais de :bonusmax e o bônus continua sendo :bonusmax.',
        'O Bônus de Boas-vindas máximo disponível para um cliente é :bonusmax.',
        'O depósito mínimo qualificável é :min.',
    ]],
    ['2. Moedas Elegíveis', [
        'O Bônus de Boas-vindas pode ser resgatado com as seguintes criptomoedas suportadas: Bitcoin (BTC), Ethereum (ETH), USD Coin (USDC) e Dogecoin (DOGE).',
        'Quando um depósito em criptomoeda é convertido na moeda contábil de bônus do cassino, o :site usa a taxa de câmbio exibida pelo cassino ou pelo sistema de pagamento no momento em que o depósito é creditado.',
        'Taxas de transação em blockchain, taxas de rede ou taxas de pagamento de terceiros não estão incluídas no valor do bônus.',
    ]],
    ['3. Requisito de Rollover', [
        'O Bônus de Boas-vindas está sujeito a um requisito de rollover de :rollover vezes o valor do bônus.',
        'Apenas apostas feitas nos slots elegíveis do :site listados na Seção 4 contam para o requisito de rollover.',
        'Por exemplo, um bônus de :min tem um requisito de rollover de :min multiplicado por :rollover.',
        'O requisito de rollover se aplica ao valor do bônus, não ao total de depósito mais bônus.',
    ]],
    ['4. Jogos Elegíveis', [
        'No projeto-piloto inicial do :site, as apostas que contam para o Bônus de Boas-vindas são apenas nos 12 jogos designados a seguir:',
        'Fortune Tiger, Queen of Bounty, Song Kran Party, Bikini Paradise, Phoenix Rises, Fortune Panda, Hood vs Wolf, Treasures of Aztec, Jack Frost\'s Winter, Fortune OX, Fortune Mouse e Fortune Rabbit.',
        'Jogos não listados acima não contribuem para o cumprimento do requisito de rollover do Bônus de Boas-vindas.',
        'O :site pode alterar os jogos disponíveis de tempos em tempos, observada a legislação aplicável e os termos do jogo e da promoção.',
    ]],
    ['5. Validade do Bônus', [
        'O Bônus de Boas-vindas precisa ser totalmente apostado em até :days dias corridos a partir do momento em que o bônus é creditado na conta do cliente.',
        'Se o requisito de rollover não for cumprido quando esse prazo terminar, o saldo promocional restante expira.',
        'Qualquer saldo em dinheiro real que permaneça na conta do cliente continua sujeito aos Termos e Condições Gerais e aos requisitos de saque aplicáveis.',
    ]],
    ['6. Aposta Máxima', [
        'Enquanto o Bônus de Boas-vindas estiver ativo, a aposta máxima qualificável é :maxbet por rodada, ou o valor equivalente em criptomoeda.',
        'O cliente que exceder a aposta máxima permitida pode ter o bônus e os ganhos promocionais associados cancelados, quando permitido pela legislação aplicável e pelos Termos e Condições Gerais.',
        'A regra de aposta máxima não se aplica quando o cliente joga exclusivamente com dinheiro real sem restrições, depois de o bônus ter sido perdido ou concluído, observadas as regras do jogo.',
    ]],
    ['7. Um Bônus de Boas-vindas por Cliente', [
        'O Bônus de Boas-vindas é limitado a um bônus por pessoa.',
        'O :site também pode considerar contas ligadas por fatores que incluem, entre outros: residência; endereço; aparelho; endereço IP; meio de pagamento; carteira de criptomoeda; telefone; e-mail; ou outras informações de conta ou identidade.',
        'É proibido abrir várias contas com o objetivo de obter Bônus de Boas-vindas adicionais.',
    ]],
    ['8. Nada de Abuso de Bônus', [
        'Os clientes não devem tentar manipular nem abusar do Bônus de Boas-vindas.',
        'Exemplos de atividade proibida incluem: criar várias contas; usar a identidade ou a conta de outra pessoa; coordenar contas para obter vários bônus; usar informações de pagamento fraudulentas; explorar erros de software, técnicos ou de preço; usar sistemas automatizados ou bots; tentar contornar restrições de rollover ou promocionais; ou qualquer outra conduta destinada a obter vantagem promocional por engano ou por contornar estes termos.',
        'Quando o :site identificar, de forma razoável, suspeita de abuso de bônus, pode suspender a conta enquanto conduz uma investigação, sempre observada a legislação aplicável e os direitos contratuais e regulatórios do cliente.',
    ]],
    ['9. Verificação e KYC', [
        'O :site pode exigir verificação de identidade, idade, endereço, origem dos recursos ou outra verificação antes de permitir que o cliente faça um saque.',
        'O cliente pode precisar concluir as verificações aplicáveis de Conheça Seu Cliente ("KYC"), prevenção à lavagem de dinheiro e jogo responsável.',
        'Cumprir o requisito de rollover não garante aprovação imediata do saque enquanto houver verificação legalmente exigida pendente.',
    ]],
    ['10. Saques', [
        'Depois de cumprido o requisito de rollover aplicável, os ganhos promocionais podem se tornar elegíveis para saque, sujeito a: cumprimento dos requisitos de rollover aplicáveis; verificação bem-sucedida da conta e da identidade; cumprimento dos requisitos aplicáveis de prevenção à lavagem de dinheiro e de jogo responsável; cumprimento dos Termos e Condições Gerais; qualquer valor mínimo de saque aplicável; e quaisquer requisitos aplicáveis de criptomoeda ou de rede.',
        'O :site não exigirá que o cliente aposte o dinheiro real depositado apenas porque esses recursos foram depositados junto com um bônus promocional, exceto quando uma condição promocional específica e lícita se aplicar expressamente.',
    ]],
    ['11. Saques em Criptomoeda', [
        'Os saques normalmente são processados em uma criptomoeda e rede suportadas, selecionadas pelo :site.',
        'É responsabilidade do cliente informar um endereço de carteira correto e selecionar a rede suportada correta.',
        'O :site não se responsabiliza por criptomoeda enviada a um endereço ou rede incorretos ou não suportados quando a transação não puder ser recuperada.',
        'Transações em blockchain podem ser irreversíveis.',
        'Taxas de rede ou de processamento aplicáveis podem ser deduzidas dos saques quando informadas antes do processamento da transação.',
    ]],
    ['12. Valor da Criptomoeda', [
        'Como os preços das criptomoedas oscilam, o valor de uma transação em criptomoeda pode mudar entre o depósito, a aposta e o saque.',
        'O cassino pode determinar o valor equivalente de uma transação em criptomoeda usando a taxa de câmbio exibida pelo processador de pagamentos ou pelo sistema do cassino no momento da transação.',
    ]],
    ['13. Depósitos e Saques', [
        'O :site reserva-se o direito de estabelecer limites mínimos e máximos de depósito e saque para cada criptomoeda suportada.',
        'O processamento de pagamentos pode estar sujeito a verificação adicional, confirmações de rede, condições da blockchain e procedimentos de conformidade aplicáveis.',
    ]],
    ['14. Elegibilidade e Jurisdições Restritas', [
        'O Bônus de Boas-vindas está disponível apenas para clientes legalmente autorizados a participar de jogos on-line na sua jurisdição e que atendam aos requisitos de elegibilidade do :site.',
        'O :site não autoriza o acesso ao serviço a partir de jurisdições em que o jogo on-line ou a prestação de serviços de jogo remoto sejam proibidos ou exijam licença ou autorização que o :site não possua.',
        'A lista atual de países restritos deve ser exibida separadamente no site do :site e pode ser atualizada conforme mudem as exigências legais e regulatórias.',
        'É responsabilidade do cliente determinar se a sua participação é lícita no local onde está.',
        'O :site não deve usar esta cláusula como substituto da definição de onde tem permissão legal para aceitar clientes.',
    ]],
    ['15. Idade Mínima', [
        'Os clientes precisam ter a idade mínima legal para jogar na sua jurisdição e devem fornecer informações corretas no cadastro.',
        'O :site não fornecerá conscientemente serviços de jogo a pessoas abaixo da idade legal aplicável.',
    ]],
    ['16. Jogo Responsável', [
        'O Bônus de Boas-vindas é uma oferta promocional e não pretende incentivar o cliente a jogar além das suas possibilidades.',
        'O cliente deve jogar apenas com um valor que possa perder.',
        'Informações sobre limites de depósito, limites de aposta, autoexclusão, restrições de conta e outras ferramentas de jogo responsável estão disponíveis na Política de Jogo Responsável do :site.',
    ]],
    ['17. Mudanças na Promoção', [
        'O :site pode retirar, alterar ou encerrar o Bônus de Boas-vindas quando for razoavelmente necessário, inclusive por exigências legais, regulatórias, técnicas, de segurança ou operacionais.',
        'Quando a lei exigir, os clientes receberão aviso adequado sobre mudanças relevantes.',
        'As mudanças não removerão retroativamente direitos já adquiridos, exceto quando permitido ou exigido pela legislação aplicável.',
    ]],
    ['18. Erros e Problemas Técnicos', [
        'Se o Bônus de Boas-vindas for creditado incorretamente por erro técnico, de software ou administrativo, o :site pode corrigir o erro.',
        'Quando um erro tiver resultado no recebimento de um valor a que o cliente não tinha direito, o :site pode tomar medidas razoáveis para corrigir o saldo da conta, observada a legislação aplicável.',
    ]],
    ['19. Sem Garantia de Ganhos', [
        'O Bônus de Boas-vindas não garante ganhos.',
        'Os jogos de cassino são jogos de azar e os resultados não são garantidos.',
    ]],
    ['20. Relação com Outros Termos', [
        'Estes Termos de Bônus devem ser lidos junto com os Termos e Condições Gerais, a Política de Privacidade, a Política de Jogo Responsável, a Política de KYC/PLD e as regras de cada jogo do :site.',
        'Se uma disposição destes Termos de Bônus conflitar com norma legal imperativa ou exigência regulatória vinculante, prevalecerá a norma legal ou a exigência regulatória.',
    ]],
    ['21. Contato', [
        'Dúvidas sobre o Bônus de Boas-vindas podem ser encaminhadas para:',
        'Viper Casino. Pessoa jurídica: Viper Casino LTD. Endereço registrado: Hong Kong, Central District, str. AIE Bldg.',
        'E-mail: support@vpcasino.net',
        'Site: vpcasino.net',
    ]],
];

const es = [
    ['1. Bono de Bienvenida', [
        'Los nuevos clientes elegibles de :site pueden recibir un bono del :bonus% sobre el depósito, hasta un bono máximo de :bonusmax.',
        'Ejemplos:',
        'Deposita :min y recibe un bono del mismo importe.',
        'Deposita :bonusmax y recibe un bono de :bonusmax.',
        'Deposita más de :bonusmax y el bono sigue siendo :bonusmax.',
        'El Bono de Bienvenida máximo disponible para un cliente es :bonusmax.',
        'El depósito mínimo que califica es :min.',
    ]],
    ['2. Monedas Elegibles', [
        'El Bono de Bienvenida puede reclamarse con las siguientes criptomonedas admitidas: Bitcoin (BTC), Ethereum (ETH), USD Coin (USDC) y Dogecoin (DOGE).',
        'Cuando un depósito en criptomoneda se convierte a la moneda contable de bonos del casino, :site usa el tipo de cambio que muestra el casino o el sistema de pago en el momento en que se acredita el depósito.',
        'Las comisiones de transacción en blockchain, las comisiones de red o las comisiones de pago de terceros no se incluyen en el importe del bono.',
    ]],
    ['3. Requisito de Apuesta', [
        'El Bono de Bienvenida está sujeto a un requisito de apuesta de :rollover veces el importe del bono.',
        'Solo las apuestas realizadas en las tragaperras elegibles de :site indicadas en la Sección 4 cuentan para el requisito de apuesta.',
        'Por ejemplo, un bono de :min conlleva un requisito de apuesta de :min multiplicado por :rollover.',
        'El requisito de apuesta se aplica al importe del bono, no al total del depósito más el bono.',
    ]],
    ['4. Juegos Elegibles', [
        'Para el piloto inicial de :site, las apuestas que cuentan para el Bono de Bienvenida solo están disponibles en los 12 juegos designados siguientes:',
        'Fortune Tiger, Queen of Bounty, Song Kran Party, Bikini Paradise, Phoenix Rises, Fortune Panda, Hood vs Wolf, Treasures of Aztec, Jack Frost\'s Winter, Fortune OX, Fortune Mouse y Fortune Rabbit.',
        'Los juegos no indicados arriba no contribuyen a completar el requisito de apuesta del Bono de Bienvenida.',
        ':site puede cambiar los juegos disponibles cada cierto tiempo, con sujeción a la ley aplicable y a los términos del juego y de la promoción.',
    ]],
    ['5. Validez del Bono', [
        'El Bono de Bienvenida debe apostarse por completo dentro de los :days días naturales siguientes al momento en que se acredita en la cuenta del cliente.',
        'Si el requisito de apuesta no se ha completado cuando vence ese plazo, el saldo promocional restante caduca.',
        'Cualquier saldo de dinero real que quede en la cuenta del cliente seguirá sujeto a los Términos y Condiciones Generales y a los requisitos de retiro aplicables.',
    ]],
    ['6. Apuesta Máxima', [
        'Mientras el Bono de Bienvenida está activo, la apuesta máxima que califica es :maxbet por giro, o su valor equivalente en criptomoneda.',
        'Al cliente que supere la apuesta máxima permitida se le puede cancelar el bono y las ganancias promocionales asociadas, cuando lo permitan la ley aplicable y los Términos y Condiciones Generales.',
        'La regla de apuesta máxima no se aplica cuando el cliente juega exclusivamente con dinero real sin restricciones, después de que el bono se haya perdido o completado, con sujeción a las reglas del juego.',
    ]],
    ['7. Un Bono de Bienvenida por Cliente', [
        'El Bono de Bienvenida se limita a un bono por persona.',
        ':site también puede considerar cuentas relacionadas por factores que incluyen, entre otros: domicilio; dirección; dispositivo; dirección IP; método de pago; monedero de criptomoneda; número de teléfono; correo electrónico; u otra información de cuenta o identidad.',
        'Está prohibido abrir varias cuentas con el fin de obtener Bonos de Bienvenida adicionales.',
    ]],
    ['8. Sin Abuso del Bono', [
        'Los clientes no deben intentar manipular ni abusar del Bono de Bienvenida.',
        'Ejemplos de actividad prohibida: crear varias cuentas; usar la identidad o la cuenta de otra persona; coordinar cuentas para obtener varios bonos; usar información de pago fraudulenta; explotar errores de software, técnicos o de precio; usar sistemas automatizados o bots; intentar eludir restricciones de apuesta o promocionales; o cualquier otra conducta dirigida a obtener una ventaja promocional mediante engaño o elusión de estos términos.',
        'Cuando :site identifique razonablemente una sospecha de abuso del bono, podrá suspender la cuenta mientras realiza una investigación, siempre con sujeción a la ley aplicable y a los derechos contractuales y regulatorios del cliente.',
    ]],
    ['9. Verificación y KYC', [
        ':site puede exigir verificación de identidad, edad, dirección, origen de los fondos u otra verificación antes de permitir que un cliente retire fondos.',
        'Puede requerirse que el cliente complete las comprobaciones aplicables de Conozca a su Cliente ("KYC"), prevención del blanqueo de capitales y juego responsable.',
        'Completar el requisito de apuesta no garantiza la aprobación inmediata del retiro mientras quede pendiente una verificación legalmente exigida.',
    ]],
    ['10. Retiros', [
        'Una vez completado el requisito de apuesta aplicable, las ganancias promocionales pueden pasar a ser retirables, con sujeción a: el cumplimiento de los requisitos de apuesta aplicables; la verificación correcta de la cuenta y la identidad; el cumplimiento de los requisitos aplicables de prevención del blanqueo de capitales y de juego responsable; el cumplimiento de los Términos y Condiciones Generales; cualquier importe mínimo de retiro aplicable; y cualquier requisito aplicable de criptomoneda o de red.',
        ':site no exigirá que un cliente apueste el dinero real depositado únicamente porque esos fondos se depositaron junto con un bono promocional, salvo que se aplique expresamente una condición promocional específica y lícita.',
    ]],
    ['11. Retiros en Criptomoneda', [
        'Los retiros se procesan normalmente con una criptomoneda y una red soportadas, seleccionadas por :site.',
        'Es responsabilidad del cliente facilitar una dirección de monedero correcta y seleccionar la red soportada correcta.',
        ':site no es responsable de la criptomoneda enviada a una dirección o red incorrecta o no soportada cuando la transacción no se pueda recuperar.',
        'Las transacciones en blockchain pueden ser irreversibles.',
        'Las comisiones de red o de procesamiento aplicables pueden deducirse de los retiros cuando se informen antes de procesar la transacción.',
    ]],
    ['12. Valor de la Criptomoneda', [
        'Como los precios de las criptomonedas fluctúan, el valor de una transacción en criptomoneda puede cambiar entre el depósito, la apuesta y el retiro.',
        'El casino puede determinar el valor equivalente de una transacción en criptomoneda usando el tipo de cambio que muestre su procesador de pagos o el sistema del casino en el momento de la transacción.',
    ]],
    ['13. Depósitos y Retiros', [
        ':site se reserva el derecho de establecer límites mínimos y máximos de depósito y retiro para cada criptomoneda soportada.',
        'El procesamiento de pagos puede estar sujeto a verificación adicional, confirmaciones de red, condiciones de la blockchain y procedimientos de cumplimiento aplicables.',
    ]],
    ['14. Elegibilidad y Jurisdicciones Restringidas', [
        'El Bono de Bienvenida está disponible solo para clientes a los que la ley de su jurisdicción permite participar en juego en línea y que cumplen los requisitos de elegibilidad de :site.',
        ':site no autoriza a los clientes a acceder al servicio desde jurisdicciones donde el juego en línea o la prestación de servicios de juego remoto estén prohibidos o exijan una licencia o permiso que :site no posee.',
        'La lista actual de países restringidos debe mostrarse por separado en el sitio web de :site y puede actualizarse a medida que cambien los requisitos legales y regulatorios.',
        'Es responsabilidad del cliente determinar si su participación es lícita en su ubicación.',
        ':site no debe usar esta cláusula como sustituto de determinar dónde tiene permitido legalmente aceptar clientes.',
    ]],
    ['15. Edad Mínima', [
        'Los clientes deben cumplir la edad mínima legal para jugar en su jurisdicción y deben facilitar información correcta durante el registro.',
        ':site no prestará a sabiendas servicios de juego a personas por debajo de la edad legal aplicable.',
    ]],
    ['16. Juego Responsable', [
        'El Bono de Bienvenida es una oferta promocional y no pretende animar a los clientes a jugar por encima de sus posibilidades.',
        'Los clientes solo deben jugar con dinero que puedan permitirse perder.',
        'La información sobre límites de depósito, límites de apuesta, autoexclusión, restricciones de cuenta y otras herramientas de juego responsable está disponible en la Política de Juego Responsable de :site.',
    ]],
    ['17. Cambios en la Promoción', [
        ':site puede retirar, modificar o suspender el Bono de Bienvenida cuando sea razonablemente necesario, incluso por requisitos legales, regulatorios, técnicos, de seguridad u operativos.',
        'Cuando la ley lo exija, se dará a los clientes un aviso adecuado de los cambios relevantes.',
        'Los cambios no eliminarán retroactivamente derechos ya adquiridos, salvo cuando lo permita o exija la ley aplicable.',
    ]],
    ['18. Errores y Problemas Técnicos', [
        'Si el Bono de Bienvenida se acredita incorrectamente por un error técnico, de software o administrativo, :site puede corregir el error.',
        'Cuando un error haya provocado que un cliente reciba un importe al que no tenía derecho, :site puede tomar medidas razonables para corregir el saldo de la cuenta, con sujeción a la ley aplicable.',
    ]],
    ['19. Sin Garantía de Ganancias', [
        'El Bono de Bienvenida no garantiza ganancias.',
        'Los juegos de casino son juegos de azar y los resultados no están garantizados.',
    ]],
    ['20. Relación con Otros Términos', [
        'Estos Términos del Bono deben leerse junto con los Términos y Condiciones Generales, la Política de Privacidad, la Política de Juego Responsable, la Política de KYC/AML y las reglas de cada juego de :site.',
        'Si una disposición de estos Términos del Bono entra en conflicto con una norma legal imperativa o un requisito regulatorio vinculante, prevalecerá la norma legal o el requisito regulatorio.',
    ]],
    ['21. Contacto', [
        'Las consultas sobre el Bono de Bienvenida pueden dirigirse a:',
        'Viper Casino. Entidad legal: Viper Casino LTD. Domicilio social: Hong Kong, Central District, str. AIE Bldg.',
        'Correo electrónico: support@vpcasino.net',
        'Sitio web: vpcasino.net',
    ]],
];

const fr = [
    ['1. Bonus de bienvenue', [
        'Les nouveaux clients éligibles de :site peuvent recevoir un bonus de :bonus% sur leur dépôt, dans la limite d\'un bonus maximal de :bonusmax.',
        'Exemples :',
        'Déposez :min et recevez un bonus du même montant.',
        'Déposez :bonusmax et recevez un bonus de :bonusmax.',
        'Déposez plus de :bonusmax et le bonus reste de :bonusmax.',
        'Le Bonus de bienvenue maximal pour un client est de :bonusmax.',
        'Le dépôt minimum qui donne droit au bonus est de :min.',
    ]],
    ['2. Monnaies éligibles', [
        'Le Bonus de bienvenue peut être réclamé avec les cryptomonnaies prises en charge suivantes : Bitcoin (BTC), Ethereum (ETH), USD Coin (USDC) et Dogecoin (DOGE).',
        'Lorsqu\'un dépôt en cryptomonnaie est converti dans la monnaie de comptabilisation des bonus du casino, :site utilise le taux de change affiché par le casino ou le système de paiement au moment où le dépôt est crédité.',
        'Les frais de transaction blockchain, les frais de réseau et les frais de paiement de tiers ne sont pas compris dans le montant du bonus.',
    ]],
    ['3. Conditions de mise', [
        'Le Bonus de bienvenue est soumis à une condition de mise de :rollover fois le montant du bonus.',
        'Seules les mises placées sur les machines à sous éligibles de :site listées à la Section 4 comptent pour la condition de mise.',
        'Par exemple, un bonus de :min entraîne une condition de mise de :min multiplié par :rollover.',
        'La condition de mise porte sur le montant du bonus, et non sur le total du dépôt et du bonus.',
    ]],
    ['4. Jeux éligibles', [
        'Pour la phase pilote de :site, les mises comptant pour le Bonus de bienvenue ne sont possibles que sur les 12 jeux désignés suivants :',
        'Fortune Tiger, Queen of Bounty, Song Kran Party, Bikini Paradise, Phoenix Rises, Fortune Panda, Hood vs Wolf, Treasures of Aztec, Jack Frost\'s Winter, Fortune OX, Fortune Mouse et Fortune Rabbit.',
        'Les jeux non listés ci-dessus ne comptent pas pour la réalisation de la condition de mise du Bonus de bienvenue.',
        ':site peut modifier les jeux proposés de temps à autre, sous réserve de la loi applicable et des conditions du jeu et de la promotion.',
    ]],
    ['5. Validité du bonus', [
        'Le Bonus de bienvenue doit être intégralement misé dans les :days jours calendaires suivant son crédit sur le compte du client.',
        'Si la condition de mise n\'est pas remplie à l\'expiration de ce délai, le solde promotionnel restant expire.',
        'Tout solde en argent réel restant sur le compte du client demeure soumis aux Conditions générales et aux exigences de retrait applicables.',
    ]],
    ['6. Mise maximale', [
        'Tant que le Bonus de bienvenue est actif, la mise maximale admissible est de :maxbet par tour, ou la valeur équivalente en cryptomonnaie.',
        'Un client qui dépasse la mise maximale autorisée peut voir son bonus et les gains promotionnels associés annulés, lorsque la loi applicable et les Conditions générales le permettent.',
        'La règle de mise maximale ne s\'applique pas lorsque le client joue exclusivement avec de l\'argent réel non soumis à restriction, après que le bonus a été perdu ou achevé, sous réserve des règles du jeu.',
    ]],
    ['7. Un bonus de bienvenue par client', [
        'Le Bonus de bienvenue est limité à un bonus par personne.',
        ':site peut aussi considérer comme liés des comptes partageant notamment : le foyer ; l\'adresse ; l\'appareil ; l\'adresse IP ; le moyen de paiement ; le portefeuille de cryptomonnaie ; le numéro de téléphone ; l\'adresse e-mail ; ou d\'autres informations de compte ou d\'identité.',
        'Ouvrir plusieurs comptes afin d\'obtenir des Bonus de bienvenue supplémentaires est interdit.',
    ]],
    ['8. Pas d\'abus de bonus', [
        'Les clients ne doivent pas tenter de manipuler ni d\'abuser du Bonus de bienvenue.',
        'Exemples d\'activité interdite : créer plusieurs comptes ; utiliser l\'identité ou le compte d\'une autre personne ; coordonner des comptes pour obtenir plusieurs bonus ; utiliser des informations de paiement frauduleuses ; exploiter des erreurs logicielles, techniques ou de tarification ; utiliser des systèmes automatisés ou des bots ; tenter de contourner les restrictions de mise ou promotionnelles ; ou tout autre comportement visant à obtenir un avantage promotionnel par la tromperie ou le contournement des présentes conditions.',
        'Lorsque :site identifie raisonnablement un soupçon d\'abus de bonus, il peut suspendre le compte concerné pendant l\'enquête, toujours sous réserve de la loi applicable et des droits contractuels et réglementaires du client.',
    ]],
    ['9. Vérification et KYC', [
        ':site peut exiger une vérification de l\'identité, de l\'âge, de l\'adresse, de l\'origine des fonds ou une autre vérification avant d\'autoriser un retrait.',
        'Le client peut devoir effectuer les contrôles applicables de connaissance du client (« KYC »), de lutte contre le blanchiment et de jeu responsable.',
        'Remplir la condition de mise ne garantit pas l\'approbation immédiate d\'un retrait tant qu\'une vérification légalement requise reste en suspens.',
    ]],
    ['10. Retraits', [
        'Une fois la condition de mise applicable remplie, les gains promotionnels peuvent devenir retirables, sous réserve : de la réalisation des conditions de mise applicables ; de la vérification du compte et de l\'identité ; du respect des exigences applicables en matière de lutte contre le blanchiment et de jeu responsable ; du respect des Conditions générales ; de tout montant minimal de retrait applicable ; et de toute exigence applicable de cryptomonnaie ou de réseau.',
        ':site n\'exigera pas d\'un client qu\'il mise les fonds en argent réel déposés au seul motif qu\'ils l\'ont été en même temps qu\'un bonus promotionnel, sauf si une condition promotionnelle spécifique et licite s\'applique expressément.',
    ]],
    ['11. Retraits en cryptomonnaie', [
        'Les retraits sont normalement traités dans une cryptomonnaie et sur un réseau pris en charge, choisis par :site.',
        'Il appartient au client de fournir une adresse de portefeuille correcte et de choisir le bon réseau pris en charge.',
        ':site n\'est pas responsable des cryptomonnaies envoyées à une adresse ou un réseau incorrect ou non pris en charge lorsque la transaction ne peut pas être récupérée.',
        'Les transactions blockchain peuvent être irréversibles.',
        'Les frais de réseau ou de traitement applicables peuvent être déduits des retraits lorsqu\'ils sont indiqués avant le traitement de la transaction.',
    ]],
    ['12. Valeur des cryptomonnaies', [
        'Les prix des cryptomonnaies fluctuant, la valeur d\'une transaction en cryptomonnaie peut changer entre le dépôt, la mise et le retrait.',
        'Le casino peut déterminer la valeur équivalente d\'une transaction en cryptomonnaie en utilisant le taux de change affiché par son prestataire de paiement ou par le système du casino au moment de la transaction.',
    ]],
    ['13. Dépôts et retraits', [
        ':site se réserve le droit de fixer des limites minimales et maximales de dépôt et de retrait pour chaque cryptomonnaie prise en charge.',
        'Le traitement des paiements peut être soumis à des vérifications supplémentaires, à des confirmations de réseau, aux conditions de la blockchain et aux procédures de conformité applicables.',
    ]],
    ['14. Éligibilité et juridictions restreintes', [
        'Le Bonus de bienvenue n\'est accessible qu\'aux clients légalement autorisés à participer à des jeux d\'argent en ligne dans leur juridiction et qui remplissent les conditions d\'éligibilité de :site.',
        ':site n\'autorise pas l\'accès au service depuis les juridictions où le jeu en ligne ou la fourniture de services de jeu à distance est interdit ou exige une licence ou une autorisation que :site ne détient pas.',
        'La liste des pays restreints en vigueur doit être affichée séparément sur le site de :site et peut être mise à jour selon l\'évolution des exigences légales et réglementaires.',
        'Il appartient au client de déterminer si sa participation est licite là où il se trouve.',
        ':site ne doit pas utiliser cette clause en remplacement de la détermination des endroits où il est légalement autorisé à accepter des clients.',
    ]],
    ['15. Âge requis', [
        'Les clients doivent avoir l\'âge légal minimum pour jouer dans leur juridiction et fournir des informations exactes lors de l\'inscription.',
        ':site ne fournira pas sciemment de services de jeu à des personnes n\'ayant pas l\'âge légal applicable.',
    ]],
    ['16. Jeu responsable', [
        'Le Bonus de bienvenue est une offre promotionnelle et n\'a pas pour but d\'encourager les clients à jouer au-delà de leurs moyens.',
        'Les clients ne devraient jouer qu\'avec des sommes qu\'ils peuvent se permettre de perdre.',
        'Les informations sur les limites de dépôt, les limites de mise, l\'auto-exclusion, les restrictions de compte et les autres outils de jeu responsable figurent dans la Politique de jeu responsable de :site.',
    ]],
    ['17. Modifications de la promotion', [
        ':site peut retirer, modifier ou interrompre le Bonus de bienvenue lorsque cela est raisonnablement nécessaire, y compris pour des raisons légales, réglementaires, techniques, de sécurité ou opérationnelles.',
        'Lorsque la loi l\'exige, les clients seront informés de manière appropriée des changements importants.',
        'Les modifications ne supprimeront pas rétroactivement des droits déjà acquis, sauf lorsque la loi applicable le permet ou l\'exige.',
    ]],
    ['18. Erreurs et problèmes techniques', [
        'Si le Bonus de bienvenue est crédité de manière incorrecte à la suite d\'une erreur technique, logicielle ou administrative, :site peut corriger l\'erreur.',
        'Lorsqu\'une erreur a conduit un client à recevoir un montant auquel il n\'avait pas droit, :site peut prendre des mesures raisonnables pour corriger le solde du compte, sous réserve de la loi applicable.',
    ]],
    ['19. Aucune garantie de gain', [
        'Le Bonus de bienvenue ne garantit aucun gain.',
        'Les jeux de casino sont des jeux de hasard et les résultats ne sont pas garantis.',
    ]],
    ['20. Articulation avec les autres conditions', [
        'Les présentes Conditions de bonus doivent être lues avec les Conditions générales, la Politique de confidentialité, la Politique de jeu responsable, la Politique KYC/LCB et les règles de chaque jeu de :site.',
        'Si une disposition des présentes Conditions de bonus entre en conflit avec une règle légale impérative ou une exigence réglementaire contraignante, la règle légale ou l\'exigence réglementaire prévaut.',
    ]],
    ['21. Contact', [
        'Les questions relatives au Bonus de bienvenue peuvent être adressées à :',
        'Viper Casino. Entité juridique : Viper Casino LTD. Siège social : Hong Kong, Central District, str. AIE Bldg.',
        'E-mail : support@vpcasino.net',
        'Site web : vpcasino.net',
    ]],
];

const de = [
    ['1. Willkommensbonus', [
        'Neue berechtigte Kundinnen und Kunden von :site können einen Einzahlungsbonus von :bonus% bis zu einem Höchstbonus von :bonusmax erhalten.',
        'Beispiele:',
        'Zahlen Sie :min ein und erhalten Sie einen Bonus in gleicher Höhe.',
        'Zahlen Sie :bonusmax ein und erhalten Sie einen Bonus von :bonusmax.',
        'Zahlen Sie mehr als :bonusmax ein, beträgt der Bonus weiterhin :bonusmax.',
        'Der höchste Willkommensbonus für eine Person beträgt :bonusmax.',
        'Die kleinste Einzahlung, die zum Bonus berechtigt, ist :min.',
    ]],
    ['2. Zugelassene Währungen', [
        'Der Willkommensbonus kann mit den folgenden unterstützten Kryptowährungen beansprucht werden: Bitcoin (BTC), Ethereum (ETH), USD Coin (USDC) und Dogecoin (DOGE).',
        'Wird eine Krypto-Einzahlung in die Bonus-Verrechnungswährung des Casinos umgerechnet, verwendet :site den Wechselkurs, den das Casino oder das Zahlungssystem zum Zeitpunkt der Gutschrift anzeigt.',
        'Blockchain-Transaktionsgebühren, Netzwerkgebühren und Zahlungsgebühren Dritter sind im Bonusbetrag nicht enthalten.',
    ]],
    ['3. Umsatzbedingung', [
        'Für den Willkommensbonus gilt eine Umsatzbedingung vom :rollover-Fachen des Bonusbetrags.',
        'Nur Einsätze an den in Abschnitt 4 aufgeführten zugelassenen Slots von :site zählen für die Umsatzbedingung.',
        'Ein Bonus von :min führt beispielsweise zu einer Umsatzbedingung von :min multipliziert mit :rollover.',
        'Die Umsatzbedingung gilt für den Bonusbetrag, nicht für Einzahlung und Bonus zusammen.',
    ]],
    ['4. Zugelassene Spiele', [
        'In der Startphase von :site zählen für den Willkommensbonus nur Einsätze an den folgenden 12 Spielen:',
        'Fortune Tiger, Queen of Bounty, Song Kran Party, Bikini Paradise, Phoenix Rises, Fortune Panda, Hood vs Wolf, Treasures of Aztec, Jack Frost\'s Winter, Fortune OX, Fortune Mouse und Fortune Rabbit.',
        'Nicht aufgeführte Spiele tragen nicht zur Erfüllung der Umsatzbedingung bei.',
        ':site kann das Spieleangebot von Zeit zu Zeit ändern, vorbehaltlich des geltenden Rechts und der jeweiligen Spiel- und Aktionsbedingungen.',
    ]],
    ['5. Gültigkeit des Bonus', [
        'Der Willkommensbonus muss innerhalb von :days Kalendertagen ab der Gutschrift auf dem Konto vollständig umgesetzt werden.',
        'Ist die Umsatzbedingung bei Ablauf dieser Frist nicht erfüllt, verfällt das verbleibende Bonusguthaben.',
        'Verbleibendes echtes Guthaben auf dem Konto unterliegt weiterhin den Allgemeinen Geschäftsbedingungen und den geltenden Auszahlungsanforderungen.',
    ]],
    ['6. Höchsteinsatz', [
        'Solange der Willkommensbonus aktiv ist, beträgt der höchste anrechenbare Einsatz :maxbet pro Drehung oder der entsprechende Gegenwert in Kryptowährung.',
        'Wer den zulässigen Höchsteinsatz überschreitet, dem können der Bonus und die damit erzielten Gewinne gestrichen werden, soweit das geltende Recht und die Allgemeinen Geschäftsbedingungen dies zulassen.',
        'Die Höchsteinsatzregel gilt nicht, wenn ausschließlich mit uneingeschränktem echtem Guthaben gespielt wird, nachdem der Bonus verfallen oder erfüllt ist, vorbehaltlich der Spielregeln.',
    ]],
    ['7. Ein Willkommensbonus pro Person', [
        'Der Willkommensbonus ist auf einen Bonus pro Person begrenzt.',
        ':site kann Konten auch als verbunden ansehen, unter anderem über: Haushalt; Anschrift; Gerät; IP-Adresse; Zahlungsmittel; Krypto-Wallet; Telefonnummer; E-Mail-Adresse; oder andere Konto- oder Identitätsangaben.',
        'Das Eröffnen mehrerer Konten, um weitere Willkommensboni zu erhalten, ist untersagt.',
    ]],
    ['8. Kein Bonusmissbrauch', [
        'Der Willkommensbonus darf nicht manipuliert oder missbraucht werden.',
        'Beispiele unzulässigen Verhaltens: mehrere Konten anlegen; die Identität oder das Konto einer anderen Person nutzen; Konten abstimmen, um mehrere Boni zu erhalten; betrügerische Zahlungsangaben verwenden; Software-, technische oder Preisfehler ausnutzen; automatisierte Systeme oder Bots einsetzen; Umsatz- oder Aktionsbeschränkungen umgehen; oder jedes andere Verhalten, das durch Täuschung oder Umgehung dieser Bedingungen einen Vorteil verschaffen soll.',
        'Erkennt :site begründet einen Verdacht auf Bonusmissbrauch, kann das betroffene Konto für die Dauer der Prüfung gesperrt werden, stets vorbehaltlich des geltenden Rechts und der vertraglichen und regulatorischen Rechte der Kundin oder des Kunden.',
    ]],
    ['9. Überprüfung und KYC', [
        ':site kann vor einer Auszahlung die Überprüfung von Identität, Alter, Anschrift, Mittelherkunft oder weitere Nachweise verlangen.',
        'Es kann erforderlich sein, die geltenden Prüfungen zur Kundenidentifizierung („KYC"), zur Geldwäscheprävention und zum verantwortungsvollen Spiel abzuschließen.',
        'Die Erfüllung der Umsatzbedingung garantiert keine sofortige Freigabe der Auszahlung, solange eine gesetzlich vorgeschriebene Überprüfung aussteht.',
    ]],
    ['10. Auszahlungen', [
        'Nach Erfüllung der geltenden Umsatzbedingung können Aktionsgewinne auszahlbar werden, vorbehaltlich: der Erfüllung der geltenden Umsatzbedingungen; der erfolgreichen Konto- und Identitätsprüfung; der Einhaltung der geltenden Anforderungen zur Geldwäscheprävention und zum verantwortungsvollen Spiel; der Einhaltung der Allgemeinen Geschäftsbedingungen; eines geltenden Mindestauszahlungsbetrags; und geltender Krypto- oder Netzwerkanforderungen.',
        ':site verlangt nicht, eingezahltes echtes Geld allein deshalb umzusetzen, weil es zusammen mit einem Bonus eingezahlt wurde, es sei denn, eine bestimmte und rechtmäßige Aktionsbedingung greift ausdrücklich.',
    ]],
    ['11. Krypto-Auszahlungen', [
        'Auszahlungen werden in der Regel über eine von :site unterstützte Kryptowährung und ein unterstütztes Netzwerk abgewickelt.',
        'Für die Angabe einer richtigen Empfangsadresse und die Wahl des richtigen unterstützten Netzwerks ist die Kundin oder der Kunde verantwortlich.',
        ':site haftet nicht für Kryptowährung, die an eine falsche oder nicht unterstützte Adresse oder ein falsches Netzwerk gesendet wurde, wenn die Transaktion nicht rückholbar ist.',
        'Blockchain-Transaktionen können unumkehrbar sein.',
        'Geltende Netzwerk- oder Bearbeitungsgebühren können von Auszahlungen abgezogen werden, sofern sie vor der Abwicklung ausgewiesen wurden.',
    ]],
    ['12. Wert der Kryptowährung', [
        'Da Kryptokurse schwanken, kann sich der Wert einer Krypto-Transaktion zwischen Einzahlung, Einsatz und Auszahlung ändern.',
        'Das Casino kann den Gegenwert einer Krypto-Transaktion anhand des Wechselkurses bestimmen, den sein Zahlungsdienstleister oder das Casinosystem zum jeweiligen Zeitpunkt anzeigt.',
    ]],
    ['13. Ein- und Auszahlungen', [
        ':site behält sich vor, für jede unterstützte Kryptowährung Mindest- und Höchstbeträge für Ein- und Auszahlungen festzulegen.',
        'Die Zahlungsabwicklung kann zusätzliche Prüfungen, Netzwerkbestätigungen, Blockchain-Bedingungen und geltende Compliance-Verfahren erfordern.',
    ]],
    ['14. Teilnahmeberechtigung und ausgeschlossene Länder', [
        'Der Willkommensbonus steht nur Personen offen, denen die Teilnahme an Online-Glücksspiel in ihrer Rechtsordnung erlaubt ist und die die Teilnahmevoraussetzungen von :site erfüllen.',
        ':site gestattet den Zugriff auf den Dienst nicht aus Rechtsordnungen, in denen Online-Glücksspiel oder das Anbieten von Fernglücksspiel verboten ist oder eine Lizenz erfordert, die :site nicht besitzt.',
        'Die aktuelle Liste ausgeschlossener Länder ist gesondert auf der Website von :site anzuzeigen und kann sich mit den rechtlichen und regulatorischen Anforderungen ändern.',
        'Ob die Teilnahme am eigenen Aufenthaltsort rechtmäßig ist, hat jede Kundin und jeder Kunde selbst zu prüfen.',
        ':site darf diese Klausel nicht als Ersatz dafür verwenden, selbst zu bestimmen, wo es rechtlich Kundinnen und Kunden annehmen darf.',
    ]],
    ['15. Mindestalter', [
        'Kundinnen und Kunden müssen das in ihrer Rechtsordnung geltende Mindestalter für Glücksspiel erfüllen und bei der Registrierung zutreffende Angaben machen.',
        ':site erbringt wissentlich keine Glücksspieldienste für Personen unterhalb des geltenden Mindestalters.',
    ]],
    ['16. Verantwortungsvolles Spielen', [
        'Der Willkommensbonus ist ein Aktionsangebot und soll niemanden dazu verleiten, über die eigenen Verhältnisse zu spielen.',
        'Spielen Sie nur mit Geld, dessen Verlust Sie verkraften können.',
        'Informationen zu Einzahlungs- und Einsatzlimits, Selbstausschluss, Kontobeschränkungen und weiteren Hilfsmitteln finden Sie in der Richtlinie zum verantwortungsvollen Spielen von :site.',
    ]],
    ['17. Änderungen der Aktion', [
        ':site kann den Willkommensbonus zurückziehen, ändern oder einstellen, soweit das vernünftigerweise erforderlich ist, auch aus rechtlichen, regulatorischen, technischen, sicherheitsbezogenen oder betrieblichen Gründen.',
        'Soweit gesetzlich vorgeschrieben, wird über wesentliche Änderungen angemessen informiert.',
        'Änderungen entziehen bereits entstandene Rechte nicht rückwirkend, außer soweit das geltende Recht dies erlaubt oder verlangt.',
    ]],
    ['18. Fehler und technische Störungen', [
        'Wird der Willkommensbonus wegen eines technischen, softwarebedingten oder administrativen Fehlers falsch gutgeschrieben, kann :site den Fehler berichtigen.',
        'Hat ein Fehler dazu geführt, dass ein Betrag ohne Anspruch gutgeschrieben wurde, kann :site angemessene Schritte zur Korrektur des Kontostands unternehmen, vorbehaltlich des geltenden Rechts.',
    ]],
    ['19. Keine Gewinngarantie', [
        'Der Willkommensbonus garantiert keine Gewinne.',
        'Casinospiele sind Glücksspiele; Ergebnisse sind nicht garantiert.',
    ]],
    ['20. Verhältnis zu anderen Bedingungen', [
        'Diese Bonusbedingungen sind zusammen mit den Allgemeinen Geschäftsbedingungen, der Datenschutzerklärung, der Richtlinie zum verantwortungsvollen Spielen, der KYC/AML-Richtlinie und den einzelnen Spielregeln von :site zu lesen.',
        'Widerspricht eine Bestimmung dieser Bonusbedingungen zwingendem Recht oder einer verbindlichen regulatorischen Anforderung, geht das zwingende Recht oder die regulatorische Anforderung vor.',
    ]],
    ['21. Kontakt', [
        'Fragen zum Willkommensbonus richten Sie bitte an:',
        'Viper Casino. Rechtsträger: Viper Casino LTD. Eingetragene Anschrift: Hong Kong, Central District, str. AIE Bldg.',
        'E-Mail: support@vpcasino.net',
        'Website: vpcasino.net',
    ]],
];

export default { en, pt_BR, es, fr, de };
