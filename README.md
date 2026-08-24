# WC WhatsApp Notifications

Plugin que manda notificações de pedido do WooCommerce direto no WhatsApp do cliente. Sem API oficial do Meta, sem aprovação de template, sem pagar por mensagem.

Funciona com a [Evolution API](https://doc.evolution-api.com/) (protocolo Baileys), que você hospeda você mesmo.

## Como funciona

Pedido muda de status → cliente recebe mensagem no WhatsApp. Cada status tem um template editável no painel do WordPress, com variáveis como `{nome}`, `{pedido}`, `{total}` e `{status}`.

Tem também um widget de botão flutuante pra quem quiser colocar o WhatsApp de contato no site.

## Requisitos

- WordPress 6.0+ / WooCommerce 7.0+ / PHP 8.0+
- Uma instância da Evolution API rodando

## Instalação

```bash
git clone https://github.com/larissa4p/wc-whatsapp-notifications wp-content/plugins/evolution-whatsapp
```

Ou baixa o zip e extrai em `wp-content/plugins/evolution-whatsapp`.

## Configuração

Após ativar, vai em **Configurações → Evolution WhatsApp** e preenche a URL da sua instância, a API Key e o nome da instância. O plugin mostra o status da conexão na hora.

Cada status do WooCommerce tem um template separado — você ativa/desativa e edita como quiser.

## Subindo a Evolution API com Docker

Se ainda não tem uma instância, o mais rápido é via Docker. Tem um `docker-compose.yml` de exemplo no repositório. Depois de subir, acesse `http://localhost:8080/manager` pra conectar via QR Code.

## Por que não a API oficial do Meta?

Porque ela exige aprovação de conta, aprovação de cada template de mensagem e cobra por conversa. Pra uma loja pequena que só quer avisar que o pedido foi confirmado, não vale a pena.

A Evolution API conecta um número normal via QR Code — funciona como o WhatsApp Web. Você precisa hospedar a instância, mas um VPS barato ou Railway resolvem.

## Licença

GPL v2 or later.
