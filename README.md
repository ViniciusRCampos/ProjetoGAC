# ProjetoGAC

Este projeto é uma aplicação web desenvolvida como parte de um teste técnico. 
Ele permite que usuários realizem operações financeiras, como depósitos, transferências e recebimentos, além de gerenciar saldo e reverter operações em caso de inconsistências ou solicitações.

## Funcionalidades

1. **Cadastro de Usuários**
   - Permite que novos usuários se cadastrem no sistema para acessar as funcionalidades financeiras.

2. **Autenticação**
   - Implementa login seguro para os usuários, garantindo que apenas pessoas autorizadas possam acessar suas contas.

3. **Operações Financeiras**
   - **Depósito**:
     - Usuários podem depositar dinheiro em suas contas.
     - Caso o saldo esteja negativo, o valor do depósito será utilizado para compensar o débito antes de adicionar ao saldo disponível.
   - **Transferência**:
     - Usuários podem transferir dinheiro para outras contas.
     - A transferência é permitida apenas com saldo disponível suficiente.
   - **Recebimento**:
     - Permite que usuários recebam dinheiro de outros usuários por meio de transferências.

4. **Validação de Saldo**
   - Antes de uma transferência, o sistema verifica se o usuário remetente possui saldo suficiente.

5. **Reversão de Operações**
   - Todas as operações financeiras (depósitos e transferências) podem ser revertidas em casos de:
     - Solicitação do usuário.
     - Identificação de inconsistências no sistema.

## Tecnologias Utilizadas

- Laravel 9x
- PHP 8.0
- MySQL
- Docker
- Node.js/NPM
- Vite (para gerenciamento de assets)

## Requisitos para Instalação
- PHP 8.x ou superior
- Composer
- MySQL
- Node.js e NPM

## Instalação

1. **Clone o Repositório**:
   ```bash
   git clone https://github.com/ViniciusRCampos/ProjetoGAC.git
   cd ProjectGAC
   ```

2. **Instale as Dependências do Backend**:
   ```bash
   composer install
   ```

3. **Instale as Dependências do Frontend**:
   ```bash
   npm install
   ```

4. **Configure o Arquivo `.env`**:
   - Copie o arquivo de exemplo:
     ```bash
     cp .env.example .env
     ```
   - Configure as variáveis de ambiente, como conexão com o banco de dados.

5. **Gere a chave da aplicação:**

   ```bash
   php artisan key:generate
   ```

6. **Execute as Migrações**:
   ```bash
   php artisan migrate
   ```

7. **Compile os Assets**:
   ```bash
   npm run dev
   ```

8. **Inicie o Servidor Local**:
   ```bash
   php artisan serve
   ```

Acesse o sistema no navegador em: [http://localhost:8000](http://localhost:8000)

---


## Uso

Após iniciar o servidor, você pode interagir com a aplicação acessando o endereço fornecido. 
Na tela inicial é possível realizar o login ou criar uma nova conta.

Para facilitar o entendimento do site existem 2 usuários de testes com registros criados.

Na Home podemos ver no topo da página o logo do sistema, o nome do usuário, seu saldo e o número referente a sua conta.

Na página de Extrato é possível ver todas as operações realizadas pela conta e filtrar elas por data, tipo de operação e se está processada ou pendente.

Na página de operações existem 3 opções de operações sendo elas:

1. **Depositar** 
    - Operação solicita apenas o valor para deposito.

2. **Transferir**
    - Necessário informar o número da conta de destino
    - Valor para transferencia.
    - No momento da transferencia valida o saldo do usuário.
3. **Estornar**
    - Verifica a existencia de algum lançamento possível de estorno
        - Encontra-se pendente
        - Foi processada a menos de 24h
    - No momento da transferencia valida o saldo do usuário.


## Usuários de Teste
O sistema já vem pré-configurado com dois usuários de teste criados automaticamente pelas migrações. Esses usuários podem ser usados para explorar as funcionalidades do sistema, como transferências e depósitos.

Dados dos Usuários de Teste:

- **Usuário 1:**

    - username: teste
    - Senha: Senha@123

- **Usuário 2:**

    - username: teste2
    - Senha: Senha@123

Os usuários de teste já possuem algumas operações financeiras pré-registradas no sistema, criadas pelas migrações, para facilitar a análise das funcionalidades.


## Configuração com Docker

Para rodar o projeto utilizando Docker, siga as etapas abaixo:

1. **Certifique-se de que o Docker e o Docker Compose estão instalados na sua máquina.**

2. **Suba os contêineres definidos no `docker-compose.yml`:**

   ```bash
   docker-compose up -d
   ```

3. **Certifique-se de que as variáveis do arquivo `.env` estão configuradas com base no `docker-compose.yml`.** 

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=password
   ```

## Melhorias

1. **FrontEnd**
    - Adicionar tela de dashboard como home:
        - Tela com gráfico para exibir a evolução do saldo durante o ano.
        - Resumo das ultimas operações.

    - Adicionar icones e elementos em todas as telas:
        - As telas estão simples apenas para ter um visual do processo sendo executado.

    - Adicionar uma tela de perfil:
        - Tela para poder atualizar os dados como nome e senha.
        - Permitir a inativação e reativação de contas.

2. **BackEnd**
    - Melhorar padronização dos erros:
        - Utilizar melhor os handlers.
    - Implementar testes:
        - Utilização de testes automatizados para validar as funções e garantir funcionalidades do sistema.
    - Refatorar lógica:
        - Em alguns pontos a lógica parece carregada e merece uma atenção.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests. Por favor, siga as diretrizes de contribuição estabelecidas.

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.