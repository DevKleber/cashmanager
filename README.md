<p align="center">
  <img src="https://i.imgur.com/HgOSG6q.png" width="240" />
</p>

<br />
<br />
<br />

**API - CashManager** - Controle Financeiro um Hábito Prazeroso  
# [Documentação completa aqui](https://cashmanager-documentation.vercel.app)
[Ver front-end](https://cashmanager-documentation.vercel.app)

<br />
<br />

## Desenvolvimento  
<br />

- Servidor

        sudo apt install apache2 libapache2-mod-php
- MySQL

        sudo apt install mysql-server php7.4-mysql

- PHP

        sudo apt install php-7.4

- Instalando composer  

        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"
        sudo mv composer.phar /usr/local/bin/composer

- Instalando laravel

        composer global require laravel/installer

- Clonando cashmanager api. 

        sudo apt install git
        git clone https://github.com/DevKleber/cashmanager.git 


- Instalando dependências. 

        cd cashmanager && composer install



<br />

### Padrões de código

- [php cs fixer](https://marketplace.visualstudio.com/items?itemName=junstyle.php-cs-fixer)

<br />

### Documentação

- [Swagger](https://cashmanager-documentation.vercel.app)
- Servidor: Vercel

<br />

### Modelagem de dados EER
- MySQL Workbench

<br />

## Funcionalidades

- Auth
    - login
    - recoverPassword
    - me
    - newaccount
    - logout
    - refresh
    - validate
    - changePassword

- categories
    - income 
    - expense
- accounts - Wallet
- credit-card
- planned-expenses
- Transactions
    - Income
    - Expense


<br />
<br />

