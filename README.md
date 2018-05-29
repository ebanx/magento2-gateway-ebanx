# EBANX Magento 2 Payment Gateway

This is the official module of EBANX for Magento 2 and was built with the Payment Provider Gateway API, introduced in version 2.1 of Magento Open Source and Magento Commerce.

This module allow your Magento 2 store to use local payment methods from different countries and currencies.

Main features included are:

- Transparent checkout
- User-friendly interface on checkout process
- Credit Card number tokenization
- 1-Click payment for saved credit cards
- Credit Card installments with configurable interest rates
- Per-country configuration for local payment methods
- Tax calculation and display on checkout for interest rates
- Simple configuration for merchants
- Magento 2 Payment Provider Gateway enabled
- Support for document number mapping to custom fields
- Payment status notification, to automatically invoice or cancel an order based

## Supported payment methods by Country

- Brazil
    - Credit Card
    - Boleto Bancário
    - TEF
    - EBANX Account
- Argentina
    - Credit Card
    - Cupon de Pagos
    - PagoFacil
    - Rapipago
- Chile
    - Multicaja
    - Sencillito
    - Servipag
    - Webpay
- Colombia
    - Baloto
    - Credit Card
    - EFT
- Ecuador
    - SafetyPay
- Mexico
    - Credit Card
    - Oxxo
    - Spei
- Peru
    - PagoEfectivo
    - SafetyPay

## Requirements

- PHP >= 7.0
- Magento >= 2.1

## Installation

Download module with composer:

```
composer require digitalhub/ebanx
```

Enable module on Magento 2:

```
php bin/magento module:enable DigitalHub_Ebanx
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## License

Copyright (c) 2018, EBANX Tecnologia da Informação Ltda. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

Neither the name of EBANX nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
